<?php
/**
 * GitHub Webhook Handler
 * 接收 GitHub push 事件並觸發部署
 */

// 設定密鑰 (需與 GitHub Webhook 設定相同)
$secret = 'your_webhook_secret_here';  // TODO: 請更換為您的密鑰

// 取得 GitHub 簽名和 payload
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$payload = file_get_contents('php://input');

// 驗證簽名
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
