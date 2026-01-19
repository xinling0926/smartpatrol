<?php
/**
 * 密碼驗證測試腳本
 * 用於驗證 CI4 的密碼驗證邏輯是否與舊版 CI3 一致
 *
 * 使用方式：
 * php test_password.php <帳號> <密碼>
 * 或瀏覽器訪問：/patrol/test_password.php?account=xxx&password=xxx
 */

// 引入 CI4 啟動檔案
require_once __DIR__ . '/../vendor/autoload.php';

// 載入 .env
$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

// 初始化 CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

// 取得參數
if (php_sapi_name() === 'cli') {
    $account = $argv[1] ?? null;
    $password = $argv[2] ?? null;
} else {
    $account = $_GET['account'] ?? null;
    $password = $_GET['password'] ?? null;
    header('Content-Type: text/plain; charset=utf-8');
}

if (!$account) {
    echo "使用方式:\n";
    echo "  CLI: php test_password.php <帳號> <密碼>\n";
    echo "  URL: /patrol/test_password.php?account=xxx&password=xxx\n\n";
    echo "範例:\n";
    echo "  php test_password.php admin 123456\n";
    exit;
}

// 連接資料庫
$db = \Config\Database::connect();

// 載入 RBAC 配置
$rbacConfig = new \Config\Rbac();
$hashMethod = $rbacConfig->hashMethod ?? 'sha1';
$identityColumn = $rbacConfig->identity ?? 'sys0102';

echo "=== 密碼驗證測試 ===\n\n";
echo "哈希方法: {$hashMethod}\n";
echo "帳號欄位: {$identityColumn}\n";
echo "測試帳號: {$account}\n\n";

// 查詢用戶
$user = $db->table('sys01')
    ->where($identityColumn, $account)
    ->get()
    ->getRow();

if (!$user) {
    echo "錯誤: 找不到帳號 '{$account}'\n";
    exit(1);
}

echo "=== 用戶資料 ===\n";
echo "ID: {$user->sys0101}\n";
echo "帳號: {$user->{$identityColumn}}\n";
echo "姓名: " . ($user->sys0103 ?? '') . ($user->sys0104 ?? '') . "\n";
echo "儲存的哈希: {$user->sys0105}\n";
echo "儲存的鹽值: " . ($user->sys0106 ?? '(空)') . "\n\n";

if ($password) {
    echo "=== 密碼驗證 ===\n";
    echo "輸入的密碼: {$password}\n";

    $salt = $user->sys0106 ?? '';

    switch (strtolower($hashMethod)) {
        case 'sha1':
            $calculated = sha1($salt . $password);
            $isValid = ($calculated === $user->sys0105);
            echo "計算方式: sha1(salt + password)\n";
            break;
        case 'password_default':
        case 'password_bcrypt':
            $calculated = '(bcrypt 無法顯示計算結果)';
            $isValid = password_verify($salt . $password, $user->sys0105);
            echo "計算方式: password_verify(salt + password, hash)\n";
            break;
        default:
            echo "錯誤: 不支援的哈希方法 '{$hashMethod}'\n";
            exit(1);
    }

    echo "計算的哈希: {$calculated}\n";
    echo "驗證結果: " . ($isValid ? "✓ 密碼正確" : "✗ 密碼錯誤") . "\n";
} else {
    echo "提示: 加上 password 參數可以測試密碼驗證\n";
}
