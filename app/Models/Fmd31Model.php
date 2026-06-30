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
        // fmd3102 存的是 fmd0106（路線群組），需 JOIN fmd0106 且只取啟用版本 fmd0108=2
        return $this->db->table($this->table)
            ->join('fmd01', 'fmd0106=fmd3102 and fmd0108=2')
            ->orderBy('fmd3102')
            ->get()
            ->getResult();
    }
}
