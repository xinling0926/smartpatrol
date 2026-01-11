<?php

namespace App\Controllers;

/**
 * OperationLog Controller - 操作日誌
 */
class OperationLog extends AdminController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper('language');
    }

    public function index(): string
    {
        $this->data['log0302s'] = date('Y-m-d');
        $this->data['log0302e'] = date('Y-m-d');

        $sys01Model = model('Sys01Model');
        $this->data['sys01s'] = $sys01Model->getNameArray('sys0101', 'sys0103,sys0104', 1);

        $this->clearQueryOption();
        return $this->render();
    }

    public function query(?int $page = null): string
    {
        if ($page === null) {
            $option = $this->getQueryOption();
            $page = 1;
        } else {
            $option = $this->loadQueryOption();
        }

        $pageSize = $this->getPageSize();
        $offset = ($page - 1) * $pageSize;
        $this->data['offset'] = $offset;

        $log03Model = model('Log03Model');
        $totalRows = $log03Model->countBy($option);
        $this->data['data'] = $log03Model->getBy($option, $pageSize, $offset);
        $this->setPage($totalRows, $page, $pageSize);

        return $this->render('');
    }
}
