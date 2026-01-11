<?php

namespace App\Models;

/**
 * Fmd21Model - 簽核分組模型
 */
class Fmd21Model extends BaseModel
{
    protected $table = 'fmd21';
    protected $primaryKey = 'fmd2101';
    protected $allowedFields = [
        'fmd2102', 'fmd2103', 'fmd2104', 'fmd2105',
        'fmd21z1', 'fmd21z2', 'fmd21z3', 'fmd21z4'
    ];

    /**
     * Reorder (same as CI3 reorder)
     */
    public function reorder(int $fmd0106): void
    {
        $fmd21s = $this->getByFmd0106($fmd0106);
        $i = 1;
        foreach ($fmd21s as $fmd21) {
            if ($fmd21->fmd2103 != $i) {
                $this->db->table($this->table)
                    ->where('fmd2101', $fmd21->fmd2101)
                    ->update(['fmd2103' => $i]);
            }
            $i++;
        }
    }

    public function getByFmd0106(int $fmd0106): array
    {
        return $this->db->table($this->table)
            ->where('fmd2102', $fmd0106)
            ->orderBy('fmd2103')
            ->get()
            ->getResult();
    }

    public function getByFmd0203(int $fmd0106, string $fmd0203): ?object
    {
        return $this->db->table($this->table)
            ->join('fmd02', 'fmd0201=fmd2105')
            ->where('fmd2102', $fmd0106)
            ->where('fmd0203', $fmd0203)
            ->get()
            ->getRow();
    }
}
