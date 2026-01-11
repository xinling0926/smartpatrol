<?php

namespace App\Models;

/**
 * Fmd07Model - 電子表單模型
 */
class Fmd07Model extends BaseModel
{
    protected $table = 'fmd07';
    protected $primaryKey = 'fmd0701';
    protected $allowedFields = [
        'fmd0702', 'fmd0703', 'fmd0704', 'fmd0705', 'fmd0706', 'fmd0707', 'fmd0708'
    ];

    public function getByFmd0101(int $fmd0101): array
    {
        return $this->db->table($this->table)
            ->where('fmd0702', $fmd0101)
            ->orderBy('fmd0703')
            ->get()
            ->getResult();
    }

    public function checkOutData(int $oldId, int $newId): void
    {
        $records = $this->getByFmd0101($oldId);
        foreach ($records as $record) {
            $originalId = $record->fmd0701;
            $record->fmd0702 = $newId;
            $record->fmd0705 = $originalId;  // Store original ID for mapping
            unset($record->fmd0701);
            unset($record->fmd07z1);
            unset($record->fmd07z2);
            unset($record->fmd07z3);
            unset($record->fmd07z4);
            // Use insertData to properly set audit fields (fmd07z1, fmd07z2)
            $this->insertData((array)$record);
        }
    }

    /**
     * Fix fmd0708 references after checkout
     */
    public function fixFmd0708(int $fmd0101): void
    {
        $fmd09Model = model('Fmd09Model');
        $fmd09s = $fmd09Model->getOriginalIdTable($fmd0101);
        $records = $this->getByFmd0101($fmd0101);

        foreach ($records as $record) {
            if ($record->fmd0708) {
                if (array_key_exists($record->fmd0708, $fmd09s)) {
                    $this->db->table($this->table)
                        ->set('fmd0708', $fmd09s[$record->fmd0708])
                        ->where('fmd0701', $record->fmd0701)
                        ->update();
                } else {
                    $this->db->table($this->table)
                        ->set('fmd0708', null)
                        ->where('fmd0701', $record->fmd0701)
                        ->update();
                }
            }
        }
    }

    /**
     * Get original ID mapping table
     */
    public function getOriginalIdTable(int $id): array
    {
        $records = $this->db->table($this->table)
            ->select('fmd0701,fmd0705')
            ->where('fmd0702', $id)
            ->orderBy('fmd0705')
            ->get()
            ->getResult();

        $result = [];
        foreach ($records as $record) {
            $result[$record->fmd0705] = $record->fmd0701;
        }

        return $result;
    }

    /**
     * Delete all fmd07 records for a report
     */
    public function deleteByFmd0101(int $fmd0101): void
    {
        $this->db->table($this->table)->where('fmd0702', $fmd0101)->delete();
        $this->db->table('fmd06')->set('fmd0610', null)->where('fmd0602', $fmd0101)->update();
        $this->db->table('fmd09')->set('fmd0906', '')->where('fmd0902', $fmd0101)->update();
    }

    /**
     * Add a new electronic form
     */
    public function add(int $fmd0101, string $name, string $fmd0704 = '', string $fmd0706 = ''): int
    {
        $data = [
            'fmd0702' => $fmd0101,
            'fmd0703' => $name,
            'fmd0704' => $fmd0704,
            'fmd0706' => $fmd0706,
        ];
        // Use insertData to properly set audit fields (fmd07z1, fmd07z2)
        $id = $this->insertData($data);

        if ($id) {
            // Set fmd0707 to the same value as fmd0701 (self-reference)
            $this->db->table($this->table)->set('fmd0707', $id)->where('fmd0701', $id)->update();
        } else {
            log_message('error', 'Fmd07Model::add() - insertData failed for fmd0101=' . $fmd0101);
        }

        return $id ?: 0;
    }

    /**
     * Auto split electronic forms based on selected fields
     */
    public function autoSplit(int $fmd0101, array $fields, string $prefix = '', string $suffix = ''): void
    {
        $fmd06Model = model('Fmd06Model');

        $fmd04s = $this->db->table('fmd04')
            ->select('fmd0401,fmd0403,fmd0404,fmd0405')
            ->where('fmd0402', $fmd0101)
            ->where('fmd0407 !=', 3)
            ->get()
            ->getResult();

        $fmd04sKey = [];
        foreach ($fmd04s as $k => $fmd04) {
            $fmd04sKey[$k] = $fmd04->fmd0401;
        }

        $arr47 = []; // Map fmd0603 to fmd07
        $fmd07s = []; // Map field value to fmd07

        // Find all patrol items without electronic form
        $fmd06s = $this->db->query("SELECT fmd0601,fmd0603 FROM fmd06 WHERE fmd0602={$fmd0101} AND fmd0606>0 AND fmd0610 IS NULL")->getResult();

        foreach ($fmd06s as $fmd06) {
            if (array_search($fmd06->fmd0603, $fmd04sKey) === false) {
                continue;
            }

            $fmd0701 = 0;
            if (array_key_exists($fmd06->fmd0603, $arr47) === false) {
                $formId = '';
                $formName = '';
                $id = $fmd06->fmd0603;

                while ($id) {
                    $idx = array_search($id, $fmd04sKey);
                    if ($idx !== false) {
                        $fmd04 = $fmd04s[$idx];
                        if (in_array($fmd04->fmd0403, $fields)) {
                            $formId = "{$fmd04->fmd0401}_{$formId}";
                            $formName = trim($fmd04->fmd0404) . $formName;
                        }
                        $id = $fmd04->fmd0405;
                    } else {
                        break;
                    }
                }

                if (array_key_exists($formId, $fmd07s)) {
                    $fmd0701 = $fmd07s[$formId];
                    $arr47[$fmd06->fmd0603] = $fmd0701;
                } else {
                    if ($formId) {
                        $fmd0701 = $this->add($fmd0101, $prefix . $formName . $suffix, '', '');
                        $fmd07s[$formId] = $fmd0701;
                        $arr47[$fmd06->fmd0603] = $fmd0701;
                    }
                }
            } else {
                $fmd0701 = $arr47[$fmd06->fmd0603];
            }

            if ($fmd0701) {
                $fmd06Model->updateData(['fmd0601' => $fmd06->fmd0601, 'fmd0610' => $fmd0701]);
            }
        }
    }

    /**
     * Update fmd0708 (patrol point reference) for an electronic form
     */
    public function updateFmd0708(int $fmd0701, ?int $fmd0708): bool
    {
        return $this->db->table($this->table)
            ->where('fmd0701', $fmd0701)
            ->update(['fmd0708' => $fmd0708]);
    }
}
