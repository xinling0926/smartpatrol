<?php

namespace App\Libraries;

/**
 * LDAP Library - CI4 Version
 * LDAP/AD 認證函式庫
 */
class Ldap
{
    private mixed $connection;
    private array $params = [];

    /**
     * Constructor
     */
    public function __construct(array $params = [])
    {
        if (!empty($params)) {
            $this->connect($params);
        }
    }

    /**
     * 連接 LDAP 伺服器
     */
    public function connect(array $params): void
    {
        $this->connection = ldap_connect($params['ad_host'], $params['ad_port']);
        ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->connection, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($this->connection, LDAP_OPT_TIMELIMIT, 30);

        $this->setParams($params);
    }

    /**
     * 綁定認證
     */
    public function bind(string $identity, string $password): bool
    {
        $pattern = '/^\w+=(\w+)$/';
        $dn = [];
        $tmp = explode(',', $this->params['ad_rootDn'] ?? '');

        if (is_array($tmp)) {
            foreach ($tmp as $val) {
                if (preg_match($pattern, $val, $matches)) {
                    $dn[] = $matches[1];
                }
            }
        }

        $rootDn = '@' . implode('.', $dn);

        if (@ldap_bind($this->connection, $identity . $rootDn, $password) === false) {
            return false;
        }

        return true;
    }

    /**
     * 設定參數
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * 取得連線物件
     */
    public function getConnection(): mixed
    {
        return $this->connection;
    }

    /**
     * 取得最後錯誤
     */
    public function getError(): string
    {
        return ldap_error($this->connection);
    }

    /**
     * 取得最後錯誤代碼
     */
    public function getErrorNo(): int
    {
        return ldap_errno($this->connection);
    }
}
