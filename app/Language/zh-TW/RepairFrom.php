<?php

/**
 * RepairFrom Language - Traditional Chinese
 */
return [
    'index_heading' => '委修管理',

    'add_repair_from_btn' => '添加',

    'v_pad0513_0' => '尚未處理',
    'v_pad0513_1' => '委修中',
    'v_pad0513_2' => '維修中',
    'v_pad0513_3' => '驗收中',
    'v_pad0513_4' => '結案中',
    'v_pad0513_5' => '已結案',
    'v_pad0513_6' => '退回',

    // query
    'input_placeholder_start_date' => '起始時間',
    'input_placeholder_end_date' => '結束時間',
    'select_state' => '選擇狀態',
    'select_ent1004_default' => '部門篩選',
    'select_sys0104_default' => '人員篩選',

    'export_excel_btn' => '導出Excel',
    'check_accept_btn' => '驗收',

    'goback_repair_btn' => '退回維修',
    'confirm_goback' => '是否確定要退回到維修中的狀態？',

    'f_pad0504' => '設備編號',
    'f_pad0503' => '設備名稱',
    'f_ent1004' => '委修部門',
    'f_sys0104' => '委修人員',
    'f_pad0509' => '送修時間',
    'f_cod0204' => '維修部門',
    'f_pad0511' => '期望完工時間',
    'f_pad0518' => '接單時間',
    'f_pad0512' => '延遲維修因素',
    'f_pad0513' => '狀態',
    'f_pad0604' => '工作描述',
    'f_pad0605' => '時間',
    'f_pad0606' => '附件',

    'label_pad0502' => '單據編號',
    'label_pad0504' => '設備編號',
    'label_pad0503' => '設備名稱',
    'label_ent1004' => '委修部門',
    'label_sys0103' => '委修人員',
    'label_pad0509' => '委修時間',
    'label_cod0204' => '維修部門',
    'label_pad0505' => '委修需求',
    'label_image' => '附件',
    'label_pad0511' => '期望完工時間',
    'label_pad0513' => '狀態',
    'label_pad0518' => '接單時間',
    'label_pad0515' => '接單人員',
    'label_pad0516' => '計劃工時',
    'label_pad0517' => '實際工時',
    'label_pad0512' => '延遲維修因素',
    'label_pad0514' => '未結事項及原因',
    'label_pad0519' => '驗收評估',
    'label_repair_log' => '維修記錄',

    'dialog_assess_title' => '請輸入驗收評估',
    'dialog_hint_title' => '溫馨提示',
    'dialog_image_title' => '附件照片',

    'update_image_span' => '上傳圖片',
    'update_image_hint' => '支持jpg、png、gif，圖片大小1M以內',

    'excel_title' => '委修記錄',

    'mail_format' => '維修工作委託單(MSR) : %s',
    'date_format' => 'Y年m月d日',

    'mail_content' => <<<EOF
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>維修工作委託單(MSR)</title>
</head>
<body>
<p>請至<a href="%s" target="_blank">維修管理</a>接單，進行後續維修工作！</p>
<table width="90%%" border="1">
  <tr>
    <td height="50" colspan="4" align="center"><strong>%s</strong></td>
  </tr>
  <tr>
    <td height="30" colspan="4" align="center"><strong>維修工作委託單(MSR)</strong></td>
  </tr>
  <tr>
    <td width="150">委修部門：</td>
    <td colspan="3">%s</td>
  </tr>
  <tr>
    <td>委 修 人：</td>
    <td>%s%s</td>
    <td width="150">委修日期：</td>
    <td width="200">%s</td>
  </tr>
  <tr>
    <td>設備名稱與編號：</td>
    <td colspan="3">%s</td>
  </tr>
  <tr>
    <td>受託部門：</td>
    <td colspan="3">%s</td>
  </tr>
  <tr>
    <td>委修需求內容 ：</td>
    <td colspan="3">%s</td>
  </tr>
  <tr>
    <td>期望完工時間：</td>
    <td colspan="3">%s</td>
  </tr>
  <tr>
    <td>延遲維修因素</td>
    <td colspan="3">%s</td>
  </tr>
</table>
</body>
</html>
EOF,

    'closed_hint_1' => '記錄未找到',
    'closed_hint_2' => '該記錄還未進入驗收中',
    'closed_hint_3' => '已驗收完畢',
    'sendto_hint' => '該記錄已送修',

    'rule_pad0502' => '單據編號',
    'rule_pad0504' => '設備編號',
    'rule_pad0503' => '設備名稱',
    'rule_pad0507' => '委修部門',
    'rule_pad0508' => '委修人員',
    'rule_pad0510' => '維修部門',
    'rule_pad0505' => '委修需求',
    'rule_pad0511' => '期望完工時間',

    'alert_hint_title' => '溫馨提示',
    'save_success_hint' => '數據已保存',
];
