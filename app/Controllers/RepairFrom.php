<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * RepairFrom Controller - 委修管理
 */
class RepairFrom extends AdminController
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
        $ent0101 = $this->session->get('ent0101');
        $ent10Id = $this->ent10Model->getCurrentUserSeeDepartmentId();

        $option = ['ent1002' => $ent0101];
        if ($pad0513 = (int)($this->request->getGet('pad0513') ?? 0)) {
            $option['pad0513'] = $pad0513;
        }
        $option['*pad0507'] = "pad0507 in ({$ent10Id})";

        $pageSize = $this->getPageSize();
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
        $option['*pad0507'] = "pad0507 in ({$ent10Id})";

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

    public function sendto(int $id): string
    {
        $pad05s = $this->pad05Model->getBy(['pad0520' => $id]);
        if ($pad05s && is_array($pad05s) && count($pad05s)) {
            return $this->error(lang('RepairFrom.sendto_hint'));
        }

        $pad03 = $this->pad03Model->find($id);
        $pad03->pad0505 = $pad03->pad0303 . "\r\n" . $pad03->pad0305 . "\r\n" . $pad03->pad0304;
        $this->data['pad03'] = $pad03;

        $pad04s = $this->pad03Model->getPad04s($id);
        $pad0506 = '';
        foreach ($pad04s as $v1) {
            $pad0506 .= $v1->pad0403 . ',';
        }
        $pad0506 = rtrim($pad0506, ",");

        $this->data['pad04s'] = $pad04s;
        $this->data['pad0301'] = $id;
        $this->data['pad0506'] = $pad0506;

        $ent0101 = $this->session->get('ent0101');
        $this->data['ent10s'] = $this->ent10Model->getNameArray(
            'ent1001',
            'ent1004',
            ['' => lang('RepairFrom.select_ent1004_default')],
            ['ent1002' => $ent0101, 'ent1007' => 1]
        );
        $this->data['sys01s'] = $this->sys01Model->getNameArray(
            'sys0101',
            'sys0103,sys0104',
            ['' => lang('RepairFrom.select_sys0104_default')],
            ['sys0108' => 1, '*sys0110' => "sys0110={$this->user->currentUser->sys0110}"]
        );

        $this->data['pad0510_opt'] = $this->cod02Model->getOptionArrayByCode('pad0510', ['' => lang('RepairFrom.f_cod0204')]);
        $this->data['pad0511_opt'] = $this->cod02Model->getOptionArrayByCode('pad0511', ['' => lang('RepairFrom.f_pad0511')]);
        $this->data['pad0512_opt'] = $this->cod02Model->getOptionArrayByCode('pad0512', ['' => lang('RepairFrom.f_pad0512')]);
        $this->data['data'] = null;

        return $this->render();
    }

    public function goback(int $id): void
    {
        $pad05 = $this->pad05Model->find($id);
        if (!$pad05 || !is_object($pad05)) {
            echo json_encode(['status' => 'fail', 'info' => lang('RepairFrom.save_not_data_hint')]);
        } elseif ($pad05->pad0513 != 3) {
            echo json_encode(['status' => 'fail', 'info' => lang('RepairFrom.unable_close_hint')]);
        } else {
            $pad0508 = $pad05->pad0508;

            $updateData = ['pad0501' => $id, 'pad0513' => 2];
            $this->pad05Model->save($updateData);

            $pad0505 = '[退回維修中]' . trim($this->request->getPost('pad0505') ?? '');
            $this->db->query(sprintf(
                "insert into pad06 (pad0602,pad0603,pad0604,pad0605,pad0606) values (%s,%s,'%s','%s','')",
                $id,
                $pad0508,
                $pad0505,
                date('Y-m-d H:i:s')
            ));

            echo json_encode(['status' => 'success', 'info' => lang('RepairFrom.save_success_hint')]);
        }
    }

    public function sendtoSave(int $id = 0): \CodeIgniter\HTTP\ResponseInterface
    {
        if ($postData = $this->request->getPost()) {
            $rules = [
                'pad0502' => 'required',
                'pad0504' => 'required',
                'pad0503' => 'required',
                'pad0507' => 'required',
                'pad0508' => 'required',
                'pad0510' => 'required',
                'pad0505' => 'required',
                'pad0511' => 'required',
            ];

            if ($this->validate($rules)) {
                $postData['pad0509'] = date('Y-m-d H:i:s');
                $postData['pad0513'] = 1;

                if (!isset($postData['pad0501'])) {
                    if ($id = $this->pad05Model->insert($postData)) {
                        if ((int)($postData['pad0520'] ?? 0)) {
                            $this->db->query(sprintf(
                                "update pad03 set pad0306=%s,pad03z3=%s,pad03z4='%s' where pad0301=%s",
                                $postData['pad0513'],
                                $this->session->get('sys0101'),
                                date('Y-m-d H:i:s'),
                                $postData['pad0520']
                            ));
                        }

                        $this->sendmail($this->pad05Model->find($id));
                        return $this->ajaxReturn("OK", ['id' => $id, 'title' => $postData['pad0502']]);
                    }
                } else {
                    if ($this->pad05Model->save($postData)) {
                        $this->sendmail($this->pad05Model->find($postData['pad0501']));
                        return $this->ajaxReturn("OK", ['id' => $postData['pad0501'], 'title' => $postData['pad0502']]);
                    }
                }
            }

            return $this->ajaxReturn($this->message->output());
        }

        return $this->ajaxReturn("OK");
    }

    public function upload(): void
    {
        $uploadPath = FCPATH . 'data/pad05/';
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
            echo json_encode(['status' => 'fail', 'info' => lang('RepairFrom.upload_type_error')]);
            return;
        }

        if ($file->getSizeByUnit('kb') > 1024) {
            echo json_encode(['status' => 'fail', 'info' => lang('RepairFrom.upload_size_error')]);
            return;
        }

        // Check image dimensions
        $imageInfo = getimagesize($file->getTempName());
        if ($imageInfo && ($imageInfo[0] > 1024 || $imageInfo[1] > 768)) {
            echo json_encode(['status' => 'fail', 'info' => lang('RepairFrom.upload_dimension_error')]);
            return;
        }

        $newName = $file->getRandomName();
        if ($file->move($uploadPath, $newName)) {
            echo json_encode(['status' => 'success', 'info' => 'data/pad05/' . $newName]);
        } else {
            echo json_encode(['status' => 'fail', 'info' => $file->getErrorString()]);
        }
    }

    public function edit(int $id = 0): string
    {
        $this->data['pad0510_opt'] = $this->cod02Model->getOptionArrayByCode('pad0510', ['' => lang('RepairFrom.f_cod0204')]);
        $this->data['pad0511_opt'] = $this->cod02Model->getOptionArrayByCode('pad0511', ['' => lang('RepairFrom.f_pad0511')]);
        $this->data['pad0512_opt'] = $this->cod02Model->getOptionArrayByCode('pad0512', ['' => lang('RepairFrom.f_pad0512')]);

        if ($id) {
            $pad05 = $this->pad05Model->find($id);
            $pad0506s = ($pad05 && is_object($pad05) && !empty($pad05->pad0506)) ? explode(",", $pad05->pad0506) : [];
            $this->data['data'] = $pad05;
            $this->data['pad0506s'] = $pad0506s;
        } else {
            $this->data['data'] = null;
            $this->data['pad0506s'] = [];
        }

        $ent10 = $this->ent10Model->find($this->user->currentUser->sys0110);
        $pad0502 = sprintf('%s-%s', $ent10->ent1003, date('YmdHi'));
        $this->data['pad0502'] = $pad0502;

        $ent0101 = $this->session->get('ent0101');
        $this->data['ent10s'] = $this->ent10Model->getNameArray(
            'ent1001',
            'ent1004',
            ['' => lang('RepairFrom.select_ent1004_default')],
            ['ent1002' => $ent0101, 'ent1007' => 1]
        );
        $this->data['sys01s'] = $this->sys01Model->getNameArray(
            'sys0101',
            'sys0103,sys0104',
            ['' => lang('RepairFrom.select_sys0104_default')],
            ['sys0108' => 1, '*sys0110' => "sys0110={$this->user->currentUser->sys0110}"]
        );

        return $this->render();
    }

    public function detail(int $id): string
    {
        $this->data['data'] = $this->pad05Model->find($id);
        $this->data['pad06s'] = $this->pad05Model->getPad06($id);
        $this->data['pad0511_opt'] = $this->cod02Model->getOptionArrayByCode('pad0511');
        $this->data['pad0512_opt'] = $this->cod02Model->getOptionArrayByCode('pad0512');

        return $this->render();
    }

    public function closed(int $id): void
    {
        $pad05 = $this->pad05Model->find($id);
        if (!$pad05 || !is_object($pad05)) {
            echo json_encode(['status' => 'fail', 'info' => lang('RepairFrom.closed_hint_1')]);
        } elseif ($pad05->pad0513 != 3) {
            echo json_encode(['status' => 'fail', 'info' => lang('RepairFrom.closed_hint_2')]);
        } else {
            $updateData = [
                'pad0501' => $id,
                'pad0513' => 4,
                'pad0519' => trim($this->request->getPost('pad0519') ?? ''),
            ];
            $this->pad05Model->save($updateData);

            if ((int)$pad05->pad0520) {
                $this->db->query(sprintf(
                    "update pad03 set pad0306=%s,pad03z3=%s,pad03z4='%s' where pad0301=%s",
                    4,
                    $this->session->get('sys0101'),
                    date('Y-m-d H:i:s'),
                    $pad05->pad0520
                ));
            }

            echo json_encode(['status' => 'success', 'info' => lang('RepairFrom.closed_hint_3')]);
        }
    }

    public function delete(): \CodeIgniter\HTTP\ResponseInterface
    {
        if ($id = $this->request->getPost('id')) {
            if ($this->pad05Model->delete($id)) {
                return $this->ajaxReturn("OK");
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('ERR');
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
        $body = lang('RepairFrom.mail_content');

        $pad0510_opt = $this->cod02Model->getOptionArrayByCode('pad0510');
        $pad0510Str = '';
        foreach ($pad0510_opt as $k1 => $v1) {
            if ($k1 == $pad05->pad0510) {
                $pad0510Str .= sprintf("〔&nbsp;√&nbsp;〕%s，&nbsp;&nbsp;&nbsp;", $v1);
            } else {
                $pad0510Str .= sprintf("〔&nbsp;&nbsp;&nbsp;〕%s，&nbsp;&nbsp;&nbsp;", $v1);
            }
        }

        $pad0511_opt = $this->cod02Model->getOptionArrayByCode('pad0511');
        $pad0511Str = '';
        foreach ($pad0511_opt as $k1 => $v1) {
            if ($k1 == $pad05->pad0511) {
                $pad0511Str .= sprintf("〔&nbsp;√&nbsp;〕%s，&nbsp;&nbsp;&nbsp;", $v1);
            } else {
                $pad0511Str .= sprintf("〔&nbsp;&nbsp;&nbsp;〕%s，&nbsp;&nbsp;&nbsp;", $v1);
            }
        }

        $pad0512_opt = $this->cod02Model->getOptionArrayByCode('pad0512');
        $pad0512Str = '';
        foreach ($pad0512_opt as $k1 => $v1) {
            if ($k1 == $pad05->pad0512) {
                $pad0512Str .= sprintf("〔&nbsp;√&nbsp;〕%s，&nbsp;&nbsp;&nbsp;", $v1);
            } else {
                $pad0512Str .= sprintf("〔&nbsp;&nbsp;&nbsp;〕%s，&nbsp;&nbsp;&nbsp;", $v1);
            }
        }

        $body = sprintf(
            $body,
            base_url('repair-to'),
            $this->session->get('ent0103'),
            $pad05->ent1003,
            $pad05->sys0103,
            $pad05->sys0104,
            date(lang('RepairFrom.date_format'), strtotime($pad05->pad0509)),
            $pad05->pad0503,
            $pad0510Str,
            $pad05->pad0505,
            $pad0511Str,
            $pad0512Str
        );

        $email = $this->pad05Model->getPowerEmail($pad05->pad0510, 31);
        if (empty($email)) {
            $email = 'admin@example.com';
        }

        sendMailReportByMail(sprintf(lang('RepairFrom.mail_format'), $pad05->pad0504), $body, $email, $this->user->currentUser->sys0107);
    }

    public function export(): void
    {
        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        $title = lang('RepairFrom.excel_title');
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

        $sheet->setCellValue('A4', lang('RepairFrom.f_pad0504'));
        $sheet->setCellValue('B4', lang('RepairFrom.f_pad0503'));
        $sheet->setCellValue('C4', lang('RepairFrom.f_ent1004'));
        $sheet->setCellValue('D4', lang('RepairFrom.f_sys0104'));
        $sheet->setCellValue('E4', lang('RepairFrom.f_pad0509'));
        $sheet->setCellValue('F4', lang('RepairFrom.f_cod0204'));
        $sheet->setCellValue('G4', lang('RepairFrom.f_pad0511'));
        $sheet->setCellValue('H4', lang('RepairFrom.f_pad0518'));
        $sheet->setCellValue('I4', lang('RepairFrom.f_pad0512'));
        $sheet->setCellValue('J4', lang('RepairFrom.f_pad0513'));

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
        $option['*pad0507'] = "pad0507 in ({$ent10Id})";

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
            $sheet->setCellValue('J' . $index, lang('RepairFrom.v_pad0513_' . $v1->pad0513));
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
