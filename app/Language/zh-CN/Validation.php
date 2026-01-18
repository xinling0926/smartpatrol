<?php

/**
 * Validation Language - Simplified Chinese
 */
return [
    // Core Messages
    'noRuleSets'      => '验证设定中未指定规则集。',
    'ruleNotFound'    => '"{0}" 不是有效的规则。',
    'groupNotFound'   => '"{0}" 不是有效的验证规则群组。',
    'groupNotArray'   => '"{0}" 规则群组必须是数组。',
    'invalidTemplate' => '"{0}" 不是有效的验证模板。',

    // Rule Messages
    'alpha'                 => '{field} 字段只能包含字母字符。',
    'alpha_dash'            => '{field} 字段只能包含字母、数字、下划线和破折号。',
    'alpha_numeric'         => '{field} 字段只能包含字母和数字。',
    'alpha_numeric_punct'   => '{field} 字段只能包含字母、数字、空格和 ~ ! # $ % & * - _ + = | : . 字符。',
    'alpha_numeric_space'   => '{field} 字段只能包含字母、数字和空格。',
    'alpha_space'           => '{field} 字段只能包含字母和空格。',
    'decimal'               => '{field} 字段必须是十进制数字。',
    'differs'               => '{field} 字段必须与 {param} 字段不同。',
    'equals'                => '{field} 字段必须完全等于: {param}。',
    'exact_length'          => '{field} 字段必须刚好是 {param} 个字符。',
    'greater_than'          => '{field} 字段必须大于 {param}。',
    'greater_than_equal_to' => '{field} 字段必须大于或等于 {param}。',
    'hex'                   => '{field} 字段只能包含十六进制字符。',
    'in_list'               => '{field} 字段必须是以下其中之一: {param}。',
    'integer'               => '{field} 字段必须是整数。',
    'is_natural'            => '{field} 字段只能包含数字。',
    'is_natural_no_zero'    => '{field} 字段只能包含数字且必须大于零。',
    'is_not_unique'         => '{field} 字段必须是数据库中已存在的值。',
    'is_unique'             => '{field} 字段必须是唯一值。',
    'less_than'             => '{field} 字段必须小于 {param}。',
    'less_than_equal_to'    => '{field} 字段必须小于或等于 {param}。',
    'matches'               => '{field} 字段与 {param} 字段不相符。',
    'max_length'            => '{field} 字段不能超过 {param} 个字符。',
    'min_length'            => '{field} 字段必须至少 {param} 个字符。',
    'not_equals'            => '{field} 字段不能是: {param}。',
    'not_in_list'           => '{field} 字段不能是以下其中之一: {param}。',
    'numeric'               => '{field} 字段只能包含数字。',
    'regex_match'           => '{field} 字段格式不正确。',
    'required'              => '{field} 字段为必填。',
    'required_with'         => '当 {param} 字段存在时，{field} 字段为必填。',
    'required_without'      => '当 {param} 字段不存在时，{field} 字段为必填。',
    'string'                => '{field} 字段必须是有效的字符串。',
    'timezone'              => '{field} 字段必须是有效的时区。',
    'valid_base64'          => '{field} 字段必须是有效的 Base64 字符串。',
    'valid_email'           => '{field} 字段必须是有效的电子邮件地址。',
    'valid_emails'          => '{field} 字段必须包含所有有效的电子邮件地址。',
    'valid_ip'              => '{field} 字段必须是有效的 IP 地址。',
    'valid_url'             => '{field} 字段必须是有效的网址。',
    'valid_url_strict'      => '{field} 字段必须是有效的网址。',
    'valid_date'            => '{field} 字段必须是有效的日期。',
    'valid_json'            => '{field} 字段必须是有效的 JSON。',
    'valid_cc_number'       => '{field} 字段必须是有效的信用卡号码。',

    // Field Labels
    'identity'              => '帐号',
    'password'              => '密码',
    'new'                   => '新密码',
    'new_confirm'           => '确认新密码',
    'sec_code'              => '验证码',
];
