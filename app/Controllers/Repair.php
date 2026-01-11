<?php

namespace App\Controllers;

/**
 * Repair Controller - 報修管理
 */
class Repair extends AdminController
{
    protected $pad03Model;
    protected $dev01Model;
    protected $sys01Model;
    protected $ent10Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper('language');
        $this->pad03Model = model('Pad03Model');
        $this->dev01Model = model('Dev01Model');
        $this->sys01Model = model('Sys01Model');
        $this->ent10Model = model('Ent10Model');
    }

    public function index(): string
    {
        $ent0101 = $this->session->get('ent0101');
        $ent10Id = $this->ent10Model->getCurrentUserSeeDepartmentId();

        $option = [
            'pad0306' => 0,
            '*sys0110' => "sys0110 in ({$ent10Id})",
        ];

        $pageSize = $this->getPageSize();
        $totalRows = $this->pad03Model->countBy($option);
        $this->data['data'] = $this->pad03Model->getBy($option, $pageSize, 0, 'pad0301 desc');
        $this->data['offset'] = 0;
        $this->setPage($totalRows, 1, $pageSize);

        $this->data['dev01s'] = $this->dev01Model->getNameArray(
            'dev0101',
            'dev0104',
            ['' => lang('Repair.select_dev0104_default')],
            ['dev0102' => $ent0101, '*dev0103' => "(dev0103 is null or dev0103 in ({$ent10Id}))"],
            'dev0104'
        );

        $this->data['sys01s'] = $this->sys01Model->getNameArray(
            'sys0101',
            'sys0103,sys0104',
            ['' => lang('Repair.select_sys0104_default')],
            ['sys0205' => 1, 'sys0108' => 1, '*sys0110' => "sys0110 in ({$ent10Id})"]
        );

        $this->data['option'] = $option;

        return $this->render();
    }

    public function query(int $page = 0): string
    {
        $ent0101 = $this->session->get('ent0101');
        $ent10Id = $this->ent10Model->getCurrentUserSeeDepartmentId();

        if ($page) {
            $option = $this->loadQueryOption();
        } else {
            $page = 1;
            $option = $this->getQueryOption();
        }

        // Remove empty options
        foreach ($option as $k1 => $v1) {
            if ($v1 === '') {
                unset($option[$k1]);
            }
        }

        $option['*sys0110'] = "sys0110 in ({$ent10Id})";

        $pageSize = $this->getPageSize();
        $offset = ($page - 1) * $pageSize;
        $this->data['offset'] = $offset;
        $totalRows = $this->pad03Model->countBy($option);
        $this->data['data'] = $this->pad03Model->getBy($option, $pageSize, $offset, 'pad0301 desc');
        $this->setPage($totalRows, $page, $pageSize);

        $this->data['dev01s'] = $this->dev01Model->getNameArray(
            'dev0101',
            'dev0104',
            ['' => lang('Repair.select_dev0104_default')],
            ['dev0102' => $ent0101, '*dev0103' => "(dev0103 is null or dev0103 in ({$ent10Id}))"],
            'dev0104'
        );

        $this->data['sys01s'] = $this->sys01Model->getNameArray(
            'sys0101',
            'sys0103,sys0104',
            ['' => lang('Repair.select_sys0104_default')],
            ['sys0205' => 1, 'sys0108' => 1, '*sys0110' => "sys0110 in ({$ent10Id})"]
        );

        $this->data['option'] = $option;

        return $this->render('');
    }

    public function detail(int $id): string
    {
        $data = $this->pad03Model->find($id);
        $data->pad04s = $this->pad03Model->getPad04s($id);
        $this->data['data'] = $data;

        return $this->render();
    }

    /**
     * Direct case closing
     */
    public function jiean(int $id): \CodeIgniter\HTTP\ResponseInterface
    {
        $pad0308 = trim($this->request->getPost('pad0308') ?? '');

        if (empty($pad0308)) {
            return $this->response->setJSON(['status' => 'fail', 'info' => lang('Repair.not_pad0308_hint')]);
        }

        $pad03 = $this->pad03Model->find($id);
        if (!$pad03) {
            return $this->response->setJSON(['status' => 'fail', 'info' => lang('Repair.not_pad03_hint')]);
        }

        $pad03->pad0306 = 5;
        $pad03->pad0308 = $pad0308;
        $this->pad03Model->save($pad03);

        return $this->response->setJSON(['status' => 'success', 'info' => lang('Repair.save_hint')]);
    }

    public function export(): void
    {
        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        $index = 5;
        $title = lang('Repair.excel_title');

        // Create spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(18);

        $sheet->mergeCells('A1:E3');
        $sheet->setCellValue('A1', $title);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(15);

        $sheet->setCellValue('A4', lang('Repair.f_dev0104'));
        $sheet->setCellValue('B4', lang('Repair.f_pad0303'));
        $sheet->setCellValue('C4', lang('Repair.f_pad0306'));
        $sheet->setCellValue('D4', lang('Repair.f_sys0104'));
        $sheet->setCellValue('E4', lang('Repair.f_pad03z2'));

        $option = $this->getQueryOption();
        $ent10Id = $this->ent10Model->getCurrentUserSeeDepartmentId();

        foreach ($option as $k1 => $v1) {
            if ($v1 === '') {
                unset($option[$k1]);
            }
        }
        $option['*sys0110'] = "sys0110 in ({$ent10Id})";

        $listdb = $this->pad03Model->getBy($option, 0, 0, 'pad0301 desc');

        foreach ($listdb as $v1) {
            $sheet->setCellValue('A' . $index, $v1->dev0104);
            $sheet->setCellValue('B' . $index, $v1->pad0303);

            $statusMap = [
                0 => lang('Repair.v_pad0306_0'),
                1 => lang('Repair.v_pad0306_1'),
                2 => lang('Repair.v_pad0306_2'),
                3 => lang('Repair.v_pad0306_3'),
                4 => lang('Repair.v_pad0306_4'),
                5 => lang('Repair.v_pad0306_5'),
            ];
            $sheet->setCellValue('C' . $index, $statusMap[$v1->pad0306] ?? '');
            $sheet->setCellValue('D' . $index, $v1->sys0103 . $v1->sys0104);
            $sheet->setCellValue('E' . $index, $v1->pad03z2);
            $index++;
        }

        // Output Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . rawurlencode($title) . '.xls"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
