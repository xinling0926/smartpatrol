<?php

namespace App\Controllers;

/**
 * Role Controller - 角色管理
 */
class Role extends AdminController
{
    protected $rol01Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper('language');
        $this->rol01Model = model('Rol01Model');
    }

    public function index(): string
    {
        $this->clearQueryOption();
        $ent0101 = $this->session->get('ent0101');
        $option = ['rol0102' => $ent0101];

        if (!$this->user->isAdmin) {
            $option['*rol0101'] = 'rol0101!=1';
        }

        $pageSize = $this->getPageSize();
        $totalRows = $this->rol01Model->countBy($option);
        $this->data['data'] = $this->rol01Model->getBy($option, $pageSize);
        $this->setPage($totalRows, 1, $pageSize);

        $cod02Model = model('Cod02Model');
        $this->data['rol0106_option'] = $cod02Model->getOptionArrayByCode('rol0106');

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

        $cod02Model = model('Cod02Model');
        $this->data['rol0106_option'] = $cod02Model->getOptionArrayByCode('rol0106');

        $pageSize = $this->getPageSize();
        $offset = ($page - 1) * $pageSize;
        $option['rol0102'] = $this->session->get('ent0101');

        $totalRows = $this->rol01Model->countBy($option);
        $this->data['data'] = $this->rol01Model->getBy($option, $pageSize, $offset);
        $this->setPage($totalRows, $page, $pageSize);

        return $this->render('');
    }

    public function detail(int $id): string
    {
        $ent0101 = $this->session->get('ent0101');
        $lang = $this->request->getLocale();
        $field = ($lang === 'zh-CN') ? 'sys0402' : 'sys0412';

        $this->data['data'] = $this->rol01Model->find($id);
        $this->data['data']->role_function = '';

        // Get all page info
        $option = ['sys0408' => 1, 'sys0406' => 1];
        if (!$this->user->isAdmin) {
            $option['sys0410'] = 0;
        }

        $ent02Model = model('Ent02Model');
        $sys0411 = [0];

        helper('common');
        $of = getOptionModule(false);
        foreach ($of as $k => $item) {
            if ($ent02Model->getLicenseCount('smart_patrol_of' . $k, $ent0101)) {
                $sys0411[] = $k;
            }
        }
        $option['*sys0411'] = 'sys0411 in (' . implode(',', $sys0411) . ')';

        $sys04Model = model('Sys04Model');
        $this->data['sys04s'] = $sys04Model->getNameArray('sys0401', $field, [], $option, 'sys0405');

        $cod02Model = model('Cod02Model');
        $this->data['rol0106_option'] = $cod02Model->getOptionArrayByCode('rol0106');

        // Handle wildcard permissions
        if ($this->data['data']->rol0105 === '*') {
            $this->data['data']->rol0105 = implode(',', array_keys($this->data['sys04s']));
        }

        return $this->render();
    }

    public function edit(int $id = 0): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $ent0101 = $this->session->get('ent0101');

        if ($this->request->is('post')) {
            $postData = $this->request->getPost();

            if (isset($postData['rol0105']) && is_array($postData['rol0105'])) {
                $postData['rol0105'] = implode(',', $postData['rol0105']);
            } else {
                $postData['rol0105'] = '';
            }

            // Remove role_function as it's not in the database
            unset($postData['role_function']);

            $rules = [
                'rol0103' => 'required',
            ];

            if ($this->validate($rules)) {
                // Check for duplicate role name
                if (!$this->roleNameCheck($postData['rol0103'])) {
                    return $this->ajaxReturn(lang('Role.rol0103_duplicate'));
                }

                if (empty($postData['rol0101'])) {
                    // Insert
                    $postData['rol0102'] = $ent0101;

                    if ($newId = $this->rol01Model->insert($postData)) {
                        return $this->ajaxReturn('OK', ['id' => $newId, 'title' => $postData['rol0103']]);
                    }
                } else {
                    // Update
                    if ($this->rol01Model->save($postData)) {
                        return $this->ajaxReturn('OK', ['id' => $id, 'title' => $postData['rol0103']]);
                    }
                }
            }

            return $this->ajaxReturn($this->message->output());
        }

        // GET request
        $lang = $this->request->getLocale();
        $field = ($lang === 'zh-CN') ? 'sys0402' : 'sys0412';

        $option = ['sys0408' => 1, 'sys0406' => 1];
        if (!$this->user->isAdmin) {
            $option['sys0410'] = 0;
        }

        $ent02Model = model('Ent02Model');
        $sys0411 = [0];

        helper('common');
        $of = getOptionModule(false);
        foreach ($of as $k => $item) {
            if ($ent02Model->getLicenseCount('smart_patrol_of' . $k, $ent0101)) {
                $sys0411[] = $k;
            }
        }
        $option['*sys0411'] = 'sys0411 in (' . implode(',', $sys0411) . ')';

        $sys04Model = model('Sys04Model');
        $this->data['sys05s'] = $sys04Model->getNameArray('sys0401', $field, [], $option, 'sys0405');

        $cod02Model = model('Cod02Model');
        $this->data['rol0106_option'] = $cod02Model->getOptionArrayByCode('rol0106');

        if ($id) {
            $this->data['data'] = $this->rol01Model->find($id);
            $this->data['data']->role_function = '';

            if ($this->data['data']->rol0105 === '*') {
                $this->data['data']->rol0105 = implode(',', array_keys($this->data['sys05s']));
            }
        } else {
            $this->data['data'] = null;
        }

        return $this->render();
    }

    public function delete(): \CodeIgniter\HTTP\ResponseInterface
    {
        $id = $this->request->getPost('id');
        if ($id) {
            if ($this->rol01Model->delete($id)) {
                return $this->ajaxReturn('OK');
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('No ID provided');
    }

    protected function roleNameCheck(string $roleName): bool
    {
        $postId = $this->request->getPost('rol0101');
        if (empty($postId)) {
            // New record
            $data = $this->rol01Model->getByRol0102Rol0103($this->session->get('ent0101'), $roleName);
            return count($data) === 0;
        }
        // Update - just check if name is not empty
        return !empty($roleName);
    }
}
