<?php

namespace App\Controllers;

/**
 * EmmaLink Controller - EMMA整合
 */
class EmmaLink extends BaseController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper('language');
    }

    public function sendNotify(int $entId): void
    {
        if (!$this->setting->byEnterprise('enable_emma_link', $entId)) {
            $message = "The enterprise disable EMMA link. [ent0101={$entId}]";
            log_message('debug', $message);
            echo $message;
            return;
        }

        $emma = new \App\Libraries\Emma();
        $emma->set_server_url($this->setting->byEnterprise('emma_server_url', $entId));
        $emma->serviceName = $this->setting->byEnterprise('emma_service_name', $entId);
        $emma->machineHostname = $this->setting->byEnterprise('emma_machine_hostname', $entId);

        $minute = date("Hi");
        while ($minute . '59' > date("His")) {
            $this->sendRepairNotify($entId, $emma);
            $this->sendApproveNotify($entId, $emma);
            sleep(3);
        }
    }

    private function sendRepairNotify(int $entId, $emma): void
    {
        if (!$lastRepairId = $this->setting->byEnterprise('emma_repair_notify_id', $entId)) {
            $lastRepairId = 0;
        }

        $pad03s = $this->db->table('pad03')
            ->select('pad0301,pad03z2,ent1001,sys0103,sys0104')
            ->join('sys01', 'sys0101=pad03z1')
            ->join('ent10', 'sys0110=ent1001')
            ->where('pad0301 >', $lastRepairId)
            ->where('ent1002', $entId)
            ->orderBy('pad0301')
            ->get()
            ->getResult();

        if ($pad03s) {
            $maxId = 0;
            foreach ($pad03s as $pad03) {
                if ($pad03->pad0301 > $maxId) {
                    $maxId = $pad03->pad0301;
                }
            }

            if ($maxId > $lastRepairId) {
                $this->setting->updateEnterprise('emma_repair_notify_id', $maxId, $entId);
            }

            $n = 1234811111;
            foreach ($pad03s as $pad03) {
                $r = rand(1, 9);
                $id = $n * $r + $pad03->pad0301;
                $message = "{$pad03->sys0103}{$pad03->sys0104} " . lang('EmmaLink.in') . " {$pad03->pad03z2} " . lang('EmmaLink.message_hint') . base_url('emma-link/repair/' . $id);

                $sys01s = $this->db->table('sys01')
                    ->select('sys0107')
                    ->where('sys0119', lang('EmmaLink.sys0119_title'))
                    ->where('sys0110', $pad03->ent1001)
                    ->groupBy('sys0107')
                    ->get()
                    ->getResult();

                if ($sys01s) {
                    foreach ($sys01s as $sys01) {
                        echo $message;
                        echo $emma->send_message($sys01->sys0107, $message);
                        echo PHP_EOL;
                    }
                }
            }
        }
    }

    private function sendApproveNotify(int $entId, $emma): void
    {
        $fmd01s = $this->db->table('fmd01')
            ->select('fmd0101,fmd0102,fmd0104,fmd0106')
            ->join('ent10', 'ent1001=fmd0102')
            ->where('ent1002', $entId)
            ->where('fmd0108', 2)
            ->get()
            ->getResult();

        if ($fmd01s) {
            foreach ($fmd01s as $fmd01) {
                $tableName = 'iso' . (($fmd01->fmd0106 < 10) ? '0' . $fmd01->fmd0106 : $fmd01->fmd0106);

                $isos = $this->db->table($tableName . 'c')
                    ->select('c01,date,fmd2206,fmd2205')
                    ->join($tableName, 'id=c02')
                    ->join('fmd22', 'fmd2202=' . $fmd01->fmd0106 . ' and fmd2203=c04 and fmd2204=c08')
                    ->where('c08 <', 100)
                    ->where('(c11 is null or (c10 is not null and c10>c11))')
                    ->get()
                    ->getResult();

                if ($isos) {
                    foreach ($isos as $iso) {
                        $this->db->table($tableName . 'c')
                            ->where('c01', $iso->c01)
                            ->update(['c11' => date('Y-m-d H:i:s')]);

                        $fmd2205 = explode(';', $iso->fmd2205);
                        $sys0110 = $iso->fmd2206 ?: $fmd01->fmd0102;

                        $sys01s = $this->db->table('sys01')
                            ->select('sys0107')
                            ->where('sys0110', $sys0110)
                            ->whereIn('sys0119', $fmd2205)
                            ->groupBy('sys0107')
                            ->get()
                            ->getResult();

                        if ($sys01s) {
                            foreach ($sys01s as $sys01) {
                                $message = "{$iso->date} {$fmd01->fmd0104} " . lang('EmmaLink.sign_hint');
                                echo $message;
                                echo $emma->send_message($sys01->sys0107, $message);
                                echo PHP_EOL;
                            }
                        }
                    }
                }
            }
        }
    }

    public function repair(int $id): string
    {
        $n = 1234811111;
        $id = $id % $n;

        $pad03Model = model('Pad03Model');
        $pad03 = $pad03Model->get($id);
        if ($pad03) {
            $pad03->pad04s = $pad03Model->getPad04s($id);
        }

        $this->data['data'] = $pad03;
        $this->data['title'] = lang('EmmaLink.table_title');
        $this->data['site_title'] = $this->setting->item('site_title');

        return view('emmalink/repair', $this->data);
    }
}
