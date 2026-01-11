<?php

namespace App\Models;

/**
 * Fmd05Model - 欄位定義模型
 */
class Fmd05Model extends BaseModel
{
    protected $table = 'fmd05';
    protected $primaryKey = 'fmd0501';
    protected $allowedFields = [
        'fmd0502', 'fmd0503', 'fmd0504', 'fmd0505', 'fmd0506', 'fmd0507',
        'fmd0508', 'fmd0509', 'fmd0510', 'fmd0511', 'fmd0512', 'fmd0513', 'fmd0514'
    ];

    public function getByFmd0101(int $fmd0101): array
    {
        return $this->db->table($this->table)
            ->where('fmd0502', $fmd0101)
            ->orderBy('fmd0503')
            ->get()
            ->getResult();
    }

    public function checkOutData(int $oldId, int $newId): void
    {
        $records = $this->getByFmd0101($oldId);
        foreach ($records as $record) {
            $originalId = $record->fmd0501;
            $record->fmd0502 = $newId;
            $record->fmd0513 = $originalId;  // Store original ID for mapping
            unset($record->fmd0501);
            unset($record->fmd05z1);
            unset($record->fmd05z2);
            unset($record->fmd05z3);
            unset($record->fmd05z4);
            // Use insertData to properly set audit fields
            $this->insertData((array)$record);
        }
    }

    /**
     * Get original ID mapping table
     */
    public function getOriginalIdTable(int $id): array
    {
        $records = $this->db->table($this->table)
            ->select('fmd0501,fmd0513')
            ->where('fmd0502', $id)
            ->orderBy('fmd0513')
            ->get()
            ->getResult();

        $result = [];
        foreach ($records as $record) {
            $result[$record->fmd0513] = $record->fmd0501;
        }

        return $result;
    }
}
