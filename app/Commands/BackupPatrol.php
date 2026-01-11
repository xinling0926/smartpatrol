<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * BackupPatrol Command - FTP 備份傳送
 */
class BackupPatrol extends BaseCommand
{
    protected $group       = 'Patrol';
    protected $name        = 'patrol:backup';
    protected $description = '傳送備份檔案至 FTP 伺服器';
    protected $usage       = 'patrol:backup <filename>';
    protected $arguments   = [
        'filename' => '要傳送的備份檔案名稱',
    ];

    protected $setting;

    public function run(array $params): void
    {
        $this->setting = service('setting');

        $fileName = $params[0] ?? null;
        if (!$fileName) {
            CLI::error('請提供備份檔案名稱');
            return;
        }

        log_message('info', '[' . static::class . '] 開始傳送備份檔案');

        $sourceFile = '/var/tmp/patrol_backup/' . $fileName;
        $destinationFile = basename($fileName);

        if (!file_exists($sourceFile)) {
            log_message('info', '[' . static::class . '] 取消 FTP 備份，原因是 ' . $sourceFile . ' 不存在');
            CLI::error('檔案不存在: ' . $sourceFile);
            return;
        }

        $errors = [];
        if ($this->setting->item('ftp_host') === null) {
            $errors[] = 'the key ftp_host not set';
        }
        if ($this->setting->item('ftp_user') === null) {
            $errors[] = 'the key ftp_user not set';
        }
        if ($this->setting->item('ftp_pass') === null) {
            $errors[] = 'the key ftp_pass not set';
        }

        if (count($errors)) {
            log_message('info', '[' . static::class . '] 取消 FTP 備份，原因是 ' . json_encode($errors));
            CLI::error('FTP 設定不完整: ' . implode(', ', $errors));
            return;
        }

        $ftpHost = $this->setting->item('ftp_host');
        $ftpUser = $this->setting->item('ftp_user');
        $ftpPass = $this->setting->item('ftp_pass');

        $connId = ftp_connect($ftpHost);
        if (!$connId) {
            log_message('info', '[' . static::class . '] FTP connection has failed! Host: ' . $ftpHost);
            CLI::error('FTP 連線失敗: ' . $ftpHost);
            return;
        }

        $loginResult = ftp_login($connId, $ftpUser, $ftpPass);
        if (!$loginResult) {
            log_message('info', '[' . static::class . '] FTP login has failed! User: ' . $ftpUser);
            CLI::error('FTP 登入失敗: ' . $ftpUser);
            ftp_close($connId);
            return;
        }

        $upload = ftp_put($connId, $destinationFile, $sourceFile, FTP_BINARY);

        if (!$upload) {
            log_message('info', '[' . static::class . '] FTP upload has failed!');
            CLI::error('FTP 上傳失敗');
        } else {
            CLI::write('Success! uploaded ' . $sourceFile . ' to ' . $ftpHost . ' as ' . $destinationFile, 'green');
        }

        ftp_close($connId);

        log_message('info', '[' . static::class . '] 結束傳送備份檔案');
    }
}
