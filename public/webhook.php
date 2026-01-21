<?php
/**
 * GitHub Webhook Handler
 * 接收 GitHub push 事件並觸發部署
 */

$secret = '883784c2eef562cd3d97712c2314a6de2a27a9cab7bb62ce587668d64e577f95';

$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$payload = file_get_contents('php://input');

$hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);
if (!hash_equals($hash, $signature)) {
    http_response_code(403);
    file_put_contents(
        dirname(__DIR__) . '/writable/logs/webhook.log',
        date('Y-m-d H:i:s') . " - Invalid signature\n",
        FILE_APPEND
    );
    die('Invalid signature');
}

// 取得事件類型
$event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';

// 記錄請求
$logMessage = date('Y-m-d H:i:s') . " - Event: {$event}";

// 只處理 push 事件
if ($event === 'push') {
    // 寫入旗標檔案，由 cron 或 systemd 執行實際部署
    $flagFile = dirname(__DIR__) . '/writable/deploy.flag';
    file_put_contents($flagFile, date('Y-m-d H:i:s'));
    $logMessage .= " - Deploy flag created";
    echo "Deploy scheduled";
} elseif ($event === 'ping') {
    // GitHub 測試 ping
    $logMessage .= " - Ping received, webhook is working!";
    echo "Pong! Webhook is configured correctly.";
} else {
    $logMessage .= " - Ignored (not a push event)";
    echo "Event ignored: {$event}";
}

// 記錄到 log 檔案
file_put_contents(
    dirname(__DIR__) . '/writable/logs/webhook.log',
    $logMessage . "\n",
    FILE_APPEND
);
