<?php

namespace App\Models;

/**
 * Ent02Model - 授權模型
 */
class Ent02Model extends BaseModel
{
    protected $table = 'ent02';
    protected $primaryKey = 'ent0201';
    protected $allowedFields = [
        'ent0202', 'ent0203', 'ent0204', 'ent0205', 'ent0206', 'ent0207',
        'ent02z1', 'ent02z2', 'ent02z3', 'ent02z4'
    ];

    /**
     * Get a single record with decrypted value
     */
    public function get(int|string $id): ?object
    {
        $ent02 = $this->find($id);
        if ($ent02) {
            $decoded = $this->decryptCode($ent02);
            $ent02->value = $decoded->ent0207 ?? '';
        }
        return $ent02;
    }

    /**
     * Get license count by type and enterprise ID
     */
    public function getLicenseCount(string $licenseType, ?int $entId = null): int
    {
        if ($entId === null) {
            $entId = session()->get('ent0101');
        }

        $now = date('Y-m-d');
        $ent02s = $this->db->table($this->table)
            ->where('ent0202', $entId)
            ->where('ent0206', $licenseType)
            ->where("(ent0205 > '{$now}' OR ent0205 IS NULL)")
            ->get()
            ->getResult();

        $count = 0;
        foreach ($ent02s as $ent02) {
            $decoded = $this->decryptCode($ent02);
            if ($decoded && $decoded->ent0101 == $entId && $decoded->ent0206 == $licenseType) {
                if ($decoded->ent0205 && $decoded->ent0205 < $now) {
                    // Already expired
                } else {
                    $count += (int)($decoded->ent0207 ?? 0);
                }
            }
        }

        return $count;
    }

    /**
     * Override insert to generate authorization code
     */
    public function insert($row = null, bool $returnID = true)
    {
        if (is_array($row)) {
            $row = (object)$row;
        }

        $row->ent0207 = $this->encryptCode($row);

        if (isset($row->ent0205) && $row->ent0205 === '') {
            unset($row->ent0205);
        }

        // Remove value field as it's not a database column
        unset($row->value);

        // Add created_by and created_on (same as CI3 MY_Model)
        if ($this->currentUser) {
            $row->ent02z1 = $this->currentUser->sys0101 ?? null;
        }
        $row->ent02z2 = date('Y-m-d H:i:s');

        return parent::insert((array)$row, $returnID);
    }

    /**
     * Override save to generate authorization code on update
     */
    public function save($row): bool
    {
        if (is_array($row)) {
            $row = (object)$row;
        }

        if (!empty($row->{$this->primaryKey})) {
            $row->ent0207 = $this->encryptCode($row);

            if (isset($row->ent0205) && $row->ent0205 === '') {
                $row->ent0205 = null;
            }

            // Remove value field as it's not a database column
            unset($row->value);

            // Add updated_by and updated_on (same as CI3 MY_Model)
            if ($this->currentUser) {
                $row->ent02z3 = $this->currentUser->sys0101 ?? null;
            }
            $row->ent02z4 = date('Y-m-d H:i:s');
        }

        return parent::save((array)$row);
    }

    /**
     * HKDF key derivation (same as CI3's Encryption::hkdf)
     */
    private function hkdf(string $key, string $digest = 'sha512', ?string $salt = null, ?int $length = null, string $info = ''): string
    {
        $digests = [
            'sha224' => 28,
            'sha256' => 32,
            'sha384' => 48,
            'sha512' => 64
        ];

        if (!isset($digests[$digest])) {
            return '';
        }

        if (empty($length) || !is_int($length)) {
            $length = $digests[$digest];
        }

        if (strlen($salt) === 0) {
            $salt = str_repeat("\0", $digests[$digest]);
        }

        $prk = hash_hmac($digest, $key, $salt, true);
        $derivedKey = '';
        $keyBlock = '';
        $blockIndex = 1;

        while (strlen($derivedKey) < $length) {
            $keyBlock = hash_hmac($digest, $keyBlock . $info . chr($blockIndex), $prk, true);
            $derivedKey .= $keyBlock;
            $blockIndex++;
        }

        return substr($derivedKey, 0, $length);
    }

    /**
     * Encrypt authorization code (compatible with CI3 Encryption library)
     * CI3 format: HMAC_hex (128 chars) + base64(IV + ciphertext)
     */
    private function encryptCode(object $data): string
    {
        $ent01Model = model('Ent01Model');
        $ent01 = $ent01Model->find($data->ent0202 ?? 0);

        if (!$ent01) {
            return '';
        }

        $ent0207 = [
            'ent0101' => $ent01->ent0101,
            'ent0102' => $ent01->ent0102,
            'ent0206' => $data->ent0206 ?? '',
            'ent0207' => $data->value ?? '',
            'ent0205' => $data->ent0205 ?? null,
        ];

        $plainText = json_encode($ent0207);
        $originalKey = hash_hmac('md5', "{$ent01->ent0101},{$ent01->ent0102}", $data->ent0206 ?? '');

        // Derive encryption key using HKDF (same as CI3)
        $encryptionKey = $this->hkdf($originalKey, 'sha512', null, strlen($originalKey), 'encryption');

        // Use PHP native openssl encryption (AES-256-CTR)
        $ivLength = openssl_cipher_iv_length('aes-256-ctr');
        $iv = openssl_random_pseudo_bytes($ivLength);
        $encrypted = openssl_encrypt($plainText, 'aes-256-ctr', $encryptionKey, OPENSSL_RAW_DATA, $iv);

        // Base64 encode IV + ciphertext
        $base64Data = base64_encode($iv . $encrypted);

        // Derive HMAC key using HKDF (same as CI3)
        $hmacKey = $this->hkdf($originalKey, 'sha512', null, null, 'authentication');

        // Calculate HMAC (hex format, 128 chars for SHA512)
        $hmac = hash_hmac('sha512', $base64Data, $hmacKey, false);

        // Final format: HMAC_hex + base64(IV + ciphertext)
        return $hmac . $base64Data;
    }

    /**
     * Decrypt authorization code (compatible with CI3 Encryption library)
     * CI3 format: HMAC_hex (128 chars) + base64(IV + ciphertext)
     */
    private function decryptCode(object $data): ?object
    {
        if (empty($data->ent0207)) {
            return null;
        }

        $ent01Model = model('Ent01Model');
        $ent01 = $ent01Model->find($data->ent0202 ?? 0);

        if (!$ent01) {
            return null;
        }

        $originalKey = hash_hmac('md5', "{$ent01->ent0101},{$ent01->ent0102}", $data->ent0206 ?? '');

        try {
            $cipherText = $data->ent0207;

            // Extract HMAC (first 128 hex chars for SHA512)
            $hmacInput = substr($cipherText, 0, 128);
            $base64Data = substr($cipherText, 128);

            // Derive HMAC key using HKDF (same as CI3)
            $hmacKey = $this->hkdf($originalKey, 'sha512', null, null, 'authentication');

            // Verify HMAC
            $hmacCheck = hash_hmac('sha512', $base64Data, $hmacKey, false);

            // Time-safe comparison
            if (!hash_equals($hmacCheck, $hmacInput)) {
                return null;
            }

            // Decode base64 data
            $decoded = base64_decode($base64Data);

            // Extract IV and ciphertext
            $ivLength = openssl_cipher_iv_length('aes-256-ctr');
            $iv = substr($decoded, 0, $ivLength);
            $encrypted = substr($decoded, $ivLength);

            // Derive encryption key using HKDF (same as CI3)
            $encryptionKey = $this->hkdf($originalKey, 'sha512', null, strlen($originalKey), 'encryption');

            // Decrypt
            $decrypted = openssl_decrypt($encrypted, 'aes-256-ctr', $encryptionKey, OPENSSL_RAW_DATA, $iv);

            if ($decrypted === false) {
                return null;
            }

            return json_decode($decrypted);
        } catch (\Exception $e) {
            return null;
        }
    }
}
