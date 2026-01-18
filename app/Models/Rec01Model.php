<?php

namespace App\Models;

/**
 * Rec01Model - 自動註記模型
 */
class Rec01Model extends BaseModel
{
    protected $table = 'rec01';
    protected $primaryKey = 'rec0101';
    protected $allowedFields = [
        'rec0102', 'rec0103', 'rec0104', 'rec0105', 'rec0106', 'rec0107', 'rec0108'
    ];

    public function getAll(): array
    {
        return $this->db->table($this->table)->get()->getResult();
    }
}
