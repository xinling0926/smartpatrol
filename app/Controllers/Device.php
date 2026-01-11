<?php

namespace App\Controllers;

/**
 * Device Controller - 巡檢設備管理
 */
class Device extends AdminController
{
    protected $dev01Model;
    protected $ent10Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper('language');
        $this->dev01Model = model('Dev01Model');
        $this->ent10Model = model('Ent10Model');
    }

    public function index(): string
    {
        $ent0101 = $this->session->get('ent0101');
        $option = ['dev0102' => $ent0101, 'dev0106' => 1];
        $this->setQueryOption($option);
        $this->data['dev0106'] = 1;

        $ent10Id = $this->ent10Model->getCurrentUserSeeDepartmentId();
        $this->data['ent10s'] = $this->ent10Model->getNameArray(
            'ent1001',
            'ent1004',
            ['' => lang('Device.search_select_default')],
            ['ent1002' => $ent0101, 'ent1007' => 1, '*ent1001' => "ent1001 in ({$ent10Id})"]
        );

        $pageSize = $this->getPageSize();
        $totalRows = $this->dev01Model->countBy($option);
        $this->data['data'] = $this->dev01Model->getBy($option, $pageSize);
        $this->data['offset'] = 0;
        $this->setPage($totalRows, 1, $pageSize);

        $cod02Model = model('Cod02Model');
        $this->data['dev0106_opt'] = $cod02Model->getOptionArrayByCode('dev0106');

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

        if (isset($option['search'])) {
            if (trim($option['search']) !== '') {
                $search = $this->db->escapeLikeString($option['search']);
                $option['*search'] = "dev0104 like '%{$search}%'";
            }
            unset($option['search']);
        }

        $option['dev0102'] = $this->session->get('ent0101');

        $pageSize = $this->getPageSize();
        $offset = ($page - 1) * $pageSize;
        $this->data['offset'] = $offset;
        $totalRows = $this->dev01Model->countBy($option);
        $this->data['data'] = $this->dev01Model->getBy($option, $pageSize, $offset);
        $this->setPage($totalRows, $page, $pageSize);

        $cod02Model = model('Cod02Model');
        $this->data['dev0106_opt'] = $cod02Model->getOptionArrayByCode('dev0106');

        return $this->render('');
    }

    public function detail(int $id): string
    {
        $this->data['data'] = $this->dev01Model->find($id);

        $cod02Model = model('Cod02Model');
        $this->data['dev0106_opt'] = $cod02Model->getOptionArrayByCode('dev0106');

        if ($this->user->isAdmin) {
            $this->data['files'] = $this->getDeviceUploadFiles($id);
        }

        return $this->render();
    }

    public function edit(int $id = 0): string|\CodeIgniter\HTTP\ResponseInterface
    {
        if ($this->request->getMethod() === 'post') {
            $postData = $this->request->getPost();

            $rules = [
                'dev0104' => 'required',
                'dev0106' => 'required',
            ];

            if ($this->validate($rules)) {
                // Check device license
                if (!$this->dev0106Check($postData['dev0106'])) {
                    return $this->ajaxReturn(lang('Device.dev0106_check_hint'));
                }

                if (empty($postData['dev0101'])) {
                    // Insert
                    $postData['dev0102'] = $this->session->get('ent0101');

                    if ($newId = $this->dev01Model->insert($postData)) {
                        return $this->ajaxReturn('OK', ['id' => $newId, 'title' => $postData['dev0104']]);
                    }
                } else {
                    // Update
                    $old = $this->dev01Model->find($id);

                    if ($this->dev01Model->save($postData)) {
                        $dev01 = $this->dev01Model->find($id);
                        helper('common');
                        pushToDevice(['dev01' => $dev01, 'description' => '更新設備:' . $dev01->dev0104], $old->dev0107);

                        return $this->ajaxReturn('OK', ['id' => $id, 'title' => $postData['dev0104']]);
                    }
                }
            }

            return $this->ajaxReturn($this->message->output());
        }

        // GET request
        $cod02Model = model('Cod02Model');
        $this->data['dev0106_opt'] = $cod02Model->getOptionArrayByCode('dev0106');
        $this->data['dept'] = $this->ent10Model->getNameArray('ent1001', 'ent1004', ['' => ''], ['ent1002' => $this->session->get('ent0101'), 'ent1007' => 1]);

        if ($id) {
            $this->data['data'] = $this->dev01Model->find($id);
        } else {
            $this->data['data'] = null;
        }

        return $this->render();
    }

    public function delete(): \CodeIgniter\HTTP\ResponseInterface
    {
        $id = $this->request->getPost('id');
        if ($id) {
            if ($this->dev01Model->delete($id)) {
                return $this->ajaxReturn('OK');
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('No ID provided');
    }

    /**
     * Check device license count
     */
    protected function dev0106Check(int $dev0106): bool
    {
        if ($dev0106 == 2) {
            return true;
        }

        $ent02Model = model('Ent02Model');
        $num = $ent02Model->getLicenseCount('smart_patrol_device', $this->session->get('ent0101'));

        $postId = $this->request->getPost('dev0101');
        if (empty($postId)) {
            $count = $this->dev01Model->countBy(['dev0102' => $this->session->get('ent0101'), 'dev0106' => 1]);
        } else {
            $count = $this->dev01Model->countBy([
                '*dev0101' => 'dev0101!=' . $postId,
                'dev0102' => $this->session->get('ent0101'),
                'dev0106' => 1,
            ]);
        }

        return $count < $num;
    }

    private function getDeviceUploadFiles(int $dev0101): array
    {
        $result = [];
        $dir = FCPATH . "data/upload/{$dev0101}/";

        if ($handle = @opendir($dir)) {
            while (($file = readdir($handle)) !== false) {
                if ($file !== '.' && $file !== '..') {
                    $result[] = $file;
                }
            }
            closedir($handle);
        }

        return $result;
    }

    /**
     * Request APP to upload database
     */
    public function uploaddatabase(int $dev0101): \CodeIgniter\HTTP\ResponseInterface
    {
        $dev01 = $this->dev01Model->find($dev0101);
        if ($dev01) {
            if ($dev01->dev0107) {
                helper('common');
                pushToDevice(['updatabase' => 1, 'description' => 'Upload Database:' . $dev01->dev0104], $dev01->dev0107);
                return $this->ajaxReturn('OK');
            } else {
                return $this->ajaxReturn(lang('Device.label_dev0107') . ' is null');
            }
        }
        return $this->ajaxReturn('Device not found');
    }

    /**
     * Request APP to execute SQL
     */
    public function execSql(int $dev0101): \CodeIgniter\HTTP\ResponseInterface
    {
        $dev01 = $this->dev01Model->find($dev0101);
        if ($dev01) {
            $sql = $this->request->getPost('sql');
            if ($dev01->dev0107 && $sql) {
                helper('common');
                pushToDevice(['execute' => $sql, 'description' => 'Execute SQL:' . $dev01->dev0104], $dev01->dev0107);
                return $this->ajaxReturn('OK');
            } else {
                return $this->ajaxReturn(lang('Device.label_dev0107') . ' is null');
            }
        }
        return $this->ajaxReturn('Device not found');
    }
}
