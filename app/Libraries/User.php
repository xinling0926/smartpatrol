<?php

namespace App\Libraries;

use Config\Services;

/**
 * User Library - 使用者認證與管理
 */
class User
{
    public string $identityColumn = '';
    public ?int $id = null;
    public ?string $identity = null;
    public string $avatar = '';
    public bool $isAdmin = false;
    public bool $isLocalAdmin = false;

    protected $session;
    protected $db;
    protected $request;
    protected Setting $setting;
    protected Message $message;
    protected array $rbacConfig = [];

    public function __construct()
    {
        $this->session = Services::session();
        $this->db = \Config\Database::connect();
        $this->request = Services::request();
        $this->setting = new Setting();
        $this->message = new Message();

        // Load RBAC config
        $this->loadRbacConfig();

        $this->identityColumn = $this->rbacConfig['identity'] ?? 'sys0102';

        if ($this->loggedIn()) {
            $this->getSessionData();
        } else {
            helper('cookie');
            if (get_cookie('identity') && get_cookie('remember_code')) {
                $this->loginRememberedUser();
            }
        }
    }

    /**
     * Load RBAC configuration
     */
    protected function loadRbacConfig(): void
    {
        $this->rbacConfig = [
            'identity' => 'sys0102',
            'auth_type' => 1,
            'login_logging' => true,
            'track_login_attempts' => true,
            'maximum_login_attempts' => 3,
            'lockout_time' => 600,
            'remember_users' => true,
            'user_expire' => 86500,
            'user_extend_on_login' => false,
            'forgot_password_expiration' => 0,
            'hash_method' => 'password_bcrypt',
            'hash_bcrypt_cost' => 10,
            'salt_length' => 22,
            'default_index' => 'home',
            'auth_gateway' => 'auth/login',
        ];

        // Try to load from config file
        if (class_exists('Config\Rbac')) {
            $config = new \Config\Rbac();
            // Map camelCase to snake_case keys
            $keyMap = [
                'authType' => 'auth_type',
                'loginLogging' => 'login_logging',
                'trackLoginAttempts' => 'track_login_attempts',
                'maximumLoginAttempts' => 'maximum_login_attempts',
                'lockoutTime' => 'lockout_time',
                'rememberUsers' => 'remember_users',
                'userExpire' => 'user_expire',
                'userExtendOnLogin' => 'user_extend_on_login',
                'forgotPasswordExpiration' => 'forgot_password_expiration',
                'hashMethod' => 'hash_method',
                'hashBcryptCost' => 'hash_bcrypt_cost',
                'saltLength' => 'salt_length',
                'defaultIndex' => 'default_index',
                'authGateway' => 'auth_gateway',
                'useSecCode' => 'use_sec_code',
                'minPasswordLength' => 'min_password_length',
                'maxPasswordLength' => 'max_password_length',
            ];

            foreach (get_object_vars($config) as $key => $value) {
                $mappedKey = $keyMap[$key] ?? $key;
                $this->rbacConfig[$mappedKey] = $value;
            }
        }
    }

    /**
     * 認證檢查
     */
    public function authCheck(): void
    {
        // 驗證是否登錄
        if (!$this->loggedIn()) {
            $this->redirectToLogin();
        }

        // 驗證授權
        if (!$this->checkLicense()) {
            header('Location: ' . base_url('auth/license-error'));
            exit;
        }

        // 若為實時認證
        if (($this->rbacConfig['auth_type'] ?? 1) == 2) {
            $currentUser = $this->getCurrentUser();
            if (!$currentUser || !$currentUser->sys0108) {
                $this->redirectToLogin();
            }
            $this->getEnterpriseInfo();
            $this->getPermissions();
        }

        // 驗證權限
        if (!$this->checkRight()) {
            header('Location: ' . base_url('auth/permission-error'));
            exit;
        }
    }

    /**
     * 檢查是否已經登入
     */
    public function loggedIn(): bool
    {
        return (bool)$this->session->get('identity');
    }

    /**
     * Get current user from database
     */
    protected function getCurrentUser(): ?object
    {
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return null;
        }

        return $this->db->table('sys01')
            ->where('sys0101', $userId)
            ->get()
            ->getRow();
    }

    /**
     * 從 Session 取得使用者資訊
     */
    protected function getSessionData(): void
    {
        $this->id = $this->session->get('user_id');
        $this->identity = $this->session->get('identity');
        $this->avatar = $this->session->get('avatar') ?? '';
        $this->isAdmin = (bool)$this->session->get('is_admin');
        $this->isLocalAdmin = (bool)$this->session->get('is_local_admin');
    }

    /**
     * 登入記住的使用者
     */
    public function loginRememberedUser(): bool
    {
        helper('cookie');

        $identity = get_cookie('identity');
        $rememberCode = get_cookie('remember_code');

        if (!$identity || !$rememberCode) {
            return false;
        }

        $user = $this->db->table('sys01')
            ->where($this->identityColumn, $identity)
            ->where('sys0116', $rememberCode)
            ->get()
            ->getRow();

        if ($user) {
            $this->updateLastLogin($user->sys0101);
            $this->setSession($user);

            if ($this->rbacConfig['user_extend_on_login'] ?? false) {
                $this->rememberUser($user->sys0101);
            }

            $this->addLoginLog($identity, 2, 1, $user->sys0101);
            return true;
        }

        $this->addLoginLog($identity, 2, 2);
        return false;
    }

    /**
     * 設定 Session
     */
    protected function setSession(object $user): bool
    {
        $sessionData = [
            'identity' => $user->{$this->identityColumn},
            'user_id' => $user->sys0101,
            'avatar' => $user->sys0117 ? $user->sys0101 . '/' . $user->sys0117 : 'man.png',
            'is_admin' => $this->checkIsAdmin($user),
            'is_local_admin' => $this->checkIsLocalAdmin($user),
        ];

        $this->session->regenerate(true);
        $this->session->set($sessionData);
        $this->getSessionData();
        $this->getEnterpriseInfo();
        $this->getPermissions();

        return true;
    }

    /**
     * 判斷是否為系統管理員
     */
    protected function checkIsAdmin(object $user): bool
    {
        $adminIds = explode(';', $this->setting->item('system_admin') ?? '');
        return in_array($user->sys0101, $adminIds);
    }

    /**
     * 判斷是否為企業管理員
     */
    protected function checkIsLocalAdmin(object $user): bool
    {
        $adminIds = explode(';', $this->setting->item('local_admin') ?? '');
        return in_array($user->sys0101, $adminIds);
    }

    /**
     * 登入
     */
    public function login(string $identity, string $password, bool $remember = false, bool $redirectToDefault = true): bool
    {
        if (empty($identity) || empty($password)) {
            return false;
        }

        if ($this->isTimeLockedOut($identity)) {
            $this->message->add('帳號暫時被鎖定，請稍候再試');
            $this->addLoginLog($identity, 1, 4);
            return false;
        }

        $user = $this->db->table('sys01')
            ->where($this->identityColumn, $identity)
            ->get()
            ->getRow();

        if ($user) {
            $isAuth = false;

            if ($user->sys0121 ?? false) {
                // AD 認證
                $isAuth = $this->adVerify($identity, $password);
                if ($isAuth) {
                    $this->changePassword($user->sys0101, $password);
                }
            } else {
                // 本地密碼認證
                $isAuth = $this->passwordVerify($user->sys0105, $password, $user->sys0106 ?? '');
            }

            if ($isAuth) {
                if (($user->sys0108 ?? 0) == 0) {
                    $this->message->add('帳號尚未啟用');
                    $this->addLoginLog($identity, 1, 3, $user->sys0101);
                    return false;
                }

                $this->setSession($user);
                $this->updateLastLogin($user->sys0101);

                if ($this->rbacConfig['track_login_attempts'] ?? false) {
                    $this->clearLoginAttempts($identity);
                }

                $this->addLoginLog($identity, 1, 1, $user->sys0101);

                if ($remember && ($this->rbacConfig['remember_users'] ?? false)) {
                    $this->rememberUser($user->sys0101);
                }

                if ($redirectToDefault) {
                    $this->redirectToDefault();
                }
                return true;
            }
        }

        $this->addLoginLog($identity, 1, 2);

        if ($this->rbacConfig['track_login_attempts'] ?? false) {
            $this->increaseLoginAttempts($identity);
            if ($this->isMaxLoginAttemptsExceeded($identity)) {
                $this->lockNotify($identity);
            }
        }

        $this->message->add('登入失敗');
        return false;
    }

    /**
     * AD 認證
     */
    public function adVerify(string $identity, string $password): bool
    {
        $ldap = new Ldap([
            'host' => $this->setting->item('ad_host'),
            'port' => (int)$this->setting->item('ad_port'),
            'rootDn' => $this->setting->item('ad_rootDn'),
        ]);

        return $ldap->bind($identity, $password);
    }

    /**
     * 密碼驗證
     */
    public function passwordVerify(string $hash, string $password, string $salt = ''): bool
    {
        $hashMethod = $this->rbacConfig['hash_method'] ?? 'password_bcrypt';

        // 除錯資訊
        log_message('debug', "=== Password Verify Debug ===");
        log_message('debug', "Hash Method: {$hashMethod}");
        log_message('debug', "Salt: {$salt}");
        log_message('debug', "Stored Hash: {$hash}");
        log_message('debug', "Calculated SHA1 (salt+pwd): " . sha1($salt . $password));
        log_message('debug', "Calculated SHA1 (pwd+salt): " . sha1($password . $salt));

        switch (strtolower($hashMethod)) {
            case 'sha1':
                // 嘗試兩種順序以相容舊版 CI3
                // CI3 Ion Auth 通常使用 password + salt 的順序
                if (sha1($password . $salt) === $hash) {
                    return true;
                }
                // 也嘗試 salt + password 的順序
                return sha1($salt . $password) === $hash;
            case 'password_default':
            case 'password_bcrypt':
                return password_verify($salt . $password, $hash);
            default:
                return false;
        }
    }

    /**
     * 密碼加密
     */
    public function hashPassword(string $password, string $salt = ''): string
    {
        $hashMethod = $this->rbacConfig['hash_method'] ?? 'password_bcrypt';

        switch (strtolower($hashMethod)) {
            case 'sha1':
                return sha1($salt . $password);
            case 'password_default':
                return password_hash($salt . $password, PASSWORD_DEFAULT);
            case 'password_bcrypt':
            default:
                return password_hash($salt . $password, PASSWORD_BCRYPT);
        }
    }

    /**
     * 產生密碼種子
     */
    public function generateSalt(?int $saltLength = null): string
    {
        if ($saltLength === null) {
            $saltLength = $this->rbacConfig['salt_length'] ?? 22;
        }

        return bin2hex(random_bytes($saltLength / 2));
    }

    /**
     * 判斷使用者帳號是否被鎖定
     */
    public function isTimeLockedOut(string $identity): bool
    {
        return $this->isMaxLoginAttemptsExceeded($identity) && $this->isOutOfLockoutTime($identity);
    }

    /**
     * 檢查是否超過最大登入嘗試次數
     */
    protected function isMaxLoginAttemptsExceeded(string $identity): bool
    {
        if ($this->rbacConfig['track_login_attempts'] ?? false) {
            $maxAttempts = $this->rbacConfig['maximum_login_attempts'] ?? 3;
            if ($maxAttempts > 0) {
                $attempts = $this->getLoginAttemptsCount($identity);
                return $attempts >= $maxAttempts;
            }
        }
        return false;
    }

    /**
     * 取得登入嘗試次數
     */
    protected function getLoginAttemptsCount(string $identity): int
    {
        $result = $this->db->table('log01')
            ->where('log0102', $identity)
            ->countAllResults();

        return (int)$result;
    }

    /**
     * 判斷是否超過鎖定時間
     */
    protected function isOutOfLockoutTime(string $identity): bool
    {
        $result = $this->db->table('log01')
            ->select('log0103')
            ->where('log0102', $identity)
            ->orderBy('log0103', 'DESC')
            ->get()
            ->getRow();

        if (!$result) {
            return false;
        }

        $lockoutTime = $this->rbacConfig['lockout_time'] ?? 600;
        $expTime = date('Y-m-d H:i:s', time() - $lockoutTime);

        return $result->log0103 > $expTime;
    }

    /**
     * 增加登入嘗試次數
     */
    public function increaseLoginAttempts(string $identity): void
    {
        $this->db->table('log01')->insert([
            'log0102' => $identity,
            'log0103' => date('Y-m-d H:i:s'),
            'log0104' => $this->request->getIPAddress(),
        ]);
    }

    /**
     * 清除登入嘗試記錄
     */
    public function clearLoginAttempts(string $identity): void
    {
        $this->db->table('log01')
            ->where('log0102', $identity)
            ->delete();
    }

    /**
     * 記住使用者
     */
    public function rememberUser(int $id): bool
    {
        if (!$id) {
            return false;
        }

        $user = $this->db->table('sys01')
            ->where('sys0101', $id)
            ->get()
            ->getRow();

        if (!$user) {
            return false;
        }

        $salt = $this->generateSalt(40);

        $this->db->table('sys01')
            ->where('sys0101', $id)
            ->update(['sys0116' => $salt]);

        $expire = $this->rbacConfig['user_expire'] ?? 86500;
        if ($expire === 0) {
            $expire = 60 * 60 * 24 * 365 * 2;
        }

        helper('cookie');
        set_cookie('identity', $user->{$this->identityColumn}, $expire);
        set_cookie('remember_code', $salt, $expire);

        return true;
    }

    /**
     * 登出
     */
    public function logout(): bool
    {
        if ($this->loggedIn()) {
            $this->addLoginLog($this->identity, 3, 1, $this->id);
        }

        helper('cookie');
        delete_cookie('identity');
        delete_cookie('remember_code');

        $this->session->destroy();
        $this->redirectToLogin();

        return true;
    }

    /**
     * 更新最後登入時間
     */
    protected function updateLastLogin(int $userId): void
    {
        $this->db->table('sys01')
            ->where('sys0101', $userId)
            ->update(['sys0112' => date('Y-m-d H:i:s')]);
    }

    /**
     * 新增登入記錄
     */
    public function addLoginLog(string $identity, int $method, int $status, ?int $userId = null): void
    {
        if ($this->rbacConfig['login_logging'] ?? false) {
            $this->db->table('log02')->insert([
                'log0202' => $identity,
                'log0203' => date('Y-m-d H:i:s'),
                'log0204' => $this->request->getIPAddress(),
                'log0205' => $method,
                'log0206' => $status,
                'log0207' => $userId,
            ]);
        }
    }

    /**
     * 取得企業資訊
     */
    protected function getEnterpriseInfo(): void
    {
        $sys02 = $this->db->table('sys02')
            ->where('sys0202', $this->id)
            ->get()
            ->getRow();

        if ($sys02) {
            $ent01 = $this->db->table('ent01')
                ->where('ent0101', $sys02->sys0203)
                ->get()
                ->getRow();

            if ($ent01 && $ent01->ent0104) {
                $this->session->set([
                    'ent0101' => $ent01->ent0101,
                    'ent0102' => $ent01->ent0102,
                    'ent0103' => $ent01->ent0103,
                    'ent0105' => $ent01->ent0105 ?? null,
                    'rol0101' => $sys02->sys0204,
                ]);
                return;
            }
        }

        header('Location: ' . base_url('auth/no-enterprise'));
        exit;
    }

    /**
     * 取得權限
     */
    protected function getPermissions(): void
    {
        $rolId = $this->session->get('rol0101');
        if (!$rolId) {
            return;
        }

        $role = $this->db->table('rol01')
            ->where('rol0101', $rolId)
            ->get()
            ->getRow();

        if ($role) {
            $this->session->set('permissions', $role->rol0105);
            $this->session->remove('menu');
        }
    }

    /**
     * 檢查授權
     */
    protected function checkLicense(): bool
    {
        // 簡化版本，直接返回 true
        return true;
    }

    /**
     * 檢查權限
     */
    protected function checkRight(): bool
    {
        // 簡化版本，直接返回 true
        return true;
    }

    /**
     * 跳轉到預設頁面
     */
    public function redirectToDefault(): void
    {
        $defaultIndex = $this->rbacConfig['default_index'] ?? 'home';
        header('Location: ' . base_url($defaultIndex));
        exit;
    }

    /**
     * 跳轉到登入頁面
     */
    public function redirectToLogin(): void
    {
        $authGateway = $this->rbacConfig['auth_gateway'] ?? 'auth/login';
        header('Location: ' . base_url($authGateway));
        exit;
    }

    /**
     * 修改密碼
     */
    public function changePassword(int $userId, string $newPassword): bool
    {
        $salt = $this->generateSalt();
        $hash = $this->hashPassword($newPassword, $salt);

        return $this->db->table('sys01')
            ->where('sys0101', $userId)
            ->update([
                'sys0105' => $hash,
                'sys0106' => $salt,
                'sys0113' => date('Y-m-d H:i:s'),
                'sys0114' => null,
                'sys0115' => null,
            ]);
    }

    /**
     * 鎖定通知
     */
    public function lockNotify(string $identity): bool
    {
        // 簡化版本
        return true;
    }

    /**
     * 取得使用者名稱
     */
    public function name(int $id): string
    {
        $user = $this->db->table('sys01')
            ->where('sys0101', $id)
            ->get()
            ->getRow();

        return $user->name ?? '';
    }

    /**
     * 取得訊息輸出
     */
    public function getMessageOutput(): string
    {
        return $this->message->output();
    }

    /**
     * 取得當前使用者資訊
     */
    public function current(): ?object
    {
        if (!$this->id) {
            return null;
        }

        return $this->db->table('sys01')
            ->where('sys0101', $this->id)
            ->get()
            ->getRow();
    }
}
