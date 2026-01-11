<?php

namespace App\Models;

/**
 * Fmd02Model - 班別設定模型
 */
class Fmd02Model extends BaseModel
{
    protected $table = 'fmd02';
    protected $primaryKey = 'fmd0201';
    protected $allowedFields = [
        'fmd0202', 'fmd0203', 'fmd0204', 'fmd0205', 'fmd0206', 'fmd0207'
    ];

    public function getByFmd0101(int $fmd0101): array
    {
        return $this->db->table($this->table)
            ->where('fmd0202', $fmd0101)
            ->orderBy('fmd0203,fmd0205')
            ->get()
            ->getResult();
    }

    public function getCurrentDaypart(int $fmd0101, ?int $now = null): ?object
    {
        $now = $now ?? time();
        $day = [];
        $day[] = date('Y-m-d', strtotime(date('Y-m-d', $now) . ' -1 day'));
        $day[] = date('Y-m-d', $now);

        $_fmd02 = new \stdClass();
        $_fmd02->fmd0201 = '';
        $_fmd02->fmd0202 = $fmd0101;
        $_fmd02->fmd0203 = '';
        $_fmd02->fmd0204 = '';
        $_fmd02->_date = $day[0];

        $fmd02s = $this->getByFmd0101($fmd0101);
        if ($fmd02s) {
            $i = 0;
            while ($i < 2) {
                foreach ($fmd02s as $fmd02) {
                    $tmp_s = strtotime($day[$i] . ' ' . $fmd02->fmd0205);
                    $tmp_e = strtotime($day[$i] . ' ' . $fmd02->fmd0206);
                    if ($tmp_s >= $tmp_e) {
                        $tmp_e = strtotime(date('Y-m-d', strtotime($day[$i] . ' +1 day')) . ' ' . $fmd02->fmd0206);
                    }
                    if ($now <= $tmp_s) {
                        break 2;
                    }
                    $_fmd02->fmd0201 = $fmd02->fmd0201;
                    $_fmd02->fmd0203 = $fmd02->fmd0203;
                    $_fmd02->fmd0204 = $fmd02->fmd0204;
                    $_fmd02->_date = $day[$i];
                }
                $i++;
            }
        } else {
            $_fmd02->_date = today();
        }

        return $_fmd02;
    }

    public function reorder(int $fmd0101, ?array $fmd0203New = null): void
    {
        $fmd02s = $this->getByFmd0101($fmd0101);
        if ($fmd0203New === null) {
            $fmd0203New = $this->getNewFmd0203($fmd0101);
        }

        foreach ($fmd02s as $fmd02) {
            if ($fmd0203New[$fmd02->fmd0203] != $fmd02->fmd0203) {
                $this->db->table($this->table)
                    ->where('fmd0201', $fmd02->fmd0201)
                    ->update(['fmd0203' => $fmd0203New[$fmd02->fmd0203]]);
            }
        }

        // Fix fmd2105
        $fmd01 = $this->db->table('fmd01')->where('fmd0101', $fmd0101)->get()->getRow();
        if ($fmd01) {
            $fmd21s = $this->db->table('fmd21')->where('fmd2102', $fmd01->fmd0106)->get()->getResult();
            foreach ($fmd21s as $fmd21) {
                if ($fmd21->fmd2105) {
                    $fmd2105 = explode(',', $fmd21->fmd2105);
                    $fmd2105New = [];
                    foreach ($fmd2105 as $item) {
                        if (array_key_exists($item, $fmd0203New) && $fmd0203New[$item] != 'X') {
                            $fmd2105New[] = $fmd0203New[$item];
                        }
                    }
                    sort($fmd2105New);
                    $fmd2105New = implode(',', $fmd2105New);
                    if ($fmd21->fmd2105 != $fmd2105New) {
                        $this->db->table('fmd21')
                            ->where('fmd2101', $fmd21->fmd2101)
                            ->update(['fmd2105' => $fmd2105New]);
                    }
                }
            }
        }

        // Fix Fmd0704
        $fmd0203New = array_flip($fmd0203New);
        ksort($fmd0203New);
        $fmd07s = $this->db->table('fmd07')
            ->where('fmd0702', $fmd0101)
            ->where('fmd0704 !=', '')
            ->get()
            ->getResult();

        foreach ($fmd07s as $fmd07) {
            $fmd0704 = '';
            for ($i = 1; $i <= count($fmd02s); $i++) {
                $oldIndex = $fmd0203New[$i] ?? 0;
                if ($oldIndex <= strlen($fmd07->fmd0704) && $oldIndex > 0 && $fmd07->fmd0704[$oldIndex - 1] == '1') {
                    $fmd0704 .= '1';
                } else {
                    $fmd0704 .= '0';
                }
            }
            if ($fmd07->fmd0704 !== $fmd0704) {
                $this->db->table('fmd07')
                    ->where('fmd0701', $fmd07->fmd0701)
                    ->update(['fmd0704' => $fmd0704]);
            }
        }
    }

    public function checkOutData(int $oldId, int $newId): void
    {
        $records = $this->getByFmd0101($oldId);
        foreach ($records as $record) {
            $originalId = $record->fmd0201;
            $record->fmd0202 = $newId;
            $record->fmd0207 = $originalId;  // Store original ID for mapping
            unset($record->fmd0201);
            unset($record->fmd02z1);
            unset($record->fmd02z2);
            unset($record->fmd02z3);
            unset($record->fmd02z4);
            // Use insertData to properly set audit fields
            $this->insertData((array)$record);
        }
    }

    private function getNewFmd0203(int $fmd0101, string $oldFmd0203 = '', string $newFmd0203 = ''): array
    {
        $fmd02s = $this->getByFmd0101($fmd0101);
        $fmd0203 = [];
        foreach ($fmd02s as $fmd02) {
            $fmd0203[$fmd02->fmd0203] = $fmd02->fmd0203;
        }

        asort($fmd0203);
        $i = 1;
        foreach ($fmd0203 as $k => $v) {
            if ($i == $newFmd0203) {
                $i++;
            }
            if ($k == $oldFmd0203) {
                $fmd0203[$k] = $newFmd0203;
            } else {
                $fmd0203[$k] = $i++;
            }
        }

        asort($fmd0203);

        return $fmd0203;
    }

    /**
     * 覆寫 getBy 加入 join
     */
    public function getBy(?array $option = null, int $limit = 0, int $offset = 0, ?string $sort = null): mixed
    {
        $builder = $this->builder();
        $builder->join('fmd01', 'fmd0101=fmd0202');

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
     * 自訂刪除 - 刪除後重新排序
     */
    public function deleteWithReorder(int $id): bool
    {
        $this->db->transStart();

        $fmd02 = $this->find($id);
        if (!$fmd02) {
            return false;
        }

        $fmd0203 = $this->getNewFmd0203($fmd02->fmd0202, $fmd02->fmd0203, 'X');
        $this->delete($id);
        $this->reorder($fmd02->fmd0202, $fmd0203);

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    /**
     * 自訂更新 - 更新後重新排序
     */
    public function updateWithReorder(array $data): bool
    {
        $fmd0203 = null;

        if (array_key_exists('fmd0203', $data)) {
            $fmd02 = $this->find($data['fmd0201']);
            if ($fmd02) {
                $fmd0203 = $this->getNewFmd0203($fmd02->fmd0202, $fmd02->fmd0203, $data['fmd0203']);
            }
        }
        unset($data['fmd0203']);

        $result = $this->updateData($data);

        if ($fmd0203 !== null && $fmd02) {
            $this->reorder($fmd02->fmd0202, $fmd0203);
        }

        return $result > 0;
    }

    /**
     * 自訂插入 - 插入後重新排序
     */
    public function insertWithReorder(array $data): int|false
    {
        $fmd0203 = $this->getNewFmd0203($data['fmd0202'], '0', $data['fmd0203']);
        $fmd0203[0] = $data['fmd0203'];
        $data['fmd0203'] = '0';

        $insertId = $this->insertData($data);

        if ($insertId) {
            $this->reorder($data['fmd0202'], $fmd0203);
        }

        return $insertId;
    }
}
