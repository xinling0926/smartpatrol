<?php

namespace App\Models;

/**
 * Pad01Model - 巡檢紀錄模型
 */
class Pad01Model extends BaseModel
{
    protected $table = 'pad01';
    protected $primaryKey = 'pad0101';
    protected $allowedFields = [
        'pad0102', 'pad0103', 'pad0104', 'pad0105', 'pad0106', 'pad0107',
        'pad0108', 'pad0109', 'pad0110', 'pad0111', 'pad0112', 'pad0113'
    ];

    /**
     * 取得使用者的部門限制條件
     */
    private function addDepartmentOption(): string
    {
        $userDep = $this->currentUser->sys0110 ?? null;

        if ($userDep) {
            $ent10Model = model('Ent10Model');
            if ($subDep = $ent10Model->getSubDepartmentId($userDep)) {
                $userDep .= ',' . $subDep;
            }
            return " and fmd0102 in ({$userDep})";
        }

        return "";
    }

    /**
     * 透過ID取回一筆資料 (含JOIN)
     */
    public function get(int|string $id): ?object
    {
        return $this->builder()
            ->join('dev01', 'dev0101=pad0103')
            ->join('sys01', 'sys0101=pad0104')
            ->join('fmd09', 'fmd0901=pad0106')
            ->join('fmd08', 'fmd0801=fmd0903')
            ->join('fmd07', 'fmd0701=pad0102')
            ->join('fmd01', 'fmd0101=fmd0702')
            ->join('ent10', 'ent1001=fmd0102')
            ->join('fmd02', 'fmd0201=pad0105', 'left')
            ->where($this->primaryKey, $id)
            ->get()
            ->getRow();
    }

    /**
     * 計算符合條件的資料筆數
     */
    public function countBy(?array $option = null): int
    {
        $builder = $this->builder();
        $userDep = $this->addDepartmentOption();

        $builder->join('dev01', 'dev0101=pad0103')
            ->join('fmd07', 'fmd0701=pad0102')
            ->join('fmd09', 'fmd0901=pad0106')
            ->join('fmd08', 'fmd0801=fmd0903')
            ->join('fmd01', 'fmd0101=fmd0702' . $userDep);

        // 處理 ent10 JOIN 條件
        if (isset($option['ent1002']) || isset($option['ent1007'])) {
            if (isset($option['ent1002']) && isset($option['ent1007'])) {
                $builder->join('ent10', 'ent1001=fmd0102 and ent1002=' . $option['ent1002'] . ' and ent1007=' . $option['ent1007']);
                unset($option['ent1002']);
                unset($option['ent1007']);
            } elseif (isset($option['ent1002'])) {
                $builder->join('ent10', 'ent1001=fmd0102 and ent1002=' . $option['ent1002']);
                unset($option['ent1002']);
            } elseif (isset($option['ent1007'])) {
                $builder->join('ent10', 'ent1001=fmd0102 and ent1007=' . $option['ent1007']);
                unset($option['ent1007']);
            }
        } else {
            $builder->join('ent10', 'ent1001=fmd0102');
        }

        // 處理日期範圍
        if (isset($option['pad0109s'])) {
            $option['*pad0109s'] = "pad0109>='" . $option['pad0109s'] . " 00:00:00'";
            unset($option['pad0109s']);
        }
        if (isset($option['pad0109e'])) {
            $option['*pad0109e'] = "pad0109<'" . $option['pad0109e'] . " 23:59:59'";
            unset($option['pad0109e']);
        }

        // 處理巡檢點篩選
        if (isset($option['pad0102s'])) {
            $option['*pad0102s'] = sprintf("pad0102 in (select fmd0701 from fmd07 where fmd0707=%s)", $option['pad0102s']);
            unset($option['pad0102s']);
        }

        // 處理班別篩選
        if (isset($option['pad0105s'])) {
            $option['*pad0105s'] = sprintf("pad0105 in (select fmd0201 from fmd02 where fmd0203=%s)", $option['pad0105s']);
            unset($option['pad0105s']);
        }

        if ($option) {
            $this->setWhereValues($builder, $option);
        }

        return $builder->countAllResults();
    }

    /**
     * 依條件取回資料
     */
    public function getBy(?array $option = null, int $limit = 0, int $offset = 0, ?string $sort = null, ?string $fields = null): mixed
    {
        $builder = $this->builder();
        $userDep = $this->addDepartmentOption();

        $builder->join('dev01', 'dev0101=pad0103')
            ->join('sys01', 'sys0101=pad0104')
            ->join('fmd09', 'fmd0901=pad0106')
            ->join('fmd08', 'fmd0801=fmd0903')
            ->join('fmd07', 'fmd0701=pad0102')
            ->join('fmd01', 'fmd0101=fmd0702' . $userDep);

        // 處理 ent10 JOIN 條件
        if (isset($option['ent1002']) || isset($option['ent1007'])) {
            if (isset($option['ent1002']) && isset($option['ent1007'])) {
                $builder->join('ent10', 'ent1001=fmd0102 and ent1002=' . $option['ent1002'] . ' and ent1007=' . $option['ent1007']);
                unset($option['ent1002']);
                unset($option['ent1007']);
            } elseif (isset($option['ent1002'])) {
                $builder->join('ent10', 'ent1001=fmd0102 and ent1002=' . $option['ent1002']);
                unset($option['ent1002']);
            } elseif (isset($option['ent1007'])) {
                $builder->join('ent10', 'ent1001=fmd0102 and ent1007=' . $option['ent1007']);
                unset($option['ent1007']);
            }
        } else {
            $builder->join('ent10', 'ent1001=fmd0102');
        }
        $builder->join('fmd02', 'fmd0201=pad0105', 'left');

        // 處理日期範圍
        if (isset($option['pad0109s'])) {
            $option['*pad0109s'] = "pad0109>='" . $option['pad0109s'] . " 00:00:00'";
            unset($option['pad0109s']);
        }
        if (isset($option['pad0109e'])) {
            $option['*pad0109e'] = "pad0109<'" . $option['pad0109e'] . " 23:59:59'";
            unset($option['pad0109e']);
        }

        // 處理巡檢點篩選
        if (isset($option['pad0102s'])) {
            $option['*pad0102s'] = sprintf("pad0102 in (select fmd0701 from fmd07 where fmd0707=%s or fmd0701=%s)", $option['pad0102s'], $option['pad0102s']);
            unset($option['pad0102s']);
        }

        // 處理班別篩選
        if (isset($option['pad0105s'])) {
            $option['*pad0105s'] = sprintf("pad0105 in (select fmd0201 from fmd02 where fmd0203=%s)", $option['pad0105s']);
            unset($option['pad0105s']);
        }

        if ($option) {
            $this->setWhereValues($builder, $option);
        }

        if (!empty($fields)) {
            $builder->select($fields);
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
     * 取得巡檢紀錄的圖片
     */
    public function getPad02s(int|string $id): array
    {
        return $this->db->query(sprintf("select pad0203 from pad02 where pad0202=%s", $id))->getResult();
    }

    /**
     * 新增資料 (不記錄log)
     */
    public function insertData(array|object $data, bool $log = false): int|false
    {
        return parent::insertData($data, $log);
    }
}
