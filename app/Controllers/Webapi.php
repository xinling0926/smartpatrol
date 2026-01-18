<?php

namespace App\Controllers;

use App\Libraries\User;

/**
 * Webapi Controller - API 接口
 * status: authorized | success | fail
 */
class Webapi extends BaseController
{
    protected $sys01Model;
    protected $dev01Model;
    protected $ent02Model;
    protected $ent10Model;
    protected User $user;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper('language');

        $this->sys01Model = model('Sys01Model');
        $this->dev01Model = model('Dev01Model');
        $this->ent02Model = model('Ent02Model');
        $this->ent10Model = model('Ent10Model');
        $this->user = new User();

        $this->response->setHeader('Content-Type', 'application/json');
    }

    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->response->setJSON(['status' => 'ok', 'message' => 'Webapi is running']);
    }

    /**
     * 取得請求參數（支援 POST form-data 和 JSON body）
     * CI3 使用 $this->input->post()，CI4 需要分別處理 form-data 和 JSON
     */
    private function getParam(string $key, $default = '')
    {
        // 先嘗試 POST form-data
        $value = $this->request->getPost($key);
        if ($value !== null) {
            return is_string($value) ? trim($value) : $value;
        }

        // 再嘗試 JSON body
        $json = $this->request->getJSON(true);
        if (is_array($json) && isset($json[$key])) {
            $val = $json[$key];
            return is_string($val) ? trim($val) : $val;
        }

        // 最後嘗試 raw input (for x-www-form-urlencoded)
        $raw = $this->request->getRawInput();
        if (is_array($raw) && isset($raw[$key])) {
            $val = $raw[$key];
            return is_string($val) ? trim($val) : $val;
        }

        return $default;
    }

    public function register(): \CodeIgniter\HTTP\ResponseInterface
    {
        $json = ['status' => 'fail', 'info' => '', 'ent0105' => ''];

        $ent0102 = $this->getParam('ent0102', '');
        $dev0105 = $this->getParam('dev0105', '');
        $imei = $this->getParam('os_imei', '');
        $dev0107 = $this->getParam('dev0107', '');
        $version = $this->getParam('version', '');

        // Debug log
        log_message('debug', '[Webapi] register - ent0102: ' . $ent0102 . ', dev0105: ' . $dev0105);

        $version = match ($version) {
            '1' => 'v1.0',
            '2' => 'v1.1',
            '3' => 'v1.2',
            '4' => 'v1.3',
            default => 'v' . $version,
        };

        // Device hardware parameters
        $hardware = [
            'display' => $this->getParam('os_display', ''),
            'release' => $this->getParam('os_release', ''),
            'model' => $this->getParam('os_model', ''),
            'androidid' => $this->getParam('os_id', ''),
            'sim' => $this->getParam('os_sim', ''),
            'serial' => $this->getParam('os_serial', ''),
            'imei' => $this->getParam('os_imei', ''),
        ];
        $hardwareData = json_encode($hardware);

        if (empty($ent0102)) {
            $json['info'] = lang('Webapi.ent0102_empty');
        } elseif (empty($dev0105)) {
            $json['info'] = lang('Webapi.dev0105_empty');
        } else {
            // Check enterprise
            $sql = "select ent01.*,(select count(*)+1 from dev01 where dev0102=ent0101) as dev0104,(select count(*) from dev01 where dev0102=ent0101 and dev0106=1) as dev0106 from ent01 where ent0102=?";
            $ent01 = $this->db->query($sql, [$ent0102])->getRow();

            if (!$ent01) {
                $json['info'] = lang('Webapi.ent10_not_exists');
            } elseif ((int)$ent01->ent0104 === 0) {
                $json['info'] = lang('Webapi.ent10_disable');
            } else {
                // Check device
                $dev01 = $this->db->query("select * from dev01 where dev0105=? and dev0102=?", [$dev0105, $ent01->ent0101])->getRow();

                if (!$dev01) {
                    // Create new device
                    $dev01 = (object)[
                        'dev0102' => $ent01->ent0101,
                        'dev0103' => '',
                        'dev0104' => sprintf('%04d', $ent01->dev0104),
                        'dev0105' => $dev0105,
                        'dev0106' => 0,
                        'dev0107' => $dev0107,
                        'dev0111' => $version,
                        'dev0112' => $imei,
                        'dev0113' => $hardwareData,
                    ];

                    $dev01->dev0101 = $this->dev01Model->insert($dev01);
                    if ($dev01->dev0101) {
                        $json['dev01'] = $this->dev01Model->find($dev01->dev0101);
                    } else {
                        $json['info'] = lang('Webapi.dev01_register_fail');
                    }
                } else {
                    // Update existing device
                    if (!empty($dev0107) && $dev01->dev0107 !== $dev0107) {
                        $this->db->query("update dev01 set dev0107=? where dev0101=?", [$dev0107, $dev01->dev0101]);
                        $dev01->dev0107 = $dev0107;
                    }
                    if (!empty($version) && $dev01->dev0111 !== $version) {
                        $this->db->query("update dev01 set dev0111=? where dev0101=?", [$version, $dev01->dev0101]);
                        $dev01->dev0111 = $version;
                    }
                    if (!empty($hardwareData) && $dev01->dev0113 !== $hardwareData) {
                        $this->db->query("update dev01 set dev0113=? where dev0101=?", [$hardwareData, $dev01->dev0101]);
                        $dev01->dev0113 = $hardwareData;
                    }
                    $json['dev01'] = $dev01;
                }

                // Set status based on device state
                switch ((int)$dev01->dev0106) {
                    case 0:
                        $json['info'] = sprintf('%s，%s', lang('Webapi.dev01_not_enable'), lang('Webapi.contact_admin'));
                        break;
                    case 1:
                        $json['status'] = 'success';
                        $json['info'] = lang('Webapi.dev01_register_success');
                        break;
                    case 2:
                        $json['info'] = lang('Webapi.dev01_disable');
                        break;
                    default:
                        $json['info'] = lang('Webapi.dev01_unknown_dev0106');
                }

                // Get related data
                $json['ent10'] = $this->db->query("select ent1001,ent1002,ent1004,ent1005,ent1007,ent10z2,ent10z4 from ent10 where ent1002=? and ent1007=1", [$dev01->dev0102])->getResult();
                $json['sys01'] = $this->db->query("select sys0101,sys0102,sys0103,sys0104,sys0105,sys0106,sys0107,sys0108,sys0110,sys0111,sys0117,sys0121,sys01z2,sys01z4 from sys01,ent10 where sys0110=ent1001 and ent1002=? and sys0108=1", [$dev01->dev0102])->getResult();
                $json['fmd10'] = $this->db->query("select * from fmd10 where fmd1012=? and fmd1010=1", [$dev01->dev0102])->getResult();

                $json['ent01'] = $ent01;
                $json['ent0105'] = base_url($ent01->ent0105);
            }
        }

        return $this->response->setJSON($json);
    }

    public function login(): \CodeIgniter\HTTP\ResponseInterface
    {
        $json = ['status' => 'fail', 'info' => lang('Webapi.user_login_fail'), 'pad03_enable' => '1'];

        $path = FCPATH . 'data/temp';
        if (!file_exists($path) && !mkdir($path, 0777, true)) {
            $json['info'] = lang('Webapi.mkdir_fail');
            return $this->response->setJSON($json);
        }

        $identity = $this->getParam('user', '');
        $password = $this->getParam('pass', '');
        $dev0101 = (int)$this->getParam('dev0101', 0);
        $dev0105 = $this->getParam('dev0105', '');
        $dev0107 = $this->getParam('dev0107', '');

        if ($dev0107 === 'null') {
            $dev0107 = '';
        }

        // Incremental sync timestamps (CI3 compatible)
        $fmd01z4 = $this->getParam('fmd01z4', '');
        $fmd10z4 = $this->getParam('fmd10z4', '');
        $sys01z4 = $this->getParam('sys01z4', '');
        $ent10z4 = $this->getParam('ent10z4', '');
        if ($fmd01z4 === 'null') $fmd01z4 = '';
        if ($fmd10z4 === 'null') $fmd10z4 = '';
        if ($sys01z4 === 'null') $sys01z4 = '';
        if ($ent10z4 === 'null') $ent10z4 = '';

        $version = $this->getParam('version', '');
        $version = match ($version) {
            '1' => 'v1.0',
            '2' => 'v1.1',
            '3' => 'v1.2',
            '4' => 'v1.3',
            default => 'v' . $version,
        };

        // Hardware info collection (CI3 compatible)
        $hardware = [
            'display' => $this->getParam('os_display', ''),
            'release' => $this->getParam('os_release', ''),
            'model' => $this->getParam('os_model', ''),
            'androidid' => $this->getParam('os_id', ''),
            'sim' => $this->getParam('os_sim', ''),
            'serial' => $this->getParam('os_serial', ''),
            'imei' => $this->getParam('os_imei', ''),
        ];
        $hardwareData = json_encode($hardware);

        // Debug log
        log_message('debug', '[Webapi] login - user: ' . $identity . ', dev0101: ' . $dev0101);

        if (empty($identity)) {
            $json['info'] = lang('Webapi.sys0102_empty');
        } elseif (empty($password)) {
            $json['info'] = lang('Webapi.sys0105_empty');
        } else {
            // Verify device
            $dev01s = $this->dev01Model->getBy(['dev0101' => $dev0101, 'dev0105' => $dev0105]);

            if (!$dev01s || count($dev01s) === 0) {
                $json['info'] = lang('Webapi.dev01_not_exists');
                $json['dev0101'] = $dev0101;
                $json['dev0105'] = $dev0105;
            } elseif ((int)$dev01s[0]->dev0106 === 0) {
                $json['info'] = sprintf('%s，%s', lang('Webapi.dev01_not_enable'), lang('Webapi.cannot_login'));
            } elseif ((int)$dev01s[0]->dev0106 === 2) {
                $json['status'] = 'authorized';
                $json['info'] = sprintf('%s，%s', lang('Webapi.dev01_disable'), lang('Webapi.cannot_login'));
            } elseif (isset($dev01s[0]->ent0104) && (int)$dev01s[0]->ent0104 === 0) {
                $json['status'] = 'forbidden';
                $json['info'] = sprintf('%s，%s', lang('Webapi.ent10_disable'), lang('Webapi.cannot_login'));
            } else {
                $dev01 = $dev01s[0];

                // Update push token
                if (!empty($dev0107) && $dev01->dev0107 !== $dev0107) {
                    $this->db->query("update dev01 set dev0107=? where dev0101=?", [$dev0107, $dev01->dev0101]);
                    $dev01->dev0107 = $dev0107;
                }

                $json['pad03_enable'] = $this->ent02Model->getLicenseCount('smart_patrol_of2', $dev01->dev0102) > 0 ? 1 : 0;
                $json['pad04_enable'] = $this->ent02Model->getLicenseCount('smart_patrol_photo', $dev01->dev0102) > 0 ? 1 : 0;

                // Check if locked out
                if ($this->user->isTimeLockedOut($identity)) {
                    $this->user->addLoginLog($identity, 1, 4);
                    $json['info'] = sprintf('%s，%s', lang('Webapi.user_lock'), lang('Webapi.contact_admin'));
                } else {
                    // Verify user
                    $user = $this->sys01Model->checkUserByIdentity($identity);
                    if ($user) {
                        // AD Authentication check (CI3 compatible)
                        $isAuth = false;
                        if (($user->sys0121 ?? '0') === '1') {
                            // User uses AD authentication
                            $isAuth = $this->user->adVerify($identity, $password);
                            // If AD auth passed, sync password to local
                            if ($isAuth) {
                                $this->user->changePassword($user->sys0101, $password);
                            }
                        } else {
                            // Local password authentication
                            $isAuth = $this->user->passwordVerify($user->sys0105, $password, $user->sys0106 ?? '');
                        }

                        if ($isAuth) {
                            if ((int)$user->sys0108 === 0) {
                                // Account not enabled
                                $json['info'] = lang('Webapi.sys01_not_enable');
                                $this->user->addLoginLog($identity, 1, 3, $user->sys0101);
                            } else {
                                // Department restriction check (CI3 compatible)
                                $depId = $this->ent10Model->getSubDepartmentId($dev01->dev0103 ?? '');
                                $depId = empty($depId) ? ($dev01->dev0103 ?? '') : sprintf("%s,%s", $dev01->dev0103, $depId);

                                if ((int)($dev01->dev0103 ?? 0) && !in_array($user->sys0110, explode(",", $depId))) {
                                    // User's department not allowed for this device
                                    $json['info'] = sprintf(lang('Webapi.dev01_format01'), $dev01->ent1004 ?? '');
                                } else {
                                    // Login success
                                    $this->sys01Model->updateLastLogin($user->sys0101);

                                    // Clear login attempts
                                    $this->user->clearLoginAttempts($identity);
                                    $this->user->addLoginLog($identity, 1, 1, $user->sys0101);

                                    // Check user's department in enterprise
                                    $ent10 = $this->db->query("select * from ent10 where ent1001=? and ent1002=?", [$user->sys0110, $dev01->dev0102])->getRow();

                                    if (!$ent10) {
                                        $json['info'] = lang('Webapi.ent01_not_exists');
                                    } else {
                                        // Generate ZIP sync file (CI3 compatible)
                                        $zipResult = $this->generateLoginSyncZip($dev01, $ent10, $depId, $fmd01z4, $fmd10z4, $sys01z4, $ent10z4);

                                        // Settings array (same as CI3)
                                        $set01s = [];
                                        $set01s[] = ['set0102' => 'pad03_enable', 'set0103' => $this->ent02Model->getLicenseCount('smart_patrol_of2', $dev01->dev0102) > 0 ? 1 : 0];
                                        $set01s[] = ['set0102' => 'pad04_enable', 'set0103' => $this->ent02Model->getLicenseCount('smart_patrol_photo', $dev01->dev0102) > 0 ? 1 : 0];
                                        $set01s[] = ['set0102' => 'power_low_alert', 'set0103' => $this->setting->item('power_low_alert') ?? '20'];

                                        $json['status'] = 'success';
                                        $json['info'] = lang('Webapi.user_login_success');
                                        $json['sys0101'] = $user->sys0101;
                                        $json['set01'] = $set01s;
                                        $json['file'] = $zipResult['file'];
                                        $json['filesize'] = $zipResult['filesize'];

                                        // Update device info with hardware data (CI3 compatible)
                                        $this->dev01Model->update($dev0101, [
                                            'dev0109' => date('Y-m-d H:i:s'),
                                            'dev0111' => $version,
                                            'dev0113' => $hardwareData,
                                        ]);
                                    }
                                }
                            }
                        } else {
                            $this->user->increaseLoginAttempts($identity);
                            $json['info'] = lang('Webapi.user_login_fail');
                        }
                    } else {
                        $json['info'] = lang('Webapi.user_not_found');
                    }
                }
            }
        }

        return $this->response->setJSON($json);
    }

    /**
     * Generate ZIP sync file for login (CI3 compatible)
     */
    private function generateLoginSyncZip(object $dev01, object $ent10, string $depId, string $fmd01z4, string $fmd10z4, string $sys01z4, string $ent10z4): array
    {
        $result = ['file' => '', 'filesize' => 0];
        $listdb = [];
        $maxsize = 500;

        // Department filter
        $depFilter = '';
        if ($dev01->dev0103 ?? '') {
            $depFilter = sprintf(" and fmd0102 in (%s)", $depId);
        }

        // Query forms (fmd01)
        if (!empty($fmd01z4)) {
            $sql = sprintf("select fmd0101,fmd0102,fmd0103,fmd0104,fmd0105,fmd0107,fmd0108,fmd0110,if(fmd01z4 is null,fmd01z2,fmd01z4) as fmd01z4
                            from fmd01,ent10
                            where fmd0102=ent1001 and fmd0108 in (2,3,4)
                            and ent1002=%s %s and (fmd01z2>'%s' or fmd01z4>'%s') order by fmd0103,fmd0102", $ent10->ent1002, $depFilter, $fmd01z4, $fmd01z4);
        } else {
            $sql = sprintf("select fmd0101,fmd0102,fmd0103,fmd0104,fmd0105,fmd0107,fmd0108,fmd0110,if(fmd01z4 is null,fmd01z2,fmd01z4) as fmd01z4
                            from fmd01,ent10
                            where fmd0102=ent1001 and fmd0108 in (2,3,4) and ent1002=%s %s order by fmd0103,fmd0102", $ent10->ent1002, $depFilter);
        }
        $fmd01s = $this->db->query($sql)->getResult();

        $path = sprintf("data/temp/%s_%s.zip", $dev01->dev0101, date("YmdHis"));
        $zipfile = FCPATH . $path;
        $zip = new \ZipArchive();
        $zip->open($zipfile, \ZipArchive::CREATE);

        if ($fmd01s && count($fmd01s)) {
            foreach ($fmd01s as $v1) {
                $files = [];

                if ($v1->fmd0108 != 2) {
                    // Delete form
                    $sqlStr = sprintf('delete from fmd01 where fmd0101=%s;delete from fmd02 where fmd0202=%s;delete from fmd03 where fmd0302=%s;delete from fmd04 where fmd0402=%s;delete from fmd05 where fmd0502=%s;delete from fmd06 where fmd0602=%s;delete from fmd07 where fmd0702=%s;delete from fmd08 where fmd0802=%s;delete from fmd09 where fmd0902=%s', $v1->fmd0101, $v1->fmd0101, $v1->fmd0101, $v1->fmd0101, $v1->fmd0101, $v1->fmd0101, $v1->fmd0101, $v1->fmd0101, $v1->fmd0101);
                    $filename = sprintf('cache/fmd0101_%s.txt', $v1->fmd0101);
                    $zip->addFromString($filename, $sqlStr);
                    $files[] = $filename;
                } else {
                    // fmd02
                    $fmd02s = $this->db->query(sprintf("select fmd0201,fmd0202,fmd0203,fmd0204,fmd0205,fmd0206 from fmd02 where fmd0202=%s", $v1->fmd0101))->getResult();
                    if ($fmd02s && count($fmd02s)) {
                        $index = 0;
                        for ($i = 0; $i < count($fmd02s); $i += $maxsize) {
                            $sqlStr = 'insert into fmd02 (fmd0201,fmd0202,fmd0203,fmd0204,fmd0205,fmd0206) values ';
                            $tmp = array_slice($fmd02s, $i, $maxsize);
                            foreach ($tmp as $v2) {
                                $sqlStr .= sprintf("(%s,%s,%s,'%s','%s','%s'),", $v2->fmd0201, $v2->fmd0202, $v2->fmd0203, $v2->fmd0204, $v2->fmd0205, $v2->fmd0206);
                            }
                            $sqlStr = rtrim($sqlStr, ",") . ";";
                            $filename = sprintf('cache/fmd02_%s_%s.txt', $v1->fmd0101, $index);
                            $zip->addFromString($filename, $sqlStr);
                            $files[] = $filename;
                            $index++;
                        }
                    }

                    // fmd03
                    $fmd03s = $this->db->query(sprintf("select fmd0301,fmd0302,fmd0304 from fmd03 where fmd0302=%s", $v1->fmd0101))->getResult();
                    if ($fmd03s && count($fmd03s)) {
                        $index = 0;
                        for ($i = 0; $i < count($fmd03s); $i += $maxsize) {
                            $sqlStr = 'insert into fmd03 (fmd0301,fmd0302,fmd0304) values ';
                            $tmp = array_slice($fmd03s, $i, $maxsize);
                            foreach ($tmp as $v2) {
                                $sqlStr .= sprintf("(%s,%s,'%s'),", $v2->fmd0301, $v2->fmd0302, $v2->fmd0304);
                            }
                            $sqlStr = rtrim($sqlStr, ",") . ";";
                            $filename = sprintf('cache/fmd03_%s_%s.txt', $v1->fmd0101, $index);
                            $zip->addFromString($filename, $sqlStr);
                            $files[] = $filename;
                            $index++;
                        }
                    }

                    // fmd04
                    $fmd04s = $this->db->query(sprintf("select fmd0401,fmd0402,fmd0403,fmd0404,fmd0405,fmd0409 from fmd04 where fmd0402=%s", $v1->fmd0101))->getResult();
                    if ($fmd04s && count($fmd04s)) {
                        $index = 0;
                        for ($i = 0; $i < count($fmd04s); $i += $maxsize) {
                            $sqlStr = 'insert into fmd04 (fmd0401,fmd0402,fmd0403,fmd0404,fmd0405,fmd0409) values ';
                            $tmp = array_slice($fmd04s, $i, $maxsize);
                            foreach ($tmp as $v2) {
                                $sqlStr .= sprintf("(%s,%s,'%s','%s','%s','%s'),", $v2->fmd0401, $v2->fmd0402, $v2->fmd0403, $v2->fmd0404, $v2->fmd0405, $v2->fmd0409);
                            }
                            $sqlStr = rtrim($sqlStr, ",") . ";";
                            $filename = sprintf('cache/fmd04_%s_%s.txt', $v1->fmd0101, $index);
                            $zip->addFromString($filename, $sqlStr);
                            $files[] = $filename;
                            $index++;
                        }
                    }

                    // fmd05
                    $fmd05s = $this->db->query(sprintf("select fmd0501,fmd0502,fmd0503,fmd0504 from fmd05 where fmd0502=%s", $v1->fmd0101))->getResult();
                    if ($fmd05s && count($fmd05s)) {
                        $index = 0;
                        for ($i = 0; $i < count($fmd05s); $i += $maxsize) {
                            $sqlStr = 'insert into fmd05 (fmd0501,fmd0502,fmd0503,fmd0504) values ';
                            $tmp = array_slice($fmd05s, $i, $maxsize);
                            foreach ($tmp as $v2) {
                                $sqlStr .= sprintf("(%s,%s,'%s','%s'),", $v2->fmd0501, $v2->fmd0502, $v2->fmd0503, $v2->fmd0504);
                            }
                            $sqlStr = rtrim($sqlStr, ",") . ";";
                            $filename = sprintf('cache/fmd05_%s_%s.txt', $v1->fmd0101, $index);
                            $zip->addFromString($filename, $sqlStr);
                            $files[] = $filename;
                            $index++;
                        }
                    }

                    // fmd06
                    $fmd06s = $this->db->query(sprintf("select fmd0601,fmd0602,fmd0603,fmd0604,fmd0606,fmd0607,fmd0608,fmd0609,fmd0610,fmd0611,fmd0612,fmd0613,fmd0614,fmd0616,fmd0618,fmd0619 from fmd06 where fmd0602=%s", $v1->fmd0101))->getResult();
                    if ($fmd06s && count($fmd06s)) {
                        $index = 0;
                        for ($i = 0; $i < count($fmd06s); $i += $maxsize) {
                            $sqlStr = 'insert into fmd06 (fmd0601,fmd0602,fmd0603,fmd0604,fmd0606,fmd0607,fmd0608,fmd0609,fmd0610,fmd0611,fmd0612,fmd0613,fmd0614,fmd0616,fmd0618,fmd0619) values ';
                            $tmp = array_slice($fmd06s, $i, $maxsize);
                            foreach ($tmp as $v2) {
                                $sqlStr .= sprintf("(%s,%s,%s,%s,'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',%s),", $v2->fmd0601, $v2->fmd0602, $v2->fmd0603, $v2->fmd0604, $v2->fmd0606, $v2->fmd0607, $v2->fmd0608, $v2->fmd0609, $v2->fmd0610, $v2->fmd0611, $v2->fmd0612, $v2->fmd0613, $v2->fmd0614, $v2->fmd0616, $v2->fmd0618, $v2->fmd0619);
                            }
                            $sqlStr = rtrim($sqlStr, ",") . ";";
                            $filename = sprintf('cache/fmd06_%s_%s.txt', $v1->fmd0101, $index);
                            $zip->addFromString($filename, $sqlStr);
                            $files[] = $filename;
                            $index++;
                        }
                    }

                    // fmd07
                    $fmd07s = $this->db->query(sprintf("select fmd0701,fmd0702,fmd0703,fmd0704,fmd0706,fmd0708 from fmd07 where fmd0702=%s", $v1->fmd0101))->getResult();
                    if ($fmd07s && count($fmd07s)) {
                        $index = 0;
                        for ($i = 0; $i < count($fmd07s); $i += $maxsize) {
                            $sqlStr = 'insert into fmd07 (fmd0701,fmd0702,fmd0703,fmd0704,fmd0706,fmd0708) values ';
                            $tmp = array_slice($fmd07s, $i, $maxsize);
                            foreach ($tmp as $v2) {
                                $sqlStr .= sprintf("(%s,%s,'%s','%s','%s','%s'),", $v2->fmd0701, $v2->fmd0702, $v2->fmd0703, $v2->fmd0704, $v2->fmd0706, $v2->fmd0708);
                            }
                            $sqlStr = rtrim($sqlStr, ",") . ";";
                            $filename = sprintf('cache/fmd07_%s_%s.txt', $v1->fmd0101, $index);
                            $zip->addFromString($filename, $sqlStr);
                            $files[] = $filename;
                            $index++;
                        }
                    }

                    // fmd08
                    $fmd08s = $this->db->query(sprintf("select fmd0801,fmd0802,fmd0803,fmd0804,fmd0805 from fmd08 where fmd0802=%s", $v1->fmd0101))->getResult();
                    if ($fmd08s && count($fmd08s)) {
                        $index = 0;
                        for ($i = 0; $i < count($fmd08s); $i += $maxsize) {
                            $sqlStr = 'insert into fmd08 (fmd0801,fmd0802,fmd0803,fmd0804,fmd0805) values ';
                            $tmp = array_slice($fmd08s, $i, $maxsize);
                            foreach ($tmp as $v2) {
                                $sqlStr .= sprintf("(%s,%s,'%s','%s','%s'),", $v2->fmd0801, $v2->fmd0802, $v2->fmd0803, $v2->fmd0804, $v2->fmd0805);
                            }
                            $sqlStr = rtrim($sqlStr, ",") . ";";
                            $filename = sprintf('cache/fmd08_%s_%s.txt', $v1->fmd0101, $index);
                            $zip->addFromString($filename, $sqlStr);
                            $files[] = $filename;
                            $index++;
                        }
                    }

                    // fmd09
                    $fmd09s = $this->db->query(sprintf("select fmd0901,fmd0902,fmd0903,fmd0904,fmd0905,fmd0906,fmd0908,fmd0909,fmd0910,fmd0911 from fmd09 where fmd0902=%s", $v1->fmd0101))->getResult();
                    if ($fmd09s && count($fmd09s)) {
                        $index = 0;
                        for ($i = 0; $i < count($fmd09s); $i += $maxsize) {
                            $sqlStr = 'insert into fmd09 (fmd0901,fmd0902,fmd0903,fmd0904,fmd0905,fmd0908,fmd0909,fmd0910,fmd0911,fmd0906) values ';
                            $tmp = array_slice($fmd09s, $i, $maxsize);
                            foreach ($tmp as $v2) {
                                $sqlStr .= sprintf("(%s,%s,'%s','%s','%s','%s','%s','%s','%s','%s'),", $v2->fmd0901, $v2->fmd0902, $v2->fmd0903, $v2->fmd0904, $v2->fmd0905, $v2->fmd0908, $v2->fmd0909, $v2->fmd0910, $v2->fmd0911, $v2->fmd0906);
                            }
                            $sqlStr = rtrim($sqlStr, ",") . ";";
                            $filename = sprintf('cache/fmd09_%s_%s.txt', $v1->fmd0101, $index);
                            $zip->addFromString($filename, $sqlStr);
                            $files[] = $filename;
                            $index++;
                        }
                    }

                    // fmd01 record itself
                    $filename = sprintf('cache/fmd0101_%s.txt', $v1->fmd0101);
                    $sqlStr = sprintf("insert into fmd01 (fmd0101,fmd0102,fmd0103,fmd0104,fmd0105,fmd0107,fmd0108,fmd0110,fmd01z4) values (%s,%s,'%s','%s',%s,%s,%s,%s,'%s');", $v1->fmd0101, $v1->fmd0102, $v1->fmd0103, $v1->fmd0104, $v1->fmd0105, $v1->fmd0107, $v1->fmd0108, $v1->fmd0110, $v1->fmd01z4);
                    $zip->addFromString($filename, $sqlStr);
                    $files[] = $filename;
                }

                $listdb[] = ['title' => $v1->fmd0104 . '_V' . $v1->fmd0107, 'files' => $files];
            }
        }

        // fmd10 - Notification schedules
        if (!empty($fmd10z4)) {
            $sql = sprintf("select fmd1001,fmd1002,fmd1003,fmd1004,fmd1005,fmd1006,fmd1007,fmd1008,fmd1009,fmd1010,fmd1011,fmd1012,if(fmd10z4 is null,fmd10z2,fmd10z4) as fmd10z4 from fmd10 where fmd1012=%s and (fmd10z2>'%s' or fmd10z4>'%s')", $ent10->ent1002, $fmd10z4, $fmd10z4);
        } else {
            $sql = sprintf("select fmd1001,fmd1002,fmd1003,fmd1004,fmd1005,fmd1006,fmd1007,fmd1008,fmd1009,fmd1010,fmd1011,fmd1012,if(fmd10z4 is null,fmd10z2,fmd10z4) as fmd10z4 from fmd10 where fmd1012=%s", $ent10->ent1002);
        }
        $fmd10s = $this->db->query($sql)->getResult();
        if ($fmd10s && count($fmd10s)) {
            $sqlStr = 'insert into fmd10 (fmd1001,fmd1002,fmd1003,fmd1004,fmd1005,fmd1006,fmd1007,fmd1008,fmd1009,fmd1010,fmd1011,fmd1012,fmd10z4) values ';
            $delid = [];
            foreach ($fmd10s as $v1) {
                $delid[] = $v1->fmd1001;
                if ($v1->fmd1010 == 1) {
                    $sqlStr .= sprintf("('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'),", $v1->fmd1001, $v1->fmd1002, $v1->fmd1003, $v1->fmd1004, $v1->fmd1005, $v1->fmd1006, $v1->fmd1007, $v1->fmd1008, $v1->fmd1009, $v1->fmd1010, $v1->fmd1011, $v1->fmd1012, $v1->fmd10z4);
                }
            }
            $sqlStr = rtrim($sqlStr, ",") . ";";

            $filename1 = 'cache/fmd10_delete.txt';
            $zip->addFromString($filename1, sprintf("delete from fmd10 where fmd1001 in (%s);", implode(",", $delid)));

            $filename2 = 'cache/fmd10.txt';
            $zip->addFromString($filename2, $sqlStr);
            $listdb[] = ['title' => lang('Webapi.table_fmd10'), 'files' => [$filename1, $filename2]];
        }

        // sys01 - User accounts
        if (!empty($sys01z4)) {
            $sql = sprintf("select sys0101,sys0102,sys0103,sys0104,sys0105,sys0106,sys0107,sys0108,sys0110,sys0111,sys0117,sys0121,if(sys01z4 is null,sys01z2,sys01z4) as sys01z4 from sys01,ent10 where sys0110=ent1001 and ent1002=%s and (sys01z2>'%s' or sys01z4>'%s')", $ent10->ent1002, $sys01z4, $sys01z4);
        } else {
            $sql = sprintf("select sys0101,sys0102,sys0103,sys0104,sys0105,sys0106,sys0107,sys0108,sys0110,sys0111,sys0117,sys0121,if(sys01z4 is null,sys01z2,sys01z4) as sys01z4 from sys01,ent10 where sys0110=ent1001 and ent1002=%s", $ent10->ent1002);
        }
        $sys01s = $this->db->query($sql)->getResult();
        if ($sys01s && count($sys01s)) {
            $sqlStr = 'insert into sys01 (sys0101,sys0102,sys0103,sys0104,sys0105,sys0106,sys0107,sys0108,sys0110,sys0111,sys0117,sys0121,sys01z4) values ';
            $delid = [];
            foreach ($sys01s as $v1) {
                $delid[] = $v1->sys0101;
                $sqlStr .= sprintf("('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'),", $v1->sys0101, $v1->sys0102, $v1->sys0103, $v1->sys0104, $v1->sys0105, $v1->sys0106, $v1->sys0107, $v1->sys0108, $v1->sys0110, $v1->sys0111, $v1->sys0117, $v1->sys0121, $v1->sys01z4);
            }
            $sqlStr = rtrim($sqlStr, ",") . ";";

            $filename1 = 'cache/sys01_delete.txt';
            $zip->addFromString($filename1, sprintf("delete from sys01 where sys0101 in (%s);", implode(",", $delid)));

            $filename2 = 'cache/sys01.txt';
            $zip->addFromString($filename2, $sqlStr);
            $listdb[] = ['title' => lang('Webapi.table_sys01'), 'files' => [$filename1, $filename2]];
        }

        // ent10 - Departments
        if (!empty($ent10z4)) {
            $sql = sprintf("select ent1001,ent1002,ent1004,ent1005,ent1007,if(ent10z4 is null,ent10z2,ent10z4) as ent10z4 from ent10 where ent1002=%s and (ent10z2>'%s' or ent10z4>'%s')", $ent10->ent1002, $ent10z4, $ent10z4);
        } else {
            $sql = sprintf("select ent1001,ent1002,ent1004,ent1005,ent1007,if(ent10z4 is null,ent10z2,ent10z4) as ent10z4 from ent10 where ent1002=%s", $ent10->ent1002);
        }
        $ent10s = $this->db->query($sql)->getResult();
        if ($ent10s && count($ent10s)) {
            $sqlStr = 'insert into ent10 (ent1001,ent1002,ent1004,ent1005,ent1007,ent10z4) values ';
            $delid = [];
            foreach ($ent10s as $v1) {
                $delid[] = $v1->ent1001;
                $sqlStr .= sprintf("('%s','%s','%s','%s','%s','%s'),", $v1->ent1001, $v1->ent1002, $v1->ent1004, $v1->ent1005, $v1->ent1007, $v1->ent10z4);
            }
            $sqlStr = rtrim($sqlStr, ",") . ";";

            $filename1 = 'cache/ent10_delete.txt';
            $zip->addFromString($filename1, sprintf("delete from ent10 where ent1001 in (%s);", implode(",", $delid)));

            $filename2 = 'cache/ent10.txt';
            $zip->addFromString($filename2, $sqlStr);
            $listdb[] = ['title' => lang('Webapi.table_ent10'), 'files' => [$filename1, $filename2]];
        }

        $zip->addFromString('cache/00000.txt', json_encode($listdb));
        $zip->close();

        if (count($listdb)) {
            $result['file'] = $path;
            $result['filesize'] = filesize($zipfile);
        } else {
            if (file_exists($zipfile)) {
                unlink($zipfile);
            }
        }

        return $result;
    }

    public function logout(): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->response->setJSON(['status' => 'success', 'info' => 'Logged out']);
    }

    public function checkVersion(): \CodeIgniter\HTTP\ResponseInterface
    {
        $json = [
            'app_version' => $this->setting->item('app_version') ?? '1.0',
            'app_version_num' => (int)($this->setting->item('app_version_num') ?? 1),
            'app_download_url' => base_url($this->setting->item('app_download_url') ?? ''),
            'app_filesize' => (int)($this->setting->item('app_filesize') ?? 0),
        ];

        return $this->response->setJSON($json);
    }

    /**
     * 修改密碼
     */
    public function changepass(): \CodeIgniter\HTTP\ResponseInterface
    {
        $json = ['status' => 'fail', 'info' => lang('Webapi.user_fail')];

        $dev0101 = (int)($this->request->getPost('dev0101') ?? 0);
        $dev0105 = trim($this->request->getPost('dev0105') ?? '');
        $sys0101 = (int)($this->request->getPost('sys0101') ?? 0);
        $pass1 = trim($this->request->getPost('pass1') ?? '');
        $pass2 = trim($this->request->getPost('pass2') ?? '');
        $pass3 = trim($this->request->getPost('pass3') ?? '');

        if (!$sys0101) {
            $json['info'] = lang('Webapi.sys0101_empty');
        } elseif (empty($pass1)) {
            $json['info'] = lang('Webapi.sys01_old_pass');
        } elseif (empty($pass2)) {
            $json['info'] = lang('Webapi.sys01_new_pass');
        } elseif ($pass2 !== $pass3) {
            $json['info'] = lang('Webapi.sys01_inconformity');
        } else {
            $user = $this->db->query("select * from sys01 where sys0101=?", [$sys0101])->getRow();
            if (!$user) {
                $json['info'] = lang('Webapi.sys01_not_exists');
            } else {
                if ($this->user->passwordVerify($user->sys0105, $pass1, $user->sys0106)) {
                    if ($this->user->changePassword($sys0101, $pass2)) {
                        $sys01 = $this->sys01Model->find($sys0101);
                        $json['sys0105'] = $sys01->sys0105;
                        $json['sys0106'] = $sys01->sys0106;
                        $json['sys01z3'] = $sys01->sys01z3 ?? '';
                        $json['sys01z4'] = $sys01->sys01z4 ?? '';
                        $json['status'] = 'success';
                        $json['info'] = lang('Webapi.changepass_success');

                        $this->dev01Model->update($dev0101, ['dev0109' => date('Y-m-d H:i:s')]);
                    } else {
                        $json['info'] = lang('Webapi.changepass_fail');
                    }
                } else {
                    $json['info'] = lang('Webapi.sys01_old_pass_error');
                }
            }
        }

        return $this->response->setJSON($json);
    }

    /**
     * 修改頭像
     */
    public function changeface(): \CodeIgniter\HTTP\ResponseInterface
    {
        $json = ['status' => 'fail', 'info' => lang('Webapi.face_upload_fail')];

        $dev0101 = (int)($this->request->getPost('dev0101') ?? 0);
        $sys0101 = (int)($this->request->getPost('sys0101') ?? 0);

        $path = FCPATH . 'data/avatar';
        if (!file_exists($path) && !mkdir($path, 0777, true)) {
            $json['info'] = lang('Webapi.mkdir_fail');
            return $this->response->setJSON($json);
        }

        $path = sprintf('%s/%s/', $path, $sys0101);
        if (!file_exists($path) && !mkdir($path, 0777, true)) {
            $json['info'] = lang('Webapi.mkdir_fail');
            return $this->response->setJSON($json);
        }

        $sys0117 = sprintf('%s.jpg', time());
        $file = $this->request->getFile('file1');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $file->move($path, $sys0117);
            $this->db->query("update sys01 set sys0117=? where sys0101=?", [$sys0117, $sys0101]);
            $json['status'] = 'success';
            $json['info'] = lang('Webapi.face_upload_success');
            $this->dev01Model->update($dev0101, ['dev0109' => date('Y-m-d H:i:s')]);
        } else {
            $json['info'] = lang('Webapi.upload_error' . ($file ? $file->getError() : '4'));
        }

        return $this->response->setJSON($json);
    }

    /**
     * 上傳巡檢記錄 - 單條記錄
     */
    public function addpad01(): \CodeIgniter\HTTP\ResponseInterface
    {
        $pad01Model = model('Pad01Model');
        $json = ['status' => 'fail', 'info' => lang('Webapi.upload_fail')];

        $pad0107 = trim($this->request->getPost('pad0107') ?? '');
        $dev0101 = (int)($this->request->getPost('dev0101') ?? 0);
        $dev0105 = trim($this->request->getPost('dev0105') ?? '');
        $imei = trim($this->request->getPost('os_imei') ?? '');

        $dev01s = $this->dev01Model->getBy(['dev0101' => $dev0101, 'dev0105' => $dev0105]);

        if (!$dev01s || count($dev01s) === 0) {
            $json['info'] = lang('Webapi.dev01_not_exists');
            $json['dev0101'] = $dev0101;
            $json['dev0105'] = $dev0105;
        } elseif ((int)$dev01s[0]->dev0106 !== 1) {
            $json['info'] = lang('Webapi.dev01_not_enable');
        } elseif ((int)$dev01s[0]->ent0104 !== 1) {
            $json['info'] = lang('Webapi.ent10_not_enable');
        } elseif (empty($pad0107) || !json_decode($pad0107)) {
            $json['info'] = lang('Webapi.json_error');
        } else {
            $dev01 = $dev01s[0];

            if (empty($dev01->dev0112) && !empty($imei)) {
                $this->db->query("update dev01 set dev0112=? where dev0101=?", [$imei, $dev01->dev0101]);
            }

            $pad01 = [
                'pad0102' => (int)$this->request->getPost('pad0102'),
                'pad0103' => $dev0101,
                'pad0104' => (int)$this->request->getPost('pad0104'),
                'pad0105' => (int)$this->request->getPost('pad0105'),
                'pad0106' => (int)$this->request->getPost('pad0106'),
                'pad0107' => $pad0107,
                'pad0108' => trim($this->request->getPost('pad0108') ?? ''),
                'pad0109' => trim($this->request->getPost('pad0109') ?? ''),
                'pad0110' => (float)$this->request->getPost('pad0110'),
                'pad0111' => (float)$this->request->getPost('pad0111'),
                'pad0112' => date('Y-m-d H:i:s'),
                'pad0113' => 1,
            ];
            $pad02s = $this->request->getPost('pad02') ?? '';

            // 檢查重複資料
            $row = $this->db->query(
                "select * from pad01 where pad0102=? and pad0103=? and pad0104=? and pad0109=?",
                [$pad01['pad0102'], $pad01['pad0103'], $pad01['pad0104'], $pad01['pad0109']]
            )->getRow();

            if (!$row) {
                $path = FCPATH . 'data/pad0203';
                if (!file_exists($path) && !mkdir($path, 0777, true)) {
                    $json['info'] = lang('Webapi.mkdir_fail');
                    return $this->response->setJSON($json);
                }

                $devPath = $path . '/' . $dev01->dev0101;
                if (!file_exists($devPath) && !mkdir($devPath, 0777, true)) {
                    $json['info'] = lang('Webapi.mkdir_fail');
                    return $this->response->setJSON($json);
                }

                $pad0101 = $pad01Model->insert($pad01);
                if ($pad0101) {
                    // 處理上傳的檔案
                    $files = $this->request->getFiles();
                    foreach ($files as $name => $file) {
                        if ($file->isValid() && !$file->hasMoved()) {
                            $ext = strtolower($file->getExtension());
                            if (in_array($ext, ['jpg', 'png', 'gif'])) {
                                $file->move($devPath, $file->getName());
                            }
                        }
                    }

                    // 處理 pad02
                    if (!empty($pad02s)) {
                        $pad02Data = json_decode($pad02s);
                        if ($pad02Data) {
                            foreach ($pad02Data as $v) {
                                $this->db->query("insert into pad02 (pad0202,pad0203) values (?,?)", [$pad0101, $v]);
                            }
                        }
                    }

                    $json['status'] = 'success';
                    $json['info'] = lang('Webapi.upload_success');
                    $json['pad0101'] = $pad0101;

                    // 同步到第三方 API
                    $this->sendPAD01ToAPI($pad0101, $this->setting->item('pad01_api'));
                } else {
                    $json['info'] = lang('Webapi.write_fail');
                }
            } else {
                $json['status'] = 'success';
                $json['info'] = lang('Webapi.upload_success');
                $json['pad0101'] = $row->pad0101;
            }

            $this->dev01Model->update($dev0101, ['dev0109' => date('Y-m-d H:i:s')]);
        }

        return $this->response->setJSON($json);
    }

    /**
     * 上傳巡檢記錄 - 多條記錄打包
     */
    public function addpad01multi(): \CodeIgniter\HTTP\ResponseInterface
    {
        log_message('info', '[Webapi] addpad01multi start');
        $pad01Model = model('Pad01Model');
        $time = time();
        $api = $this->setting->item('pad01_api');
        $json = ['status' => 'fail', 'info' => lang('Webapi.upload_fail')];

        $dev0101 = (int)($this->request->getPost('dev0101') ?? 0);
        $dev0105 = trim($this->request->getPost('dev0105') ?? '');
        $imei = trim($this->request->getPost('os_imei') ?? '');

        $zipPath = FCPATH . 'data/pad0203/zip';
        if (!file_exists(FCPATH . 'data/pad0203') && !mkdir(FCPATH . 'data/pad0203', 0777, true)) {
            $json['info'] = lang('Webapi.mkdir_fail');
            return $this->response->setJSON($json);
        }
        if (!file_exists($zipPath) && !mkdir($zipPath, 0777, true)) {
            $json['info'] = lang('Webapi.mkdir_fail');
            return $this->response->setJSON($json);
        }

        $dev01s = $this->dev01Model->getBy(['dev0101' => $dev0101, 'dev0105' => $dev0105]);

        if (!$dev01s || count($dev01s) === 0) {
            $json['info'] = lang('Webapi.dev01_not_exists');
            $json['dev0101'] = $dev0101;
            $json['dev0105'] = $dev0105;
            return $this->response->setJSON($json);
        } elseif ((int)$dev01s[0]->dev0106 !== 1) {
            $json['info'] = lang('Webapi.dev01_not_enable');
            return $this->response->setJSON($json);
        } elseif ((int)$dev01s[0]->ent0104 !== 1) {
            $json['info'] = lang('Webapi.ent10_not_enable');
            return $this->response->setJSON($json);
        }

        $dev01 = $dev01s[0];
        if (empty($dev01->dev0112) && !empty($imei)) {
            $this->db->query("update dev01 set dev0112=? where dev0101=?", [$imei, $dev01->dev0101]);
        }

        $file = $this->request->getFile('file1');
        if (!$file || !$file->isValid()) {
            $json['info'] = lang('Webapi.upload_error' . ($file ? $file->getError() : '4'));
            return $this->response->setJSON($json);
        }

        $zipfile = sprintf('%s/%s_%s.zip', $zipPath, $dev0101, $time);
        $file->move($zipPath, basename($zipfile));

        // 解壓縮並處理
        $zip = new \ZipArchive();
        if ($zip->open($zipfile) === true) {
            $extractPath = FCPATH . 'data/pad0203/' . $dev01->dev0101;
            if (!file_exists($extractPath) && !mkdir($extractPath, 0777, true)) {
                $json['info'] = lang('Webapi.mkdir_fail');
                return $this->response->setJSON($json);
            }

            $zip->extractTo($extractPath);

            // 處理 data.txt
            $dataFile = $extractPath . '/data.txt';
            if (file_exists($dataFile)) {
                $dataContent = file_get_contents($dataFile);
                $records = json_decode($dataContent, true);

                if ($records) {
                    $successCount = 0;
                    foreach ($records as $record) {
                        $pad01 = [
                            'pad0102' => (int)($record['pad0102'] ?? 0),
                            'pad0103' => $dev0101,
                            'pad0104' => (int)($record['pad0104'] ?? 0),
                            'pad0105' => (int)($record['pad0105'] ?? 0),
                            'pad0106' => (int)($record['pad0106'] ?? 0),
                            'pad0107' => $record['pad0107'] ?? '',
                            'pad0108' => $record['pad0108'] ?? '',
                            'pad0109' => $record['pad0109'] ?? '',
                            'pad0110' => (float)($record['pad0110'] ?? 0),
                            'pad0111' => (float)($record['pad0111'] ?? 0),
                            'pad0112' => date('Y-m-d H:i:s'),
                            'pad0113' => 1,
                        ];

                        // 檢查重複
                        $exists = $this->db->query(
                            "select pad0101 from pad01 where pad0102=? and pad0103=? and pad0104=? and pad0109=?",
                            [$pad01['pad0102'], $pad01['pad0103'], $pad01['pad0104'], $pad01['pad0109']]
                        )->getRow();

                        if (!$exists) {
                            $pad0101 = $pad01Model->insert($pad01);
                            if ($pad0101) {
                                $successCount++;
                                $this->sendPAD01ToAPI($pad0101, $api);
                            }
                        }
                    }

                    $json['status'] = 'success';
                    $json['info'] = sprintf(lang('Webapi.upload_multi_success'), $successCount);
                }

                unlink($dataFile);
            }

            $zip->close();
        }

        $this->dev01Model->update($dev0101, ['dev0109' => date('Y-m-d H:i:s')]);
        log_message('info', '[Webapi] addpad01multi end');

        return $this->response->setJSON($json);
    }

    /**
     * 上傳報修記錄 - 單條記錄
     */
    public function addrepair(): \CodeIgniter\HTTP\ResponseInterface
    {
        $pad03Model = model('Pad03Model');
        $json = ['status' => 'fail', 'info' => lang('Webapi.upload_fail')];

        $dev0101 = (int)($this->request->getPost('dev0101') ?? 0);
        $dev0105 = trim($this->request->getPost('dev0105') ?? '');
        $sys0101 = (int)($this->request->getPost('sys0101') ?? 0);
        $title = trim($this->request->getPost('title') ?? '');
        $info = trim($this->request->getPost('info') ?? '');
        $time = trim($this->request->getPost('time') ?? '');
        $pad0305 = trim($this->request->getPost('weizhi') ?? '');

        if (empty($title)) {
            $json['info'] = lang('Webapi.pad0303_empty');
        } elseif (empty($info)) {
            $json['info'] = lang('Webapi.pad0304_empty');
        } else {
            $dev01s = $this->dev01Model->getBy(['dev0101' => $dev0101, 'dev0105' => $dev0105]);

            if (!$dev01s || count($dev01s) === 0) {
                $json['info'] = lang('Webapi.dev01_not_exists');
                $json['dev0101'] = $dev0101;
                $json['dev0105'] = $dev0105;
            } elseif ((int)$dev01s[0]->dev0106 !== 1) {
                $json['info'] = lang('Webapi.dev01_not_enable');
            } elseif ((int)$dev01s[0]->ent0104 !== 1) {
                $json['info'] = lang('Webapi.ent10_not_enable');
            } else {
                $dev01 = $dev01s[0];

                // 檢查重複
                $row = $this->db->query("select * from pad03 where pad0302=? and pad03z2=?", [$dev0101, $time])->getRow();

                if ($row) {
                    $json['pad0301'] = $row->pad0301;
                } else {
                    $path = FCPATH . 'data/pad0403';
                    if (!file_exists($path) && !mkdir($path, 0777, true)) {
                        $json['info'] = lang('Webapi.mkdir_fail');
                        return $this->response->setJSON($json);
                    }

                    $devPath = $path . '/' . $dev01->dev0104;
                    if (!file_exists($devPath) && !mkdir($devPath, 0777, true)) {
                        $json['info'] = lang('Webapi.mkdir_fail');
                        return $this->response->setJSON($json);
                    }

                    $pad03 = [
                        'pad0302' => $dev0101,
                        'pad0303' => $title,
                        'pad0304' => $info,
                        'pad0305' => $pad0305,
                        'pad03z1' => $sys0101,
                        'pad03z2' => $time,
                        'pad03z3' => $sys0101,
                        'pad03z4' => date('Y-m-d H:i:s'),
                    ];

                    $pad0301 = $pad03Model->insert($pad03);
                    if ($pad0301) {
                        // 處理上傳檔案
                        $files = $this->request->getFiles();
                        foreach ($files as $file) {
                            if ($file->isValid() && !$file->hasMoved()) {
                                $ext = strtolower($file->getExtension());
                                if (in_array($ext, ['jpg', 'png', 'gif'])) {
                                    $fileName = $file->getName();
                                    $file->move($devPath, $fileName);
                                    $pad0403 = sprintf('data/pad0403/%s/%s', $dev01->dev0104, $fileName);
                                    $this->db->query("insert into pad04 (pad0402,pad0403) values (?,?)", [$pad0301, $pad0403]);
                                }
                            }
                        }

                        $json['pad0301'] = $pad0301;
                        $this->sendPAD03ToAPI($pad0301);
                        $json['info'] = lang('Webapi.upload_success');
                        $json['status'] = 'success';
                    } else {
                        $json['info'] = lang('Webapi.write_fail');
                    }
                }

                $this->dev01Model->update($dev0101, ['dev0109' => date('Y-m-d H:i:s')]);
            }
        }

        return $this->response->setJSON($json);
    }

    /**
     * 獲取伺服器時間
     */
    public function now(): \CodeIgniter\HTTP\ResponseInterface
    {
        $dev0101 = (int)($this->request->getPost('dev0101') ?? 0);

        $json = ['status' => 'success', 'info' => '', 'time' => date('Y-m-d H:i:s')];

        if ($dev0101) {
            $this->dev01Model->update($dev0101, ['dev0109' => date('Y-m-d H:i:s')]);
        }

        return $this->response->setJSON($json);
    }

    /**
     * 上傳資料庫備份
     */
    public function updatabase(): \CodeIgniter\HTTP\ResponseInterface
    {
        log_message('debug', 'call from myphone');
        $json = ['status' => 'fail', 'info' => lang('Webapi.upload_fail')];

        $dev0101 = (int)($this->request->getPost('dev0101') ?? 0);

        $path = FCPATH . 'data/databases';
        if (!file_exists($path) && !mkdir($path, 0777, true)) {
            $json['info'] = lang('Webapi.mkdir_fail');
            return $this->response->setJSON($json);
        }

        $devPath = $path . '/' . $dev0101;
        if (!file_exists($devPath) && !mkdir($devPath, 0777, true)) {
            $json['info'] = lang('Webapi.mkdir_fail');
            return $this->response->setJSON($json);
        }

        $file = $this->request->getFile('file1');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = time() . '.db';
            $file->move($devPath, $fileName);
            $json = ['status' => 'success', 'info' => lang('Webapi.upload_success')];
            $this->dev01Model->update($dev0101, ['dev0109' => date('Y-m-d H:i:s')]);
        } else {
            $json['info'] = lang('Webapi.upload_error' . ($file ? $file->getError() : '4'));
        }

        return $this->response->setJSON($json);
    }

    /**
     * 獲取APP版本資訊
     */
    public function version(): \CodeIgniter\HTTP\ResponseInterface
    {
        $dev0101 = (int)($this->request->getPost('dev0101') ?? 0);

        $file = $this->setting->item('app_download_url') ?? '';
        $size = $this->setting->item('app_filesize') ?? 0;

        if (strpos($file, 'http://') === false && file_exists(FCPATH . $file)) {
            $size = filesize(FCPATH . $file);
            $file = base_url($file);
        }

        $version = [
            'package' => 'cn.com.duit.smartpatrol',
            'versionCode' => $this->setting->item('app_version_num') ?? 1,
            'versionName' => $this->setting->item('app_version') ?? '1.0',
            'file' => $file,
            'filesize' => $size,
        ];

        if ($dev0101) {
            $this->dev01Model->update($dev0101, ['dev0109' => date('Y-m-d H:i:s')]);
        }

        return $this->response->setJSON($version);
    }

    /**
     * 根據巡檢獲取用戶
     */
    public function getUserByPatrol(): \CodeIgniter\HTTP\ResponseInterface
    {
        $depts = $this->db->query(
            "SELECT ent1001 AS id, ent1004 AS `name`, sys0107 AS spv_mail, CONCAT(sys0103,sys0104) AS spv_name FROM ent10 AS a LEFT JOIN sys01 AS b ON a.ent1006=b.sys0101 WHERE ent1007 = 1"
        )->getResult();

        $users = $this->db->query(
            "SELECT sys0101, sys0103 AS fname, sys0104 AS lname, sys0111 AS tel, sys0110 AS unit_id, b.ent1004 AS unit_name FROM sys01 AS a LEFT JOIN ent10 AS b ON a.sys0110=b.ent1001 WHERE sys0108 = 1"
        )->getResult();

        return $this->response->setJSON(['unit' => $depts, 'staff' => $users]);
    }

    /**
     * 將巡檢記錄同步到第三方平台
     */
    private function sendPAD01ToAPI(int $pad0101, ?string $api): bool
    {
        if (empty($api)) {
            return false;
        }

        $pad01Model = model('Pad01Model');
        $pad01 = $pad01Model->find($pad0101);
        if (!$pad01) {
            return false;
        }

        $postData = [];
        $index = 1;
        $pad0107 = json_decode($pad01->pad0107, true);

        if ($pad0107) {
            foreach ($pad0107 as $v1) {
                if (!isset($v1['files']) || count($v1['files']) <= 0) {
                    continue;
                }
                foreach ($v1['files'] as $v2) {
                    $filePath = FCPATH . $v2;
                    if (file_exists($filePath)) {
                        $postData['file' . $index++] = new \CURLFile($filePath);
                    }
                }
            }
        }

        $postData['sys0101'] = $pad01->sys0101 ?? '';
        $postData['pad0102'] = $pad01->pad0102;
        $postData['pad0107'] = $pad01->pad0107;
        $postData['pad0109'] = $pad01->pad0109;

        return $this->httpRequest($api, $postData);
    }

    /**
     * 將報修記錄同步到第三方平台
     */
    private function sendPAD03ToAPI(int $pad0301): bool
    {
        $api = $this->setting->item('pad03_api');
        if (empty($api)) {
            return false;
        }

        $pad03 = $this->db->query("select * from pad03 where pad0301=?", [$pad0301])->getRow();
        if (!$pad03) {
            return false;
        }

        $postData = [
            'pad0301' => $pad03->pad0301,
            'pad0302' => $pad03->pad0302,
            'pad0303' => $pad03->pad0303,
            'pad0304' => $pad03->pad0304,
            'pad0305' => $pad03->pad0305,
        ];

        return $this->httpRequest($api, $postData);
    }

    /**
     * HTTP 請求
     */
    private function httpRequest(string $url, $data = '', $header = '', int $timeout = 1): bool
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, (array)$header);
        }

        $result = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            log_message('error', 'HTTP Request Error: ' . $error);
            return false;
        }

        return true;
    }

    /**
     * 下載所有數據 - 生成 ZIP 檔案供設備同步
     */
    public function getAll(): \CodeIgniter\HTTP\ResponseInterface
    {
        $json = ['status' => 'success', 'info' => '', 'file' => '', 'filesize' => ''];
        $dev0101 = (int)($this->request->getPost('dev0101') ?? 0);
        $dev0105 = trim($this->request->getPost('dev0105') ?? '');

        $path = FCPATH . 'data/temp';
        if (!file_exists($path) && !mkdir($path, 0777, true)) {
            $json['info'] = lang('Webapi.mkdir_fail');
            return $this->response->setJSON($json);
        }

        $dev01s = $this->dev01Model->getBy(['dev0101' => $dev0101, 'dev0105' => $dev0105]);
        if (!$dev01s || count($dev01s) === 0) {
            $json['status'] = 'fail';
            $json['info'] = lang('Webapi.dev01_not_exists');
            $json['dev0101'] = $dev0101;
            $json['dev0105'] = $dev0105;
            return $this->response->setJSON($json);
        }

        $dev01 = $dev01s[0];
        $dev0102 = $dev01->dev0102;
        $dev0103 = $dev01->dev0103 ?? '';

        // 部門限制
        if ($dev0103) {
            $depId = $this->ent10Model->getSubDepartmentId($dev0103);
            $depId = empty($depId) ? $dev0103 : sprintf("%s,%s", $dev0103, $depId);
            $depId = sprintf(" and ent1001 in (%s)", $depId);
        } else {
            $depId = '';
        }

        // 查詢表單
        $fmd01s = $this->db->query(
            sprintf("select fmd0101,fmd0102,fmd0103,fmd0104,fmd0105,fmd0107,fmd0108,fmd0110,fmd01z2,fmd01z4 from fmd01,ent10 where fmd0102=ent1001 and fmd0108=2 and ent1002=%s %s", $dev0102, $depId)
        )->getResult();

        $listdb = [];
        $zipPath = sprintf("data/temp/%s_%s_all.zip", $dev0101, date("YmdHis"));
        $zipfile = FCPATH . $zipPath;

        $zip = new \ZipArchive();
        $zip->open($zipfile, \ZipArchive::CREATE);

        if ($fmd01s && count($fmd01s)) {
            $maxsize = 500;
            foreach ($fmd01s as $v1) {
                $files = [];

                // fmd02
                $fmd02s = $this->db->query(sprintf("select fmd0201,fmd0202,fmd0203,fmd0204,fmd0205,fmd0206 from fmd02 where fmd0202=%s", $v1->fmd0101))->getResult();
                if ($fmd02s && count($fmd02s)) {
                    $index = 0;
                    for ($i = 0; $i < count($fmd02s); $i += $maxsize) {
                        $sqlStr = 'insert into fmd02 (fmd0201,fmd0202,fmd0203,fmd0204,fmd0205,fmd0206) values ';
                        $tmp = array_slice($fmd02s, $i, $maxsize);
                        foreach ($tmp as $v2) {
                            $sqlStr .= sprintf("(%s,%s,%s,'%s','%s','%s'),", $v2->fmd0201, $v2->fmd0202, $v2->fmd0203, $v2->fmd0204, $v2->fmd0205, $v2->fmd0206);
                        }
                        $sqlStr = rtrim($sqlStr, ",") . ";";
                        $filename = sprintf('cache/fmd02_%s_%s.txt', $v1->fmd0101, $index);
                        $zip->addFromString($filename, $sqlStr);
                        $files[] = $filename;
                        $index++;
                    }
                }

                // fmd03
                $fmd03s = $this->db->query(sprintf("select fmd0301,fmd0302,fmd0304 from fmd03 where fmd0302=%s", $v1->fmd0101))->getResult();
                if ($fmd03s && count($fmd03s)) {
                    $index = 0;
                    for ($i = 0; $i < count($fmd03s); $i += $maxsize) {
                        $sqlStr = 'insert into fmd03 (fmd0301,fmd0302,fmd0304) values ';
                        $tmp = array_slice($fmd03s, $i, $maxsize);
                        foreach ($tmp as $v2) {
                            $sqlStr .= sprintf("(%s,%s,'%s'),", $v2->fmd0301, $v2->fmd0302, $v2->fmd0304);
                        }
                        $sqlStr = rtrim($sqlStr, ",") . ";";
                        $filename = sprintf('cache/fmd03_%s_%s.txt', $v1->fmd0101, $index);
                        $zip->addFromString($filename, $sqlStr);
                        $files[] = $filename;
                        $index++;
                    }
                }

                // fmd04
                $fmd04s = $this->db->query(sprintf("select fmd0401,fmd0402,fmd0403,fmd0404,fmd0405,fmd0409 from fmd04 where fmd0402=%s", $v1->fmd0101))->getResult();
                if ($fmd04s && count($fmd04s)) {
                    $index = 0;
                    for ($i = 0; $i < count($fmd04s); $i += $maxsize) {
                        $sqlStr = 'insert into fmd04 (fmd0401,fmd0402,fmd0403,fmd0404,fmd0405,fmd0409) values ';
                        $tmp = array_slice($fmd04s, $i, $maxsize);
                        foreach ($tmp as $v2) {
                            $sqlStr .= sprintf("(%s,%s,'%s','%s','%s','%s'),", $v2->fmd0401, $v2->fmd0402, $v2->fmd0403, $v2->fmd0404, $v2->fmd0405, $v2->fmd0409);
                        }
                        $sqlStr = rtrim($sqlStr, ",") . ";";
                        $filename = sprintf('cache/fmd04_%s_%s.txt', $v1->fmd0101, $index);
                        $zip->addFromString($filename, $sqlStr);
                        $files[] = $filename;
                        $index++;
                    }
                }

                // fmd05
                $fmd05s = $this->db->query(sprintf("select fmd0501,fmd0502,fmd0503,fmd0504 from fmd05 where fmd0502=%s", $v1->fmd0101))->getResult();
                if ($fmd05s && count($fmd05s)) {
                    $index = 0;
                    for ($i = 0; $i < count($fmd05s); $i += $maxsize) {
                        $sqlStr = 'insert into fmd05 (fmd0501,fmd0502,fmd0503,fmd0504) values ';
                        $tmp = array_slice($fmd05s, $i, $maxsize);
                        foreach ($tmp as $v2) {
                            $sqlStr .= sprintf("(%s,%s,'%s','%s'),", $v2->fmd0501, $v2->fmd0502, $v2->fmd0503, $v2->fmd0504);
                        }
                        $sqlStr = rtrim($sqlStr, ",") . ";";
                        $filename = sprintf('cache/fmd05_%s_%s.txt', $v1->fmd0101, $index);
                        $zip->addFromString($filename, $sqlStr);
                        $files[] = $filename;
                        $index++;
                    }
                }

                // fmd06
                $fmd06s = $this->db->query(sprintf("select fmd0601,fmd0602,fmd0603,fmd0604,fmd0606,fmd0607,fmd0608,fmd0609,fmd0610,fmd0611,fmd0612,fmd0613,fmd0614,fmd0616,fmd0618,fmd0619 from fmd06 where fmd0602=%s", $v1->fmd0101))->getResult();
                if ($fmd06s && count($fmd06s)) {
                    $index = 0;
                    for ($i = 0; $i < count($fmd06s); $i += $maxsize) {
                        $sqlStr = 'insert into fmd06 (fmd0601,fmd0602,fmd0603,fmd0604,fmd0606,fmd0607,fmd0608,fmd0609,fmd0610,fmd0611,fmd0612,fmd0613,fmd0614,fmd0616,fmd0618,fmd0619) values ';
                        $tmp = array_slice($fmd06s, $i, $maxsize);
                        foreach ($tmp as $v2) {
                            $sqlStr .= sprintf("(%s,%s,%s,%s,'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',%s),", $v2->fmd0601, $v2->fmd0602, $v2->fmd0603, $v2->fmd0604, $v2->fmd0606, $v2->fmd0607, $v2->fmd0608, $v2->fmd0609, $v2->fmd0610, $v2->fmd0611, $v2->fmd0612, $v2->fmd0613, $v2->fmd0614, $v2->fmd0616, $v2->fmd0618, $v2->fmd0619);
                        }
                        $sqlStr = rtrim($sqlStr, ",") . ";";
                        $filename = sprintf('cache/fmd06_%s_%s.txt', $v1->fmd0101, $index);
                        $zip->addFromString($filename, $sqlStr);
                        $files[] = $filename;
                        $index++;
                    }
                }

                // fmd07
                $fmd07s = $this->db->query(sprintf("select fmd0701,fmd0702,fmd0703,fmd0704,fmd0706,fmd0708 from fmd07 where fmd0702=%s", $v1->fmd0101))->getResult();
                if ($fmd07s && count($fmd07s)) {
                    $index = 0;
                    for ($i = 0; $i < count($fmd07s); $i += $maxsize) {
                        $sqlStr = 'insert into fmd07 (fmd0701,fmd0702,fmd0703,fmd0704,fmd0706,fmd0708) values ';
                        $tmp = array_slice($fmd07s, $i, $maxsize);
                        foreach ($tmp as $v2) {
                            $sqlStr .= sprintf("(%s,%s,'%s','%s','%s','%s'),", $v2->fmd0701, $v2->fmd0702, $v2->fmd0703, $v2->fmd0704, $v2->fmd0706, $v2->fmd0708);
                        }
                        $sqlStr = rtrim($sqlStr, ",") . ";";
                        $filename = sprintf('cache/fmd07_%s_%s.txt', $v1->fmd0101, $index);
                        $zip->addFromString($filename, $sqlStr);
                        $files[] = $filename;
                        $index++;
                    }
                }

                // fmd08
                $fmd08s = $this->db->query(sprintf("select fmd0801,fmd0802,fmd0803,fmd0804,fmd0805 from fmd08 where fmd0802=%s", $v1->fmd0101))->getResult();
                if ($fmd08s && count($fmd08s)) {
                    $index = 0;
                    for ($i = 0; $i < count($fmd08s); $i += $maxsize) {
                        $sqlStr = 'insert into fmd08 (fmd0801,fmd0802,fmd0803,fmd0804,fmd0805) values ';
                        $tmp = array_slice($fmd08s, $i, $maxsize);
                        foreach ($tmp as $v2) {
                            $sqlStr .= sprintf("(%s,%s,'%s','%s','%s'),", $v2->fmd0801, $v2->fmd0802, $v2->fmd0803, $v2->fmd0804, $v2->fmd0805);
                        }
                        $sqlStr = rtrim($sqlStr, ",") . ";";
                        $filename = sprintf('cache/fmd08_%s_%s.txt', $v1->fmd0101, $index);
                        $zip->addFromString($filename, $sqlStr);
                        $files[] = $filename;
                        $index++;
                    }
                }

                // fmd09
                $fmd09s = $this->db->query(sprintf("select fmd0901,fmd0902,fmd0903,fmd0904,fmd0905,fmd0906,fmd0908,fmd0909,fmd0910,fmd0911 from fmd09 where fmd0902=%s", $v1->fmd0101))->getResult();
                if ($fmd09s && count($fmd09s)) {
                    $index = 0;
                    for ($i = 0; $i < count($fmd09s); $i += $maxsize) {
                        $sqlStr = 'insert into fmd09 (fmd0901,fmd0902,fmd0903,fmd0904,fmd0905,fmd0908,fmd0909,fmd0910,fmd0911,fmd0906) values ';
                        $tmp = array_slice($fmd09s, $i, $maxsize);
                        foreach ($tmp as $v2) {
                            $sqlStr .= sprintf("(%s,%s,'%s','%s','%s','%s','%s','%s','%s','%s'),", $v2->fmd0901, $v2->fmd0902, $v2->fmd0903, $v2->fmd0904, $v2->fmd0905, $v2->fmd0908, $v2->fmd0909, $v2->fmd0910, $v2->fmd0911, $v2->fmd0906);
                        }
                        $sqlStr = rtrim($sqlStr, ",") . ";";
                        $filename = sprintf('cache/fmd09_%s_%s.txt', $v1->fmd0101, $index);
                        $zip->addFromString($filename, $sqlStr);
                        $files[] = $filename;
                        $index++;
                    }
                }

                // fmd01 本身
                $filename = sprintf('cache/fmd0101_%s.txt', $v1->fmd0101);
                $sqlStr = sprintf("insert into fmd01 (fmd0101,fmd0102,fmd0103,fmd0104,fmd0105,fmd0107,fmd0108,fmd0110,fmd01z4) values (%s,%s,'%s','%s',%s,%s,%s,%s,'%s');", $v1->fmd0101, $v1->fmd0102, $v1->fmd0103, $v1->fmd0104, $v1->fmd0105, $v1->fmd0107, $v1->fmd0108, $v1->fmd0110, $v1->fmd01z4);
                $zip->addFromString($filename, $sqlStr);
                $files[] = $filename;
                $listdb[] = ['title' => $v1->fmd0104 . '_V' . $v1->fmd0107, 'files' => $files];
            }
        }

        // fmd10 通知排程
        $fmd10s = $this->db->query(sprintf("select fmd1001,fmd1002,fmd1003,fmd1004,fmd1005,fmd1006,fmd1007,fmd1008,fmd1009,fmd1010,fmd1011,fmd1012,if(fmd10z4 is null,fmd10z2,fmd10z4) as fmd10z4 from fmd10 where fmd1012=%s", $dev0102))->getResult();
        if ($fmd10s && count($fmd10s)) {
            $sqlStr = 'insert into fmd10 (fmd1001,fmd1002,fmd1003,fmd1004,fmd1005,fmd1006,fmd1007,fmd1008,fmd1009,fmd1010,fmd1011,fmd1012,fmd10z4) values ';
            foreach ($fmd10s as $v1) {
                if ($v1->fmd1010 == 1) {
                    $sqlStr .= sprintf("('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'),", $v1->fmd1001, $v1->fmd1002, $v1->fmd1003, $v1->fmd1004, $v1->fmd1005, $v1->fmd1006, $v1->fmd1007, $v1->fmd1008, $v1->fmd1009, $v1->fmd1010, $v1->fmd1011, $v1->fmd1012, $v1->fmd10z4);
                }
            }
            $sqlStr = rtrim($sqlStr, ",") . ";";
            $filename = 'cache/fmd10.txt';
            $zip->addFromString($filename, $sqlStr);
            $listdb[] = ['title' => lang('Webapi.table_fmd10'), 'files' => [$filename]];
        }

        // sys01 帳號
        $sys01s = $this->db->query(sprintf("select sys0101,sys0102,sys0103,sys0104,sys0105,sys0106,sys0107,sys0108,sys0110,sys0111,sys0117,sys0121,if(sys01z4 is null,sys01z2,sys01z4) as sys01z4 from sys01,ent10 where sys0110=ent1001 and ent1002=%s", $dev0102))->getResult();
        if ($sys01s && count($sys01s)) {
            $sqlStr = 'insert into sys01 (sys0101,sys0102,sys0103,sys0104,sys0105,sys0106,sys0107,sys0108,sys0110,sys0111,sys0117,sys0121,sys01z4) values ';
            foreach ($sys01s as $v1) {
                $sqlStr .= sprintf("('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'),", $v1->sys0101, $v1->sys0102, $v1->sys0103, $v1->sys0104, $v1->sys0105, $v1->sys0106, $v1->sys0107, $v1->sys0108, $v1->sys0110, $v1->sys0111, $v1->sys0117, $v1->sys0121, $v1->sys01z4);
            }
            $sqlStr = rtrim($sqlStr, ",") . ";";
            $filename = 'cache/sys01.txt';
            $zip->addFromString($filename, $sqlStr);
            $listdb[] = ['title' => lang('Webapi.table_sys01'), 'files' => [$filename]];
        }

        // ent10 部門
        $ent10s = $this->db->query(sprintf("select ent1001,ent1002,ent1004,ent1005,ent1007,if(ent10z4 is null,ent10z2,ent10z4) as ent10z4 from ent10 where ent1002=%s", $dev0102))->getResult();
        if ($ent10s && count($ent10s)) {
            $sqlStr = 'insert into ent10 (ent1001,ent1002,ent1004,ent1005,ent1007,ent10z4) values ';
            foreach ($ent10s as $v1) {
                $sqlStr .= sprintf("('%s','%s','%s','%s','%s','%s'),", $v1->ent1001, $v1->ent1002, $v1->ent1004, $v1->ent1005, $v1->ent1007, $v1->ent10z4);
            }
            $sqlStr = rtrim($sqlStr, ",") . ";";
            $filename = 'cache/ent10.txt';
            $zip->addFromString($filename, $sqlStr);
            $listdb[] = ['title' => lang('Webapi.table_ent10'), 'files' => [$filename]];
        }

        // 設定資訊
        $set01s = [];
        $set01s[] = ['set0102' => 'pad03_enable', 'set0103' => $this->ent02Model->getLicenseCount('smart_patrol_of2', $dev0102) > 0 ? 1 : 0];
        $set01s[] = ['set0102' => 'power_low_alert', 'set0103' => $this->setting->item('power_low_alert') ?? ''];
        $json['set01'] = $set01s;

        $zip->addFromString('cache/00000.txt', json_encode($listdb));
        $zip->close();

        if (count($listdb)) {
            $json['file'] = $zipPath;
            $json['filesize'] = filesize($zipfile);
        } else {
            if (file_exists($zipfile)) {
                unlink($zipfile);
            }
            $json['file'] = '';
            $json['filesize'] = 0;
        }

        return $this->response->setJSON($json);
    }

    /**
     * 獲取異常巡檢結果
     */
    public function getException(): \CodeIgniter\HTTP\ResponseInterface
    {
        $json = ['status' => 'fail', 'info' => '', 'list' => []];

        $ent0102 = trim($this->request->getGet('qycode') ?? '');
        $pad0109s = trim($this->request->getGet('start_time') ?? '');
        $pad0109e = trim($this->request->getGet('ends_time') ?? '');
        $offset = (int)($this->request->getGet('pagestat') ?? 0);
        $pagesize = (int)($this->request->getGet('pagesize') ?? 100);

        if ($pagesize > 100 || $pagesize < 1) {
            $pagesize = 100;
        }
        if ($offset < 0) {
            $offset = 0;
        }

        if (empty($ent0102)) {
            $json['info'] = lang('Webapi.ent0102_empty');
            return $this->response->setJSON($json);
        }

        $ent01Model = model('Ent01Model');
        $ent01s = $ent01Model->getBy(['ent0102' => $ent0102]);

        if (!$ent01s || count($ent01s) === 0) {
            $json['info'] = lang('Webapi.ent0102_not_exists');
            return $this->response->setJSON($json);
        }

        $ent01 = $ent01s[0];
        if ((int)$ent01->ent0104 === 0) {
            $json['info'] = lang('Webapi.ent10_disable');
            return $this->response->setJSON($json);
        }

        // 查詢異常巡檢結果
        $pad0109 = "";
        if (!empty($pad0109s)) {
            $pad0109 .= " and pad0109>='" . $this->db->escapeString($pad0109s) . "'";
        }
        if (!empty($pad0109e)) {
            $pad0109 .= " and pad0109<'" . $this->db->escapeString($pad0109e) . "'";
        }

        $sql = sprintf(
            "select * from pad01,sys01,fmd09,fmd07,fmd01,ent10 where pad0102=fmd0701 and fmd0702=fmd0101 and sys0101=pad0104 and pad0106=fmd0901 and fmd0102=ent1001 and pad0113=1 and ent1002=%s and pad0107 like '%%:true%%' %s order by pad0101 desc limit %s,%s",
            $ent01->ent0101,
            $pad0109,
            $offset,
            $pagesize
        );
        $list = $this->db->query($sql)->getResult();

        $result = [];
        foreach ($list as $v1) {
            $pad0107 = json_decode($v1->pad0107, true);
            if (!$pad0107) {
                continue;
            }
            foreach ($pad0107 as $v2) {
                if (!isset($v2['value'][0]) || !is_array($v2['value'][0])) {
                    continue;
                }
                $val = array_values($v2['value'][0]);
                $key = array_keys($v2['value'][0]);
                if ($val[0] != 1) {
                    continue;
                }

                $item = new \stdClass();
                $item->serial = $v1->pad0101 . '-' . $v2['fmd0601'];
                $item->username = $v1->sys0103 . $v1->sys0104;
                $item->fmd0905 = $v1->fmd0905;
                $item->fmd0908 = $v1->fmd0908;
                $item->pad0109 = $v1->pad0109;
                $item->pad0112 = $v1->pad0112;
                $item->prompt = $v2['prompt'] ?? '';
                $item->fmd0504 = $v2['fmd0504'] ?? '';
                $item->value = $key[0] ?? '';
                $item->info = $v2['info'] ?? '';
                $item->files = $v2['files'] ?? [];
                $result[] = $item;
            }
        }

        $json = ['status' => 'success', 'info' => '', 'list' => $result];
        return $this->response->setJSON($json);
    }

    /**
     * 上傳報修記錄 - 多條記錄打包
     */
    public function addrepairmulti(): \CodeIgniter\HTTP\ResponseInterface
    {
        $pad03Model = model('Pad03Model');
        $time = time();
        $json = ['status' => 'fail', 'info' => lang('Webapi.upload_fail')];

        $dev0101 = (int)($this->request->getPost('dev0101') ?? 0);
        $dev0105 = trim($this->request->getPost('dev0105') ?? '');

        $zipPath = FCPATH . 'data/pad0304';
        if (!file_exists($zipPath) && !mkdir($zipPath, 0777, true)) {
            $json['info'] = lang('Webapi.mkdir_fail');
            return $this->response->setJSON($json);
        }
        $zipPath .= '/zip';
        if (!file_exists($zipPath) && !mkdir($zipPath, 0777, true)) {
            $json['info'] = lang('Webapi.mkdir_fail');
            return $this->response->setJSON($json);
        }

        $dev01s = $this->dev01Model->getBy(['dev0101' => $dev0101, 'dev0105' => $dev0105]);
        if (!$dev01s || count($dev01s) === 0) {
            $json['info'] = lang('Webapi.dev01_not_exists');
            $json['dev0101'] = $dev0101;
            $json['dev0105'] = $dev0105;
            return $this->response->setJSON($json);
        } elseif ((int)$dev01s[0]->dev0106 !== 1) {
            $json['info'] = lang('Webapi.dev01_not_enable');
            return $this->response->setJSON($json);
        } elseif ((int)$dev01s[0]->ent0104 !== 1) {
            $json['info'] = lang('Webapi.ent10_not_enable');
            return $this->response->setJSON($json);
        }

        $dev01 = $dev01s[0];
        $file = $this->request->getFile('file1');

        if (!$file || !$file->isValid()) {
            $json['info'] = lang('Webapi.upload_error' . ($file ? $file->getError() : '4'));
            return $this->response->setJSON($json);
        }

        $zipfile = sprintf('%s/%s_%s.zip', $zipPath, $dev0101, $time);
        $file->move($zipPath, basename($zipfile));

        $zip = new \ZipArchive();
        if ($zip->open($zipfile) === true) {
            $zip->extractTo(FCPATH);
            $info = $zip->getFromName('data/0000.txt');
            $zip->close();

            $dataFile = FCPATH . 'data/0000.txt';
            if (file_exists($dataFile)) {
                unlink($dataFile);
            }

            if (!empty($info)) {
                $jsonstr = json_decode($info);
                if ($jsonstr) {
                    foreach ($jsonstr as $item) {
                        // 檢查重複
                        $rows = $this->db->query(
                            "select count(*) as abc from pad03 where pad0302=? and pad03z2=?",
                            [$dev0101, $item->pad03z2]
                        )->getRow();

                        if ((int)$rows->abc) {
                            $json['status'] = 'success';
                            $json['info'] = lang('Webapi.data_exists');
                            continue;
                        }

                        $pad03 = [
                            'pad0302' => $dev0101,
                            'pad0303' => $item->pad0302 ?? '',
                            'pad0304' => $item->pad0303 ?? '',
                            'pad0305' => $item->pad0304 ?? '',
                            'pad03z1' => $item->pad03z1 ?? 0,
                            'pad03z2' => $item->pad03z2 ?? '',
                            'pad03z3' => $item->pad03z1 ?? 0,
                            'pad03z4' => date('Y-m-d H:i:s', $time),
                        ];

                        $pad0301 = $pad03Model->insert($pad03);
                        if ($pad0301) {
                            if (isset($item->pad04) && is_array($item->pad04)) {
                                foreach ($item->pad04 as $filePath) {
                                    $this->db->query("insert into pad04 (pad0402,pad0403) values (?,?)", [$pad0301, $filePath]);
                                }
                            }
                            $this->sendPAD03ToAPI($pad0301);
                            $json['status'] = 'success';
                            $json['info'] = lang('Webapi.upload_success');
                        } else {
                            $json['status'] = 'fail';
                            $json['info'] = lang('Webapi.write_fail');
                            break;
                        }
                    }

                    $this->dev01Model->update($dev0101, ['dev0109' => date('Y-m-d H:i:s')]);
                } else {
                    $json['info'] = lang('Webapi.json_format_error');
                }
            } else {
                $json['info'] = lang('Webapi.data_empty');
            }
        } else {
            $json['info'] = lang('Webapi.zip_cannot_open');
        }

        return $this->response->setJSON($json);
    }

    /**
     * 同步企業資料
     */
    public function updateent01(): \CodeIgniter\HTTP\ResponseInterface
    {
        $json = ['status' => 'fail', 'info' => lang('Webapi.upload_fail'), 'ent01' => new \stdClass()];

        $dev0101 = (int)($this->request->getPost('dev0101') ?? 0);
        $dev0105 = trim($this->request->getPost('dev0105') ?? '');

        $dev01 = $this->dev01Model->find($dev0101);
        if (!$dev01) {
            $json['info'] = lang('Webapi.dev01_not_exists');
        } else {
            $ent01 = $this->db->query("select * from ent01 where ent0101=?", [$dev01->dev0102])->getRow();
            $json['ent01'] = $ent01 ?? new \stdClass();
            $json['status'] = 'success';
            $json['info'] = '';
        }

        $this->dev01Model->update($dev0101, ['dev0109' => date('Y-m-d H:i:s')]);

        return $this->response->setJSON($json);
    }

    /**
     * 上傳設備日誌
     */
    public function uploaddev02(): \CodeIgniter\HTTP\ResponseInterface
    {
        log_message('debug', 'Webapi uploaddev02.');

        $dev02Model = model('Dev02Model');
        $dev0101 = (int)($this->request->getPost('dev0101') ?? 0);
        $dev0105 = trim($this->request->getPost('dev0105') ?? '');
        $imei = trim($this->request->getPost('os_imei') ?? '');
        $dev02s = trim($this->request->getPost('dev02') ?? '');
        $dev02s = json_decode($dev02s);

        if ($dev02s && is_array($dev02s)) {
            foreach ($dev02s as $dev02) {
                $data = (array)$dev02;
                unset($data['dev0201']);
                $data['dev0202'] = $dev0101;
                $data['dev0207'] = date('Y-m-d H:i:s');
                $dev02Model->insert($data);
            }
        }

        $dev01s = $this->dev01Model->getBy(['dev0101' => $dev0101, 'dev0105' => $dev0105], 1);
        if ($dev01s && is_object($dev01s) && empty($dev01s->dev0112) && !empty($imei)) {
            $this->db->query("update dev01 set dev0112=?,dev0109=? where dev0101=?", [$imei, date('Y-m-d H:i:s'), $dev01s->dev0101]);
        }

        $json = ['status' => 'success', 'info' => lang('Webapi.upload_success')];
        return $this->response->setJSON($json);
    }

    /**
     * 註冊 Tags
     */
    public function regtags(): \CodeIgniter\HTTP\ResponseInterface
    {
        $json = ['status' => 'success', 'info' => lang('Webapi.upload_success')];

        $dev0101 = (int)($this->request->getPost('dev0101') ?? 0);
        $dev0105 = trim($this->request->getPost('dev0105') ?? '');
        $sys0101 = (int)($this->request->getPost('sys0101') ?? 0);
        $tags = trim($this->request->getPost('tags') ?? '');
        $tags = json_decode($tags);

        $dev01s = $this->dev01Model->getBy(['dev0101' => $dev0101, 'dev0105' => $dev0105]);
        if (!$dev01s || count($dev01s) === 0) {
            $json['status'] = 'fail';
            $json['info'] = lang('Webapi.dev01_not_exists');
        } else {
            $dev01 = $dev01s[0];
            if ($tags && is_array($tags)) {
                foreach ($tags as $item) {
                    $sql = "update fmd09 set fmd0909=?,fmd0910=?,fmd0911=?,fmd09z3=?,fmd09z4=? where fmd0901=? and (fmd0909 is null or fmd0909='')";
                    $this->db->query($sql, [
                        $item->fmd0909 ?? '',
                        $item->fmd0910 ?? '',
                        $item->fmd0911 ?? '',
                        $item->fmd09z3 ?? 0,
                        $item->fmd09z4 ?? '',
                        $item->fmd0901 ?? 0,
                    ]);
                }
            }
            $this->dev01Model->update($dev0101, ['dev0109' => date('Y-m-d H:i:s')]);
        }

        return $this->response->setJSON($json);
    }

    /**
     * 上傳照片
     */
    public function photograph(): \CodeIgniter\HTTP\ResponseInterface
    {
        log_message('debug', '[Webapi] photograph - START');

        try {
            $pad07Model = model('Pad07Model');
            $json = ['status' => 'fail', 'info' => lang('Webapi.upload_fail')];

            $pad0707 = trim($this->request->getPost('pad0707') ?? '');
            $dev0101 = (int)($this->request->getPost('dev0101') ?? 0);
            $dev0105 = trim($this->request->getPost('dev0105') ?? '');
            $imei = trim($this->request->getPost('os_imei') ?? '');

            log_message('debug', '[Webapi] photograph - params: dev0101=' . $dev0101 . ', dev0105=' . $dev0105 . ', pad0707=' . $pad0707);

            $dev01s = $this->dev01Model->getBy(['dev0101' => $dev0101, 'dev0105' => $dev0105]);
            log_message('debug', '[Webapi] photograph - dev01s count: ' . ($dev01s ? count($dev01s) : 0));

            if (!$dev01s || count($dev01s) === 0) {
                log_message('debug', '[Webapi] photograph - device not found');
                $json['info'] = lang('Webapi.dev01_not_exists');
                $json['dev0101'] = $dev0101;
                $json['dev0105'] = $dev0105;
            } elseif ((int)$dev01s[0]->dev0106 !== 1) {
                log_message('debug', '[Webapi] photograph - device not enabled, dev0106=' . $dev01s[0]->dev0106);
                $json['info'] = lang('Webapi.dev01_not_enable');
            } elseif ((int)$dev01s[0]->ent0104 !== 1) {
                log_message('debug', '[Webapi] photograph - enterprise not enabled, ent0104=' . $dev01s[0]->ent0104);
                $json['info'] = lang('Webapi.ent10_not_enable');
            } elseif (empty($pad0707)) {
                log_message('debug', '[Webapi] photograph - pad0707 is empty');
                $json['info'] = lang('Webapi.json_error');
            } else {
                $dev01 = $dev01s[0];
                log_message('debug', '[Webapi] photograph - device validated, proceeding');

                if (empty($dev01->dev0112) && !empty($imei)) {
                    $this->dev01Model->update($dev01->dev0101, ['dev0112' => $imei]);
                    log_message('debug', '[Webapi] photograph - updated dev0112');
                }

                $pad07Data = [
                    'pad0702' => (int)($this->request->getPost('pad0702') ?? 0),
                    'pad0703' => $dev0101,
                    'pad0704' => (int)($this->request->getPost('pad0704') ?? 0),
                    'pad0705' => $this->request->getPost('pad0705') ?? '',
                    'pad0706' => $this->request->getPost('pad0706') ?? '',
                    'pad0707' => $pad0707,
                    'pad0708' => date('Y-m-d H:i:s'),
                ];
                log_message('debug', '[Webapi] photograph - pad07Data: ' . json_encode($pad07Data));

                // 檢查重複
                $existingPad07 = $this->db->query(
                    "select * from pad07 where pad0702=? and pad0703=? and pad0704=? and pad0706 like ? and pad0707=STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')",
                    [$pad07Data['pad0702'], $pad07Data['pad0703'], $pad07Data['pad0704'], '%' . $pad07Data['pad0706'] . '%', $pad07Data['pad0707']]
                )->getRow();
                log_message('debug', '[Webapi] photograph - duplicate check: ' . ($existingPad07 ? 'found' : 'not found'));

                if ($existingPad07) {
                    $json['status'] = 'success';
                    $json['info'] = lang('Webapi.upload_success');
                    $json['pad0701'] = $existingPad07->pad0701;
                    $json['pad0708'] = $existingPad07->pad0708;
                    log_message('debug', '[Webapi] photograph - returning existing record');
                } else {
                    $year = date('Y');
                    $month = date('m');
                    $day = date('d');
                    $photoBase = FCPATH . 'data/photograph/' . $year . '/' . $month . '/' . $day . '/' . $pad07Data['pad0703'];
                    log_message('debug', '[Webapi] photograph - photoBase: ' . $photoBase);

                    if (!file_exists($photoBase) && !mkdir($photoBase, 0777, true)) {
                        log_message('error', '[Webapi] photograph - mkdir failed: ' . $photoBase);
                        $json['info'] = lang('Webapi.mkdir_fail');
                        return $this->response->setJSON($json);
                    }
                    log_message('debug', '[Webapi] photograph - directory ready');

                    $pad0701 = $pad07Model->insert($pad07Data);
                    log_message('debug', '[Webapi] photograph - insert result: ' . ($pad0701 ?: 'false'));

                    if ($pad0701) {
                        $files = $this->request->getFiles();
                        log_message('debug', '[Webapi] photograph - files count: ' . count($files));

                        foreach ($files as $name => $file) {
                            log_message('debug', '[Webapi] photograph - processing file: ' . $name);
                            if (!$file->isValid()) {
                                log_message('debug', '[Webapi] photograph - file not valid: ' . $file->getErrorString());
                                continue;
                            }
                            if ($file->hasMoved()) {
                                log_message('debug', '[Webapi] photograph - file already moved');
                                continue;
                            }
                            $ext = strtolower($file->getExtension());
                            log_message('debug', '[Webapi] photograph - file extension: ' . $ext);
                            if (!in_array($ext, ['jpg', 'png'])) {
                                log_message('debug', '[Webapi] photograph - invalid extension');
                                continue;
                            }
                            $fileName = $file->getName();
                            $pad0706 = sprintf('%s/%s', $photoBase, $fileName);
                            log_message('debug', '[Webapi] photograph - moving file to: ' . $pad0706);
                            $file->move($photoBase, $fileName);
                            $pad07Model->update($pad0701, ['pad0706' => $pad0706]);
                            log_message('debug', '[Webapi] photograph - file moved and updated');
                            break; // 只允許傳一張照片
                        }

                        $json['status'] = 'success';
                        $json['info'] = lang('Webapi.upload_success');
                        $json['pad0701'] = $pad0701;
                        $json['pad0708'] = $pad07Data['pad0708'];
                        log_message('debug', '[Webapi] photograph - success');
                    } else {
                        log_message('error', '[Webapi] photograph - insert failed');
                        $json['info'] = lang('Webapi.write_fail');
                    }
                }

                $this->dev01Model->update($dev0101, ['dev0109' => date('Y-m-d H:i:s')]);
            }

            log_message('debug', '[Webapi] photograph - END, response: ' . json_encode($json));
            // 使用與 CI3 相同的輸出方式
            echo json_encode($json);
            exit;

        } catch (\Exception $e) {
            log_message('error', '[Webapi] photograph - Exception: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            echo json_encode([
                'status' => 'fail',
                'info' => lang('Webapi.upload_fail'),
                'debug' => $e->getMessage()
            ]);
            exit;
        }
    }
}
