<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\BaseConnection;

/**
 * BaseModel - 基礎模型類
 */
class BaseModel extends Model
{
    protected $table = '';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [];
    protected $useTimestamps = false;
    protected $createdField = '';
    protected $updatedField = '';

    protected $message;
    protected $currentUser;

    /**
     * 是否啟用資料追蹤 (記錄到 tracer 資料表)
     * 預設對 fmd* 資料表啟用
     */
    protected bool $enableTracing = true;

    /**
     * 要追蹤的資料表前綴
     */
    protected array $tracedTablePrefixes = ['fmd'];

    // 追蹤操作類型常數
    protected const TRACE_INSERT = 'INSERT';
    protected const TRACE_UPDATE = 'UPDATE';
    protected const TRACE_DELETE = 'DELETE';

    public function __construct(?BaseConnection $db = null, ?\CodeIgniter\Validation\ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);

        // 自動設定表名和主鍵
        if (empty($this->table)) {
            $className = strtolower(preg_replace('/Model$/', '', class_basename($this)));
            $this->table = $className;
        }

        if (preg_match("/^[a-z]{3}[0-9]{2}$/", $this->table)) {
            $this->primaryKey = $this->table . '01';
        }

        $this->message = service('message');
        $user = service('user');
        $this->currentUser = $user ? $user->current() : null;
    }

    /**
     * 設定表名
     */
    public function setTableName(string $value): string
    {
        $this->table = $value;
        if (preg_match("/^[a-z]{3}[0-9]{2}$/", $this->table)) {
            $this->primaryKey = $this->table . '01';
        } else {
            $this->primaryKey = 'id';
        }
        return $this->table;
    }

    /**
     * 取得表名
     */
    public function getTableName(): string
    {
        return $this->table;
    }

    /**
     * 透過ID取回一筆資料
     */
    public function get(int|string $id): ?object
    {
        return $this->find($id);
    }

    /**
     * 依條件取回資料
     */
    public function getBy(?array $option = null, int $limit = 0, int $offset = 0, ?string $sort = null): mixed
    {
        $builder = $this->builder();

        if ($option) {
            $this->setWhereValues($builder, $option);
        }

        if ($sort) {
            $this->setOrderBy($builder, $sort);
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
     * 計算符合條件的資料筆數
     */
    public function countBy(?array $option = null): int
    {
        $builder = $this->builder();

        if ($option) {
            $this->setWhereValues($builder, $option);
        }

        return $builder->countAllResults();
    }

    /**
     * 依條件刪除資料
     */
    public function deleteBy(array $option): bool
    {
        // 如果需要追蹤，先取得要刪除的記錄
        if ($this->shouldTrace()) {
            $records = $this->getBy($option);
            foreach ($records as $record) {
                $recordArray = (array)$record;
                $id = $recordArray[$this->primaryKey] ?? 0;
                if ($id) {
                    $this->trace($id, self::TRACE_DELETE, $recordArray, null);
                }
            }
        }

        $builder = $this->builder();
        $this->setWhereValues($builder, $option);
        return $builder->delete();
    }

    /**
     * 刪除單筆資料 (含追蹤)
     */
    public function deleteData(int|string $id): bool
    {
        // 如果需要追蹤，先取得要刪除的記錄
        if ($this->shouldTrace()) {
            $record = $this->find($id);
            if ($record) {
                $this->trace($id, self::TRACE_DELETE, (array)$record, null);
            }
        }

        return $this->delete($id);
    }

    /**
     * 指定欄位取回資料Array
     */
    public function getNameArray(
        string $keyField,
        string $nameFields,
        mixed $data = null,
        ?array $option = null,
        ?string $sort = null
    ): array {
        $builder = $this->builder();
        $builder->select($keyField)->select($nameFields);

        if ($option) {
            $this->setWhereValues($builder, $option);
        }

        if ($sort) {
            $this->setOrderBy($builder, $sort);
        }

        $result = $builder->get()->getResult();

        return $this->createNameArray($result, $keyField, $nameFields, $data);
    }

    /**
     * 建立名稱陣列
     */
    public function createNameArray(array $data, string $keyField, string $nameFields, mixed $ext = null): array
    {
        $arr = [];

        if ($ext) {
            if (is_array($ext)) {
                $arr = $ext;
            } else {
                switch ($ext) {
                    case '1':
                        $arr = ['' => lang('Globe.all')];
                        break;
                    case '2':
                        $arr = ['' => lang('Globe.dropdown_select')];
                        break;
                }
            }
        }

        $nameFieldsArray = explode(',', $nameFields);
        foreach ($data as $obj) {
            $key = $obj->{$keyField};
            $name = '';
            foreach ($nameFieldsArray as $field) {
                $field = trim($field);
                if (isset($obj->{$field})) {
                    $name .= $obj->{$field} . ' ';
                }
            }
            if (!array_key_exists($key, $arr)) {
                $arr[$key] = trim($name);
            }
        }

        return $arr;
    }

    /**
     * 設定 WHERE 條件
     */
    protected function setWhereValues($builder, ?array $data): void
    {
        if (!$data) {
            return;
        }

        foreach ($data as $fieldName => $option) {
            if (strpos($fieldName, '*') === 0) {
                $builder->where($option, null, false);
            } elseif ($option !== '') {
                $builder->where($fieldName, $option);
            }
        }
    }

    /**
     * 設定排序方法
     */
    protected function setOrderBy($builder, ?string $sort): void
    {
        if ($sort === null) {
            return;
        }

        $sortFields = explode(',', $sort);
        foreach ($sortFields as $s) {
            $builder->orderBy(trim($s));
        }
    }

    /**
     * 過濾資料欄位
     */
    protected function filterData(array|object $data): array
    {
        if (is_object($data)) {
            $data = (array)$data;
        }

        $columns = $this->db->getFieldNames($this->table);
        $filteredData = [];

        foreach ($columns as $column) {
            if (array_key_exists($column, $data)) {
                $filteredData[$column] = $data[$column];
            }
        }

        return $filteredData;
    }

    /**
     * 檢查是否應該追蹤此資料表
     */
    protected function shouldTrace(): bool
    {
        if (!$this->enableTracing) {
            return false;
        }

        foreach ($this->tracedTablePrefixes as $prefix) {
            if (strpos($this->table, $prefix) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * 記錄追蹤資訊
     */
    protected function trace(int|string $recordId, string $action, ?array $oldData = null, ?array $newData = null, ?string $note = null): void
    {
        if (!$this->shouldTrace()) {
            return;
        }

        try {
            // 避免追蹤 tracer 表本身造成無限迴圈
            if ($this->table === 'tracer') {
                return;
            }

            // 檢查 tracer 資料表是否存在
            if (!$this->db->tableExists('tracer')) {
                return;
            }

            $tracerModel = model('TracerModel');
            $tracerModel->trace($this->table, $recordId, $action, $oldData, $newData, $note);
        } catch (\Throwable $e) {
            // 追蹤失敗不應影響主要操作，只記錄錯誤
            log_message('error', 'Tracer error: ' . $e->getMessage());
        }
    }

    /**
     * 新增資料
     */
    public function insertData(array|object $data, bool $log = true): int|false
    {
        if (is_object($data)) {
            $data = (array)$data;
        }

        // 過濾欄位
        $data = $this->filterData($data);

        // 加入新增人員與時間的紀錄
        if ($log) {
            $createdBy = $this->primaryKey === 'id' ? 'created_by' : $this->table . 'z1';
            $createdOn = $this->primaryKey === 'id' ? 'created_on' : $this->table . 'z2';

            if ($this->currentUser) {
                $data[$createdBy] = $this->currentUser->sys0101 ?? null;
            }
            $data[$createdOn] = date('Y-m-d H:i:s');
        }

        if ($this->db->table($this->table)->insert($data)) {
            $insertId = $this->db->insertID();

            // 追蹤新增操作
            $this->trace($insertId, self::TRACE_INSERT, null, $data);

            return $insertId;
        }

        return false;
    }

    /**
     * 更新資料
     */
    public function updateData(array|object $data, bool $log = true): int|false
    {
        if (is_object($data)) {
            $data = (array)$data;
        }

        // 過濾欄位
        $data = $this->filterData($data);

        // 必須包含id欄位才能更新
        if (!isset($data[$this->primaryKey])) {
            return false;
        }

        $id = $data[$this->primaryKey];
        unset($data[$this->primaryKey]);

        // 取得舊資料用於追蹤
        $oldData = null;
        if ($this->shouldTrace()) {
            $oldRecord = $this->find($id);
            if ($oldRecord) {
                $oldData = (array)$oldRecord;
            }
        }

        // 加入更新人員與時間的紀錄
        if ($log) {
            $updatedBy = $this->table . 'z3';
            $updatedOn = $this->table . 'z4';

            if ($this->currentUser) {
                $data[$updatedBy] = $this->currentUser->sys0101 ?? null;
            }
            $data[$updatedOn] = date('Y-m-d H:i:s');
        }

        $this->db->table($this->table)->where($this->primaryKey, $id)->update($data);
        $affectedRows = $this->db->affectedRows();

        // 追蹤更新操作
        if ($affectedRows > 0) {
            $newData = $data;
            $newData[$this->primaryKey] = $id;
            $this->trace($id, self::TRACE_UPDATE, $oldData, $newData);
        }

        return $affectedRows;
    }

    /**
     * 保存數據 (新增或更新)
     */
    public function saveData(array|object $data, bool $log = true): int|false
    {
        if (is_object($data)) {
            $data = (array)$data;
        }

        if (isset($data[$this->primaryKey]) && !empty($data[$this->primaryKey])) {
            return $this->updateData($data, $log);
        }

        return $this->insertData($data, $log);
    }

    /**
     * 覆寫 CI4 的 save() 方法
     * 確保更新時也會自動寫入 {table}z3 / {table}z4 (更新人員/時間)
     * 讓手機端的增量同步機制能正確偵測資料變更
     *
     * 排除沒有 z4 欄位的資料表（如 tag01）
     */
    public function save($data): bool
    {
        // 沒有 z4 欄位的表，走 CI4 原生 save()
        $tablesWithoutZ4 = ['tag01'];
        if (in_array($this->table, $tablesWithoutZ4, true)) {
            return parent::save($data);
        }

        $result = $this->saveData($data);
        return $result !== false;
    }

    /**
     * 開始交易
     */
    public function transStart(): void
    {
        $this->db->transStart();
    }

    /**
     * 完成交易
     */
    public function transComplete(): bool
    {
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            $this->message->add(lang('Model.update_err_hint'));
            return false;
        }

        return true;
    }
}
