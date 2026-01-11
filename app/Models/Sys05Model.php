<?php

namespace App\Models;

/**
 * Sys05Model - 功能表模型
 */
class Sys05Model extends BaseModel
{
    protected $table = 'sys05';
    protected $primaryKey = 'sys0501';
    protected $allowedFields = [
        'sys0502', 'sys0503', 'sys0504', 'sys0505', 'sys0506', 'sys0507', 'sys0508'
    ];

    public function getByEntId(int $entId): array
    {
        return $this->db->table($this->table)
            ->where('sys0502', $entId)
            ->orderBy('sys0503')
            ->get()
            ->getResult();
    }

    /**
     * 取得選單項目
     */
    public function getMenu(?int $parentId = null, bool $orderBySeq = true, string $select = '*'): array
    {
        $builder = $this->db->table($this->table)->select($select);

        if ($parentId === null) {
            $builder->where('sys0506 IS NULL');
        } else {
            $builder->where('sys0506', $parentId);
        }

        if ($orderBySeq) {
            $builder->orderBy('sys0505');
        }

        return $builder->get()->getResult();
    }

    /**
     * 取得使用者選單
     */
    public function getUserMenu(bool $isAdmin, $permissions = []): array
    {
        // 取得權限陣列
        $permArray = [];
        if (is_string($permissions) && !empty($permissions)) {
            $permArray = explode(',', $permissions);
        } elseif (is_array($permissions)) {
            $permArray = $permissions;
        }

        // 取得語言
        $lang = session()->get('lang') ?? 'zh-TW';
        $titleField = ($lang === 'zh-CN') ? 'sys0502' : 'COALESCE(sys0508, sys0502)';

        // 取得根選單項目
        $rootMenus = $this->db->table($this->table)
            ->select("sys05.*, {$titleField} as title, sys04.sys0401, sys04.sys0403, sys04.sys0404, sys04.sys0409")
            ->join('sys04', 'sys04.sys0401 = sys05.sys0505', 'left')
            ->where('sys05.sys0506 IS NULL')
            ->orderBy('sys05.sys0504')
            ->get()
            ->getResult();

        $result = [];

        foreach ($rootMenus as $menu) {
            // 檢查權限
            if (!$isAdmin && $menu->sys0401 && !in_array($menu->sys0401, $permArray)) {
                // 檢查是否有子選單有權限
                $hasPermSubMenu = $this->hasPermittedSubMenu($menu->sys0501, $isAdmin, $permArray);
                if (!$hasPermSubMenu) {
                    continue;
                }
            }

            // 取得子選單
            $subMenus = $this->db->table($this->table)
                ->select("sys05.*, {$titleField} as title, sys04.sys0401, sys04.sys0403, sys04.sys0404, sys04.sys0409")
                ->join('sys04', 'sys04.sys0401 = sys05.sys0505', 'left')
                ->where('sys05.sys0506', $menu->sys0501)
                ->orderBy('sys05.sys0504')
                ->get()
                ->getResult();

            $filteredSubMenus = [];
            foreach ($subMenus as $subMenu) {
                // 檢查權限
                if (!$isAdmin && $subMenu->sys0401 && !in_array($subMenu->sys0401, $permArray)) {
                    continue;
                }
                $filteredSubMenus[] = $subMenu;
            }

            // 如果有子選單，設定到 submenu 屬性
            if (!empty($filteredSubMenus)) {
                $menu->submenu = $filteredSubMenus;
            } else {
                $menu->submenu = null;
            }

            $result[] = $menu;
        }

        return $result;
    }

    /**
     * 檢查是否有被允許的子選單
     */
    protected function hasPermittedSubMenu(int $parentId, bool $isAdmin, array $permArray): bool
    {
        if ($isAdmin) {
            return true;
        }

        $subMenus = $this->db->table($this->table)
            ->select('sys05.sys0501, sys05.sys0505')
            ->join('sys04', 'sys04.sys0401 = sys05.sys0505', 'left')
            ->where('sys05.sys0506', $parentId)
            ->get()
            ->getResult();

        foreach ($subMenus as $subMenu) {
            if ($subMenu->sys0505 && in_array($subMenu->sys0505, $permArray)) {
                return true;
            }
        }

        return false;
    }

}
