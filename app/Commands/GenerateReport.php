<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * GenerateReport Command - 自動產生巡檢報表
 */
class GenerateReport extends BaseCommand
{
    protected $group       = 'Patrol';
    protected $name        = 'patrol:generate-report';
    protected $description = '自動產生巡檢報表';
    protected $usage       = 'patrol:generate-report';

    protected int $now;
    protected $fmd01Model;
    protected $fmd02Model;
    protected $fmd20Model;
    protected $fmd21Model;
    protected $isoModel;
    protected $rec01Model;
    protected $ent02Model;
    protected $db;

    public function run(array $params): void
    {
        $this->now = time();
        $this->db = db_connect();
        $this->fmd01Model = model('Fmd01Model');
        $this->fmd02Model = model('Fmd02Model');
        $this->fmd20Model = model('Fmd20Model');
        $this->fmd21Model = model('Fmd21Model');
        $this->isoModel = model('IsoModel');
        $this->rec01Model = model('Rec01Model');
        $this->ent02Model = model('Ent02Model');

        log_message('info', '[' . static::class . '] 開始計算巡檢報表');

        $fmd01s = $this->fmd01Model->getBy(['fmd0108' => '2']);
        if (!$fmd01s) {
            log_message('info', '[' . static::class . '] 無巡檢報表設定');
            log_message('info', '[' . static::class . '] 結束計算巡檢報表');
            CLI::write('無巡檢報表設定', 'yellow');
            return;
        }

        $result = [];
        foreach ($fmd01s as $fmd01) {
            if ($fmd01->fmd0105 < 9) {
                $this->isoModel->setTableName($fmd01);
                $useApproveModule = $this->ent02Model->getLicenseCount('smart_patrol_of1', $fmd01->ent1002);

                $fmd02s = $this->fmd02Model->getByFmd0101($fmd01->fmd0101);
                if ($fmd02s) {
                    $list = $this->getDaypartList($useApproveModule, $fmd01, $this->now, $fmd02s);
                    foreach ($list as $date => $fmd0203s) {
                        $result[] = $fmd01->fmd0104 . ' ' . $this->isoModel->generateReport($fmd01, $date, implode(',', $fmd0203s));
                    }
                } else {
                    $isoDate = $this->isoModel->isoDate($fmd01->fmd0105, date("Y-m-d", $this->now));
                    if (!$useApproveModule || $this->checkApprove($fmd01, $isoDate, '')) {
                        $result[] = $fmd01->fmd0104 . ' ' . $this->isoModel->generateReport($fmd01, $isoDate);
                    }
                }
            }
        }

        // 處理自動加入註記
        $this->processAutoComments();

        log_message('info', '[' . static::class . '] 巡檢報表： ' . json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        log_message('info', '[' . static::class . '] 結束計算巡檢報表');

        CLI::write('巡檢報表產生完成: ' . count($result) . ' 筆', 'green');
    }

    private function processAutoComments(): void
    {
        $data = [];
        $rec01s = $this->rec01Model->getAll();
        foreach ($rec01s as $rec01) {
            $data[$rec01->rec0102][$rec01->rec0104][] = $rec01->rec0106;
        }

        foreach ($data as $fmd0101 => $val) {
            $fmd01 = $this->fmd01Model->getBy(['fmd0108' => 2, 'fmd0101' => $fmd0101], 1);
            if ($fmd01) {
                $fmd02 = $this->fmd02Model->getCurrentDaypart($fmd01->fmd0101);
                $tableName = $this->isoModel->setTableName($fmd01);

                $isoDate = $this->isoModel->isoDate($fmd01->fmd0105, $fmd02->_date ?? date('Y-m-d'));
                $reportMaster = $this->isoModel->getMasterByDate($isoDate);
                if ($reportMaster) {
                    $fmd0201 = $fmd02->fmd0201 ?? 'all';

                    foreach ($val as $fmd0701 => $rec0106s) {
                        $comments = json_decode($reportMaster->comments ?? '{}');
                        if (!isset($comments->{$fmd0201})) {
                            $comments->{$fmd0201} = new \stdClass;
                        }
                        if (!isset($comments->{$fmd0201}->{$fmd0701})) {
                            $comments->{$fmd0201}->{$fmd0701} = [];
                        }
                        foreach ($rec0106s as $comment) {
                            if (!in_array($comment, $comments->{$fmd0201}->{$fmd0701})) {
                                $comments->{$fmd0201}->{$fmd0701}[] = $comment;
                            }
                        }
                        $reportMaster->comments = json_encode($comments);
                    }

                    if (isset($reportMaster->id)) {
                        $this->db->table($tableName)
                            ->where('id', $reportMaster->id)
                            ->update(['comments' => $reportMaster->comments]);
                    }
                }
            }
        }
    }

    private function checkApprove(object $fmd01, string $date, ?string $fmd0203 = null): bool
    {
        $fmd20 = $this->fmd20Model->getByFmd0106($fmd01->fmd0106);
        if ($fmd20) {
            if ($fmd0203) {
                $fmd21 = $this->fmd21Model->getByFmd0203($fmd01->fmd0106, $fmd0203);
                if ($fmd21) {
                    $fmd2101 = $fmd21->fmd2101;
                } else {
                    return true;
                }
            } else {
                $fmd2101 = 0;
            }
            $isoC08 = $this->isoModel->getApproveState($date, $fmd2101);
            if ($isoC08 > 0 && $isoC08 < 101 && !(($fmd01->fmd0105 == 2 || $fmd01->fmd0105 == 3) && $isoC08 == 100)) {
                return false;
            }
        }
        return true;
    }

    private function getDaypartList(bool $useApproveModule, object $fmd01, int $now, array $fmd02s): array
    {
        $result = [];
        $d = date('Y-m-d', $now);

        foreach ($fmd02s as $fmd02) {
            $fmd02->start_time = strtotime($d . ' ' . $fmd02->fmd0205);
            $fmd02->end_time = strtotime($d . ' ' . $fmd02->fmd0206);
            if ($fmd02->start_time > $fmd02->end_time) {
                $fmd02->end_time = strtotime($d . ' ' . $fmd02->fmd0206 . ' +1 day');
            }

            if ($now < $fmd02->start_time) {
                $d = date('Y-m-d', strtotime($d . ' -1 day'));
            }

            if (!$useApproveModule || $this->checkApprove($fmd01, $d, $fmd02->fmd0203)) {
                if (!array_key_exists($d, $result)) {
                    $result[$d] = [];
                }
                $result[$d][] = $fmd02->fmd0203;
            }
        }

        return $result;
    }
}
