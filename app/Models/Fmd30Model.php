<?php

namespace App\Models;

/**
 * Fmd30Model - 歲修停車模型
 */
class Fmd30Model extends BaseModel
{
    protected $table = 'fmd30';
    protected $primaryKey = 'fmd3001';
    protected $allowedFields = [
        'fmd3002', 'fmd3003', 'fmd3004', 'fmd3005', 'fmd3006', 'fmd3007',
        'fmd3008', 'fmd3009', 'fmd3010', 'fmd30z1', 'fmd30z2', 'fmd30z3', 'fmd30z4'
    ];

    /**
     * 取得啟用中各報表目前最新版本的ID
     */
    public function getByNewestId(): string|false
    {
        $sql = 'select fmd3009,max(fmd3001) as fmd3001 from fmd30 where fmd3006 in (1,2,3) group by fmd3009';
        if ($data = $this->db->query($sql)->getResultArray()) {
            $tmp = implode(',', array_column($data, 'fmd3001'));
            return $tmp;
        }
        return false;
    }

    /**
     * 依使用者部門取得編輯中或是啟用中的報表
     */
    public function getByEdit(?array $option = null, int $limit = 0, int $offset = 0, ?string $sort = null): array
    {
        $builder = $this->builder();
        $this->addDepartmentOption($builder);
        $builder->join('fmd01', 'fmd0101=fmd3002');

        if ($sort === null) {
            $sort = 'fmd0103,fmd0104';
        }

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

        if ($limit) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResult();
    }

    /**
     * 計算報表數量
     */
    public function countByEdit(?array $option = null): int
    {
        $builder = $this->builder();
        $this->addDepartmentOption($builder);
        $builder->join('fmd01', 'fmd0101=fmd3002');

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
     * 部門限制
     */
    private function addDepartmentOption($builder): void
    {
        $currentUser = service('currentUser');
        $userDep = $currentUser->sys0110 ?? null;

        if ($userDep) {
            $ent10Model = model('Ent10Model');
            if ($subDep = $ent10Model->getSubDepartmentId($userDep)) {
                $userDep .= ',' . $subDep;
            }
            $builder->where("fmd0102 in ({$userDep})");
        } else {
            $builder->where('1<>1');
        }
    }

    /**
     * 根據 fmd3001 取巡檢報表資訊
     */
    public function getWithFmd01andEnt10(int $fmd3001): ?object
    {
        $sql = "SELECT * FROM fmd01, fmd30, ent10 WHERE fmd0101 = fmd3002 AND fmd0102 = ent1001 AND fmd3001 = '{$fmd3001}' LIMIT 1";
        return $this->db->query($sql)->getRow();
    }

    /**
     * 更新歲修停車管理狀態
     */
    public function updateFmd3006(int $fmd3001, int $status): int
    {
        $currentUser = service('currentUser');
        $this->db->table($this->table)
            ->where('fmd3001', $fmd3001)
            ->update([
                'fmd3006' => $status,
                'fmd30z3' => $currentUser->sys0101,
                'fmd30z4' => date('Y-m-d H:i:s')
            ]);
        return $this->db->affectedRows();
    }

    /**
     * 變更歲修狀態
     */
    public function changeFormState(int $fmd3001, int $newFmd3006): bool|int
    {
        if (!$fmd30 = $this->find($fmd3001)) {
            return false;
        }

        $currentUser = service('currentUser');

        // 原本是初始者可以啟用或刪除
        if ($fmd30->fmd3006 == 1) {
            if ($newFmd3006 == 0) { // 刪除
                $this->db->table($this->table)->where('fmd3001', $fmd3001)->delete();
                return true;
            }
            if ($newFmd3006 == 2) { // 啟用
                return $this->updateFmd3006($fmd3001, $newFmd3006);
            }
        }

        // 原本是啟用中可以編輯或停用
        if ($fmd30->fmd3006 == 2) {
            if ($newFmd3006 == 3) { // 編輯
                // 複製一筆新紀錄
                $data = [
                    'fmd3002' => $fmd30->fmd3002,
                    'fmd3003' => $fmd30->fmd3003,
                    'fmd3004' => $fmd30->fmd3004,
                    'fmd3005' => $fmd30->fmd3005,
                    'fmd3006' => 3,
                    'fmd3007' => $fmd30->fmd3001,
                    'fmd3008' => $fmd30->fmd3008 + 1,
                    'fmd3009' => $fmd30->fmd3009 ?? $fmd30->fmd3001,
                    'fmd3010' => $fmd30->fmd3010,
                    'fmd30z3' => $currentUser->sys0101,
                    'fmd30z4' => date('Y-m-d H:i:s')
                ];
                return $this->insert($data);
            }
            if ($newFmd3006 == 0) {
                return $this->updateFmd3006($fmd3001, $newFmd3006);
            }
        }

        return false;
    }

    /**
     * 更新繼承表單ID
     */
    public function updateFmd3007(int $fmd3001): int
    {
        $this->db->table($this->table)->set('fmd3007', $fmd3001)->where('fmd3001', $fmd3001)->update();
        return $this->db->affectedRows();
    }

    /**
     * 表單新增後把fmd3001寫到fmd3009, 若只修改表單版本則免
     */
    public function updateFmd3009(int $fmd3001): int
    {
        $this->db->table($this->table)->set('fmd3009', $fmd3001)->where('fmd3001', $fmd3001)->update();
        return $this->db->affectedRows();
    }

    /**
     * 歲修編輯
     */
    public function checkOut(int $fmd3001): int|false
    {
        helper('language');
        if (!$fmd30 = $this->find($fmd3001)) {
            $this->message->add(lang('Model.not_data_hint4'));
            return false;
        }

        if ($fmd30->fmd3006 != 2) {
            $this->message->add(lang('Model.model_fmd01_hint5'));
            return false;
        }

        $lastVersion = $this->db->table($this->table)
            ->where('fmd3009', $fmd30->fmd3009)
            ->orderBy('fmd3008', 'desc')
            ->limit(1)
            ->get()
            ->getRow();

        if ($lastVersion->fmd3006 == 3) {
            // 如果最後一個版已經是編輯中，直接回傳這個版本
            return $lastVersion->fmd3001;
        } elseif ($fmd30->fmd3001 != $lastVersion->fmd3001) {
            $this->message->add(lang('Model.model_fmd01_hint6'));
            return false;
        }

        $currentUser = service('currentUser');

        $this->db->transStart();

        $data = [
            'fmd3002' => $fmd30->fmd3002,
            'fmd3003' => $fmd30->fmd3003,
            'fmd3004' => $fmd30->fmd3004,
            'fmd3005' => $fmd30->fmd3005,
            'fmd3006' => 3,
            'fmd3007' => $fmd30->fmd3001,
            'fmd3008' => $fmd30->fmd3008 + 1,
            'fmd3009' => $fmd30->fmd3009,
            'fmd3010' => $fmd30->fmd3010
        ];
        $newFmd3001 = $this->insert($data);

        if ($this->db->transComplete()) {
            return $newFmd3001;
        }

        return false;
    }

    /**
     * 把編輯中的版本回滾
     */
    public function revert(int $fmd3001): bool
    {
        helper('language');
        if ($fmd30 = $this->find($fmd3001)) {
            if ($fmd30->fmd3006 == 3) {
                if ($fmd30->fmd3008 == 1) {
                    $this->message->add(lang('Model.model_fmd01_hint'));
                } else {
                    $this->db->table($this->table)->where('fmd3001', $fmd3001)->delete();
                    return true;
                }
            } else {
                $this->message->add(lang('Model.model_fmd01_hint1'));
            }
        } else {
            $this->message->add(lang('Model.model_fmd01_hint2'));
        }
        return false;
    }

    /**
     * 提交新版本
     */
    public function commit(int $fmd3001): bool
    {
        helper('language');
        if (!$fmd30 = $this->find($fmd3001)) {
            $this->message->add(lang('Model.model_fmd01_hint2'));
            return false;
        }

        if ($fmd30->fmd3006 != 3) {
            if ($fmd30->fmd3006 != 1) {
                $this->message->add(lang('Model.model_fmd01_hint3'));
                return false;
            }
        }

        $currentUser = service('currentUser');

        $this->db->transStart();

        // 停用舊版本
        $activeFmd30 = $this->db->table($this->table)
            ->where('fmd3009', $fmd30->fmd3009)
            ->where('fmd3006', 2)
            ->limit(1)
            ->get()
            ->getRow();

        if ($activeFmd30) {
            $this->db->table($this->table)
                ->where('fmd3001', $activeFmd30->fmd3001)
                ->update(['fmd3006' => 0]);
        }

        // 啟用新版本
        $this->db->table($this->table)
            ->where('fmd3001', $fmd3001)
            ->update([
                'fmd3006' => 2,
                'fmd30z3' => $currentUser->sys0101,
                'fmd30z4' => date('Y-m-d H:i:s')
            ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            $this->message->add(lang('Model.update_err_hint'));
            return false;
        }

        return true;
    }

    /**
     * 根據 fmd0101 找出歲修報表
     */
    public function getByFmd0101(int $fmd0101): array|false
    {
        $sql = "select * from fmd30 where fmd3002 in ( select fmd0101 from fmd01 where fmd0108 = 2 and fmd0106 = ( select fmd0106 from ( select fmd0106, max(fmd0107) as fmd0107 from fmd01 where fmd0101 = '".$fmd0101."' group by fmd0106) as fmd01a ) ) and fmd3006 in (2,4)";
        if ($fmd30s = $this->db->query($sql)->getResult()) {
            return $fmd30s;
        }
        return false;
    }

    /**
     * 根據 $fmd3001 找出進行中的歲修停車紀錄
     */
    public function getOngoingById(int $fmd3001): object|false
    {
        helper('date');
        $today = today();
        $sql = "select * from fmd30 where fmd3009 in (select fmd3009 from fmd30 where fmd3001 = '{$fmd3001}') and fmd3006 = 2 and date('{$today}') between fmd3003 and fmd3004 limit 1";
        if ($fmd30 = $this->db->query($sql)->getRow()) {
            $fmd30->fmd3003_lock = true;
            return $fmd30;
        }
        return false;
    }
}
