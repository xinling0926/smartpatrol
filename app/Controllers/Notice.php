<?php

namespace App\Controllers;

/**
 * Notice Controller - 通知管理
 */
class Notice extends AdminController
{
    protected $fmd10Model;
    protected $ent10Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper('language');
        $this->fmd10Model = model('Fmd10Model');
        $this->ent10Model = model('Ent10Model');
    }

    public function index(): string
    {
        $ent0101 = $this->session->get('ent0101');
        $option = ['fmd1012' => $ent0101];

        $pageSize = $this->getPageSize();
        $totalRows = $this->fmd10Model->countBy($option);
        $this->data['data'] = $this->fmd10Model->getBy($option, $pageSize, 0, 'fmd1001 desc');
        $this->data['offset'] = 0;
        $this->setPage($totalRows, 1, $pageSize);

        return $this->render();
    }

    public function query(int $page = 0): string
    {
        $ent0101 = $this->session->get('ent0101');

        if ($page) {
            $option = $this->loadQueryOption();
        } else {
            $page = 1;
            $option = $this->getQueryOption();
        }

        foreach ($option as $k1 => $v1) {
            if ($v1 === '') {
                unset($option[$k1]);
            }
        }
        $option['fmd1012'] = $ent0101;

        $pageSize = $this->getPageSize();
        $offset = ($page - 1) * $pageSize;
        $this->data['offset'] = $offset;
        $totalRows = $this->fmd10Model->countBy($option);
        $this->data['data'] = $this->fmd10Model->getBy($option, $pageSize, $offset, 'fmd1001 desc');
        $this->setPage($totalRows, $page, $pageSize);

        $this->data['option'] = $option;

        return $this->render('');
    }

    public function detail(int $id): string
    {
        $fmd10s = $this->fmd10Model->find($id);

        if ($fmd10s->fmd1002) {
            $fmd10s->dev01s = $this->db->query("select dev0104 from dev01 where dev0101 in ({$fmd10s->fmd1002})")->getResult();
        } else {
            $fmd10s->dev01s = [];
        }

        if ($fmd10s->fmd1003) {
            $fmd10s->sys01s = $this->db->query("select sys0103,sys0104 from sys01 where sys0101 in ({$fmd10s->fmd1003})")->getResult();
        } else {
            $fmd10s->sys01s = [];
        }

        $this->data['data'] = $fmd10s;

        return $this->render();
    }

    public function edit(int $id = 0): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $ent0101 = $this->session->get('ent0101');

        if ($this->request->is('post')) {
            $postData = $this->request->getPost();

            $rules = [
                'fmd1004' => 'required',
                'fmd1005' => 'required',
            ];

            if ($this->validate($rules)) {
                $fmd1002 = '';
                $fmd1003 = '';

                // Process device IDs
                $dev0101s = $this->request->getPost('dev0101');
                if (is_array($dev0101s)) {
                    foreach ($dev0101s as $v) {
                        if (!empty($v)) {
                            $fmd1002 .= $v . ',';
                        }
                    }
                }

                // Process user IDs
                $sys0101s = $this->request->getPost('sys0101');
                if (is_array($sys0101s)) {
                    foreach ($sys0101s as $v) {
                        if (!empty($v)) {
                            $fmd1003 .= $v . ',';
                        }
                    }
                }

                // Process notification days
                $fmd1007s = $this->request->getPost('fmd1007');
                if (is_array($fmd1007s)) {
                    $postData['fmd1007'] = rtrim(implode(',', $fmd1007s), ',');
                }

                // Process notification types
                $fmd1008s = $this->request->getPost('fmd1008');
                if (is_array($fmd1008s)) {
                    $postData['fmd1008'] = rtrim(implode(',', $fmd1008s), ',');
                } else {
                    $postData['fmd1008'] = '1';
                }

                unset($postData['dev0101'], $postData['sys0101']);
                $postData['fmd1002'] = rtrim($fmd1002, ',');
                $postData['fmd1003'] = rtrim($fmd1003, ',');
                $postData['fmd1006'] = (int)($postData['fmd1006'] ?? 1);
                $postData['fmd1009'] = (int)($postData['fmd1009'] ?? 0);
                $postData['fmd1010'] = (int)($postData['fmd1010'] ?? 1);

                if (empty($postData['fmd1001'])) {
                    // Insert
                    $postData['fmd1012'] = $ent0101;

                    if ($newId = $this->fmd10Model->insert($postData)) {
                        $fmd10 = $this->fmd10Model->find($newId);

                        // Push to devices
                        $fmd1002Condition = empty($postData['fmd1002'])
                            ? " and dev0102={$ent0101}"
                            : " and dev0101 in ({$postData['fmd1002']})";

                        $dev01s = $this->db->query("select group_concat(dev0107 SEPARATOR ';') as dev0107 from dev01 where dev0107<>''{$fmd1002Condition}")->getRow();

                        if ($dev01s && !empty($dev01s->dev0107)) {
                            helper('common');
                            pushToDevice(['fmd10' => [$fmd10], 'description' => ''], $dev01s->dev0107);
                        }

                        return $this->ajaxReturn('OK', ['id' => $newId, 'title' => $postData['fmd1004']]);
                    }
                } else {
                    // Update
                    if ($this->fmd10Model->save($postData)) {
                        $fmd10 = $this->fmd10Model->find($id);

                        // Push to devices
                        $fmd1002Condition = empty($postData['fmd1002'])
                            ? " and dev0102={$ent0101}"
                            : " and dev0101 in ({$postData['fmd1002']})";

                        $dev01s = $this->db->query("select group_concat(dev0107 SEPARATOR ';') as dev0107 from dev01 where dev0107<>''{$fmd1002Condition}")->getRow();

                        if ($dev01s && !empty($dev01s->dev0107)) {
                            helper('common');
                            pushToDevice(['fmd10' => [$fmd10], 'description' => ''], $dev01s->dev0107);
                        }

                        return $this->ajaxReturn('OK', ['id' => $id, 'title' => $postData['fmd1004']]);
                    }
                }
            }

            return $this->ajaxReturn($this->message->output());
        }

        // GET request
        if ($id) {
            $this->data['data'] = $this->fmd10Model->find($id);
        } else {
            $this->data['data'] = null;
        }

        $dev01Model = model('Dev01Model');
        $sys01Model = model('Sys01Model');

        $this->data['ent10s'] = $this->ent10Model->getBy(['ent1002' => $ent0101]);
        $this->data['dev01s'] = $dev01Model->getBy(['dev0102' => $ent0101, 'dev0106' => 1]);
        $this->data['sys01s'] = $sys01Model->getBy(['ent1002' => $ent0101, 'sys0108' => 1]);

        return $this->render();
    }

    public function delete(): \CodeIgniter\HTTP\ResponseInterface
    {
        $id = $this->request->getPost('id');
        if ($id) {
            if ($this->fmd10Model->delete($id)) {
                return $this->ajaxReturn('OK');
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('No ID provided');
    }

    /**
     * Get users and devices by department ID
     */
    public function getsys012dev01(int $ent1001): \CodeIgniter\HTTP\ResponseInterface
    {
        $ent0101 = $this->session->get('ent0101');

        $dev01Model = model('Dev01Model');
        $sys01Model = model('Sys01Model');

        $result = ['sys01' => [], 'dev01' => []];

        // Get all sub-department IDs
        $depId = $this->ent10Model->getSubDepartmentId($ent1001);
        $depId = empty($depId) ? $ent1001 : $ent1001 . ',' . $depId;

        $result['sys01'] = $sys01Model->getBy(['*sys0110' => "sys0110 in ({$depId})"]);
        $result['dev01'] = $dev01Model->getBy(['*dev0103' => "dev0103 in ({$depId}) or (dev0103 is null and dev0102={$ent0101})"]);

        return $this->response->setJSON($result);
    }
}
