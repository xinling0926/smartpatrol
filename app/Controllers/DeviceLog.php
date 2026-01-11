<?php

namespace App\Controllers;

/**
 * DeviceLog Controller - 設備日誌
 */
class DeviceLog extends AdminController
{
    protected $dev01Model;
    protected $dev02Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper('language');
        $this->dev01Model = model('Dev01Model');
        $this->dev02Model = model('Dev02Model');
    }

    public function index(): string
    {
        $this->data['dev0203s'] = date('Y-m-d');
        $this->data['dev0203e'] = date('Y-m-d');
        $this->data['dev0206k'] = '';

        $this->data['dev01s'] = $this->dev01Model->getNameArray(
            'dev0101',
            'dev0104',
            1,
            ['dev0102' => $this->session->get('ent0101')]
        );

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

        foreach ($option as $k1 => $v1) {
            if ($v1 === '') {
                unset($option[$k1]);
            }
        }

        $option['dev0102'] = $this->session->get('ent0101');
        $pageSize = $this->getPageSize();
        $offset = ($page - 1) * $pageSize;
        $this->data['offset'] = $offset;
        $totalRows = $this->dev02Model->countBy($option);
        $this->data['data'] = $this->dev02Model->getBy($option, $pageSize, $offset, 'dev0207 desc');
        $this->setPage($totalRows, $page, $pageSize);

        return $this->render('');
    }
}
