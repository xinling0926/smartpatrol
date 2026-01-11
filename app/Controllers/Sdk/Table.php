<?php

namespace App\Controllers\Sdk;

use App\Controllers\AdminController;
use App\Models\Sys30Model;

/**
 * 資料表管理控制器
 */
class Table extends AdminController
{
    protected Sys30Model $sys30Model;

    public function __construct()
    {
        parent::__construct();
        $this->sys30Model = model('Sys30Model');
    }

    public function index(): string
    {
        $this->clearQueryOption();
        $pageSize = $this->getPageSize();
        $totalRows = $this->sys30Model->countBy(null);
        $this->data['list'] = $this->sys30Model->getBy(null, $pageSize);
        $this->setPage($totalRows, 1, $pageSize);

        if (count($this->data['list']) > 0) {
            $this->data['data'] = $this->sys30Model->get($this->data['list'][0]->sys3001);
        }

        return $this->render();
    }

    public function getTableList(): string
    {
        $result = [
            'data' => $this->sys30Model->getTableList(),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'draw' => 1,
        ];
        $result['recordsTotal'] = count($result['data']);

        return $this->json($result);
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
        $totalRows = $this->sys30Model->countBy($option);
        $this->data['list'] = $this->sys30Model->getBy($option, $pageSize, $offset);
        $this->setPage($totalRows, $page, $pageSize);

        return $this->render('');
    }

    public function detail(int $id): string
    {
        $this->data['data'] = $this->sys30Model->get($id);
        return $this->render();
    }

    public function edit(int $id = 0): string
    {
        $request = service('request');

        if ($postData = $request->getPost()) {
            // 檢查
            $validation = service('validation');
            $validation->setRules([
                'sys3002' => 'required|is_unique[sys30.sys3002]',
            ], [
                'sys3002' => [
                    'required' => 'Table Name必填',
                    'is_unique' => 'Table Name已存在',
                ],
            ]);

            if ($validation->withRequest($request)->run()) {
                if (!array_key_exists('sys3001', $postData)) {
                    // 新增
                    if ($newId = $this->sys30Model->insert($postData)) {
                        return $this->ajaxReturn('OK', ['id' => $newId, 'title' => $postData['sys3002']]);
                    }
                } else {
                    // 修改
                    if ($this->sys30Model->update($postData['sys3001'], $postData)) {
                        return $this->ajaxReturn('OK', ['id' => $id, 'title' => $postData['sys3002']]);
                    }
                }
            }
            return $this->ajaxReturn($this->message->output());
        }

        if ($id) {
            // 修改
            $this->data['data'] = $this->sys30Model->get($id);
        } else {
            $this->data['data'] = null;
        }

        return $this->render();
    }

    public function delete(): string
    {
        $request = service('request');
        $id = $request->getPost('id');

        if ($id) {
            if ($this->sys30Model->delete($id)) {
                return $this->ajaxReturn('OK');
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }

        return $this->ajaxReturn('Invalid ID');
    }
}
