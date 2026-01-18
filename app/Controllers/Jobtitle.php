<?php

namespace App\Controllers;

/**
 * Jobtitle Controller - 職稱管理
 */
class Jobtitle extends AdminController
{
    protected $ent20Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper('language');
        $this->ent20Model = model('Ent20Model');
    }

    public function index(): string
    {
        $this->clearQueryOption();

        $pageSize = $this->getPageSize();
        $totalRows = $this->ent20Model->countBy(null);
        $this->data['data'] = $this->ent20Model->getBy(null, $pageSize);
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
        $totalRows = $this->ent20Model->countBy($option);
        $this->data['data'] = $this->ent20Model->getBy($option, $pageSize, $offset);
        $this->setPage($totalRows, $page, $pageSize);

        return $this->render('');
    }

    public function detail(int $id): string
    {
        $this->data['data'] = $this->ent20Model->find($id);
        return $this->render();
    }

    public function edit(int $id = 0): string|\CodeIgniter\HTTP\ResponseInterface
    {
        if ($this->request->is('post')) {
            $postData = $this->request->getPost();

            $rules = [
                'ent2003' => 'required',
                'ent2004' => 'required',
            ];

            $postData['ent2002'] = $this->session->get('ent0101');

            if ($this->validate($rules)) {
                if (empty($postData['ent2001'])) {
                    // Insert
                    if ($newId = $this->ent20Model->insert($postData)) {
                        return $this->ajaxReturn('OK', ['id' => $newId]);
                    }
                } else {
                    // Update
                    if ($this->ent20Model->save($postData)) {
                        return $this->ajaxReturn('OK', ['id' => $id]);
                    }
                }
            }

            return $this->ajaxReturn($this->message->output());
        }

        // GET request
        if ($id) {
            $this->data['data'] = $this->ent20Model->find($id);
        } else {
            $this->data['data'] = null;
        }

        return $this->render();
    }

    public function delete(): \CodeIgniter\HTTP\ResponseInterface
    {
        $id = $this->request->getPost('id');
        if ($id) {
            if ($this->ent20Model->delete($id)) {
                return $this->ajaxReturn('OK');
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('No ID provided');
    }
}
