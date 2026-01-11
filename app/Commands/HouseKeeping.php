<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use ZipArchive;

/**
 * HouseKeeping Command - 系統記錄歸檔
 */
class HouseKeeping extends BaseCommand
{
    protected $group       = 'Patrol';
    protected $name        = 'patrol:house-keeping';
    protected $description = '系統記錄歸檔清理';
    protected $usage       = 'patrol:house-keeping';

    protected array $directories;
    protected $setting;

    public function run(array $params): void
    {
        $this->setting = service('setting');
        $this->directories = [
            'applog' => WRITEPATH . 'logs',
            'data_temp' => ROOTPATH . 'data/temp',
            'pad0203_zip' => ROOTPATH . 'data/pad0203/zip',
            'pad0304_zip' => ROOTPATH . 'data/pad0304/zip',
        ];

        log_message('info', '[' . static::class . '] 開始執行系統記錄歸檔');

        $houseKeeping = $this->setting->item('house_keeping');
        if (empty($houseKeeping)) {
            $houseKeeping = '-7 days';
        }

        $mdatetime = strtotime(date('Y-m-d 23:59:59', strtotime($houseKeeping)));
        $archiveName = date('Y_M', $mdatetime) . '.zip';

        foreach ($this->directories as $type => $dir) {
            if (!is_dir($dir)) {
                CLI::write("目錄不存在: {$dir}", 'yellow');
                continue;
            }

            log_message('info', '[' . static::class . '] start ' . $type . ' to ' . $archiveName);

            $historyDir = $dir . '/history';
            if (!is_dir($historyDir)) {
                if (!mkdir($historyDir, 0777, true)) {
                    log_message('info', '[' . static::class . '] cannot create <' . $historyDir . '>');
                    CLI::write("無法建立目錄: {$historyDir}", 'red');
                    continue;
                }
            }

            $files = match ($type) {
                'applog' => glob($dir . '/log*.php'),
                'data_temp', 'pad0203_zip', 'pad0304_zip' => glob($dir . '/*.zip'),
                default => [],
            };

            if (is_array($files) && count($files)) {
                usort($files, fn($x, $y) => filemtime($x) < filemtime($y) ? 1 : -1);

                $items = [];
                foreach ($files as $file) {
                    if (filemtime($file) < $mdatetime) {
                        $items[] = $file;
                    }
                }

                if (count($items)) {
                    $archive = $historyDir . '/' . $archiveName;
                    $zip = new ZipArchive();

                    if ($zip->open($archive, ZipArchive::CREATE) !== true) {
                        log_message('info', '[' . static::class . '] cannot open <' . $archive . '>');
                        CLI::write("無法開啟壓縮檔: {$archive}", 'red');
                        continue;
                    }

                    foreach ($items as $item) {
                        $zip->addFile($item, basename($item));
                    }

                    $logData = [
                        'type' => $type,
                        'dir' => $dir,
                        'numfiles' => $zip->numFiles,
                        'status' => $zip->status,
                    ];
                    $zip->close();

                    foreach ($items as $item) {
                        @unlink($item);
                    }

                    log_message('info', '[' . static::class . '] ' . json_encode($logData));
                    CLI::write("歸檔完成: {$type} - {$logData['numfiles']} 檔案", 'green');
                }
            }

            log_message('info', '[' . static::class . '] end ' . $type . ' to ' . $archiveName);
        }

        log_message('info', '[' . static::class . '] 結束執行系統記錄歸檔');
        CLI::write('系統記錄歸檔完成', 'green');
    }
}
