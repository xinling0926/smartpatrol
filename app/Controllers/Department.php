<?php

namespace App\Controllers;

/**
 * Department Controller - 部門管理
 */
class Department extends AdminController
{
    protected $ent10Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper('language');
        $this->ent10Model = model('Ent10Model');
    }

    public function index(): string
    {
        return $this->render();
    }

    public function detail(int $id): string
    {
        $this->data['data'] = $this->ent10Model->find($id);
        return $this->render();
    }

    public function edit(int $id = 0, ?int $parentId = null): string|\CodeIgniter\HTTP\ResponseInterface
    {
        if ($this->request->getMethod() === 'post') {
            $postData = $this->request->getPost();

            $rules = [
                'ent1004' => 'required',
            ];

            if ($this->validate($rules)) {
                if (empty($postData['ent1001'])) {
                    // Insert
                    $postData['ent1002'] = $this->session->get('ent0101');

                    if ($newId = $this->ent10Model->insert($postData)) {
                        $obj = $this->ent10Model->find($newId);

                        helper('common');
                        pushToDevice(['ent10' => [$obj], 'description' => '']);

                        return $this->ajaxReturn('OK', ['id' => $newId, 'parent' => $obj->ent1005]);
                    }
                } else {
                    // Update
                    if ($this->ent10Model->save($postData)) {
                        $title = $postData['ent1004'];
                        if (isset($postData['ent1007']) && $postData['ent1007'] == 0) {
                            $title .= '(停用)';
                        }

                        $obj = $this->ent10Model->find($id);
                        helper('common');
                        pushToDevice(['ent10' => [$obj], 'description' => '']);

                        return $this->ajaxReturn('OK', ['id' => $id, 'title' => $title]);
                    }
                }
            }

            return $this->ajaxReturn($this->message->output());
        }

        // GET request
        if ($id) {
            $this->data['data'] = $this->ent10Model->find($id);
        } else {
            $this->data['data'] = null;
        }

        if ($parentId === null) {
            $this->data['title'] = lang('Department.edit_heading');
        } else {
            $this->data['title'] = lang('Department.add_heading');
            $this->data['ent1005'] = $parentId;
        }

        return $this->render();
    }

    /**
     * Provide department tree structure for frontend Tree component
     */
    public function tree(): \CodeIgniter\HTTP\ResponseInterface
    {
        $id = $this->request->getGet('id');

        if ($id === '#') {
            $node = 0;
            $icon = 'fa fa-university';
        } else {
            $node = (int)$id;
            $icon = 'fa fa-users';
        }

        $ent0101 = $this->session->get('ent0101');
        $data = $this->ent10Model->where('ent1002', $ent0101)
            ->where('ent1005', $node)
            ->findAll();

        $result = [];
        foreach ($data as $v) {
            $title = $v->ent1004;
            if ($v->ent1007 == 0) {
                $title .= '(' . lang('Department.v_ent1007_2') . ')';
            }

            $n = [
                'id' => $v->ent1001,
                'text' => $title,
                'icon' => $icon,
            ];

            $childCount = $this->ent10Model->where('ent1005', $v->ent1001)->countAllResults();
            if ($childCount > 0) {
                $n['children'] = true;
            }

            $result[] = $n;
        }

        return $this->response->setJSON($result);
    }
}
