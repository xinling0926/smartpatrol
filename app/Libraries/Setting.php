<?php

namespace App\Libraries;

/**
 * Setting Library - 系統設定管理
 */
class Setting
{
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
    }

    /**
     * 取得設定值
     */
    public function item(string $key): ?string
    {
        $sessionKey = "setting_{$key}";

        if ($value = $this->session->get($sessionKey)) {
            return $value;
        }

        $result = $this->db->table('sys10')
            ->where('sys1002', $key)
            ->get()
            ->getRow();

        if ($result) {
            $this->session->set($sessionKey, $result->sys1003);
            return $result->sys1003;
        }

        return null;
    }

    /**
     * 取得企業參數設定值
     */
    public function byEnterprise(string $key, ?int $ent0101 = null): ?string
    {
        if (is_null($ent0101)) {
            $ent0101 = $this->session->get('ent0101');
        }

        if (is_null($ent0101)) {
            return '';
        }

        $cacheKey = "ent03_{$ent0101}_{$key}";

        if ($value = $this->session->get($cacheKey)) {
            return $value;
        }

        $result = $this->db->table('ent03')
            ->where('ent0302', $ent0101)
            ->where('ent0303', $key)
            ->get()
            ->getRow();

        if ($result) {
            $this->session->set($cacheKey, $result->ent0304);
            return $result->ent0304;
        }

        return null;
    }

    /**
     * 更新企業設定
     */
    public function updateEnterprise(string $key, string $value, ?int $ent0101 = null): bool
    {
        if (is_null($ent0101)) {
            $ent0101 = $this->session->get('ent0101');
        }

        if (is_null($ent0101)) {
            return false;
        }

        $cacheKey = "ent03_{$ent0101}_{$key}";

        $existing = $this->db->table('ent03')
            ->where('ent0302', $ent0101)
            ->where('ent0303', $key)
            ->get()
            ->getRow();

        if ($existing) {
            $this->db->table('ent03')
                ->where('ent0301', $existing->ent0301)
                ->update(['ent0304' => $value]);
        } else {
            $this->db->table('ent03')->insert([
                'ent0302' => $ent0101,
                'ent0303' => $key,
                'ent0304' => $value
            ]);
        }

        $this->session->set($cacheKey, $value);
        return true;
    }

    /**
     * 存入 cache
     */
    public function saveCache(string $key, string $value): void
    {
        $this->session->set("setting_{$key}", $value);
    }
}
