<?php

namespace App\Models;

/**
 * Dev02Model - 設備記錄模型
 */
class Dev02Model extends BaseModel
{
    protected $table = 'dev02';
    protected $primaryKey = 'dev0201';

    /**
     * 覆寫 getBy 加入 join 和預處理
     */
    public function getBy(?array $option = null, int $limit = 0, int $offset = 0, ?string $sort = null): mixed
    {
        $this->preProcessing($option);

        $builder = $this->builder();
        $builder->join('dev01', 'dev0101=dev0202');

        if ($option) {
            foreach ($option as $key => $value) {
                if (strpos($key, '*') === 0) {
                    $builder->where($value);
                } else {
                    $builder->where($key, $value);
                }
            }
        }

        if ($sort) {
            $builder->orderBy($sort);
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
     * 覆寫 countBy 加入 join 和預處理
     */
    public function countBy(?array $option = null): int
    {
        $this->preProcessing($option);

        $builder = $this->builder();
        $builder->join('dev01', 'dev0101=dev0202');

        if ($option) {
            foreach ($option as $key => $value) {
                if (strpos($key, '*') === 0) {
                    $builder->where($value);
                } else {
                    $builder->where($key, $value);
                }
            }
        }

        return $builder->countAllResults();
    }

    /**
     * 預處理查詢條件
     */
    private function preProcessing(?array &$option): void
    {
        if ($option === null) {
            return;
        }

        if (array_key_exists('dev0203s', $option)) {
            $option['*dev0203s'] = "dev0203>='" . $option['dev0203s'] . " 00:00:00'";
            unset($option['dev0203s']);
        }

        if (array_key_exists('dev0203e', $option)) {
            $option['*dev0203e'] = "dev0203<'" . $option['dev0203e'] . " 23:59:59'";
            unset($option['dev0203e']);
        }

        if (array_key_exists('dev0206k', $option)) {
            if (!empty($option['dev0206k'])) {
                $option['*dev0206k'] = "dev0206 like '%" . $option['dev0206k'] . "%'";
            }
            unset($option['dev0206k']);
        }
    }
}
