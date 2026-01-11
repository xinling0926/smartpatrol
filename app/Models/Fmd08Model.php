<?php

namespace App\Models;

/**
 * Fmd08Model - 路線設定模型
 */
class Fmd08Model extends BaseModel
{
    protected $table = 'fmd08';
    protected $primaryKey = 'fmd0801';
    protected $allowedFields = ['fmd0802', 'fmd0803', 'fmd0804', 'fmd0805', 'fmd0806'];

    public function getByFmd0101(int $fmd0101): array
    {
        return $this->db->table($this->table)
            ->where('fmd0802', $fmd0101)
            ->orderBy('fmd0803')
            ->get()
            ->getResult();
    }

    public function checkOutData(int $oldId, int $newId): void
    {
        $records = $this->getByFmd0101($oldId);
        foreach ($records as $record) {
            $originalId = $record->fmd0801;
            unset($record->fmd0801);
            unset($record->fmd08z1);
            unset($record->fmd08z2);
            unset($record->fmd08z3);
            unset($record->fmd08z4);
            $record->fmd0802 = $newId;
            $record->fmd0806 = $originalId;  // Store original ID for mapping
            // Use insertData to properly set audit fields (fmd08z1, fmd08z2)
            $this->insertData((array)$record);
        }
    }

    /**
     * 透過ID取回一筆資料 (含JOIN)
     */
    public function get(int|string $id): ?object
    {
        return $this->builder()
            ->join('fmd01', 'fmd0101=fmd0802')
            ->join('ent10', 'ent1001=fmd0102')
            ->where($this->primaryKey, $id)
            ->get()
            ->getRow();
    }

    /**
     * 計算符合條件的資料筆數
     */
    public function countBy(?array $option = null): int
    {
        $builder = $this->builder();
        $builder->join('fmd01', 'fmd0101=fmd0802')
            ->join('ent10', 'ent1001=fmd0102');

        if ($option) {
            $this->setWhereValues($builder, $option);
        }

        return $builder->countAllResults();
    }

    /**
     * 依條件取回資料
     */
    public function getBy(?array $option = null, int $limit = 0, int $offset = 0, ?string $sort = null): mixed
    {
        $builder = $this->builder();
        $builder->join('fmd01', 'fmd0101=fmd0802')
            ->join('ent10', 'ent1001=fmd0102');

        if ($option) {
            $this->setWhereValues($builder, $option);
        }

        if ($sort) {
            $this->setOrderBy($builder, $sort);
        }

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        if ($limit === 1) {
            return $builder->get()->getRow();
        }

        return $builder->get()->getResult();
    }

    /**
     * 取得 fmd0804 名稱陣列 (路線名稱下拉選單)
     */
    public function getFmd0804Array(mixed $ext = null, ?array $option = null): array
    {
        $session = session();
        $ent0101 = $session->get('ent0101');

        $builder = $this->builder();
        $builder->join('fmd01', 'fmd0101=fmd0802')
            ->join('ent10', 'ent1001=fmd0102');

        if ($option) {
            $this->setWhereValues($builder, $option);
        }

        $builder->where('ent1002', $ent0101)
            ->where('fmd0108', 2)
            ->select('fmd0804')
            ->groupBy('fmd0804')
            ->orderBy('fmd0804');

        $fmd08s = $builder->get()->getResult();

        return $this->createNameArray($fmd08s, 'fmd0804', 'fmd0804', $ext);
    }

    /**
     * 取得指定表單的路線陣列
     */
    public function getFmd0101Array(int|string $fmd0101): array
    {
        $builder = $this->builder();

        if ($fmd0101 !== '') {
            $builder->where('fmd0802', $fmd0101);
        }

        $fmd08s = $builder->orderBy('fmd0803')->get()->getResult();

        return $this->createNameArray($fmd08s, 'fmd0804', 'fmd0804');
    }

    /**
     * 刪除路線 (含附屬巡檢點)
     */
    public function deleteRoute(int|string $id): bool
    {
        // 刪除路線前，先把路線附屬的巡檢點刪了
        $this->db->table('fmd09')->where('fmd0903', $id)->delete();
        return $this->delete($id);
    }

    /**
     * Get original ID mapping table
     */
    public function getOriginalIdTable(int $id): array
    {
        $records = $this->db->table($this->table)
            ->select('fmd0801,fmd0806')
            ->where('fmd0802', $id)
            ->orderBy('fmd0806')
            ->get()
            ->getResult();

        $result = [];
        foreach ($records as $record) {
            $result[$record->fmd0806] = $record->fmd0801;
        }

        return $result;
    }
}
