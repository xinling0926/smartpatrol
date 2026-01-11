<?php

namespace App\Models;

/**
 * Fmd06Model - 欄位設定模型
 */
class Fmd06Model extends BaseModel
{
    protected $table = 'fmd06';
    protected $primaryKey = 'fmd0601';
    protected $allowedFields = [
        'fmd0602', 'fmd0603', 'fmd0604', 'fmd0605', 'fmd0606', 'fmd0607', 'fmd0608', 'fmd0609',
        'fmd0610', 'fmd0611', 'fmd0612', 'fmd0613', 'fmd0614', 'fmd0615', 'fmd0616', 'fmd0617',
        'fmd0618', 'fmd0619', 'fmd06z1', 'fmd06z2', 'fmd06z3', 'fmd06z4'
    ];

    public function getByFmd0101(int $fmd0101, string $select = 'fmd06.*'): array
    {
        return $this->db->table($this->table)
            ->select($select)
            ->join('fmd05', 'fmd0501=fmd0604')
            ->where('fmd0602', $fmd0101)
            ->orderBy('fmd0503')
            ->get()
            ->getResult();
    }

    /**
     * Add field settings for a patrol item
     */
    public function add(int $fmd0101, int $fmd0401): bool
    {
        $this->db->transStart();

        $fmd05Model = model('Fmd05Model');
        $fmd05s = $fmd05Model->getByFmd0101($fmd0101);

        foreach ($fmd05s as $fmd05) {
            $data = [
                'fmd0602' => $fmd0101,
                'fmd0603' => $fmd0401,
                'fmd0604' => $fmd05->fmd0501,
                'fmd0605' => $fmd05->fmd0504,
                'fmd0606' => $fmd05->fmd0505,
                'fmd0607' => $fmd05->fmd0506,
                'fmd0608' => $fmd05->fmd0508,
                'fmd0609' => $fmd05->fmd0509,
                'fmd0611' => $fmd05->fmd0507,
                'fmd0612' => $fmd05->fmd0510,
                'fmd0613' => $fmd05->fmd0511,
                'fmd0614' => $fmd05->fmd0512,
                'fmd0616' => $fmd05->fmd0514 ?? null,
            ];

            // Use insertData to properly set audit fields
            $id = $this->insertData($data);
            $this->db->table($this->table)->where('fmd0601', $id)->update(['fmd0617' => $id]);
        }

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    /**
     * Get field settings by fmd07 ID
     */
    public function getByFmd0701(int $fmd0701): array
    {
        return $this->db->table($this->table)
            ->select('fmd06.*,fmd0504')
            ->where('fmd0610', $fmd0701)
            ->join('fmd04', 'fmd0401=fmd0603')
            ->join('fmd05', 'fmd0501=fmd0604')
            ->orderBy('fmd0409,fmd0503')
            ->get()
            ->getResult();
    }

    public function checkOutData(int $oldId, int $newId): void
    {
        // Get ID mapping tables from related models
        $fmd04Model = model('Fmd04Model');
        $fmd04s = $fmd04Model->getOriginalIdTable($newId);

        $fmd05Model = model('Fmd05Model');
        $fmd05s = $fmd05Model->getOriginalIdTable($newId);

        $fmd07Model = model('Fmd07Model');
        $fmd07s = $fmd07Model->getOriginalIdTable($newId);

        $records = $this->getByFmd0101($oldId);
        foreach ($records as $record) {
            // Only copy if the patrol item (fmd0603) exists in the mapping
            if (array_key_exists($record->fmd0603, $fmd04s)) {
                $originalId = $record->fmd0601;
                $record->fmd0602 = $newId;
                $record->fmd0603 = $fmd04s[$record->fmd0603];  // Map to new fmd04 ID
                // Map fmd0604 to new fmd05 ID if exists in mapping
                if (isset($fmd05s[$record->fmd0604])) {
                    $record->fmd0604 = $fmd05s[$record->fmd0604];
                }
                // Map fmd0610 to new fmd07 ID if exists in mapping
                if ($record->fmd0610 && isset($fmd07s[$record->fmd0610])) {
                    $record->fmd0610 = $fmd07s[$record->fmd0610];
                }
                $record->fmd0615 = $originalId;  // Store original ID for mapping
                unset($record->fmd0601);
                unset($record->fmd06z1);
                unset($record->fmd06z2);
                unset($record->fmd06z3);
                unset($record->fmd06z4);
                // Use insertData to properly set audit fields
                $this->insertData((array)$record);
            }
        }
    }

    /**
     * Copy field settings from source to targets
     */
    public function copy(int $source, array $targets): bool
    {
        $fmd06 = $this->db->table($this->table)
            ->select('fmd0606,fmd0607,fmd0608,fmd0609,fmd0611,fmd0612,fmd0613,fmd0614,fmd0616,fmd0618')
            ->where('fmd0601', $source)
            ->get()
            ->getRow();

        if (!$fmd06) {
            return false;
        }

        foreach ($targets as $targetId) {
            $data = (array)$fmd06;
            $data['fmd0601'] = $targetId;
            $this->updateData($data);
        }

        return true;
    }
}
