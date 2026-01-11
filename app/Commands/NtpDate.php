<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * NtpDate Command - 取得 NTP 伺服器位址
 */
class NtpDate extends BaseCommand
{
    protected $group       = 'Patrol';
    protected $name        = 'patrol:ntpdate';
    protected $description = '取得 NTP 時間伺服器位址';
    protected $usage       = 'patrol:ntpdate';

    protected $setting;

    public function run(array $params): void
    {
        $this->setting = service('setting');

        $timeServers = $this->setting->item('time_servers');
        if ($timeServers) {
            $hostData = explode(',', $timeServers);
            $host = $hostData[mt_rand(0, count($hostData) - 1)];
            if ($host) {
                CLI::write(trim($host));
            }
        } else {
            CLI::write('No time servers configured', 'yellow');
        }
    }
}
