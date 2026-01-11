<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * AnnualCheckup Command - 歲修停車檢查
 */
class AnnualCheckup extends BaseCommand
{
    protected $group       = 'Patrol';
    protected $name        = 'patrol:annual-checkup';
    protected $description = '檢查歲修停車設定到期';
    protected $usage       = 'patrol:annual-checkup';

    protected int $now;
    protected $fmd30Model;

    public function run(array $params): void
    {
        $this->now = time();
        $this->fmd30Model = model('Fmd30Model');

        log_message('info', '[' . static::class . '] 開始檢查歲修停車設定');

        $fmd30s = $this->fmd30Model->getBy(['fmd3006' => '2']);
        if (!$fmd30s) {
            log_message('info', '[' . static::class . '] 無歲修停車報表設定！');
            CLI::write('無歲修停車報表設定', 'yellow');
            return;
        }

        $fmd30Closed = [];

        foreach ($fmd30s as $fmd30) {
            $fmd30Full = $this->fmd30Model->getWithFmd01andEnt10($fmd30->fmd3001);
            if ($fmd30Full) {
                $fmd3004Real = strtotime($fmd30Full->fmd3004 . " 23:59:59");

                if ($this->now > $fmd3004Real) {
                    // 檢查是否有正在歲修編輯狀態的紀錄
                    $fmd30Editing = $this->fmd30Model->getBy([
                        'fmd3009' => $fmd30Full->fmd3009,
                        'fmd3006' => '3'
                    ], 1);

                    if ($fmd30Editing) {
                        $fmd30Closed[] = json_encode($fmd30Editing);
                        $this->fmd30Model->delete($fmd30Editing->fmd3001);
                    }

                    // 將到期的歲修紀錄 fmd3006 改為 4
                    $fmd30Closed[] = json_encode($fmd30Full);
                    $this->fmd30Model->updateFmd3006($fmd30Full->fmd3001, 4);
                }
            }
        }

        if (count($fmd30Closed)) {
            log_message('info', '[' . static::class . '] 到期的歲修紀錄: ' . json_encode($fmd30Closed, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            CLI::write('到期的歲修紀錄: ' . count($fmd30Closed) . ' 筆', 'green');
        } else {
            log_message('info', '[' . static::class . '] 無到期的歲修紀錄');
            CLI::write('無到期的歲修紀錄', 'yellow');
        }

        log_message('info', '[' . static::class . '] 結束檢查歲修停車設定');
    }
}
