<?php

namespace App\Models;

/**
 * Sys04Model - 系統設定模型
 */
class Sys04Model extends BaseModel
{
    protected $table = 'sys04';
    protected $primaryKey = 'sys0401';
    protected $allowedFields = [
        'sys0402', 'sys0403', 'sys0404', 'sys0405', 'sys0406', 'sys0407',
        'sys0408', 'sys0409', 'sys0410', 'sys0411', 'sys0412'
    ];

    public function getByKey(string $key, ?int $entId = null): ?object
    {
        $builder = $this->db->table($this->table)->where('sys0402', $key);

        if ($entId !== null) {
            $builder->where('sys0404', $entId);
        }

        return $builder->get()->getRow();
    }

    public function getValue(string $key, ?int $entId = null): ?string
    {
        $result = $this->getByKey($key, $entId);
        return $result ? $result->sys0403 : null;
    }

    public function setValue(string $key, string $value, ?int $entId = null): bool
    {
        $existing = $this->getByKey($key, $entId);

        if ($existing) {
            return $this->db->table($this->table)
                ->where('sys0401', $existing->sys0401)
                ->update(['sys0403' => $value]);
        }

        $data = [
            'sys0402' => $key,
            'sys0403' => $value,
        ];

        if ($entId !== null) {
            $data['sys0404'] = $entId;
        }

        return $this->db->table($this->table)->insert($data);
    }

    /**
     * 取得頁面資訊
     *
     * @param string $folder 資料夾
     * @param string $controller 控制器名稱
     * @param string $action 動作名稱
     * @return object|null
     */
    public function getPageInfo(string $folder, string $controller, string $action): ?object
    {
        // 取得語言
        $lang = session()->get('lang') ?? 'zh-TW';
        $titleField = ($lang === 'zh-CN') ? 'sys0405' : 'COALESCE(sys0412, sys0405)';

        // 產生可能的 controller 名稱格式
        $controllerVariants = $this->generateNameVariants($controller);

        // 產生可能的 action 名稱格式
        $actionVariants = $this->generateNameVariants($action);

        // 去除重複
        $controllerVariants = array_unique($controllerVariants);
        $actionVariants = array_unique($actionVariants);

        $builder = $this->db->table($this->table)
            ->select("*, {$titleField} as title");

        // 嘗試多種格式匹配
        $builder->whereIn('sys0403', $controllerVariants);
        $builder->whereIn('sys0404', $actionVariants);

        if (!empty($folder)) {
            $builder->where('sys0409', $folder);
        }

        return $builder->get()->getRow();
    }

    /**
     * 產生名稱變體 (支援 hyphen, underscore 格式)
     */
    protected function generateNameVariants(string $name): array
    {
        $variants = [$name];

        // 常見的後綴
        $commonSuffixes = ['log', 'report', 'item', 'setting', 'message', 'from', 'to', 'checkup', 'link', 'man', 'edit'];
        foreach ($commonSuffixes as $suffix) {
            if (str_ends_with($name, $suffix) && strlen($name) > strlen($suffix)) {
                $prefix = substr($name, 0, -strlen($suffix));
                $variants[] = $prefix . '-' . $suffix;
                $variants[] = $prefix . '_' . $suffix;
            }
        }

        // 常見的前綴
        $commonPrefixes = ['query', 'form', 'device', 'repair', 'annual', 'approve', 'generate', 'system', 'operation', 'emma', 'android'];
        foreach ($commonPrefixes as $prefix) {
            if (str_starts_with($name, $prefix) && strlen($name) > strlen($prefix)) {
                $suffix = substr($name, strlen($prefix));
                $variants[] = $prefix . '-' . $suffix;
                $variants[] = $prefix . '_' . $suffix;

                // 處理複合名稱 (如 queryreportitem -> query_report_item)
                foreach ($commonSuffixes as $endSuffix) {
                    if (str_ends_with($suffix, $endSuffix) && strlen($suffix) > strlen($endSuffix)) {
                        $middle = substr($suffix, 0, -strlen($endSuffix));
                        $variants[] = $prefix . '_' . $middle . '_' . $endSuffix;
                        $variants[] = $prefix . '-' . $middle . '-' . $endSuffix;
                    }
                }
            }
        }

        return $variants;
    }
}
