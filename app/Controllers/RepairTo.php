<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * RepairTo Controller - 維修管理
 */
class RepairTo extends AdminController
{
    protected $cod02Model;
    protected $ent10Model;
    protected $sys01Model;
    protected $pad03Model;
    protected $pad05Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper(['language', 'date', 'email']);
        $this->cod02Model = model('Cod02Model');
        $this->ent10Model = model('Ent10Model');
        $this->sys01Model = model('Sys01Model');
        $this->pad03Model = model('Pad03Model');
        $this->pad05Model = model('Pad05Model');
    }

    public function index(): string
    {
        $pageSize = $this->getPageSize();
        $ent0101 = $this->session->get('ent0101');
        $ent10Id = $this->ent10Model->getCurrentUserSeeDepartmentId();

        $option = ['ent1002' => $ent0101];
        if ($pad0513 = (int)($this->request->getGet('pad0513') ?? 0)) {
            $option['pad0513'] = $pad0513;
        }
        $option['*pad0510'] = "pad0510 in ({$ent10Id})";

        $totalRows = $this->pad05Model->countBy($option);
        $this->data['data'] = $this->pad05Model->getBy($option, $pageSize, 0, 'pad0509 desc');
        $this->data['offset'] = 0;
        $this->setPage($totalRows, 1, $pageSize);

        $this->data['pad0510_opt'] = $this->cod02Model->getOptionArrayByCode('pad0510');
        $this->data['pad0511_opt'] = $this->cod02Model->getOptionArrayByCode('pad0511');
        $this->data['pad0512_opt'] = $this->cod02Model->getOptionArrayByCode('pad0512');
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

        foreach ($option as $k1 => $v1) {
            if ($v1 === '') {
                unset($option[$k1]);
            }
        }

        $option['ent1002'] = $ent0101;
        $option['*pad0510'] = "pad0510 in ({$ent10Id})";

        $pageSize = $this->getPageSize();
        $offset = ($page - 1) * $pageSize;
        $this->data['offset'] = $offset;
        $totalRows = $this->pad05Model->countBy($option);
        $this->data['data'] = $this->pad05Model->getBy($option, $pageSize, $offset, 'pad0509 desc');
        $this->setPage($totalRows, $page, $pageSize);

        $this->data['pad0511_opt'] = $this->cod02Model->getOptionArrayByCode('pad0511');
        $this->data['pad0512_opt'] = $this->cod02Model->getOptionArrayByCode('pad0512');
        $this->data['option'] = $option;

        return $this->render('');
    }

    public function detail(int $id): string
    {
        $this->data['data'] = $this->pad05Model->find($id);
        $this->data['pad06s'] = $this->pad05Model->getPad06($id);
        $this->data['pad0511_opt'] = $this->cod02Model->getOptionArrayByCode('pad0511');
        $this->data['pad0512_opt'] = $this->cod02Model->getOptionArrayByCode('pad0512');

        return $this->render();
    }

    public function jiedan(int $id): string
    {
        $this->data['data'] = $this->pad05Model->find($id);
        $this->data['pad0512_opt'] = $this->cod02Model->getOptionArrayByCode('pad0512');

        return $this->render('');
    }

    public function saveJiedan(int $id): void
    {
        $pad05 = $this->pad05Model->find($id);
        if (!$pad05 || !is_object($pad05)) {
            echo json_encode(['status' => 'fail', 'info' => lang('RepairTo.save_not_data_hint')]);
        } elseif ($pad05->pad0513 != 1) {
            echo json_encode(['status' => 'fail', 'info' => lang('RepairTo.save_disuse_hint')]);
        } else {
            $pad0516 = trim($this->request->getPost('pad0516') ?? '');
            if (empty($pad0516)) {
                echo json_encode(['status' => 'fail', 'info' => lang('RepairTo.rules_pad0516_hint')]);
                return;
            }

            $updateData = [
                'pad0501' => (int)$this->request->getPost('pad0501'),
                'pad0516' => $pad0516,
                'pad0513' => 2,
                'pad0515' => $this->user->currentUser->sys0103 . $this->user->currentUser->sys0104,
                'pad0518' => date('Y-m-d H:i:s'),
            ];

            if ($pad0512 = $this->request->getPost('pad0512')) {
                $updateData['pad0512'] = (int)$pad0512;
            }

            $this->pad05Model->save($updateData);

            if ((int)$pad05->pad0520) {
                $this->db->query(sprintf(
                    "update pad03 set pad0306=%s,pad03z3=%s,pad03z4='%s' where pad0301=%s",
                    2,
                    $this->session->get('sys0101'),
                    date('Y-m-d H:i:s'),
                    $pad05->pad0520
                ));
            }

            echo json_encode(['status' => 'success', 'info' => lang('RepairTo.save_success_hint')]);
        }
    }

    public function upload(): void
    {
        $uploadPath = FCPATH . 'data/pad06/';
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $file = $this->request->getFile('files');
        if (!$file || !$file->isValid()) {
            echo json_encode(['status' => 'fail', 'info' => $file ? $file->getErrorString() : 'No file uploaded']);
            return;
        }

        $allowedTypes = ['image/gif', 'image/jpeg', 'image/png'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            echo json_encode(['status' => 'fail', 'info' => lang('RepairTo.upload_type_error')]);
            return;
        }

        if ($file->getSizeByUnit('kb') > 1024) {
            echo json_encode(['status' => 'fail', 'info' => lang('RepairTo.upload_size_error')]);
            return;
        }

        // Check image dimensions
        $imageInfo = getimagesize($file->getTempName());
        if ($imageInfo && ($imageInfo[0] > 1024 || $imageInfo[1] > 768)) {
            echo json_encode(['status' => 'fail', 'info' => lang('RepairTo.upload_dimension_error')]);
            return;
        }

        $newName = $file->getRandomName();
        if ($file->move($uploadPath, $newName)) {
            echo json_encode(['status' => 'success', 'info' => 'data/pad06/' . $newName]);
        } else {
            echo json_encode(['status' => 'fail', 'info' => $file->getErrorString()]);
        }
    }

    public function addpad06(int $id): string
    {
        $ent0101 = $this->session->get('ent0101');
        $this->data['ent10s'] = $this->ent10Model->getNameArray(
            'ent1001',
            'ent1004',
            ['' => lang('RepairTo.select_ent1004_default')],
            ['ent1002' => $ent0101, 'ent1007' => 1]
        );
        $this->data['sys01s'] = $this->sys01Model->getNameArray(
            'sys0101',
            'sys0103,sys0104',
            ['' => lang('RepairTo.select_sys0104_default')],
            ['sys0108' => 1, '*sys0110' => "sys0110={$this->user->currentUser->sys0110}"]
        );
        $this->data['pad0501'] = $id;

        return $this->render('');
    }

    public function saveAddpad06(int $id): void
    {
        $pad0508 = (int)($this->request->getPost('pad0508') ?? 0);
        $pad0505 = trim($this->request->getPost('pad0505') ?? '');
        $pad0606 = trim($this->request->getPost('pad0606') ?? '');

        if ($pad0508 === 0) {
            echo json_encode(['status' => 'fail', 'info' => lang('RepairTo.rules_pad0508_hint')]);
        } elseif (empty($pad0505)) {
            echo json_encode(['status' => 'fail', 'info' => lang('RepairTo.rules_pad0505_hint')]);
        } else {
            $pad05 = $this->pad05Model->find($id);
            if (!$pad05 || !is_object($pad05)) {
                echo json_encode(['status' => 'fail', 'info' => lang('RepairTo.save_not_data_hint')]);
            } elseif ($pad05->pad0513 != 2) {
                echo json_encode(['status' => 'fail', 'info' => lang('RepairTo.save_disuse_hint')]);
            } else {
                $this->db->query(sprintf(
                    "insert into pad06 (pad0602,pad0603,pad0604,pad0605,pad0606) values (%s,%s,'%s','%s','%s')",
                    $id,
                    $pad0508,
                    $pad0505,
                    date('Y-m-d H:i:s'),
                    $pad0606
                ));
                echo json_encode(['status' => 'success', 'info' => lang('RepairTo.save_success_hint')]);
            }
        }
    }

    public function jiean(int $id): string
    {
        $this->data['pad0501'] = $id;
        $this->data['pad0512_opt'] = $this->cod02Model->getOptionArrayByCode('pad0512', ['' => lang('RepairTo.dropdown_select')]);

        return $this->render('');
    }

    public function saveJiean(int $id): void
    {
        $pad0514 = trim($this->request->getPost('pad0514') ?? '');
        $pad0517 = trim($this->request->getPost('pad0517') ?? '');
        $pad0512 = (int)($this->request->getPost('pad0512') ?? 0);

        if (empty($pad0517) && empty($pad0514)) {
            echo json_encode(['status' => 'fail', 'info' => lang('RepairTo.must_miss_hint')]);
        } else {
            $pad05 = $this->pad05Model->find($id);
            if (!$pad05 || !is_object($pad05)) {
                echo json_encode(['status' => 'fail', 'info' => lang('RepairTo.save_not_data_hint')]);
            } elseif ($pad05->pad0513 != 2) {
                echo json_encode(['status' => 'fail', 'info' => lang('RepairTo.save_disuse_hint')]);
            } else {
                $updateData = [
                    'pad0501' => $id,
                    'pad0512' => $pad0512,
                    'pad0513' => 3,
                    'pad0514' => $pad0514,
                    'pad0517' => $pad0517,
                ];
                $this->pad05Model->save($updateData);

                if ((int)$pad05->pad0520) {
                    $this->db->query(sprintf(
                        "update pad03 set pad0306=%s,pad03z3=%s,pad03z4='%s' where pad0301=%s",
                        3,
                        $this->session->get('sys0101'),
                        date('Y-m-d H:i:s'),
                        $pad05->pad0520
                    ));
                }

                $this->sendmail($this->pad05Model->find($id));
                echo json_encode(['status' => 'success', 'info' => lang('RepairTo.save_success_hint')]);
            }
        }
    }

    public function goback(int $id): void
    {
        $pad05 = $this->pad05Model->find($id);
        if (!$pad05 || !is_object($pad05)) {
            echo json_encode(['status' => 'fail', 'info' => lang('RepairTo.save_not_data_hint')]);
        } elseif ($pad05->pad0513 != 4) {
            echo json_encode(['status' => 'fail', 'info' => lang('RepairTo.unable_close_hint')]);
        } else {
            $pad0508 = $pad05->pad0508;

            $updateData = ['pad0501' => $id, 'pad0513' => 2];
            $this->pad05Model->save($updateData);

            $this->db->query(sprintf(
                "insert into pad06 (pad0602,pad0603,pad0604,pad0605,pad0606) values (%s,%s,'%s','%s','')",
                $id,
                $pad0508,
                '退回維修中',
                date('Y-m-d H:i:s')
            ));

            echo json_encode(['status' => 'success', 'info' => lang('RepairTo.save_success_hint')]);
        }
    }

    public function closed(int $id): void
    {
        $pad05 = $this->pad05Model->find($id);
        if (!$pad05 || !is_object($pad05)) {
            echo json_encode(['status' => 'fail', 'info' => lang('RepairTo.save_not_data_hint')]);
        } elseif ($pad05->pad0513 != 4) {
            echo json_encode(['status' => 'fail', 'info' => lang('RepairTo.unable_close_hint')]);
        } else {
            $updateData = ['pad0501' => $id, 'pad0513' => 5];
            $this->pad05Model->save($updateData);

            if ((int)$pad05->pad0520) {
                $this->db->query(sprintf(
                    "update pad03 set pad0306=%s,pad03z3=%s,pad03z4='%s' where pad0301=%s",
                    5,
                    $this->session->get('sys0101'),
                    date('Y-m-d H:i:s'),
                    $pad05->pad0520
                ));
            }

            $this->sendmail($this->pad05Model->find($id));
            echo json_encode(['status' => 'success', 'info' => lang('RepairTo.save_success_hint')]);
        }
    }

    public function selectSys01(int $id): void
    {
        $listdb = [];
        $sys01s = $this->sys01Model->getBy(['sys0108' => 1, 'sys0110' => $id]);
        foreach ($sys01s as $v1) {
            $listdb[] = ['sys0101' => $v1->sys0101, 'sys0103' => $v1->sys0103 . $v1->sys0104];
        }
        echo json_encode($listdb);
    }

    private function sendmail(object $pad05): void
    {
        $body = lang('RepairTo.mail_content');

        // 維修部門
        $pad0510_opt = $this->cod02Model->getOptionArrayByCode('pad0510');
        $pad0510Str = '';
        foreach ($pad0510_opt as $k1 => $v1) {
            if ($k1 == $pad05->pad0510) {
                $pad0510Str .= sprintf("〔&nbsp;√&nbsp;〕%s，&nbsp;&nbsp;&nbsp;", $v1);
            } else {
                $pad0510Str .= sprintf("〔&nbsp;&nbsp;&nbsp;〕%s，&nbsp;&nbsp;&nbsp;", $v1);
            }
        }

        // 期望完工時間
        $pad0511_opt = $this->cod02Model->getOptionArrayByCode('pad0511');
        $pad0511Str = '';
        foreach ($pad0511_opt as $k1 => $v1) {
            if ($k1 == $pad05->pad0511) {
                $pad0511Str .= sprintf("〔&nbsp;√&nbsp;〕%s，&nbsp;&nbsp;&nbsp;", $v1);
            } else {
                $pad0511Str .= sprintf("〔&nbsp;&nbsp;&nbsp;〕%s，&nbsp;&nbsp;&nbsp;", $v1);
            }
        }

        // 延遲維修因素
        $pad0512_opt = $this->cod02Model->getOptionArrayByCode('pad0512');
        $pad0512Str = '';
        foreach ($pad0512_opt as $k1 => $v1) {
            if ($k1 == $pad05->pad0512) {
                $pad0512Str .= sprintf("〔&nbsp;√&nbsp;〕%s，&nbsp;&nbsp;&nbsp;", $v1);
            } else {
                $pad0512Str .= sprintf("〔&nbsp;&nbsp;&nbsp;〕%s，&nbsp;&nbsp;&nbsp;", $v1);
            }
        }

        // 維修明細
        $lstr = '';
        $count = 1;
        $pad06s = $this->pad05Model->getPad06($pad05->pad0501);
        if ($pad06s && is_array($pad06s) && count($pad06s)) {
            $count = count($pad06s) + 1;
            foreach ($pad06s as $v1) {
                $lstr .= sprintf(
                    '<tr><td>%s</td><td>%s%s</td><td>%s</td></tr>',
                    $v1->pad0604,
                    $v1->sys0103,
                    $v1->sys0104,
                    $v1->pad0605
                );
            }
        }

        $body = sprintf(
            $body,
            base_url('repair-from'),
            $this->session->get('ent0103'),
            $pad05->ent1003,
            $pad05->sys0103,
            $pad05->sys0104,
            date(lang('RepairTo.date_format'), strtotime($pad05->pad0509)),
            $pad05->pad0503,
            $pad0510Str,
            $pad05->pad0505,
            $pad0511Str,
            $pad05->pad0515,
            date(lang('RepairTo.date_format'), strtotime($pad05->pad0518)),
            $count,
            $lstr,
            $pad0512Str,
            $pad05->pad0514,
            $pad05->pad0516,
            $pad05->pad0517
        );

        $email = $this->pad05Model->getPowerEmail($pad05->pad0507, 30);
        if (empty($email)) {
            $email = 'admin@example.com';
        }

        sendMailReportByMail(sprintf(lang('RepairTo.mail_format'), $pad05->pad0504), $body, $email, $this->user->currentUser->sys0107);
    }

    public function export(): void
    {
        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        $title = lang('RepairTo.excel_title');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(18);
        $sheet->getColumnDimension('H')->setWidth(18);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(12);

        $sheet->mergeCells('A1:J3');
        $sheet->setCellValue('A1', $title);
        $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(15);

        $sheet->setCellValue('A4', lang('RepairTo.f_pad0504'));
        $sheet->setCellValue('B4', lang('RepairTo.f_pad0503'));
        $sheet->setCellValue('C4', lang('RepairTo.f_ent1004'));
        $sheet->setCellValue('D4', lang('RepairTo.f_sys0104'));
        $sheet->setCellValue('E4', lang('RepairTo.f_pad0509'));
        $sheet->setCellValue('F4', lang('RepairTo.f_cod0204'));
        $sheet->setCellValue('G4', lang('RepairTo.f_pad0511'));
        $sheet->setCellValue('H4', lang('RepairTo.f_pad0518'));
        $sheet->setCellValue('I4', lang('RepairTo.f_pad0512'));
        $sheet->setCellValue('J4', lang('RepairTo.f_pad0513'));

        $pad0511_opt = $this->cod02Model->getOptionArrayByCode('pad0511');
        $pad0512_opt = $this->cod02Model->getOptionArrayByCode('pad0512');

        $option = $this->getQueryOption();
        $ent0101 = $this->session->get('ent0101');
        $ent10Id = $this->ent10Model->getCurrentUserSeeDepartmentId();

        foreach ($option as $k1 => $v1) {
            if ($v1 === '') {
                unset($option[$k1]);
            }
        }
        $option['ent1002'] = $ent0101;
        $option['*pad0510'] = "pad0510 in ({$ent10Id})";

        $listdb = $this->pad05Model->getBy($option, 0, 0, 'pad0509 desc');
        $index = 5;
        foreach ($listdb as $v1) {
            $sheet->setCellValue('A' . $index, $v1->pad0504);
            $sheet->setCellValue('B' . $index, $v1->pad0503);
            $sheet->setCellValue('C' . $index, $v1->ent1004);
            $sheet->setCellValue('D' . $index, $v1->sys0103 . $v1->sys0104);
            $sheet->setCellValue('E' . $index, $v1->pad0509);
            $sheet->setCellValue('F' . $index, $v1->cod0204 ?? '');
            $sheet->setCellValue('G' . $index, $pad0511_opt[$v1->pad0511] ?? '');
            $sheet->setCellValue('H' . $index, $v1->pad0518);
            $sheet->setCellValue('I' . $index, $pad0512_opt[$v1->pad0512] ?? '');
            $sheet->setCellValue('J' . $index, lang('RepairTo.v_pad0513_' . $v1->pad0513));
            $index++;
        }

        $writer = new Xls($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . rawurlencode($title) . '.xls"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
