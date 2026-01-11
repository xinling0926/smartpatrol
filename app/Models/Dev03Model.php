<?php

namespace App\Models;

/**
 * Dev03Model - 設備訊息模型
 */
class Dev03Model extends BaseModel
{
    protected $table = 'dev03';
    protected $primaryKey = 'dev0301';
    protected $allowedFields = [
        'dev0302', 'dev0303', 'dev0304', 'dev0305', 'dev0306', 'dev0307', 'dev0310'
    ];

    /**
     * 覆寫 getBy 加入 join
     */
    public function getBy(?array $option = null, int $limit = 0, int $offset = 0, ?string $sort = null): mixed
    {
        $builder = $this->builder();
        $builder->join('sys01', 'sys0101=dev0303', 'left');
        $builder->join('dev01', 'dev0101=dev0302', 'left');

        if ($option) {
            foreach ($option as $key => $value) {
                if (strpos($key, '*') === 0) {
                    $builder->where($value);
                } else {
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
     * 覆寫 countBy 加入 join
     */
    public function countBy(?array $option = null): int
    {
        $builder = $this->builder();
        $builder->join('sys01', 'sys0101=dev0303', 'left');
        $builder->join('dev01', 'dev0101=dev0302', 'left');

        if ($option) {
            foreach ($option as $key => $value) {
                if (strpos($key, '*') === 0) {
                    $builder->where($value);
                } else {
                    $builder->where($key, $value);
                }
            }
        }

        return $builder->countAllResults();
    }

    /**
     * 覆寫 find 加入 join
     */
    public function find($id = null)
    {
        if ($id === null) {
            return parent::find();
        }

        $builder = $this->builder();
        $builder->join('sys01', 'sys0101=dev0303', 'left');
        $builder->join('dev01', 'dev0101=dev0302', 'left');
        $builder->where($this->primaryKey, $id);

        return $builder->get()->getRow();
    }
}
