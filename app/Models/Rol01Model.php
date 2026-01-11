<?php

namespace App\Models;

/**
 * Rol01Model - 角色模型
 */
class Rol01Model extends BaseModel
{
    protected $table = 'rol01';
    protected $primaryKey = 'rol0101';
    protected $allowedFields = [
        'rol0102', 'rol0103', 'rol0104', 'rol0105', 'rol0106'
    ];

    public function getByEntId(int $entId): array
    {
        return $this->db->table($this->table)
            ->where('rol0102', $entId)
            ->orderBy('rol0104')
            ->get()
            ->getResult();
    }

    /**
     * 取得編輯用角色下拉選單陣列
     *
     * @param int $entId 企業ID
     * @param int|null $currentRole 目前角色ID (確保包含在結果中)
     * @param int $type 類型: 1=全部, 2=請選擇
     * @return array
     */
    public function getEditRole(int $entId, ?int $currentRole = null, int $type = 0): array
    {
        $arr = [];

        // 根據 type 設定預設選項
        switch ($type) {
            case 1:
                $arr = ['' => lang('Globe.all')];
                break;
            case 2:
                $arr = ['' => lang('Globe.dropdown_select')];
                break;
        }

        // 查詢該企業的角色
        $result = $this->db->table($this->table)
            ->select('rol0101, rol0103')
            ->where('rol0102', $entId)
            ->orderBy('rol0104')
            ->get()
            ->getResult();

        foreach ($result as $row) {
            $arr[$row->rol0101] = $row->rol0103;
        }

        return $arr;
    }

    /**
     * 依企業ID和角色名稱查詢
     *
     * @param int $entId 企業ID
     * @param string $roleName 角色名稱
     * @return array
     */
    public function getByRol0102Rol0103(int $entId, string $roleName): array
    {
        return $this->db->table($this->table)
            ->where('rol0102', $entId)
            ->where('rol0103', $roleName)
            ->get()
            ->getResult();
    }
}
