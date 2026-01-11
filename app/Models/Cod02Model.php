<?php

namespace App\Models;

/**
 * Cod02Model - 代碼模型
 */
class Cod02Model extends BaseModel
{
    protected $table = 'cod02';
    protected $primaryKey = 'cod0201';

    /**
     * 依代碼取得選項陣列
     */
    public function getOptionArrayByCode(string $cod0102, mixed $ext = null): array
    {
        $locale = service('request')->getLocale();
        $field = ($locale === 'zh-CN') ? 'cod0204' : 'cod0207';

        $data = $this->db->table('cod02')
            ->select('cod0203')
            ->select($field)
            ->join('cod01', 'cod0101=cod0202')
            ->where('cod0102', $cod0102)
            ->where('cod0206', 1)
            ->orderBy('cod0205')
            ->get()
            ->getResult();

        return $this->createNameArray($data, 'cod0203', $field, $ext);
    }

    /**
     * 取得代碼值
     */
    public function getValue(string $cod0102, string $cod0203): string|false
    {
        $locale = service('request')->getLocale();
        $field = ($locale === 'zh-CN') ? 'cod0204' : 'cod0207';

        $data = $this->db->table('cod02')
            ->select($field)
            ->join('cod01', 'cod0101=cod0202')
            ->where('cod0102', $cod0102)
            ->where('cod0203', $cod0203)
            ->get()
            ->getRow();

        if ($data) {
            return $data->{$field};
        }

        return false;
    }
}
