<?php

namespace App\Models;

/**
 * Fmd01Model - 巡檢報表模型
 */
class Fmd01Model extends BaseModel
{
    protected $table = 'fmd01';
    protected $primaryKey = 'fmd0101';
    protected $allowedFields = [
        'fmd0102', 'fmd0103', 'fmd0104', 'fmd0105', 'fmd0106', 'fmd0107',
        'fmd0108', 'fmd0109', 'fmd0110', 'fmd0111'
    ];

    /**
     * 覆寫 getBy 加入 join 和部門限制
     */
    public function getBy(?array $option = null, int $limit = 0, int $offset = 0, ?string $sort = null): mixed
    {
        $builder = $this->builder();
        $builder->join('ent10', 'ent1001=fmd0102');

        // 限制部門
        if ($option && array_key_exists('filter_dep', $option)) {
            if ($option['filter_dep']) {
                $this->addDepartmentOption($builder);
            }
            unset($option['filter_dep']);
        }

        if ($option) {
            $this->setWhereValues($builder, $option);
        }

        if ($sort === null) {
            $sort = 'fmd0103,fmd0104';
        }
        $this->setOrderBy($builder, $sort);

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        if ($limit === 1) {
            return $builder->get()->getRow();
        }

        return $builder->get()->getResult();
    }

    /**
     * 覆寫 countBy 加入 join 和部門限制
     */
    public function countBy(?array $option = null): int
    {
        $builder = $this->builder();
        $builder->join('ent10', 'ent1001=fmd0102');

        if ($option && array_key_exists('filter_dep', $option)) {
            if ($option['filter_dep']) {
                $this->addDepartmentOption($builder);
            }
            unset($option['filter_dep']);
        }

        if ($option) {
            $this->setWhereValues($builder, $option);
        }

        return $builder->countAllResults();
    }

    /**
     * 覆寫 find 加入 join
     */
    public function find($id = null)
    {
        if ($id === null) {
            return parent::find();
        }

        return $this->builder()
            ->join('ent10', 'ent1001=fmd0102')
            ->where($this->primaryKey, $id)
            ->get()
            ->getRow();
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

        if ($option && array_key_exists('filter_dep', $option)) {
            if ($option['filter_dep']) {
                $this->addDepartmentOption($builder);
            }
            unset($option['filter_dep']);
        }

        if ($option) {
            $this->setWhereValues($builder, $option);
        }

        if ($sort === null) {
            $sort = 'fmd0103,fmd0104';
        }
        $this->setOrderBy($builder, $sort);

        $result = $builder->get()->getResult();

        return $this->createNameArray($result, $keyField, $nameFields, $data);
    }

    /**
     * 更新 fmd0106
     */
    public function updateFmd0106(int $fmd0101): int
    {
        $this->db->table('fmd01')
            ->where('fmd0101', $fmd0101)
            ->update(['fmd0106' => $fmd0101]);

        return $this->db->affectedRows();
    }

    /**
     * 修改表單狀態
     */
    public function changeFormState(int $fmd0101, int $newFmd0108): bool
    {
        $fmd01 = $this->find($fmd0101);
        if (!$fmd01) {
            return false;
        }

        if (($fmd01->fmd0108 == 0 && $newFmd0108 == 1)
            || ($fmd01->fmd0108 == 2 && $newFmd0108 == 4)
            || ($fmd01->fmd0108 == 4 && $newFmd0108 == 2)
        ) {
            if ($newFmd0108 == 1) {
                $fmd02Model = model('Fmd02Model');
                $fmd02Model->reorder($fmd0101);

                $fmd03Model = model('Fmd03Model');
                $fmd03Model->reorder($fmd0101);

                $fmd04Model = model('Fmd04Model');
                $fmd04Model->add($fmd0101, null, 1, []);

                $fmd08Model = model('Fmd08Model');
                $fmd08Model->insertData([
                    'fmd0802' => $fmd0101,
                    'fmd0803' => 1,
                    'fmd0804' => $fmd01->fmd0104 . lang('FormItem.patrol_route'),
                    'fmd0805' => 0,
                ]);
            }

            return $this->updateFmd0108($fmd0101, $newFmd0108) > 0;
        }

        return false;
    }

    /**
     * 更新表單狀態
     */
    private function updateFmd0108(int $fmd0101, int $newFmd0108): int
    {
        $data = [
            'fmd0101' => $fmd0101,
            'fmd0108' => $newFmd0108,
        ];

        if ($newFmd0108 == 4) {
            $fmd01 = $this->find($fmd0101);
            $data['fmd0104'] = $fmd01->fmd0104 . lang('FormItem.model_stop');
        }

        return $this->updateData($data);
    }

    /**
     * 加入部門限制條件
     */
    private function addDepartmentOption($builder): void
    {
        $currentUser = $this->currentUser;
        $userDep = $currentUser->sys0110 ?? null;

        if ($userDep) {
            $ent10Model = model('Ent10Model');
            $subDep = $ent10Model->getSubDepartmentId($userDep);
            if ($subDep) {
                $userDep .= ',' . $subDep;
            }
        }

        if ($userDep) {
            $builder->where("fmd0102 IN ({$userDep})");
        } else {
            $builder->where('1<>1');
        }
    }

    /**
     * 取得啟用中各報表目前最新版本的ID
     */
    public function getByNewestId(): string
    {
        $sql = 'SELECT fmd0106, MAX(fmd0101) as fmd0101 FROM fmd01 WHERE fmd0108 IN (1,2) GROUP BY fmd0106';
        $data = $this->db->query($sql)->getResultArray();

        return implode(',', array_column($data, 'fmd0101'));
    }

    /**
     * 依使用者部門取得編輯中或是啟用中的報表
     */
    public function getByEdit(?array $option = null, int $limit = 0, int $offset = 0, ?string $sort = null): array
    {
        $builder = $this->builder();

        // 限制部門
        $this->addDepartmentOption($builder);

        // 限制啟用中或編輯中的
        $id = $this->getByNewestId();
        if ($id) {
            $builder->where("fmd0101 IN ({$id})");
        }

        if ($option) {
            $this->setWhereValues($builder, $option);
        }

        if ($sort === null) {
            $sort = 'fmd0103,fmd0104';
        }
        $this->setOrderBy($builder, $sort);

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResult();
    }

    /**
     * 計算編輯中或啟用中的報表數量
     */
    public function countByEdit(?array $option = null): int
    {
        $builder = $this->builder();

        $this->addDepartmentOption($builder);

        $id = $this->getByNewestId();
        if ($id) {
            $builder->where("fmd0101 IN ({$id})");
        }

        if ($option) {
            $this->setWhereValues($builder, $option);
        }

        return $builder->countAllResults();
    }

    /**
     * 把編輯中的版本回滾
     */
    public function revert(int $id): bool
    {
        $fmd01 = $this->find($id);
        if (!$fmd01) {
            $this->message->add(lang('FormItem.model_fmd01_hint2'));
            return false;
        }

        if ($fmd01->fmd0108 != 1) {
            $this->message->add(lang('FormItem.model_fmd01_hint1'));
            return false;
        }

        if ($fmd01->fmd0107 == 1) {
            $this->message->add(lang('FormItem.model_fmd01_hint'));
            return false;
        }

        // 刪除所有設定資料
        $this->db->table('fmd01')->where('fmd0101', $id)->delete();
        $this->db->table('fmd02')->where('fmd0202', $id)->delete();
        $this->db->table('fmd03')->where('fmd0302', $id)->delete();
        $this->db->table('fmd04')->where('fmd0402', $id)->delete();
        $this->db->table('fmd05')->where('fmd0502', $id)->delete();
        $this->db->table('fmd06')->where('fmd0602', $id)->delete();
        $this->db->table('fmd07')->where('fmd0702', $id)->delete();
        $this->db->table('fmd08')->where('fmd0802', $id)->delete();
        $this->db->table('fmd09')->where('fmd0902', $id)->delete();

        return true;
    }

    /**
     * 提交報表
     */
    public function commit(int $id): bool
    {
        $fmd01 = $this->find($id);
        if (!$fmd01) {
            $this->message->add(lang('FormItem.model_fmd01_hint2'));
            return false;
        }

        if ($fmd01->fmd0108 != 1) {
            $this->message->add(lang('FormItem.model_fmd01_hint3'));
            return false;
        }

        // 檢查 fmd0606=0 and fmd0610 is not null
        $count = $this->db->table('fmd06')
            ->where('fmd0602', $id)
            ->where('fmd0606', 0)
            ->where('fmd0610 IS NOT NULL')
            ->countAllResults();

        if ($count > 0) {
            $this->message->add(lang('FormItem.model_fmd01_hint4_1') . $count . lang('FormItem.model_fmd01_hint4_2'));
            return false;
        }

        // 檢查巡檢項目是不是都加入電子表單了
        $count = $this->db->table('fmd06')
            ->where('fmd0602', $id)
            ->where('fmd0606 !=', 0)
            ->where('fmd0610 IS NULL')
            ->countAllResults();

        if ($count > 0) {
            $this->message->add(lang('FormItem.model_fmd01_hint4_1') . $count . lang('FormItem.model_fmd01_hint4_3'));
            return false;
        }

        $this->db->transStart();

        $activeFmd01 = $this->getBy(['fmd0106' => $fmd01->fmd0106, 'fmd0108' => 2], 1);
        if ($activeFmd01) {
            $this->updateData(['fmd0101' => $activeFmd01->fmd0101, 'fmd0108' => 3]);
        }

        $this->updateData([
            'fmd0101' => $fmd01->fmd0101,
            'fmd0108' => 2,
            'fmd0111' => date('Y-m-d H:i:s'),
        ]);

        // 刪除空白的電子表單
        $this->db->query("DELETE FROM fmd07 WHERE fmd0702={$id} AND fmd0701 NOT IN (SELECT fmd0610 FROM fmd06 WHERE fmd0602={$id} AND fmd0610 IS NOT NULL GROUP BY fmd0610)");

        // 檢查ISO表格是否需要新增欄位
        $isoModel = model('IsoModel');
        $isoModel->setTableName($fmd01);
        $isoModel->checkSchema($fmd01->fmd0101);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            $this->message->add(lang('Model.update_err_hint'));
            return false;
        }

        return true;
    }

    /**
     * 簽出報表
     */
    public function checkOut(int $id): int|false
    {
        $fmd01 = $this->find($id);
        if (!$fmd01) {
            $this->message->add(lang('FormItem.not_data_hint4'));
            return false;
        }

        if ($fmd01->fmd0108 != 2) {
            $this->message->add(lang('FormItem.model_fmd01_hint5'));
            return false;
        }

        $lastVersion = $this->db->table('fmd01')
            ->where('fmd0106', $fmd01->fmd0106)
            ->orderBy('fmd0107', 'desc')
            ->limit(1)
            ->get()
            ->getRow();

        if ($lastVersion->fmd0108 == 1) {
            return $lastVersion->fmd0101;
        }

        if ($fmd01->fmd0101 != $lastVersion->fmd0101) {
            $this->message->add(lang('FormItem.model_fmd01_hint6'));
            return false;
        }

        $this->db->transStart();

        $newData = [
            'fmd0102' => $fmd01->fmd0102,
            'fmd0103' => $fmd01->fmd0103,
            'fmd0104' => $fmd01->fmd0104,
            'fmd0105' => $fmd01->fmd0105,
            'fmd0106' => $fmd01->fmd0106,
            'fmd0107' => $fmd01->fmd0107 + 1,
            'fmd0108' => 1,
            'fmd0110' => $fmd01->fmd0110 ?? null,
        ];

        $fmd0101 = $this->insertData($newData);

        $fmd02Model = model('Fmd02Model');
        $fmd02Model->checkOutData($id, $fmd0101);

        $fmd03Model = model('Fmd03Model');
        $fmd03Model->checkOutData($id, $fmd0101);

        $fmd07Model = model('Fmd07Model');
        $fmd07Model->checkOutData($id, $fmd0101);

        $fmd04Model = model('Fmd04Model');
        $fmd04Model->checkOutData($id, $fmd0101);

        $fmd05Model = model('Fmd05Model');
        $fmd05Model->checkOutData($id, $fmd0101);

        $fmd06Model = model('Fmd06Model');
        $fmd06Model->checkOutData($id, $fmd0101);

        $fmd08Model = model('Fmd08Model');
        $fmd08Model->checkOutData($id, $fmd0101);

        $fmd09Model = model('Fmd09Model');
        $fmd09Model->checkOutData($id, $fmd0101);

        $fmd07Model->fixFmd0708($fmd0101);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return false;
        }

        return $fmd0101;
    }

    /**
     * 取得最後版本
     */
    public function getLastVersion(int $fmd0106): ?object
    {
        return $this->db->table('fmd01')
            ->where('fmd0106', $fmd0106)
            ->where('fmd0108 <', 3)
            ->orderBy('fmd0101', 'desc')
            ->get()
            ->getRow();
    }

    /**
     * 取得當前使用者有權限的報表的fmd0106
     */
    public function getUserActiveFmd0106(): array
    {
        $builder = $this->builder();
        $this->addDepartmentOption($builder);
        $fmd01s = $builder->select('fmd0106')
            ->where('fmd0108', 2)
            ->get()
            ->getResultArray();

        return array_column($fmd01s, 'fmd0106');
    }

    /**
     * 以fmd0106取得該報表當前使用版本
     */
    public function getActiveByFmd0106(int $fmd0106): ?object
    {
        return $this->db->table('fmd01')
            ->join('ent10', 'ent1001=fmd0102')
            ->where('fmd0106', $fmd0106)
            ->where('fmd0108', 2)
            ->get()
            ->getRow();
    }

    /**
     * 依使用者取得部門
     */
    public function getEnt10ByUser(): array
    {
        $builder = $this->builder();
        $this->addDepartmentOption($builder);

        return $builder->select('ent10.*')
            ->join('ent10', 'ent1001=fmd0102')
            ->groupBy('ent1001')
            ->where('fmd0108', 2)
            ->orderBy('ent1004')
            ->get()
            ->getResult();
    }

    /**
     * 获取需要推送到设备的资料
     */
    public function getPushDevice(int $fmd0101): array|false
    {
        $fmd01 = $this->db->query("SELECT fmd0101,fmd0102,fmd0103,fmd0104,fmd0105,fmd0107,fmd0108,fmd0110,fmd01z2,fmd01z4 FROM fmd01 WHERE fmd0101=" . $fmd0101)->getRow();
        if (!isset($fmd01) || !is_object($fmd01)) {
            return false;
        }

        $result = [];
        $result['fmd01'] = [$fmd01];

        if ($fmd01->fmd0108 == 2) {
            $result['fmd02'] = $this->db->query("SELECT fmd0201,fmd0202,fmd0203,fmd0204,fmd0205,fmd0206 FROM fmd02 WHERE fmd0202=" . $fmd0101)->getResult();
            $result['fmd03'] = $this->db->query("SELECT fmd0301,fmd0302,fmd0304 FROM fmd03 WHERE fmd0302=" . $fmd0101)->getResult();
            $result['fmd04'] = $this->db->query("SELECT fmd0401,fmd0402,fmd0403,fmd0404,fmd0405,fmd0409 FROM fmd04 WHERE fmd0402=" . $fmd0101)->getResult();
            $result['fmd05'] = $this->db->query("SELECT fmd0501,fmd0502,fmd0503,fmd0504 FROM fmd05 WHERE fmd0502=" . $fmd0101)->getResult();
            $result['fmd06'] = $this->db->query("SELECT fmd0601,fmd0602,fmd0603,fmd0604,fmd0606,fmd0607,fmd0608,fmd0609,fmd0610,fmd0611,fmd0612,fmd0613,fmd0614,fmd0616 FROM fmd06 WHERE fmd0602=" . $fmd0101)->getResult();
            $result['fmd07'] = $this->db->query("SELECT fmd0701,fmd0702,fmd0703,fmd0704,fmd0706,fmd0708 FROM fmd07 WHERE fmd0702=" . $fmd0101)->getResult();
            $result['fmd08'] = $this->db->query("SELECT fmd0801,fmd0802,fmd0803,fmd0804,fmd0805 FROM fmd08 WHERE fmd0802=" . $fmd0101)->getResult();
            $result['fmd09'] = $this->db->query("SELECT fmd0901,fmd0902,fmd0903,fmd0904,fmd0905,fmd0908,fmd0909,fmd0910,fmd0911 FROM fmd09 WHERE fmd0902=" . $fmd0101)->getResult();
        }

        return $result;
    }
}
