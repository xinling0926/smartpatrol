<?php

namespace App\Models;

/**
 * Pad07Model - 照片模型
 */
class Pad07Model extends BaseModel
{
    protected $table = 'pad07';
    protected $primaryKey = 'pad0701';

    /**
     * Count records with joins
     */
    public function countBy(?array $option = null): int
    {
        $builder = $this->builder();
        $builder->join('ent10', 'pad0702=ent1001')
            ->join('sys01', 'sys0101=pad0704')
            ->join('dev01', 'dev0101=pad0703');

        if ($option === null) {
            $option = [];
        }

        // Handle date range
        if (isset($option['pad0707s']) && $option['pad0707s']) {
            $option['*pad0707s'] = "pad0707>='" . $option['pad0707s'] . " 00:00:00'";
            unset($option['pad0707s']);
        }
        if (isset($option['pad0707e']) && $option['pad0707e']) {
            $option['*pad0707e'] = "pad0707<'" . $option['pad0707e'] . " 23:59:59'";
            unset($option['pad0707e']);
        }

        foreach ($option as $key => $value) {
            if (strpos($key, '*') === 0) {
                $builder->where($value);
            } else {
                $builder->where($key, $value);
            }
        }

        return $builder->countAllResults();
    }

    /**
     * Get records with joins
     */
    public function getBy(?array $option = null, int $limit = 0, int $offset = 0, ?string $sort = null): mixed
    {
        $builder = $this->builder();
        $builder->join('ent10', 'pad0702=ent1001')
            ->join('sys01', 'sys0101=pad0704')
            ->join('dev01', 'dev0101=pad0703');

        if ($option === null) {
            $option = [];
        }

        // Handle date range
        if (isset($option['pad0707s']) && $option['pad0707s']) {
            $option['*pad0707s'] = "pad0707>='" . $option['pad0707s'] . " 00:00:00'";
            unset($option['pad0707s']);
        }
        if (isset($option['pad0707e']) && $option['pad0707e']) {
            $option['*pad0707e'] = "pad0707<'" . $option['pad0707e'] . " 23:59:59'";
            unset($option['pad0707e']);
        }

        foreach ($option as $key => $value) {
            if (strpos($key, '*') === 0) {
                $builder->where($value);
            } else {
                $builder->where($key, $value);
            }
        }

        if ($sort) {
            $builder->orderBy($sort);
        }

        if ($limit) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResult();
    }

    public function getByPad0101(int $pad0101): array
    {
        return $this->db->table($this->table)
            ->where('pad0702', $pad0101)
            ->get()
            ->getResult();
    }
}
