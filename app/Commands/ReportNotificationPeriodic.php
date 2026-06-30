<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;

/**
 * ReportNotificationPeriodic Command - 週期性連續異常通知（特殊版）
 *
 * 與 patrol:report-notification（每日、只看昨天）不同：
 *  1. 依 cron_startdate + notification_freq 判斷「今天是不是週期日」，不是就跳過
 *  2. 回看整個週期（notification_freq 天），找出「連續異常達 error_limit_count 次」
 *     的巡檢項目，彙整成通知信
 *
 * 排程仍每日執行，由本程式自行判斷是否為週期日：
 *   10 7 * * * cd /var/www/smartpatrol/ci4 && /usr/bin/php spark patrol:report-notification-periodic
 *
 * 使用的系統設定（sys10）：
 *   cron_startdate     通知信起始日期（預設 2020-08-25）
 *   notification_freq  週期天數（預設 6）
 *   error_limit_count  累計次數門檻（預設 4）
 */
class ReportNotificationPeriodic extends BaseCommand
{
    protected $group       = 'Patrol';
    protected $name        = 'patrol:report-notification-periodic';
    protected $description = '週期性連續異常通知（回看整個週期，找連續異常項目）';
    protected $usage       = 'patrol:report-notification-periodic';

    protected $fmd01Model;
    protected $fmd31Model;
    protected $isoModel;
    protected $setting;
    protected $db;

    /**
     * 計算某日期的絕對天數（與 CI3 版本一致，避免時區/DST 誤差）
     */
    private function days(\DateTime $x): int
    {
        $y = (int)$x->format('Y') - 1;
        $days = $y * 365;
        $days += (int)($y / 4);
        $days -= (int)($y / 100);
        $days += (int)($y / 400);
        $days += (int)$x->format('z');

        return $days;
    }

    public function run(array $params): void
    {
        $this->fmd01Model = model('Fmd01Model');
        $this->fmd31Model = model('Fmd31Model');
        $this->isoModel   = model('IsoModel');
        $this->setting    = service('setting');
        $this->db         = db_connect();

        log_message('info', '[' . static::class . '] 開始檢查巡檢報表（週期版）');

        // 載入 email 設定（Config/Patrol.php，繞過 .env 覆寫機制）
        $email = Services::email();
        $configFile = APPPATH . 'Config/Patrol.php';
        if (file_exists($configFile)) {
            $config = config('Patrol');
            if (isset($config->email)) {
                $email->initialize((array)$config->email);
            }
        }

        // === 週期判斷 ===
        $begindate = $this->setting->item('cron_startdate');
        if (empty($begindate)) {
            $begindate = '2020-08-25';
        }
        $f = (int)$this->setting->item('notification_freq');
        $f = $f ?: 6;
        $t = (int)$this->setting->item('error_limit_count');
        $t = $t ?: 4;

        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $a1 = $this->days(date_create($begindate)) - 1;
        $a2 = $this->days(date_create($yesterday));

        CLI::write("開始日期: {$begindate} ({$a1})");
        CLI::write("昨天日期: {$yesterday} ({$a2})");
        CLI::write('相差: ' . ($a2 - $a1) . ' 天，週期: ' . $f);

        if ((($a2 - $a1) % $f) != 0) {
            CLI::write('今天非週期日，不執行', 'yellow');
            log_message('info', '[' . static::class . '] 今天非週期日，不執行');
            return;
        }
        CLI::write('今天是週期日，開始處理', 'green');

        $view = 'formitem/email/report_notification';

        $mailQueue = [];

        $fmd31s = $this->fmd31Model->getAll();
        if ($fmd31s) {
            foreach ($fmd31s as $fmd31) {
                $fmd3103 = json_decode($fmd31->fmd3103);
                $fmd3104 = json_decode($fmd31->fmd3104);
                $fmd0104 = $fmd31->fmd0104;

                $isoDate = $this->isoModel->isoDate($fmd31->fmd0105, $yesterday);

                $fmd01 = $this->fmd01Model->find($fmd31->fmd0101);
                if (!$fmd01) {
                    continue;
                }
                $tableid = $fmd01->fmd0106;
                $this->isoModel->setTableName($fmd01);

                $reportMaster = $this->isoModel->getMasterByDate($isoDate);
                if (!$reportMaster) {
                    log_message('info', '[' . static::class . '] ' . $fmd31->fmd0104 . '(' . $isoDate . ') 沒有主檔');
                    continue;
                }

                $queue = new \stdClass();
                $queue->reportName = $fmd0104;
                $queue->iso_date = $isoDate;
                $queue->subject = $fmd0104 . '(' . $isoDate . ') 巡檢通知';
                $queue->emailList = false;
                $queue->body = '';

                // 標準異常/漏檢判斷（決定是否有收件清單）
                if (!empty($fmd3103->ERROR) && !empty($reportMaster->error_count)) {
                    $queue->emailList = $fmd3104;
                }
                if (!empty($fmd3103->MISS) && !empty($reportMaster->miss_count)) {
                    $queue->emailList = $fmd3104;
                }

                // === 週期內連續異常分析 ===
                $ed = $yesterday;
                $sd = date('Y-m-d', strtotime("{$ed} -{$f} day"));
                $queue->startdate = $sd;
                $queue->enddate = $ed;
                $queue->body = ''; // 只發送連續異常，不發送單日漏檢/異常

                // 取得這個報表的項目欄位數（fmd03）
                $rs = $this->db->query("SELECT COUNT(0) AS c FROM fmd03 WHERE fmd0302 = ?", [$tableid])->getResult();
                if (count($rs) > 0 && $rs[0]->c > 0) {
                    // 組合 item1_name/item2_name/... 的 concat
                    $gf = '';
                    for ($i = 1; $i <= $rs[0]->c; $i++) {
                        $gf .= (empty($gf))
                            ? "ifnull(item{$i}_name,'')"
                            : ",'/'," . "ifnull(item{$i}_name,'')";
                    }
                    $tbname = ($tableid < 10) ? '0' . $tableid : $tableid;

                    $sql  = "SELECT concat({$gf}) AS d, sum(if(a.error_count,1,0)+if(a.miss_count,1,0)) AS c";
                    $sql .= " FROM iso{$tbname}a a INNER JOIN iso{$tbname} m ON a.master_id=m.id";
                    $sql .= " WHERE m.date BETWEEN ? AND ?";
                    $sql .= " GROUP BY d HAVING c >= ?";
                    log_message('info', '[' . static::class . '] count report sql: ' . $sql . " [{$sd}, {$ed}, {$t}]");

                    $queue->emailList = $fmd3104;
                    $queue->body .= '週期內連續異常項目如下';

                    if ($rows = $this->db->query($sql, [$sd, $ed, $t])->getResult()) {
                        foreach ($rows as $r) {
                            if (empty(trim($r->d))) {
                                continue;
                            }
                            $queue->body .= '<br/>';
                            $queue->body .= "{$r->d}-------(累積次數:{$r->c})";
                        }
                    } else {
                        $queue->body .= '<br/>恭喜您，這個週期沒有連續異常超過' . $t . '次的項目。';
                    }
                }

                $mailQueue[$fmd31->fmd3101] = $queue;
            }
        }

        // === 處理 mail 通知 ===
        foreach ($mailQueue as $queue) {
            if (!$queue->emailList) {
                log_message('info', '[' . static::class . '] ' . $queue->reportName . ' 不需要發送通知');
                CLI::write($queue->reportName . ' 不需要發送通知', 'yellow');
                continue;
            }

            $data = [
                'site_title'   => $this->setting->item('site_title'),
                'report_name'  => $queue->reportName,
                'iso_date'     => $queue->iso_date,
                'notification' => $queue->body,
                'startdate'    => $queue->startdate,
                'enddate'      => $queue->enddate,
                'base_url'     => base_url(),
            ];

            log_message('info', '[' . static::class . '] mail subject: ' . $queue->subject);
            $email->setSubject($this->setting->item('site_title') . ' - ' . $queue->subject);
            $email->setFrom($this->setting->item('system_email'), $this->setting->item('site_title'));

            $to2 = [];
            foreach ($queue->emailList as $list) {
                if (!empty($list->email)) {
                    $to2[] = $list->email;
                }
            }
            log_message('info', '[' . static::class . '] mail to: ' . implode(', ', $to2));
            $email->setTo(implode(',', $to2));

            $email->setMessage(view($view, $data));

            if ($email->send(false)) {
                log_message('info', '[' . static::class . '] send notification success');
                CLI::write('通知發送成功: ' . $queue->reportName, 'green');
            } else {
                log_message('error', '[' . static::class . '] send notification error: ' . $email->printDebugger());
                CLI::write('通知發送失敗: ' . $queue->reportName, 'red');
            }
        }

        log_message('info', '[' . static::class . '] 結束檢查巡檢報表（週期版）');
        CLI::write('週期性巡檢通知檢查完成', 'green');
    }
}
