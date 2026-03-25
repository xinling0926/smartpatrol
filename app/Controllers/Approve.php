<?php

namespace App\Controllers;

/**
 * Approve Controller - 簽核管理
 */
class Approve extends AdminController
{
    protected $fmd01Model;
    protected $fmd20Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper('language');
        $this->fmd01Model = model('Fmd01Model');
        $this->fmd20Model = model('Fmd20Model');
    }

    public function index(): string
    {
        $isoModel = model('IsoModel');
        $fmd21Model = model('Fmd21Model');
        $fmd22Model = model('Fmd22Model');

        $this->data['data'] = [];
        $this->data['fmd21s'] = [];

        $fmd22s = $fmd22Model->getUserApproveFlow();
        if ($fmd22s) {
            foreach ($fmd22s as $fmd22) {
                $fmd01 = $this->fmd01Model->getActiveByFmd0106($fmd22->fmd2202);
                if ($fmd01) {
                    $isoModel->setTableName($fmd01);
                    $data = $isoModel->getWaitingForApprove($fmd22->fmd2203, $fmd22->fmd2204);
                    if ($data) {
                        $fmd01->subdata = $data;
                        $this->data['data'][] = $fmd01;

                        foreach ($data as $item) {
                            if (!empty($item->c04) && !isset($this->data['fmd21s'][$item->c04])) {
                                $fmd21 = $fmd21Model->find($item->c04);
                                if ($fmd21) {
                                    $this->data['fmd21s'][$fmd21->fmd2101] = $fmd21->fmd2104;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $this->render();
    }

    public function approveForm(int $fmd0101, int $isoC01): \CodeIgniter\HTTP\ResponseInterface
    {
        $fmd01 = $this->fmd01Model->find($fmd0101);
        if (!$fmd01) {
            return $this->ajaxReturn('ERR', ['description' => 'Form not found']);
        }

        $isoModel = model('IsoModel');
        $isoModel->setTableName($fmd01);

        $isoC = $isoModel->getIsoC($isoC01);
        $iso = $isoModel->getMaster($isoC->c02);
        $report = $isoModel->getReport($fmd01->fmd0101, $isoC->c02);

        $output = "<div class=\"box box-primary\"><div class=\"box-header with-border\">";
        $output .= "<h3 class=\"box-title\">{$iso->date} ({$fmd01->fmd0103}) {$fmd01->fmd0104}</h3>";
        $output .= "</div><div class=\"box-body scroll\">{$report}</div></div>";

        $doApproval = false;
        if ($isoC->c08 < 100) {
            $fmd22Model = model('Fmd22Model');
            $doApproval = $fmd22Model->checkUserApproveFlow($fmd01->fmd0106, $isoC->c04, $isoC->c08);
        }

        if ($doApproval) {
            $output .= '<div class="row"><div class="col-lg-6">';
        }

        $fmd21Model = model('Fmd21Model');
        $this->data['fmd21s'] = $fmd21Model->getByFmd0106($fmd01->fmd0106);
        $this->data['iso_ds'] = $isoModel->getSignInfo($iso->id);

        $cod02Model = model('Cod02Model');
        $this->data['approve_state'] = $cod02Model->getOptionArrayByCode('approve_state');

        $output .= view('approve/sign_info', $this->data);

        if ($doApproval) {
            $output .= '</div><div class="col-lg-6">';
            $apfData = ['fmd0101' => $fmd01->fmd0101, 'id' => $isoC->c01, 'current_user' => $this->data['current_user']];
            $output .= view('approve/approve_form', $apfData);
            $output .= '</div></div>';
        }

        return $this->ajaxReturn('OK', ['report' => $output]);
    }

    public function doApprove(): \CodeIgniter\HTTP\ResponseInterface
    {
        $fmd0101 = $this->request->getPost('fmd0101');
        $fmd01 = $this->fmd01Model->find($fmd0101);

        if (!$fmd01) {
            return $this->ajaxReturn('ERR', ['description' => 'Form not found']);
        }

        $isoModel = model('IsoModel');
        $isoModel->setTableName($fmd01);

        $id = $this->request->getPost('id');
        $result = $this->request->getPost('result');
        $memo = $this->request->getPost('memo');

        if ($isoModel->approveReport($fmd01, $id, $result, $memo)) {
            $fmd21Model = model('Fmd21Model');
            $this->data['fmd21s'] = $fmd21Model->getByFmd0106($fmd01->fmd0106);

            $isoC = $isoModel->getIsoC($id);
            $this->data['iso_ds'] = $isoModel->getSignInfo($isoC->c02);

            $cod02Model = model('Cod02Model');
            $this->data['approve_state'] = $cod02Model->getOptionArrayByCode('approve_state');

            $signInfo = view('approve/sign_info', $this->data);
            return $this->ajaxReturn('OK', ['sign_info' => $signInfo]);
        } else {
            return $this->ajaxReturn('ERR', ['description' => $this->message->output()]);
        }
    }
}
