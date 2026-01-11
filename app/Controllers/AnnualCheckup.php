<?php

namespace App\Controllers;

/**
 * AnnualCheckup Controller - 歲修停車管理
 */
class AnnualCheckup extends AdminController
{
    protected $fmd01Model;
    protected $fmd30Model;
    protected array $fmd3005List = [];
    protected array $fmd3006List = [];

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper('language');
        $this->fmd01Model = model('Fmd01Model');
        $this->fmd30Model = model('Fmd30Model');

        $this->fmd3005List = [
            1 => lang('AnnualCheckup.f_fmd3005_1'),
            2 => lang('AnnualCheckup.f_fmd3005_2'),
        ];
        $this->fmd3006List = [
            0 => lang('AnnualCheckup.f_fmd3006_0'),
            1 => lang('AnnualCheckup.f_fmd3006_1'),
            2 => lang('AnnualCheckup.f_fmd3006_2'),
            3 => lang('AnnualCheckup.f_fmd3006_3'),
            4 => lang('AnnualCheckup.f_fmd3006_4'),
            'all' => lang('AnnualCheckup.f_fmd3006_all'),
        ];
    }

    public function index(): string
    {
        $option = ['*fmd3006' => "fmd3006 in (1,2,3)"];
        $sort = null;
        $pageSize = $this->getPageSize();
        $totalRows = $this->fmd30Model->countByEdit($option);
        $this->data['data'] = $this->fmd30Model->getByEdit($option, $pageSize, 0, $sort);
        $this->data['offset'] = 0;
        $this->setPage($totalRows, 1, $pageSize);

        $this->data['fmd3005_opt'] = $this->fmd3005List;
        $this->data['fmd3006_opt'] = $this->fmd3006List;

        $ent10Model = model('Ent10Model');
        $this->data['dep_opt'] = $ent10Model->getNameArray(
            'ent1001',
            'ent1004',
            ['' => lang('AnnualCheckup.index_select_default')],
            ['ent1002' => $this->session->get('ent0101'), 'ent1007' => 1]
        );

        $this->setQueryOption($option);
        return $this->render();
    }

    public function query(int $page = 0): string
    {
        $sort = null;

        if ($page) {
            $option = $this->loadQueryOption();
            $this->setQueryOption($option);
        } else {
            $page = 1;
            $option = $this->getQueryOption();

            // Filter out empty values first
            $option = array_filter($option, function($value) {
                return $value !== '' && $value !== null;
            });

            $option['*searchTmp'] = [];

            if (($option['fmd3006'] ?? '') === 'all') {
                unset($option['fmd3006']);
                unset($option['*fmd3006']);
            } elseif (isset($option['fmd3006']) && $option['fmd3006'] !== '') {
                $option['*fmd3006'] = "fmd3006 = '" . $option['fmd3006'] . "'";
                unset($option['fmd3006']);
            }

            if (isset($option['search']) && $option['search']) {
                $option['*searchTmp'][] = "(fmd0103 like '%" . $option['search'] . "%' or fmd0104 like '%" . $option['search'] . "%')";
            }
            // Always remove search from option as it's not a column
            unset($option['search']);

            if (isset($option['*fmd3006']) && $option['*fmd3006']) {
                $option['*searchTmp'][] = $option['*fmd3006'];
                unset($option['*fmd3006']);
            }

            if (count($option['*searchTmp'])) {
                $option['*search'] = implode(' and ', $option['*searchTmp']);
            }
            unset($option['*searchTmp']);

            if (isset($option['*search'])) {
                unset($option['*fmd3006']);
            }

            $this->setQueryOption($option);
        }

        $pageSize = $this->getPageSize();
        $offset = ($page - 1) * $pageSize;
        $this->data['offset'] = $offset;
        $totalRows = $this->fmd30Model->countByEdit($option);
        $this->data['data'] = $this->fmd30Model->getByEdit($option, $pageSize, $offset, $sort);
        $this->setPage($totalRows, $page, $pageSize);

        $this->data['fmd3005_opt'] = $this->fmd3005List;
        $this->data['fmd3006_opt'] = $this->fmd3006List;

        $ent10Model = model('Ent10Model');
        $this->data['dep_opt'] = $ent10Model->getNameArray('ent1001', 'ent1004', 0, ['ent1002' => $this->session->get('ent0101')]);

        return $this->render('');
    }

    public function detail(int $fmd3001): string
    {
        $page = $this->request->getGet('page') ?? 1;
        ini_set('max_execution_time', '60');

        if ($fmd30 = $this->fmd30Model->getWithFmd01andEnt10($fmd3001)) {
            $this->data['data'] = $fmd30;
        } else {
            $this->data['data'] = null;
        }

        $this->data['fmd3005_opt'] = $this->fmd3005List;
        return $this->render();
    }

    public function edit(?int $id = null): string|\CodeIgniter\HTTP\ResponseInterface
    {
        if ($postData = $this->request->getPost()) {
            $rules = [
                'fmd0101' => 'required',
                'fmd3003' => 'required',
            ];

            if ($this->validate($rules)) {
                if (!$this->fmd3004Check($postData['fmd3004'] ?? '', $postData['fmd3003'] ?? '')) {
                    return $this->ajaxReturn($this->message->output());
                }

                if (!$this->request->getPost('fmd3001')) {
                    // Insert
                    $insertData = [
                        'fmd3002' => $postData['fmd0101'],
                        'fmd3003' => $postData['fmd3003'],
                        'fmd3004' => $postData['fmd3004'],
                        'fmd3005' => $postData['fmd3005'],
                        'fmd3006' => 1,
                        'fmd3008' => 1,
                    ];

                    if ($fmd3001 = $this->fmd30Model->insert($insertData)) {
                        $this->fmd30Model->updateFmd3007($fmd3001);
                        $this->fmd30Model->updateFmd3009($fmd3001);
                        $fmd01 = $this->fmd01Model->find($postData['fmd0101']);
                        return $this->ajaxReturn("OK", ['id' => $fmd3001, 'title' => $fmd01->fmd0104]);
                    }
                } else {
                    // Update
                    $updateData = [
                        'fmd3001' => $postData['fmd3001'],
                        'fmd3003' => $postData['fmd3003'],
                        'fmd3004' => $postData['fmd3004'],
                        'fmd3005' => $postData['fmd3005'],
                        'fmd30z3' => $this->user->currentUser->sys0101,
                        'fmd30z4' => date('Y-m-d'),
                    ];
                    $this->fmd30Model->save($updateData);
                    return $this->ajaxReturn("OK");
                }
            }

            return $this->ajaxReturn($this->message->output());
        }

        $ent10Model = model('Ent10Model');
        $this->data['fmd01s'] = $this->fmd01Model->getNameArray(
            'fmd0101',
            'fmd0104',
            2,
            ['filter_dep' => true, 'fmd0108' => 2],
            'fmd0104'
        );
        $this->data['ent10s'] = $this->fmd01Model->createNameArray(
            $this->fmd01Model->getEnt10ByUser(),
            'ent1001',
            'ent1004',
            ['' => lang('AnnualCheckup.search_select_ent1004_default')]
        );

        if ($fmd30 = $this->fmd30Model->getWithFmd01andEnt10($id)) {
            $this->data['data'] = $fmd30;
        } else {
            $this->data['data'] = null;
        }

        $this->data['fmd3005_list'] = $this->fmd3005List;

        return $this->render();
    }

    public function state(): \CodeIgniter\HTTP\ResponseInterface
    {
        $fmd3001 = $this->request->getPost('id');
        $state = $this->request->getPost('s');

        if ($fmd3001) {
            if ($this->fmd30Model->changeFormState($fmd3001, $state)) {
                return $this->ajaxReturn("OK");
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }

        return $this->ajaxReturn('ERR');
    }

    public function formHistory(int $fmd3001): string
    {
        if ($fmd30 = $this->fmd30Model->find($fmd3001)) {
            $this->data['fmd30s'] = $this->fmd30Model->getBy(['fmd3009' => $fmd30->fmd3009], 0, 0, 'fmd3008 desc');
            return $this->render();
        }
        return '';
    }

    public function delete(): \CodeIgniter\HTTP\ResponseInterface
    {
        if ($id = $this->request->getPost('id')) {
            if ($this->fmd30Model->delete($id)) {
                return $this->ajaxReturn("OK");
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('ERR');
    }

    public function checkOut(): \CodeIgniter\HTTP\ResponseInterface
    {
        if ($id = $this->request->getPost('id')) {
            if ($newId = $this->fmd30Model->checkOut($id)) {
                return $this->ajaxReturn("OK", ['id' => $newId]);
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('ERR');
    }

    public function revert(): \CodeIgniter\HTTP\ResponseInterface
    {
        if ($id = $this->request->getPost('id')) {
            if ($this->fmd30Model->revert($id)) {
                return $this->ajaxReturn("OK");
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('ERR');
    }

    public function commit(): \CodeIgniter\HTTP\ResponseInterface
    {
        if ($id = $this->request->getPost('id')) {
            if ($this->fmd30Model->commit($id)) {
                return $this->ajaxReturn("OK");
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('ERR');
    }

    public function editFmd30(int $id): string|\CodeIgniter\HTTP\ResponseInterface
    {
        if ($postData = $this->request->getPost()) {
            $rules = ['fmd3003' => 'required'];

            if ($this->validate($rules) && $this->fmd3004Check($postData['fmd3004'] ?? '', $postData['fmd3003'] ?? '')) {
                $updateData = [
                    'fmd3001' => $postData['fmd3001'],
                    'fmd3003' => $postData['fmd3003'],
                    'fmd3004' => $postData['fmd3004'],
                    'fmd3005' => $postData['fmd3005'],
                    'fmd30z3' => $this->user->currentUser->sys0101,
                    'fmd30z4' => date('Y-m-d H:i:s'),
                ];
                $this->fmd30Model->save($updateData);
                return $this->ajaxReturn("OK");
            }

            return $this->ajaxReturn($this->message->output());
        }

        $ent10Model = model('Ent10Model');
        $this->data['fmd01s'] = $this->fmd01Model->getNameArray(
            'fmd0101',
            'fmd0104',
            2,
            ['filter_dep' => true, 'fmd0108' => 2],
            'fmd0104'
        );
        $this->data['ent10s'] = $this->fmd01Model->createNameArray(
            $this->fmd01Model->getEnt10ByUser(),
            'ent1001',
            'ent1004',
            ['' => lang('AnnualCheckup.search_select_ent1004_default')]
        );

        if ($fmd30 = $this->fmd30Model->getWithFmd01andEnt10($id)) {
            $this->data['data'] = $fmd30;
        } else {
            $this->data['data'] = null;
        }

        $this->data['fmd3005_opt'] = $this->fmd3005List;

        return $this->render();
    }

    private function fmd3004Check(string $fmd3004, string $fmd3003): bool
    {
        $fmd3003T = strtotime($fmd3003 . ' 00:00:00');
        $fmd3004T = strtotime($fmd3004 . ' 23:59:59');
        $currentT = strtotime(date('Y-m-d') . ' 00:00:00');
        $errMsg = [];
        $fmd3003Lock = false;

        if ($postData = $this->request->getPost()) {
            if (isset($postData['fmd3001'])) {
                if ($fmd30 = $this->fmd30Model->getOngoingById($postData['fmd3001'])) {
                    if (isset($fmd30->fmd3003_lock) && $fmd30->fmd3003_lock) {
                        $fmd3003Lock = true;
                    }
                }
            }
        }

        if ($fmd3003Lock && ($fmd3003 !== ($fmd30->fmd3003 ?? ''))) {
            $errMsg[] = lang('AnnualCheckup.v_fmd3003_locked');
        }

        if ($fmd3003T > $fmd3004T) {
            $errMsg[] = lang('AnnualCheckup.v_fmd3003_gt_fmd3004_error');
        }

        if ($currentT > $fmd3004T) {
            $errMsg[] = lang('AnnualCheckup.v_current_gt_fmd3004_error');
        }

        if ($currentT > $fmd3003T && !$fmd3003Lock) {
            $errMsg[] = lang('AnnualCheckup.v_current_gt_fmd3003_error');
        }

        if (count($errMsg)) {
            $this->message->add(implode(', ', $errMsg));
            return false;
        }

        return true;
    }
}
