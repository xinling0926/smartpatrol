<?php

namespace App\Controllers;

/**
 * SystemSetting Controller - 系統設定管理
 */
class SystemSetting extends AdminController
{
    protected $commonModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper('language');
        $this->commonModel = model('CommonModel');
        $this->commonModel->setTableName('sys10');
    }

    public function index(): string
    {
        $pageSize = $this->getPageSize();
        $option = [];
        $this->data['data'] = $this->commonModel->getBy($option, $pageSize);
        $totalRows = $this->commonModel->countBy($option);
        $this->setPage($totalRows, 1, $pageSize);

        return $this->render();
    }

    public function query(int $page = 0): string
    {
        if ($page) {
            $option = $this->loadQueryOption();
        } else {
            $page = 1;
            $option = $this->getQueryOption();
        }

        $pageSize = $this->getPageSize();
        $offset = ($page - 1) * $pageSize;
        $option = [];
        $this->data['data'] = $this->commonModel->getBy($option, $pageSize, $offset);
        $totalRows = $this->commonModel->countBy($option);
        $this->setPage($totalRows, $page, $pageSize);

        return $this->render();
    }

    public function detail(int $id): string
    {
        $this->data['data'] = $this->commonModel->find($id);
        return $this->render();
    }

    public function edit(int $id = 0): string|\CodeIgniter\HTTP\ResponseInterface
    {
        if ($this->request->is('post')) {
            $postData = $this->request->getPost();

            if (!isset($postData['system_setting_function'])) {
                $postData['system_setting_function'] = '';
            }

            $rules = [
                'sys1002' => 'required',
            ];

            if ($this->validate($rules)) {
                // Check for duplicate
                if (!$this->sys1002Check($postData['sys1002'])) {
                    return $this->ajaxReturn(lang('SystemSetting.sys1002_duplicate'));
                }

                if (empty($postData['sys1001'])) {
                    if ($newId = $this->commonModel->insert($postData)) {
                        return $this->ajaxReturn('OK', ['id' => $newId, 'title' => $postData['sys1002']]);
                    }
                } else {
                    if ($this->commonModel->save($postData)) {
                        return $this->ajaxReturn('OK', ['id' => $id, 'title' => $postData['sys1002']]);
                    }
                }
            }

            return $this->ajaxReturn($this->message->output());
        }

        if ($id) {
            $this->data['data'] = $this->commonModel->find($id);
            $this->data['data']->system_setting_function = '';
        } else {
            $this->data['data'] = null;
        }

        return $this->render();
    }

    protected function sys1002Check(string $sys1002): bool
    {
        $postId = $this->request->getPost('sys1001');
        if (empty($postId)) {
            $data = $this->commonModel->getNameArray('sys1001', 'sys1002', [], ['sys1002' => $sys1002], null);
            return count($data) === 0;
        }
        return !empty($sys1002);
    }

    public function delete(): \CodeIgniter\HTTP\ResponseInterface
    {
        if ($id = $this->request->getPost('id')) {
            if ($this->commonModel->delete($id)) {
                return $this->ajaxReturn('OK');
            }
        }
        return $this->ajaxReturn('ERR');
    }

    public function androidMan(): string
    {
        $pageSize = $this->getPageSize();
        $option = ['*search' => "sys1002 like 'app_%'"];
        $this->data['data'] = $this->commonModel->getBy($option);
        $totalRows = $this->commonModel->countBy($option);
        $this->setPage($totalRows, 1, $pageSize);

        return $this->render('main', 'systemsetting/android_man');
    }

    public function androidManEdit(): string|\CodeIgniter\HTTP\ResponseInterface
    {
        if ($this->request->is('post')) {
            return $this->ajaxReturn('OK', ['id' => null]);
        }

        return $this->render();
    }

    public function androidManUploadApk(): \CodeIgniter\HTTP\ResponseInterface
    {
        $uploadDir = FCPATH . 'data/app/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Get file from 'files' array (jQuery File Upload uses files[])
        $uploadedFiles = $this->request->getFileMultiple('files');
        $uploadedFile = !empty($uploadedFiles) ? $uploadedFiles[0] : null;

        if ($uploadedFile && $uploadedFile->isValid()) {
            $newName = $uploadedFile->getName();

            // 如果已存在同名檔案，將舊檔案重新命名為備份
            if (file_exists($uploadDir . $newName)) {
                $pathInfo = pathinfo($newName);
                $baseName = $pathInfo['filename'];
                $extension = $pathInfo['extension'] ?? 'apk';
                $timestamp = date('Ymd_His');
                $backupName = "{$baseName}_backup_{$timestamp}.{$extension}";

                // 確保備份檔名不重複
                $counter = 1;
                while (file_exists($uploadDir . $backupName)) {
                    $backupName = "{$baseName}_backup_{$timestamp}_{$counter}.{$extension}";
                    $counter++;
                }

                rename($uploadDir . $newName, $uploadDir . $backupName);
                log_message('info', "APK backup created: {$backupName}");
            }

            try {
                $uploadedFile->move($uploadDir, $newName);
            } catch (\Exception $e) {
                return $this->response
                    ->setContentType('application/json')
                    ->setBody(json_encode(['files' => [['error' => $e->getMessage()]]]));
            }

            $dataSet = [
                'app_filesize' => filesize($uploadDir . $newName),
                'app_download_url' => 'data/app/' . $newName,
                'app_version' => '1.0',
                'app_version_num' => 1,
            ];

            // Parse APK version info
            $apkInfo = $this->parseApkVersion($uploadDir . $newName);
            if ($apkInfo) {
                if (!empty($apkInfo['versionName'])) {
                    $dataSet['app_version'] = $apkInfo['versionName'];
                }
                if (!empty($apkInfo['versionCode'])) {
                    $dataSet['app_version_num'] = $apkInfo['versionCode'];
                }
            }

            // Update database
            foreach ($dataSet as $key => $value) {
                $data = $this->commonModel->getBy(['sys1002' => $key], 1);
                if ($data) {
                    $data->sys1003 = $value;
                    $this->commonModel->save($data);
                    $this->setting->saveCache($key, $value);
                }
            }

            return $this->response
                ->setContentType('application/json')
                ->setBody(json_encode(['files' => [['name' => $newName, 'url' => base_url('data/app/' . $newName), 'size' => $dataSet['app_filesize']]]]));
        } else {
            // Return error message
            $error = $uploadedFile ? $uploadedFile->getErrorString() : 'No file uploaded';
            return $this->response
                ->setContentType('application/json')
                ->setBody(json_encode(['files' => [['error' => $error]]]));
        }
    }

    /**
     * Parse APK file to extract version information
     */
    protected function parseApkVersion(string $apkPath): ?array
    {
        if (!file_exists($apkPath)) {
            return null;
        }

        $result = [
            'versionCode' => null,
            'versionName' => null,
        ];

        // Use php-apk-parser library
        if (class_exists('\ApkParser\Parser')) {
            try {
                $apk = new \ApkParser\Parser($apkPath);
                $manifest = $apk->getManifest();
                $result['versionCode'] = $manifest->getVersionCode();
                $result['versionName'] = $manifest->getVersionName();
                return $result;
            } catch (\Throwable $e) {
                // 解析失敗（含 ZipArchive 暫存目錄不可寫的 ValueError）不應中斷上傳，
                // 版本資訊留空，由管理員後台手動填寫
                log_message('error', 'APK Parse Error: ' . $e->getMessage());
            }
        }

        return $result;
    }
}
