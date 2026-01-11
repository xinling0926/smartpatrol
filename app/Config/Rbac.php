<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * RBAC Configuration
 * 權限控管機制參數設定
 */
class Rbac extends BaseConfig
{
    /**
     * 認證方式:
     * 1=登錄認證,只有在登入時檢查帳號密碼，之後就讀Session值
     * 2=實時認證,每次呼叫都會再檢查一次帳號是否還有效，以及當前權限
     */
    public int $authType = 1;

    /**
     * 是否保留使用者登入紀錄
     */
    public bool $loginLogging = true;

    /**
     * SESSION標記
     */
    public string $authKey = 'auth';

    /**
     * 登入頁面網址
     */
    public string $authGateway = 'auth/login';

    /**
     * 成功登錄後默認跳轉網址
     */
    public string $defaultIndex = '/';

    /**
     * A database column which is used to login with
     */
    public string $identity = 'sys0102';

    /**
     * Minimum Required Length of Password
     */
    public int $minPasswordLength = 4;

    /**
     * Maximum Allowed Length of Password
     */
    public int $maxPasswordLength = 20;

    /**
     * Email Activation for registration
     */
    public bool $emailActivation = false;

    /**
     * Manual Activation for registration
     */
    public bool $manualActivation = false;

    /**
     * Allow users to be remembered and enable auto-login
     */
    public bool $rememberUsers = true;

    /**
     * How long to remember the user (seconds). Set to zero for no expiration
     */
    public int $userExpire = 604800; // 60*60*24*7

    /**
     * Extend the users cookies every time they auto-login
     */
    public bool $userExtendOnLogin = false;

    /**
     * 密碼加密方法：sha1 or PASSWORD_DEFAULT or PASSWORD_BCRYPT
     */
    public string $hashMethod = 'sha1';

    /**
     * 如果使用 PASSWORD_BCRYPT 進行加密，需要在指定算法递归的层数，預設值為10
     */
    public int $hashBcryptCost = 10;

    /**
     * 指定密碼種子的長度，預設為22
     */
    public int $saltLength = 22;

    /**
     * 是否追蹤登入失敗的次數，透過帳號或是IP
     */
    public bool $trackLoginAttempts = true;

    /**
     * 是否透過IP追蹤登入失敗
     */
    public bool $trackLoginIpAddress = false;

    /**
     * 登入失敗的最大次數
     */
    public int $maximumLoginAttempts = 3;

    /**
     * 登入鎖定時間(秒)
     */
    public int $lockoutTime = 600;

    /**
     * The number of seconds after which a forgot password request will expire
     */
    public int $forgotPasswordExpiration = 600;

    /**
     * 是否啟用驗證碼
     */
    public bool $useSecCode = true;
}
