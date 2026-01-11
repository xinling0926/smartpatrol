<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * QueryReportItem Controller - 巡檢項目查詢
 */
class QueryReportItem extends AdminController
{
    protected $fmd01Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper(['language', 'excel']);
        $this->fmd01Model = model('Fmd01Model');
    }

    public function index(): string
    {
        $this->data['fmd01s'] = $this->fmd01Model->getNameArray(
            'fmd0101',
            'fmd0104',
            2,
            ['filter_dep' => true, '*state' => 'fmd0108 in (2,4)'],
            'fmd0104'
        );

        return $this->render();
    }

    public function selectReport(string $fmd0101): \CodeIgniter\HTTP\ResponseInterface
    {
        if ($fmd01 = $this->fmd01Model->find($fmd0101)) {
            $fmd03Model = model('Fmd03Model');
            $this->data['fmd03s'] = $fmd03Model->getByFmd0101($fmd0101);
            $optionView = view('queryreportitem/option', $this->data);
            return $this->ajaxReturn('OK', ['fmd01' => $fmd01, 'option' => $optionView]);
        } else {
            return $this->ajaxReturn('ERR', lang('QueryReportItem.not_table_data_hint'));
        }
    }

    public function query(?int $page = null): string
    {
        if ($page) {
            $option = $this->loadQueryOption();
        } else {
            $page = 1;
            $option = $this->getQueryOption();
        }

        $fmd0101 = $option['fmd0101'] ?? null;
        if (!$fmd0101) {
            return $this->error(lang('QueryReportItem.select_form_hint'));
        }

        if ($fmd01 = $this->fmd01Model->find($fmd0101)) {
            $pageSize = $this->getPageSize();

            switch ((int)$fmd01->fmd0105) {
                case 3:
                    $startDate = $option['start_year'] . '-' . $option['start_month'];
                    $endDate = $option['end_year'] . '-' . $option['end_month'];
                    break;
                case 4:
                case 5:
                    $startDate = $option['start_year'] . '-' . $option['start_month'];
                    $endDate = $option['end_year'] . '-' . $option['end_month'] . '#2';
                    break;
                default:
                    $startDate = $option['start_date'];
                    $endDate = $option['end_date'];
                    break;
            }

            $opt = [];
            if ($startDate === $endDate) {
                $opt['date'] = $startDate;
            } else {
                $opt['*start_date'] = "date>='{$startDate}'";
                $opt['*end_date'] = "date<='{$endDate}'";
            }

            switch ($option['state'] ?? '') {
                case '1':
                    $opt['*state'] = 'detail.error_count=0 and detail.miss_count=0';
                    break;
                case '2':
                    $opt['*state'] = 'detail.error_count>0';
                    break;
                case '3':
                    $opt['*state'] = 'detail.miss_count>0';
                    break;
            }

            $fmd03Model = model('Fmd03Model');
            $fmd03s = $fmd03Model->getByFmd0101($fmd0101);
            foreach ($fmd03s as $fmd03) {
                $fn = 'item' . $fmd03->fmd0303 . '_name';
                if (isset($option[$fn]) && $option[$fn]) {
                    $opt['*' . $fn] = "{$fn} like '{$option[$fn]}%'";
                }
            }

            $offset = ($page - 1) * $pageSize;
            $isoModel = model('IsoModel');
            $isoModel->setTableName($fmd01);
            $this->data['data'] = $isoModel->queryDetailBy($opt, $pageSize, $offset, 'date');
            $totalRows = $isoModel->countDetailBy($opt);

            if ($this->data['data']) {
                $this->data['fmd03s'] = $fmd03s;

                $fmd02Model = model('Fmd02Model');
                $this->data['fmd02s'] = $fmd02Model->getByFmd0101($fmd0101);

                $fmd05Model = model('Fmd05Model');
                $this->data['fmd05s'] = $fmd05Model->getByFmd0101($fmd0101);

                $this->data['offset'] = $offset;
                $this->setPage($totalRows, $page, $pageSize);
                $this->data['show_export'] = true;

                return $this->render();
            } else {
                return $this->error(lang('QueryReportItem.not_data_hint'));
            }
        }

        return $this->error(lang('QueryReportItem.select_form_hint'));
    }

    public function downloadExcel(): void
    {
        $option = $this->loadQueryOption();

        $fmd0101 = $option['fmd0101'] ?? null;
        if (!$fmd0101) {
            echo lang('QueryReportItem.select_form_hint');
            return;
        }

        if ($fmd01 = $this->fmd01Model->find($fmd0101)) {
            switch ((int)$fmd01->fmd0105) {
                case 3:
                    $startDate = $option['start_year'] . '-' . $option['start_month'];
                    $endDate = $option['end_year'] . '-' . $option['end_month'];
                    break;
                case 4:
                case 5:
                    $startDate = $option['start_year'] . '-' . $option['start_month'];
                    $endDate = $option['end_year'] . '-' . $option['end_month'] . '#2';
                    break;
                default:
                    $startDate = $option['start_date'];
                    $endDate = $option['end_date'];
                    break;
            }

            $opt = [];
            if ($startDate === $endDate) {
                $opt['date'] = $startDate;
            } else {
                $opt['*start_date'] = "date>='{$startDate}'";
                $opt['*end_date'] = "date<='{$endDate}'";
            }

            switch ($option['state'] ?? '') {
                case '1':
                    $opt['*state'] = 'detail.error_count=0 and detail.miss_count=0';
                    break;
                case '2':
                    $opt['*state'] = 'detail.error_count>0';
                    break;
                case '3':
                    $opt['*state'] = 'detail.miss_count>0';
                    break;
            }

            $fmd03Model = model('Fmd03Model');
            $fmd03s = $fmd03Model->getByFmd0101($fmd0101);
            foreach ($fmd03s as $fmd03) {
                $fn = 'item' . $fmd03->fmd0303 . '_name';
                if (isset($option[$fn]) && $option[$fn]) {
                    $opt['*' . $fn] = "{$fn} like '{$option[$fn]}%'";
                }
            }

            $isoModel = model('IsoModel');
            $isoModel->setTableName($fmd01);
            $totalRows = $isoModel->countDetailBy($opt);

            if ($totalRows > 60000) {
                echo lang('QueryReportItem.export_pass_count_hint');
                return;
            }

            $data = $isoModel->queryDetailBy($opt, 0, 0, 'date');

            $fmd02Model = model('Fmd02Model');
            $fmd02s = $fmd02Model->getByFmd0101($fmd0101);

            $fmd05Model = model('Fmd05Model');
            $fmd05s = $fmd05Model->getByFmd0101($fmd0101);

            ini_set('memory_limit', '2048M');
            ini_set('max_execution_time', 600);

            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()
                ->setCreator('Smart Patrol')
                ->setTitle(lang('QueryReportItem.excel_title'));

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle(lang('QueryReportItem.excel_title'));

            $row = 1;
            $col = 1;

            // Header
            if ($fmd02s) {
                $fieldCount = count($fmd05s);
                $sheet->mergeCells(getCellID($col, $row) . ':' . getCellID($col, $row + 1));
                $sheet->setCellValue(getCellID($col++, $row), lang('QueryReportItem.f_common_no'));
                $sheet->mergeCells(getCellID($col, $row) . ':' . getCellID($col, $row + 1));
                $sheet->setCellValue(getCellID($col++, $row), lang('QueryReportItem.f_date'));

                foreach ($fmd03s as $fmd03) {
                    $sheet->mergeCells(getCellID($col, $row) . ':' . getCellID($col, $row + 1));
                    $sheet->setCellValue(getCellID($col++, $row), $fmd03->fmd0304);
                }

                if ($fieldCount == 1) {
                    $sheet->mergeCells(getCellID($col, $row) . ':' . getCellID($col + count($fmd02s) - 1, $row));
                    foreach ($fmd05s as $d) {
                        $sheet->setCellValue(getCellID($col, $row), $d->fmd0504);
                    }
                    $row++;
                    foreach ($fmd02s as $fmd02) {
                        $sheet->setCellValue(getCellID($col++, $row), $fmd02->fmd0204);
                    }
                } else {
                    foreach ($fmd02s as $fmd02) {
                        $sheet->mergeCells(getCellID($col, $row) . ':' . getCellID($col + count($fmd05s) - 1, $row));
                        $sheet->setCellValue(getCellID($col, $row), $fmd02->fmd0204);
                        $col += count($fmd05s);
                    }
                    $row++;
                    $col = count($fmd03s) + 3;
                    foreach ($fmd02s as $fmd02) {
                        foreach ($fmd05s as $d) {
                            $sheet->setCellValue(getCellID($col++, $row), $d->fmd0504);
                        }
                    }
                }
            } else {
                $sheet->setCellValue(getCellID($col++, $row), lang('QueryReportItem.f_common_no'));
                $sheet->setCellValue(getCellID($col++, $row), lang('QueryReportItem.f_date'));
                foreach ($fmd03s as $d) {
                    $sheet->setCellValue(getCellID($col++, $row), $d->fmd0304);
                }
                foreach ($fmd05s as $d) {
                    $sheet->setCellValue(getCellID($col++, $row), $d->fmd0504);
                }
            }

            $row++;
            $colCount = $col - 1;

            // Data
            $i = 0;
            foreach ($data as $d) {
                $col = 1;
                $i++;
                $sheet->setCellValue(getCellID($col++, $row), $i);
                $sheet->setCellValue(getCellID($col++, $row), $d->date);

                foreach ($fmd03s as $fmd03) {
                    $fn = "item{$fmd03->fmd0303}_name";
                    $sheet->setCellValue(getCellID($col++, $row), $d->$fn);
                }

                if ($fmd02s) {
                    foreach ($fmd02s as $fmd02) {
                        foreach ($fmd05s as $fmd05) {
                            $fnData = 'data' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503;
                            $fnErr = 'err' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503;

                            if (($d->$fnErr ?? 0) == 1) {
                                $color = 'FFdd4b39';
                            } elseif (($d->$fnErr ?? 0) == 2) {
                                $color = 'FFd2d6de';
                            } else {
                                $color = '';
                            }

                            if ($color) {
                                $sheet->getStyle(getCellID($col, $row))->getFill()->setFillType(Fill::FILL_SOLID);
                                $sheet->getStyle(getCellID($col, $row))->getFill()->getStartColor()->setARGB($color);
                            }
                            $sheet->setCellValue(getCellID($col++, $row), $d->$fnData ?? '');
                        }
                    }
                } else {
                    foreach ($fmd05s as $fmd05) {
                        $fnData = 'data1_' . $fmd05->fmd0503;
                        $fnErr = 'err1_' . $fmd05->fmd0503;

                        if (($d->$fnErr ?? 0) == 1) {
                            $color = 'FFdd4b39';
                        } elseif (($d->$fnErr ?? 0) == 2) {
                            $color = 'FFd2d6de';
                        } else {
                            $color = '';
                        }

                        if ($color) {
                            $sheet->getStyle(getCellID($col, $row))->getFill()->setFillType(Fill::FILL_SOLID);
                            $sheet->getStyle(getCellID($col, $row))->getFill()->getStartColor()->setARGB($color);
                        }
                        $sheet->setCellValue(getCellID($col++, $row), $d->$fnData ?? '');
                    }
                }
                $row++;
            }

            // Column width
            $sheet->getColumnDimension(getColumnID(1))->setWidth(40 / 6);
            $sheet->getColumnDimension(getColumnID(2))->setWidth(100 / 6);
            for ($i = 3; $i <= $colCount; $i++) {
                $sheet->getColumnDimension(getColumnID($i))->setWidth(70 / 6);
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
}
