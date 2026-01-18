<?php

namespace App\Controllers;

/**
 * Tag Controller - 標籤管理
 */
class Tag extends AdminController
{
    protected $tag01Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->tag01Model = model('Tag01Model');
    }

    public function index(): string
    {
        $option = ['tag0102' => $this->session->get('ent0101')];

        $pageSize = $this->getPageSize();
        $totalRows = $this->tag01Model->countBy($option);
        $this->data['data'] = $this->tag01Model->getBy($option, $pageSize);
        $this->data['offset'] = 0;
        $this->setPage($totalRows, 1, $pageSize);

        $cod02Model = model('Cod02Model');
        $this->data['tag0106_opt'] = $cod02Model->getOptionArrayByCode('tag0106', ['' => '状态']);

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

        $option['tag0102'] = $this->session->get('ent0101');

        $pageSize = $this->getPageSize();
        $offset = ($page - 1) * $pageSize;
        $this->data['offset'] = $offset;
        $totalRows = $this->tag01Model->countBy($option);
        $this->data['data'] = $this->tag01Model->getBy($option, $pageSize, $offset);
        $this->setPage($totalRows, $page, $pageSize);

        return $this->render('');
    }

    public function detail(int $id): string
    {
        $cod02Model = model('Cod02Model');
        $this->data['tag0106_opt'] = $cod02Model->getOptionArrayByCode('tag0106');
        $this->data['data'] = $this->tag01Model->find($id);

        return $this->render();
    }

    public function edit(int $id = 0): string|\CodeIgniter\HTTP\ResponseInterface
    {
        if ($this->request->is('post')) {
            $postData = $this->request->getPost();

            $rules = [
                'tag0104' => 'required',
            ];

            if ($this->validate($rules)) {
                if (empty($postData['tag0101'])) {
                    // Insert
                    $postData['tag0102'] = $this->session->get('ent0101');

                    if ($newId = $this->tag01Model->insert($postData)) {
                        $tag01 = $this->tag01Model->find($newId);
                        helper('common');
                        pushToDevice(['tag01' => [$tag01], 'description' => '']);

                        return $this->ajaxReturn('OK', ['id' => $newId, 'title' => $postData['tag0104']]);
                    }
                } else {
                    // Update
                    if ($this->tag01Model->save($postData)) {
                        $tag01 = $this->tag01Model->find($id);
                        helper('common');
                        pushToDevice(['tag01' => [$tag01], 'description' => '']);

                        return $this->ajaxReturn('OK', ['id' => $id, 'title' => $postData['tag0104']]);
                    }
                }
            }

            return $this->ajaxReturn($this->message->output());
        }

        // GET request
        $cod02Model = model('Cod02Model');
        $this->data['tag0106_opt'] = $cod02Model->getOptionArrayByCode('tag0106');
        $this->data['data'] = $id ? $this->tag01Model->find($id) : null;

        return $this->render();
    }

    public function delete(): \CodeIgniter\HTTP\ResponseInterface
    {
        $id = $this->request->getPost('id');
        if ($id) {
            if ($this->tag01Model->delete($id)) {
                return $this->ajaxReturn('OK');
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('No ID provided');
    }
}
