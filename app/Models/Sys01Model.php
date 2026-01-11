<?php

namespace App\Models;

/**
 * Sys01Model - 使用者帳號模型
 */
class Sys01Model extends BaseModel
{
    protected $table = 'sys01';
    protected $primaryKey = 'sys0101';
    protected $allowedFields = [
        'sys0102', 'sys0103', 'sys0104', 'sys0105', 'sys0106', 'sys0107',
        'sys0108', 'sys0109', 'sys0110', 'sys0111', 'sys0112', 'sys0113',
        'sys0114', 'sys0115', 'sys0116', 'sys0117', 'sys0118', 'sys0119',
        'sys0120', 'sys0121', 'sys0122'
    ];

    protected string $identityColumn = 'sys0102';
    protected string $hashMethod = 'bcrypt';

    public function __construct()
    {
        parent::__construct();

        $rbacConfig = config('Rbac');
        if ($rbacConfig) {
            $this->identityColumn = $rbacConfig->identity ?? 'sys0102';
            $this->hashMethod = $rbacConfig->hashMethod ?? 'bcrypt';
        }
    }

    /**
     * 檢查使用者是否存在
     */
    public function checkUser(string $id): int
    {
        return $this->where('sys0102', $id)->countAllResults() > 0 ? 1 : 0;
    }

    /**
     * 取得登入失敗的次數
     */
    public function getAttemptsNum(string $identity): int
    {
        $builder = $this->db->table('sys03')->select('1');

        $rbacConfig = config('Rbac');
        if ($rbacConfig && ($rbacConfig->trackLoginIpAddress ?? false)) {
            $builder->where('sys0302', service('request')->getIPAddress());
        } else {
            if (strlen($identity) > 0) {
                $builder->orWhere('sys0303', $identity);
            }
        }

        return $builder->countAllResults();
    }

    /**
     * 取得最後登入失敗的時間
     */
    public function getLastAttemptTime(string $identity): string
    {
        $rbacConfig = config('Rbac');
        if (!($rbacConfig->trackLoginAttempts ?? true)) {
            return '0';
        }

        $builder = $this->db->table('sys03')->selectMax('sys0304');

        if ($rbacConfig->trackLoginIpAddress ?? false) {
            $builder->where('sys0302', service('request')->getIPAddress());
        } else {
            if (strlen($identity) > 0) {
                $builder->orWhere('sys0303', $identity);
            }
        }

        $result = $builder->get(1)->getRow();
        return $result->sys0304 ?? '0';
    }

    /**
     * 使用登入帳號取得使用者資料
     */
    public function checkUserByIdentity(string $identity): ?object
    {
        return $this->where($this->identityColumn, $identity)->first();
    }

    /**
     * 檢查自動登入碼
     */
    public function checkRememberCode(string $identity, string $rememberCode): object|false
    {
        $result = $this->select('sys0101,sys0102,sys0103,sys0104,sys0107,sys0108,sys0112,sys0117')
            ->where($this->identityColumn, $identity)
            ->where('sys0116', $rememberCode)
            ->first();

        if (!$result) {
            return false;
        }

        if ($result->sys0108 == 0) {
            $this->message->add(lang('Auth.account_enabled_hint'));
            return false;
        }

        return $result;
    }

    /**
     * 更新使用者最後登入時間
     */
    public function updateLastLogin(int $id): bool
    {
        return $this->db->table('sys01')
            ->where('sys0101', $id)
            ->update(['sys0112' => date('Y-m-d H:i:s')]);
    }

    /**
     * 清除登入失敗紀錄
     */
    public function clearLoginAttempts(string $identity): bool
    {
        $expTime = date('Y-m-d H:i:s', strtotime('-1 day'));
        $builder = $this->db->table('sys03');

        $rbacConfig = config('Rbac');
        if ($rbacConfig && ($rbacConfig->trackLoginIpAddress ?? false)) {
            $builder->where('sys0302', service('request')->getIPAddress());
        }

        $builder->where('sys0303', $identity);
        $builder->orWhere("sys0304 <", $expTime);

        return $builder->delete();
    }

    /**
     * 更新記住登入碼
     */
    public function updateRememberCode(int $id, ?string $rememberCode): int
    {
        if (!$id) {
            return 0;
        }

        $this->db->table('sys01')
            ->where('sys0101', $id)
            ->update(['sys0116' => $rememberCode]);

        return $this->db->affectedRows();
    }

    /**
     * 新增登入失敗紀錄
     */
    public function increaseLoginAttempts(string $identity): bool
    {
        return $this->db->table('sys03')->insert([
            'sys0302' => service('request')->getIPAddress(),
            'sys0303' => $identity,
            'sys0304' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * 依帳號取得使用者
     */
    public function getByIdentity(string $identity, string $select = ''): ?object
    {
        $builder = $this->builder();
        if ($select) {
            $builder->select($select);
        }
        return $builder->where($this->identityColumn, $identity)->get()->getRow();
    }

    /**
     * 忘記密碼處理
     */
    public function forgottenPassword(string $identity): bool
    {
        $activationCode = '';
        if (function_exists('openssl_random_pseudo_bytes')) {
            $activationCode = openssl_random_pseudo_bytes(128);
        }

        for ($i = 0; $i < 1024; $i++) {
            $activationCode = sha1($activationCode . mt_rand() . microtime());
        }

        $update = [
            'sys0114' => $activationCode,
            'sys0115' => date('Y-m-d H:i:s'),
        ];

        $this->db->table('sys01')
            ->where($this->identityColumn, $identity)
            ->update($update);

        return $this->db->affectedRows() == 1;
    }

    /**
     * 依忘記密碼碼取得使用者
     */
    public function getByForgottenPassword(string $code): ?object
    {
        return $this->where('sys0114', $code)->first();
    }

    /**
     * 清除忘記密碼碼
     */
    public function clearForgottenPasswordCode(string $code): bool
    {
        if (empty($code)) {
            return false;
        }

        if ($this->where('sys0114', $code)->countAllResults() > 0) {
            $this->db->table('sys01')
                ->where('sys0114', $code)
                ->update(['sys0114' => null, 'sys0115' => null]);
            return true;
        }

        return false;
    }

    /**
     * 重設密碼
     */
    public function resetPassword(string $identity, string $new): bool
    {
        if (!$this->identityCheck($identity)) {
            return false;
        }

        $result = $this->select('sys0101, sys0105, sys0106')
            ->where($this->identityColumn, $identity)
            ->first();

        if (!$result) {
            $this->message->add(lang('Auth.password_change_failed_hint'));
            return false;
        }

        $newPassword = password_hash($new, PASSWORD_BCRYPT);

        $data = [
            'sys0105' => $newPassword,
            'sys0116' => null,
            'sys0113' => null,
            'sys0114' => null,
        ];

        $this->db->table('sys01')
            ->where($this->identityColumn, $identity)
            ->update($data);

        if ($this->db->affectedRows() == 1) {
            $this->message->add(lang('Auth.password_change_success_hint'));
            return true;
        }

        $this->message->add(lang('Auth.password_change_failed_hint'));
        return false;
    }

    /**
     * 檢查帳號是否存在
     */
    public function identityCheck(string $identity = ''): bool
    {
        if (empty($identity)) {
            return false;
        }

        return $this->where($this->identityColumn, $identity)->countAllResults() > 0;
    }

    /**
     * 變更密碼
     */
    public function changePassword(string $identity, string $old, string $new): bool
    {
        $user = $this->select('sys0101, sys0105, sys0106')
            ->where($this->identityColumn, $identity)
            ->first();

        if (!$user) {
            $this->message->add(lang('Auth.password_change_failed_hint'));
            return false;
        }

        if (!password_verify($old, $user->sys0105)) {
            $this->message->add(lang('Auth.password_change_failed_hint'));
            return false;
        }

        $hashedNewPassword = password_hash($new, PASSWORD_BCRYPT);
        $data = [
            'sys0105' => $hashedNewPassword,
            'sys0116' => null,
        ];

        if ($this->db->table('sys01')->where($this->identityColumn, $identity)->update($data)) {
            $this->message->add(lang('Auth.password_change_success_hint'));
            return true;
        }

        $this->message->add(lang('Auth.password_change_failed_hint'));
        return false;
    }

    /**
     * 取得使用者
     */
    public function user(?int $id = null): ?object
    {
        if (!$id) {
            $id = session()->get('user_id');
        }

        return $this->find($id);
    }

    /**
     * 取得使用者清單
     */
    public function users(?int $limit = null): array
    {
        $builder = $this->builder();
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->get()->getResult();
    }

    /**
     * 新增登入紀錄
     */
    public function addLoginLog(string $identity, int $method, int $status, ?int $userid = null): bool
    {
        $data = [
            'log0202' => date('Y-m-d H:i:s'),
            'log0203' => service('request')->getIPAddress(),
            'log0204' => $identity,
            'log0205' => $status,
            'log0206' => $method,
        ];

        if ($userid) {
            $data['log0207'] = $userid;
        }

        return $this->db->table('log02')->insert($data);
    }

    /**
     * 覆寫 getBy 加入 join
     */
    public function getBy(?array $option = null, int $limit = 0, int $offset = 0, ?string $sort = null): mixed
    {
        $builder = $this->builder();
        $builder->join('sys02', 'sys0202=sys0101');
        $builder->join('ent10', 'ent1001=sys0110', 'left outer');

        if ($option) {
            $this->setWhereValues($builder, $option);
        }

        if ($sort) {
            $this->setOrderBy($builder, $sort);
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
     * 覆寫 countBy 加入 join
     */
    public function countBy(?array $option = null): int
    {
        $builder = $this->builder();
        $builder->join('sys02', 'sys0202=sys0101');
        $builder->join('ent10', 'ent1001=sys0110');

        if ($option) {
            $this->setWhereValues($builder, $option);
        }

        return $builder->countAllResults();
    }

    /**
     * 覆寫 getNameArray
     */
    public function getNameArray(
        string $keyField,
        string $nameFields,
        mixed $data = null,
        ?array $option = null,
        ?string $sort = null
    ): array {
        $builder = $this->builder();
        $builder->select($keyField)->select($nameFields);
        $builder->join('sys02', 'sys0202=sys0101');
        $builder->where('sys0203', session()->get('ent0101'));

        if ($option) {
            $this->setWhereValues($builder, $option);
        }

        if ($sort === null) {
            $sort = 'sys0103,sys0104';
        }
        $this->setOrderBy($builder, $sort);

        $result = $builder->get()->getResult();

        return $this->createNameArray($result, $keyField, $nameFields, $data);
    }
}
