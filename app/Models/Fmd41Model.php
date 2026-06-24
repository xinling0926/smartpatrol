<?php

namespace App\Models;

/**
 * Fmd41Model - EUI 統計數值設定
 */
class Fmd41Model extends BaseModel
{
    protected $table = 'fmd41';
    protected $primaryKey = 'fmd4101';
    protected $allowedFields = [
        'fmd4102', 'fmd4103', 'fmd4104', 'fmd4105',
        'fmd41z1', 'fmd41z2', 'fmd41z3', 'fmd41z4'
    ];

    /**
     * 透過ID取回一筆資料 (含 JOIN fmd40)
     */
    public function get(int|string $id): ?object
    {
        return $this->builder()
            ->join('fmd40', 'fmd4102=fmd4002')
            ->where($this->primaryKey, $id)
            ->get()
            ->getRow();
    }

    /**
     * 依條件取回資料 (含 JOIN fmd40)
     */
    public function getBy(?array $option = null, int $limit = 0, int $offset = 0, ?string $sort = null): mixed
    {
        $builder = $this->builder();
        $builder->join('fmd40', 'fmd4102=fmd4002');

        if ($option) {
            $this->setWhereValues($builder, $option);
        }

        if ($sort) {
            $this->setOrderBy($builder, $sort);
        } else {
            $builder->orderBy('fmd4004,fmd4103 desc');
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
     * 計算符合條件的資料筆數 (含 JOIN fmd40)
     */
    public function countBy(?array $option = null): int
    {
        $builder = $this->builder();
        $builder->join('fmd40', 'fmd4102=fmd4002');

        if ($option) {
            $this->setWhereValues($builder, $option);
        }

        return $builder->countAllResults();
    }
}
