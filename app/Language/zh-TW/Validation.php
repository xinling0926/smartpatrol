<?php

/**
 * Validation Language - Traditional Chinese
 */
return [
    // Core Messages
    'noRuleSets'      => '驗證設定中未指定規則集。',
    'ruleNotFound'    => '"{0}" 不是有效的規則。',
    'groupNotFound'   => '"{0}" 不是有效的驗證規則群組。',
    'groupNotArray'   => '"{0}" 規則群組必須是陣列。',
    'invalidTemplate' => '"{0}" 不是有效的驗證範本。',

    // Rule Messages
    'alpha'                 => '{field} 欄位只能包含字母字元。',
    'alpha_dash'            => '{field} 欄位只能包含字母、數字、底線和破折號。',
    'alpha_numeric'         => '{field} 欄位只能包含字母和數字。',
    'alpha_numeric_punct'   => '{field} 欄位只能包含字母、數字、空格和 ~ ! # $ % & * - _ + = | : . 字元。',
    'alpha_numeric_space'   => '{field} 欄位只能包含字母、數字和空格。',
    'alpha_space'           => '{field} 欄位只能包含字母和空格。',
    'decimal'               => '{field} 欄位必須是十進位數字。',
    'differs'               => '{field} 欄位必須與 {param} 欄位不同。',
    'equals'                => '{field} 欄位必須完全等於: {param}。',
    'exact_length'          => '{field} 欄位必須剛好是 {param} 個字元。',
    'greater_than'          => '{field} 欄位必須大於 {param}。',
    'greater_than_equal_to' => '{field} 欄位必須大於或等於 {param}。',
    'hex'                   => '{field} 欄位只能包含十六進位字元。',
    'in_list'               => '{field} 欄位必須是以下其中之一: {param}。',
    'integer'               => '{field} 欄位必須是整數。',
    'is_natural'            => '{field} 欄位只能包含數字。',
    'is_natural_no_zero'    => '{field} 欄位只能包含數字且必須大於零。',
    'is_not_unique'         => '{field} 欄位必須是資料庫中已存在的值。',
    'is_unique'             => '{field} 欄位必須是唯一值。',
    'less_than'             => '{field} 欄位必須小於 {param}。',
    'less_than_equal_to'    => '{field} 欄位必須小於或等於 {param}。',
    'matches'               => '{field} 欄位與 {param} 欄位不相符。',
    'max_length'            => '{field} 欄位不能超過 {param} 個字元。',
    'min_length'            => '{field} 欄位必須至少 {param} 個字元。',
    'not_equals'            => '{field} 欄位不能是: {param}。',
    'not_in_list'           => '{field} 欄位不能是以下其中之一: {param}。',
    'numeric'               => '{field} 欄位只能包含數字。',
    'regex_match'           => '{field} 欄位格式不正確。',
    'required'              => '{field} 欄位為必填。',
    'required_with'         => '當 {param} 欄位存在時，{field} 欄位為必填。',
    'required_without'      => '當 {param} 欄位不存在時，{field} 欄位為必填。',
    'string'                => '{field} 欄位必須是有效的字串。',
    'timezone'              => '{field} 欄位必須是有效的時區。',
    'valid_base64'          => '{field} 欄位必須是有效的 Base64 字串。',
    'valid_email'           => '{field} 欄位必須是有效的電子郵件地址。',
    'valid_emails'          => '{field} 欄位必須包含所有有效的電子郵件地址。',
    'valid_ip'              => '{field} 欄位必須是有效的 IP 位址。',
    'valid_url'             => '{field} 欄位必須是有效的網址。',
    'valid_url_strict'      => '{field} 欄位必須是有效的網址。',
    'valid_date'            => '{field} 欄位必須是有效的日期。',
    'valid_json'            => '{field} 欄位必須是有效的 JSON。',
    'valid_cc_number'       => '{field} 欄位必須是有效的信用卡號碼。',

    // Field Labels
    'identity'              => '帳號',
    'password'              => '密碼',
    'new'                   => '新密碼',
    'new_confirm'           => '確認新密碼',
    'sec_code'              => '驗證碼',
];
