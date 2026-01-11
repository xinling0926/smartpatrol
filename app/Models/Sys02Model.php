<?php

namespace App\Models;

/**
 * Sys02Model - 使用者角色關聯模型
 */
class Sys02Model extends BaseModel
{
    protected $table = 'sys02';
    protected $primaryKey = 'sys0201';
    protected $allowedFields = [
        'sys0202', 'sys0203', 'sys0204', 'sys0205', 'sys0206', 'sys0207', 'sys0208'
    ];

    public function getByUserId(int $userId): array
    {
        return $this->db->table($this->table)
            ->where('sys0202', $userId)
            ->get()
            ->getResult();
    }
}
