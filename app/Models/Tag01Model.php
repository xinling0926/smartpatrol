<?php

namespace App\Models;

/**
 * Tag01Model - 標籤模型
 */
class Tag01Model extends BaseModel
{
    protected $table = 'tag01';
    protected $primaryKey = 'tag0101';
    protected $allowedFields = [
        'tag0102', 'tag0103', 'tag0104', 'tag0105', 'tag0106', 'tag0107'
    ];
}
