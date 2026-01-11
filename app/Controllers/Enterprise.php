<?php

namespace App\Controllers;

/**
 * Enterprise Controller - 企業管理
 */
class Enterprise extends AdminController
{
    protected $ent01Model;
    protected $ent02Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper('language');
        $this->ent01Model = model('Ent01Model');
        $this->ent02Model = model('Ent02Model');
    }

    public function index(): string
    {
        $this->clearQueryOption();

        $pageSize = $this->getPageSize();
        $totalRows = $this->ent01Model->countBy(null);
        $this->data['data'] = $this->ent01Model->getBy(null, $pageSize);
        $this->setPage($totalRows, 1, $pageSize);

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

        $pageSize = $this->getPageSize();
        $offset = ($page - 1) * $pageSize;
        $totalRows = $this->ent01Model->countBy($option);
        $this->data['data'] = $this->ent01Model->getBy($option, $pageSize, $offset);
        $this->setPage($totalRows, $page, $pageSize);

        return $this->render('');
    }

    public function detail(int $id): string
    {
        $this->data['data'] = $this->ent01Model->find($id);
        $this->data['ent02s'] = $this->ent02Model->getBy(['ent0202' => $id]);
        $this->data['smart_patrol_device'] = $this->ent02Model->getLicenseCount('smart_patrol_device', $id);
        $this->data['smart_patrol_form'] = $this->ent02Model->getLicenseCount('smart_patrol_form', $id);

        return $this->render();
    }

    public function edit(int $id = 0): string|\CodeIgniter\HTTP\ResponseInterface
    {
        if ($this->request->getMethod() === 'post') {
            $postData = $this->request->getPost();

            $rules = [
                'ent0102' => 'required',
                'ent0103' => 'required',
            ];

            // Additional rules for new enterprise
            if (empty($postData['ent0101'])) {
                if ($this->request->getPost($this->user->identityColumn)) {
                    if ($this->user->identityColumn === 'sys0102') {
                        $rules['sys0102'] = 'required|is_unique[sys01.sys0102]';
                    }
                    if ($this->user->identityColumn === 'sys0107') {
                        $rules['sys0107'] = 'required|valid_email|is_unique[sys01.sys0107]';
                    }

                    $rbacConfig = config('Rbac');
                    $minLength = $rbacConfig->minPasswordLength ?? 8;
                    $maxLength = $rbacConfig->maxPasswordLength ?? 20;

                    $rules['sys0105'] = "required|min_length[{$minLength}]|max_length[{$maxLength}]|matches[sys0105_2]";
                    $rules['sys0105_2'] = 'required';
                    $rules['sys0103'] = 'required';
                }
            }

            if ($this->validate($rules)) {
                // Check for duplicates
                if (!$this->ent0102Check($postData['ent0102'])) {
                    return $this->ajaxReturn(lang('Enterprise.ent0102_check'));
                }
                if (!$this->ent0103Check($postData['ent0103'])) {
                    return $this->ajaxReturn(lang('Enterprise.ent0103_check'));
                }

                if (empty($postData['ent0101'])) {
                    // Insert new enterprise
                    if ($ent0101 = $this->ent01Model->insert($postData)) {
                        $rol01Model = model('Rol01Model');
                        $rol0101 = $rol01Model->addEnterpriseAdmin($ent0101);

                        $sys01Model = model('Sys01Model');
                        $sys02Model = model('Sys02Model');

                        // Add enterprise admin user
                        if ($this->request->getPost($this->user->identityColumn)) {
                            $postData['sys0106'] = $this->user->generateSalt();
                            $postData['sys0105'] = $this->user->hashPassword($postData['sys0105'], $postData['sys0106']);
                            $postData['sys0108'] = 1;

                            if ($sys0101 = $sys01Model->insert($postData)) {
                                $sys02Model->insert(['sys0202' => $sys0101, 'sys0203' => $ent0101, 'sys0204' => $rol0101, 'sys0205' => '1']);
                            }
                        }

                        // Add current user as enterprise admin
                        if ($this->request->getPost('add_me') === '1') {
                            $sys02Model->insert(['sys0202' => $this->user->id, 'sys0203' => $ent0101, 'sys0204' => $rol0101, 'sys0205' => '1']);
                        }

                        // Add to department table
                        $ent10Model = model('Ent10Model');
                        $ent10Model->insert([
                            'ent1002' => $ent0101,
                            'ent1003' => $postData['ent0102'],
                            'ent1004' => $postData['ent0103'],
                            'ent1005' => 0,
                            'ent1007' => 1,
                        ]);

                        helper('common');
                        $ent01 = $this->ent01Model->find($ent0101);
                        pushToDevice(['ent01' => [$ent01], 'description' => '']);

                        return $this->ajaxReturn('OK', ['id' => $ent0101, 'title' => $postData['ent0103']]);
                    }
                } else {
                    // Update
                    $updateLicense = false;
                    $licenseCount = ['smart_patrol_device' => 0, 'smart_patrol_form' => 0];

                    $ent01 = $this->ent01Model->find($postData['ent0101']);
                    if ($ent01 && $ent01->ent0102 !== $postData['ent0102']) {
                        $updateLicense = true;
                        $ent02s = $this->ent02Model->getBy(['ent0202' => $postData['ent0101']]);
                        if ($ent02s) {
                            foreach ($ent02s as $ent02) {
                                if (in_array($ent02->ent0206, ['smart_patrol_device', 'smart_patrol_form'])) {
                                    $licenseCount[$ent02->ent0206] = $this->ent02Model->getLicenseCount($ent02->ent0206, $ent02->ent0202);
                                }
                            }
                        }
                    }

                    if ($this->ent01Model->save($postData)) {
                        $updateEnt0103 = false;
                        if ($this->session->get('ent0101') == $postData['ent0101']) {
                            $this->session->set('ent0102', $postData['ent0102']);
                            $this->session->set('ent0103', $postData['ent0103']);
                            $updateEnt0103 = true;
                        }

                        if ($updateLicense) {
                            $ent02s = $this->ent02Model->getBy(['ent0202' => $postData['ent0101']]);
                            if ($ent02s) {
                                foreach ($ent02s as $ent02) {
                                    if (in_array($ent02->ent0206, ['smart_patrol_device', 'smart_patrol_form'])) {
                                        $ent02->value = $licenseCount[$ent02->ent0206];
                                        $this->ent02Model->save($ent02);
                                    }
                                }
                            }
                        }

                        helper('common');
                        $ent01 = $this->ent01Model->find($id);
                        pushToDevice(['ent01' => [$ent01], 'description' => '']);

                        return $this->ajaxReturn('OK', ['id' => $id, 'title' => $postData['ent0103'], 'update_ent0103' => $updateEnt0103]);
                    }
                }
            }

            return $this->ajaxReturn($this->message->output());
        }

        // GET request
        if ($id) {
            $this->data['data'] = $this->ent01Model->find($id);
            return $this->render();
        } else {
            return view('enterprise/add');
        }
    }

    public function delete(): \CodeIgniter\HTTP\ResponseInterface
    {
        // Support both GET and POST requests - try multiple methods
        $id = $this->request->getPost('id') ?? $this->request->getGet('id') ?? $this->request->getVar('id');

        if ($id) {
            if ($this->ent01Model->delete($id)) {
                return $this->ajaxReturn('OK');
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('No ID provided');
    }

    protected function ent0102Check(string $ent0102): bool
    {
        $postId = $this->request->getPost('ent0101');
        if (empty($postId)) {
            $data = $this->ent01Model->getBy(['ent0102' => $ent0102]);
            return count($data) === 0;
        }
        return !empty($ent0102);
    }

    protected function ent0103Check(string $ent0103): bool
    {
        $postId = $this->request->getPost('ent0101');
        if (empty($postId)) {
            $data = $this->ent01Model->getBy(['ent0103' => $ent0103]);
            return count($data) === 0;
        }
        return !empty($ent0103);
    }

    public function detailEnt02(int $id): string
    {
        $this->data['data'] = $this->ent02Model->find($id);
        return $this->render();
    }

    public function editEnt02(int $id = 0): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $cod02Model = model('Cod02Model');

        if ($this->request->getMethod() === 'post') {
            $postData = $this->request->getPost();

            $rules = [
                'ent0206' => 'required',
                'value' => 'required',
            ];

            if ($this->validate($rules)) {
                $postData['ent0203'] = $cod02Model->getValue('ent0206', $postData['ent0206']);

                if (empty($postData['ent0201'])) {
                    if ($newId = $this->ent02Model->insert($postData)) {
                        return $this->ajaxReturn('OK', ['id' => $newId, 'title' => $postData['ent0203']]);
                    }
                } else {
                    if ($this->ent02Model->save($postData)) {
                        return $this->ajaxReturn('OK', ['id' => $id, 'title' => $postData['ent0203']]);
                    }
                }
            }

            return $this->ajaxReturn($this->message->output());
        }

        $this->data['ent0206_option'] = $cod02Model->getOptionArrayByCode('ent0206', ['' => '']);

        if ($id) {
            $this->data['data'] = $this->ent02Model->get($id);
        } else {
            $this->data['data'] = null;
            $this->data['ent0101'] = $this->request->getGet('parent');
        }

        return $this->render();
    }

    public function deleteEnt02(): \CodeIgniter\HTTP\ResponseInterface
    {
        // Support both GET and POST requests - try multiple methods
        $id = $this->request->getPost('id') ?? $this->request->getGet('id') ?? $this->request->getVar('id');

        if ($id) {
            if ($this->ent02Model->delete($id)) {
                return $this->ajaxReturn('OK');
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('No ID provided');
    }

    public function uploadLogo(): void
    {
        $dir = FCPATH . 'data/logo/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $uploadedFile = $this->request->getFile('file');
        if ($uploadedFile && $uploadedFile->isValid()) {
            $newName = 'enterprise_logo.' . $uploadedFile->getExtension();
            @unlink($dir . $newName);
            $uploadedFile->move($dir, $newName);

            echo json_encode(['files' => [['name' => $newName, 'url' => base_url('data/logo/' . $newName)]]]);
        }
    }
}
