<?php

namespace App\Controllers\Hpw;

use App\Controllers\AdminController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use stdClass;

/**
 * 加工廠巡檢統計表
 *
 * 輸入日期起訖，查詢加工廠所有巡檢報表的上傳電子表單數量
 */
class HpwReport2 extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(?int $id = null): string
    {
        return $this->render();
    }

    public function report(): string
    {
        $option = $this->getQueryOption();
        $startDate = $option['start_date'];
        $endDate = $option['end_date'];

        $title = [];
        for ($s = $startDate; $s <= $endDate; $s = tomorrow($s)) {
            $title[] = date('m.d', strtotime($s));
        }

        $this->data['start_date'] = $startDate;
        $this->data['title'] = $title;
        $this->data['detail'] = $this->calculateDetail($startDate, $endDate);

        if ($this->data['detail']) {
            $this->data['summery'] = $this->calculateSummery($this->data['detail'], count($title));
            return $this->render();
        } else {
            return $this->error(lang('Hpw/hpw_report2.not_data'));
        }
    }

    private function calculateSummery(array $data, int $fieldCount): array
    {
        $title = [];
        $value = [];

        foreach ($data as $d) {
            $title[] = $d->name;
            $n = 0;
            for ($i = 0; $i < $fieldCount; $i++) {
                $fn = 'data' . ($i + 1);
                if (isset($d->$fn)) {
                    $n += $d->$fn;
                }
            }
            $value[] = $n;
        }

        return ['title' => $title, 'value' => $value];
    }

    private function calculateDetail(string $startDate, string $endDate): array
    {
        $db = db_connect();
        $result = [];
        $i = 1;

        for ($s = $startDate; $s <= $endDate; $s = tomorrow($s)) {
            $e = tomorrow($s);
            $sql = "SELECT fmd0104, COUNT(*) as count FROM pad01
                    JOIN fmd07 ON fmd0701=pad0102
                    JOIN fmd01 ON fmd0101=fmd0702
                    WHERE fmd0102=14 AND fmd0106<>64 AND pad0109>=? AND pad0109<?
                    GROUP BY fmd0104";

            $data = $db->query($sql, [$s, $e])->getResult();
            $fn = 'data' . $i;

            foreach ($data as $d) {
                if (array_key_exists($d->fmd0104, $result)) {
                    $item = $result[$d->fmd0104];
                } else {
                    $item = new stdClass();
                    $item->name = $d->fmd0104;
                    $result[$d->fmd0104] = $item;
                }
                $item->$fn = (float)$d->count;
            }
            $i++;
        }

        ksort($result);
        return $result;
    }

    public function detail(string $id): string
    {
        $id = urldecode($id);
        $parts = explode('|', $id);
        $reportName = $parts[0];
        $e = date('Y-m-d', strtotime("{$parts[1]} +{$parts[2]} days"));

        $db = db_connect();
        $fmd01 = $db->table('fmd01')
            ->where('fmd0104', $reportName)
            ->where('fmd0108', 2)
            ->get()
            ->getRow();

        $s = yesterday($e);
        $sql = "SELECT fmd0703, pad0107 FROM pad01
                JOIN fmd07 ON fmd0701=pad0102
                JOIN fmd01 ON fmd0101=fmd0702
                WHERE fmd0106=? AND pad0109>=? AND pad0109<?
                ORDER BY pad0109";

        $this->data['pad01s'] = $db->query($sql, [$fmd01->fmd0106, $s, $e])->getResult();

        foreach ($this->data['pad01s'] as $pad01) {
            $pad01->pad0107 = json_decode($pad01->pad0107, true);
        }

        return $this->render();
    }

    public function exportExcel(): void
    {
        helper('excel');

        $option = $this->loadQueryOption();
        $startDate = $option['start_date'];
        $endDate = tomorrow($option['end_date']);

        $db = db_connect();
        $sql = "SELECT fmd0104, pad0107, pad0109 FROM pad01
                JOIN fmd07 ON fmd0701=pad0102
                JOIN fmd01 ON fmd0101=fmd0702
                WHERE fmd0102=14 AND fmd0106<>64 AND pad0109>=? AND pad0109<?
                ORDER BY fmd0104, pad0109";

        $data = $db->query($sql, [$startDate, $endDate])->getResult();

        $reportData = [];
        foreach ($data as $d) {
            if (array_key_exists($d->fmd0104, $reportData)) {
                $fmd01 = $reportData[$d->fmd0104];
            } else {
                $fmd01 = new stdClass();
                $fmd01->fmd0104 = $d->fmd0104;
                $reportData[$d->fmd0104] = $fmd01;
            }

            $rawData = json_decode($d->pad0107, true);
            if (!isset($fmd01->title)) {
                $fmd01->title = [];
                foreach ($rawData as $val) {
                    $fmd01->title[] = $val['prompt'];
                }
            }
            $value = [$d->pad0109];
            foreach ($rawData as $val) {
                $value[] = array_keys($val['value'][0])[0];
            }
            $fmd01->data[] = $value;
        }

        ini_set('memory_limit', '1024M');

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Smart Patrol')
            ->setTitle(lang('Hpw/hpw_report2.highcharts_title'));

        // Remove default sheet
        $spreadsheet->removeSheetByIndex(0);

        foreach ($reportData as $item) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle(mb_substr($item->fmd0104, 0, 31)); // Excel sheet name max 31 chars

            $row = 1;
            $col = 1;
            $sheet->setCellValue(getCellID($col++, $row), lang('Hpw/hpw_report2.excel_times'));
            $sheet->setCellValue(getCellID($col++, $row), lang('Hpw/hpw_report2.excel_patrol_date'));

            for ($i = 0; $i < count($item->title); $i++) {
                $sheet->setCellValue(getCellID($col++, $row), $item->title[$i]);
            }
            $row++;

            $index = 0;
            foreach ($item->data as $r) {
                $col = 1;
                $index++;
                $sheet->setCellValue(getCellID($col++, $row), $index);
                foreach ($r as $v) {
                    $sheet->setCellValue(getCellID($col++, $row), $v);
                }
                $row++;
            }

            // 設定欄寬
            $colCount = count($item->title) + 2;
            $sheet->getColumnDimension(getColumnID(1))->setWidth(40 / 6);
            $sheet->getColumnDimension(getColumnID(2))->setWidth(100 / 6);
            for ($i = 3; $i <= $colCount; $i++) {
                $sheet->getColumnDimension(getColumnID($i))->setWidth(70 / 6);
            }
        }

        $fileName = date('Ymd_His');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . rawurlencode($fileName) . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
