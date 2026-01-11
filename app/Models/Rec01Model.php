<?php

namespace App\Models;

/**
 * Rec01Model - 自動註記模型
 */
class Rec01Model extends BaseModel
{
    protected $table = 'rec01';
    protected $primaryKey = 'rec0101';

    public function getAll(): array
    {
        return $this->db->table($this->table)->get()->getResult();
    }
}
