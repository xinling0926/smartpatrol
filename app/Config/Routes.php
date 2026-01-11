<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default Route
$routes->get('/', 'Home::index');

// Authentication Routes
$routes->group('auth', static function ($routes) {
    $routes->get('/', 'Auth::index');
    $routes->get('login', 'Auth::index');
    $routes->post('login', 'Auth::login');
    $routes->get('logout', 'Auth::logout');
    $routes->get('secode', 'Auth::secode');
    $routes->get('forgot-password', 'Auth::forgotPassword');
    $routes->post('forgot-password', 'Auth::forgotPassword');
    $routes->get('reset-password/(:any)', 'Auth::resetPassword/$1');
    $routes->post('reset-password/(:any)', 'Auth::resetPassword/$1');
});

// Account Routes
$routes->group('account', static function ($routes) {
    $routes->get('/', 'Account::index');
    $routes->get('query/(:num)', 'Account::query/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'Account::edit/$1');
    $routes->match(['get', 'post'], 'delete', 'Account::delete');
    $routes->match(['get', 'post'], 'delete/', 'Account::delete');
    $routes->get('change-password', 'Account::changePassword');
    $routes->post('change-password', 'Account::changePassword');
});

// Department Routes
$routes->group('department', static function ($routes) {
    $routes->get('/', 'Department::index');
    $routes->get('query/(:num)', 'Department::query/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'Department::edit/$1');
    $routes->match(['get', 'post'], 'delete', 'Department::delete');
    $routes->match(['get', 'post'], 'delete/', 'Department::delete');
});

// Device Routes
$routes->group('device', static function ($routes) {
    $routes->get('/', 'Device::index');
    $routes->get('query/(:num)', 'Device::query/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'Device::edit/$1');
    $routes->match(['get', 'post'], 'delete', 'Device::delete');
    $routes->match(['get', 'post'], 'delete/', 'Device::delete');
});

// Role Routes
$routes->group('role', static function ($routes) {
    $routes->get('/', 'Role::index');
    $routes->get('query/(:num)', 'Role::query/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'Role::edit/$1');
    $routes->match(['get', 'post'], 'delete', 'Role::delete');
    $routes->match(['get', 'post'], 'delete/', 'Role::delete');
});

// Form Routes
$routes->group('form', static function ($routes) {
    $routes->get('/', 'Form::index');
    $routes->get('query/(:num)', 'Form::query/$1');
    $routes->match(['get', 'post'], 'query', 'Form::query');
    $routes->get('detail/(:num)', 'Form::detail/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'Form::edit/$1');
    $routes->match(['get', 'post'], 'edit_fmd02/(:num)', 'Form::editFmd02/$1');
    $routes->match(['get', 'post'], 'edit_fmd03/(:num)', 'Form::editFmd03/$1');
    $routes->match(['get', 'post'], 'edit_fmd05/(:num)', 'Form::editFmd05/$1');
    $routes->match(['get', 'post'], 'delete', 'Form::delete');
    $routes->match(['get', 'post'], 'delete/', 'Form::delete');
    $routes->match(['get', 'post'], 'delete_fmd02', 'Form::deleteFmd02');
    $routes->match(['get', 'post'], 'delete_fmd02/', 'Form::deleteFmd02');
    $routes->match(['get', 'post'], 'delete_fmd03', 'Form::deleteFmd03');
    $routes->match(['get', 'post'], 'delete_fmd03/', 'Form::deleteFmd03');
    $routes->match(['get', 'post'], 'delete_fmd05', 'Form::deleteFmd05');
    $routes->match(['get', 'post'], 'delete_fmd05/', 'Form::deleteFmd05');
    $routes->match(['get', 'post'], 'state', 'Form::state');
    $routes->match(['get', 'post'], 'state/', 'Form::state');
});

// FormItem Routes
$routes->group('form-item', static function ($routes) {
    $routes->get('/', 'FormItem::index');
    $routes->match(['get', 'post'], 'query', 'FormItem::query');
    $routes->match(['get', 'post'], 'query/(:num)', 'FormItem::query/$1');
    $routes->get('detail/(:num)', 'FormItem::detail/$1');
    $routes->match(['get', 'post'], 'edit-fmd01/(:num)', 'FormItem::editFmd01/$1');
    $routes->match(['get', 'post'], 'edit-fmd02/(:num)', 'FormItem::editFmd02/$1');
    $routes->match(['get', 'post'], 'edit-fmd04/(:num)', 'FormItem::editFmd04/$1');
    $routes->match(['get', 'post'], 'add-fmd04-sub/(:num)', 'FormItem::addFmd04Sub/$1');
    $routes->match(['get', 'post'], 'add-fmd04-first/(:num)', 'FormItem::addFmd04First/$1');
    $routes->match(['get', 'post'], 'edit-fmd06/(:num)', 'FormItem::editFmd06/$1');
    $routes->match(['get', 'post'], 'edit-fmd07/(:num)', 'FormItem::editFmd07/$1');
    $routes->match(['get', 'post'], 'edit-fmd08/(:num)', 'FormItem::editFmd08/$1');
    $routes->match(['get', 'post'], 'edit-fmd09/(:num)', 'FormItem::editFmd09/$1');
    $routes->match(['get', 'post'], 'delete-fmd02', 'FormItem::deleteFmd02');
    $routes->match(['get', 'post'], 'delete-fmd04', 'FormItem::deleteFmd04');
    $routes->match(['get', 'post'], 'delete-fmd06', 'FormItem::deleteFmd06');
    $routes->match(['get', 'post'], 'delete-fmd07', 'FormItem::deleteFmd07');
    $routes->match(['get', 'post'], 'delete-fmd08', 'FormItem::deleteFmd08');
    $routes->match(['get', 'post'], 'delete-fmd09', 'FormItem::deleteFmd09');
    $routes->match(['get', 'post'], 'revert', 'FormItem::revert');
    $routes->match(['get', 'post'], 'commit', 'FormItem::commit');
    $routes->match(['get', 'post'], 'check-out', 'FormItem::checkOut');
    $routes->get('form-history/(:num)', 'FormItem::formHistory/$1');
});

// Enterprise Routes
$routes->group('enterprise', static function ($routes) {
    $routes->get('/', 'Enterprise::index');
    $routes->match(['get', 'post'], 'query', 'Enterprise::query');
    $routes->match(['get', 'post'], 'query/(:num)', 'Enterprise::query/$1');
    $routes->get('detail/(:num)', 'Enterprise::detail/$1');
    $routes->get('detail_ent02/(:num)', 'Enterprise::detailEnt02/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'Enterprise::edit/$1');
    $routes->match(['get', 'post'], 'edit', 'Enterprise::edit');
    $routes->match(['get', 'post'], 'edit_ent02/(:num)', 'Enterprise::editEnt02/$1');
    $routes->match(['get', 'post'], 'edit_ent02', 'Enterprise::editEnt02');
    $routes->match(['get', 'post'], 'delete', 'Enterprise::delete');
    $routes->match(['get', 'post'], 'delete/', 'Enterprise::delete');
    $routes->match(['get', 'post'], 'delete_ent02', 'Enterprise::deleteEnt02');
    $routes->match(['get', 'post'], 'delete_ent02/', 'Enterprise::deleteEnt02');
    $routes->match(['get', 'post'], 'upload_logo', 'Enterprise::uploadLogo');
    $routes->match(['get', 'post'], 'upload_logo/', 'Enterprise::uploadLogo');
});

// System Setting Routes
$routes->group('system-setting', static function ($routes) {
    $routes->get('/', 'SystemSetting::index');
    $routes->post('save', 'SystemSetting::save');
});

// System Setting Routes (without hyphen)
$routes->group('systemsetting', static function ($routes) {
    $routes->get('/', 'SystemSetting::index');
    $routes->match(['get', 'post'], 'query/(:num)', 'SystemSetting::query/$1');
    $routes->get('detail/(:num)', 'SystemSetting::detail/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'SystemSetting::edit/$1');
    $routes->match(['get', 'post'], 'edit', 'SystemSetting::edit');
    $routes->match(['get', 'post'], 'delete', 'SystemSetting::delete');
    $routes->match(['get', 'post'], 'delete/', 'SystemSetting::delete');
    $routes->post('save', 'SystemSetting::save');
});

// Repair Routes
$routes->group('repair', static function ($routes) {
    $routes->get('/', 'Repair::index');
    $routes->get('query/(:num)', 'Repair::query/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'Repair::edit/$1');
    $routes->match(['get', 'post'], 'export', 'Repair::export');
});

// Repair From Routes
$routes->group('repair-from', static function ($routes) {
    $routes->get('/', 'RepairFrom::index');
    $routes->get('query/(:num)', 'RepairFrom::query/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'RepairFrom::edit/$1');
    $routes->match(['get', 'post'], 'edit', 'RepairFrom::edit');
    $routes->get('sendto/(:num)', 'RepairFrom::sendto/$1');
    $routes->match(['get', 'post'], 'sendto_save/(:num)', 'RepairFrom::sendtoSave/$1');
    $routes->match(['get', 'post'], 'sendto_save', 'RepairFrom::sendtoSave');
    $routes->match(['get', 'post'], 'goback/(:num)', 'RepairFrom::goback/$1');
    $routes->post('upload', 'RepairFrom::upload');
    $routes->get('detail/(:num)', 'RepairFrom::detail/$1');
    $routes->match(['get', 'post'], 'closed/(:num)', 'RepairFrom::closed/$1');
    $routes->get('select_sys01/(:num)', 'RepairFrom::selectSys01/$1');
    $routes->match(['get', 'post'], 'delete', 'RepairFrom::delete');
    $routes->match(['get', 'post'], 'delete/', 'RepairFrom::delete');
    $routes->match(['get', 'post'], 'export', 'RepairFrom::export');
});

// Repair To Routes
$routes->group('repair-to', static function ($routes) {
    $routes->get('/', 'RepairTo::index');
    $routes->get('query/(:num)', 'RepairTo::query/$1');
    $routes->get('detail/(:num)', 'RepairTo::detail/$1');
    $routes->get('jiedan/(:num)', 'RepairTo::jiedan/$1');
    $routes->match(['get', 'post'], 'save_jiedan/(:num)', 'RepairTo::saveJiedan/$1');
    $routes->post('upload', 'RepairTo::upload');
    $routes->get('addpad06/(:num)', 'RepairTo::addpad06/$1');
    $routes->match(['get', 'post'], 'save_addpad06/(:num)', 'RepairTo::saveAddpad06/$1');
    $routes->get('jiean/(:num)', 'RepairTo::jiean/$1');
    $routes->match(['get', 'post'], 'save_jiean/(:num)', 'RepairTo::saveJiean/$1');
    $routes->match(['get', 'post'], 'goback/(:num)', 'RepairTo::goback/$1');
    $routes->match(['get', 'post'], 'closed/(:num)', 'RepairTo::closed/$1');
    $routes->get('select_sys01/(:num)', 'RepairTo::selectSys01/$1');
    $routes->match(['get', 'post'], 'export', 'RepairTo::export');
});

// Query Report Routes
$routes->group('query-report', static function ($routes) {
    $routes->get('/', 'QueryReport::index');
    $routes->get('index/(:segment)', 'QueryReport::index/$1');
    $routes->match(['get', 'post'], 'query', 'QueryReport::query');
    $routes->match(['get', 'post'], 'query/(:num)', 'QueryReport::query/$1');
    $routes->get('select_report/(:segment)', 'QueryReport::selectReport/$1');
    $routes->get('detail/(:segment)', 'QueryReport::detail/$1');
    $routes->post('get_fmd01', 'QueryReport::getFmd01');
    $routes->post('send', 'QueryReport::send');
    $routes->get('(:segment)', 'QueryReport::index/$1');
});

// Query Report Item Routes
$routes->group('query-report-item', static function ($routes) {
    $routes->get('/', 'QueryReportItem::index');
    $routes->match(['get', 'post'], 'query', 'QueryReportItem::query');
    $routes->match(['get', 'post'], 'query/(:num)', 'QueryReportItem::query/$1');
    $routes->get('select_report/(:segment)', 'QueryReportItem::selectReport/$1');
    $routes->get('download_excel', 'QueryReportItem::downloadExcel');
    $routes->get('export', 'QueryReportItem::downloadExcel');
});

// Generate Report Routes
$routes->group('generate-report', static function ($routes) {
    $routes->get('/', 'GenerateReport::index');
    $routes->match(['get', 'post'], 'query/(:num)', 'GenerateReport::query/$1');
    $routes->match(['get', 'post'], 'generate', 'GenerateReport::generate');
    $routes->match(['get', 'post'], 'generate-report', 'GenerateReport::generateReport');
    $routes->match(['get', 'post'], 'select-report/(:num)', 'GenerateReport::selectReport/$1');
    $routes->match(['get', 'post'], 'send-report', 'GenerateReport::sendReport');
    $routes->match(['get', 'post'], 'detail/(:any)', 'GenerateReport::detail/$1');
    $routes->get('(:segment)', 'GenerateReport::index/$1');
});

// Rawdata Routes
$routes->group('rawdata', static function ($routes) {
    $routes->get('/', 'Rawdata::index');
    $routes->match(['get', 'post'], 'query/(:num)', 'Rawdata::query/$1');
    $routes->get('detail/(:num)', 'Rawdata::detail/$1');
    $routes->get('detail_dialog/(:num)', 'Rawdata::detailDialog/$1');
    $routes->get('detail_dialog/(:num)/(:num)', 'Rawdata::detailDialog/$1/$2');
    $routes->get('detail_dialog_miss/(:segment)', 'Rawdata::detailDialogMiss/$1');
    $routes->match(['get', 'post'], 'detail_add_comment', 'Rawdata::detailAddComment');
    $routes->match(['get', 'post'], 'detail_add_comment/(:segment)', 'Rawdata::detailAddComment/$1');
    $routes->match(['get', 'post'], 'detail_auto_comment_edit', 'Rawdata::detailAutoCommentEdit');
    $routes->match(['get', 'post'], 'detail_auto_comment_edit/(:segment)', 'Rawdata::detailAutoCommentEdit/$1');
    $routes->get('linkage_by_ent1001/(:num)', 'Rawdata::linkageByEnt1001/$1');
    $routes->post('linkage_pad0104', 'Rawdata::linkagePad0104');
});

// Missing Routes
$routes->group('missing', static function ($routes) {
    $routes->get('/', 'Missing::index');
    $routes->get('select-report/(:segment)', 'Missing::selectReport/$1');
    $routes->get('select_report/(:segment)', 'Missing::selectReport/$1');
    $routes->match(['get', 'post'], 'query', 'Missing::query');
    $routes->match(['get', 'post'], 'query/(:num)', 'Missing::query/$1');
});

// Approve Routes
$routes->group('approve', static function ($routes) {
    $routes->get('/', 'Approve::index');
    $routes->get('approve-form/(:num)/(:num)', 'Approve::approveForm/$1/$2');
    $routes->post('do-approve', 'Approve::doApprove');
});

// Approve Setting Routes
$routes->group('approve-setting', static function ($routes) {
    $routes->get('/', 'ApproveSetting::index');
    $routes->get('query/(:num)', 'ApproveSetting::query/$1');
    $routes->get('detail/(:num)', 'ApproveSetting::detail/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'ApproveSetting::edit/$1');
    $routes->match(['get', 'post'], 'edit-fmd21/(:num)', 'ApproveSetting::editFmd21/$1');
    $routes->match(['get', 'post'], 'edit-fmd22/(:num)', 'ApproveSetting::editFmd22/$1');
    $routes->match(['get', 'post'], 'delete-fmd21', 'ApproveSetting::deleteFmd21');
    $routes->match(['get', 'post'], 'delete-fmd22', 'ApproveSetting::deleteFmd22');
    $routes->match(['get', 'post'], 'check-out', 'ApproveSetting::checkOut');
    $routes->match(['get', 'post'], 'commit', 'ApproveSetting::commit');
});

// Annual Checkup Routes
$routes->group('annual-checkup', static function ($routes) {
    $routes->get('/', 'AnnualCheckup::index');
    $routes->match(['get', 'post'], 'query', 'AnnualCheckup::query');
    $routes->match(['get', 'post'], 'query/(:num)', 'AnnualCheckup::query/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'AnnualCheckup::edit/$1');
    $routes->match(['get', 'post'], 'delete', 'AnnualCheckup::delete');
    $routes->match(['get', 'post'], 'delete/', 'AnnualCheckup::delete');
    $routes->match(['get', 'post'], 'revert', 'AnnualCheckup::revert');
    $routes->match(['get', 'post'], 'revert/', 'AnnualCheckup::revert');
    $routes->match(['get', 'post'], 'commit', 'AnnualCheckup::commit');
    $routes->match(['get', 'post'], 'commit/', 'AnnualCheckup::commit');
    $routes->match(['get', 'post'], 'check-out', 'AnnualCheckup::checkOut');
    $routes->match(['get', 'post'], 'check-out/', 'AnnualCheckup::checkOut');
    $routes->get('detail/(:num)', 'AnnualCheckup::detail/$1');
});

// annualcheckup alias (without separator)
$routes->group('annualcheckup', static function ($routes) {
    $routes->get('/', 'AnnualCheckup::index');
    $routes->match(['get', 'post'], 'query', 'AnnualCheckup::query');
    $routes->match(['get', 'post'], 'query/(:num)', 'AnnualCheckup::query/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'AnnualCheckup::edit/$1');
    $routes->match(['get', 'post'], 'delete', 'AnnualCheckup::delete');
    $routes->match(['get', 'post'], 'delete/', 'AnnualCheckup::delete');
    $routes->match(['get', 'post'], 'revert', 'AnnualCheckup::revert');
    $routes->match(['get', 'post'], 'revert/', 'AnnualCheckup::revert');
    $routes->match(['get', 'post'], 'commit', 'AnnualCheckup::commit');
    $routes->match(['get', 'post'], 'commit/', 'AnnualCheckup::commit');
    $routes->match(['get', 'post'], 'check-out', 'AnnualCheckup::checkOut');
    $routes->match(['get', 'post'], 'check-out/', 'AnnualCheckup::checkOut');
    $routes->get('detail/(:num)', 'AnnualCheckup::detail/$1');
});

// Device Message Routes
$routes->group('device-message', static function ($routes) {
    $routes->get('/', 'DeviceMessage::index');
    $routes->match(['get', 'post'], 'query', 'DeviceMessage::query');
    $routes->get('query/(:num)', 'DeviceMessage::query/$1');
    $routes->get('detail/(:num)', 'DeviceMessage::detail/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'DeviceMessage::edit/$1');
    $routes->match(['get', 'post'], 'delete', 'DeviceMessage::delete');
    $routes->match(['get', 'post'], 'delete/', 'DeviceMessage::delete');
    $routes->match(['get', 'post'], 'push', 'DeviceMessage::push');
    $routes->match(['get', 'post'], 'push/', 'DeviceMessage::push');
});

// Device Log Routes
$routes->group('device-log', static function ($routes) {
    $routes->get('/', 'DeviceLog::index');
    $routes->match(['get', 'post'], 'query', 'DeviceLog::query');
    $routes->match(['get', 'post'], 'query/(:num)', 'DeviceLog::query/$1');
});

// Operation Log Routes
$routes->group('operation-log', static function ($routes) {
    $routes->get('/', 'OperationLog::index');
    $routes->match(['get', 'post'], 'query/(:num)', 'OperationLog::query/$1');
});

// Operation Log Routes (underscore alias for CI3 compatibility)
$routes->group('operation_log', static function ($routes) {
    $routes->get('/', 'OperationLog::index');
    $routes->match(['get', 'post'], 'query/(:num)', 'OperationLog::query/$1');
});

// Notice Routes
$routes->group('notice', static function ($routes) {
    $routes->get('/', 'Notice::index');
    $routes->get('query/(:num)', 'Notice::query/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'Notice::edit/$1');
    $routes->match(['get', 'post'], 'delete', 'Notice::delete');
    $routes->match(['get', 'post'], 'delete/', 'Notice::delete');
});

// Tag Routes
$routes->group('tag', static function ($routes) {
    $routes->get('/', 'Tag::index');
    $routes->get('query/(:num)', 'Tag::query/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'Tag::edit/$1');
    $routes->match(['get', 'post'], 'delete', 'Tag::delete');
    $routes->match(['get', 'post'], 'delete/', 'Tag::delete');
});

// Jobtitle Routes
$routes->group('jobtitle', static function ($routes) {
    $routes->get('/', 'Jobtitle::index');
    $routes->get('query/(:num)', 'Jobtitle::query/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'Jobtitle::edit/$1');
    $routes->match(['get', 'post'], 'delete', 'Jobtitle::delete');
    $routes->match(['get', 'post'], 'delete/', 'Jobtitle::delete');
});

// Photograph Routes
$routes->group('photograph', static function ($routes) {
    $routes->get('/', 'Photograph::index');
    $routes->match(['get', 'post'], 'query', 'Photograph::query');
    $routes->match(['get', 'post'], 'query/(:num)', 'Photograph::query/$1');
    $routes->get('detail/(:num)', 'Photograph::detail/$1');
    $routes->get('getPhotograph/(:num)', 'Photograph::getPhotograph/$1');
    $routes->get('getPhotograph/(:num)/(:num)', 'Photograph::getPhotograph/$1/$2');
    $routes->get('linkageByEnt1001/(:num)', 'Photograph::linkageByEnt1001/$1');
    $routes->get('linkagebyent1001/(:num)', 'Photograph::linkageByEnt1001/$1');
    $routes->match(['get', 'post'], 'delete', 'Photograph::delete');
    $routes->match(['get', 'post'], 'delete/', 'Photograph::delete');
});

// Web API Routes
$routes->group('webapi', static function ($routes) {
    $routes->get('/', 'Webapi::index');
    $routes->match(['get', 'post'], 'index', 'Webapi::index');
    $routes->match(['get', 'post'], 'register', 'Webapi::register');
    $routes->match(['get', 'post'], 'login', 'Webapi::login');
    $routes->match(['get', 'post'], 'logout', 'Webapi::logout');
    $routes->match(['get', 'post'], 'changepass', 'Webapi::changepass');
    $routes->match(['get', 'post'], 'changeface', 'Webapi::changeface');
    $routes->match(['get', 'post'], 'addpad01', 'Webapi::addpad01');
    $routes->match(['get', 'post'], 'addpad01multi', 'Webapi::addpad01multi');
    $routes->match(['get', 'post'], 'addrepair', 'Webapi::addrepair');
    $routes->match(['get', 'post'], 'addrepairmulti', 'Webapi::addrepairmulti');
    $routes->match(['get', 'post'], 'now', 'Webapi::now');
    $routes->match(['get', 'post'], 'updatabase', 'Webapi::updatabase');
    $routes->match(['get', 'post'], 'version', 'Webapi::version');
    $routes->match(['get', 'post'], 'checkVersion', 'Webapi::checkVersion');
    $routes->match(['get', 'post'], 'getUserByPatrol', 'Webapi::getUserByPatrol');
    $routes->match(['get', 'post'], 'getAll', 'Webapi::getAll');
    $routes->match(['get', 'post'], 'getException', 'Webapi::getException');
    $routes->match(['get', 'post'], 'updateent01', 'Webapi::updateent01');
    $routes->match(['get', 'post'], 'uploaddev02', 'Webapi::uploaddev02');
    $routes->match(['get', 'post'], 'regtags', 'Webapi::regtags');
    $routes->match(['get', 'post'], 'photograph', 'Webapi::photograph');
    // Legacy routes
    $routes->post('upload', 'Webapi::addpad01');
    $routes->post('sync', 'Webapi::addpad01multi');
});

// Emma Link Routes
$routes->get('emma-link/send-notify/(:num)', 'EmmaLink::sendNotify/$1');
$routes->get('emma-link/repair/(:num)', 'EmmaLink::repair/$1');

// HPW Report Routes
$routes->group('hpw', ['namespace' => 'App\Controllers\Hpw'], static function ($routes) {
    // Report 1 - 市場部開門率統計表
    $routes->get('report1', 'HpwReport1::index');
    $routes->match(['get', 'post'], 'report1/report', 'HpwReport1::report');

    // Report 2 - 加工廠抽查數量統計表
    $routes->get('report2', 'HpwReport2::index');
    $routes->match(['get', 'post'], 'report2/report', 'HpwReport2::report');
    $routes->get('report2/detail/(:any)', 'HpwReport2::detail/$1');
    $routes->get('report2/export-excel', 'HpwReport2::exportExcel');

    // Report 4 - 市場部開門率統計表 (新版)
    $routes->get('report4', 'HpwReport4::index');
    $routes->match(['get', 'post'], 'report4/report', 'HpwReport4::report');
});

// ============================================================
// CI3 Compatibility - Underscore Alias Routes
// ============================================================

// form_item alias
$routes->group('form_item', static function ($routes) {
    $routes->get('/', 'FormItem::index');
    $routes->get('query/(:num)', 'FormItem::query/$1');
    $routes->get('detail/(:num)', 'FormItem::detail/$1');
    $routes->match(['get', 'post'], 'edit-fmd01/(:num)', 'FormItem::editFmd01/$1');
    $routes->match(['get', 'post'], 'edit-fmd02/(:num)', 'FormItem::editFmd02/$1');
    $routes->match(['get', 'post'], 'delete-fmd02', 'FormItem::deleteFmd02');
    $routes->match(['get', 'post'], 'delete-fmd02/', 'FormItem::deleteFmd02');
    $routes->match(['get', 'post'], 'revert', 'FormItem::revert');
    $routes->match(['get', 'post'], 'revert/', 'FormItem::revert');
    $routes->match(['get', 'post'], 'commit', 'FormItem::commit');
    $routes->match(['get', 'post'], 'commit/', 'FormItem::commit');
    $routes->match(['get', 'post'], 'check-out', 'FormItem::checkOut');
    $routes->match(['get', 'post'], 'check-out/', 'FormItem::checkOut');
    $routes->get('form-history/(:num)', 'FormItem::formHistory/$1');
});

// formitem alias (no separator - for JavaScript URL building)
$routes->group('formitem', static function ($routes) {
    $routes->get('/', 'FormItem::index');
    $routes->match(['get', 'post'], 'query', 'FormItem::query');
    $routes->match(['get', 'post'], 'query/(:num)', 'FormItem::query/$1');
    $routes->get('detail/(:num)', 'FormItem::detail/$1');
    $routes->match(['get', 'post'], 'edit_fmd01/(:num)', 'FormItem::editFmd01/$1');
    $routes->match(['get', 'post'], 'edit_fmd02/(:num)', 'FormItem::editFmd02/$1');
    $routes->match(['get', 'post'], 'edit_fmd04/(:num)', 'FormItem::editFmd04/$1');
    $routes->match(['get', 'post'], 'add_fmd04_sub/(:num)', 'FormItem::addFmd04Sub/$1');
    $routes->match(['get', 'post'], 'add_fmd04first/(:num)', 'FormItem::addFmd04First/$1');
    $routes->match(['get', 'post'], 'edit_fmd06/(:num)', 'FormItem::editFmd06/$1');
    $routes->match(['get', 'post'], 'edit_fmd07/(:num)', 'FormItem::editFmd07/$1');
    $routes->match(['get', 'post'], 'edit_fmd08/(:num)', 'FormItem::editFmd08/$1');
    $routes->match(['get', 'post'], 'edit_fmd09/(:num)', 'FormItem::editFmd09/$1');
    $routes->match(['get', 'post'], 'add_fmd07/(:segment)', 'FormItem::addFmd07/$1');
    $routes->match(['get', 'post'], 'add_fmd09/(:num)', 'FormItem::addFmd09/$1');
    $routes->match(['get', 'post'], 'edit_form/(:num)', 'FormItem::editForm/$1');
    $routes->match(['get', 'post'], 'add_fmd0906/(:num)', 'FormItem::addFmd0906/$1');
    $routes->match(['get', 'post'], 'delete_fmd02', 'FormItem::deleteFmd02');
    $routes->match(['get', 'post'], 'delete_fmd02/', 'FormItem::deleteFmd02');
    $routes->match(['get', 'post'], 'delete_fmd04', 'FormItem::deleteFmd04');
    $routes->match(['get', 'post'], 'delete_fmd04/', 'FormItem::deleteFmd04');
    $routes->match(['get', 'post'], 'delete_fmd06', 'FormItem::deleteFmd06');
    $routes->match(['get', 'post'], 'delete_fmd06/', 'FormItem::deleteFmd06');
    $routes->match(['get', 'post'], 'delete_fmd07', 'FormItem::deleteFmd07');
    $routes->match(['get', 'post'], 'delete_fmd07/', 'FormItem::deleteFmd07');
    $routes->match(['get', 'post'], 'delete_fmd08', 'FormItem::deleteFmd08');
    $routes->match(['get', 'post'], 'delete_fmd08/', 'FormItem::deleteFmd08');
    $routes->match(['get', 'post'], 'delete_fmd09', 'FormItem::deleteFmd09');
    $routes->match(['get', 'post'], 'delete_fmd09/', 'FormItem::deleteFmd09');
    $routes->match(['get', 'post'], 'delete_fmd0906', 'FormItem::deleteFmd0906');
    $routes->match(['get', 'post'], 'delete_fmd0906/', 'FormItem::deleteFmd0906');
    $routes->match(['get', 'post'], 'copy_fmd06', 'FormItem::copyFmd06');
    $routes->match(['get', 'post'], 'copy_fmd06/', 'FormItem::copyFmd06');
    $routes->match(['get', 'post'], 'order_fmd04', 'FormItem::orderFmd04');
    $routes->match(['get', 'post'], 'order_fmd04/', 'FormItem::orderFmd04');
    $routes->match(['get', 'post'], 'revert', 'FormItem::revert');
    $routes->match(['get', 'post'], 'revert/', 'FormItem::revert');
    $routes->match(['get', 'post'], 'commit', 'FormItem::commit');
    $routes->match(['get', 'post'], 'commit/', 'FormItem::commit');
    $routes->match(['get', 'post'], 'check_out', 'FormItem::checkOut');
    $routes->match(['get', 'post'], 'check_out/', 'FormItem::checkOut');
    $routes->get('form_history/(:num)', 'FormItem::formHistory/$1');
});

// system_setting alias
$routes->group('system_setting', static function ($routes) {
    $routes->get('/', 'SystemSetting::index');
    $routes->match(['get', 'post'], 'query/(:num)', 'SystemSetting::query/$1');
    $routes->get('detail/(:num)', 'SystemSetting::detail/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'SystemSetting::edit/$1');
    $routes->match(['get', 'post'], 'edit', 'SystemSetting::edit');
    $routes->post('save', 'SystemSetting::save');
    $routes->get('android_man', 'SystemSetting::androidMan');
    $routes->match(['get', 'post'], 'android_man_edit', 'SystemSetting::androidManEdit');
    $routes->post('android_man_upload_apk', 'SystemSetting::androidManUploadApk');
});

// systemsetting alias (no separator)
$routes->group('systemsetting', static function ($routes) {
    $routes->get('/', 'SystemSetting::index');
    $routes->match(['get', 'post'], 'query/(:num)', 'SystemSetting::query/$1');
    $routes->get('detail/(:num)', 'SystemSetting::detail/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'SystemSetting::edit/$1');
    $routes->match(['get', 'post'], 'edit', 'SystemSetting::edit');
    $routes->get('androidman', 'SystemSetting::androidMan');
    $routes->match(['get', 'post'], 'android_man_edit/(:num)', 'SystemSetting::androidManEdit');
    $routes->match(['get', 'post'], 'android_man_edit', 'SystemSetting::androidManEdit');
    $routes->post('android_man_upload_apk', 'SystemSetting::androidManUploadApk');
});

// repair_from alias
$routes->group('repair_from', static function ($routes) {
    $routes->get('/', 'RepairFrom::index');
    $routes->get('query/(:num)', 'RepairFrom::query/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'RepairFrom::edit/$1');
    $routes->match(['get', 'post'], 'edit', 'RepairFrom::edit');
    $routes->get('sendto/(:num)', 'RepairFrom::sendto/$1');
    $routes->match(['get', 'post'], 'sendto_save/(:num)', 'RepairFrom::sendtoSave/$1');
    $routes->match(['get', 'post'], 'sendto_save', 'RepairFrom::sendtoSave');
    $routes->match(['get', 'post'], 'goback/(:num)', 'RepairFrom::goback/$1');
    $routes->post('upload', 'RepairFrom::upload');
    $routes->get('detail/(:num)', 'RepairFrom::detail/$1');
    $routes->match(['get', 'post'], 'closed/(:num)', 'RepairFrom::closed/$1');
    $routes->get('select_sys01/(:num)', 'RepairFrom::selectSys01/$1');
    $routes->match(['get', 'post'], 'delete', 'RepairFrom::delete');
    $routes->match(['get', 'post'], 'delete/', 'RepairFrom::delete');
    $routes->match(['get', 'post'], 'export', 'RepairFrom::export');
});

// repair_to alias
$routes->group('repair_to', static function ($routes) {
    $routes->get('/', 'RepairTo::index');
    $routes->get('query/(:num)', 'RepairTo::query/$1');
    $routes->get('detail/(:num)', 'RepairTo::detail/$1');
    $routes->get('jiedan/(:num)', 'RepairTo::jiedan/$1');
    $routes->match(['get', 'post'], 'save_jiedan/(:num)', 'RepairTo::saveJiedan/$1');
    $routes->post('upload', 'RepairTo::upload');
    $routes->get('addpad06/(:num)', 'RepairTo::addpad06/$1');
    $routes->match(['get', 'post'], 'save_addpad06/(:num)', 'RepairTo::saveAddpad06/$1');
    $routes->get('jiean/(:num)', 'RepairTo::jiean/$1');
    $routes->match(['get', 'post'], 'save_jiean/(:num)', 'RepairTo::saveJiean/$1');
    $routes->match(['get', 'post'], 'goback/(:num)', 'RepairTo::goback/$1');
    $routes->match(['get', 'post'], 'closed/(:num)', 'RepairTo::closed/$1');
    $routes->get('select_sys01/(:num)', 'RepairTo::selectSys01/$1');
    $routes->match(['get', 'post'], 'export', 'RepairTo::export');
});

// query_report alias
$routes->group('query_report', static function ($routes) {
    $routes->get('/', 'QueryReport::index');
    $routes->get('index/(:segment)', 'QueryReport::index/$1');
    $routes->match(['get', 'post'], 'query', 'QueryReport::query');
    $routes->match(['get', 'post'], 'query/(:num)', 'QueryReport::query/$1');
    $routes->get('select_report/(:segment)', 'QueryReport::selectReport/$1');
    $routes->get('detail/(:segment)', 'QueryReport::detail/$1');
    $routes->post('get_fmd01', 'QueryReport::getFmd01');
    $routes->post('send', 'QueryReport::send');
    $routes->get('(:segment)', 'QueryReport::index/$1');
});

// query_report_item alias
$routes->group('query_report_item', static function ($routes) {
    $routes->get('/', 'QueryReportItem::index');
    $routes->match(['get', 'post'], 'query', 'QueryReportItem::query');
    $routes->match(['get', 'post'], 'query/(:num)', 'QueryReportItem::query/$1');
    $routes->get('select_report/(:segment)', 'QueryReportItem::selectReport/$1');
    $routes->get('download_excel', 'QueryReportItem::downloadExcel');
    $routes->get('export', 'QueryReportItem::downloadExcel');
});

// queryreport alias (no separator)
$routes->group('queryreport', static function ($routes) {
    $routes->get('/', 'QueryReport::index');
    $routes->get('index/(:segment)', 'QueryReport::index/$1');
    $routes->match(['get', 'post'], 'query', 'QueryReport::query');
    $routes->match(['get', 'post'], 'query/(:num)', 'QueryReport::query/$1');
    $routes->get('select_report/(:segment)', 'QueryReport::selectReport/$1');
    $routes->get('detail/(:segment)', 'QueryReport::detail/$1');
    $routes->post('get_fmd01', 'QueryReport::getFmd01');
    $routes->post('send', 'QueryReport::send');
    $routes->get('(:segment)', 'QueryReport::index/$1');
});

// queryreportitem alias (no separator)
$routes->group('queryreportitem', static function ($routes) {
    $routes->get('/', 'QueryReportItem::index');
    $routes->match(['get', 'post'], 'query', 'QueryReportItem::query');
    $routes->match(['get', 'post'], 'query/(:num)', 'QueryReportItem::query/$1');
    $routes->get('select_report/(:segment)', 'QueryReportItem::selectReport/$1');
    $routes->get('download_excel', 'QueryReportItem::downloadExcel');
    $routes->get('export', 'QueryReportItem::downloadExcel');
});

// generate_report alias
$routes->group('generate_report', static function ($routes) {
    $routes->get('/', 'GenerateReport::index');
    $routes->match(['get', 'post'], 'query/(:num)', 'GenerateReport::query/$1');
    $routes->match(['get', 'post'], 'generate', 'GenerateReport::generate');
    $routes->match(['get', 'post'], 'generate_report', 'GenerateReport::generateReport');
    $routes->match(['get', 'post'], 'select_report/(:num)', 'GenerateReport::selectReport/$1');
    $routes->match(['get', 'post'], 'send_report', 'GenerateReport::sendReport');
    $routes->match(['get', 'post'], 'detail/(:any)', 'GenerateReport::detail/$1');
    $routes->get('(:segment)', 'GenerateReport::index/$1');
});

// generatereport alias (no separator)
$routes->group('generatereport', static function ($routes) {
    $routes->get('/', 'GenerateReport::index');
    $routes->match(['get', 'post'], 'query', 'GenerateReport::query');
    $routes->match(['get', 'post'], 'query/(:num)', 'GenerateReport::query/$1');
    $routes->match(['get', 'post'], 'generate', 'GenerateReport::generate');
    $routes->match(['get', 'post'], 'generatereport', 'GenerateReport::generateReport');
    $routes->match(['get', 'post'], 'generate_report', 'GenerateReport::generateReport');
    $routes->match(['get', 'post'], 'select_report/(:segment)', 'GenerateReport::selectReport/$1');
    $routes->match(['get', 'post'], 'send_report', 'GenerateReport::sendReport');
    $routes->match(['get', 'post'], 'detail/(:any)', 'GenerateReport::detail/$1');
    $routes->get('(:segment)', 'GenerateReport::index/$1');
});

// approve_setting alias
$routes->group('approve_setting', static function ($routes) {
    $routes->get('/', 'ApproveSetting::index');
    $routes->get('query/(:num)', 'ApproveSetting::query/$1');
    $routes->get('detail/(:num)', 'ApproveSetting::detail/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'ApproveSetting::edit/$1');
    $routes->match(['get', 'post'], 'edit_fmd21/(:num)', 'ApproveSetting::editFmd21/$1');
    $routes->match(['get', 'post'], 'edit_fmd22/(:num)', 'ApproveSetting::editFmd22/$1');
    $routes->match(['get', 'post'], 'delete_fmd21', 'ApproveSetting::deleteFmd21');
    $routes->match(['get', 'post'], 'delete_fmd21/', 'ApproveSetting::deleteFmd21');
    $routes->match(['get', 'post'], 'delete_fmd22', 'ApproveSetting::deleteFmd22');
    $routes->match(['get', 'post'], 'delete_fmd22/', 'ApproveSetting::deleteFmd22');
    $routes->match(['get', 'post'], 'check_out', 'ApproveSetting::checkOut');
    $routes->match(['get', 'post'], 'check_out/', 'ApproveSetting::checkOut');
    $routes->match(['get', 'post'], 'commit', 'ApproveSetting::commit');
    $routes->match(['get', 'post'], 'commit/', 'ApproveSetting::commit');
});

// approvesetting alias (no separator - for CI3 compatibility)
$routes->group('approvesetting', static function ($routes) {
    $routes->get('/', 'ApproveSetting::index');
    $routes->get('query/(:num)', 'ApproveSetting::query/$1');
    $routes->get('detail/(:num)', 'ApproveSetting::detail/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'ApproveSetting::edit/$1');
    $routes->match(['get', 'post'], 'edit_fmd21/(:num)', 'ApproveSetting::editFmd21/$1');
    $routes->match(['get', 'post'], 'edit_fmd22/(:num)', 'ApproveSetting::editFmd22/$1');
    $routes->match(['get', 'post'], 'delete_fmd21', 'ApproveSetting::deleteFmd21');
    $routes->match(['get', 'post'], 'delete_fmd21/', 'ApproveSetting::deleteFmd21');
    $routes->match(['get', 'post'], 'delete_fmd22', 'ApproveSetting::deleteFmd22');
    $routes->match(['get', 'post'], 'delete_fmd22/', 'ApproveSetting::deleteFmd22');
    $routes->match(['get', 'post'], 'check_out', 'ApproveSetting::checkOut');
    $routes->match(['get', 'post'], 'check_out/', 'ApproveSetting::checkOut');
    $routes->match(['get', 'post'], 'commit', 'ApproveSetting::commit');
    $routes->match(['get', 'post'], 'commit/', 'ApproveSetting::commit');
});

// annual_checkup alias
$routes->group('annual_checkup', static function ($routes) {
    $routes->get('/', 'AnnualCheckup::index');
    $routes->match(['get', 'post'], 'query', 'AnnualCheckup::query');
    $routes->match(['get', 'post'], 'query/(:num)', 'AnnualCheckup::query/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'AnnualCheckup::edit/$1');
    $routes->match(['get', 'post'], 'delete', 'AnnualCheckup::delete');
    $routes->match(['get', 'post'], 'delete/', 'AnnualCheckup::delete');
    $routes->match(['get', 'post'], 'revert', 'AnnualCheckup::revert');
    $routes->match(['get', 'post'], 'revert/', 'AnnualCheckup::revert');
    $routes->match(['get', 'post'], 'commit', 'AnnualCheckup::commit');
    $routes->match(['get', 'post'], 'commit/', 'AnnualCheckup::commit');
    $routes->match(['get', 'post'], 'check-out', 'AnnualCheckup::checkOut');
    $routes->match(['get', 'post'], 'check-out/', 'AnnualCheckup::checkOut');
    $routes->get('detail/(:num)', 'AnnualCheckup::detail/$1');
});

// device_message alias
$routes->group('device_message', static function ($routes) {
    $routes->get('/', 'DeviceMessage::index');
    $routes->match(['get', 'post'], 'query', 'DeviceMessage::query');
    $routes->get('query/(:num)', 'DeviceMessage::query/$1');
    $routes->get('detail/(:num)', 'DeviceMessage::detail/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'DeviceMessage::edit/$1');
    $routes->match(['get', 'post'], 'delete', 'DeviceMessage::delete');
    $routes->match(['get', 'post'], 'delete/', 'DeviceMessage::delete');
    $routes->match(['get', 'post'], 'push', 'DeviceMessage::push');
    $routes->match(['get', 'post'], 'push/', 'DeviceMessage::push');
});

// devicemessage alias (no separator)
$routes->group('devicemessage', static function ($routes) {
    $routes->get('/', 'DeviceMessage::index');
    $routes->match(['get', 'post'], 'query', 'DeviceMessage::query');
    $routes->get('query/(:num)', 'DeviceMessage::query/$1');
    $routes->get('detail/(:num)', 'DeviceMessage::detail/$1');
    $routes->match(['get', 'post'], 'edit/(:num)', 'DeviceMessage::edit/$1');
    $routes->match(['get', 'post'], 'delete', 'DeviceMessage::delete');
    $routes->match(['get', 'post'], 'delete/', 'DeviceMessage::delete');
    $routes->match(['get', 'post'], 'push', 'DeviceMessage::push');
    $routes->match(['get', 'post'], 'push/', 'DeviceMessage::push');
});

// device_log alias
$routes->group('device_log', static function ($routes) {
    $routes->get('/', 'DeviceLog::index');
    $routes->match(['get', 'post'], 'query', 'DeviceLog::query');
    $routes->match(['get', 'post'], 'query/(:num)', 'DeviceLog::query/$1');
});

// devicelog alias (no separator)
$routes->group('devicelog', static function ($routes) {
    $routes->get('/', 'DeviceLog::index');
    $routes->match(['get', 'post'], 'query', 'DeviceLog::query');
    $routes->match(['get', 'post'], 'query/(:num)', 'DeviceLog::query/$1');
});

// emma_link alias
$routes->get('emma_link/send-notify/(:num)', 'EmmaLink::sendNotify/$1');
$routes->get('emma_link/send_notify/(:num)', 'EmmaLink::sendNotify/$1');
$routes->get('emma_link/repair/(:num)', 'EmmaLink::repair/$1');

// SDK Routes
$routes->group('sdk', ['namespace' => 'App\Controllers\Sdk'], static function ($routes) {
    // Menu Management
    $routes->get('menu', 'Menu::index');
    $routes->get('menu/detail/(:num)', 'Menu::detail/$1');
    $routes->match(['get', 'post'], 'menu/edit/(:num)', 'Menu::edit/$1');
    $routes->match(['get', 'post'], 'menu/edit/(:num)/(:num)', 'Menu::edit/$1/$2');
    $routes->get('menu/tree', 'Menu::tree');

    // Page Management
    $routes->get('page', 'Page::index');
    $routes->match(['get', 'post'], 'page/query', 'Page::query');
    $routes->match(['get', 'post'], 'page/query/(:num)', 'Page::query/$1');
    $routes->get('page/detail/(:num)', 'Page::detail/$1');
    $routes->match(['get', 'post'], 'page/edit', 'Page::edit');
    $routes->match(['get', 'post'], 'page/edit/(:num)', 'Page::edit/$1');
    $routes->post('page/delete', 'Page::delete');
    $routes->post('page/order', 'Page::order');

    // Table Management
    $routes->get('table', 'Table::index');
    $routes->match(['get', 'post'], 'table/query', 'Table::query');
    $routes->match(['get', 'post'], 'table/query/(:num)', 'Table::query/$1');
    $routes->get('table/detail/(:num)', 'Table::detail/$1');
    $routes->match(['get', 'post'], 'table/edit', 'Table::edit');
    $routes->match(['get', 'post'], 'table/edit/(:num)', 'Table::edit/$1');
    $routes->post('table/delete', 'Table::delete');
    $routes->get('table/get-table-list', 'Table::getTableList');
});
