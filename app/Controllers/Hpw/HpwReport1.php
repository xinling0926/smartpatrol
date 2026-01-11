<?php

namespace App\Controllers\Hpw;

use App\Controllers\AdminController;
use stdClass;

/**
 * 市场部开门率统计表
 *
 * 輸入日期起訖（默認昨天開始算往前五天），查詢巡檢報表(報表編號SCB-01)資料的開門數量
 * 圖表部分以路線為單位加總，數據名稱刪除路線名稱裡的"日常"與"巡檢路線"
 */
class HpwReport1 extends AdminController
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

        $db = db_connect();
        $fmd01 = $db->table('fmd01')
            ->where('fmd0103', 'SCB-01')
            ->get()
            ->getRow();

        if ($fmd01) {
            $title = [];
            for ($s = $startDate; $s <= $endDate; $s = tomorrow($s)) {
                $title[] = date('m.d', strtotime($s));
            }

            $isoModel = model('IsoModel');
            $isoModel->setTableName($fmd01);

            $this->data['title'] = $title;
            $detail = $this->calculateDetail($startDate, $endDate, $isoModel, $db);

            if ($detail) {
                $this->data['detail'] = $detail;
                $this->data['summery'] = $this->calculateSummery($detail, count($title));
                return $this->render();
            } else {
                return $this->error(lang('Hpw/hpw_report1.not_report_data'));
            }
        } else {
            return $this->error(lang('Hpw/hpw_report1.not_report_set_data'));
        }
    }

    private function calculateSummery(array $data, int $fieldCount): array
    {
        $result = [];

        foreach ($data as $d) {
            if (array_key_exists($d->route, $result)) {
                $item = $result[$d->route];
            } else {
                $item = new stdClass();
                $item->name = $d->route;
                $item->data = array_fill(0, $fieldCount, '0');
                $item->count = 0;
                $result[$d->route] = $item;
            }
            $item->count++;
            for ($i = 0; $i < $fieldCount; $i++) {
                $fn = 'data' . ($i + 1);
                if (isset($d->$fn)) {
                    $item->data[$i] += $d->$fn;
                }
            }
        }

        ksort($result);
        $output = [];
        $avg = new stdClass();
        $avg->name = lang('Hpw/hpw_report1.average');
        $avg->data = array_fill(0, $fieldCount, '0');

        foreach ($result as $d) {
            for ($i = 0; $i < $fieldCount; $i++) {
                $d->data[$i] = round($d->data[$i] / $d->count, 2);
                $avg->data[$i] += $d->data[$i];
            }
            $output[] = $d;
        }

        if ($output) {
            for ($i = 0; $i < $fieldCount; $i++) {
                $avg->data[$i] = round($avg->data[$i] / count($output), 2);
            }
            $output[] = $avg;
        }

        return $output;
    }

    private function calculateDetail(string $startDate, string $endDate, $isoModel, $db): array
    {
        $result = [];
        $i = 1;

        for ($s = $startDate; $s <= $endDate; $s = tomorrow($s)) {
            $reportDetail = $isoModel->queryDetailBy(['date' => $s]);
            $fn = 'data' . $i;

            foreach ($reportDetail as $d) {
                if ($d->src1_1) {
                    if (array_key_exists($d->item1_name, $result)) {
                        $item = $result[$d->item1_name];
                    } else {
                        $item = new stdClass();
                        $item->name = $d->item1_name;
                        $result[$d->item1_name] = $item;

                        // 查詢路線名稱
                        if ($d->src1_1) {
                            $fmd08 = $db->table('pad01')
                                ->select('fmd0804')
                                ->join('fmd09', 'fmd0901=pad0106')
                                ->join('fmd08', 'fmd0801=fmd0903')
                                ->where('pad0101', $d->src1_1)
                                ->get()
                                ->getRow();

                            if ($fmd08) {
                                $item->route = str_replace(
                                    [lang('Hpw/hpw_report1.str_1'), lang('Hpw/hpw_report1.str_2')],
                                    ['', ''],
                                    $fmd08->fmd0804
                                );
                            }
                        }
                    }
                    $item->$fn = (float)$d->data1_1;
                }
            }
            $i++;
        }

        ksort($result);
        return $result;
    }
}
