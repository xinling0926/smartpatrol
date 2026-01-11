<?php

namespace App\Models;

/**
 * CommonModel - 通用模型
 *
 * 可以動態設定表名，用於沒有專屬 Model 的資料表
 */
class CommonModel extends BaseModel
{
    protected $table = '';
    protected $primaryKey = 'id';
    protected $allowedFields = [];

    /**
     * 設定表名並自動設定允許欄位
     */
    public function setTableName(string $value): string
    {
        parent::setTableName($value);

        // 自動取得資料表欄位作為允許欄位
        try {
            $this->allowedFields = $this->db->getFieldNames($this->table);
        } catch (\Exception $e) {
            $this->allowedFields = [];
        }

        return $this->table;
    }
}
