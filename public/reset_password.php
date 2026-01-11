<?php
/**
 * 密碼重設工具 - 使用後請刪除此檔案
 * Password Reset Tool - DELETE THIS FILE AFTER USE
 */

// Path to the front controller
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

// Load our paths config file
require FCPATH . '../app/Config/Paths.php';

$paths = new Config\Paths();

// Location of the framework bootstrap file
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

// Load environment settings from .env files
require_once SYSTEMPATH . 'Config/DotEnv.php';
(new CodeIgniter\Config\DotEnv(ROOTPATH))->load();

// Define ENVIRONMENT
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', env('CI_ENVIRONMENT', 'production'));
}

// Initialize CodeIgniter
$app = Config\Services::codeigniter();
$app->initialize();

// Load RBAC config
$rbacConfig = config('Rbac');
$identityColumn = $rbacConfig->identity ?? 'sys0102';
$hashMethod = $rbacConfig->hashMethod ?? 'sha1';
$saltLength = $rbacConfig->saltLength ?? 22;

$db = \Config\Database::connect();
$message = '';
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identity = trim($_POST['identity'] ?? '');
    $newPassword = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if (empty($identity) || empty($newPassword)) {
        $message = '帳號和密碼不能為空';
    } elseif ($newPassword !== $confirmPassword) {
        $message = '兩次輸入的密碼不一致';
    } else {
        // Find user
        $user = $db->table('sys01')
            ->where($identityColumn, $identity)
            ->get()
            ->getRow();

        if (!$user) {
            $message = "找不到使用者: {$identity}";
        } else {
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
                $success = true;
                $userName = ($user->sys0103 ?? '') . ($user->sys0104 ?? '');
                $message = "密碼已成功更新！使用者: {$userName} (ID: {$user->sys0101})";
            } else {
                $message = '密碼更新失敗';
            }
        }
    }
}

// Get all users for reference
$users = [];
try {
    $users = $db->table('sys01')
        ->select("sys0101, {$identityColumn} as identity, CONCAT(sys0103, sys0104) as name, sys0108 as status")
        ->orderBy('sys0101')
        ->get()
        ->getResult();
} catch (Exception $e) {
    $message = '資料庫連線失敗: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>密碼重設工具</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .warning { background: #fff3cd; border: 1px solid #ffc107; color: #856404; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .success { background: #d4edda; border: 1px solid #28a745; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #f8d7da; border: 1px solid #dc3545; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background: #007bff; color: white; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .status-active { color: green; }
        .status-inactive { color: red; }
        .info { background: #e7f3ff; border: 1px solid #b6d4fe; color: #084298; padding: 10px; border-radius: 4px; margin-bottom: 20px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>密碼重設工具</h1>

        <div class="warning">
            <strong>警告：</strong>此工具用於重設使用者密碼。使用完畢後請立即刪除此檔案！<br>
            檔案位置：<code>/public/reset_password.php</code>
        </div>

        <div class="info">
            <strong>系統設定：</strong><br>
            身份欄位：<?= htmlspecialchars($identityColumn) ?><br>
            雜湊方法：<?= htmlspecialchars($hashMethod) ?>
        </div>

        <?php if ($message): ?>
            <div class="<?= $success ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="identity">使用者帳號 (<?= htmlspecialchars($identityColumn) ?>)：</label>
                <input type="text" id="identity" name="identity" required value="<?= htmlspecialchars($_POST['identity'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="new_password">新密碼：</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">確認密碼：</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">重設密碼</button>
        </form>

        <?php if (!empty($users)): ?>
        <h2>使用者列表</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>帳號</th>
                    <th>姓名</th>
                    <th>狀態</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user->sys0101) ?></td>
                    <td><?= htmlspecialchars($user->identity) ?></td>
                    <td><?= htmlspecialchars($user->name ?? '') ?></td>
                    <td class="<?= $user->status ? 'status-active' : 'status-inactive' ?>">
                        <?= $user->status ? '啟用' : '停用' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</body>
</html>
