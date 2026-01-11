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
     * 推送資料到裝置
     * TODO: 實作裝置推送功能
     *
     * @param array $data 要推送的資料
     * @return bool
     */
    function pushToDevice(array $data): bool
    {
        // 暫時只記錄日誌，等待實作
        log_message('debug', 'pushToDevice called with data: ' . json_encode($data));
        return true;
    }
}

if (!function_exists('pushDev03ToDevice')) {
    /**
     * 推送 Dev03 訊息到裝置
     * TODO: 需要移植 BaiDuPush 庫
     *
     * @param object $dev03 Dev03 物件
     * @param string $dev0107 裝置 ID（可選）
     * @return object
     */
    function pushDev03ToDevice(object $dev03, string $dev0107 = ''): object
    {
        $session = \Config\Services::session();

        // 移除不需要的欄位
        if (isset($dev03->dev0306)) {
            unset($dev03->dev0306);
        }
        if (isset($dev03->dev0307)) {
            unset($dev03->dev0307);
        }
        if (isset($dev03->dev0308)) {
            unset($dev03->dev0308);
        }
        if (isset($dev03->dev0309)) {
            unset($dev03->dev0309);
        }

        $message = [
            'title' => $dev03->dev0304,
            'description' => $dev03->dev0305,
            'custom_content' => ['dev03' => $dev03, 'ent0102' => $session->get('ent0102')],
            'open_type' => 2,
            'pkg_content' => '#Intent;component=cn.com.duit.smartpatrol/.MessageActivity;end'
        ];

        $opts = [
            'msg_type' => 1
        ];

        // TODO: 實作百度推送
        // 目前返回模擬成功結果
        $oRet = new \stdClass();

        // 檢查是否有配置百度推送
        $appKey = config('App')->baiduPushAppKey ?? '';
        $secretKey = config('App')->baiduPushSecretKey ?? '';

        if (empty($appKey) || empty($secretKey)) {
            // 沒有配置推送，返回模擬成功
            $oRet->status = 'success';
            $oRet->msgid = 'mock_' . time();
            $oRet->time = time();
            log_message('debug', 'pushDev03ToDevice: No Baidu Push config, returning mock success. Message: ' . json_encode($message));
        } else {
            // TODO: 使用 BaiDuPush 庫進行實際推送
            // 暫時返回模擬成功
            $oRet->status = 'success';
            $oRet->msgid = 'mock_' . time();
            $oRet->time = time();
            log_message('debug', 'pushDev03ToDevice: Baidu Push not implemented yet. Message: ' . json_encode($message));
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
