<?php

namespace App\Models;

/**
 * Fmd04Model - 巡檢項目模型
 */
class Fmd04Model extends BaseModel
{
    protected $table = 'fmd04';
    protected $primaryKey = 'fmd0401';
    protected $allowedFields = [
        'fmd0402', 'fmd0403', 'fmd0404', 'fmd0405', 'fmd0406', 'fmd0407', 'fmd0408', 'fmd0409', 'fmd0410',
        'fmd04z1', 'fmd04z2', 'fmd04z3', 'fmd04z4'
    ];

    public function getByFmd0101(int $fmd0101, string $select = '*', bool $includeDeleted = false): array
    {
        $builder = $this->db->table($this->table)
            ->select($select)
            ->join('fmd03', 'fmd0301=fmd0403')
            ->where('fmd0402', $fmd0101);

        if (!$includeDeleted) {
            $builder->where('fmd0407 !=', 3);
        }

        return $builder->orderBy('fmd0409,fmd0401')
            ->get()
            ->getResult();
    }

    /**
     * Get first child of a parent item
     */
    public function getFirstChild(int $parentId): ?object
    {
        return $this->db->table($this->table)
            ->where('fmd0405', $parentId)
            ->where('fmd0407 !=', 3)
            ->orderBy('fmd0408')
            ->get()
            ->getRow();
    }

    /**
     * Get fmd0409 based on parent and order
     */
    public function getFmd0409(int $fmd0101, int $fmd0405, int $fmd0408): int
    {
        if ($parent = $this->find($fmd0405)) {
            if ($parent->fmd0410 == 0) {
                return $parent->fmd0409;
            }
        }

        $fmd04 = $this->db->table($this->table)
            ->where('fmd0402', $fmd0101)
            ->where('fmd0405', $fmd0405)
            ->where('fmd0408', $fmd0408)
            ->get()
            ->getRow();

        if ($fmd04) {
            return $fmd04->fmd0409;
        }

        $fmd04 = $this->db->table($this->table)
            ->where('fmd0402', $fmd0101)
            ->where('fmd0405', $fmd0405)
            ->where('fmd0408', $fmd0408 - 1)
            ->get()
            ->getRow();

        if ($fmd04) {
            if ($fmd04->fmd0410 > 1) {
                return $fmd04->fmd0409 + $fmd04->fmd0410;
            } else {
                return $fmd04->fmd0409 + 1;
            }
        }

        return 0;
    }

    /**
     * Adjust order of items
     */
    public function adjustOrder(int $fmd0101, int $parentId, int $startOrder, int $value = 1): int
    {
        $value = round($value);
        if ($value == 0) {
            return 0;
        }

        $builder = $this->db->table($this->table)
            ->where('fmd0402', $fmd0101)
            ->where('fmd0405', $parentId);

        if ($value > 0) {
            $builder->where('fmd0408 >=', $startOrder)
                ->set('fmd0408', 'fmd0408+' . $value, false);
        } else {
            $builder->where('fmd0408 >', $startOrder)
                ->set('fmd0408', 'fmd0408-' . abs($value), false);
        }
        $builder->update();

        return $this->db->affectedRows();
    }

    /**
     * Adjust fmd0409 values
     */
    public function adjustFmd0409(int $fmd0101, int $row, int $value = 1): int
    {
        $value = round($value);
        if ($value == 0) {
            return 0;
        }

        $builder = $this->db->table($this->table)
            ->where('fmd0402', $fmd0101);

        if ($value > 0) {
            $builder->where('fmd0409 >=', $row)
                ->set('fmd0409', 'fmd0409+' . $value, false);
        } else {
            $builder->where('fmd0409 >', $row)
                ->set('fmd0409', 'fmd0409-' . abs($value), false);
        }
        $builder->update();

        return $this->db->affectedRows();
    }

    /**
     * Add a new row of patrol items
     */
    public function add(int $fmd0101, ?object $parent, int $order, array $data): bool
    {
        log_message('debug', 'Fmd04Model::add() called with fmd0101=' . $fmd0101 . ', order=' . $order);
        log_message('debug', 'Parent: ' . ($parent ? json_encode($parent) : 'null'));
        log_message('debug', 'POST data: ' . json_encode($data));

        $this->db->transStart();

        $fmd03Model = model('Fmd03Model');
        $fmd03s = $fmd03Model->getByFmd0101($fmd0101);

        log_message('debug', 'fmd03s count: ' . count($fmd03s));

        if ($parent) {
            foreach ($fmd03s as $k => $fmd03) {
                unset($fmd03s[$k]);
                if ($parent->fmd0403 == $fmd03->fmd0301) {
                    break;
                }
            }
            $fmd0405 = $parent->fmd0401;
        } else {
            $fmd0405 = 0;
        }

        $fmd0409 = $this->getFmd0409($fmd0101, $fmd0405, $order);

        if ($order > 1) {
            if ($parent) {
                $this->adjustOrder($fmd0101, $parent->fmd0401, $order, 1);
            } else {
                $this->adjustOrder($fmd0101, 0, $order, 1);
            }
        }

        if ($parent === null || $parent->fmd0409 != $fmd0409) {
            $this->adjustFmd0409($fmd0101, $fmd0409, 1);
        }

        $lastFmd03 = end($fmd03s);
        reset($fmd03s);

        log_message('debug', 'After removal, fmd03s count: ' . count($fmd03s));
        log_message('debug', 'fmd0409: ' . $fmd0409 . ', fmd0405: ' . $fmd0405);
        log_message('debug', 'lastFmd03: ' . ($lastFmd03 ? json_encode($lastFmd03) : 'false'));

        // Safety check: if no fmd03s remaining, nothing to insert
        if (empty($fmd03s)) {
            log_message('error', 'Fmd04Model::add() - No fmd03s remaining after removal loop');
            $this->db->transComplete();
            return $this->db->transStatus();
        }

        $insertedCount = 0;
        foreach ($fmd03s as $fmd03) {
            $fmd0404 = $data['fmd0404' . $fmd03->fmd0301] ?? '';

            // Check if this is the last fmd03 in the array
            $isLastFmd03 = ($fmd03->fmd0301 == $lastFmd03->fmd0301);

            $d = [
                'fmd0402' => $fmd0101,
                'fmd0403' => $fmd03->fmd0301,
                'fmd0404' => $fmd0404,
                'fmd0405' => $fmd0405,
                'fmd0407' => 1,
                'fmd0408' => $order,
                'fmd0409' => $fmd0409,
                'fmd0410' => $isLastFmd03 ? 0 : 1,
            ];

            // Use insertData to properly set audit fields
            $fmd0405 = $this->insertData($d);
            if (!$fmd0405) {
                log_message('error', 'Failed to insert fmd04 record: ' . json_encode($d));
                log_message('error', 'DB error: ' . json_encode($this->db->error()));
            }
            $insertedCount++;
            log_message('debug', 'Inserted fmd04 record, insertID: ' . $fmd0405);
            $order = 1;
        }

        log_message('debug', 'Total inserted fmd04 records: ' . $insertedCount);

        // Increment parent fmd0410
        if ($parent) {
            $this->incFmd0410($parent->fmd0401);
        }

        $fmd06Model = model('Fmd06Model');
        if ($parent && $parent->fmd0410 == 0) {
            $fmd06s = $fmd06Model->getBy(['fmd0603' => $parent->fmd0401]);
            foreach ($fmd06s as $fmd06) {
                $fmd06Model->updateData(['fmd0601' => $fmd06->fmd0601, 'fmd0603' => $fmd0405]);
            }
        } else {
            $fmd06Model->add($fmd0101, $fmd0405);
        }

        $this->db->transComplete();

        $status = $this->db->transStatus();
        log_message('debug', 'Transaction completed, status: ' . ($status ? 'true' : 'false'));

        return $status;
    }

    /**
     * Increment fmd0410 for a patrol item
     */
    public function incFmd0410(int $id, int $num = 1): void
    {
        $fmd04 = $this->find($id);
        if ($fmd04) {
            if ($fmd04->fmd0410 == 0) {
                $this->db->table($this->table)->where('fmd0401', $id)->set('fmd0410', 1)->update();
            } else {
                $this->db->table($this->table)->where('fmd0401', $id)->set('fmd0410', "fmd0410+{$num}", false)->update();
            }

            if ($fmd04->fmd0405 && $fmd04->fmd0410) {
                $this->incFmd0410($fmd04->fmd0405, $num);
            }
        }
    }

    /**
     * Decrement fmd0410 for a patrol item
     */
    public function decFmd0410(int $id): void
    {
        $fmd04 = $this->find($id);
        if ($fmd04) {
            if ($fmd04->fmd0410 > 0) {
                $this->db->table($this->table)->where('fmd0401', $id)->set('fmd0410', 'fmd0410-1', false)->update();
            }

            if ($fmd04->fmd0405 && $fmd04->fmd0410 > 1) {
                $this->decFmd0410($fmd04->fmd0405);
            }
        }
    }

    /**
     * Recursively delete patrol items
     */
    public function deleteRecursive(int $id): bool
    {
        $fmd04 = $this->find($id);
        if (!$fmd04) {
            return false;
        }

        $children = $this->getBy(['fmd0405' => $id]);
        foreach ($children as $child) {
            $this->deleteRecursive($child->fmd0401);
        }

        $this->adjustOrder($fmd04->fmd0402, $fmd04->fmd0405, $fmd04->fmd0408, -1);

        $parent = $this->find($fmd04->fmd0405);
        if (!$parent || $parent->fmd0409 < $fmd04->fmd0409 || $parent->fmd0410 > 1) {
            $this->adjustFmd0409($fmd04->fmd0402, $fmd04->fmd0409, -1);
        }
        if ($parent) {
            $this->decFmd0410($parent->fmd0401);
        }

        $fmd06Model = model('Fmd06Model');
        if ($parent && $parent->fmd0410 == 1) {
            $fmd06s = $fmd06Model->getBy(['fmd0603' => $fmd04->fmd0401]);
            foreach ($fmd06s as $fmd06) {
                $fmd06Model->updateData(['fmd0601' => $fmd06->fmd0601, 'fmd0603' => $parent->fmd0401]);
            }
        } else {
            $fmd06Model->deleteBy(['fmd0603' => $fmd04->fmd0401]);
        }

        if ($fmd04->fmd0407 == 1) {
            return $this->delete($id);
        } else {
            return $this->updateData(['fmd0401' => $fmd04->fmd0401, 'fmd0407' => 3]) > 0;
        }
    }

    public function checkOutData(int $oldId, int $newId): void
    {
        $fmd03Model = model('Fmd03Model');
        $fmd03s = $fmd03Model->getOriginalIdTable($newId);
        $records = $this->getByFmd0101($oldId);
        $fmd04s = [];

        foreach ($records as $record) {
            $oid = $record->fmd0401;
            $record->fmd0402 = $newId;
            $record->fmd0403 = $fmd03s[$record->fmd0403] ?? $record->fmd0403;
            if ($record->fmd0405) {
                $record->fmd0405 = $fmd04s[$record->fmd0405] ?? $record->fmd0405;
            }
            $record->fmd0406 = $oid;
            $record->fmd0407 = 0;
            unset($record->fmd0401);
            unset($record->fmd04z1);
            unset($record->fmd04z2);
            unset($record->fmd04z3);
            unset($record->fmd04z4);

            // Use insertData to properly set audit fields
            $fmd04s[$oid] = $this->insertData((array)$record);
        }
    }

    /**
     * Get root node of a patrol item
     */
    public function getRootNode(int $fmd0401): ?object
    {
        $item = $this->find($fmd0401);
        if ($item && $item->fmd0405) {
            $item = $this->getRootNode($item->fmd0405);
        }
        return $item;
    }

    /**
     * Reorder patrol items (move up or down)
     * @param int $id The item ID to move
     * @param int $dir Direction: 1=up, 2=down
     */
    public function order(int $id, int $dir): void
    {
        log_message('debug', "Fmd04Model::order() - id: {$id}, dir: {$dir}");

        if ($dir == 1) {
            $fmd04_2 = $this->find($id);
            if (!$fmd04_2) {
                log_message('debug', "Fmd04Model::order() - fmd04_2 not found for id: {$id}");
                return;
            }

            // Debug: 列出所有兄弟項目
            $siblings = $this->db->table($this->table)
                ->select('fmd0401, fmd0404, fmd0408')
                ->where('fmd0402', $fmd04_2->fmd0402)
                ->where('fmd0405', $fmd04_2->fmd0405)
                ->where('fmd0407 !=', 3)
                ->orderBy('fmd0408')
                ->get()
                ->getResult();
            $siblingInfo = array_map(fn($s) => "id:{$s->fmd0401},order:{$s->fmd0408}", $siblings);
            log_message('debug', "Fmd04Model::order() - current item fmd0408={$fmd04_2->fmd0408}, siblings: " . implode(' | ', $siblingInfo));

            $order = $fmd04_2->fmd0408 - 1;
            log_message('debug', "Fmd04Model::order() - looking for fmd0408={$order}, fmd0402={$fmd04_2->fmd0402}, fmd0405={$fmd04_2->fmd0405}");
            $fmd04_1 = $this->db->table($this->table)
                ->where('fmd0402', $fmd04_2->fmd0402)
                ->where('fmd0405', $fmd04_2->fmd0405)
                ->where('fmd0408', $order)
                ->get()
                ->getRow();
        } else {
            $fmd04_1 = $this->find($id);
            if (!$fmd04_1) {
                log_message('debug', "Fmd04Model::order() - fmd04_1 not found for id: {$id}");
                return;
            }

            // Debug: 列出所有兄弟項目
            $siblings = $this->db->table($this->table)
                ->select('fmd0401, fmd0404, fmd0408')
                ->where('fmd0402', $fmd04_1->fmd0402)
                ->where('fmd0405', $fmd04_1->fmd0405)
                ->where('fmd0407 !=', 3)
                ->orderBy('fmd0408')
                ->get()
                ->getResult();
            $siblingInfo = array_map(fn($s) => "id:{$s->fmd0401},order:{$s->fmd0408}", $siblings);
            log_message('debug', "Fmd04Model::order() - current item fmd0408={$fmd04_1->fmd0408}, siblings: " . implode(' | ', $siblingInfo));

            $order = $fmd04_1->fmd0408 + 1;
            log_message('debug', "Fmd04Model::order() - looking for fmd0408={$order}, fmd0402={$fmd04_1->fmd0402}, fmd0405={$fmd04_1->fmd0405}");
            $fmd04_2 = $this->db->table($this->table)
                ->where('fmd0402', $fmd04_1->fmd0402)
                ->where('fmd0405', $fmd04_1->fmd0405)
                ->where('fmd0408', $order)
                ->get()
                ->getRow();
        }

        log_message('debug', "Fmd04Model::order() - fmd04_1: " . ($fmd04_1 ? $fmd04_1->fmd0401 : 'null') . ", fmd04_2: " . ($fmd04_2 ? $fmd04_2->fmd0401 : 'null'));

        if ($fmd04_1 && $fmd04_2) {
            if (!$fmd04_1->fmd0410) {
                $fmd04_1->fmd0410 = 1;
            }
            if (!$fmd04_2->fmd0410) {
                $fmd04_2->fmd0410 = 1;
            }

            $this->db->transStart();

            $this->db->table($this->table)->update(
                ['fmd0408' => $fmd04_2->fmd0408, 'fmd0409' => $fmd04_1->fmd0409 + $fmd04_2->fmd0410],
                ['fmd0401' => $fmd04_1->fmd0401]
            );
            $this->db->table($this->table)->update(
                ['fmd0408' => $fmd04_1->fmd0408, 'fmd0409' => $fmd04_2->fmd0409 - $fmd04_1->fmd0410],
                ['fmd0401' => $fmd04_2->fmd0401]
            );

            $this->adjustChildFmd0409($fmd04_1->fmd0401, $fmd04_2->fmd0410);
            $this->adjustChildFmd0409($fmd04_2->fmd0401, $fmd04_1->fmd0410 * -1);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                log_message('error', "Fmd04Model::order() - transaction failed");
            } else {
                log_message('debug', "Fmd04Model::order() - transaction completed successfully");
            }
        } else {
            log_message('debug', "Fmd04Model::order() - cannot swap, missing fmd04_1 or fmd04_2");
        }
    }

    /**
     * Adjust fmd0409 for child items
     */
    public function adjustChildFmd0409(int $parentId, int $value): void
    {
        $children = $this->db->table($this->table)
            ->where('fmd0405', $parentId)
            ->get()
            ->getResult();

        foreach ($children as $child) {
            $this->db->table($this->table)->update(
                ['fmd0409' => $child->fmd0409 + $value],
                ['fmd0401' => $child->fmd0401]
            );
            if ($child->fmd0410) {
                $this->adjustChildFmd0409($child->fmd0401, $value);
            }
        }
    }

    /**
     * Duplicate a patrol item and its children
     */
    public function duplicate(int $fmd0401): bool
    {
        $fmd04 = $this->find($fmd0401);
        if (!$fmd04) {
            return false;
        }

        $this->db->transStart();

        $this->adjustOrder($fmd04->fmd0402, $fmd04->fmd0405, $fmd04->fmd0408 + 1, 1);
        $adjustFmd0409 = $fmd04->fmd0410 ? $fmd04->fmd0410 : 1;
        $this->adjustFmd0409($fmd04->fmd0402, $fmd04->fmd0409 + $adjustFmd0409, $adjustFmd0409);

        $data = [
            'fmd0402' => $fmd04->fmd0402,
            'fmd0403' => $fmd04->fmd0403,
            'fmd0404' => $fmd04->fmd0404 . lang('BaseModel.copy'),
            'fmd0405' => $fmd04->fmd0405,
            'fmd0406' => null,
            'fmd0407' => 1,
            'fmd0408' => $fmd04->fmd0408 + 1,
            'fmd0409' => $fmd04->fmd0409 + $adjustFmd0409,
            'fmd0410' => $fmd04->fmd0410,
        ];

        $newFmd0401 = $this->insertData($data);

        if ($fmd04->fmd0410) {
            $this->copyChild($fmd0401, $newFmd0401, $adjustFmd0409);
        } else {
            $this->copyFmd06($fmd0401, $newFmd0401);
        }

        $this->incFmd0410($fmd04->fmd0405, $adjustFmd0409);

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    /**
     * Copy child items recursively
     */
    private function copyChild(int $sourceId, int $toId, int $adjustFmd0409): void
    {
        $children = $this->db->table($this->table)
            ->where('fmd0405', $sourceId)
            ->get()
            ->getResult();

        foreach ($children as $fmd04) {
            $fmd0401 = $fmd04->fmd0401;

            $data = [
                'fmd0402' => $fmd04->fmd0402,
                'fmd0403' => $fmd04->fmd0403,
                'fmd0404' => $fmd04->fmd0404,
                'fmd0405' => $toId,
                'fmd0406' => null,
                'fmd0407' => 1,
                'fmd0408' => $fmd04->fmd0408,
                'fmd0409' => $fmd04->fmd0409 + $adjustFmd0409,
                'fmd0410' => $fmd04->fmd0410,
            ];

            $newFmd0401 = $this->insertData($data);

            if ($fmd04->fmd0410) {
                $this->copyChild($fmd0401, $newFmd0401, $adjustFmd0409);
            } else {
                $this->copyFmd06($fmd0401, $newFmd0401);
            }
        }
    }

    /**
     * Copy fmd06 records from one patrol item to another
     */
    private function copyFmd06(int $fromFmd0401, int $toFmd0401): void
    {
        $fmd06Model = model('Fmd06Model');
        $sources = $this->db->table('fmd06')
            ->where('fmd0603', $fromFmd0401)
            ->get()
            ->getResult();

        foreach ($sources as $fmd06) {
            $data = [
                'fmd0602' => $fmd06->fmd0602,
                'fmd0603' => $toFmd0401,
                'fmd0604' => $fmd06->fmd0604,
                'fmd0605' => $fmd06->fmd0605,
                'fmd0606' => $fmd06->fmd0606,
                'fmd0607' => $fmd06->fmd0607,
                'fmd0608' => $fmd06->fmd0608,
                'fmd0609' => $fmd06->fmd0609,
                'fmd0610' => null,
                'fmd0611' => $fmd06->fmd0611,
                'fmd0612' => $fmd06->fmd0612,
                'fmd0613' => $fmd06->fmd0613,
                'fmd0614' => $fmd06->fmd0614,
                'fmd0615' => null,
                'fmd0616' => $fmd06->fmd0616,
                'fmd0617' => null,
                'fmd0618' => $fmd06->fmd0618,
                'fmd0619' => $fmd06->fmd0619,
            ];

            $id = $fmd06Model->insertData($data);
            $this->db->table('fmd06')->where('fmd0601', $id)->update(['fmd0617' => $id]);
        }
    }

    /**
     * Get original ID mapping table
     */
    public function getOriginalIdTable(int $id): array
    {
        $data = $this->db->table($this->table)
            ->select('fmd0401,fmd0406')
            ->where('fmd0402', $id)
            ->orderBy('fmd0406')
            ->get()
            ->getResult();

        $result = [];
        foreach ($data as $d) {
            $result[$d->fmd0406] = $d->fmd0401;
        }

        return $result;
    }

    /**
     * Get patrol item prompt (breadcrumb path)
     */
    public function getPatrolItemPrompt(int $fmd0401): string
    {
        $fmd04 = $this->find($fmd0401);
        if (!$fmd04) {
            return '';
        }

        if ($fmd04->fmd0405) {
            $s = $this->getPatrolItemPrompt($fmd04->fmd0405);
            if ($fmd04->fmd0404) {
                return $s . ' / ' . $fmd04->fmd0404;
            } else {
                return $s;
            }
        } else {
            return $fmd04->fmd0404 ?? '';
        }
    }

    /**
     * Get display names for patrol items associated with an electronic form
     */
    public function getDisplayNameByFmd0701(int $fmd0701): array
    {
        $fmd0603s = $this->db->table('fmd06')
            ->select('fmd0603')
            ->where('fmd0610', $fmd0701)
            ->groupBy('fmd0603')
            ->orderBy('fmd0603')
            ->get()
            ->getResult();

        $result = [];
        foreach ($fmd0603s as $d) {
            $result[$d->fmd0603] = $this->getDisplayName($d->fmd0603);
        }

        return $result;
    }

    /**
     * Get display name for a patrol item (recursive breadcrumb path)
     */
    public function getDisplayName(int $fmd0401): string
    {
        $fmd04 = $this->db->table($this->table)
            ->select('fmd0304,fmd0404,fmd0405')
            ->join('fmd03', 'fmd0301=fmd0403')
            ->where('fmd0401', $fmd0401)
            ->get()
            ->getRow();

        if (!$fmd04) {
            return '';
        }

        if ($fmd04->fmd0405) {
            return $this->getDisplayName($fmd04->fmd0405) . " / {$fmd04->fmd0304}: {$fmd04->fmd0404}";
        } else {
            return "{$fmd04->fmd0304}: {$fmd04->fmd0404}";
        }
    }
}
