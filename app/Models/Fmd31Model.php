<?php

namespace App\Models;

/**
 * Fmd31Model - 報表通知設定模型
 */
class Fmd31Model extends BaseModel
{
    protected $table = 'fmd31';
    protected $primaryKey = 'fmd3101';
    protected $allowedFields = [
        'fmd3102', 'fmd3103', 'fmd3104', 'fmd3105', 'fmd3106', 'fmd3107', 'fmd3108'
    ];

    public function getAll(): array
    {
        return $this->db->table($this->table)
            ->join('fmd01', 'fmd0101=fmd3102')
            ->get()
            ->getResult();
    }
}
