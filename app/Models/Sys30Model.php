<?php

namespace App\Models;

/**
 * Sys30Model - 系統擴展模型
 */
class Sys30Model extends BaseModel
{
    protected $table = 'sys30';
    protected $primaryKey = 'sys3001';
    protected $allowedFields = [
        'sys3002', 'sys3003', 'sys3004', 'sys3005', 'sys3006', 'sys3007'
    ];
}
