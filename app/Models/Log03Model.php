<?php

namespace App\Models;

/**
 * Log03Model - 操作日誌模型
 */
class Log03Model extends BaseModel
{
    protected $table = 'log03';
    protected $primaryKey = 'log0301';
    protected $allowedFields = [
        'log0302', 'log0303', 'log0304', 'log0305'
    ];

    /**
     * 計算符合條件的資料筆數
     */
    public function countBy(?array $option = null): int
    {
        $builder = $this->builder();
        $this->applyQueryOptions($builder, $option);
        return $builder->countAllResults();
    }

    /**
     * 依條件取回資料
     */
    public function getBy(?array $option = null, int $limit = 0, int $offset = 0, ?string $sort = null): mixed
    {
        $builder = $this->builder();
        $builder->select('log03.*, sys01.sys0102, sys01.sys0103, sys01.sys0104, sys04.sys0402, sys04.sys0405, sys04.sys0412');
        $builder->join('sys01', 'sys01.sys0101 = log03.log0304', 'left');
        $builder->join('sys04', 'sys04.sys0401 = log03.log0305', 'left');

        $this->applyQueryOptions($builder, $option);

        $builder->orderBy('log0301', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResult();
    }

    /**
     * 套用查詢條件
     */
    protected function applyQueryOptions($builder, ?array $option): void
    {
        if (!$option) {
            return;
        }

        // 處理日期範圍
        if (!empty($option['log0302s'])) {
            $builder->where('log0302 >=', $option['log0302s'] . ' 00:00:00');
        }

        if (!empty($option['log0302e'])) {
            $builder->where('log0302 <=', $option['log0302e'] . ' 23:59:59');
        }

        // 處理使用者
        if (!empty($option['log0304'])) {
            $builder->where('log0304', $option['log0304']);
        }
    }
}
