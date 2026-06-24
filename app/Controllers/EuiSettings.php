<?php

namespace App\Controllers;

/**
 * EuiSettings Controller - EUI 統計數值設定
 */
class EuiSettings extends AdminController
{
    protected $fmd41Model;
    protected $ent10Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper('language');
        $this->fmd41Model = model('Fmd41Model');
        $this->ent10Model = model('Ent10Model');
    }

    public function index(): string
    {
        $this->clearQueryOption();

        $pageSize = $this->getPageSize();
        $totalRows = $this->fmd41Model->countBy(null);
        $this->data['data'] = $this->fmd41Model->getBy(null, $pageSize);
        $this->setPage($totalRows, 1, $pageSize);

        $ent0101 = $this->session->get('ent0101');
        $ent10Id = $this->ent10Model->getCurrentUserSeeDepartmentId();
        $this->data['ent10s'] = $this->ent10Model->getNameArray(
            'ent1001',
            'ent1004',
            ['' => lang('EuiSettings.search_select_default')],
            ['ent1002' => $ent0101, 'ent1007' => 1, '*ent1001' => "ent1001 in ({$ent10Id})"]
        );

        return $this->render();
    }

    public function query(int $page = 0): string
    {
        if ($page) {
            $option = $this->loadQueryOption();
        } else {
            $page = 1;
            $option = $this->getQueryOption();
        }

        foreach ($option as $k => $v) {
            if ($v === '') {
                unset($option[$k]);
            }
        }

        $pageSize = $this->getPageSize();
        $offset = ($page - 1) * $pageSize;
        $totalRows = $this->fmd41Model->countBy($option);
        $this->data['data'] = $this->fmd41Model->getBy($option, $pageSize, $offset);
        $this->setPage($totalRows, $page, $pageSize);

        return $this->render('');
    }

    public function detail(int $id): string
    {
        $this->data['data'] = $this->fmd41Model->get($id);
        return $this->render();
    }

    public function edit(int $id = 0): string|\CodeIgniter\HTTP\ResponseInterface
    {
        if ($this->request->is('post')) {
            $postData = $this->request->getPost();

            $rules = ['fmd4104' => 'required'];

            if ($this->validate($rules)) {
                if (!array_key_exists('fmd4101', $postData)) {
                    // 不允許新增（EUI 紀錄由排程自動產生）
                    return $this->ajaxReturn(lang('EuiSettings.add_not_allowed'));
                }
                if ($this->fmd41Model->updateData($postData)) {
                    return $this->ajaxReturn('OK', ['id' => $id]);
                }
            }

            return $this->ajaxReturn($this->message->output());
        }

        if ($id) {
            $this->data['data'] = $this->fmd41Model->get($id);
        } else {
            $this->data['data'] = null;
        }

        return $this->render();
    }

    public function delete(): \CodeIgniter\HTTP\ResponseInterface
    {
        $id = $this->request->getPost('id');
        if ($id) {
            if ($this->fmd41Model->delete($id)) {
                return $this->ajaxReturn('OK');
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('No ID provided');
    }
}
