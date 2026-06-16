<?php

namespace App\Controllers;

/**
 * LogViewer - 透過瀏覽器檢視 CI4 log 檔案
 * 只給管理員使用
 */
class LogViewer extends AdminController
{
    public function index(): string
    {
        $logDir = WRITEPATH . 'logs/';
        $files = [];
        if (is_dir($logDir)) {
            $items = scandir($logDir, SCANDIR_SORT_DESCENDING);
            foreach ($items as $item) {
                if (strpos($item, 'log-') === 0 && substr($item, -4) === '.log') {
                    $files[] = [
                        'name' => $item,
                        'size' => round(filesize($logDir . $item) / 1024, 1),
                        'mtime' => date('Y-m-d H:i:s', filemtime($logDir . $item)),
                    ];
                }
            }
        }

        $output = '<html><head><meta charset="utf-8"><title>Log Viewer</title>';
        $output .= '<style>body{font-family:monospace;padding:20px;}a{margin-right:10px;}pre{background:#f4f4f4;padding:10px;overflow:auto;max-height:80vh;}.crit{color:red;font-weight:bold;}</style>';
        $output .= '</head><body><h2>Log Files</h2>';
        foreach ($files as $f) {
            $output .= sprintf(
                '<a href="?file=%s">%s</a> (%s KB, %s)<br>',
                urlencode($f['name']),
                htmlspecialchars($f['name']),
                $f['size'],
                $f['mtime']
            );
        }

        $file = $this->request->getGet('file');
        if ($file) {
            $file = basename($file);
            $path = $logDir . $file;
            if (file_exists($path)) {
                $lines = $this->request->getGet('lines') ?? 200;
                $content = shell_exec_safe($path, (int)$lines);
                if ($content === null) {
                    $content = file_get_contents($path);
                    $lines = explode("\n", $content);
                    $content = implode("\n", array_slice($lines, -200));
                }
                $content = htmlspecialchars($content);
                $content = preg_replace('/(CRITICAL[^\n]+)/', '<span class="crit">$1</span>', $content);
                $output .= '<h3>' . htmlspecialchars($file) . '</h3>';
                $output .= '<pre>' . $content . '</pre>';
            }
        }

        $output .= '</body></html>';
        return $output;
    }
}

if (!function_exists('shell_exec_safe')) {
    function shell_exec_safe(string $path, int $lines): ?string
    {
        if (function_exists('shell_exec') && !ini_get('safe_mode')) {
            return @shell_exec('tail -n ' . $lines . ' ' . escapeshellarg($path));
        }
        return null;
    }
}
