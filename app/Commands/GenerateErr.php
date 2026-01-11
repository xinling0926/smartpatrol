<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * GenerateErr Command - 異常統計資料產生
 */
class GenerateErr extends BaseCommand
{
    protected $group       = 'Patrol';
    protected $name        = 'patrol:generate-err';
    protected $description = '產生異常統計資料';
    protected $usage       = 'patrol:generate-err';

    protected $db;

    public function run(array $params): void
    {
        $this->db = db_connect();

        CLI::write('開始產生異常統計資料...', 'yellow');

        // 新站
        $this->deptData(5);
        // 台中
        $this->deptData(8);
        // 高雄
        $this->deptData(12);

        CLI::write('異常統計資料產生完成', 'green');
    }

    protected function deptData(int $deptNo): void
    {
        $sql = "SELECT DISTINCT fmd0102, fmd0106 FROM fmd01 WHERE fmd0102=? AND fmd0105=1";
        $rsDept = $this->db->query($sql, [$deptNo])->getResult();

        $ls = [];
        foreach ($rsDept as $dept) {
            $ls = $this->runIso($ls, 'iso' . str_pad($dept->fmd0106, 2, '0', STR_PAD_LEFT));
        }

        foreach ($ls as $row) {
            $this->db->table('fmd42')
                ->where('fmd4202', $deptNo)
                ->where('fmd4203', $row->date)
                ->delete();

            $this->db->table('fmd42')->insert([
                'fmd4202' => $deptNo,
                'fmd4203' => $row->date,
                'fmd4204' => $row->total,
                'fmd4205' => $row->err,
                'fmd4206' => $row->miss,
            ]);
        }

        CLI::write("部門 {$deptNo} 處理完成: " . count($ls) . ' 筆', 'green');
    }

    protected function runIso(array $ls, string $tb): array
    {
        $sql = "SELECT * FROM {$tb} ORDER BY date";
        $rs = $this->db->query($sql)->getResult();

        foreach ($rs as $row) {
            if (!isset($ls[$row->date])) {
                $obj = new \stdClass();
                $obj->date = $row->date;
                $obj->total = 0;
                $obj->err = 0;
                $obj->miss = 0;
                $ls[$row->date] = $obj;
            }

            for ($i = 1; $i < 10; $i++) {
                $checkName = "check{$i}";
                if (isset($row->{$checkName})) {
                    $ls[$row->date]->total += intval($row->{$checkName});
                }

                $errorName = "error{$i}";
                if (isset($row->{$errorName})) {
                    $ls[$row->date]->err += intval($row->{$errorName});
                }

                $missName = "miss{$i}";
                if (isset($row->{$missName})) {
                    $ls[$row->date]->miss += intval($row->{$missName});
                }
            }
        }

        return $ls;
    }
}
