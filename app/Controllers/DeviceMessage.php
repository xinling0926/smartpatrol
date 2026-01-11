<?php

namespace App\Controllers;

/**
 * DeviceMessage Controller - 消息推送
 */
class DeviceMessage extends AdminController
{
    protected $sys01Model;
    protected $dev01Model;
    protected $dev03Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper(['language', 'common']);
        $this->sys01Model = model('Sys01Model');
        $this->dev01Model = model('Dev01Model');
        $this->dev03Model = model('Dev03Model');
    }

    public function index(): string
    {
        $ent0101 = $this->session->get('ent0101');
        $option = ['dev0310' => $ent0101];

        $pageSize = $this->getPageSize();
        $totalRows = $this->dev03Model->countBy($option);
        $this->data['dev03s'] = $this->dev03Model->getBy($option, $pageSize, 0, 'dev03z2 desc');
        $this->data['offset'] = 0;
        $this->setPage($totalRows, 1, $pageSize);

        return $this->render();
    }

    public function query(?int $page = null): string
    {
        $ent0101 = $this->session->get('ent0101');
        if ($page === null) {
            $option = $this->getQueryOption();
            $page = 1;
        } else {
            $option = $this->loadQueryOption();
        }
        $option['dev0310'] = $ent0101;

        $pageSize = $this->getPageSize();
        $offset = ($page - 1) * $pageSize;
        $this->data['offset'] = $offset;
        $totalRows = $this->dev03Model->countBy($option);
        $this->data['dev03s'] = $this->dev03Model->getBy($option, $pageSize, $offset, 'dev03z2 desc');
        $this->setPage($totalRows, $page, $pageSize);

        return $this->render('');
    }

    public function detail(int $id): string
    {
        $this->data['data'] = $this->dev03Model->find($id);
        return $this->render();
    }

    public function edit(int $id = 0): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $ent0101 = $this->session->get('ent0101');

        if ($postData = $this->request->getPost()) {
            $rules = [
                'dev0304' => 'required',
                'dev0305' => 'required',
            ];

            if ($this->validate($rules)) {
                if (mb_strlen($postData['dev0304'], 'UTF8') >= 20) {
                    return $this->ajaxReturn(lang('DeviceMessage.rules_dev0304_msg'));
                }
                if (mb_strlen($postData['dev0305'], 'UTF8') >= 300) {
                    return $this->ajaxReturn(lang('DeviceMessage.rules_dev0305_msg'));
                }

                if (!isset($postData['dev0301'])) {
                    // Insert
                    $postData['dev0310'] = $ent0101;
                    if ($dev0301 = $this->dev03Model->insert($postData)) {
                        $dev03 = $this->dev03Model->find($dev0301);
                        if ($dev03->dev0302 == "0") {
                            $obj = pushDev03ToDevice($dev03);
                        } else {
                            $dev01 = $this->dev01Model->find($dev03->dev0302);
                            $obj = pushDev03ToDevice($dev03, $dev01->dev0107);
                        }

                        $status = ($obj->status ?? '') === 'success' ? 1 : 0;
                        $this->db->query(sprintf(
                            "update dev03 set dev0306=%s,dev0307='%s' where dev0301=%s",
                            $status,
                            json_encode($obj),
                            $dev0301
                        ));

                        return $this->ajaxReturn("OK", ['id' => $dev0301, 'title' => $postData['dev0304']]);
                    }
                } else {
                    // Update
                    if ($this->dev03Model->save($postData)) {
                        $dev03 = $this->dev03Model->find($id);
                        $obj = pushDev03ToDevice($dev03);
                        $status = ($obj->status ?? '') === 'success' ? 1 : 0;
                        $this->db->query(sprintf(
                            "update dev03 set dev0306=%s,dev0307='%s' where dev0301=%s",
                            $status,
                            json_encode($obj),
                            $id
                        ));

                        return $this->ajaxReturn("OK", ['id' => $id, 'title' => $postData['dev0304']]);
                    }
                }
            }

            return $this->ajaxReturn($this->message->output());
        }

        $this->data['sys01s'] = $this->sys01Model->getNameArray(
            'sys0101',
            'sys0103,sys0104',
            1,
            ['*sys0110' => "sys0110 in (select ent1001 from ent10 where ent1002={$ent0101})", 'sys0108' => 1]
        );
        $this->data['dev01s'] = $this->dev01Model->getNameArray(
            'dev0101',
            'dev0104',
            1,
            ['dev0102' => $ent0101, 'dev0106' => 1]
        );

        if ($id) {
            $this->data['data'] = $this->dev03Model->find($id);
        } else {
            $this->data['data'] = null;
        }

        return $this->render();
    }
}
