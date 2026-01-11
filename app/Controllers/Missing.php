<?php

namespace App\Controllers;

/**
 * Missing Controller - 漏檢與異常統計表
 */
class Missing extends AdminController
{
    protected $fmd01Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper('language');
        $this->fmd01Model = model('Fmd01Model');
    }

    public function index(): string
    {
        $this->data['fmd01s'] = $this->fmd01Model->getNameArray(
            'fmd0101',
            'fmd0104',
            ['' => lang('Globe.dropdown_select'), 'ALL' => lang('Globe.all')],
            ['filter_dep' => true, 'fmd0108' => 2]
        );

        return $this->render();
    }

    public function selectReport(string $fmd0101): \CodeIgniter\HTTP\ResponseInterface
    {
        if ($fmd0101 === 'ALL') {
            return $this->ajaxReturn('OK', ['fmd0105' => '1']);
        }

        $fmd01 = $this->fmd01Model->find($fmd0101);
        if ($fmd01) {
            return $this->ajaxReturn('OK', ['fmd0105' => $fmd01->fmd0105]);
        }

        return $this->ajaxReturn('ERR', lang('Missing.not_table_data_hint'));
    }

    public function query(?int $page = null): string
    {
        $option = $this->getQueryOption();

        $fmd0101 = $option['fmd0101'] ?? null;
        if (!$fmd0101) {
            return $this->error(lang('Missing.select_form_hint'));
        }

        if ($fmd0101 === 'ALL') {
            $fmd01s = $this->fmd01Model->getBy(['filter_dep' => true, 'fmd0108' => 2]);
        } else {
            $fmd01s = [];
            $fmd01 = $this->fmd01Model->find($fmd0101);
            if ($fmd01) {
                $fmd01s[] = $fmd01;
            }
        }

        $isoModel = model('IsoModel');
        $fmd02Model = model('Fmd02Model');

        $data = [];
        foreach ($fmd01s as $fmd01) {
            switch ((int)$fmd01->fmd0105) {
                case 3:
                    if ($fmd0101 === 'ALL') {
                        $startDate = date('Y-m', strtotime($option['start_date']));
                        $endDate = date('Y-m', strtotime($option['end_date']));
                    } else {
                        $startDate = $option['start_year'] . '-' . $option['start_month'];
                        $endDate = $option['end_year'] . '-' . $option['end_month'];
                    }
                    break;
                case 4:
                    if ($fmd0101 === 'ALL') {
                        $startDate = date('Y-m', strtotime($option['start_date']));
                        $endDate = date('Y-m', strtotime($option['end_date'])) . '#2';
                    } else {
                        $startDate = $option['start_year'] . '-' . $option['start_month'];
                        $endDate = $option['end_year'] . '-' . $option['end_month'] . '#2';
                    }
                    break;
                default:
                    $startDate = $option['start_date'];
                    $endDate = $option['end_date'];
                    break;
            }

            $opt = [];
            if ($startDate === $endDate) {
                $opt['date'] = $startDate;
            } else {
                $opt['*start_date'] = "date>='{$startDate}'";
                $opt['*end_date'] = "date<='{$endDate}'";
            }

            $isoModel->setTableName($fmd01);

            $miss = new \stdClass();
            $miss->iso = $isoModel->queryMasterBy($opt, 0, 0, 'date');
            $miss->fmd01 = $fmd01;
            $miss->fmd02s = $fmd02Model->getByFmd0101($fmd01->fmd0101);
            $data[] = $miss;
        }

        if (count($data) === 1 && empty($data[0]->iso)) {
            return $this->error(lang('Missing.not_data_hint'));
        }

        $this->data['data'] = $data;
        return $this->render();
    }
}
