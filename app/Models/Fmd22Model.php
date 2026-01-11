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
        $userTitle = $currentUser->sys0119 ?? '';

        return $this->db->table($this->table)
            ->where("fmd2205 LIKE '%{$userTitle}%'")
            ->get()
            ->getResult();
    }

    public function checkUserApproveFlow(int $fmd0106, int $fmd2101, int $step): bool
    {
        $currentUser = $this->currentUser;
        $userTitle = $currentUser->sys0119 ?? '';

        $result = $this->db->table($this->table)
            ->where('fmd2202', $fmd0106)
            ->where('fmd2203', $fmd2101)
            ->where('fmd2204', $step)
            ->where("fmd2205 LIKE '%{$userTitle}%'")
            ->countAllResults();

        return $result > 0;
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
