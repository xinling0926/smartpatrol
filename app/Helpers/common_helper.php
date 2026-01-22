<?php

/**
 * Common Helper - CI4 Version
 * 通用輔助函數
 */

if (!function_exists('debug_echo')) {
    /**
     * 除錯輸出
     */
    function debug_echo(mixed $obj, bool $newLine = false): void
    {
        if (ENVIRONMENT !== 'development') {
            return;
        }
        if (is_object($obj) || is_array($obj)) {
            print_r($obj);
        } else {
            echo $obj;
        }
        if ($newLine) {
            echo "<br>";
        }
    }
}

if (!function_exists('debug_log')) {
    /**
     * 除錯日誌
     */
    function debug_log(string $message, mixed $obj): void
    {
        if (ENVIRONMENT !== 'development') {
            return;
        }
        if (is_object($obj) || is_array($obj)) {
            log_message('debug', $message . print_r($obj, true));
        } else {
            log_message('debug', $message . $obj);
        }
    }
}

if (!function_exists('int2date')) {
    /**
     * 時間戳轉日期
     */
    function int2date(?int $timestamp): string
    {
        if ($timestamp) {
            return date('Y-m-d', $timestamp);
        }
        return '';
    }
}

if (!function_exists('int2time')) {
    /**
     * 時間戳轉日期時間
     */
    function int2time(?int $timestamp): string
    {
        if ($timestamp) {
            return date('Y-m-d H:i:s', $timestamp);
        }
        return '';
    }
}

if (!function_exists('now')) {
    /**
     * 取得當前日期時間
     */
    function now(): string
    {
        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('today')) {
    /**
     * 取得今天日期
     */
    function today(): string
    {
        return date('Y-m-d');
    }
}

if (!function_exists('first_day_of_month')) {
    /**
     * 取得當月第一天
     */
    function first_day_of_month(?string $date = null): string
    {
        if ($date) {
            return date('Y-m', strtotime($date)) . '-01';
        }
        return date('Y-m') . '-01';
    }
}

if (!function_exists('first_monday_of_month')) {
    /**
     * 取得當月第一個星期一
     */
    function first_monday_of_month(?string $date = null): string
    {
        $d1 = first_day_of_month($date);
        $week = date('w', strtotime($date ?? ''));
        return match ($week) {
            0 => date('Y-m-d', strtotime("{$d1} +1 day")),
            1 => $d1,
            default => date('Y-m-d', strtotime("{$d1} +" . (8 - $week) . " day")),
        };
    }
}

if (!function_exists('firstMondayOfMonth')) {
    /**
     * 取得當月第一個星期一 (camelCase 別名)
     */
    function firstMondayOfMonth(?string $date = null): string
    {
        return first_monday_of_month($date);
    }
}

if (!function_exists('monday')) {
    /**
     * 取得指定日期所在週的星期一
     */
    function monday(string $date = ''): string
    {
        if (empty($date)) {
            $date = today();
        }
        $week = date('w', strtotime($date));
        return match ($week) {
            0 => date('Y-m-d', strtotime("$date -6 day")),
            1 => date('Y-m-d', strtotime($date)),
            default => date('Y-m-d', strtotime("$date -" . ($week - 1) . " day")),
        };
    }
}

if (!function_exists('yesterday')) {
    /**
     * 取得昨天日期
     */
    function yesterday(string $date = ''): string
    {
        return date('Y-m-d', strtotime("$date -1 day"));
    }
}

if (!function_exists('tomorrow')) {
    /**
     * 取得明天日期
     */
    function tomorrow(string $date = ''): string
    {
        return date('Y-m-d', strtotime("$date +1 day"));
    }
}

if (!function_exists('user_display_name')) {
    /**
     * 取得使用者顯示名稱
     */
    function user_display_name(object $user): string
    {
        $s = "{$user->sys0103}{$user->sys0104}";
        if (preg_match("/^[a-zA-Z]+$/", $s)) {
            return "{$user->sys0103} {$user->sys0104}";
        }
        return "{$user->sys0103}{$user->sys0104}";
    }
}

if (!function_exists('userDisplayName')) {
    /**
     * 取得使用者顯示名稱 (camelCase 別名)
     */
    function userDisplayName(object $user): string
    {
        return user_display_name($user);
    }
}

if (!function_exists('get_option_module')) {
    /**
     * 取得模組選項
     */
    function get_option_module(bool $includeStandard = true): array
    {
        $of = [];
        if ($includeStandard) {
            $of['0'] = '标准版';
        }
        $of['1'] = '签核模组';
        $of['2'] = '报修模组';
        return $of;
    }
}

if (!function_exists('getOptionModule')) {
    /**
     * 取得模組選項 (camelCase 別名)
     */
    function getOptionModule(bool $includeStandard = true): array
    {
        return get_option_module($includeStandard);
    }
}

if (!function_exists('search_role_for_approval')) {
    /**
     * SmartPatrol 系統
     * ISO表單簽核層級檢查，傳入一個職級判斷這個職級在簽核流程裡負責哪些關卡
     */
    function search_role_for_approval(string $sys0119, string $iso0008): array
    {
        $roles = explode('>', $iso0008);
        $result = [];
        foreach ($roles as $k => $role) {
            $r = explode('/', $role);
            if (array_search($sys0119, $r) !== false) {
                if ($k > 0) {
                    $result[] = $k;
                }
            }
        }
        return $result;
    }
}

if (!function_exists('nl2p')) {
    /**
     * 換行轉段落
     */
    function nl2p(string $string, bool $lineBreaks = true, bool $xml = true): string
    {
        $string = str_replace(['<p>', '</p>', '<br>', '<br />'], '', $string);
        if ($lineBreaks) {
            return '<p>' . preg_replace(
                ["/([\n]{2,})/i", "/([^>])\n([^<])/i"],
                ["</p>\n<p>", '$1<br' . ($xml ? ' /' : '') . '>$2'],
                trim($string)
            ) . '</p>';
        }
        return '<p>' . preg_replace(
            ["/([\n]{2,})/i", "/([\r\n]{3,})/i", "/([^>])\n([^<])/i"],
            ["</p>\n<p>", "</p>\n<p>", '$1<br' . ($xml ? ' /' : '') . '>$2'],
            trim($string)
        ) . '</p>';
    }
}

if (!function_exists('nl2p_lite')) {
    /**
     * 換行轉段落(簡易版)
     */
    function nl2p_lite(string $str): string
    {
        $arr = explode("\n", $str);
        $out = '';
        foreach ($arr as $line) {
            if (strlen(trim($line)) > 0) {
                $out .= '<p>' . trim($line) . '</p>';
            }
        }
        return $out;
    }
}

if (!function_exists('pushToDevice')) {
    /**
     * 推送資料到裝置 (FCM)
     *
     * @param array $data 要推送的資料，包含:
     *   - token: FCM 裝置 token (必填)
     *   - title: 通知標題
     *   - body: 通知內容
     *   - data: 自訂資料 (array)
     * @return bool
     */
    function pushToDevice(array $data): bool
    {
        try {
            $messaging = getFcmMessaging();
            if (!$messaging) {
                log_message('warning', 'pushToDevice: FCM not configured');
                return false;
            }

            $token = $data['token'] ?? '';
            if (empty($token)) {
                log_message('warning', 'pushToDevice: No device token provided');
                return false;
            }

            $message = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', $token);

            // 設定通知內容
            if (!empty($data['title']) || !empty($data['body'])) {
                $message = $message->withNotification([
                    'title' => $data['title'] ?? '',
                    'body' => $data['body'] ?? '',
                ]);
            }

            // 設定自訂資料
            if (!empty($data['data']) && is_array($data['data'])) {
                // FCM data 必須是 string => string 格式
                $fcmData = [];
                foreach ($data['data'] as $key => $value) {
                    $fcmData[$key] = is_string($value) ? $value : json_encode($value);
                }
                $message = $message->withData($fcmData);
            }

            // 設定 Android 優先級
            $message = $message->withAndroidConfig([
                'priority' => 'high',
            ]);

            $messaging->send($message);
            log_message('info', 'pushToDevice: Message sent successfully to token: ' . substr($token, 0, 20) . '...');
            return true;

        } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
            log_message('warning', 'pushToDevice: Token not found (device may be unregistered): ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            log_message('error', 'pushToDevice Error: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('getFcmMessaging')) {
    /**
     * 取得 FCM Messaging 實例
     *
     * @return \Kreait\Firebase\Contract\Messaging|null
     */
    function getFcmMessaging(): ?\Kreait\Firebase\Contract\Messaging
    {
        static $messaging = null;

        if ($messaging !== null) {
            return $messaging;
        }

        $serviceAccountFile = WRITEPATH . config('App')->fcmServiceAccountFile;

        if (!file_exists($serviceAccountFile)) {
            log_message('warning', 'FCM service account file not found: ' . $serviceAccountFile);
            return null;
        }

        try {
            $factory = (new \Kreait\Firebase\Factory())
                ->withServiceAccount($serviceAccountFile);

            $messaging = $factory->createMessaging();
            return $messaging;
        } catch (\Exception $e) {
            log_message('error', 'FCM initialization error: ' . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('pushDev03ToDevice')) {
    /**
     * 推送 Dev03 訊息到裝置 (FCM)
     *
     * @param object $dev03 Dev03 物件，需包含 dev0302 (FCM token)
     * @param string $dev0107 裝置 ID（可選，用於查詢 token）
     * @return object 回傳結果物件，包含 status, msgid, time
     */
    function pushDev03ToDevice(object $dev03, string $dev0107 = ''): object
    {
        $session = \Config\Services::session();
        $oRet = new \stdClass();
        $oRet->status = 'error';
        $oRet->msgid = '';
        $oRet->time = time();

        // 取得 FCM token
        $token = $dev03->dev0302 ?? '';

        // 如果沒有 token 但有 dev0107，嘗試從資料庫查詢
        if (empty($token) && !empty($dev0107)) {
            $db = \Config\Database::connect();
            $dev01 = $db->table('dev01')
                ->where('dev0107', $dev0107)
                ->get()
                ->getRow();
            if ($dev01 && !empty($dev01->dev0109)) {
                $token = $dev01->dev0109; // 假設 dev0109 存放 FCM token
            }
        }

        if (empty($token)) {
            log_message('warning', 'pushDev03ToDevice: No FCM token available');
            $oRet->error = 'No FCM token';
            return $oRet;
        }

        // 移除不需要傳送的欄位
        $dev03Copy = clone $dev03;
        unset($dev03Copy->dev0306, $dev03Copy->dev0307, $dev03Copy->dev0308, $dev03Copy->dev0309);

        // 準備推送資料
        $pushData = [
            'token' => $token,
            'title' => $dev03->dev0304 ?? '',
            'body' => $dev03->dev0305 ?? '',
            'data' => [
                'dev03' => json_encode($dev03Copy),
                'ent0102' => $session->get('ent0102') ?? '',
                'type' => 'message',
                'click_action' => 'MESSAGE_ACTIVITY',
            ],
        ];

        // 執行推送
        $result = pushToDevice($pushData);

        if ($result) {
            $oRet->status = 'success';
            $oRet->msgid = 'fcm_' . time() . '_' . random_string('alnum', 8);
            log_message('info', 'pushDev03ToDevice: Message sent successfully');
        } else {
            log_message('warning', 'pushDev03ToDevice: Failed to send message');
        }

        return $oRet;
    }
}

if (!function_exists('addParagraphsNew')) {
    /**
     * 添加段落標籤
     */
    function addParagraphsNew(string $text): string
    {
        $returntext = '';
        $pattern1 = '%^(<(\w++)[^>]*+>.*?</\2>)$%smx';
        $pattern2 = '%(?:\s*\n\s*+|^)(.+?)(?:\s+$)?(?=\s*\n\s*|$)%x';

        $sections = preg_split($pattern1, $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        for ($i = 0; $i < count($sections); $i++) {
            if (preg_match($pattern1, $sections[$i])) {
                $returntext .= "\n" . $sections[$i] . "\n";
                $i++;
            } else {
                $returntext .= preg_replace($pattern2, "\n<p>$1</p>\n", $sections[$i]);
            }
        }
        $returntext = preg_replace('/^\s+/', '', $returntext);
        $returntext = preg_replace('/\s+$/', '', $returntext);
        return $returntext;
    }
}
