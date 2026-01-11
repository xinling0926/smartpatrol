<?php

namespace App\Models;

/**
 * Fmd09Model - 巡檢點模型
 */
class Fmd09Model extends BaseModel
{
    protected $table = 'fmd09';
    protected $primaryKey = 'fmd0901';
    protected $allowedFields = [
        'fmd0902', 'fmd0903', 'fmd0904', 'fmd0905', 'fmd0906', 'fmd0907',
        'fmd0908', 'fmd0909', 'fmd0910', 'fmd0911'
    ];

    public function getByFmd0101(int $fmd0101): array
    {
        return $this->db->table($this->table)
            ->select('fmd09.*')
            ->join('fmd08', 'fmd0801=fmd0903')
            ->where('fmd0902', $fmd0101)
            ->orderBy('fmd0803,fmd0904')
            ->get()
            ->getResult();
    }

    public function checkOutData(int $oldId, int $newId): void
    {
        // Get ID mapping tables from related models
        $fmd07Model = model('Fmd07Model');
        $fmd07s = $fmd07Model->getOriginalIdTable($newId);

        $fmd08Model = model('Fmd08Model');
        $fmd08s = $fmd08Model->getOriginalIdTable($newId);

        $records = $this->getByFmd0101($oldId);
        foreach ($records as $record) {
            // Only copy if the route (fmd0903) exists in the mapping
            if (!isset($fmd08s[$record->fmd0903])) {
                continue;
            }
            $originalId = $record->fmd0901;
            $record->fmd0902 = $newId;
            $record->fmd0903 = $fmd08s[$record->fmd0903];  // Map to new fmd08 ID
            // Map fmd0906 (comma-separated list of fmd07 IDs)
            if ($record->fmd0906) {
                $fmd0906Array = explode(',', $record->fmd0906);
                for ($i = 0; $i < count($fmd0906Array); $i++) {
                    if (isset($fmd07s[$fmd0906Array[$i]])) {
                        $fmd0906Array[$i] = $fmd07s[$fmd0906Array[$i]];
                    }
                }
                $record->fmd0906 = implode(',', $fmd0906Array);
            }
            $record->fmd0907 = $originalId;  // Store original ID for mapping
            unset($record->fmd0901);
            unset($record->fmd09z1);
            unset($record->fmd09z2);
            unset($record->fmd09z3);
            unset($record->fmd09z4);
            // Use insertData to properly set audit fields
            $this->insertData((array)$record);
        }
    }

    /**
     * Get next fmd0904 value for a route
     */
    public function getNextFmd0904(int $fmd0801): int
    {
        $result = $this->db->table($this->table)
            ->selectMax('fmd0904')
            ->where('fmd0903', $fmd0801)
            ->get()
            ->getRow();

        if ($result->fmd0904 === null) {
            return 1;
        }

        return (int)$result->fmd0904 + 1;
    }

    /**
     * Get original ID mapping table
     */
    public function getOriginalIdTable(int $id): array
    {
        $records = $this->db->table($this->table)
            ->select('fmd0901,fmd0907')
            ->where('fmd0902', $id)
            ->orderBy('fmd0907')
            ->get()
            ->getResult();

        $result = [];
        foreach ($records as $record) {
            $result[$record->fmd0907] = $record->fmd0901;
        }

        return $result;
    }

    /**
     * Get form IDs already assigned to patrol points for a report
     */
    public function getFormIdByFmd0101(int $fmd0101): array
    {
        $fmd09s = $this->db->table($this->table)
            ->select('fmd0906')
            ->where('fmd0902', $fmd0101)
            ->where('fmd0906 !=', '')
            ->get()
            ->getResultArray();

        return array_column($fmd09s, 'fmd0906');
    }

    /**
     * Add electronic forms to a patrol point
     */
    public function addFmd0906(int $fmd0901, array $fmd0906s): bool
    {
        if (!is_array($fmd0906s) || empty($fmd0906s)) {
            return false;
        }

        $this->db->transStart();

        $fmd07Model = model('Fmd07Model');
        foreach ($fmd0906s as $fmd0906) {
            $fmd07Model->updateFmd0708($fmd0906, $fmd0901);
        }

        $fmd09 = $this->find($fmd0901);
        if ($fmd09 && $fmd09->fmd0906) {
            $fmd0906s = array_merge(explode(',', $fmd09->fmd0906), $fmd0906s);
        }

        $this->updateData([
            'fmd0901' => $fmd0901,
            'fmd0906' => implode(',', $fmd0906s)
        ]);

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    /**
     * Delete electronic form from a patrol point
     */
    public function deleteFmd0906(int $fmd0901, int $fmd0906): bool
    {
        $fmd09 = $this->find($fmd0901);
        if (!$fmd09 || !$fmd09->fmd0906) {
            return false;
        }

        $fmd0906s = explode(',', $fmd09->fmd0906);
        $idx = array_search($fmd0906, $fmd0906s);

        if ($idx === false) {
            return false;
        }

        unset($fmd0906s[$idx]);

        $this->db->transStart();

        $fmd07Model = model('Fmd07Model');
        $fmd07Model->updateFmd0708($fmd0906, null);

        $this->updateData([
            'fmd0901' => $fmd0901,
            'fmd0906' => implode(',', $fmd0906s)
        ]);

        $this->db->transComplete();

        return $this->db->transStatus();
    }
}
