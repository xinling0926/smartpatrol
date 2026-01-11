<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * GenerateEui Command - EUI 統計資料產生
 */
class GenerateEui extends BaseCommand
{
    protected $group       = 'Patrol';
    protected $name        = 'patrol:generate-eui';
    protected $description = '產生 EUI 統計資料';
    protected $usage       = 'patrol:generate-eui';

    protected $db;

    public function run(array $params): void
    {
        $this->db = db_connect();

        CLI::write('開始產生 EUI 統計資料...', 'yellow');

        // 新站
        $ls = [];
        $ls = $this->runIso($ls, 'iso05', 'A');
        $ls = $this->runIso($ls, 'iso05', 'B');
        $ls = $this->runIso($ls, 'iso05', 'C');
        $this->saveData($ls, 5, 111337);

        // 台中
        $ls = [];
        $ls = $this->runIso($ls, 'iso57', 'A');
        $ls = $this->runIso($ls, 'iso57', 'B');
        $ls = $this->runIso($ls, 'iso57', 'C');
        $this->saveData($ls, 8, 125987);

        // 高雄
        $ls = [];
        $ls = $this->runIso($ls, 'iso109', 'A');
        $ls = $this->runIso($ls, 'iso109', 'B');
        $ls = $this->runIso($ls, 'iso109', 'C');
        $this->saveData($ls, 12, 77818);

        CLI::write('EUI 統計資料產生完成', 'green');
    }

    protected function saveData(array $ls, int $deptNo, int $floorArea): void
    {
        foreach ($ls as $row) {
            $total = ($row->A ?? 0) + ($row->B ?? 0) + ($row->C ?? 0);
            $v = $total / $floorArea;

            $this->db->table('fmd41')
                ->where('fmd4102', $deptNo)
                ->where('fmd4103', $row->date)
                ->delete();

            $this->db->table('fmd41')->insert([
                'fmd4102' => $deptNo,
                'fmd4103' => $row->date,
                'fmd4104' => $v,
            ]);
        }
    }

    protected function runIso(array $ls, string $tb, string $mof): array
    {
        $sql = "SELECT {$tb}.date, substr(item2_name,1,4), item2_name, data1_1
                FROM {$tb}a
                INNER JOIN {$tb} ON {$tb}.id={$tb}a.master_id
                WHERE item2_name LIKE 'MOF{$mof}%' AND item3_name LIKE '%總用電指數'
                ORDER BY date";

        $rs = $this->db->query($sql)->getResult();

        for ($i = 1; $i < count($rs); $i++) {
            $row = $rs[$i];

            // 取出前一天的數值
            $pi = $i - 1;
            $pv = 0;
            while ($pi >= 0) {
                if (floatval($rs[$pi]->data1_1) >= 0) {
                    $pv = floatval($rs[$pi]->data1_1);
                    break;
                }
                $pi--;
            }

            // 要比前一天大
            if (($row->data1_1 > $pv) && $pv > 0) {
                $v = round($row->data1_1 - $pv, 1);
                $si = mb_strpos($row->item2_name, '倍數：');
                $x = intval(mb_substr($row->item2_name, $si + 3, 4));

                if (!isset($ls[$row->date])) {
                    $obj = new \stdClass();
                    $obj->date = $row->date;
                    $ls[$row->date] = $obj;
                }
                $ls[$row->date]->{$mof} = round($v * $x, 2);
            }
        }

        return $ls;
    }
}
