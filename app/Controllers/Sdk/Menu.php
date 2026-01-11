<?php

namespace App\Controllers\Sdk;

use App\Controllers\AdminController;
use App\Models\Sys05Model;
use App\Models\Sys04Model;

/**
 * 功能表管理控制器
 */
class Menu extends AdminController
{
    protected Sys05Model $sys05Model;

    public function __construct()
    {
        parent::__construct();
        $this->sys05Model = model('Sys05Model');
    }

    public function index(): string
    {
        return $this->render();
    }

    public function detail(int $id): string
    {
        $this->data['data'] = $this->sys05Model->get($id);
        return $this->render();
    }

    public function edit(int $id = 0, ?int $parentId = null): string
    {
        $request = service('request');

        if ($postData = $request->getPost()) {
            // 檢查
            $validation = service('validation');
            $validation->setRules([
                'sys0502' => 'required',
                'sys0503' => 'required',
            ], [
                'sys0502' => ['required' => '項目名称必填'],
                'sys0503' => ['required' => 'Icon必填'],
            ]);

            if ($validation->withRequest($request)->run()) {
                if (!array_key_exists('sys0501', $postData)) {
                    // 新增
                    if ($postData['sys0506'] == 0) {
                        $postData['sys0506'] = null;
                    }
                    if ($newId = $this->sys05Model->insert($postData)) {
                        $obj = $this->sys05Model->get($newId);
                        return $this->ajaxReturn('OK', ['id' => $newId, 'parent' => $obj->sys0506]);
                    }
                } else {
                    // 修改
                    if ($this->sys05Model->update($postData['sys0501'], $postData)) {
                        return $this->ajaxReturn('OK', ['id' => $id, 'title' => $postData['sys0502']]);
                    }
                }
            }
            return $this->ajaxReturn($this->message->output());
        }

        if ($id) {
            // 修改
            $this->data['data'] = $this->sys05Model->get($id);
        } else {
            $this->data['data'] = null;
        }

        $sys04Model = model('Sys04Model');
        $this->data['pages'] = $sys04Model->getNameArray('sys0401', 'sys0402', ['' => '目录'], ['sys0408' => 1], 'sys0405');

        if ($parentId === null) {
            $this->data['title'] = '功能表项目信息';
        } else {
            $this->data['title'] = '添加功能表项目';
            $this->data['sys0506'] = $parentId;
        }

        return $this->render();
    }

    /**
     * 提供前端Tree元件取得部門結構資訊
     */
    public function tree(): string
    {
        $request = service('request');
        $id = $request->getGet('id');

        if ($id !== null) {
            if ($id === '#') {
                $result = [
                    'id' => '0',
                    'text' => '主功能表',
                    'children' => true,
                    'icon' => 'fa fa-list'
                ];
            } else {
                $node = (int)$id;
                $data = $this->sys05Model->getMenu(($node === 0 ? null : $node), true, '*');
                $result = [];

                foreach ($data as $v) {
                    $n = [
                        'id' => $v->sys0501,
                        'text' => $v->sys0502,
                        'icon' => 'fa ' . $v->sys0503
                    ];

                    if ($this->sys05Model->where('sys0506', $v->sys0501)->countAllResults()) {
                        $n['children'] = true;
                    }
                    $result[] = $n;
                }
            }

            return $this->response->setJSON($result)->getBody();
        }

        return '';
    }
}
