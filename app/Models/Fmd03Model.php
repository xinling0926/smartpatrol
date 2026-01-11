<?php

namespace App\Models;

/**
 * Fmd03Model - 欄位設定模型
 */
class Fmd03Model extends BaseModel
{
    protected $table = 'fmd03';
    protected $primaryKey = 'fmd0301';
    protected $allowedFields = [
        'fmd0302', 'fmd0303', 'fmd0304', 'fmd0305', 'fmd0306'
    ];

    public function getByFmd0101(int $fmd0101): array
    {
        return $this->db->table($this->table)
            ->where('fmd0302', $fmd0101)
            ->orderBy('fmd0303')
            ->get()
            ->getResult();
    }

    public function reorder(int $fmd0101): void
    {
        $records = $this->getByFmd0101($fmd0101);
        $n = 1;
        foreach ($records as $record) {
            if ($record->fmd0303 != $n) {
                $this->db->table($this->table)
                    ->where('fmd0301', $record->fmd0301)
                    ->update(['fmd0303' => $n]);
            }
            $n++;
        }
    }

    public function checkOutData(int $oldId, int $newId): void
    {
        $records = $this->getByFmd0101($oldId);
        foreach ($records as $record) {
            $record->fmd0306 = $record->fmd0301;  // Store original ID for mapping
            $record->fmd0302 = $newId;
            unset($record->fmd0301);
            unset($record->fmd03z1);
            unset($record->fmd03z2);
            unset($record->fmd03z3);
            unset($record->fmd03z4);
            // Use insertData to properly set audit fields
            $this->insertData((array)$record);
        }
    }

    /**
     * Get original ID mapping table
     */
    public function getOriginalIdTable(int $id): array
    {
        $fmd03s = $this->db->table($this->table)
            ->select('fmd0301,fmd0306')
            ->where('fmd0302', $id)
            ->orderBy('fmd0306')
            ->get()
            ->getResult();

        $result = [];
        foreach ($fmd03s as $fmd03) {
            $result[$fmd03->fmd0306] = $fmd03->fmd0301;
        }

        return $result;
    }
}
