<?php

namespace App\Controllers;

/**
 * Account Controller - 帳號管理
 */
class Account extends AdminController
{
    protected $sys01Model;
    protected $sys02Model;
    protected $rol01Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper('language');

        $this->sys01Model = model('Sys01Model');
        $this->sys02Model = model('Sys02Model');
        $this->rol01Model = model('Rol01Model');
    }

    public function index(): string
    {
        $ent0101 = $this->session->get('ent0101');
        $option = ['sys0203' => $ent0101, '*sys0205' => 'sys0205>0'];

        // If not admin, don't list admin account
        if (!$this->user->isAdmin) {
            $option['*sys0202'] = 'sys0202!=1';
        }

        $this->data['sys0205'] = 1;
        $this->setQueryOption($option);

        $pageSize = $this->getPageSize();
        $totalRows = $this->sys01Model->countBy($option);

        $this->data['role'] = $this->rol01Model->getNameArray('rol0101', 'rol0103', [], ['rol0102' => $ent0101]);
        $this->data['data'] = $this->sys01Model->getBy($option, $pageSize);

        $ent10Model = model('Ent10Model');
        $this->data['dept'] = $ent10Model->getNameArray('ent1001', 'ent1004', ['' => lang('Auth.account_index_dept_filter')], ['ent1002' => $ent0101, 'ent1007' => 1]);
        $this->data['identity_column'] = $this->user->identityColumn;
        $this->setPage($totalRows, 1, $pageSize);

        return $this->render();
    }

    public function query(int $page = 0): string
    {
        $ent0101 = $this->session->get('ent0101');

        if ($this->request->is('post')) {
            $page = 1;
            $option = $this->getQueryOption();
        } else {
            $option = $this->loadQueryOption();
        }

        if (isset($option['search']) && $option['search']) {
            $search = $this->db->escapeLikeString($option['search']);
            $option['*search'] = "(CONCAT(sys0103, sys0104) like '%{$search}%' or sys0102 like '%{$search}%')";
            unset($option['search']);
        }

        if (isset($option['sys0205']) && $option['sys0205']) {
            $option['*sys0205'] = 'sys0205>0';
            unset($option['sys0205']);
        }

        $option['sys0203'] = $ent0101;

        if (!$this->user->isAdmin) {
            $option['*sys0202'] = 'sys0202!=1';
        }

        $pageSize = $this->getPageSize();
        $offset = ($page - 1) * $pageSize;
        $totalRows = $this->sys01Model->countBy($option);

        $this->data['role'] = $this->rol01Model->getNameArray('rol0101', 'rol0103', [], ['rol0102' => $ent0101]);
        $this->data['data'] = $this->sys01Model->getBy($option, $pageSize, $offset);

        $ent10Model = model('Ent10Model');
        $this->data['dept'] = $ent10Model->getNameArray('ent1001', 'ent1004', [], ['ent1002' => $ent0101]);

        $this->setPage($totalRows, $page, $pageSize);
        $this->data['identity_column'] = $this->user->identityColumn;

        return $this->render('');
    }

    public function detail(int $id): string
    {
        $this->data['identity_column'] = $this->user->identityColumn;
        $this->data['data'] = $this->sys01Model->getBy(['sys0201' => $id], 1);

        $ent0101 = $this->session->get('ent0101');
        $this->data['role'] = $this->rol01Model->getNameArray('rol0101', 'rol0103', [], ['rol0102' => $ent0101]);

        $ent10Model = model('Ent10Model');
        $this->data['dept'] = $ent10Model->find($this->data['data']->sys0110 ?? null);

        return $this->render();
    }

    public function edit(int $id = 0): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $ent0101 = $this->session->get('ent0101');

        if ($this->request->is('post')) {
            $postData = $this->request->getPost();

            // Set validation rules
            $rules = [
                'sys0103' => 'required',
            ];

            if (empty($postData['sys0201'])) {
                // New record
                if ($this->user->identityColumn === 'sys0102') {
                    $rules['sys0102'] = 'required|is_unique[sys01.sys0102]';
                }
                if ($this->user->identityColumn === 'sys0107') {
                    $rules['sys0107'] = 'required|is_unique[sys01.sys0107]';
                }

                $rbacConfig = config('Rbac');
                $minLength = $rbacConfig->minPasswordLength ?? 8;
                $maxLength = $rbacConfig->maxPasswordLength ?? 20;

                $rules['sys0105'] = "required|min_length[{$minLength}]|max_length[{$maxLength}]|matches[sys0105_2]";
                $rules['sys0105_2'] = 'required';
                $rules['sys0204'] = 'required';
            } else {
                // Update - password is optional
                if (!empty($postData['sys0105'])) {
                    $rbacConfig = config('Rbac');
                    $minLength = $rbacConfig->minPasswordLength ?? 8;
                    $maxLength = $rbacConfig->maxPasswordLength ?? 20;

                    $rules['sys0105'] = "min_length[{$minLength}]|max_length[{$maxLength}]|matches[sys0105_2]";
                    $rules['sys0105_2'] = 'required';
                }
            }

            if (!empty($postData['sys0107'])) {
                $rules['sys0107'] = 'valid_email';
            }

            if ($this->validate($rules)) {
                // Handle signature file
                if (isset($postData['sys0120'])) {
                    $fileName = $postData['sys0120'];
                    if ($fileName === '') {
                        unset($postData['sys0120']);
                    } elseif (mb_strlen($fileName) > 20) {
                        $postData['sys0120'] = date('YmdHis') . '.' . strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    }
                }

                if (empty($postData['sys0201'])) {
                    // Insert
                    $password = $postData['sys0105'];
                    $postData['sys0106'] = $this->user->generateSalt();
                    $postData['sys0105'] = $this->user->hashPassword($password, $postData['sys0106']);
                    $postData['sys0108'] = 1;

                    if ($sys0101 = $this->sys01Model->insert($postData)) {
                        $postData['sys0202'] = $sys0101;
                        $postData['sys0203'] = $ent0101;

                        if ($newId = $this->sys02Model->insert($postData)) {
                            if (isset($postData['sys0120'])) {
                                $this->saveSignFile($sys0101, $fileName, $postData['sys0120']);
                            }

                            $sys01 = $this->sys01Model->find($sys0101);
                            helper('common');
                            pushToDevice(['sys01' => [$sys01], 'description' => '']);

                            return $this->ajaxReturn('OK', ['id' => $newId, 'title' => userDisplayName($sys01)]);
                        }
                    }
                } else {
                    // Update
                    $password = $postData['sys0105'] ?? '';
                    unset($postData['sys0105']);

                    if ($this->sys01Model->save($postData) && $this->sys02Model->save($postData)) {
                        if (isset($postData['sys0120'])) {
                            $this->saveSignFile($postData['sys0101'], $fileName, $postData['sys0120']);
                        }

                        if ($password) {
                            $this->user->changePassword($postData['sys0101'], $password);
                        }

                        $sys01 = $this->sys01Model->find($postData['sys0101']);
                        helper('common');
                        pushToDevice(['sys01' => [$sys01], 'description' => '']);

                        return $this->ajaxReturn('OK', ['id' => $id, 'title' => userDisplayName($sys01)]);
                    }
                }
            }

            return $this->ajaxReturn($this->message->output());
        }

        // GET request - display form
        $ent10Model = model('Ent10Model');
        $ent20Model = model('Ent20Model');

        $this->data['identity_column'] = $this->user->identityColumn;
        $this->data['dept'] = $ent10Model->getNameArray('ent1001', 'ent1004', ['' => ''], ['ent1002' => $ent0101, 'ent1007' => 1]);
        $this->data['jobtitle'] = $ent20Model->getNameArray('ent2004', 'ent2004', ['' => ''], ['ent2002' => $ent0101, 'ent2005' => 1]);

        if ($id) {
            $this->data['data'] = $this->sys01Model->getBy(['sys0201' => $id], 1);
            $this->data['role'] = $this->rol01Model->getEditRole($ent0101, $this->data['data']->sys0204 ?? null);
        } else {
            $this->data['data'] = null;
            $this->data['role'] = $this->rol01Model->getEditRole($ent0101, null, 2);
        }

        // Hide admin role if not admin
        if (!$this->user->isAdmin) {
            $adminUser = $this->sys01Model->getByIdentity('sysadmin');
            if ($adminUser && isset($this->data['role'][$adminUser->sys0101])) {
                unset($this->data['role'][$adminUser->sys0101]);
            }
        }

        return $this->render();
    }

    public function profile(): string
    {
        return $this->render('main');
    }

    public function uploadSys0120(): void
    {
        $options = [
            'upload_dir' => FCPATH . 'data/temp/',
            'upload_url' => base_url('data/temp') . '/',
            'user_dirs' => true,
            'max_width' => 2000,
            'max_height' => 2000,
            'min_width' => 100,
            'min_height' => 100,
            'max_file_size' => 5 * 1024 * 1024,
        ];

        // Handle upload using CI4 file upload
        $uploadedFile = $this->request->getFile('file');
        if ($uploadedFile && $uploadedFile->isValid()) {
            $newName = $uploadedFile->getRandomName();
            $uploadedFile->move($options['upload_dir'], $newName);
            echo json_encode(['files' => [['name' => $newName, 'url' => $options['upload_url'] . $newName]]]);
        }
    }

    public function uploadAvatar(): void
    {
        $currentUserId = $this->session->get('sys0101');
        $dir = FCPATH . 'data/avatar/' . $currentUserId . '/';

        if (is_dir($dir)) {
            array_map('unlink', glob($dir . '*'));
        } else {
            mkdir($dir, 0777, true);
        }

        $uploadedFile = $this->request->getFile('file');
        if ($uploadedFile && $uploadedFile->isValid()) {
            $newName = date('YmdHis') . '.' . $uploadedFile->getExtension();
            $uploadedFile->move($dir, $newName);

            // Update database
            $this->sys01Model->update($currentUserId, ['sys0117' => $newName]);

            // Update session
            $this->session->set('avatar', $currentUserId . '/' . $newName);

            echo json_encode(['files' => [['name' => $newName, 'url' => base_url($dir . $newName)]]]);
        }
    }

    private function saveSignFile(int $sys0101, string $from, string $to): void
    {
        $dir = FCPATH . 'data/sign/' . $sys0101;
        if (is_dir($dir)) {
            array_map('unlink', glob($dir . '/*'));
        } else {
            mkdir($dir, 0777, true);
        }

        $sessionId = session_id();
        rename(FCPATH . 'data/temp/' . $sessionId . '/' . $from, $dir . '/' . $to);
    }

    public function changePassword(): \CodeIgniter\HTTP\ResponseInterface
    {
        $rbacConfig = config('Rbac');
        $minLength = $rbacConfig->minPasswordLength ?? 8;
        $maxLength = $rbacConfig->maxPasswordLength ?? 20;

        $rules = [
            'password' => 'required',
            'sys0105' => "required|min_length[{$minLength}]|max_length[{$maxLength}]|matches[sys0105_2]",
            'sys0105_2' => 'required',
        ];

        if ($this->validate($rules)) {
            $currentUser = $this->sys01Model->find($this->session->get('sys0101'));

            // Check old password
            if (!$this->user->passwordVerify($currentUser->sys0105, $this->request->getPost('password'), $currentUser->sys0106)) {
                return $this->ajaxReturn('ERR', lang('Account.old_password_error'));
            }

            $this->user->changePassword($currentUser->sys0101, $this->request->getPost('sys0105'));

            helper('common');
            pushToDevice(['sys01' => '1', 'description' => '']);

            return $this->ajaxReturn('OK', lang('Account.change_password_success'));
        }

        return $this->ajaxReturn('ERR', $this->message->output());
    }

    public function resetAllPassword(int $ent0101, string $password = ''): void
    {
        if (!$password) {
            echo '未指定密碼！';
            return;
        }

        if (!$this->user->isAdmin) {
            echo '不是系統管理員！';
            return;
        }

        $users = $this->sys01Model->getBy(['sys0203' => $ent0101]);
        foreach ($users as $user) {
            $this->user->changePassword($user->sys0101, $password);
            echo $user->sys0102 . '<br>';
        }
    }
}
