<?php

namespace App\Controllers\Sdk;

use App\Controllers\AdminController;
use App\Models\Sys04Model;
use App\Models\Cod02Model;

/**
 * 頁面管理控制器
 */
class Page extends AdminController
{
    protected Sys04Model $sys04Model;

    public function __construct()
    {
        parent::__construct();
        $this->sys04Model = model('Sys04Model');
    }

    public function index(): string
    {
        $this->clearQueryOption();
        $pageSize = $this->getPageSize();
        $totalRows = $this->sys04Model->countBy(null);
        $this->data['data'] = $this->sys04Model->getBy(null, $pageSize);
        $this->data['offset'] = 0;

        $cod02Model = model('Cod02Model');
        $this->data['sys0406_opt'] = $cod02Model->getOptionArrayByCode('sys0406');
        $this->data['of'] = get_option_module();
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
        $totalRows = $this->sys04Model->countBy($option);
        $this->data['data'] = $this->sys04Model->getBy($option, $pageSize, $offset);
        $this->data['offset'] = $offset;
        $this->setPage($totalRows, $page, $pageSize);

        $cod02Model = model('Cod02Model');
        $this->data['sys0406_opt'] = $cod02Model->getOptionArrayByCode('sys0406');
        $this->data['of'] = get_option_module();

        return $this->render('');
    }

    public function detail(int $id): string
    {
        $this->data['data'] = $this->sys04Model->get($id);

        $cod02Model = model('Cod02Model');
        $this->data['sys0406_opt'] = $cod02Model->getOptionArrayByCode('sys0406');
        $this->data['of'] = get_option_module();

        return $this->render();
    }

    public function edit(int $id = 0): string
    {
        $request = service('request');

        if ($postData = $request->getPost()) {
            // 檢查
            $validation = service('validation');
            $validation->setRules([
                'sys0402' => 'required',
            ], [
                'sys0402' => ['required' => 'Title必填'],
            ]);

            if ($validation->withRequest($request)->run()) {
                $dir = trim($postData['sys0409'] ?? '', '/');
                if ($dir) {
                    $postData['sys0409'] = str_replace('.', '', $dir) . '/';
                } else {
                    $postData['sys0409'] = '';
                }

                if (!array_key_exists('sys0401', $postData)) {
                    // 新增
                    if ($newId = $this->sys04Model->insert($postData)) {
                        return $this->ajaxReturn('OK', ['id' => $newId, 'title' => $postData['sys0402']]);
                    }
                } else {
                    // 修改
                    if ($this->sys04Model->update($postData['sys0401'], $postData)) {
                        return $this->ajaxReturn('OK', ['id' => $id, 'title' => $postData['sys0402']]);
                    }
                }
            }
            return $this->ajaxReturn($this->message->output());
        }

        $cod02Model = model('Cod02Model');
        $this->data['sys0406_opt'] = $cod02Model->getOptionArrayByCode('sys0406');

        if ($id) {
            // 修改
            $this->data['data'] = $this->sys04Model->get($id);
        } else {
            $this->data['data'] = null;
        }
        $this->data['of'] = get_option_module(true);

        return $this->render();
    }

    public function delete(): string
    {
        $request = service('request');
        $id = $request->getPost('id');

        if ($id) {
            if ($this->sys04Model->delete($id)) {
                return $this->ajaxReturn('OK');
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }

        return $this->ajaxReturn('Invalid ID');
    }

    public function order(): void
    {
        $request = service('request');
        $id = $request->getPost('id');
        $direction = $request->getPost('direction');
        $this->sys04Model->reorder($id, $direction);
    }
}
