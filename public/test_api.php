<?php
/**
 * 測試 API - 用於測試 chunked encoding 問題
 * 直接訪問: /patrol/test_api.php
 */

$output = json_encode([
    'status' => 'success',
    'info' => '測試回應',
    'time' => date('Y-m-d H:i:s')
]);

// 清除所有輸出緩衝區
while (ob_get_level() > 0) {
    ob_end_clean();
}

// 強制 HTTP/1.0
header('HTTP/1.0 200 OK');
header('Content-Type: application/json; charset=utf-8');
header('Content-Length: ' . strlen($output));
header('Connection: close');

echo $output;
exit;
