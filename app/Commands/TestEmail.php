<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;

/**
 * TestEmail Command - 測試 SMTP 寄信設定
 *
 * 讀取 Config/Patrol.php 的 email 設定寄出測試信，
 * 並把完整 SMTP 對話印在畫面上，方便診斷寄信問題。
 *
 * 用法：
 *   php spark patrol:test-email 收件人@example.com
 *   php spark patrol:test-email 收件人@example.com 寄件人@example.com
 */
class TestEmail extends BaseCommand
{
    protected $group       = 'Patrol';
    protected $name        = 'patrol:test-email';
    protected $description = '測試 SMTP 寄信設定（顯示完整 SMTP 對話）';
    protected $usage       = 'patrol:test-email <to> [from]';
    protected $arguments   = [
        'to'   => '收件人 email（必填）',
        'from' => '寄件人 email（選填，預設讀 Config/Patrol 的 fromEmail）',
    ];

    public function run(array $params): void
    {
        $to = $params[0] ?? null;
        if (!$to) {
            CLI::error('請提供收件人 email：php spark patrol:test-email 收件人@example.com');
            return;
        }

        // 讀取 Config/Patrol.php 的 email 設定
        $emailConfig = [];
        $configFile = APPPATH . 'Config/Patrol.php';
        if (file_exists($configFile)) {
            $config = config('Patrol');
            if (isset($config->email)) {
                $emailConfig = (array)$config->email;
            }
            CLI::write('已讀取 Config/Patrol.php 的 email 設定', 'green');
        } else {
            CLI::write('找不到 Config/Patrol.php，將使用 Config/Email.php 的設定', 'yellow');
        }

        // 顯示目前使用的設定（密碼遮罩）
        CLI::write('--- 目前 SMTP 設定 ---', 'cyan');
        foreach ($emailConfig as $k => $v) {
            $display = ($k === 'SMTPPass') ? str_repeat('*', strlen((string)$v)) : $v;
            if (is_bool($display)) {
                $display = $display ? 'true' : 'false';
            }
            CLI::write("  {$k} = {$display}");
        }

        // 寄件人：優先用參數，其次 Config/Patrol 的 fromEmail
        $from = $params[1] ?? ($emailConfig['fromEmail'] ?? '');
        $fromName = $emailConfig['fromName'] ?? 'SmartPatrol';
        if (!$from) {
            CLI::error('沒有寄件人 email，請用第二個參數指定，或在 Config/Patrol.php 設定 fromEmail');
            return;
        }

        CLI::write('--- 開始寄送 ---', 'cyan');
        CLI::write("From: {$from}");
        CLI::write("To:   {$to}");

        $email = Services::email();
        if ($emailConfig) {
            $email->initialize($emailConfig);
        }

        $email->setFrom($from, $fromName);
        $email->setTo($to);
        $email->setSubject('SmartPatrol SMTP 測試信 ' . date('Y-m-d H:i:s'));
        $email->setMessage('這是一封來自 SmartPatrol 的 SMTP 測試信。<br>若您收到此信，代表寄信設定正常。<br>時間：' . date('Y-m-d H:i:s'));

        if ($email->send(false)) {
            CLI::write('✓ 寄送成功！請到收件匣確認。', 'green');
        } else {
            CLI::error('✗ 寄送失敗，以下是完整 SMTP 對話：');
        }

        // 不論成功失敗都印出完整 SMTP 對話
        CLI::write('===== SMTP DEBUG =====', 'yellow');
        CLI::write($email->printDebugger(['headers']));
    }
}
