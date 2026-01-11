<?php

namespace App\Models;

/**
 * Fmd10Model - 表單擴展模型
 */
class Fmd10Model extends BaseModel
{
    protected $table = 'fmd10';
    protected $primaryKey = 'fmd1001';
    protected $allowedFields = [
        'fmd1002', 'fmd1003', 'fmd1004', 'fmd1005', 'fmd1006', 'fmd1007',
        'fmd1008', 'fmd1009', 'fmd1010'
    ];
}
