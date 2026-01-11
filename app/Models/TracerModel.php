<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * TracerModel - 資料修改追蹤模型
 *
 * 記錄所有 fmd* 模型的新增、修改、刪除操作
 */
class TracerModel extends Model
{
    protected $table = 'tracer';
    protected $primaryKey = 'tracer01';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $allowedFields = [
        'tracer02', 'tracer03', 'tracer04', 'tracer05',
        'tracer06', 'tracer07', 'tracer08', 'tracer09'
    ];

    // 操作類型常數
    const ACTION_INSERT = 'INSERT';
    const ACTION_UPDATE = 'UPDATE';
    const ACTION_DELETE = 'DELETE';

    /**
     * 記錄資料變更
     *
     * @param string $tableName 資料表名稱
     * @param int|string $recordId 記錄ID
     * @param string $action 操作類型 (INSERT/UPDATE/DELETE)
     * @param array|null $oldData 舊資料
     * @param array|null $newData 新資料
     * @param string|null $note 備註
     * @return int|false
     */
    public function trace(
        string $tableName,
        int|string $recordId,
        string $action,
        ?array $oldData = null,
        ?array $newData = null,
        ?string $note = null
    ): int|false {
        $user = service('user');
        $currentUser = $user ? $user->current() : null;

        $data = [
            'tracer02' => $tableName,
            'tracer03' => $recordId,
            'tracer04' => $action,
            'tracer05' => $oldData ? json_encode($oldData, JSON_UNESCAPED_UNICODE) : null,
            'tracer06' => $newData ? json_encode($newData, JSON_UNESCAPED_UNICODE) : null,
            'tracer07' => $currentUser ? $currentUser->sys0101 : null,
            'tracer08' => date('Y-m-d H:i:s'),
            'tracer09' => $note,
        ];

        if ($this->db->table($this->table)->insert($data)) {
            return $this->db->insertID();
        }

        return false;
    }

    /**
     * 取得某資料表的變更歷史
     */
    public function getHistoryByTable(string $tableName, int $limit = 100, int $offset = 0): array
    {
        return $this->db->table($this->table)
            ->select('tracer.*, sys0102, sys0103, sys0104')
            ->join('sys01', 'sys0101 = tracer07', 'left')
            ->where('tracer02', $tableName)
            ->orderBy('tracer08', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResult();
    }

    /**
     * 取得某筆記錄的變更歷史
     */
    public function getHistoryByRecord(string $tableName, int|string $recordId, int $limit = 100): array
    {
        return $this->db->table($this->table)
            ->select('tracer.*, sys0102, sys0103, sys0104')
            ->join('sys01', 'sys0101 = tracer07', 'left')
            ->where('tracer02', $tableName)
            ->where('tracer03', $recordId)
            ->orderBy('tracer08', 'DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    /**
     * 取得某使用者的操作歷史
     */
    public function getHistoryByUser(int $userId, int $limit = 100, int $offset = 0): array
    {
        return $this->db->table($this->table)
            ->select('tracer.*, sys0102, sys0103, sys0104')
            ->join('sys01', 'sys0101 = tracer07', 'left')
            ->where('tracer07', $userId)
            ->orderBy('tracer08', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResult();
    }

    /**
     * 依時間範圍取得變更歷史
     */
    public function getHistoryByDateRange(string $startDate, string $endDate, ?string $tableName = null, int $limit = 1000): array
    {
        $builder = $this->db->table($this->table)
            ->select('tracer.*, sys0102, sys0103, sys0104')
            ->join('sys01', 'sys0101 = tracer07', 'left')
            ->where('tracer08 >=', $startDate . ' 00:00:00')
            ->where('tracer08 <=', $endDate . ' 23:59:59');

        if ($tableName) {
            $builder->where('tracer02', $tableName);
        }

        return $builder->orderBy('tracer08', 'DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    /**
     * 取得變更差異 (比較新舊資料)
     */
    public function getDiff(int $tracerId): array
    {
        $record = $this->find($tracerId);
        if (!$record) {
            return [];
        }

        $oldData = $record->tracer05 ? json_decode($record->tracer05, true) : [];
        $newData = $record->tracer06 ? json_decode($record->tracer06, true) : [];

        $diff = [];
        $allKeys = array_unique(array_merge(array_keys($oldData), array_keys($newData)));

        foreach ($allKeys as $key) {
            $oldValue = $oldData[$key] ?? null;
            $newValue = $newData[$key] ?? null;

            if ($oldValue !== $newValue) {
                $diff[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $diff;
    }

    /**
     * 計算某資料表的變更次數
     */
    public function countByTable(string $tableName, ?string $action = null): int
    {
        $builder = $this->db->table($this->table)
            ->where('tracer02', $tableName);

        if ($action) {
            $builder->where('tracer04', $action);
        }

        return $builder->countAllResults();
    }

    /**
     * 清除舊的追蹤記錄 (保留指定天數內的記錄)
     */
    public function cleanup(int $keepDays = 90): int
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$keepDays} days"));

        $this->db->table($this->table)
            ->where('tracer08 <', $cutoffDate)
            ->delete();

        return $this->db->affectedRows();
    }
}
