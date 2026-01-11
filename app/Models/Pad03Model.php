<?php

namespace App\Models;

/**
 * Pad03Model - 維修單模型
 */
class Pad03Model extends BaseModel
{
    protected $table = 'pad03';
    protected $primaryKey = 'pad0301';
    protected $allowedFields = [
        'pad0302', 'pad0303', 'pad0304', 'pad0305', 'pad0306', 'pad0307',
        'pad0308', 'pad0309', 'pad0310', 'pad03z1', 'pad03z2', 'pad03z3', 'pad03z4'
    ];

    /**
     * Get single record with joins
     */
    public function get(string|int $id): ?object
    {
        $builder = $this->builder();
        $builder->join('dev01', 'dev0101=pad0302')
            ->join('sys01', 'sys0101=pad03z1')
            ->join('ent10', 'sys0110=ent1001');
        return $builder->where($this->primaryKey, $id)->get()->getRow();
    }

    /**
     * Count records with joins
     */
    public function countBy(?array $option = null): int
    {
        $builder = $this->builder();
        $builder->join('dev01', 'dev0101=pad0302')
            ->join('sys01', 'sys0101=pad03z1')
            ->join('ent10', 'sys0110=ent1001');

        if ($option === null) {
            $option = [];
        }

        // Handle date range
        if (isset($option['pad03z2s'])) {
            $option['*pad03z2s'] = "pad03z2>='" . $option['pad03z2s'] . " 00:00:00'";
            unset($option['pad03z2s']);
        }
        if (isset($option['pad03z2e'])) {
            $option['*pad03z2e'] = "pad03z2<'" . $option['pad03z2e'] . " 23:59:59'";
            unset($option['pad03z2e']);
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
        $builder->join('dev01', 'dev0101=pad0302')
            ->join('sys01', 'sys0101=pad03z1')
            ->join('ent10', 'sys0110=ent1001');

        if ($option === null) {
            $option = [];
        }

        // Handle date range
        if (isset($option['pad03z2s'])) {
            $option['*pad03z2s'] = "pad03z2>='" . $option['pad03z2s'] . " 00:00:00'";
            unset($option['pad03z2s']);
        }
        if (isset($option['pad03z2e'])) {
            $option['*pad03z2e'] = "pad03z2<'" . $option['pad03z2e'] . " 23:59:59'";
            unset($option['pad03z2e']);
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

    public function getPad04s(int $pad0301): array
    {
        return $this->db->table('pad04')
            ->where('pad0402', $pad0301)
            ->orderBy('pad0401')
            ->get()
            ->getResult();
    }
}
