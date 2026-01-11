<?php

namespace App\Models;

/**
 * Pad05Model - 委修/維修單模型
 */
class Pad05Model extends BaseModel
{
    protected $table = 'pad05';
    protected $primaryKey = 'pad0501';
    protected $allowedFields = [
        'pad0502', 'pad0503', 'pad0504', 'pad0505', 'pad0506', 'pad0507',
        'pad0508', 'pad0509', 'pad0510', 'pad0511', 'pad0512', 'pad0513',
        'pad0514', 'pad0515', 'pad0516', 'pad0517', 'pad0518', 'pad0519', 'pad0520'
    ];

    /**
     * Get single record with joins
     */
    public function get(string|int $id): ?object
    {
        $builder = $this->builder();
        $builder->join('ent10', 'pad0507=ent1001')
            ->join('sys01', 'sys0101=pad0508')
            ->join('cod02', 'cod0203=pad0510')
            ->join('cod01', "cod0101=cod0202 and cod0102='pad0510'");
        return $builder->where($this->primaryKey, $id)->get()->getRow();
    }

    /**
     * Count records with joins
     */
    public function countBy(?array $option = null): int
    {
        $builder = $this->builder();
        $builder->join('ent10', 'pad0507=ent1001')
            ->join('sys01', 'sys0101=pad0508')
            ->join('cod02', 'cod0203=pad0510')
            ->join('cod01', "cod0101=cod0202 and cod0102='pad0510'");

        if ($option === null) {
            $option = [];
        }

        // Handle date range
        if (isset($option['pad0509s'])) {
            $option['*pad0509s'] = "pad0509>='" . $option['pad0509s'] . " 00:00:00'";
            unset($option['pad0509s']);
        }
        if (isset($option['pad0509e'])) {
            $option['*pad0509e'] = "pad0509<'" . $option['pad0509e'] . " 23:59:59'";
            unset($option['pad0509e']);
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
        $builder->join('ent10', 'pad0507=ent1001')
            ->join('sys01', 'sys0101=pad0508')
            ->join('cod02', 'cod0203=pad0510')
            ->join('cod01', "cod0101=cod0202 and cod0102='pad0510'");

        if ($option === null) {
            $option = [];
        }

        // Handle date range
        if (isset($option['pad0509s'])) {
            $option['*pad0509s'] = "pad0509>='" . $option['pad0509s'] . " 00:00:00'";
            unset($option['pad0509s']);
        }
        if (isset($option['pad0509e'])) {
            $option['*pad0509e'] = "pad0509<'" . $option['pad0509e'] . " 23:59:59'";
            unset($option['pad0509e']);
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

    /**
     * Get pad06 records for a pad05
     */
    public function getPad06(int $pad0501): array
    {
        return $this->db->query(
            "select * from pad06, sys01 where pad0603=sys0101 and pad0602=?",
            [$pad0501]
        )->getResult();
    }

    /**
     * Get power email addresses
     * @param int $ent1001 Department ID
     * @param int $power 30=委修管理, 31=維修管理
     */
    public function getPowerEmail(int $ent1001, int $power): string
    {
        $userDep = (string)$ent1001;
        $ent10Model = model('Ent10Model');

        if ($subDep = $ent10Model->getSubDepartmentId($ent1001)) {
            $userDep .= ',' . $subDep;
        }

        if (!empty($userDep)) {
            $rest = '';
            $sys01s = $this->db->query(
                "select sys0107, rol0105 from sys01, sys02, rol01
                 where sys0101=sys0202 and sys0204=rol0101 and sys0108=1
                 and sys0110 in ({$userDep}) and sys0107 is not null and sys0107<>''
                 group by sys0107"
            )->getResult();

            foreach ($sys01s as $v1) {
                $rol0105 = explode(",", $v1->rol0105);
                if (in_array($power, $rol0105)) {
                    $rest .= $v1->sys0107 . ',';
                }
            }
            return rtrim($rest, ',');
        }

        return "";
    }
}
