<?php

namespace App\Controllers;

/**
 * QueryReport Controller - 查詢巡檢報告
 */
class QueryReport extends AdminController
{
    protected $fmd01Model;
    protected $fmd30Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper(['language', 'date']);
        $this->fmd01Model = model('Fmd01Model');
        $this->fmd30Model = model('Fmd30Model');
    }

    public function index(?string $id = null): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($id) {
            $this->session->setFlashdata('id', $id);
            if ($this->request->getGet('approve') === '') {
                $this->session->setFlashdata('anchor', 'approve');
            }
            return redirect()->to('query-report');
        }

        $this->data['id'] = $this->session->getFlashdata('id');
        if ($this->data['id']) {
            $idParts = explode('-', $this->data['id']);
            $isoModel = model('IsoModel');
            $fmd01 = $this->fmd01Model->find($idParts[0]);
            $this->data['fmd0101'] = $fmd01->fmd0101;
            $isoModel->setTableName($fmd01);
            $this->data['master'] = $isoModel->getMaster($idParts[1]);
        } else {
            $this->data['fmd0101'] = '';
        }

        $this->data['fmd01s'] = $this->fmd01Model->getNameArray(
            'fmd0101',
            'fmd0104',
            2,
            ['filter_dep' => true, 'fmd0108' => 2],
            'fmd0104'
        );
        $this->data['ent10s'] = $this->fmd01Model->createNameArray(
            $this->fmd01Model->getEnt10ByUser(),
            'ent1001',
            'ent1004',
            ['' => lang('QueryReport.search_select_ent1004_default')]
        );

        if ($anchor = $this->session->getFlashdata('anchor')) {
            $this->data['anchor'] = 'scroll_to_anchor("' . $anchor . '");';
        }

        return $this->render();
    }

    public function selectReport(string $fmd0101): \CodeIgniter\HTTP\ResponseInterface
    {
        if ($fmd01 = $this->fmd01Model->find($fmd0101)) {
            return $this->ajaxReturn('OK', $fmd01);
        } else {
            return $this->ajaxReturn('ERR', lang('QueryReport.select_report_err_hint'));
        }
    }

    public function query(?int $page = null): string
    {
        if ($page) {
            $option = $this->loadQueryOption();
        } else {
            $page = 1;
            $option = $this->getQueryOption();
        }

        if (array_key_exists('id', $option)) {
            $idParts = explode('-', $option['id']);
            $fmd0101 = $idParts[0];
            $id = $idParts[1];
        } else {
            $fmd0101 = $option['fmd0101'] ?? null;
            if (empty($fmd0101)) {
                return $this->error(lang('QueryReport.select_form_hint'));
            }
        }

        if ($fmd01 = $this->fmd01Model->find($fmd0101)) {
            $this->data['fmd01'] = $fmd01;
            $pageSize = $this->getPageSize();
            $isoModel = model('IsoModel');
            $isoModel->setTableName($fmd01);

            if (isset($id)) {
                $data = $isoModel->queryMasterBy(['id' => $id]);
                $offset = 0;
                $totalRows = 1;
            } else {
                switch ((int)$fmd01->fmd0105) {
                    case 3:
                        $startDate = $option['start_year'] . '-' . $option['start_month'];
                        $endDate = $option['end_year'] . '-' . $option['end_month'];
                        break;
                    case 4:
                    case 5:
                        $startDate = $option['start_year'] . '-' . $option['start_month'];
                        $endDate = $option['end_year'] . '-' . $option['end_month'] . '#2';
                        break;
                    default:
                        $startDate = $option['start_date'];
                        $endDate = $option['end_date'];
                        break;
                }

                $queryOption = [];
                if ($startDate === $endDate) {
                    $queryOption['date'] = $startDate;
                } else {
                    $queryOption['*start_date'] = "date>='{$startDate}'";
                    $queryOption['*end_date'] = "date<='{$endDate}'";
                }

                $offset = ($page - 1) * $pageSize;
                $data = $isoModel->queryMasterBy($queryOption, $pageSize, $offset, 'date desc');
                $totalRows = $isoModel->countMasterBy($queryOption);
            }

            if (!isset($startDate) || empty($startDate)) {
                $startDate = date('Y-m-d');
                $endDate = date('Y-m-d');
            }

            $sql = "select * from fmd30 where fmd3006 in (2,4) and fmd3002 in ( select fmd0101 from fmd01 where fmd0106 in ( select fmd0106 from fmd01 where fmd0101 = '{$fmd0101}') and fmd0108 in (2,3) ) and ( ( date('{$startDate}') between fmd3003 and fmd3004 or date('{$endDate}') between fmd3003 and fmd3004 ) or ( date('{$startDate}') <= fmd3003 and date('{$endDate}') >= fmd3004) )";
            if ($fmd30s = $this->db->query($sql)->getResult()) {
                foreach ($data as $report) {
                    if ($fmd3005 = $this->inFmd30($report, $fmd30s)) {
                        $report->fmd0104 = '[' . lang('QueryReport.f_fmd3005_' . $fmd3005) . '] ' . $report->fmd0104;
                    }
                }
            }

            if ($data) {
                $this->data['data'] = $data;
                $this->data['offset'] = $offset;
                $this->setPage($totalRows, $page, $pageSize);
                return $this->render();
            } else {
                return $this->error(lang('QueryReport.not_data_hint'));
            }
        }

        return $this->error(lang('QueryReport.select_form_hint'));
    }

    public function detail(string $id): void
    {
        $idParts = explode('-', $id);

        $isoModel = model('IsoModel');
        $report = $isoModel->getReport($idParts[0], $idParts[1]);
        $report = "<div class='scroll'>{$report}</div>";

        $ent02Model = model('Ent02Model');
        if ($ent02Model->getLicenseCount('smart_patrol_of1', $this->session->get('ent0101'))) {
            if ($fmd01 = $this->fmd01Model->find($idParts[0])) {
                $fmd21Model = model('Fmd21Model');
                $this->data['fmd21s'] = $fmd21Model->getByFmd0106($fmd01->fmd0106);
                $this->data['iso_ds'] = $isoModel->getSignInfo($idParts[1]);

                $cod02Model = model('Cod02Model');
                $this->data['approve_state'] = $cod02Model->getOptionArrayByCode('approve_state');
                $report .= view('queryreport/sign_info', $this->data);
            }
        }

        echo $report;
    }

    public function getFmd01(): \CodeIgniter\HTTP\ResponseInterface
    {
        if ($this->request->getPost('close_report') === 'true') {
            $fmd0108 = 4;
        } else {
            $fmd0108 = 2;
        }

        $ent1001 = $this->request->getPost('ent1001');
        if ($ent1001) {
            $fmd01s = $this->fmd01Model->getNameArray(
                'fmd0101',
                'fmd0104',
                ['0' => lang('QueryReport.dropdown_select')],
                ['filter_dep' => true, 'fmd0102' => $ent1001, 'fmd0108' => $fmd0108],
                'fmd0104'
            );
        } else {
            $fmd01s = $this->fmd01Model->getNameArray(
                'fmd0101',
                'fmd0104',
                ['0' => lang('QueryReport.dropdown_select')],
                ['filter_dep' => true, 'fmd0108' => $fmd0108],
                'fmd0104'
            );
        }

        return $this->response->setJSON($fmd01s);
    }

    private function inFmd30(object $report, array $fmd30s): string|false
    {
        foreach ($fmd30s as $fmd30) {
            $fmd3003Real = strtotime($fmd30->fmd3003 . ' 00:00:00');
            $fmd3004Real = strtotime($fmd30->fmd3004 . ' 23:59:59');
            $dateReal = strtotime($report->date . ' 00:00:01');
            if ($fmd3003Real <= $dateReal && $dateReal <= $fmd3004Real) {
                return $fmd30->fmd3005;
            }
        }
        return false;
    }
}
