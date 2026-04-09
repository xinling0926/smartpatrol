<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * IsoModel - 巡檢報表資料模型
 */
class IsoModel extends Model
{
    protected $table = '';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $message;
    protected $currentUser;

    // N+1 查詢快取
    private array $fieldDefineCache = [];
    private array $fmd06Cache = [];
    private array $fmd04CrossCache = [];

    public function __construct()
    {
        parent::__construct();
        $this->message = service('message');
        $user = service('user');
        $this->currentUser = $user ? $user->current() : null;
    }

    /**
     * 設定表名
     */
    public function setTableName(object $fmd01): string
    {
        $tableName = 'iso' . (($fmd01->fmd0106 < 10) ? '0' . $fmd01->fmd0106 : $fmd01->fmd0106);

        if ($this->table !== $tableName) {
            $this->table = $tableName;

            // 檢查報表Table已經建立了沒
            if (!$this->db->tableExists($this->table)) {
                $this->createTable($fmd01->fmd0101);
            } else {
                // 表格已存在，檢查是否需要新增欄位
                $this->checkSchema($fmd01->fmd0101);
            }
            if (!$this->db->tableExists($this->table . 'c')) {
                $this->createApproveTable();
            }
        }

        return $this->table;
    }

    /**
     * 依日期取得報表主檔
     */
    public function getMasterByDate(string $date): ?object
    {
        return $this->db->table($this->table)
            ->where('date', $date)
            ->get()
            ->getRow();
    }

    /**
     * 取得報表主檔
     */
    public function getMaster(int $reportId): ?object
    {
        return $this->db->table($this->table)
            ->join('sys01', 'sys0101=user_id')
            ->join('fmd01', 'fmd01.fmd0101=' . $this->table . '.fmd0101')
            ->where('id', $reportId)
            ->get()
            ->getRow();
    }

    /**
     * 取得報表主檔（不 JOIN）
     */
    public function getMasterOnly(int $reportId): ?object
    {
        return $this->db->table($this->table)
            ->where('id', $reportId)
            ->get()
            ->getRow();
    }

    /**
     * 查詢主檔
     */
    public function queryMasterBy(?array $option = null, int $limit = 0, int $offset = 0, ?string $sort = null): mixed
    {
        $builder = $this->db->table($this->table);

        if ($option) {
            foreach ($option as $key => $value) {
                if (strpos($key, '*') === 0) {
                    $builder->where($value, null, false);
                } elseif ($value !== '') {
                    $builder->where($key, $value);
                }
            }
        }

        if ($sort) {
            $builder->orderBy($sort);
        }

        $builder->join('sys01', 'sys0101=user_id');
        $builder->join('fmd01', 'fmd01.fmd0101=' . $this->table . '.fmd0101');

        // Add approve table JOIN if showapprove is enabled
        if (config('App')->showapprove) {
            $builder->join($this->table . 'c', $this->table . '.id=' . $this->table . 'c.c02', 'left');
        }

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        if ($limit === 1) {
            return $builder->get()->getRow();
        }

        return $builder->get()->getResult();
    }

    /**
     * 計算主檔數量
     */
    public function countMasterBy(?array $option = null): int
    {
        $builder = $this->db->table($this->table);

        if ($option) {
            foreach ($option as $key => $value) {
                if (strpos($key, '*') === 0) {
                    $builder->where($value, null, false);
                } elseif ($value !== '') {
                    $builder->where($key, $value);
                }
            }
        }

        return $builder->countAllResults();
    }

    /**
     * 查詢明細資料
     */
    public function queryDetailBy(?array $option = null, int $limit = 0, int $offset = 0, ?string $sort = null): mixed
    {
        $builder = $this->db->table($this->table . 'a detail');
        $builder->join($this->table . ' master', 'master.id=detail.master_id');
        $builder->select('detail.*, master.date');

        if ($option) {
            foreach ($option as $key => $value) {
                if (strpos($key, '*') === 0) {
                    $builder->where($value, null, false);
                } elseif ($value !== '') {
                    $builder->where($key, $value);
                }
            }
        }

        if ($sort) {
            $builder->orderBy($sort);
        }

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        if ($limit === 1) {
            return $builder->get()->getRow();
        }

        return $builder->get()->getResult();
    }

    /**
     * 計算明細數量
     */
    public function countDetailBy(?array $option = null): int
    {
        $builder = $this->db->table($this->table . 'a detail');
        $builder->join($this->table . ' master', 'master.id=detail.master_id');

        if ($option) {
            foreach ($option as $key => $value) {
                if (strpos($key, '*') === 0) {
                    $builder->where($value, null, false);
                } elseif ($value !== '') {
                    $builder->where($key, $value);
                }
            }
        }

        return $builder->countAllResults();
    }

    /**
     * 取得明細資料
     */
    public function getDetailByMaster(int $masterId): array
    {
        $data = $this->db->table($this->table . 'a')
            ->where('master_id', $masterId)
            ->orderBy('line')
            ->get()
            ->getResult();

        $result = [];
        foreach ($data as $item) {
            $result[$item->line] = $item;
        }

        return $result;
    }

    /**
     * 取得欄位定義
     */
    public function getFieldDefine(int $fmd0601): ?object
    {
        if (isset($this->fieldDefineCache[$fmd0601])) {
            return $this->fieldDefineCache[$fmd0601];
        }

        $result = $this->db->table('fmd06')
            ->select('fmd0409,fmd0503,fmd0616,fmd0617')
            ->join('fmd04', 'fmd0401=fmd0603')
            ->join('fmd05', 'fmd0501=fmd0604')
            ->where('fmd0601', $fmd0601)
            ->get()
            ->getRow();

        $this->fieldDefineCache[$fmd0601] = $result;
        return $result;
    }

    /**
     * 取得 fmd06 快取（開關欄位用）
     */
    private function getFmd06Cached(int $fmd0601, string $select = 'fmd0608'): ?object
    {
        $key = $fmd0601 . '_' . $select;
        if (isset($this->fmd06Cache[$key])) {
            return $this->fmd06Cache[$key];
        }

        $result = $this->db->table('fmd06')
            ->select($select)
            ->where('fmd0601', $fmd0601)
            ->get()
            ->getRow();

        $this->fmd06Cache[$key] = $result;
        return $result;
    }

    /**
     * 取得跨版本欄位對應快取
     */
    private function getFmd04CrossVersion(int $fmd0101, string $fmd0617): ?object
    {
        $key = $fmd0101 . '_' . $fmd0617;
        if (isset($this->fmd04CrossCache[$key])) {
            return $this->fmd04CrossCache[$key];
        }

        $result = $this->db->table('fmd04')
            ->select('fmd0409')
            ->join('fmd01', 'fmd0402=fmd0101')
            ->join('fmd06', 'fmd0401=fmd0603')
            ->where('fmd0101', $fmd0101)
            ->where('fmd0617', $fmd0617)
            ->get()
            ->getRow();

        $this->fmd04CrossCache[$key] = $result;
        return $result;
    }

    /**
     * 產生報表 - 完整版本
     */
    public function generateReport(object $fmd01, string $date, ?string $fmd0203s = null): int|false
    {
        helper('common');

        // 清除快取
        $this->fieldDefineCache = [];
        $this->fmd06Cache = [];
        $this->fmd04CrossCache = [];

        // 開啟 SQL 日誌記錄
        $logSql = true;
        if ($logSql) {
            log_message('info', '========== generateReport START ==========');
            log_message('info', "fmd01: {$fmd01->fmd0101}, date: {$date}, fmd0203s: " . ($fmd0203s ?? 'null'));
        }

        // 依照報表週期設定，指定抓取原始紀錄的時間起訖
        switch ((int)$fmd01->fmd0105) {
            case 1: // 日
                if ($fmd0203s) {
                    $fmd02 = $this->db->table('fmd02')
                        ->where('fmd0202', $fmd01->fmd0101)
                        ->orderBy('fmd0203')
                        ->get()
                        ->getRow();
                    $startTime = $date . ' ' . $fmd02->fmd0205;
                    $endTime = date('Y-m-d H:i:s', strtotime("$startTime +1 day"));
                } else {
                    $startTime = $date;
                    $endTime = date('Y-m-d', strtotime("$startTime +1 day"));
                }
                break;
            case 2: // 週
                $startTime = monday($date);
                $endTime = date('Y-m-d', strtotime("$startTime +7 day"));
                break;
            case 3: // 月
                $startTime = $date . '-1';
                $endTime = date('Y-m-d', strtotime("$startTime +1 month"));
                break;
            case 4: // 每月兩次
                $month = explode('#', $date)[0];
                $time = explode('#', $date)[1];
                $startTime = $month . '-1';
                if ($time == 1) {
                    $endTime = date('Y-m-d', strtotime("$startTime +2 week"));
                } else {
                    $startTime = date('Y-m-d', strtotime("$startTime +2 week"));
                    $endTime = first_day_of_month(date('Y-m-d', strtotime("{$startTime} +1 month")));
                }
                break;
            case 5: // 每月兩次 第幾周的星期幾
                $month = explode('#', $date)[0];
                $time = explode('#', $date)[1];
                $startTime = first_monday_of_month($month . '-1');
                if ($time == 1) {
                    $endTime = date('Y-m-d', strtotime("$startTime +2 week"));
                } else {
                    $startTime = date('Y-m-d', strtotime("$startTime +2 week"));
                    $endTime = first_day_of_month(date('Y-m-d', strtotime("$startTime +1 month")));
                }
                break;
            case 9:
            default:
                $startTime = date('Y-m-d ', strtotime($date));
                $endTime = date('Y-m-d', strtotime("$startTime +1 day"));
        }

        if ($logSql) {
            log_message('info', "[SQL] 時間區間: {$startTime} ~ {$endTime}");
        }

        // 取出時間區間內這個巡檢報表所用的電子表單版本
        $fmd0101s = $this->db->table('pad01')
            ->select('fmd0101')
            ->join('fmd07', 'fmd0701=pad0102')
            ->join('fmd01', 'fmd0101=fmd0702')
            ->where('fmd0106', $fmd01->fmd0106)
            ->where("pad0109 >=", $startTime)
            ->where("pad0109 <", $endTime)
            ->where("pad0113", 1)
            ->groupBy('fmd0101')
            ->get()
            ->getResult();
        if ($logSql) {
            log_message('info', '[SQL] 查詢電子表單版本: ' . (string)$this->db->getLastQuery());
        }

        if (!$fmd0101s) {
            $fmd0101s = $this->db->table('fmd01')
                ->select('fmd0101')
                ->where('fmd0106', $fmd01->fmd0106)
                ->where('fmd0108', 2)
                ->get()
                ->getResult();
            if ($logSql) {
                log_message('info', '[SQL] 查詢備用表單版本: ' . (string)$this->db->getLastQuery());
            }
        }

        $this->table = ($fmd01->fmd0106 < 10) ? 'iso0' . $fmd01->fmd0106 : 'iso' . $fmd01->fmd0106;

        if (!$reportMaster = $this->getMasterByDate($date)) {
            $reportMaster = new \stdClass();
            $reportMaster->date = $date;
            $reportMaster->log = '';
        }

        // 假如已經產生過，而且是有班別的，載入之前的版本
        if (isset($reportMaster->id) && $fmd0203s) {
            $reportDetail = $this->getDetailByMaster($reportMaster->id);
        } else {
            $reportMaster->fmd0101 = end($fmd0101s)->fmd0101;
            $reportDetail = $this->buildReportDetail($reportMaster->fmd0101);
        }

        // 從報表主檔檢查歲修停車管理
        $fmd30Model = model('Fmd30Model');
        $reportMaster->fmd3001 = false;
        if ($fmd30s = $fmd30Model->getByFmd0101($reportMaster->fmd0101)) {
            foreach ($fmd30s as $fmd30) {
                $fmd3003Real = strtotime($fmd30->fmd3003 . ' 00:00:00');
                $fmd3004Real = strtotime($fmd30->fmd3004 . ' 23:59:59');
                $dateReal = strtotime($date . ' 00:00:00') + 999;
                if (($fmd3003Real < $dateReal) && ($dateReal < $fmd3004Real)) {
                    $reportMaster->fmd3001 = $fmd30->fmd3001;
                    break;
                }
            }
        }

        // 依照選取的班別查出對應班別的設定紀錄
        if ($fmd0203s) {
            $fmd02s = $this->db->table('fmd02')
                ->where('fmd0202', $reportMaster->fmd0101)
                ->where("fmd0203 in ({$fmd0203s})")
                ->get()
                ->getResult();
        } else {
            $fmd02 = new \stdClass();
            $fmd02->fmd0203 = '1';
            $fmd02s = [$fmd02];
        }

        // 記錄開關群組狀態
        $switches = [];

        // 取出這個時段這個巡檢報表所有的巡檢紀錄
        $builder = $this->db->table('pad01')
            ->select('fmd0101,fmd0701,pad0101,pad0105,pad0107,pad0109')
            ->join('fmd07', 'fmd0701=pad0102')
            ->join('fmd01', 'fmd0101=fmd0702')
            ->where('fmd0106', $fmd01->fmd0106)
            ->where("pad0109 >=", $startTime)
            ->where("pad0109 <", $endTime)
            ->where("pad0113", 1);

        if ($fmd0203s) {
            $builder->select('fmd0203')
                ->join('fmd02', 'fmd0201=pad0105')
                ->where("fmd0203 in ({$fmd0203s})");
        }

        $pad01s = $builder->orderBy('pad0109')->get()->getResult();
        if ($logSql) {
            log_message('info', '[SQL] 查詢巡檢紀錄: ' . (string)$this->db->getLastQuery());
            log_message('info', '[SQL] 巡檢紀錄筆數: ' . count($pad01s));
        }

        // 批次預載入：收集所有 fmd0601 ID，一次查完
        $allFmd0601s = [];
        foreach ($pad01s as $pad01) {
            if ($rawData = json_decode($pad01->pad0107, true)) {
                foreach ($rawData as $data) {
                    $allFmd0601s[$data['fmd0601']] = true;
                }
            }
        }
        if ($allFmd0601s) {
            $fmd0601Ids = implode(',', array_keys($allFmd0601s));

            // 預載入 fieldDefine
            $rows = $this->db->query("SELECT fmd0601, fmd0409, fmd0503, fmd0616, fmd0617 FROM fmd06 JOIN fmd04 ON fmd0401=fmd0603 JOIN fmd05 ON fmd0501=fmd0604 WHERE fmd0601 IN ({$fmd0601Ids})")->getResult();
            foreach ($rows as $row) {
                $cached = new \stdClass();
                $cached->fmd0409 = $row->fmd0409;
                $cached->fmd0503 = $row->fmd0503;
                $cached->fmd0616 = $row->fmd0616;
                $cached->fmd0617 = $row->fmd0617;
                $this->fieldDefineCache[$row->fmd0601] = $cached;
            }

            // 預載入 fmd06（開關欄位用）
            $rows = $this->db->query("SELECT fmd0601, fmd0608, fmd0618 FROM fmd06 WHERE fmd0601 IN ({$fmd0601Ids})")->getResult();
            foreach ($rows as $row) {
                $cached1 = new \stdClass();
                $cached1->fmd0608 = $row->fmd0608;
                $this->fmd06Cache[$row->fmd0601 . '_fmd0608'] = $cached1;

                $cached2 = new \stdClass();
                $cached2->fmd0608 = $row->fmd0608;
                $cached2->fmd0618 = $row->fmd0618;
                $this->fmd06Cache[$row->fmd0601 . '_fmd0608,fmd0618'] = $cached2;
            }

            // 預載入跨版本對應
            if (isset($reportMaster->fmd0101)) {
                $rows = $this->db->query("SELECT fmd0617, fmd0409 FROM fmd04 JOIN fmd01 ON fmd0402=fmd0101 JOIN fmd06 ON fmd0401=fmd0603 WHERE fmd0101={$reportMaster->fmd0101}")->getResult();
                foreach ($rows as $row) {
                    $cached = new \stdClass();
                    $cached->fmd0409 = $row->fmd0409;
                    $this->fmd04CrossCache[$reportMaster->fmd0101 . '_' . $row->fmd0617] = $cached;
                }
            }

            if ($logSql) {
                log_message('info', '[SQL] 預載入完成: fieldDefine=' . count($this->fieldDefineCache) . ', fmd06=' . count($this->fmd06Cache) . ', fmd04Cross=' . count($this->fmd04CrossCache));
            }
        }

        foreach ($pad01s as $pad01) {
            if ($rawData = json_decode($pad01->pad0107, true)) {
                $powerOff = null;
                // 先查有沒有開關欄位
                foreach ($rawData as $data) {
                    if ($data['fmd0606'] == 8) {
                        $fmd06 = $this->getFmd06Cached($data['fmd0601'], 'fmd0608');
                        $opt = explode(PHP_EOL, $fmd06->fmd0608);
                        $value = count($data['value']) ? array_keys($data['value'][0])[0] : '';
                        if (array_key_exists(1, $opt) && $value == $opt[1]) {
                            $powerOff = true;
                            break;
                        }
                    }
                }

                $sno = 1;
                foreach ($rawData as $data) {
                    $itemInfo = $this->getFieldDefine($data['fmd0601']);
                    if (!$itemInfo) {
                        continue;
                    }

                    if ($pad01->fmd0101 == $reportMaster->fmd0101) {
                        if (!isset($reportDetail[$itemInfo->fmd0409])) {
                            continue;
                        }
                        $row = $reportDetail[$itemInfo->fmd0409];
                    } else {
                        $fmd04 = $this->getFmd04CrossVersion($reportMaster->fmd0101, $itemInfo->fmd0617);
                        if ($fmd04) {
                            if (!isset($reportDetail[$fmd04->fmd0409])) {
                                continue;
                            }
                            $row = $reportDetail[$fmd04->fmd0409];
                        } else {
                            continue;
                        }
                    }

                    if ($fmd0203s) {
                        $fnData = 'data' . $pad01->fmd0203 . '_' . $itemInfo->fmd0503;
                        $fnMemo = 'memo' . $pad01->fmd0203 . '_' . $itemInfo->fmd0503;
                        $fnErr = 'err' . $pad01->fmd0203 . '_' . $itemInfo->fmd0503;
                        $fnSrc = 'src' . $pad01->fmd0203 . '_' . $itemInfo->fmd0503;
                        $fnSno = 'sno' . $pad01->fmd0203 . '_' . $itemInfo->fmd0503;
                        $fnFile = 'file' . $pad01->fmd0203 . '_' . $itemInfo->fmd0503;
                    } else {
                        $fnData = 'data1_' . $itemInfo->fmd0503;
                        $fnMemo = 'memo1_' . $itemInfo->fmd0503;
                        $fnErr = 'err1_' . $itemInfo->fmd0503;
                        $fnSrc = 'src1_' . $itemInfo->fmd0503;
                        $fnSno = 'sno1_' . $itemInfo->fmd0503;
                        $fnFile = 'file1_' . $itemInfo->fmd0503;
                    }

                    // 把巡檢結果記錄到對應欄位
                    if ($data['fmd0606'] == 5) { // 多選
                        $arr = [];
                        $err = [];
                        for ($i = 0; $i < count($data['value']); $i++) {
                            $arr[] = array_keys($data['value'][$i])[0];
                            $err[] = (array_values($data['value'][$i])[0]) ? 1 : 0;
                        }
                        $row->$fnData = implode(',', $arr);
                        $row->$fnErr = in_array(1, $err) ? 1 : 0;
                    } else {
                        $row->$fnData = count($data['value']) ? array_keys($data['value'][0])[0] : '';
                        $row->$fnErr = (count($data['value']) && array_values($data['value'][0])[0]) ? 1 : 0;
                    }

                    // 設備停機處理
                    if ($powerOff === true) {
                        if ($data['fmd0606'] != 8) {
                            $row->$fnErr = 0;
                            $row->$fnData = ' ';
                        }
                    }

                    // 開關類型記錄
                    if ($data['fmd0606'] == 8) {
                        $fmd06 = $this->getFmd06Cached($data['fmd0601'], 'fmd0608,fmd0618');
                        if ($fmd06 && $fmd06->fmd0618) {
                            $s = explode("\n", $fmd06->fmd0608);
                            if (isset($switches[$fmd06->fmd0618])) {
                                $switch = $switches[$fmd06->fmd0618];
                                if ($fmd0203s) {
                                    if (isset($switch->data[$pad01->fmd0203])) {
                                        $switch->data[$pad01->fmd0203] = ($switch->data[$pad01->fmd0203] || ($row->$fnData == $s[0]));
                                    } else {
                                        $switch->data[$pad01->fmd0203] = ($row->$fnData == $s[0]);
                                    }
                                } else {
                                    $switch->data = ($switch->data || ($row->$fnData == $s[0]));
                                }
                            } else {
                                $switch = new \stdClass();
                                $switch->id = $fmd06->fmd0618;
                                $switch->on = $s[0];
                                $switch->off = $s[1] ?? '';
                                if ($fmd0203s) {
                                    $switch->data = [];
                                    $switch->data[$pad01->fmd0203] = ($row->$fnData == $s[0]);
                                } else {
                                    $switch->data = ($row->$fnData == $s[0]);
                                }
                                $switches[$fmd06->fmd0618] = $switch;
                            }
                        }
                    }

                    // 非必填欄位處理
                    if ($itemInfo->fmd0616 == 1 && $row->$fnData === '') {
                        $row->$fnData = ' ';
                    }

                    $row->$fnMemo = isset($data['info']) ? $data['info'] : '';
                    $row->$fnSrc = $pad01->pad0101;
                    $row->$fnSno = $sno;
                    $row->$fnFile = array_key_exists('files', $data) ? count($data['files']) : 0;
                    $sno++;
                }
            }
        }

        // 找出這個報表的電子表單
        $fmd07Model = model('Fmd07Model');
        $fmd07s = $fmd07Model->getByFmd0101($reportMaster->fmd0101);

        // 針對班別限定做記號
        foreach ($fmd07s as $fmd07) {
            if ($fmd07->fmd0704) {
                foreach ($fmd02s as $fmd02) {
                    if (substr($fmd07->fmd0704, ($fmd02->fmd0203 - 1), 1) === '0') {
                        $fmd06s = $this->db->table('fmd06')
                            ->select('fmd0409,fmd0503')
                            ->join('fmd04', 'fmd0401=fmd0603')
                            ->join('fmd05', 'fmd0501=fmd0604')
                            ->where('fmd0610', $fmd07->fmd0701)
                            ->get()
                            ->getResult();
                        foreach ($fmd06s as $fmd06) {
                            if (isset($reportDetail[$fmd06->fmd0409])) {
                                $row = $reportDetail[$fmd06->fmd0409];
                                $fnSrc = 'src' . $fmd02->fmd0203 . '_' . $fmd06->fmd0503;
                                if (!isset($row->$fnSrc)) {
                                    $row->$fnSrc = 0;
                                    $fnErr = 'err' . $fmd02->fmd0203 . '_' . $fmd06->fmd0503;
                                    $row->$fnErr = 0;
                                }
                            }
                        }
                    }
                }
            }
        }

        // 針對沒有加入電子表單的做記號
        $fmd06s = $this->db->table('fmd06')
            ->select('fmd0409,fmd0503')
            ->join('fmd04', 'fmd0401=fmd0603')
            ->join('fmd05', 'fmd0501=fmd0604')
            ->where('fmd0602', $reportMaster->fmd0101)
            ->where('fmd0610 is null')
            ->get()
            ->getResult();

        foreach ($fmd06s as $fmd06) {
            foreach ($fmd02s as $fmd02) {
                if (isset($reportDetail[$fmd06->fmd0409])) {
                    $row = $reportDetail[$fmd06->fmd0409];
                    $fnSrc = 'src' . $fmd02->fmd0203 . '_' . $fmd06->fmd0503;
                    $row->$fnSrc = 0;
                    $fnErr = 'err' . $fmd02->fmd0203 . '_' . $fmd06->fmd0503;
                    $row->$fnErr = 0;
                }
            }
        }

        // 統計每個班別的應檢數量
        $check = [];
        foreach ($fmd02s as $fmd02) {
            $check[$fmd02->fmd0203] = 0;
        }

        // 判斷漏檢與統計應檢數量
        $fmd05Model = model('Fmd05Model');
        $fmd05s = $fmd05Model->getByFmd0101($reportMaster->fmd0101);

        foreach ($reportDetail as $row) {
            foreach ($fmd02s as $fmd02) {
                foreach ($fmd05s as $fmd05) {
                    $fnData = 'data' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503;
                    $fnErr = 'err' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503;
                    $fnSrc = 'src' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503;

                    $isMiss = false;
                    if (!$reportMaster->fmd3001) {
                        if (isset($row->$fnSrc)) {
                            if ($row->$fnSrc === null) {
                                $isMiss = true;
                            } elseif ($row->$fnSrc > 0 && (!isset($row->$fnData) || $row->$fnData === '')) {
                                $isMiss = true;
                            }
                        } else {
                            $isMiss = true;
                        }
                    }

                    if ($isMiss) {
                        $row->$fnErr = 2;
                    }

                    if (isset($row->$fnSrc)) {
                        if ($row->$fnSrc > 0) {
                            $check[$fmd02->fmd0203]++;
                        }
                    } else {
                        $check[$fmd02->fmd0203]++;
                    }
                }
            }
        }

        // 檢查開關群組 Summary
        if (isset($reportMaster->summary) && $reportMaster->summary) {
            $summary = json_decode($reportMaster->summary);
        } else {
            $summary = new \stdClass();
        }

        if ($fmd0203s) {
            $summary->fmd02 = true;
            foreach ($fmd02s as $fmd02) {
                if (!isset($summary->{$fmd02->fmd0204})) {
                    $summary->{$fmd02->fmd0204} = new \stdClass();
                }
                $summary->{$fmd02->fmd0204}->switch = [];
                foreach ($switches as $switch) {
                    if (!isset($switch->data[$fmd02->fmd0203])) {
                        $summary->{$fmd02->fmd0204}->switch[] = lang('Model.on_off_group') . "[{$switch->id}]" . lang('Model.model_all') . "{$switch->off}。";
                    }
                }
                if (empty($summary->{$fmd02->fmd0204}->switch)) {
                    unset($summary->{$fmd02->fmd0204}->switch);
                }
            }
        } else {
            $summary->switch = [];
            foreach ($switches as $switch) {
                if (!$switch->data) {
                    $summary->switch[] = lang('Model.on_off_group') . "[{$switch->id}]" . lang('Model.model_all') . "{$switch->off}。";
                }
            }
            if (empty($summary->switch)) {
                unset($summary->switch);
            }
        }
        $reportMaster->summary = json_encode($summary);

        // 重新抓出所有班別做統計
        if (isset($reportMaster->id)) {
            if ($fmd0203s) {
                $fmd02s = $this->db->table('fmd02')
                    ->where('fmd0202', $reportMaster->fmd0101)
                    ->get()
                    ->getResult();
            }
        }

        // 統計漏檢與異常
        $err = [];
        $miss = [];
        foreach ($fmd02s as $fmd02) {
            $err[$fmd02->fmd0203] = 0;
            $miss[$fmd02->fmd0203] = 0;
        }

        foreach ($reportDetail as $row) {
            $row->error_count = 0;
            $row->miss_count = 0;
            foreach ($fmd02s as $fmd02) {
                foreach ($fmd05s as $fmd05) {
                    $fnErr = 'err' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503;
                    if (isset($row->$fnErr)) {
                        switch ($row->$fnErr) {
                            case 1:
                                $row->error_count++;
                                $err[$fmd02->fmd0203]++;
                                break;
                            case 2:
                                $row->miss_count++;
                                $miss[$fmd02->fmd0203]++;
                                break;
                        }
                    }
                }
            }
        }

        // 紀錄表頭統計數字
        $reportMaster->error_count = 0;
        $reportMaster->miss_count = 0;
        $reportMaster->check_count = 0;
        foreach ($fmd02s as $fmd02) {
            $reportMaster->error_count += $err[$fmd02->fmd0203];
            $reportMaster->miss_count += $miss[$fmd02->fmd0203];
            if (array_key_exists($fmd02->fmd0203, $check)) {
                $reportMaster->check_count += $check[$fmd02->fmd0203];
            }
            if ($fmd0203s) {
                $fnErr = 'error' . $fmd02->fmd0203;
                $fnMiss = 'miss' . $fmd02->fmd0203;
                $fnCheck = 'check' . $fmd02->fmd0203;
                $reportMaster->$fnErr = $err[$fmd02->fmd0203];
                $reportMaster->$fnMiss = $miss[$fmd02->fmd0203];
                if (array_key_exists($fmd02->fmd0203, $check)) {
                    $reportMaster->$fnCheck = $check[$fmd02->fmd0203];
                }
            }
        }

        // 記錄產生日期與人員
        $reportMaster->user_id = $this->currentUser->sys0101 ?? 1;
        $reportMaster->datetime = date('Y-m-d H:i:s');

        // 不要將 fmd3001 存入
        if (isset($reportMaster->fmd3001)) {
            unset($reportMaster->fmd3001);
        }

        // 存檔
        if ($logSql) {
            log_message('info', '[SQL] 開始存檔 table: ' . $this->table);
            log_message('info', '[SQL] reportMaster: ' . json_encode($reportMaster, JSON_UNESCAPED_UNICODE));
            log_message('info', '[SQL] reportDetail count: ' . count($reportDetail));
        }

        $this->db->transStart();

        if (isset($reportMaster->id)) {
            $this->db->table($this->table)
                ->where('id', $reportMaster->id)
                ->update((array)$reportMaster);
            if ($logSql) {
                log_message('info', '[SQL] UPDATE master: ' . (string)$this->db->getLastQuery());
            }
            if (!$fmd0203s) {
                $this->db->table($this->table . 'a')
                    ->where('master_id', $reportMaster->id)
                    ->delete();
                if ($logSql) {
                    log_message('info', '[SQL] DELETE detail: ' . (string)$this->db->getLastQuery());
                }
            }
        } else {
            $this->db->table($this->table)->insert((array)$reportMaster);
            if ($logSql) {
                log_message('info', '[SQL] INSERT master: ' . (string)$this->db->getLastQuery());
            }
            $reportMaster->id = $this->db->insertID();
        }

        $detailIndex = 0;
        foreach ($reportDetail as $detail) {
            if (isset($detail->id)) {
                $this->db->table($this->table . 'a')
                    ->where('id', $detail->id)
                    ->update((array)$detail);
                if ($logSql && $detailIndex < 3) {
                    log_message('info', '[SQL] UPDATE detail[' . $detailIndex . ']: ' . (string)$this->db->getLastQuery());
                }
            } else {
                $detail->master_id = $reportMaster->id;
                $this->db->table($this->table . 'a')->insert((array)$detail);
                if ($logSql && $detailIndex < 3) {
                    log_message('info', '[SQL] INSERT detail[' . $detailIndex . ']: ' . (string)$this->db->getLastQuery());
                }
            }
            $detailIndex++;
        }
        if ($logSql && $detailIndex > 3) {
            log_message('info', '[SQL] ... 省略後續 detail SQL (共 ' . $detailIndex . ' 筆)');
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            $this->message->add(lang('Model.update_err_hint'));
            if ($logSql) {
                log_message('error', '[SQL] Transaction FAILED');
                log_message('info', '========== generateReport END (FAILED) ==========');
            }
            return false;
        }

        if ($logSql) {
            log_message('info', '[SQL] Transaction SUCCESS, reportMaster->id: ' . $reportMaster->id);
            log_message('info', '========== generateReport END ==========');
        }

        return $reportMaster->id;
    }

    /**
     * 建立報表明細
     */
    private function buildReportDetail(int $fmd0101): array
    {
        $fmd03Model = model('Fmd03Model');
        $fmd03s = $fmd03Model->getByFmd0101($fmd0101);

        $fmd04Model = model('Fmd04Model');
        $fmd04s = $fmd04Model->getByFmd0101($fmd0101, 'fmd0401,fmd0404,fmd0409,fmd0410,fmd0303');

        $rows = [];
        $columns = count($fmd03s);
        $lastRow = 0;

        foreach ($fmd04s as $node) {
            if (array_key_exists($node->fmd0409, $rows)) {
                $data = $rows[$node->fmd0409];
            } else {
                $data = new \stdClass();
                $data->line = $node->fmd0409;
                $rows[$node->fmd0409] = $data;
            }

            $fn1 = "item{$node->fmd0303}_name";
            $fn2 = "item{$node->fmd0303}_child";

            $data->$fn1 = $node->fmd0404;
            $data->$fn2 = $node->fmd0410;

            if ($lastRow < $node->fmd0409) {
                $lastRow = $node->fmd0409;
            }
        }

        // 把項目名稱空格填滿
        for ($i = 0; $i <= $lastRow; $i++) {
            if (isset($rows[$i])) {
                $data = $rows[$i];
                for ($j = 1; $j <= $columns; $j++) {
                    $fn2 = "item{$j}_child";
                    if (isset($data->$fn2) && $data->$fn2 > 1) {
                        $fn1 = "item{$j}_name";
                        for ($k = $i + 1; $k < $i + $data->$fn2; $k++) {
                            if (isset($rows[$k])) {
                                $rows[$k]->$fn1 = $data->$fn1;
                            }
                        }
                    }
                }
            }
        }

        return $rows;
    }

    /**
     * 取得報表 HTML
     */
    public function getReport(int $fmd0101, int $reportId): string|false
    {
        $fmd01Model = model('Fmd01Model');
        $fmd02Model = model('Fmd02Model');
        $fmd03Model = model('Fmd03Model');
        $fmd05Model = model('Fmd05Model');
        $fmd07Model = model('Fmd07Model');

        $fmd01 = $fmd01Model->find($fmd0101);
        $this->setTableName($fmd01);

        $reportMaster = $this->getMaster($reportId);
        if (!$reportMaster) {
            $this->message->add(lang('Model.not_data_hint2'));
            return false;
        }

        $reportDetail = $this->getDetailByMaster($reportMaster->id);
        $commentData = [];

        $commentFmd02Cache = [];
        $commentFmd07Cache = [];
        if ($comments = json_decode($reportMaster->comments)) {
            foreach ($comments as $fmd0201 => $fmd0201v) {
                if (!isset($commentFmd02Cache[$fmd0201])) {
                    $commentFmd02Cache[$fmd0201] = $fmd02Model->getBy(['fmd0201' => $fmd0201], 1);
                }
                $fmd02s = $commentFmd02Cache[$fmd0201];
                if ($fmd02s) {
                    $fmd0203 = $fmd02s->fmd0203;
                } else {
                    $fmd0203 = 'all';
                }
                foreach ($fmd0201v as $fmd0701v => $fmd0701s) {
                    if (!isset($commentFmd07Cache[$fmd0701v])) {
                        $commentFmd07Cache[$fmd0701v] = $fmd07Model->getBy(['fmd0701' => $fmd0701v], 1);
                    }
                    $fmd07s = $commentFmd07Cache[$fmd0701v];
                    if ($fmd07s) {
                        $fmd0707 = (empty($fmd07s->fmd0707)) ? $fmd07s->fmd0701 : $fmd07s->fmd0707;
                        $commentData[$fmd0203][$fmd0707] = count($fmd0701s);
                    }
                }
            }
        }

        if (is_null($reportMaster) || is_null($reportDetail)) {
            $this->message->add(lang('Model.not_data_hint2'));
            return false;
        }

        $fmd03s = $fmd03Model->getByFmd0101($fmd0101);
        $fmd05s = $fmd05Model->getByFmd0101($fmd0101);

        // 表頭
        $fmd02s = $fmd02Model->getByFmd0101($fmd0101);
        if ($fmd02s) {
            $fieldCount = count($fmd05s);
            $rowspan = ' rowspan="2"';
            $header = "<tr><th{$rowspan}>" . lang('Model.model_f') . "</th>";
            foreach ($fmd03s as $d) {
                $header .= "<th{$rowspan}>{$d->fmd0304}</th>";
            }
            if ($fieldCount == 1) {
                $colspan = " colspan=\"" . count($fmd02s) . "\"";
                foreach ($fmd05s as $d) {
                    $header .= "<th{$colspan}>{$d->fmd0504}</th>";
                }
                $header .= '</tr><tr>';
            }
            if ($fieldCount > 1) {
                $colspan = " colspan=\"{$fieldCount}\"";
            } else {
                $colspan = "";
            }
            foreach ($fmd02s as $fmd02) {
                $header .= "<th{$colspan}>{$fmd02->fmd0204}</th>";
            }
            if ($fieldCount > 1) {
                $header .= '</tr><tr>';
                foreach ($fmd02s as $fmd02) {
                    foreach ($fmd05s as $d) {
                        $header .= "<th>{$d->fmd0504}</th>";
                    }
                }
            }
            $header .= '</tr>';
        } else {
            $header = '<tr><th>' . lang('Model.model_f') . '</th>';
            foreach ($fmd03s as $d) {
                $header .= "<th>{$d->fmd0304}</th>";
            }
            foreach ($fmd05s as $d) {
                $header .= "<th>{$d->fmd0504}</th>";
            }
            $header .= '</tr>';
        }

        // 表身
        $body = '';
        $err = [];
        $miss = [];
        $check = [];

        if ($fmd02s) {
            foreach ($fmd02s as $fmd02) {
                foreach ($fmd05s as $fmd05) {
                    $err[] = 0;
                    $miss[] = 0;
                    $check[] = 0;
                }
            }
        } else {
            foreach ($fmd05s as $fmd05) {
                $err[] = 0;
                $miss[] = 0;
                $check[] = 0;
            }
            $fmd02s = [];
            $fmd02 = new \stdClass();
            $fmd02->fmd0201 = '';
            $fmd02->fmd0203 = '1';
            $fmd02s[] = $fmd02;
        }

        // 取得 fmd0701 ID 列表
        $fmd0701IdList = [];
        $_fmd0101 = '';
        foreach ($fmd05s as $key => $fmd05) {
            $sql = 'SELECT fmd0101, fmd0106, fmd0409, fmd0504, fmd0601, fmd0603, fmd0604, fmd0606, fmd0701, fmd0703, ent1004 FROM `fmd01`, `fmd04`, `fmd05`, `fmd06`, `fmd07`, `ent10` WHERE fmd0101 = fmd0602 AND fmd0401 = fmd0603 AND fmd0604 = fmd0501 AND fmd0610 = fmd0701 AND ent1001 = fmd0102 AND fmd0501 = ' . $fmd05->fmd0501 . ' ORDER BY fmd0409 ASC';
            if ($result = $this->db->query($sql)->getResult()) {
                foreach ($result as $val) {
                    $fmd0701IdList[] = $val;
                    $_fmd0101 = $val->fmd0101;
                }
            }
        }

        $colCount = count($fmd03s);
        $checkedCount = [];
        $enableColumnRate = true;
        $fmd07Cache = [];

        foreach ($reportDetail as $row) {
            $body .= "<tr><td>" . ($row->line + 1) . "</td>";

            // 巡檢項目名稱
            foreach ($fmd03s as $fmd03) {
                $fnItemName = 'item' . $fmd03->fmd0303 . '_name';
                $fnItemChild = 'item' . $fmd03->fmd0303 . '_child';
                if (isset($row->$fnItemChild) && $row->$fnItemChild !== null) {
                    if ($row->$fnItemChild > 1) {
                        $body .= "<td rowspan=\"{$row->$fnItemChild}\">{$row->$fnItemName}</td>";
                    } elseif ($row->$fnItemChild == 0 && $fmd03->fmd0303 < $colCount) {
                        $body .= "<td colspan='" . ($colCount - $fmd03->fmd0303 + 1) . "'>{$row->$fnItemName}</td>";
                    } else {
                        $body .= "<td>" . ($row->$fnItemName ?? '') . "</td>";
                    }
                }
            }

            // 巡檢項目欄位
            $i = 0;
            foreach ($fmd02s as $fmd02) {
                if (!isset($fmd02->fmd0201) || empty($fmd02->fmd0201)) {
                    $fmd02->fmd0201 = 'all';
                }
                if (!isset($checkedCount[$fmd02->fmd0201]['err'])) {
                    $checkedCount[$fmd02->fmd0201]['err'] = 0;
                    $checkedCount[$fmd02->fmd0201]['total'] = 0;
                    $checkedCount[$fmd02->fmd0201]['fmd05_col'] = 0;
                }

                foreach ($fmd05s as $key => $fmd05) {
                    $checkedCount[$fmd02->fmd0201]['total'] = count($fmd0701IdList);
                    $checkedCount[$fmd02->fmd0201]['fmd05_col'] = $key;

                    $_tmp = new \stdClass();
                    $_tmp->fmd0101 = $_fmd0101;
                    $_tmp->fmd0201 = $fmd02->fmd0201;
                    $_tmp->fmd0501 = $fmd05->fmd0501;
                    $_tmp->master_id = $row->master_id;
                    $_tmp->line = $row->line;
                    $_tmpJson = json_encode($_tmp);

                    if ($fmd02->fmd0201 == 'all') {
                        $enableColumnRate = false;
                    } else {
                        $fnCheck = 'check' . $fmd02->fmd0203;
                        if (isset($reportMaster->$fnCheck)) {
                            $check[$i] = $reportMaster->$fnCheck;
                        }
                    }

                    $fnData = 'data' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503;
                    $fnMemo = 'memo' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503;
                    $fnErr = 'err' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503;
                    $fnSrc = 'src' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503;
                    $fnFile = 'file' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503;

                    $errVal = $row->$fnErr ?? 0;
                    $srcVal = $row->$fnSrc ?? null;
                    $dataVal = $row->$fnData ?? null;
                    $memoVal = $row->$fnMemo ?? '';
                    $fileVal = $row->$fnFile ?? 0;

                    if ($errVal == 1) {
                        $err[$i]++;
                        $checkedCount[$fmd02->fmd0201]['err']++;
                        if ($srcVal) {
                            $body .= '<td class="bg-red a" onclick="show_rawdata(' . $srcVal . ',' . $row->master_id . ')">';
                        } else {
                            $body .= '<td class="bg-red">';
                        }
                    } elseif ($errVal == 2) {
                        $miss[$i]++;
                        $body .= '<td class="bg-gray a" onclick="show_rawdata_miss(\'' . urlencode($_tmpJson) . '\')">';
                    } elseif ($fileVal) {
                        $body .= '<td class="a" onclick="show_rawdata(' . $srcVal . ',' . $row->master_id . ')">';
                    } else {
                        if (empty($srcVal)) {
                            $body .= '<td class="bg-white">';
                        } else {
                            $body .= '<td class="a" onclick="show_rawdata(' . $srcVal . ',' . $row->master_id . ')">';
                        }
                    }

                    if ($dataVal !== null) {
                        $body .= $dataVal;
                    }
                    $body .= "<br>";

                    if ($fileVal) {
                        $body .= "<i class=\"fa fa-picture-o\"></i>";
                    }
                    if ($memoVal) {
                        if (empty($srcVal)) {
                            $body .= "<span>{$memoVal}</span>";
                        } else {
                            $body .= "<i class=\"fa fa-align-left\"></i>";
                        }
                    }

                    // 註記圖示
                    if ($commentData && isset($fmd0701IdList[$row->line])) {
                        $fmd0701 = $fmd0701IdList[$row->line]->fmd0701;
                        if (!isset($fmd07Cache[$fmd0701])) {
                            $fmd07Cache[$fmd0701] = $fmd07Model->getBy(['fmd0701' => $fmd0701], 1);
                        }
                        $fmd07s = $fmd07Cache[$fmd0701];
                        if ($fmd07s) {
                            $fmd0203 = ($fmd02->fmd0201 == 'all') ? 'all' : $fmd02->fmd0203;
                            $fmd0707 = (empty($fmd07s->fmd0707)) ? $fmd07s->fmd0701 : $fmd07s->fmd0707;
                            if (isset($commentData[$fmd0203][$fmd0707]) && $commentData[$fmd0203][$fmd0707]) {
                                $body .= "<i class=\"fa fa-pencil\"></i>";
                            }
                        }
                    }

                    $body .= '</td>';
                    $i++;
                }
            }
            $body .= '</tr>';
        }

        // 表尾統計
        $colspan = count($fmd03s) + 1;
        $footer = "<tr class=\"bg-red text-right\"><td colspan='{$colspan}'>" . lang('Model.model_err_count_f') . "</td>";
        foreach ($err as $item) {
            $footer .= "<td>{$item}</td>";
        }
        $footer .= '</tr>';

        // 異常率
        $errRateAvg = 0;
        if ($enableColumnRate) {
            $errRate = [];
            $footer .= "<tr class=\"bg-red text-right\"><td colspan='{$colspan}'>" . lang('Model.model_err_rate_f') . "</td>";
            foreach ($checkedCount as $fmd0201 => $itemCount) {
                $denominator = ($itemCount['total'] == 0) ? 99999 : $itemCount['total'];
                $_item = round($itemCount['err'] / $denominator * 100, 1);
                $_colspan = $itemCount['fmd05_col'] + 1;
                $footer .= "<td colspan='{$_colspan}'>{$_item}%</td>";
                $errRate[] = $_item;
            }
            $errRateCount = count($errRate);
            if ($errRateCount) {
                $errRateAvg = round(array_sum($errRate) / $errRateCount, 1);
            }
            $footer .= '</tr>';
        } else {
            if ($reportMaster->check_count > 0) {
                $errRateAvg = round($reportMaster->error_count / $reportMaster->check_count * 100, 1);
            }
        }

        // 漏檢數
        $footer .= "<tr class=\"bg-gray text-right\"><td colspan='{$colspan}'>" . lang('Model.model_miss_count_f') . "</td>";
        foreach ($miss as $item) {
            $footer .= "<td>{$item}</td>";
        }
        $footer .= '</tr>';

        // 漏檢率
        $missRateAvg = 0;
        if ($enableColumnRate) {
            $missRate = [];
            $footer .= "<tr class=\"bg-gray text-right\"><td colspan='{$colspan}'>" . lang('Model.model_miss_rate_f') . "</td>";
            foreach ($miss as $key => $item) {
                $denominator = ($check[$key] == 0) ? 99999 : $check[$key];
                if ($denominator != 99999) {
                    $denominator = $denominator / count($fmd05s);
                }
                $_item = round($item / $denominator * 100, 1);
                $footer .= "<td>{$_item}%</td>";
                $missRate[] = $_item;
            }
            $missRateCount = count($missRate);
            if ($missRateCount) {
                $missRateAvg = round(array_sum($missRate) / $missRateCount, 1);
            }
            $footer .= '</tr>';
        } else {
            if ($reportMaster->check_count > 0) {
                $missRateAvg = round($reportMaster->miss_count / $reportMaster->check_count * 100, 1);
            }
        }

        $colspan = count($fmd03s) + count($err);
        $footer .= "<tr class=\"bg-white text-right\"><td colspan='{$colspan}'>" . lang('Model.model_err_count_f') . "</td>";
        $footer .= "<td>{$reportMaster->error_count}<br>{$errRateAvg}%</td>";
        $footer .= '</tr>';
        $footer .= "<tr class=\"bg-white text-right\"><td colspan='{$colspan}'>" . lang('Model.model_miss_count_f') . "</td>";
        $footer .= "<td>{$reportMaster->miss_count}<br>{$missRateAvg}%</td>";
        $footer .= '</tr>';

        // Summary
        $summaryHtml = '';
        if ($summary = $this->renderSummary($reportMaster->summary)) {
            $colspan = count($fmd03s) + count($err) + count($miss) + 1;
            $summaryHtml = "<tfoot><tr class=\"bg-red\"><td colspan='{$colspan}'>{$summary}</td></tr></tfoot>";
        }

        return "<table id=\"patrol_table\" class=\"table table-striped table-bordered dataTable\"><tbody>{$header}{$body}{$footer}</tbody>{$summaryHtml}</table>";
    }

    /**
     * 渲染 Summary
     */
    public function renderSummary($summary): string
    {
        $output = '';
        if ($summary) {
            if (is_string($summary)) {
                $summary = json_decode($summary);
            }
            if (isset($summary->fmd02) && $summary->fmd02) {
                foreach ($summary as $key => $value) {
                    if ($key != 'fmd02') {
                        if (is_object($value)) {
                            $value = json_encode($value);
                        }
                        $s = $this->renderSummary($value);
                        if ($s) {
                            $output .= $key . ':<br>' . $s;
                        }
                    }
                }
            } else {
                foreach ($summary as $key => $value) {
                    if ($key == 'switch') {
                        $output .= '* ' . implode('<br>* ', $value);
                    }
                }
            }
        }
        return $output;
    }

    /**
     * 取得簽核狀態
     */
    public function getApproveState(string $date, int $fmd2101): ?int
    {
        $isoC = $this->db->table($this->table . 'c')
            ->select('c08')
            ->join($this->table, 'id=c02')
            ->where('date', $date)
            ->where('c04', $fmd2101)
            ->get()
            ->getRow();

        return $isoC ? (int)$isoC->c08 : null;
    }

    /**
     * 取得一筆簽核紀錄
     */
    public function getIsoC(int $c01): ?object
    {
        return $this->db->table($this->table . 'c')
            ->where('c01', $c01)
            ->get()
            ->getRow();
    }

    /**
     * 取得完整簽核紀錄
     */
    public function getSignInfo(int $reportId): array
    {
        return $this->db->table($this->table . 'd')
            ->join($this->table . 'c', 'c01=d02')
            ->where('c02', $reportId)
            ->orderBy('d02,d03')
            ->get()
            ->getResult();
    }

    /**
     * 取得等待簽核的報表
     */
    public function getWaitingForApprove(int $fmd2101, int $fmd2204): array
    {
        $sql = "SELECT * FROM {$this->table}c
                JOIN {$this->table} ON id=c02
                WHERE c04={$fmd2101} AND c08={$fmd2204}";
        return $this->db->query($sql)->getResult();
    }

    /**
     * 取得退回清單
     */
    public function getRejectedList(): array
    {
        return $this->db->table($this->table . 'c')
            ->join($this->table, 'id=c02')
            ->where('c08', 101)
            ->get()
            ->getResult();
    }

    /**
     * 送簽報表
     */
    public function sendReport(int $reportId, int $fmd2101, string $memo, object $fmd01): bool
    {
        $isoC = $this->db->table($this->table . 'c')
            ->where('c02', $reportId)
            ->where('c04', $fmd2101)
            ->get()
            ->getRow();

        if ($isoC) {
            if ($isoC->c08 > 0 && $isoC->c08 < 101 && !(($fmd01->fmd0105 == 3 || $fmd01->fmd0105 == 2) && $isoC->c08 == 100)) {
                if ($isoC->c08 == 100) {
                    $this->message->add(lang('Approve.signed_hint1'));
                } else {
                    $this->message->add(lang('Approve.signed_hint2'));
                }
                return false;
            }
            $isoC->c05++;
        } else {
            $iso = $this->getMaster($reportId);
            if (!$iso) {
                $this->message->add(lang('Model.not_data_hint3'));
                return false;
            }

            $fmd20 = $this->db->table('fmd20')
                ->where('fmd2002', $fmd01->fmd0106)
                ->get()
                ->getRow();

            $isoC = new \stdClass();
            $isoC->c02 = $reportId;
            $isoC->c03 = $fmd20->fmd2001;
            $isoC->c04 = $fmd2101;
            $isoC->c05 = 1;
        }

        $isoC->c06 = $this->currentUser->sys0101;
        $isoC->c07 = date('Y-m-d H:i:s');
        $isoC->c08 = 1;

        $isoD = new \stdClass();
        if (isset($isoC->c01)) {
            $isoD->d02 = $isoC->c01;
            $d03Result = $this->db->table($this->table . 'd')
                ->selectMax('d03')
                ->where('d02', $isoC->c01)
                ->get()
                ->getRow();
            $isoD->d03 = ($d03Result->d03 ?? 0) + 1;
        } else {
            $isoD->d03 = 1;
        }
        $isoD->d04 = $isoC->c07;
        $isoD->d05 = $isoC->c06;
        $isoD->d06 = 0;
        $isoD->d07 = $memo;

        $this->db->transStart();

        if (isset($isoC->c01)) {
            $this->db->table($this->table . 'c')
                ->where('c01', $isoC->c01)
                ->update((array)$isoC);
        } else {
            $this->db->table($this->table . 'c')->insert((array)$isoC);
            $isoD->d02 = $this->db->insertID();
        }
        $this->db->table($this->table . 'd')->insert((array)$isoD);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            $this->message->add(lang('Model.update_err_hint'));
            return false;
        }

        return true;
    }

    /**
     * 簽核報表
     */
    public function approveReport(object $fmd01, int $isoCc01, int $result, string $memo): bool
    {
        $isoC = $this->db->table($this->table . 'c')
            ->where('c01', $isoCc01)
            ->get()
            ->getRow();

        if (!$isoC) {
            return false;
        }

        if ($isoC->c08 < 1 || $isoC->c08 > 99) {
            $this->message->add(lang('Approve.signed_hint3'));
            return false;
        }

        $fmd22Model = model('Fmd22Model');
        if (!$fmd22Model->checkUserApproveFlow($fmd01->fmd0106, $isoC->c04, $isoC->c08)) {
            $this->message->add(lang('Approve.signed_hint4'));
            return false;
        }

        if ($result == 1) {
            while ($fmd22Model->checkUserApproveFlow($fmd01->fmd0106, $isoC->c04, $isoC->c08)) {
                $isoC->c08++;
            }
            if ($fmd22Model->getLastStep($fmd01->fmd0106, $isoC->c04) < $isoC->c08) {
                $isoC->c08 = 100;
            }
        } else {
            $isoC->c08 = 101;
        }

        $isoC->c09 = $this->currentUser->sys0101;
        $isoC->c10 = date('Y-m-d H:i:s');

        $isoD = new \stdClass();
        $isoD->d02 = $isoC->c01;
        $d03Result = $this->db->table($this->table . 'd')
            ->selectMax('d03')
            ->where('d02', $isoC->c01)
            ->get()
            ->getRow();
        $isoD->d03 = ($d03Result->d03 ?? 0) + 1;
        $isoD->d04 = $isoC->c10;
        $isoD->d05 = $isoC->c09;
        $isoD->d06 = $result;
        $isoD->d07 = $memo;

        $this->db->transStart();
        $this->db->table($this->table . 'c')
            ->where('c01', $isoC->c01)
            ->update((array)$isoC);
        $this->db->table($this->table . 'd')->insert((array)$isoD);
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            $this->message->add(lang('Model.update_err_hint'));
            return false;
        }

        return true;
    }

    /**
     * 計算報表日期
     */
    public function isoDate(int $type, string $date): string
    {
        helper('common');

        switch ($type) {
            case 1:
            case 9:
                return $date;
            case 2:
                return monday($date);
            case 3:
                return date('Y-m', strtotime($date));
            case 4:
                $time = strtotime($date);
                $mon1 = date('Y-m', strtotime($date)) . '-01';
                $mon2 = date('Y-m-d', strtotime("$mon1 + 2 week"));
                $time2 = strtotime($mon2);
                if ($time < $time2) {
                    return date('Y-m', strtotime($mon1)) . '#1';
                }
                return date('Y-m', strtotime($mon2)) . '#2';
            case 5:
                $time = strtotime($date);
                $mon1 = first_monday_of_month($date);
                $mon2 = date('Y-m-d', strtotime("$mon1 + 2 week"));
                $time2 = strtotime($mon2);
                if ($time < $time2) {
                    return date('Y-m', strtotime($mon1)) . '#1';
                }
                return date('Y-m', strtotime($mon2)) . '#2';
            default:
                return $date;
        }
    }

    /**
     * 檢查 schema
     */
    public function checkSchema(int $fmd0101): void
    {
        $fmd02Model = model('Fmd02Model');
        $fmd02s = $fmd02Model->getByFmd0101($fmd0101);

        $tableName = $this->table;
        $forge = \Config\Database::forge();

        // 檢查報表主檔
        $fields = $this->db->getFieldNames($this->table);
        if ($fmd02s) {
            $addField = [];
            foreach ($fmd02s as $fmd02) {
                if (!in_array('error' . $fmd02->fmd0203, $fields)) {
                    $addField['error' . $fmd02->fmd0203] = ['type' => 'INT', 'unsigned' => true, 'null' => false];
                    $addField['miss' . $fmd02->fmd0203] = ['type' => 'INT', 'unsigned' => true, 'null' => false];
                    $addField['check' . $fmd02->fmd0203] = ['type' => 'INT', 'unsigned' => true, 'null' => false];
                }
            }
            if ($addField) {
                $forge->addColumn($tableName, $addField);
            }
        }

        // 檢查明細檔
        $fmd05Model = model('Fmd05Model');
        $fmd05s = $fmd05Model->getByFmd0101($fmd0101);

        $fmd06Model = model('Fmd06Model');
        $fmd06s = $fmd06Model->getByFmd0101($fmd0101);

        $fields = $this->db->getFieldNames($this->table . 'a');
        if ($fmd02s) {
            $addField = [];
            foreach ($fmd02s as $fmd02) {
                foreach ($fmd05s as $fmd05) {
                    if (!in_array('data' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503, $fields)) {
                        $len = match ((int)$fmd05->fmd0505) {
                            0 => 0,
                            1, 2 => 10,
                            9 => 20,
                            default => $fmd05->fmd0506,
                        };

                        foreach ($fmd06s as $fmd06) {
                            if ($fmd06->fmd0604 == $fmd05->fmd0501) {
                                $len = match ((int)$fmd06->fmd0606) {
                                    1, 2 => max($len, 10),
                                    9 => max($len, 20),
                                    default => max($len, $fmd06->fmd0607),
                                };
                            }
                        }

                        $addField['data' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503] = ['type' => 'VARCHAR', 'constraint' => $len, 'null' => true];
                        $addField['memo' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503] = ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true];
                        $addField['err' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503] = ['type' => 'SMALLINT', 'null' => true];
                        $addField['src' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503] = ['type' => 'INT', 'unsigned' => true, 'null' => true];
                        $addField['sno' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503] = ['type' => 'SMALLINT', 'unsigned' => true, 'null' => true];
                        $addField['file' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503] = ['type' => 'SMALLINT', 'unsigned' => true, 'null' => true];
                    }
                }
            }
            if ($addField) {
                $fieldCount = count($fields) + count($addField);
                if ($fieldCount >= 1000) {
                    $error = sprintf(lang('Model.model_iso_field_count_exceeds_limit'), $tableName, $fieldCount);
                    log_message('error', $error);
                    $this->message->add($error);
                } else {
                    $forge->addColumn($tableName . 'a', $addField);
                }
            }
        }
    }

    /**
     * 建立資料表
     */
    private function createTable(int $fmd0101): void
    {
        $fmd02Model = model('Fmd02Model');
        $fmd02s = $fmd02Model->getByFmd0101($fmd0101);

        $tableName = $this->table;
        $forge = \Config\Database::forge();

        $fields = [];
        $fields['id'] = ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true];
        $fields['fmd0101'] = ['type' => 'INT', 'unsigned' => true];
        $fields['date'] = ['type' => 'VARCHAR', 'constraint' => 10];
        $fields['user_id'] = ['type' => 'INT', 'unsigned' => true];
        $fields['datetime'] = ['type' => 'DATETIME'];
        $fields['log'] = ['type' => 'TEXT', 'null' => true];
        $fields['summary'] = ['type' => 'TEXT', 'null' => true];
        $fields['comments'] = ['type' => 'TEXT'];
        $fields['error_count'] = ['type' => 'INT', 'unsigned' => true, 'default' => 0];
        $fields['miss_count'] = ['type' => 'INT', 'unsigned' => true, 'default' => 0];
        $fields['check_count'] = ['type' => 'INT', 'unsigned' => true, 'default' => 0];

        if ($fmd02s) {
            foreach ($fmd02s as $fmd02) {
                $fields['error' . $fmd02->fmd0203] = ['type' => 'INT', 'unsigned' => true, 'default' => 0];
                $fields['miss' . $fmd02->fmd0203] = ['type' => 'INT', 'unsigned' => true, 'default' => 0];
                $fields['check' . $fmd02->fmd0203] = ['type' => 'INT', 'unsigned' => true, 'default' => 0];
            }
        }

        $forge->addField($fields);
        $forge->addKey('id', true);
        $forge->createTable($tableName, true);

        $this->db->query("ALTER TABLE {$tableName} ADD INDEX ix_{$tableName}_date (fmd0101,date)");

        // 明細表
        $detailFields = [];
        $detailFields['id'] = ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true];
        $detailFields['master_id'] = ['type' => 'INT', 'unsigned' => true];
        $detailFields['line'] = ['type' => 'SMALLINT', 'unsigned' => true];
        $detailFields['error_count'] = ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 0];
        $detailFields['miss_count'] = ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 0];

        $fmd03Model = model('Fmd03Model');
        $fmd03s = $fmd03Model->getByFmd0101($fmd0101);
        foreach ($fmd03s as $fmd03) {
            $detailFields['item' . $fmd03->fmd0303 . '_name'] = ['type' => 'VARCHAR', 'constraint' => $fmd03->fmd0305, 'null' => true];
            $detailFields['item' . $fmd03->fmd0303 . '_child'] = ['type' => 'SMALLINT', 'null' => true];
        }

        $fmd05Model = model('Fmd05Model');
        $fmd05s = $fmd05Model->getByFmd0101($fmd0101);

        $fmd06Model = model('Fmd06Model');
        $fmd06s = $fmd06Model->getByFmd0101($fmd0101);

        if (!$fmd02s) {
            $fmd02 = new \stdClass();
            $fmd02->fmd0203 = '1';
            $fmd02s = [$fmd02];
        }

        foreach ($fmd02s as $fmd02) {
            foreach ($fmd05s as $fmd05) {
                $len = match ((int)$fmd05->fmd0505) {
                    0 => 0,
                    1, 2 => 10,
                    9 => 20,
                    default => $fmd05->fmd0506,
                };

                foreach ($fmd06s as $fmd06) {
                    if ($fmd06->fmd0604 == $fmd05->fmd0501) {
                        $len = match ((int)$fmd06->fmd0606) {
                            1, 2 => max($len, 10),
                            9 => max($len, 20),
                            default => max($len, $fmd06->fmd0607),
                        };
                    }
                }

                $detailFields['data' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503] = ['type' => 'VARCHAR', 'constraint' => $len, 'null' => true];
                $detailFields['memo' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503] = ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true];
                $detailFields['err' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503] = ['type' => 'SMALLINT', 'null' => true];
                $detailFields['src' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503] = ['type' => 'INT', 'unsigned' => true, 'null' => true];
                $detailFields['sno' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503] = ['type' => 'SMALLINT', 'unsigned' => true, 'null' => true];
                $detailFields['file' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503] = ['type' => 'SMALLINT', 'unsigned' => true, 'null' => true];
            }
        }

        $forge->addField($detailFields);
        $forge->addKey('id', true);
        $forge->createTable($tableName . 'a', true);

        $this->db->query("ALTER TABLE {$tableName}a ADD INDEX ix_{$tableName}a_master (master_id)");
    }

    /**
     * 建立簽核資料表
     */
    private function createApproveTable(): void
    {
        $forge = \Config\Database::forge();

        $fields = [
            'c01' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'c02' => ['type' => 'INT', 'unsigned' => true],
            'c03' => ['type' => 'INT', 'unsigned' => true],
            'c04' => ['type' => 'INT', 'unsigned' => true],
            'c05' => ['type' => 'INT', 'unsigned' => true],
            'c06' => ['type' => 'INT', 'unsigned' => true],
            'c07' => ['type' => 'DATETIME'],
            'c08' => ['type' => 'TINYINT', 'unsigned' => true],
            'c09' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'c10' => ['type' => 'DATETIME', 'null' => true],
            'c11' => ['type' => 'DATETIME', 'null' => true],
        ];

        $forge->addField($fields);
        $forge->addKey('c01', true);
        $forge->createTable($this->table . 'c', true);

        $this->db->query("ALTER TABLE {$this->table}c ADD INDEX ix_{$this->table}c_c02 (c02)");

        $recordFields = [
            'd01' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'd02' => ['type' => 'INT', 'unsigned' => true],
            'd03' => ['type' => 'SMALLINT', 'unsigned' => true],
            'd04' => ['type' => 'DATETIME'],
            'd05' => ['type' => 'INT', 'unsigned' => true],
            'd06' => ['type' => 'TINYINT', 'unsigned' => true],
            'd07' => ['type' => 'VARCHAR', 'constraint' => 200],
        ];

        $forge->addField($recordFields);
        $forge->addKey('d01', true);
        $forge->createTable($this->table . 'd', true);

        $this->db->query("ALTER TABLE {$this->table}d ADD INDEX ix_{$this->table}d_d02 (d02)");
    }
}
