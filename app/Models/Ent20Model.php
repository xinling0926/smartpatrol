<?php

namespace App\Models;

/**
 * Ent20Model - 企業擴展模型
 */
class Ent20Model extends BaseModel
{
    protected $table = 'ent20';
    protected $primaryKey = 'ent2001';
    protected $allowedFields = [
        'ent2002', 'ent2003', 'ent2004', 'ent2005'
    ];
}
