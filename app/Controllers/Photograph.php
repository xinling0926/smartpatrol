<?php

namespace App\Controllers;

/**
 * Photograph Controller - 照片管理
 */
class Photograph extends AdminController
{
    protected $pad07Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper(['language', 'text']);
        $this->pad07Model = model('Pad07Model');
    }

    public function index(): string
    {
        $option = [];
        $sort = 'pad0707 desc';
        $pageSize = $this->getPageSize();
        $totalRows = $this->pad07Model->countBy($option);

        $data = [];
        if ($data = $this->pad07Model->getBy($option, $pageSize, 0, $sort)) {
            foreach ($data as $key => $val) {
                $data[$key]->sys0103_sys0104 = $val->sys0103 . $val->sys0104;
            }
        }

        $this->data['data'] = $data;
        $this->data['offset'] = 0;
        $this->setPage($totalRows, 1, $pageSize);
        $this->setQueryOption($option);

        $ent10Model = model('Ent10Model');
        $dev01Model = model('Dev01Model');
        $sys01Model = model('Sys01Model');

        $this->data['ent10s'] = $ent10Model->getNameArray('ent1001', 'ent1004', ['' => lang('Photograph.search_select_ent1001_default')], ['ent1007' => 1]);
        $this->data['dev01s'] = $dev01Model->getNameArray('dev0101', 'dev0104', ['' => lang('Photograph.search_select_dev0101_default')], ['dev0106' => 1]);
        $this->data['sys01s'] = $sys01Model->getNameArray('sys0101', 'sys0103,sys0104', ['' => lang('Photograph.search_select_sys0101_default')], ['sys0205' => 1, 'sys0108' => 1]);
        $this->data['dep_opt'] = $ent10Model->getNameArray('ent1001', 'ent1004', 0, ['ent1007' => 1]);
        $this->data['is_admin'] = $this->user->isAdmin;
        $this->data['is_local_admin'] = $this->user->isLocalAdmin;

        return $this->render();
    }

    public function query(int $page = 0): string
    {
        $sort = 'pad0707 desc';

        if ($page) {
            $option = $this->loadQueryOption();
            $this->setQueryOption($option);
        } else {
            $page = 1;
            $option = $this->getQueryOption();

            // Filter out empty values
            $option = array_filter($option, function($value) {
                return $value !== '' && $value !== null;
            });

            $option['*searchTmp'] = [];

            // Handle search field
            if (isset($option['search']) && $option['search']) {
                $option['*searchTmp'][] = "(pad0705 like '%" . $option['search'] . "%')";
            }
            unset($option['search']);

            // Map ent1001 to pad0702
            if (isset($option['ent1001']) && $option['ent1001']) {
                $option['pad0702'] = $option['ent1001'];
            }
            unset($option['ent1001']);

            if (count($option['*searchTmp'])) {
                $option['*search'] = implode(' and ', $option['*searchTmp']);
            }
            unset($option['*searchTmp']);

            $this->setQueryOption($option);
        }

        $pageSize = $this->getPageSize();
        $offset = ($page - 1) * $pageSize;
        $this->data['offset'] = $offset;
        $totalRows = $this->pad07Model->countBy($option);

        $data = [];
        if ($data = $this->pad07Model->getBy($option, $pageSize, $offset, $sort)) {
            foreach ($data as $key => $val) {
                $data[$key]->sys0103_sys0104 = $val->sys0103 . $val->sys0104;
            }
        }

        $this->data['data'] = $data;
        $this->setPage($totalRows, $page, $pageSize);

        $ent10Model = model('Ent10Model');
        $this->data['dep_opt'] = $ent10Model->getNameArray('ent1001', 'ent1004', 0, ['ent1007' => 1]);
        $this->data['is_admin'] = $this->user->isAdmin;
        $this->data['is_local_admin'] = $this->user->isLocalAdmin;

        return $this->render('');
    }

    public function detail(int $pad0701): string
    {
        $result = $this->pad07Model->getBy(['pad0701' => $pad0701], 1, 0, null);
        if ($result && count($result) > 0) {
            $pad07 = $result[0];
            $pad07->sys0103_sys0104 = $pad07->sys0103 . $pad07->sys0104;
            if (isset($pad07->pad0705) && !empty($pad07->pad0705)) {
                $pad07->pad0705 = addParagraphsNew($pad07->pad0705);
            }
            $this->data['data'] = $pad07;
        } else {
            $this->data['data'] = null;
        }

        return $this->render();
    }

    public function delete(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (!$this->user->isAdmin && !$this->user->isLocalAdmin) {
            return $this->ajaxReturn('Permission Denied');
        }

        if ($pad0701 = $this->request->getPost('id')) {
            if ($pad07 = $this->pad07Model->find($pad0701)) {
                $pad0706 = $pad07->pad0706;
                $pad0706Path = dirname($pad0706);
                $pad0706Thumb = 'thumb_' . basename($pad0706);
                @unlink($pad0706Path . '/' . $pad0706Thumb);
                @unlink($pad0706);
            }

            if ($this->pad07Model->delete($pad0701)) {
                return $this->ajaxReturn("OK");
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }

        return $this->ajaxReturn($this->message->output());
    }

    public function getPhotograph(int $pad0701, int $thumbnail = 0): void
    {
        $flag = 0;

        if ($pad07 = $this->pad07Model->find($pad0701)) {
            if (file_exists($pad07->pad0706)) {
                $baseDir = dirname($pad07->pad0706);
                $baseName = basename($pad07->pad0706);
                $src = $pad07->pad0706;
                $imageMime = image_type_to_mime_type(exif_imagetype($src));

                if ($thumbnail) {
                    $dest = $baseDir . '/thumb_' . $baseName;
                    if (file_exists($dest)) {
                        $flag = 1;
                    } else {
                        if ($this->makeThumb($src, $dest, 100, $imageMime)) {
                            $flag = 1;
                        } else {
                            log_message('error', '縮圖製作發生錯誤');
                        }
                    }
                } else {
                    $dest = $src;
                    $flag = 1;
                }

                if ($flag) {
                    header("Content-type: " . $imageMime);
                    header('Content-Length: ' . filesize($dest));
                    header('Content-Disposition: attachment; filename="' . basename($dest) . '"');
                    readfile($dest);
                    exit;
                }
            }
        }

        header("Content-type: image/jpeg");
        header('Content-Length: 0');
        echo null;
        exit;
    }

    private function makeThumb(string $src, string $dest, int $desiredWidth, string $imageMime): bool
    {
        switch ($imageMime) {
            case "image/jpeg":
                $imageFuncIn = 'imagecreatefromjpeg';
                $imageFuncOut = 'imagejpeg';
                break;
            case "image/png":
                $imageFuncIn = 'imagecreatefrompng';
                $imageFuncOut = 'imagepng';
                break;
            default:
                return false;
        }

        $sourceImage = $imageFuncIn($src);
        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);

        $desiredHeight = (int)floor($height * ($desiredWidth / $width));

        $virtualImage = imagecreatetruecolor($desiredWidth, $desiredHeight);
        imagecopyresampled($virtualImage, $sourceImage, 0, 0, 0, 0, $desiredWidth, $desiredHeight, $width, $height);

        return $imageFuncOut($virtualImage, $dest);
    }

    public function linkageByEnt1001(int $ent1001 = 0): void
    {
        $ent10Model = model('Ent10Model');
        $sys01Model = model('Sys01Model');

        $ent10Id = $ent10Model->getSubDepartmentId($ent1001);
        $ent10Id = empty($ent10Id) ? (string)$ent1001 : $ent1001 . ',' . $ent10Id;
        $sys01s = $sys01Model->getNameArray('sys0101', 'sys0103,sys0104', null, ['sys0205' => 1, 'sys0108' => 1, '*sys0110' => "sys0110 in ({$ent10Id})"]);

        echo json_encode(['sys01s' => $sys01s]);
    }
}
