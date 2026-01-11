<?php

namespace App\Models;

/**
 * Fmd20Model - 簽核流程模型
 */
class Fmd20Model extends BaseModel
{
    protected $table = 'fmd20';
    protected $primaryKey = 'fmd2001';
    protected $allowedFields = [
        'fmd2002', 'fmd2003',
        'fmd20z1', 'fmd20z2', 'fmd20z3', 'fmd20z4'
    ];

    public function getByFmd0106(int $fmd0106): ?object
    {
        return $this->db->table($this->table)
            ->where('fmd2002', $fmd0106)
            ->get()
            ->getRow();
    }

    /**
     * Check out (same as CI3 check_out)
     */
    public function checkOut(int $fmd0101): bool
    {
        $fmd01 = $this->db->table('fmd01')->where('fmd0101', $fmd0101)->get()->getRow();
        if (!$fmd01) {
            return false;
        }

        $fmd20 = $this->getByFmd0106($fmd01->fmd0106);
        if ($fmd20) {
            if ($fmd20->fmd2003 == 1) {
                return false;
            }
            $fmd20->fmd2003 = 1;
            $this->save((array)$fmd20);
        } else {
            $this->insert([
                'fmd2002' => $fmd01->fmd0106,
                'fmd2003' => 1
            ]);
        }
        return true;
    }

    /**
     * Commit (same as CI3 commit)
     */
    public function commit(int $fmd0101): bool
    {
        $fmd01 = $this->db->table('fmd01')->where('fmd0101', $fmd0101)->get()->getRow();
        if (!$fmd01) {
            return false;
        }

        $fmd20 = $this->getByFmd0106($fmd01->fmd0106);
        if (!$fmd20) {
            return false;
        }

        if ($fmd20->fmd2003 == 2) {
            return false;
        }

        $fmd21Model = model('Fmd21Model');
        $fmd22Model = model('Fmd22Model');
        $fmd21Model->reorder($fmd01->fmd0106);
        $fmd22Model->reorder($fmd01->fmd0106);

        $fmd22s = $fmd22Model->getByFmd0106($fmd01->fmd0106);
        if (!$fmd22s) {
            $this->message->add(lang('ApproveSetting.not_set_hint'));
            return false;
        }

        $fmd20->fmd2003 = 2;
        $this->save((array)$fmd20);

        return true;
    }
}
