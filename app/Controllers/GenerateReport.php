<?php

namespace App\Controllers;

/**
 * GenerateReport Controller - 生成巡檢報告
 */
class GenerateReport extends AdminController
{
    protected $fmd01Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper(['language', 'common', 'form', 'assets', 'html']);
        $this->fmd01Model = model('Fmd01Model');
    }

    public function index(?string $id = null): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($id) {
            $this->session->setFlashdata('id', $id);
            if ($this->request->getGet('approve') === '') {
                $this->session->setFlashdata('anchor', 'approve');
            }
            return redirect()->to('generate-report');
        }

        $this->data['id'] = $this->session->getFlashdata('id');
        if ($this->data['id']) {
            $idParts = explode('-', $this->data['id']);
            $isoModel = model('IsoModel');
            $fmd01 = $this->fmd01Model->find($idParts[0]);
            $this->data['fmd0101'] = $fmd01->fmd0101;
            $isoModel->setTableName($fmd01);
            $this->data['master'] = $isoModel->getMaster($idParts[1]);

            $adate = $this->data['master']->date;
            $this->data['year'] = substr($adate, 0, 4);
            $this->data['month'] = substr($adate, 5, 2);
            $this->data['def_select_report'] = $this->loadSelectReport($fmd01->fmd0101);
        } else {
            $this->data['fmd0101'] = '';
        }

        $this->data['ent10s'] = $this->fmd01Model->createNameArray(
            $this->fmd01Model->getEnt10ByUser(),
            'ent1001',
            'ent1004',
            ['' => lang('GenerateReport.search_select_ent1004_default')]
        );

        if ($anchor = $this->session->getFlashdata('anchor')) {
            $this->data['anchor'] = 'scroll_to_anchor("' . $anchor . '");';
        }

        $this->data['fmd01s'] = $this->fmd01Model->getBy(['filter_dep' => true, 'fmd0108' => 2]);

        $cod02Model = model('Cod02Model');
        $this->data['fmd0105_opt'] = $cod02Model->getOptionArrayByCode('fmd0105');

        return $this->render();
    }

    public function loadSelectReport(string $fmd0101): string
    {
        if ($fmd01 = $this->fmd01Model->find($fmd0101)) {
            $this->data['fmd01'] = $fmd01;
            switch ((int)$fmd01->fmd0105) {
                case 3:
                case 4:
                    $this->data['use_datepicker'] = false;
                    break;
                default:
                    $this->data['use_datepicker'] = true;
            }

            $ent02Model = model('Ent02Model');
            if ($ent02Model->getLicenseCount('smart_patrol_of1', $this->session->get('ent0101'))) {
                $fmd21Model = model('Fmd21Model');
                $this->data['fmd21s'] = $fmd21Model->getNameArray('fmd2101', 'fmd2104', 0, ['fmd2102' => $fmd01->fmd0106]);
                if (!$this->data['fmd21s']) {
                    unset($this->data['fmd21s']);
                }
            }

            if (!array_key_exists('fmd21s', $this->data)) {
                $fmd02Model = model('Fmd02Model');
                $fmd02s = $fmd02Model->getNameArray('fmd0203', 'fmd0204', null, ['fmd0202' => $fmd0101], 'fmd0203');
                $this->data['fmd02s'] = $fmd02s;
            }

            return view('generatereport/select_report', $this->data);
        }

        return '';
    }

    public function selectReport(string $fmd0101): string
    {
        if ($fmd01 = $this->fmd01Model->find($fmd0101)) {
            $this->data['fmd01'] = $fmd01;
            switch ((int)$fmd01->fmd0105) {
                case 3:
                case 4:
                    $this->data['use_datepicker'] = false;
                    break;
                default:
                    $this->data['use_datepicker'] = true;
            }

            $ent02Model = model('Ent02Model');
            if ($ent02Model->getLicenseCount('smart_patrol_of1', $this->session->get('ent0101'))) {
                $fmd21Model = model('Fmd21Model');
                $this->data['fmd21s'] = $fmd21Model->getNameArray('fmd2101', 'fmd2104', 0, ['fmd2102' => $fmd01->fmd0106]);
                if (!$this->data['fmd21s']) {
                    unset($this->data['fmd21s']);
                }
            }

            if (!array_key_exists('fmd21s', $this->data)) {
                $fmd02Model = model('Fmd02Model');
                $fmd02s = $fmd02Model->getNameArray('fmd0203', 'fmd0204', null, ['fmd0202' => $fmd0101], 'fmd0203');
                $this->data['fmd02s'] = $fmd02s;
            }

            return $this->render();
        }

        return '';
    }

    public function generateReport(): \CodeIgniter\HTTP\ResponseInterface
    {
        $fmd02s = null;
        $fmd2101 = 0;

        if ($this->request->getPost('fmd02_count')) {
            $fmd0203 = $this->request->getPost('fmd0203');
            if ($fmd0203 === null) {
                return $this->ajaxReturn('err', ['description' => lang('GenerateReport.select_group_hint')]);
            } else {
                $fmd02s = implode(',', $fmd0203);
            }
        } elseif ($this->request->getPost('fmd21_count')) {
            $fmd2101 = $this->request->getPost('fmd2101');
            if ($fmd2101 === null) {
                return $this->ajaxReturn('err', ['description' => lang('GenerateReport.select_group_hint')]);
            } else {
                $fmd21Model = model('Fmd21Model');
                $fmd21 = $fmd21Model->find($fmd2101);
                $fmd02s = $fmd21->fmd2105;
            }
        }

        $fmd0101 = $this->request->getPost('fmd0101');
        if ($fmd01 = $this->fmd01Model->find($fmd0101)) {
            if ($fmd01->fmd0108 != 2) {
                return $this->ajaxReturn('err', ['description' => lang('GenerateReport.not_start_report_hint')]);
            }

            switch ((int)$fmd01->fmd0105) {
                case 2:
                    $date = monday($this->request->getPost('date'));
                    break;
                case 3:
                    $date = $this->request->getPost('year') . '-' . $this->request->getPost('month');
                    break;
                case 4:
                case 5:
                    $date = $this->request->getPost('year') . '-' . $this->request->getPost('month') . '#' . $this->request->getPost('time');
                    break;
                case 1:
                case 9:
                default:
                    $date = $this->request->getPost('date');
                    break;
            }

            $isoModel = model('IsoModel');
            $isoModel->setTableName($fmd01);

            $ent02Model = model('Ent02Model');
            $fmd20 = null;
            if ($ent02Model->getLicenseCount('smart_patrol_of1', $this->session->get('ent0101'))) {
                if (!isset($fmd2101)) {
                    $fmd2101 = 0;
                }
                $fmd20Model = model('Fmd20Model');
                if ($fmd20 = $fmd20Model->getByFmd0106($fmd01->fmd0106)) {
                    $isoC08 = $isoModel->getApproveState($date, $fmd2101);
                    if ($isoC08 > 0 && $isoC08 < 101 && !(($fmd01->fmd0105 == 2 || $fmd01->fmd0105 == 3) && $isoC08 == 100)) {
                        if ($isoC08 == 100) {
                            $message = lang('GenerateReport.singed_hint1');
                        } else {
                            $message = lang('GenerateReport.singed_hint2');
                        }
                        return $this->ajaxReturn('err', ['description' => $message]);
                    }
                }
            }

            if (isset($fmd02s)) {
                $reportId = $isoModel->generateReport($fmd01, $date, $fmd02s);
            } else {
                $reportId = $isoModel->generateReport($fmd01, $date);
            }

            if ($reportId) {
                $report = $isoModel->getReport($fmd01->fmd0101, $reportId);

                $output = "<div class=\"box box-primary\"><div class=\"box-header with-border\">";
                $output .= "<h3 class=\"box-title\">{$date} ({$fmd01->fmd0103}) {$fmd01->fmd0104}</h3></div>";
                $output .= "<div class=\"box-body scroll\">{$report}</div></div>";

                if (isset($fmd20)) {
                    if ($ent02Model->getLicenseCount('smart_patrol_of1', $this->session->get('ent0101'))) {
                        $fmd21Model = model('Fmd21Model');
                        $apfData1 = [
                            'fmd0101' => $fmd01->fmd0101,
                            'report_id' => $reportId,
                            'fmd2101' => $fmd2101,
                            'fmd21s' => $fmd21Model->getByFmd0106($fmd01->fmd0106),
                            'iso_ds' => $isoModel->getSignInfo($reportId),
                        ];

                        $cod02Model = model('Cod02Model');
                        $apfData1['approve_state'] = $cod02Model->getOptionArrayByCode('approve_state');
                        $output .= view('queryreport/sign_info', $apfData1);
                    }

                    if ($fmd20->fmd2003 == 1) {
                        $approveForm = view('layout/error', ['message' => lang('GenerateReport.singed_hint3')]);
                    } else {
                        $apfData = [
                            'fmd0101' => $fmd01->fmd0101,
                            'report_id' => $reportId,
                            'fmd2101' => $fmd2101,
                            'current_user' => $this->data['current_user'],
                        ];
                        $approveForm = view('generatereport/approve_form', $apfData);
                    }
                    $output .= $approveForm;
                }

                return $this->ajaxReturn('OK', ['report' => $output]);
            } else {
                return $this->ajaxReturn('err', ['description' => $this->message->output()]);
            }
        }

        return $this->ajaxReturn('err', ['description' => lang('GenerateReport.form_not_found')]);
    }

    public function sendReport(): \CodeIgniter\HTTP\ResponseInterface
    {
        if ($fmd0101 = $this->request->getPost('fmd0101')) {
            if ($fmd01 = $this->fmd01Model->find($fmd0101)) {
                $isoModel = model('IsoModel');
                $isoModel->setTableName($fmd01);

                $reportId = $this->request->getPost('report_id');
                $fmd2101 = $this->request->getPost('fmd2101');
                $memo = $this->request->getPost('memo');

                if ($isoModel->sendReport($reportId, $fmd2101, $memo, $fmd01)) {
                    return $this->ajaxReturn('OK');
                }
            }
        }

        return $this->ajaxReturn('ERR', ['description' => $this->message->output()]);
    }

    public function detail(string $id): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->ajaxReturn('OK');
    }
}
