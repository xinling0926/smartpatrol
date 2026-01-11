<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;

/**
 * ReportNotification Command - 巡檢報表通知
 */
class ReportNotification extends BaseCommand
{
    protected $group       = 'Patrol';
    protected $name        = 'patrol:report-notification';
    protected $description = '發送巡檢報表通知';
    protected $usage       = 'patrol:report-notification';

    protected $fmd01Model;
    protected $fmd31Model;
    protected $isoModel;
    protected $setting;

    public function run(array $params): void
    {
        $this->fmd01Model = model('Fmd01Model');
        $this->fmd31Model = model('Fmd31Model');
        $this->isoModel = model('IsoModel');
        $this->setting = service('setting');

        log_message('info', '[' . static::class . '] 開始檢查巡檢報表');

        $email = Services::email();

        // 從設定檔載入 email 設定
        $configFile = APPPATH . 'Config/Patrol.php';
        if (file_exists($configFile)) {
            $config = config('Patrol');
            if (isset($config->email)) {
                $email->initialize((array)$config->email);
            }
        }

        $mailQueue = [];

        $fmd31s = $this->fmd31Model->getAll();
        if ($fmd31s) {
            foreach ($fmd31s as $fmd31) {
                $fmd3103 = json_decode($fmd31->fmd3103);
                $fmd3104 = json_decode($fmd31->fmd3104);
                $fmd0104 = $fmd31->fmd0104;

                helper('date');
                $isoDate = $this->isoModel->isoDate($fmd31->fmd0105, date("Y-m-d", strtotime(yesterday())));

                $fmd01 = $this->fmd01Model->find($fmd31->fmd0101);
                if ($fmd01) {
                    $this->isoModel->setTableName($fmd01);
                    $reportMaster = $this->isoModel->getMasterByDate($isoDate);

                    if ($reportMaster) {
                        $queue = new \stdClass;
                        $queue->reportName = $fmd0104;
                        $queue->iso_date = $isoDate;
                        $queue->subject = $fmd0104 . '(' . $isoDate . ') 巡檢通知';
                        $queue->emailList = false;
                        $queue->body = '';

                        if (!empty($fmd3103->ERROR) && !empty($reportMaster->error_count)) {
                            $queue->emailList = $fmd3104;
                            $queue->body .= ' 異常 ' . $reportMaster->error_count . ' 筆 ';
                        }
                        if (!empty($fmd3103->MISS) && !empty($reportMaster->miss_count)) {
                            $queue->emailList = $fmd3104;
                            $queue->body .= ' 漏檢 ' . $reportMaster->miss_count . ' 筆 ';
                        }
                        $mailQueue[$fmd31->fmd3101] = $queue;
                    } else {
                        log_message('info', '[' . static::class . '] ' . $fmd31->fmd0104 . '(' . $isoDate . ') 沒有主檔');
                    }
                }
            }
        }

        // 處理 mail 通知
        foreach ($mailQueue as $queue) {
            if ($queue->emailList) {
                $data = [
                    'site_title' => $this->setting->item('site_title'),
                    'report_name' => $queue->reportName,
                    'iso_date' => $queue->iso_date,
                    'notification' => $queue->body,
                    'base_url' => base_url(),
                ];

                log_message('info', '[' . static::class . '] mail subject: ' . $queue->subject);

                $email->setSubject($this->setting->item('site_title') . ' - ' . $queue->subject);
                $email->setFrom($this->setting->item('system_email'), $this->setting->item('site_title'));

                $to = [];
                $to2 = [];
                foreach ($queue->emailList as $list) {
                    $to[] = '"' . $list->name . ' <' . $list->email . '>"';
                    if (!empty($list->email)) {
                        $to2[] = $list->email;
                    }
                }

                log_message('info', '[' . static::class . '] mail to: ' . implode(', ', $to));
                $email->setTo(implode(',', $to2));

                log_message('info', '[' . static::class . '] mail body: ' . $queue->body);
                $email->setMessage(view('form_item/email/report_notification', $data));

                try {
                    if ($email->send()) {
                        log_message('info', '[' . static::class . '] send notification success');
                        CLI::write('通知發送成功: ' . $queue->reportName, 'green');
                    } else {
                        log_message('info', '[' . static::class . '] send notification error: ' . $email->printDebugger());
                        CLI::write('通知發送失敗: ' . $queue->reportName, 'red');
                    }
                } catch (\Exception $e) {
                    log_message('info', '[' . static::class . '] send notification error: ' . $e->getMessage());
                    CLI::error('通知發送錯誤: ' . $e->getMessage());
                }
            } else {
                log_message('info', '[' . static::class . '] ' . $queue->reportName . ' 不需要發送通知');
                CLI::write($queue->reportName . ' 不需要發送通知', 'yellow');
            }
        }

        log_message('info', '[' . static::class . '] 結束檢查巡檢報表');
        CLI::write('巡檢報表通知檢查完成', 'green');
    }
}
