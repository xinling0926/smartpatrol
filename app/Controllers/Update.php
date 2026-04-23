<?php

namespace App\Controllers;

/**
 * Update Controller - 系統程式更新
 *
 * 透過上傳 zip 檔案自動部署新版程式
 */
class Update extends AdminController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper('language');
    }

    public function index(): string
    {
        return $this->render();
    }

    /**
     * 接收上傳的 zip 檔案並執行更新
     */
    public function upload(): \CodeIgniter\HTTP\ResponseInterface
    {
        @set_time_limit(300);

        $file = $this->request->getFile('zipfile');
        if (!$file || !$file->isValid()) {
            return $this->ajaxReturn('ERR', ['description' => '檔案上傳失敗']);
        }

        if ($file->getClientExtension() !== 'zip') {
            return $this->ajaxReturn('ERR', ['description' => '只接受 .zip 檔案']);
        }

        $log = [];
        $tmpDir = WRITEPATH . 'update_' . date('YmdHis');
        $backupDir = WRITEPATH . 'backup_' . date('YmdHis');

        try {
            // 1. 移動檔案到暫存位置
            $zipPath = $tmpDir . '.zip';
            mkdir($tmpDir, 0755, true);
            $file->move(dirname($zipPath), basename($zipPath));
            $log[] = "✓ 檔案已上傳: " . round(filesize($zipPath) / 1024 / 1024, 2) . " MB";

            // 2. 解壓縮
            $zip = new \ZipArchive();
            if ($zip->open($zipPath) !== true) {
                throw new \Exception('無法開啟 zip 檔案');
            }
            $zip->extractTo($tmpDir);
            $zip->close();
            $log[] = "✓ 解壓縮完成";

            // 3. 找出 app/ 和 public/ 目錄
            $sourceApp = $this->findDir($tmpDir, 'app');
            $sourcePublic = $this->findDir($tmpDir, 'public');
            if (!$sourceApp) {
                throw new \Exception('zip 中找不到 app/ 目錄');
            }

            // 4. 備份目前版本
            mkdir($backupDir, 0755, true);
            $this->copyDir(APPPATH, $backupDir . '/app');
            if ($sourcePublic) {
                $this->copyDir(FCPATH, $backupDir . '/public');
            }
            $log[] = "✓ 備份完成: " . $backupDir;

            // 5. 覆蓋檔案
            $this->copyDir($sourceApp, APPPATH);
            $log[] = "✓ app/ 已更新";
            if ($sourcePublic) {
                $this->copyDir($sourcePublic, FCPATH);
                $log[] = "✓ public/ 已更新";
            }

            // 6. 清除快取
            if (is_dir(WRITEPATH . 'cache')) {
                $this->clearDir(WRITEPATH . 'cache');
                $log[] = "✓ 快取已清除";
            }

            // 7. 清理暫存
            $this->removeDir($tmpDir);
            @unlink($zipPath);
            $log[] = "✓ 暫存檔案已清理";

            return $this->ajaxReturn('OK', ['log' => $log, 'backup' => $backupDir]);

        } catch (\Exception $e) {
            $log[] = "✗ 錯誤: " . $e->getMessage();
            return $this->ajaxReturn('ERR', ['description' => $e->getMessage(), 'log' => $log]);
        }
    }

    /**
     * 在目錄中尋找指定子目錄（支援巢狀如 release/app/）
     */
    private function findDir(string $base, string $name): ?string
    {
        if (is_dir($base . '/' . $name)) {
            return $base . '/' . $name;
        }
        $items = scandir($base);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $base . '/' . $item;
            if (is_dir($path) && is_dir($path . '/' . $name)) {
                return $path . '/' . $name;
            }
        }
        return null;
    }

    /**
     * 遞迴複製目錄
     */
    private function copyDir(string $src, string $dst): void
    {
        if (!is_dir($dst)) {
            mkdir($dst, 0755, true);
        }
        $items = scandir($src);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $srcPath = $src . '/' . $item;
            $dstPath = $dst . '/' . $item;
            if (is_dir($srcPath)) {
                $this->copyDir($srcPath, $dstPath);
            } else {
                copy($srcPath, $dstPath);
            }
        }
    }

    /**
     * 遞迴刪除目錄
     */
    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) return;
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $this->removeDir($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }

    /**
     * 清空目錄內容（保留目錄）
     */
    private function clearDir(string $dir): void
    {
        if (!is_dir($dir)) return;
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..' || $item === '.gitkeep') continue;
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $this->removeDir($path);
            } else {
                @unlink($path);
            }
        }
    }
}
