<?php

namespace App\Controllers;

/**
 * ApproveSetting Controller - 簽核流程設定
 */
class ApproveSetting extends AdminController
{
    protected $fmd01Model;
    protected $fmd20Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper('language');
        $this->fmd01Model = model('Fmd01Model');
        $this->fmd20Model = model('Fmd20Model');
    }

    public function index(): string
    {
        $option = [];
        if ($newestId = $this->fmd01Model->getByNewestId()) {
            $option['*fmd0101'] = 'fmd0101 in (' . $newestId . ')';
        }
        $option['ent1002'] = $this->session->get('ent0101');
        $option['filter_dep'] = true;

        $pageSize = $this->getPageSize();
        $totalRows = $this->fmd01Model->countBy($option);
        $this->data['data'] = $this->fmd01Model->getBy($option, $pageSize);

        foreach ($this->data['data'] as $fmd01) {
            $fmd01->fmd20 = $this->fmd20Model->getByFmd0106($fmd01->fmd0106);
        }

        $this->data['offset'] = 0;
        $this->setPage($totalRows, 1, $pageSize);

        $cod02Model = model('Cod02Model');
        $this->data['fmd0105_opt'] = $cod02Model->getOptionArrayByCode('fmd0105');
        $this->data['fmd2003_opt'] = $cod02Model->getOptionArrayByCode('fmd2003');

        $ent10Model = model('Ent10Model');
        $ent10Id = $ent10Model->getCurrentUserSeeDepartmentId();
        $this->data['ent10s'] = $ent10Model->getNameArray(
            'ent1001',
            'ent1004',
            ['' => lang('ApproveSetting.dept_filter')],
            ['ent1002' => $this->session->get('ent0101'), 'ent1007' => 1, '*ent1001' => "ent1001 in ({$ent10Id})"]
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
            if (isset($option['search']) && $option['search']) {
                $option['*search'] = "(fmd0103 like '%" . $option['search'] . "%' or fmd0104 like '%" . $option['search'] . "%')";
                unset($option['search']);
            }
            $this->setQueryOption($option);
        }

        $option['*fmd0101'] = 'fmd0101 in (' . $this->fmd01Model->getByNewestId() . ')';
        $option['ent1002'] = $this->session->get('ent0101');
        $option['filter_dep'] = true;

        $pageSize = $this->getPageSize();
        $offset = ($page - 1) * $pageSize;
        $this->data['offset'] = $offset;
        $totalRows = $this->fmd01Model->countBy($option);
        $this->data['data'] = $this->fmd01Model->getBy($option, $pageSize, $offset);

        foreach ($this->data['data'] as $fmd01) {
            $fmd01->fmd20 = $this->fmd20Model->getByFmd0106($fmd01->fmd0106);
        }

        $this->setPage($totalRows, $page, $pageSize);

        $cod02Model = model('Cod02Model');
        $this->data['fmd0105_opt'] = $cod02Model->getOptionArrayByCode('fmd0105');
        $this->data['fmd2003_opt'] = $cod02Model->getOptionArrayByCode('fmd2003');

        return $this->render('');
    }

    public function detail(int $id): string
    {
        if (!$fmd01 = $this->fmd01Model->find($id)) {
            return '';
        }

        if ($this->data['fmd20'] = $this->fmd20Model->getByFmd0106($fmd01->fmd0106)) {
            $fmd21Model = model('Fmd21Model');
            $fmd22Model = model('Fmd22Model');
            $this->data['fmd21s'] = $fmd21Model->getByFmd0106($fmd01->fmd0106);
            $this->data['fmd22s'] = $fmd22Model->getByFmd0106($fmd01->fmd0106);
        }
        $this->data['fmd01'] = $fmd01;

        $cod02Model = model('Cod02Model');
        $this->data['fmd0105_opt'] = $cod02Model->getOptionArrayByCode('fmd0105');
        $this->data['fmd2003_opt'] = $cod02Model->getOptionArrayByCode('fmd2003');

        $fmd02Model = model('Fmd02Model');
        $this->data['fmd02s'] = $fmd02Model->getNameArray('fmd0203', 'fmd0204', null, ['fmd0202' => $id], 'fmd0203');

        $ent10Model = model('Ent10Model');
        $this->data['ent10s'] = $ent10Model->getNameArray(
            'ent1001',
            'ent1004',
            ['0' => lang('ApproveSetting.v_fmd2206_0')],
            ['ent1002' => $this->session->get('ent0101'), 'ent1007' => 1]
        );

        return $this->render();
    }

    public function edit(int $id): \CodeIgniter\HTTP\ResponseInterface
    {
        if ($this->fmd20Model->commit($id)) {
            return $this->ajaxReturn("OK", ['id' => $id]);
        } else {
            return $this->ajaxReturn($this->message->output());
        }
    }

    public function checkOut(): \CodeIgniter\HTTP\ResponseInterface
    {
        if ($id = $this->request->getPost('id')) {
            if ($this->fmd20Model->checkOut($id)) {
                return $this->ajaxReturn("OK", ['id' => $id]);
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('ERR');
    }

    public function editFmd21(?int $id = null): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $fmd21Model = model('Fmd21Model');

        if ($postData = $this->request->getPost()) {
            $rules = ['fmd2104' => 'required'];

            if ($this->validate($rules)) {
                if (isset($postData['fmd2105'])) {
                    $postData['fmd2105'] = implode(',', $postData['fmd2105']);
                    if (isset($postData['fmd0101'])) {
                        $fmd01 = $this->fmd01Model->find($postData['fmd0101']);
                        $postData['fmd2102'] = $fmd01->fmd0106;
                    }
                    if ($fmd21Model->save($postData)) {
                        return $this->ajaxReturn("OK", ['id' => $id]);
                    }
                } else {
                    $this->message->add(lang('ApproveSetting.fmd2105_is_null'));
                }
            }

            return $this->ajaxReturn($this->message->output());
        }

        $fmd02Model = model('Fmd02Model');

        if ($id) {
            $fmd21 = $fmd21Model->find($id);
            $fmd21->fmd2105 = explode(',', $fmd21->fmd2105);
            $this->data['data'] = $fmd21;
            $fmd01 = $this->fmd01Model->getLastVersion($fmd21->fmd2102);
            $this->data['fmd02s'] = $fmd02Model->getNameArray('fmd0203', 'fmd0204', null, ['fmd0202' => $fmd01->fmd0101], 'fmd0203');
        } else {
            $this->data['data'] = null;
            $this->data['fmd0101'] = $this->request->getGet('fmd0101');
            $this->data['fmd02s'] = $fmd02Model->getNameArray('fmd0203', 'fmd0204', null, ['fmd0202' => $this->data['fmd0101']], 'fmd0203');
        }

        return $this->render();
    }

    public function editFmd22(?int $id = null): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $fmd22Model = model('Fmd22Model');

        if ($postData = $this->request->getPost()) {
            $rules = [
                'fmd2204' => 'required|integer',
                'fmd2205' => 'required',
            ];

            if ($this->validate($rules)) {
                if (isset($postData['fmd0101'])) {
                    $fmd01 = $this->fmd01Model->find($postData['fmd0101']);
                    $postData['fmd2202'] = $fmd01->fmd0106;
                }
                if ($fmd22Model->save($postData)) {
                    return $this->ajaxReturn("OK", ['id' => $id]);
                }
            }

            return $this->ajaxReturn($this->message->output());
        }

        $fmd02Model = model('Fmd02Model');
        $fmd21Model = model('Fmd21Model');

        if ($id) {
            $this->data['data'] = $fmd22Model->find($id);
            $this->data['fmd21s'] = $fmd21Model->getNameArray('fmd2101', 'fmd2104', 0, ['fmd2102' => $this->data['data']->fmd2202]);
        } else {
            $this->data['data'] = null;
            $this->data['fmd0101'] = $this->request->getGet('fmd0101');
            $fmd01 = $this->fmd01Model->find($this->data['fmd0101']);
            $fmd02s = $fmd02Model->getByFmd0101($fmd01->fmd0101);
            $fmd21s = $fmd21Model->getNameArray('fmd2101', 'fmd2104', 0, ['fmd2102' => $fmd01->fmd0106]);

            if ($fmd02s && !$fmd21s) {
                $this->data['message'] = lang('ApproveSetting.must_add_fmd21');
                return view('layout/error_dialog', $this->data);
            }
            $this->data['fmd21s'] = $fmd21s;
        }

        $ent10Model = model('Ent10Model');
        $this->data['ent10s'] = $ent10Model->getNameArray(
            'ent1001',
            'ent1004',
            ['0' => lang('ApproveSetting.v_fmd2206_0')],
            ['ent1002' => $this->session->get('ent0101'), 'ent1007' => 1]
        );

        return $this->render();
    }

    public function deleteFmd21(): \CodeIgniter\HTTP\ResponseInterface
    {
        if ($id = $this->request->getPost('id')) {
            $fmd21Model = model('Fmd21Model');
            $fmd22Model = model('Fmd22Model');

            if ($fmd21Model->delete($id)) {
                $fmd22s = $fmd22Model->getBy(['fmd2203' => $id]);
                foreach ($fmd22s as $fmd22) {
                    $fmd22Model->delete($fmd22->fmd2201);
                }
                return $this->ajaxReturn("OK");
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('ERR');
    }

    public function deleteFmd22(): \CodeIgniter\HTTP\ResponseInterface
    {
        if ($id = $this->request->getPost('id')) {
            $fmd22Model = model('Fmd22Model');
            if ($fmd22Model->delete($id)) {
                return $this->ajaxReturn("OK");
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('ERR');
    }
}
