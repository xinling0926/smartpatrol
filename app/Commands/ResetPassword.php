<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * ResetPassword Command - 重設使用者密碼
 */
class ResetPassword extends BaseCommand
{
    protected $group       = 'Auth';
    protected $name        = 'auth:reset-password';
    protected $description = '重設使用者密碼';
    protected $usage       = 'auth:reset-password <identity> <new_password>';
    protected $arguments   = [
        'identity'     => '使用者帳號 (sys0102)',
        'new_password' => '新密碼',
    ];

    public function run(array $params): void
    {
        $identity = $params[0] ?? null;
        $newPassword = $params[1] ?? null;

        // Interactive mode if no parameters
        if (empty($identity)) {
            $identity = CLI::prompt('請輸入使用者帳號 (sys0102)');
        }

        if (empty($newPassword)) {
            $newPassword = CLI::prompt('請輸入新密碼');
        }

        if (empty($identity) || empty($newPassword)) {
            CLI::error('帳號和密碼不能為空');
            return;
        }

        // Load database
        $db = \Config\Database::connect();

        // Load RBAC config
        $rbacConfig = config('Rbac');
        $identityColumn = $rbacConfig->identity ?? 'sys0102';
        $hashMethod = $rbacConfig->hashMethod ?? 'sha1';
        $saltLength = $rbacConfig->saltLength ?? 22;

        // Find user
        $user = $db->table('sys01')
            ->where($identityColumn, $identity)
            ->get()
            ->getRow();

        if (!$user) {
            CLI::error("找不到使用者: {$identity}");
            return;
        }

        $userName = ($user->sys0103 ?? '') . ($user->sys0104 ?? '');
        CLI::write("找到使用者: {$userName} (ID: {$user->sys0101})", 'green');
        CLI::write("目前雜湊方法: {$hashMethod}");

        // Generate salt
        $salt = bin2hex(random_bytes($saltLength / 2));

        // Hash password
        switch (strtolower($hashMethod)) {
            case 'sha1':
                $hash = sha1($salt . $newPassword);
                break;
            case 'password_default':
                $hash = password_hash($salt . $newPassword, PASSWORD_DEFAULT);
                break;
            case 'password_bcrypt':
            default:
                $hash = password_hash($salt . $newPassword, PASSWORD_BCRYPT);
                break;
        }

        // Update password
        $result = $db->table('sys01')
            ->where('sys0101', $user->sys0101)
            ->update([
                'sys0105' => $hash,
                'sys0106' => $salt,
                'sys0113' => date('Y-m-d H:i:s'),
                'sys0114' => null,
                'sys0115' => null,
            ]);

        if ($result) {
            CLI::write('密碼已成功更新！', 'green');
            CLI::write("帳號: {$identity}");
            CLI::write("新密碼: {$newPassword}");
            CLI::write("雜湊方法: {$hashMethod}");
        } else {
            CLI::error('密碼更新失敗');
        }
    }
}
