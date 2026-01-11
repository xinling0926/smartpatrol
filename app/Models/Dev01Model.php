<?php

namespace App\Models;

/**
 * Dev01Model - 設備模型
 */
class Dev01Model extends BaseModel
{
    protected $table = 'dev01';
    protected $primaryKey = 'dev0101';
    protected $allowedFields = [
        'dev0102', 'dev0103', 'dev0104', 'dev0105', 'dev0106', 'dev0107',
        'dev0108', 'dev0109', 'dev0110'
    ];

    /**
     * 覆寫 getBy 加入 join
     */
    public function getBy(?array $option = null, int $limit = 0, int $offset = 0, ?string $sort = null): mixed
    {
        $builder = $this->builder();
        $builder->join('ent01', 'ent0101=dev0102');
        $builder->join('ent10', 'ent1001=dev0103', 'left outer');

        if ($option) {
            $this->setWhereValues($builder, $option);
        }

        if ($sort === null) {
            $sort = 'dev0104 asc';
        }
        $this->setOrderBy($builder, $sort);

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        if ($limit === 1) {
            return $builder->get()->getRow();
        }

        return $builder->get()->getResult();
    }

    /**
     * 覆寫 find 加入 join
     */
    public function find($id = null)
    {
        if ($id === null) {
            return parent::find();
        }

        return $this->builder()
            ->join('ent01', 'ent0101=dev0102')
            ->join('ent10', 'ent1001=dev0103', 'left outer')
            ->where($this->primaryKey, $id)
            ->get()
            ->getRow();
    }

    /**
     * 覆寫 insertData 處理空值
     */
    public function insertData(array|object $data, bool $log = true): int|false
    {
        if (is_object($data)) {
            $data = (array)$data;
        }

        if (isset($data['dev0103']) && $data['dev0103'] === '') {
            $data['dev0103'] = null;
        }

        return parent::insertData($data, $log);
    }

    /**
     * 覆寫 updateData 處理空值
     */
    public function updateData(array|object $data, bool $log = true): int|false
    {
        if (is_object($data)) {
            $data = (array)$data;
        }

        if (isset($data['dev0103']) && $data['dev0103'] === '') {
            $data['dev0103'] = null;
        }

        return parent::updateData($data, $log);
    }

    /**
     * 獲取某個企業下面所有設備的推送ID
     */
    public function getAllDev0107(int $dev0102 = 0): array
    {
        $dev0107 = [];
        $dev01s = $this->getBy(['dev0102' => $dev0102, 'dev0106' => 1]);

        foreach ($dev01s as $v1) {
            if (empty($v1->dev0107) || trim($v1->dev0107) === 'null') {
                continue;
            }
            $dev0107[] = $v1->dev0107;
        }

        return $dev0107;
    }
}
