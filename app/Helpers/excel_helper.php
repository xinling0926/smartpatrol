<?php

/**
 * Excel Helper - CI4 Version
 * Excel 匯出輔助函數 (使用 PhpSpreadsheet)
 */

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

if (!function_exists('getCellID')) {
    /**
     * 取得儲存格ID
     */
    function getCellID(int $col, int $row): string
    {
        return getColumnID($col) . strval($row);
    }
}

if (!function_exists('getColumnID')) {
    /**
     * 取得欄位ID (A, B, C, ... AA, AB, ...)
     */
    function getColumnID(int $col): string
    {
        $s = '';
        if ($col > 26) {
            $n = floor(($col - 1) / 26);
            $col = $col - ($n * 26);
            $s = chr(64 + $n);
        }
        $s .= chr(64 + $col);
        return $s;
    }
}

if (!function_exists('download_excel')) {
    /**
     * 下載Excel檔案
     *
     * @param string $fileName 檔案名稱
     * @param array $data 資料陣列
     * @param array|null $option 選項
     */
    function download_excel(string $fileName, array $data, ?array $option = null): void
    {
        $reportName = $option['report_name'] ?? '';
        $stringField = $option['string_field'] ?? '';
        $showSn = $option['show_sn'] ?? false;

        // 處理欄位名稱
        if (isset($option['field_name'])) {
            $fieldName = is_array($option['field_name'])
                ? $option['field_name']
                : explode(';', $option['field_name']);
        } else {
            $fieldName = [];
            if (!empty($data) && is_object($data[0])) {
                foreach ($data[0] as $property => $value) {
                    $fieldName[] = $property;
                }
            }
        }

        // 處理標題
        if (isset($option['title']) && $option['title']) {
            $title = is_array($option['title'])
                ? $option['title']
                : explode(';', $option['title']);
        } else {
            $title = $fieldName;
        }

        // 處理欄寬
        $fieldWidth = null;
        if (isset($option['field_width'])) {
            $fieldWidth = is_array($option['field_width'])
                ? $option['field_width']
                : explode(';', $option['field_width']);
        }

        // 處理合計欄位
        $sumField = isset($option['sum_field']) ? explode(';', $option['sum_field']) : [];

        ini_set('memory_limit', '1024M');

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Smart Patrol')
            ->setTitle($reportName);

        $sheet = $spreadsheet->getActiveSheet();

        $row = 1;
        $lastCol = count($fieldName);
        if ($showSn) {
            $lastCol++;
        }

        // 報表名稱
        if ($reportName) {
            $sheet->setCellValue(getCellID(1, $row), $reportName);
            $sheet->getStyle(getCellID(1, $row))->getFont()->setSize(16)->setBold(true);
            $sheet->mergeCells(getCellID(1, $row) . ':' . getCellID($lastCol, $row));
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        // 描述
        if (isset($option['description'])) {
            $sheet->setCellValue(getCellID(1, $row), $option['description']);
            if (!isset($option['print_date'])) {
                $row++;
            }
        }

        // 列印日期
        if (isset($option['print_date'])) {
            $printDateCell = getCellID(1, $row);
            $sheet->setCellValue($printDateCell, '报表日期 : ' . $option['print_date']);
            $sheet->mergeCells(getCellID(1, $row) . ':' . getCellID($lastCol, $row));
            $sheet->getStyle($printDateCell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $row++;
        }

        // 標題列
        if (!empty($title)) {
            $col = 1;
            if ($showSn) {
                $sheet->setCellValue(getCellID($col++, $row), '项次');
            }
            if (is_array($title[0] ?? null)) {
                foreach ($title as $t) {
                    for ($i = 0; $i < count($t);) {
                        $colNow = $col;
                        $sheet->setCellValue(getCellID($col++, $row), $t[$i++]);
                        while ($i < count($t) && $t[$i] === '+=') {
                            $i++;
                            $col++;
                        }
                        if ($col - $colNow > 1) {
                            $sheet->mergeCells(getCellID($colNow, $row) . ':' . getCellID($col - 1, $row));
                        }
                    }
                    $row++;
                    $col = $showSn ? 2 : 1;
                }
            } else {
                for ($i = 0; $i < count($title);) {
                    $colNow = $col;
                    $sheet->setCellValue(getCellID($col++, $row), $title[$i++]);
                    while ($i < count($title) && $title[$i] === '+=') {
                        $i++;
                        $col++;
                    }
                    if ($col - $colNow > 1) {
                        $sheet->mergeCells(getCellID($colNow, $row) . ':' . getCellID($col - 1, $row));
                    }
                }
                $row++;
            }
        }

        // 準備合計陣列
        $sumArray = [];
        if (!empty($sumField)) {
            foreach ($sumField as $f) {
                $sumArray[$f] = 0;
            }
        }

        // 資料列
        $i = 0;
        foreach ($data as $r) {
            $col = 1;
            $i++;
            if ($showSn) {
                $sheet->setCellValue(getCellID($col, $row), $i);
                $col++;
            }
            foreach ($fieldName as $field) {
                $value = is_object($r) ? ($r->$field ?? '') : ($r[$field] ?? '');
                if (strpos($stringField, $field) === false) {
                    $sheet->setCellValue(getCellID($col, $row), $value);
                } else {
                    $sheet->setCellValueExplicit(getCellID($col, $row), $value);
                }
                if (!empty($sumField) && array_key_exists($field, $sumArray)) {
                    $sumArray[$field] += (float)$value;
                }
                $col++;
            }
            $row++;
        }

        // 新增小計
        if (!empty($sumField) && !empty($data)) {
            $sheet->setCellValue(getCellID(1, $row), '小计');
            $col = $showSn ? 2 : 1;
            $firstRow = $data[0];
            foreach ($firstRow as $property => $value) {
                if (array_key_exists($property, $sumArray)) {
                    $sheet->setCellValue(getCellID($col, $row), $sumArray[$property]);
                }
                $col++;
            }
            $row++;
        }

        // 設定欄寬
        if (is_array($fieldWidth)) {
            $col = 1;
            if ($showSn) {
                $sheet->getColumnDimension(getColumnID($col))->setWidth(40);
                $col++;
            }
            foreach ($fieldWidth as $width) {
                $sheet->getColumnDimension(getColumnID($col))->setWidth((int)$width);
                $col++;
            }
        } else {
            for ($i = 1; $i <= $lastCol; $i++) {
                $sheet->getColumnDimension(getColumnID($i))->setAutoSize(true);
            }
        }

        // 取得Excel版本設定
        $setting = service('setting');
        $version = $setting->item('export_excel_version') ?: 'Excel2007';

        // 輸出
        if ($version === 'Excel2007') {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . rawurlencode($fileName) . '.xlsx"');
            $writer = new Xlsx($spreadsheet);
        } else {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . rawurlencode($fileName) . '.xls"');
            $writer = new Xls($spreadsheet);
        }
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
