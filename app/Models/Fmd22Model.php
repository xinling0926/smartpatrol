<?php

namespace App\Models;

/**
 * Fmd22Model - 簽核關卡模型
 */
class Fmd22Model extends BaseModel
{
    protected $table = 'fmd22';
    protected $primaryKey = 'fmd2201';
    protected $allowedFields = [
        'fmd2202', 'fmd2203', 'fmd2204', 'fmd2205', 'fmd2206', 'fmd2207',
        'fmd22z1', 'fmd22z2', 'fmd22z3', 'fmd22z4'
    ];

    /**
     * Get by fmd0106 (same as CI3 get_by_fmd0106)
     */
    public function getByFmd0106(int $fmd0106): array
    {
        return $this->db->table($this->table)
            ->where('fmd2202', $fmd0106)
            ->orderBy('fmd2204')
            ->get()
            ->getResult();
    }

    /**
     * Reorder (same as CI3 reorder)
     */
    public function reorder(int $fmd0106): void
    {
        $fmd22s = $this->db->table($this->table)
            ->select('fmd2201, fmd2203, fmd2204')
            ->where('fmd2202', $fmd0106)
            ->orderBy('fmd2203, fmd2204')
            ->get()
            ->getResult();

        $fmd2203 = 0;
        $i = 1;
        foreach ($fmd22s as $fmd22) {
            if ($fmd2203 != $fmd22->fmd2203) {
                $i = 1;
                $fmd2203 = $fmd22->fmd2203;
            }
            if ($fmd22->fmd2204 != $i) {
                $this->db->table($this->table)
                    ->where('fmd2201', $fmd22->fmd2201)
                    ->update(['fmd2204' => $i]);
            }
            $i++;
        }
    }

    public function getUserApproveFlow(): array
    {
        $currentUser = $this->currentUser;
        if (empty($currentUser->sys0110)) {
            return [];
        }

        $sys0119 = $currentUser->sys0119 ?? '';
        $fmd01Model = model('Fmd01Model');
        $fmd0106s = $fmd01Model->getUserActiveFmd0106();
        $fmd0106 = implode(',', $fmd0106s);

        $sql = "SELECT fmd2202, fmd2203, fmd2204 FROM fmd22 JOIN fmd20 ON fmd2002=fmd2202";
        $sql .= " WHERE fmd2003=2";
        $sql .= " AND (fmd2205='{$sys0119}' OR fmd2205 LIKE '{$sys0119};%' OR fmd2205 LIKE '%;{$sys0119}' OR fmd2205 LIKE '%;{$sys0119};%')";
        if ($fmd0106) {
            $sql .= " AND (fmd2206={$currentUser->sys0110} OR (fmd2206=0 AND fmd2202 IN ({$fmd0106})))";
        } else {
            $sql .= " AND fmd2206={$currentUser->sys0110}";
        }

        return $this->db->query($sql)->getResult();
    }

    public function checkUserApproveFlow(int $fmd0106, int $fmd2101, int $step): bool
    {
        $currentUser = $this->currentUser;
        $sys0119 = $currentUser->sys0119 ?? '';

        $fmd22 = $this->db->table($this->table)
            ->where('fmd2202', $fmd0106)
            ->where('fmd2203', $fmd2101)
            ->where('fmd2204', $step)
            ->get()
            ->getRow();

        if ($fmd22 && in_array($sys0119, explode(';', $fmd22->fmd2205))) {
            if ($fmd22->fmd2206 == 0) {
                $fmd01Model = model('Fmd01Model');
                $fmd0106s = $fmd01Model->getUserActiveFmd0106();
                return in_array($fmd0106, $fmd0106s);
            } else {
                return $fmd22->fmd2206 == $currentUser->sys0110;
            }
        }

        return false;
    }

    public function getLastStep(int $fmd0106, int $fmd2101): int
    {
        $result = $this->db->table($this->table)
            ->selectMax('fmd2204')
            ->where('fmd2202', $fmd0106)
            ->where('fmd2203', $fmd2101)
            ->get()
            ->getRow();

        return $result->fmd2204 ?? 0;
    }
}
