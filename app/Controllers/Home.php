<?php

namespace App\Controllers;

/**
 * Home Controller - 首頁儀表板
 */
class Home extends AdminController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper(['language', 'date']);
    }

    public function index(): string
    {
        $ent02Model = model('Ent02Model');

        $this->getMissCount();

        $entId = (int) $this->session->get('ent0101');

        if ($ent02Model->getLicenseCount('smart_patrol_of1', $entId)) {
            $this->data['approval_count'] = $this->getApprovalCount();
            $this->data['rejected_list'] = $this->getRejectedList();
        }

        if ($ent02Model->getLicenseCount('smart_patrol_of2', $entId)) {
            $this->getRepairList();
        }

        // Custom for specific enterprise
        if ($this->session->get('ent0102') === 'hpw') {
            $this->data['hpw_report1_count'] = $this->getHpwReport1Count();
            $this->data['hpw_report2_count'] = $this->getHpwReport2Count();
        }

        // Chart permission
        $this->data['chartpermission'] = config('App')->showChart ?? false;

        return $this->render();
    }

    private function getApprovalCount(): int
    {
        $isoModel = model('IsoModel');
        $fmd01Model = model('Fmd01Model');
        $fmd22Model = model('Fmd22Model');

        $result = 0;

        $fmd22s = $fmd22Model->getUserApproveFlow();
        if ($fmd22s) {
            foreach ($fmd22s as $fmd22) {
                $fmd01 = $fmd01Model->getActiveByFmd0106($fmd22->fmd2202);
                if ($fmd01) {
                    $isoModel->setTableName($fmd01);
                    $data = $isoModel->getWaitingForApprove($fmd22->fmd2203, $fmd22->fmd2204);
                    if ($data) {
                        $result += count($data);
                    }
                }
            }
        }

        return $result;
    }

    private function getRejectedList(): array
    {
        $isoModel = model('IsoModel');
        $fmd01Model = model('Fmd01Model');
        $fmd21Model = model('Fmd21Model');

        $result = [];
        $this->data['fmd21s'] = [];

        $fmd01s = $fmd01Model->getBy(['filter_dep' => true, 'fmd0108' => 2]);
        if ($fmd01s) {
            foreach ($fmd01s as $fmd01) {
                $isoModel->setTableName($fmd01);
                $data = $isoModel->getRejectedList();
                if ($data) {
                    $fmd01->data = $data;
                    $result[] = $fmd01;

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

        return $result;
    }

    private function getRepairList(): void
    {
        $repair = [];

        $ent10Model = model('Ent10Model');
        $ent10Id = $ent10Model->getCurrentUserSeeDepartmentId();

        $pad03Model = model('Pad03Model');
        $pad05Model = model('Pad05Model');

        // Unprocessed records
        $option = ['pad0306' => 0, '*sys0110' => "sys0110 in ({$ent10Id})"];
        $rows = $pad03Model->countBy($option);
        $pad03s = $pad03Model->getBy($option, 1, 0, 'pad03z2 asc');
        $repair['pad030'] = ['rows' => $rows, 'time' => $rows && $pad03s ? $pad03s->pad03z2 : ''];

        // Delegated repair records
        $option = ['pad0513' => 1, '*pad0510' => "pad0510 in ({$ent10Id})"];
        $rows = $pad05Model->countBy($option);
        $pad05s = $pad05Model->getBy($option, 1, 0, 'pad0509 asc');
        $repair['pad051'] = ['rows' => $rows, 'time' => $rows && $pad05s ? $pad05s->pad0509 : ''];

        // Acceptance records
        $option = ['pad0513' => 3, '*pad0507' => "pad0507 in ({$ent10Id})"];
        $rows = $pad05Model->countBy($option);
        $pad05s = $pad05Model->getBy($option, 1, 0, 'pad05z4 asc');
        $repair['pad053'] = ['rows' => $rows, 'time' => $rows && $pad05s ? $pad05s->pad05z4 : ''];

        // Case closed records
        $option = ['pad0513' => 4, '*pad0510' => "pad0510 in ({$ent10Id})"];
        $rows = $pad05Model->countBy($option);
        $pad05s = $pad05Model->getBy($option, 1, 0, 'pad05z4 asc');
        $repair['pad054'] = ['rows' => $rows, 'time' => $rows && $pad05s ? $pad05s->pad05z4 : ''];

        $this->data['repair'] = $repair;
    }

    private function getHpwReport1Count(): string
    {
        $fmd01 = $this->db->table('fmd01')->where('fmd0103', 'SCB-003')->get()->getRow();

        if ($fmd01) {
            $isoModel = model('IsoModel');
            $isoModel->setTableName($fmd01);

            $day = date('Y-m-d', strtotime('-1 day'));
            $value = 0;
            $count = 0;

            $data = $isoModel->queryDetailBy(['date' => $day]);
            if ($data) {
                foreach ($data as $d) {
                    if ($d->item2_name === '商家开门率') {
                        $value += (float)$d->data1_1;
                        $count++;
                    }
                }

                if ($count > 0) {
                    $value = round($value / $count, 2);
                    return "{$value}％";
                }
            }
        }

        return '查无资料！';
    }

    private function getHpwReport2Count(): float
    {
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $today = date('Y-m-d');

        $sql = "select count(*) as count from pad01 join fmd07 on fmd0701=pad0102 join fmd01 on fmd0101=fmd0702
                where fmd0102=14 and fmd0106<>64 and pad0109>=? and pad0109<?";

        $data = $this->db->query($sql, [$yesterday, $today])->getRow();

        return $data ? (float)$data->count : 0;
    }

    private function getMissCount(): void
    {
        $fmd01Model = model('Fmd01Model');
        $fmd02Model = model('Fmd02Model');
        $isoModel = model('IsoModel');

        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        $this->data['fa_toggle_1'] = 'fa-toggle-on';
        $this->data['fa_toggle_2'] = 'fa-toggle-off';
        $this->data['fa_toggle_3'] = 'fa-toggle-off';
        $this->data['btnToggleString_1'] = lang('Home.current_miss_error_count');
        $this->data['btnToggleString_2'] = $today . ' 漏檢與異常';
        $this->data['btnToggleString_3'] = $yesterday . ' 漏檢與異常';

        $onduty = true;
        $date = $today;

        $getData = $this->request->getGet();
        if (!empty($getData['toggle_date'])) {
            if ($getData['toggle_date'] === $today && isset($getData['onduty'])) {
                $this->data['fa_toggle_1'] = 'fa-toggle-on';
                $this->data['fa_toggle_2'] = 'fa-toggle-off';
                $this->data['fa_toggle_3'] = 'fa-toggle-off';
                $date = $getData['toggle_date'];
                $onduty = true;
            } elseif ($getData['toggle_date'] === $today) {
                $this->data['fa_toggle_1'] = 'fa-toggle-off';
                $this->data['fa_toggle_2'] = 'fa-toggle-on';
                $this->data['fa_toggle_3'] = 'fa-toggle-off';
                $date = $getData['toggle_date'];
                $onduty = false;
            } elseif ($getData['toggle_date'] === $yesterday) {
                $this->data['fa_toggle_1'] = 'fa-toggle-off';
                $this->data['fa_toggle_2'] = 'fa-toggle-off';
                $this->data['fa_toggle_3'] = 'fa-toggle-on';
                $date = $getData['toggle_date'];
                $onduty = false;
            }
        }

        $ent10s = $fmd01Model->getEnt10ByUser();
        if ($ent10s) {
            foreach ($ent10s as $key => $ent10) {
                $ent10->fmd01s = $fmd01Model->getBy(['filter_dep' => true, 'fmd0108' => '2', 'fmd0102' => $ent10->ent1001], 0, 0, 'fmd0103');

                foreach ($ent10->fmd01s as $fkey => $fmd01) {
                    $isoModel->setTableName($fmd01);
                    $fmd01->fmd02 = $fmd02Model->getCurrentDaypart($fmd01->fmd0101);

                    if ($fmd01->fmd02) {
                        if ($onduty && empty($fmd01->fmd02->fmd0201)) {
                            unset($ent10->fmd01s[$fkey]);
                            continue;
                        }
                    }

                    $_date = $onduty && isset($fmd01->fmd02->_date) ? $fmd01->fmd02->_date : date('Y-m-d', strtotime($date));
                    $isoDate = $isoModel->isoDate($fmd01->fmd0105, $_date);
                    $fmd01->date = $isoDate;
                    $fmd01->iso = $isoModel->queryMasterBy(['date' => $isoDate], 1, 0, 'date');

                    // Calculate error and miss rates
                    if (is_object($fmd01->iso)) {
                        $fmd01->iso->error_count = 0;
                        $fmd01->iso->miss_count = 0;
                        $fmd01->iso->check_count = 0;
                        $fmd01->error_rate = 0;
                        $fmd01->miss_rate = 0;
                    }
                }
            }
        }

        $this->data['miss'] = $ent10s;

        $cod02Model = model('Cod02Model');
        $this->data['fmd0105_opt'] = $cod02Model->getOptionArrayByCode('fmd0105');
        $this->data['today'] = $today;
        $this->data['yesterday'] = $yesterday;
    }

    public function axGetDepartment(): \CodeIgniter\HTTP\ResponseInterface
    {
        $departments = $this->getLabels();
        return $this->response->setJSON($departments);
    }

    /**
     * Get department labels from fmd40 table
     */
    private function getLabels(): array
    {
        $labels = [];
        $rs = $this->db->query('select fmd4002, fmd4003 from fmd40 order by fmd4004')->getResult();
        foreach ($rs as $row) {
            $labels[] = $row->fmd4003 ?? $row->fmd4002;
        }
        return $labels;
    }

    private function getArray(string $sql, int $y, int $m): array
    {
        $st = ($y + 1911) . '-01';
        $et = ($y + 1911) . '-' . str_pad($m, 2, '0', STR_PAD_LEFT) . '-32';

        $arr = [];
        $rs = $this->db->query('select * from fmd40 order by fmd4004')->getResult();
        foreach ($rs as $row) {
            $arr[$row->fmd4002] = 0;
        }

        $rs = $this->db->query($sql, [$st, $et])->getResult();
        foreach ($rs as $r) {
            $arr[$r->fmd4002] = $r->v;
        }

        return array_values($arr);
    }

    public function axGetEUI(int $y, int $m): \CodeIgniter\HTTP\ResponseInterface
    {
        $sql = "select fmd4002,sum(fmd4104) as v from fmd40 left join fmd41 on fmd40.fmd4002=fmd41.fmd4102 where fmd4103>=? and fmd4103<? group by fmd4002 order by fmd4004";
        $mPad = str_pad($m, 2, '0', STR_PAD_LEFT);

        $o = (object)[
            'label' => ($y - 1) . '/' . $mPad,
            'backgroundColor' => '#66c5dc',
            'data' => $this->getArray($sql, $y - 1, $m),
        ];

        $n = (object)[
            'label' => $y . '/' . $mPad,
            'backgroundColor' => '#f5a209',
            'data' => $this->getArray($sql, $y, $m),
        ];

        return $this->response->setJSON([
            'labels' => $this->getLabels(),
            'datasets' => [$o, $n]
        ]);
    }

    public function axGetERR(int $y, int $m): \CodeIgniter\HTTP\ResponseInterface
    {
        $sql = "select fmd4202 as fmd4002,100-round(sum(fmd4205)/sum(fmd4204)*100,2) as v from fmd42 where fmd4203>=? and fmd4203<? group by fmd4202";
        $mPad = str_pad($m, 2, '0', STR_PAD_LEFT);

        $o = (object)[
            'label' => ($y - 1) . '/' . $mPad,
            'backgroundColor' => '#998ec3',
            'data' => $this->getArray($sql, $y - 1, $m),
        ];

        $n = (object)[
            'label' => $y . '/' . $mPad,
            'backgroundColor' => '#b35806',
            'data' => $this->getArray($sql, $y, $m),
        ];

        return $this->response->setJSON([
            'labels' => $this->getLabels(),
            'datasets' => [$o, $n]
        ]);
    }

    public function axGetMISS(int $y, int $m): \CodeIgniter\HTTP\ResponseInterface
    {
        $sql = "select fmd4202 as fmd4002,100-round(sum(fmd4206)/sum(fmd4204)*100,2) as v from fmd42 where fmd4203>=? and fmd4203<? group by fmd4202";
        $mPad = str_pad($m, 2, '0', STR_PAD_LEFT);

        $o = (object)[
            'label' => ($y - 1) . '/' . $mPad,
            'backgroundColor' => '#d8b365',
            'data' => $this->getArray($sql, $y - 1, $m),
        ];

        $n = (object)[
            'label' => $y . '/' . $mPad,
            'backgroundColor' => '#5ab4ac',
            'data' => $this->getArray($sql, $y, $m),
        ];

        return $this->response->setJSON([
            'labels' => $this->getLabels(),
            'datasets' => [$o, $n]
        ]);
    }
}
