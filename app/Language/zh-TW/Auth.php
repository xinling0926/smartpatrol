<?php

/**
 * Auth Language - Traditional Chinese
 */
return [
    // field names
    'f_sys0102' => '帳號',
    'f_sys0103' => '姓氏',
    'f_sys0104' => '名字',
    'f_sys0105' => '密碼',
    'f_sys0107' => '電子郵件',
    'f_sys0108' => '狀態',
    'f_sys0110' => '部門',
    'f_sys0111' => '電話',
    'f_sys0112' => '最近登入時間',
    'f_sys0113' => '最近變更密碼時間',
    'f_sys0117' => '頭像',
    'f_sys0119' => '職級',
    'f_sys0120' => '簽名檔',
    'f_sys0121' => 'AD認證',
    'f_sys0204' => '權限角色',

    'sec_code' => '驗證碼',

    // virtual fields
    'f_users_name' => '姓名',
    'f_users_password_confirm' => '確認密碼',

    // data descriptions
    'v_sys0108_0' => '停用',
    'v_sys0108_1' => '啟用',
    'v_sys0121_0' => '停用',
    'v_sys0121_1' => '啟用',

    // Login
    'login_title' => '使用者登入',
    'login_remember_label' => '記住我',
    'login_submit_btn' => '登入',
    'login_forgot_password' => '忘記密碼?',
    'sec_code_check_error' => '驗證碼輸入不正確!',
    'no_enterprise' => '尚未開通這個帳號的企業權限，請洽詢系統管理員',
    'permission_error' => '權限不足，請洽詢系統管理員',
    'license_error' => '查無此功能模組授權，請聯繫數聯信息服務人員。',

    // Index
    'account_index_heading' => '帳號列表',
    'account_index_add_btn' => '新增帳號',
    'account_index_dept_filter' => '部門篩選',

    // edit
    'edit_sys0105_hint' => '不修改請保持空白',
    'edit_sys0120_btn' => '上傳簽名檔',
    'edit_sys0120_hint' => '支持jpg、png、gif，圖片大小5M以內',

    'upload_avatar_heading' => '上傳頭像',
    'upload_avatar_btn' => '上傳頭像',
    'upload_avatar_hint' => '支持jpg、png、gif，圖片大小2M以內',

    // Change Password
    'change_password_heading' => '修改密碼',
    'change_password_old_password_label' => '原密碼',
    'change_password_new_password_label' => '新密碼',
    'change_password_new_password_confirm_label' => '密碼確認',
    'change_password_submit_btn' => '修改密碼',
    'change_password_success_heading' => '成功!',
    'change_password_fail_heading' => '錯誤!',
    'change_password_success_message' => '密碼修改完成，下次請用新密碼登入',

    // Forgot Password
    'forgot_password_heading' => '忘記密碼',
    'forgot_password_subheading' => '請填寫您的%s，以便讓我們寄送電子郵件重新啟用密碼。',
    'forgot_password_login' => '回登入畫面',
    'forgot_password_submit_btn' => '送出',
    'forgot_password_account_not_found' => '找不到此%s相關資訊.',
    'forgot_password_successful' => '密碼已重設，請收取電子郵件',
    'forgot_password_unsuccessful' => '密碼重設失敗',

    // Reset Password
    'reset_password_heading' => '修改密碼',
    'reset_password_subheading' => '新密碼 (至少含 %s 字元長度)',
    'reset_password_new_password_label' => '新密碼',
    'reset_password_new_password_confirm_label' => '確認新密碼',
    'reset_password_submit_btn' => '修改',
    'reset_password_validation_new_password_label' => '新密碼',
    'reset_password_validation_new_password_confirm_label' => '確認新密碼',

    // Password
    'password_change_successful' => '密碼變更成功',
    'password_change_unsuccessful' => '密碼變更失敗',

    // Forgot Password Email
    'email_forgotten_password_subject' => '密碼重設驗證',
    'email_forgot_password_link' => '重置密碼',
    'email_forgot_password_body_1' => '<p>您可以通過下面的鏈接重置您的密碼:</p>',
    'email_forgot_password_body_2' => '<p>如果您未發起這次重置密碼, 請忽略這封郵件。</p><p>如果您不點擊上面的鏈接, 並且設置新密碼的話, 您的密碼不會被更改。</p>',

    // New Password Email
    'email_new_password_subject' => '新密碼',
    'email_new_password_heading' => '新密碼 %s',
    'email_new_password_subheading' => '您的密碼已重置為: %s',

    // Lock Notify Email
    'email_lock_notify_subject' => '帳號已被鎖定',
    'email_lock_notify_body_1' => "<p>%s 您好:</p><p>系統設定連續登入錯誤三次就會鎖定帳號十分鐘。</p><p>您可以通過下面的鏈接重置您的密碼:</p>",
    'email_lock_notify_body_2' => "<p>如果您不點上面的鏈接, 並且設置新密碼的話, 您的密碼不會被更改。</p>",
];
