<?php

namespace App\Models;

/**
 * Ent10Model - 部門模型
 */
class Ent10Model extends BaseModel
{
    protected $table = 'ent10';
    protected $primaryKey = 'ent1001';
    protected $allowedFields = [
        'ent1002', 'ent1003', 'ent1004', 'ent1005', 'ent1006', 'ent1007'
    ];

    /**
     * 取得根部門
     */
    public function getRoot(int $entId): array
    {
        return $this->db->table('ent10')
            ->where('ent1002', $entId)
            ->where('ent1005 IS NULL')
            ->orderBy('ent1004')
            ->get()
            ->getResult();
    }

    /**
     * 取得所屬的部門ID (遞迴)
     */
    public function getSubDepartmentId(int $depId, ?array &$data = null): string
    {
        $top = false;
        if ($data === null) {
            $data = [];
            $top = true;
        }

        $deps = $this->db->table('ent10')
            ->select('ent1001')
            ->where('ent1005', $depId)
            ->get()
            ->getResult();

        foreach ($deps as $dep) {
            $data[] = $dep->ent1001;
            $this->getSubDepartmentId($dep->ent1001, $data);
        }

        if ($top) {
            return implode(',', $data);
        }

        return '';
    }

    /**
     * 取得當前使用者所管理的部門ID
     */
    public function getCurrentUserSeeDepartmentId(): string
    {
        $userDep = $this->currentUser->sys0110 ?? null;

        if ($userDep) {
            $subDep = $this->getSubDepartmentId($userDep);
            if ($subDep) {
                $userDep .= ',' . $subDep;
            }
            return $userDep;
        }

        return '0';
    }
}
