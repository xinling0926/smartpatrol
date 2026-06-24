<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Webhook (no auth required)
$routes->post('webhook/receive', 'Webhook::receive');

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
    $routes->get('forgot_password', 'Auth::forgotPassword');
    $routes->post('forgot_password', 'Auth::forgotPassword');
    $routes->get('reset-password/(:any)', 'Auth::resetPassword/$1');
    $routes->post('reset-password/(:any)', 'Auth::resetPassword/$1');
    $routes->get('reset_password/(:any)', 'Auth::resetPassword/$1');
    $routes->post('reset_password/(:any)', 'Auth::resetPassword/$1');
});

// Account Routes
$routes->group('account', static function ($routes) {
    $routes->get('/', 'Account::index');
    $routes->get('query/(:num)', 'Account::query/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Account::edit/$1');
    $routes->match(['GET', 'POST'], 'delete', 'Account::delete');
    $routes->match(['GET', 'POST'], 'delete/', 'Account::delete');
    $routes->get('change-password', 'Account::changePassword');
    $routes->post('change-password', 'Account::changePassword');
    // Alias with underscore for backwards compatibility
    $routes->get('change_password', 'Account::changePassword');
    $routes->post('change_password', 'Account::changePassword');
});

// Department Routes
$routes->group('department', static function ($routes) {
    $routes->get('/', 'Department::index');
    $routes->get('query/(:num)', 'Department::query/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Department::edit/$1');
    $routes->match(['GET', 'POST'], 'delete', 'Department::delete');
    $routes->match(['GET', 'POST'], 'delete/', 'Department::delete');
});

// Device Routes
$routes->group('device', static function ($routes) {
    $routes->get('/', 'Device::index');
    $routes->get('query/(:num)', 'Device::query/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Device::edit/$1');
    $routes->match(['GET', 'POST'], 'delete', 'Device::delete');
    $routes->match(['GET', 'POST'], 'delete/', 'Device::delete');
});

// Role Routes
$routes->group('role', static function ($routes) {
    $routes->get('/', 'Role::index');
    $routes->get('query/(:num)', 'Role::query/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Role::edit/$1');
    $routes->match(['GET', 'POST'], 'delete', 'Role::delete');
    $routes->match(['GET', 'POST'], 'delete/', 'Role::delete');
});

// Form Routes
$routes->group('form', static function ($routes) {
    $routes->get('/', 'Form::index');
    $routes->get('query/(:num)', 'Form::query/$1');
    $routes->match(['GET', 'POST'], 'query', 'Form::query');
    $routes->get('detail/(:num)', 'Form::detail/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Form::edit/$1');
    $routes->match(['GET', 'POST'], 'edit_fmd02/(:num)', 'Form::editFmd02/$1');
    $routes->match(['GET', 'POST'], 'edit_fmd03/(:num)', 'Form::editFmd03/$1');
    $routes->match(['GET', 'POST'], 'edit_fmd05/(:num)', 'Form::editFmd05/$1');
    $routes->match(['GET', 'POST'], 'delete', 'Form::delete');
    $routes->match(['GET', 'POST'], 'delete/', 'Form::delete');
    $routes->match(['GET', 'POST'], 'delete_fmd02', 'Form::deleteFmd02');
    $routes->match(['GET', 'POST'], 'delete_fmd02/', 'Form::deleteFmd02');
    $routes->match(['GET', 'POST'], 'delete_fmd03', 'Form::deleteFmd03');
    $routes->match(['GET', 'POST'], 'delete_fmd03/', 'Form::deleteFmd03');
    $routes->match(['GET', 'POST'], 'delete_fmd05', 'Form::deleteFmd05');
    $routes->match(['GET', 'POST'], 'delete_fmd05/', 'Form::deleteFmd05');
    $routes->match(['GET', 'POST'], 'state', 'Form::state');
    $routes->match(['GET', 'POST'], 'state/', 'Form::state');
});

// FormItem Routes
$routes->group('form-item', static function ($routes) {
    $routes->get('/', 'FormItem::index');
    $routes->match(['GET', 'POST'], 'query', 'FormItem::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'FormItem::query/$1');
    $routes->get('detail/(:num)', 'FormItem::detail/$1');
    $routes->match(['GET', 'POST'], 'edit-fmd01/(:num)', 'FormItem::editFmd01/$1');
    $routes->match(['GET', 'POST'], 'edit-fmd02/(:num)', 'FormItem::editFmd02/$1');
    $routes->match(['GET', 'POST'], 'edit-fmd04/(:num)', 'FormItem::editFmd04/$1');
    $routes->match(['GET', 'POST'], 'add-fmd04/(:num)', 'FormItem::addFmd04/$1');
    $routes->match(['GET', 'POST'], 'add-fmd04-sub/(:num)', 'FormItem::addFmd04Sub/$1');
    $routes->match(['GET', 'POST'], 'add-fmd04-first/(:num)', 'FormItem::addFmd04First/$1');
    $routes->match(['GET', 'POST'], 'copy-fmd04', 'FormItem::copyFmd04');
    $routes->match(['GET', 'POST'], 'copy-fmd06', 'FormItem::copyFmd06');
    $routes->match(['GET', 'POST'], 'order-fmd04', 'FormItem::orderFmd04');
    $routes->match(['GET', 'POST'], 'edit-fmd06/(:num)', 'FormItem::editFmd06/$1');
    $routes->match(['GET', 'POST'], 'edit-fmd07/(:num)', 'FormItem::editFmd07/$1');
    $routes->match(['GET', 'POST'], 'edit-fmd08/(:num)', 'FormItem::editFmd08/$1');
    $routes->match(['GET', 'POST'], 'edit-fmd09/(:num)', 'FormItem::editFmd09/$1');
    $routes->match(['GET', 'POST'], 'delete-fmd02', 'FormItem::deleteFmd02');
    $routes->match(['GET', 'POST'], 'delete-fmd04', 'FormItem::deleteFmd04');
    $routes->match(['GET', 'POST'], 'delete-fmd06', 'FormItem::deleteFmd06');
    $routes->match(['GET', 'POST'], 'delete-fmd07', 'FormItem::deleteFmd07');
    $routes->match(['GET', 'POST'], 'delete-fmd08', 'FormItem::deleteFmd08');
    $routes->match(['GET', 'POST'], 'delete-fmd09', 'FormItem::deleteFmd09');
    $routes->match(['GET', 'POST'], 'revert', 'FormItem::revert');
    $routes->match(['GET', 'POST'], 'commit', 'FormItem::commit');
    $routes->match(['GET', 'POST'], 'check-out', 'FormItem::checkOut');
    $routes->get('form-history/(:num)', 'FormItem::formHistory/$1');
});

// Enterprise Routes
$routes->group('enterprise', static function ($routes) {
    $routes->get('/', 'Enterprise::index');
    $routes->match(['GET', 'POST'], 'query', 'Enterprise::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'Enterprise::query/$1');
    $routes->get('detail/(:num)', 'Enterprise::detail/$1');
    $routes->get('detail_ent02/(:num)', 'Enterprise::detailEnt02/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Enterprise::edit/$1');
    $routes->match(['GET', 'POST'], 'edit', 'Enterprise::edit');
    $routes->match(['GET', 'POST'], 'edit_ent02/(:num)', 'Enterprise::editEnt02/$1');
    $routes->match(['GET', 'POST'], 'edit_ent02', 'Enterprise::editEnt02');
    $routes->match(['GET', 'POST'], 'delete', 'Enterprise::delete');
    $routes->match(['GET', 'POST'], 'delete/', 'Enterprise::delete');
    $routes->match(['GET', 'POST'], 'delete_ent02', 'Enterprise::deleteEnt02');
    $routes->match(['GET', 'POST'], 'delete_ent02/', 'Enterprise::deleteEnt02');
    $routes->match(['GET', 'POST'], 'upload_logo', 'Enterprise::uploadLogo');
    $routes->match(['GET', 'POST'], 'upload_logo/', 'Enterprise::uploadLogo');
});

// Update Routes
$routes->group('update', static function ($routes) {
    $routes->get('/', 'Update::index');
    $routes->post('upload', 'Update::upload');
});

// EUI Settings Routes
$routes->group('eui_settings', static function ($routes) {
    $routes->get('/', 'EuiSettings::index');
    $routes->match(['GET', 'POST'], 'query', 'EuiSettings::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'EuiSettings::query/$1');
    $routes->get('detail/(:num)', 'EuiSettings::detail/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'EuiSettings::edit/$1');
    $routes->match(['GET', 'POST'], 'edit', 'EuiSettings::edit');
    $routes->match(['GET', 'POST'], 'delete', 'EuiSettings::delete');
});

// EUI Settings Routes (no separator alias)
$routes->group('euisettings', static function ($routes) {
    $routes->get('/', 'EuiSettings::index');
    $routes->match(['GET', 'POST'], 'query', 'EuiSettings::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'EuiSettings::query/$1');
    $routes->get('detail/(:num)', 'EuiSettings::detail/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'EuiSettings::edit/$1');
    $routes->match(['GET', 'POST'], 'edit', 'EuiSettings::edit');
    $routes->match(['GET', 'POST'], 'delete', 'EuiSettings::delete');
});

// Log Viewer Routes
// $routes->group('logviewer', static function ($routes) {
//     $routes->get('/', 'LogViewer::index');
// });

// System Setting Routes
$routes->group('system-setting', static function ($routes) {
    $routes->get('/', 'SystemSetting::index');
    $routes->post('save', 'SystemSetting::save');
});

// System Setting Routes (without hyphen)
$routes->group('systemsetting', static function ($routes) {
    $routes->get('/', 'SystemSetting::index');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'SystemSetting::query/$1');
    $routes->get('detail/(:num)', 'SystemSetting::detail/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'SystemSetting::edit/$1');
    $routes->match(['GET', 'POST'], 'edit', 'SystemSetting::edit');
    $routes->match(['GET', 'POST'], 'delete', 'SystemSetting::delete');
    $routes->match(['GET', 'POST'], 'delete/', 'SystemSetting::delete');
    $routes->post('save', 'SystemSetting::save');
});

// Repair Routes
$routes->group('repair', static function ($routes) {
    $routes->get('/', 'Repair::index');
    $routes->get('query/(:num)', 'Repair::query/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Repair::edit/$1');
    $routes->match(['GET', 'POST'], 'export', 'Repair::export');
});

// Repair From Routes
$routes->group('repair-from', static function ($routes) {
    $routes->get('/', 'RepairFrom::index');
    $routes->get('query/(:num)', 'RepairFrom::query/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'RepairFrom::edit/$1');
    $routes->match(['GET', 'POST'], 'edit', 'RepairFrom::edit');
    $routes->get('sendto/(:num)', 'RepairFrom::sendto/$1');
    $routes->match(['GET', 'POST'], 'sendto_save/(:num)', 'RepairFrom::sendtoSave/$1');
    $routes->match(['GET', 'POST'], 'sendto_save', 'RepairFrom::sendtoSave');
    $routes->match(['GET', 'POST'], 'goback/(:num)', 'RepairFrom::goback/$1');
    $routes->post('upload', 'RepairFrom::upload');
    $routes->get('detail/(:num)', 'RepairFrom::detail/$1');
    $routes->match(['GET', 'POST'], 'closed/(:num)', 'RepairFrom::closed/$1');
    $routes->get('select_sys01/(:num)', 'RepairFrom::selectSys01/$1');
    $routes->match(['GET', 'POST'], 'delete', 'RepairFrom::delete');
    $routes->match(['GET', 'POST'], 'delete/', 'RepairFrom::delete');
    $routes->match(['GET', 'POST'], 'export', 'RepairFrom::export');
});

// Repair To Routes
$routes->group('repair-to', static function ($routes) {
    $routes->get('/', 'RepairTo::index');
    $routes->get('query/(:num)', 'RepairTo::query/$1');
    $routes->get('detail/(:num)', 'RepairTo::detail/$1');
    $routes->get('jiedan/(:num)', 'RepairTo::jiedan/$1');
    $routes->match(['GET', 'POST'], 'save_jiedan/(:num)', 'RepairTo::saveJiedan/$1');
    $routes->post('upload', 'RepairTo::upload');
    $routes->get('addpad06/(:num)', 'RepairTo::addpad06/$1');
    $routes->match(['GET', 'POST'], 'save_addpad06/(:num)', 'RepairTo::saveAddpad06/$1');
    $routes->get('jiean/(:num)', 'RepairTo::jiean/$1');
    $routes->match(['GET', 'POST'], 'save_jiean/(:num)', 'RepairTo::saveJiean/$1');
    $routes->match(['GET', 'POST'], 'goback/(:num)', 'RepairTo::goback/$1');
    $routes->match(['GET', 'POST'], 'closed/(:num)', 'RepairTo::closed/$1');
    $routes->get('select_sys01/(:num)', 'RepairTo::selectSys01/$1');
    $routes->match(['GET', 'POST'], 'export', 'RepairTo::export');
});

// Query Report Routes
$routes->group('query-report', static function ($routes) {
    $routes->get('/', 'QueryReport::index');
    $routes->get('index/(:segment)', 'QueryReport::index/$1');
    $routes->match(['GET', 'POST'], 'query', 'QueryReport::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'QueryReport::query/$1');
    $routes->get('select_report/(:segment)', 'QueryReport::selectReport/$1');
    $routes->get('detail/(:segment)', 'QueryReport::detail/$1');
    $routes->post('get_fmd01', 'QueryReport::getFmd01');
    $routes->post('send', 'QueryReport::send');
    $routes->get('(:segment)', 'QueryReport::index/$1');
});

// Query Report Item Routes
$routes->group('query-report-item', static function ($routes) {
    $routes->get('/', 'QueryReportItem::index');
    $routes->match(['GET', 'POST'], 'query', 'QueryReportItem::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'QueryReportItem::query/$1');
    $routes->get('select_report/(:segment)', 'QueryReportItem::selectReport/$1');
    $routes->get('download_excel', 'QueryReportItem::downloadExcel');
    $routes->get('export', 'QueryReportItem::downloadExcel');
});

// Generate Report Routes
$routes->group('generate-report', static function ($routes) {
    $routes->get('/', 'GenerateReport::index');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'GenerateReport::query/$1');
    $routes->match(['GET', 'POST'], 'generate', 'GenerateReport::generate');
    $routes->match(['GET', 'POST'], 'generate-report', 'GenerateReport::generateReport');
    $routes->match(['GET', 'POST'], 'select-report/(:num)', 'GenerateReport::selectReport/$1');
    $routes->match(['GET', 'POST'], 'send-report', 'GenerateReport::sendReport');
    $routes->match(['GET', 'POST'], 'detail/(:any)', 'GenerateReport::detail/$1');
    $routes->get('(:segment)', 'GenerateReport::index/$1');
});

// Rawdata Routes
$routes->group('rawdata', static function ($routes) {
    $routes->get('/', 'Rawdata::index');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'Rawdata::query/$1');
    $routes->get('detail/(:num)', 'Rawdata::detail/$1');
    $routes->get('detail_dialog/(:num)', 'Rawdata::detailDialog/$1');
    $routes->get('detail_dialog/(:num)/(:num)', 'Rawdata::detailDialog/$1/$2');
    $routes->get('detail_dialog_miss', 'Rawdata::detailDialogMiss');
    $routes->get('detail_dialog_miss/(:segment)', 'Rawdata::detailDialogMiss/$1');
    $routes->match(['GET', 'POST'], 'detail_add_comment', 'Rawdata::detailAddComment');
    $routes->match(['GET', 'POST'], 'detail_add_comment/(:segment)', 'Rawdata::detailAddComment/$1');
    $routes->match(['GET', 'POST'], 'detail_auto_comment_edit', 'Rawdata::detailAutoCommentEdit');
    $routes->match(['GET', 'POST'], 'detail_auto_comment_edit/(:segment)', 'Rawdata::detailAutoCommentEdit/$1');
    $routes->get('linkage_by_ent1001/(:num)', 'Rawdata::linkageByEnt1001/$1');
    $routes->post('linkage_pad0104', 'Rawdata::linkagePad0104');
});

// Missing Routes
$routes->group('missing', static function ($routes) {
    $routes->get('/', 'Missing::index');
    $routes->get('select-report/(:segment)', 'Missing::selectReport/$1');
    $routes->get('select_report/(:segment)', 'Missing::selectReport/$1');
    $routes->match(['GET', 'POST'], 'query', 'Missing::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'Missing::query/$1');
});

// Approve Routes
$routes->group('approve', static function ($routes) {
    $routes->get('/', 'Approve::index');
    $routes->get('approve-form/(:num)/(:num)', 'Approve::approveForm/$1/$2');
    $routes->get('approve_form/(:num)/(:num)', 'Approve::approveForm/$1/$2');
    $routes->post('do-approve', 'Approve::doApprove');
    $routes->post('do_approve', 'Approve::doApprove');
});

// Approve Setting Routes
$routes->group('approve-setting', static function ($routes) {
    $routes->get('/', 'ApproveSetting::index');
    $routes->get('query/(:num)', 'ApproveSetting::query/$1');
    $routes->get('detail/(:num)', 'ApproveSetting::detail/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'ApproveSetting::edit/$1');
    $routes->match(['GET', 'POST'], 'edit-fmd21/(:num)', 'ApproveSetting::editFmd21/$1');
    $routes->match(['GET', 'POST'], 'edit-fmd22/(:num)', 'ApproveSetting::editFmd22/$1');
    $routes->match(['GET', 'POST'], 'delete-fmd21', 'ApproveSetting::deleteFmd21');
    $routes->match(['GET', 'POST'], 'delete-fmd22', 'ApproveSetting::deleteFmd22');
    $routes->match(['GET', 'POST'], 'check-out', 'ApproveSetting::checkOut');
    $routes->match(['GET', 'POST'], 'commit', 'ApproveSetting::commit');
});

// Annual Checkup Routes
$routes->group('annual-checkup', static function ($routes) {
    $routes->get('/', 'AnnualCheckup::index');
    $routes->match(['GET', 'POST'], 'query', 'AnnualCheckup::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'AnnualCheckup::query/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'AnnualCheckup::edit/$1');
    $routes->match(['GET', 'POST'], 'delete', 'AnnualCheckup::delete');
    $routes->match(['GET', 'POST'], 'delete/', 'AnnualCheckup::delete');
    $routes->match(['GET', 'POST'], 'revert', 'AnnualCheckup::revert');
    $routes->match(['GET', 'POST'], 'revert/', 'AnnualCheckup::revert');
    $routes->match(['GET', 'POST'], 'commit', 'AnnualCheckup::commit');
    $routes->match(['GET', 'POST'], 'commit/', 'AnnualCheckup::commit');
    $routes->match(['GET', 'POST'], 'check-out', 'AnnualCheckup::checkOut');
    $routes->match(['GET', 'POST'], 'check-out/', 'AnnualCheckup::checkOut');
    $routes->get('detail/(:num)', 'AnnualCheckup::detail/$1');
});

// annualcheckup alias (without separator)
$routes->group('annualcheckup', static function ($routes) {
    $routes->get('/', 'AnnualCheckup::index');
    $routes->match(['GET', 'POST'], 'query', 'AnnualCheckup::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'AnnualCheckup::query/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'AnnualCheckup::edit/$1');
    $routes->match(['GET', 'POST'], 'delete', 'AnnualCheckup::delete');
    $routes->match(['GET', 'POST'], 'delete/', 'AnnualCheckup::delete');
    $routes->match(['GET', 'POST'], 'revert', 'AnnualCheckup::revert');
    $routes->match(['GET', 'POST'], 'revert/', 'AnnualCheckup::revert');
    $routes->match(['GET', 'POST'], 'commit', 'AnnualCheckup::commit');
    $routes->match(['GET', 'POST'], 'commit/', 'AnnualCheckup::commit');
    $routes->match(['GET', 'POST'], 'check-out', 'AnnualCheckup::checkOut');
    $routes->match(['GET', 'POST'], 'check-out/', 'AnnualCheckup::checkOut');
    $routes->get('detail/(:num)', 'AnnualCheckup::detail/$1');
});

// Device Message Routes
$routes->group('device-message', static function ($routes) {
    $routes->get('/', 'DeviceMessage::index');
    $routes->match(['GET', 'POST'], 'query', 'DeviceMessage::query');
    $routes->get('query/(:num)', 'DeviceMessage::query/$1');
    $routes->get('detail/(:num)', 'DeviceMessage::detail/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'DeviceMessage::edit/$1');
    $routes->match(['GET', 'POST'], 'delete', 'DeviceMessage::delete');
    $routes->match(['GET', 'POST'], 'delete/', 'DeviceMessage::delete');
    $routes->match(['GET', 'POST'], 'push', 'DeviceMessage::push');
    $routes->match(['GET', 'POST'], 'push/', 'DeviceMessage::push');
});

// Device Log Routes
$routes->group('device-log', static function ($routes) {
    $routes->get('/', 'DeviceLog::index');
    $routes->match(['GET', 'POST'], 'query', 'DeviceLog::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'DeviceLog::query/$1');
});

// Operation Log Routes
$routes->group('operation-log', static function ($routes) {
    $routes->get('/', 'OperationLog::index');
    $routes->match(['GET', 'POST'], 'query', 'OperationLog::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'OperationLog::query/$1');
});

// Operation Log Routes (underscore alias for CI3 compatibility)
$routes->group('operation_log', static function ($routes) {
    $routes->get('/', 'OperationLog::index');
    $routes->match(['GET', 'POST'], 'query', 'OperationLog::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'OperationLog::query/$1');
});

// Operation Log Routes (no separator - for JavaScript URL building)
$routes->group('operationlog', static function ($routes) {
    $routes->get('/', 'OperationLog::index');
    $routes->match(['GET', 'POST'], 'query', 'OperationLog::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'OperationLog::query/$1');
});

// Notice Routes
$routes->group('notice', static function ($routes) {
    $routes->get('/', 'Notice::index');
    $routes->get('query/(:num)', 'Notice::query/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Notice::edit/$1');
    $routes->match(['GET', 'POST'], 'delete', 'Notice::delete');
    $routes->match(['GET', 'POST'], 'delete/', 'Notice::delete');
});

// Tag Routes
$routes->group('tag', static function ($routes) {
    $routes->get('/', 'Tag::index');
    $routes->get('query/(:num)', 'Tag::query/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Tag::edit/$1');
    $routes->match(['GET', 'POST'], 'delete', 'Tag::delete');
    $routes->match(['GET', 'POST'], 'delete/', 'Tag::delete');
});

// Jobtitle Routes
$routes->group('jobtitle', static function ($routes) {
    $routes->get('/', 'Jobtitle::index');
    $routes->get('query/(:num)', 'Jobtitle::query/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Jobtitle::edit/$1');
    $routes->match(['GET', 'POST'], 'delete', 'Jobtitle::delete');
    $routes->match(['GET', 'POST'], 'delete/', 'Jobtitle::delete');
});

// Photograph Routes
$routes->group('photograph', static function ($routes) {
    $routes->get('/', 'Photograph::index');
    $routes->match(['GET', 'POST'], 'query', 'Photograph::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'Photograph::query/$1');
    $routes->get('detail/(:num)', 'Photograph::detail/$1');
    $routes->get('getPhotograph/(:num)', 'Photograph::getPhotograph/$1');
    $routes->get('getPhotograph/(:num)/(:num)', 'Photograph::getPhotograph/$1/$2');
    $routes->get('linkageByEnt1001/(:num)', 'Photograph::linkageByEnt1001/$1');
    $routes->get('linkagebyent1001/(:num)', 'Photograph::linkageByEnt1001/$1');
    $routes->match(['GET', 'POST'], 'delete', 'Photograph::delete');
    $routes->match(['GET', 'POST'], 'delete/', 'Photograph::delete');
});

// Web API Routes
$routes->group('webapi', static function ($routes) {
    $routes->get('/', 'Webapi::index');
    $routes->match(['GET', 'POST'], 'index', 'Webapi::index');
    $routes->match(['GET', 'POST'], 'register', 'Webapi::register');
    $routes->match(['GET', 'POST'], 'login', 'Webapi::login');
    $routes->match(['GET', 'POST'], 'logout', 'Webapi::logout');
    $routes->match(['GET', 'POST'], 'changepass', 'Webapi::changepass');
    $routes->match(['GET', 'POST'], 'changeface', 'Webapi::changeface');
    $routes->match(['GET', 'POST'], 'addpad01', 'Webapi::addpad01');
    $routes->match(['GET', 'POST'], 'addpad01multi', 'Webapi::addpad01multi');
    $routes->match(['GET', 'POST'], 'addrepair', 'Webapi::addrepair');
    $routes->match(['GET', 'POST'], 'addrepairmulti', 'Webapi::addrepairmulti');
    $routes->match(['GET', 'POST'], 'now', 'Webapi::now');
    $routes->match(['GET', 'POST'], 'updatabase', 'Webapi::updatabase');
    $routes->match(['GET', 'POST'], 'version', 'Webapi::version');
    $routes->match(['GET', 'POST'], 'checkVersion', 'Webapi::checkVersion');
    $routes->match(['GET', 'POST'], 'getUserByPatrol', 'Webapi::getUserByPatrol');
    $routes->match(['GET', 'POST'], 'getAll', 'Webapi::getAll');
    $routes->match(['GET', 'POST'], 'getException', 'Webapi::getException');
    $routes->match(['GET', 'POST'], 'updateent01', 'Webapi::updateent01');
    $routes->match(['GET', 'POST'], 'uploaddev02', 'Webapi::uploaddev02');
    $routes->match(['GET', 'POST'], 'regtags', 'Webapi::regtags');
    $routes->match(['GET', 'POST'], 'photograph', 'Webapi::photograph');
    $routes->post('messageAck/(:alpha)', 'Webapi::messageAck/$1');
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
    $routes->match(['GET', 'POST'], 'report1/report', 'HpwReport1::report');

    // Report 2 - 加工廠抽查數量統計表
    $routes->get('report2', 'HpwReport2::index');
    $routes->match(['GET', 'POST'], 'report2/report', 'HpwReport2::report');
    $routes->get('report2/detail/(:any)', 'HpwReport2::detail/$1');
    $routes->get('report2/export-excel', 'HpwReport2::exportExcel');

    // Report 4 - 市場部開門率統計表 (新版)
    $routes->get('report4', 'HpwReport4::index');
    $routes->match(['GET', 'POST'], 'report4/report', 'HpwReport4::report');
});

// ============================================================
// CI3 Compatibility - Underscore Alias Routes
// ============================================================

// form_item alias
$routes->group('form_item', static function ($routes) {
    $routes->get('/', 'FormItem::index');
    $routes->get('query/(:num)', 'FormItem::query/$1');
    $routes->get('detail/(:num)', 'FormItem::detail/$1');
    $routes->match(['GET', 'POST'], 'edit-fmd01/(:num)', 'FormItem::editFmd01/$1');
    $routes->match(['GET', 'POST'], 'edit-fmd02/(:num)', 'FormItem::editFmd02/$1');
    $routes->match(['GET', 'POST'], 'delete-fmd02', 'FormItem::deleteFmd02');
    $routes->match(['GET', 'POST'], 'delete-fmd02/', 'FormItem::deleteFmd02');
    $routes->match(['GET', 'POST'], 'revert', 'FormItem::revert');
    $routes->match(['GET', 'POST'], 'revert/', 'FormItem::revert');
    $routes->match(['GET', 'POST'], 'commit', 'FormItem::commit');
    $routes->match(['GET', 'POST'], 'commit/', 'FormItem::commit');
    $routes->match(['GET', 'POST'], 'check-out', 'FormItem::checkOut');
    $routes->match(['GET', 'POST'], 'check-out/', 'FormItem::checkOut');
    $routes->get('form-history/(:num)', 'FormItem::formHistory/$1');
});

// formitem alias (no separator - for JavaScript URL building)
$routes->group('formitem', static function ($routes) {
    $routes->get('/', 'FormItem::index');
    $routes->match(['GET', 'POST'], 'query', 'FormItem::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'FormItem::query/$1');
    $routes->get('detail/(:num)', 'FormItem::detail/$1');
    $routes->match(['GET', 'POST'], 'edit_fmd01/(:num)', 'FormItem::editFmd01/$1');
    $routes->match(['GET', 'POST'], 'edit_fmd02/(:num)', 'FormItem::editFmd02/$1');
    $routes->match(['GET', 'POST'], 'edit_fmd04/(:num)', 'FormItem::editFmd04/$1');
    $routes->match(['GET', 'POST'], 'add_fmd04_sub/(:num)', 'FormItem::addFmd04Sub/$1');
    $routes->match(['GET', 'POST'], 'add_fmd04first/(:num)', 'FormItem::addFmd04First/$1');
    $routes->match(['GET', 'POST'], 'edit_fmd06/(:num)', 'FormItem::editFmd06/$1');
    $routes->match(['GET', 'POST'], 'edit_fmd07/(:num)', 'FormItem::editFmd07/$1');
    $routes->match(['GET', 'POST'], 'edit_fmd08/(:num)', 'FormItem::editFmd08/$1');
    $routes->match(['GET', 'POST'], 'edit_fmd09/(:num)', 'FormItem::editFmd09/$1');
    $routes->match(['GET', 'POST'], 'add_fmd07/(:segment)', 'FormItem::addFmd07/$1');
    $routes->match(['GET', 'POST'], 'add_fmd09/(:num)', 'FormItem::addFmd09/$1');
    $routes->match(['GET', 'POST'], 'edit_form/(:num)', 'FormItem::editForm/$1');
    $routes->match(['GET', 'POST'], 'add_fmd0906/(:num)', 'FormItem::addFmd0906/$1');
    $routes->match(['GET', 'POST'], 'delete_fmd02', 'FormItem::deleteFmd02');
    $routes->match(['GET', 'POST'], 'delete_fmd02/', 'FormItem::deleteFmd02');
    $routes->match(['GET', 'POST'], 'delete_fmd04', 'FormItem::deleteFmd04');
    $routes->match(['GET', 'POST'], 'delete_fmd04/', 'FormItem::deleteFmd04');
    $routes->match(['GET', 'POST'], 'delete_fmd06', 'FormItem::deleteFmd06');
    $routes->match(['GET', 'POST'], 'delete_fmd06/', 'FormItem::deleteFmd06');
    $routes->match(['GET', 'POST'], 'delete_fmd07', 'FormItem::deleteFmd07');
    $routes->match(['GET', 'POST'], 'delete_fmd07/', 'FormItem::deleteFmd07');
    $routes->match(['GET', 'POST'], 'delete_fmd08', 'FormItem::deleteFmd08');
    $routes->match(['GET', 'POST'], 'delete_fmd08/', 'FormItem::deleteFmd08');
    $routes->match(['GET', 'POST'], 'delete_fmd09', 'FormItem::deleteFmd09');
    $routes->match(['GET', 'POST'], 'delete_fmd09/', 'FormItem::deleteFmd09');
    $routes->match(['GET', 'POST'], 'delete_fmd0906', 'FormItem::deleteFmd0906');
    $routes->match(['GET', 'POST'], 'delete_fmd0906/', 'FormItem::deleteFmd0906');
    $routes->match(['GET', 'POST'], 'copy_fmd04', 'FormItem::copyFmd04');
    $routes->match(['GET', 'POST'], 'copy_fmd04/', 'FormItem::copyFmd04');
    $routes->match(['GET', 'POST'], 'copy_fmd06', 'FormItem::copyFmd06');
    $routes->match(['GET', 'POST'], 'copy_fmd06/', 'FormItem::copyFmd06');
    $routes->match(['GET', 'POST'], 'add_fmd04/(:num)', 'FormItem::addFmd04/$1');
    $routes->match(['GET', 'POST'], 'order_fmd04', 'FormItem::orderFmd04');
    $routes->match(['GET', 'POST'], 'order_fmd04/', 'FormItem::orderFmd04');
    $routes->match(['GET', 'POST'], 'revert', 'FormItem::revert');
    $routes->match(['GET', 'POST'], 'revert/', 'FormItem::revert');
    $routes->match(['GET', 'POST'], 'commit', 'FormItem::commit');
    $routes->match(['GET', 'POST'], 'commit/', 'FormItem::commit');
    $routes->match(['GET', 'POST'], 'check_out', 'FormItem::checkOut');
    $routes->match(['GET', 'POST'], 'check_out/', 'FormItem::checkOut');
    $routes->get('form_history/(:num)', 'FormItem::formHistory/$1');
});

// system_setting alias
$routes->group('system_setting', static function ($routes) {
    $routes->get('/', 'SystemSetting::index');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'SystemSetting::query/$1');
    $routes->get('detail/(:num)', 'SystemSetting::detail/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'SystemSetting::edit/$1');
    $routes->match(['GET', 'POST'], 'edit', 'SystemSetting::edit');
    $routes->post('save', 'SystemSetting::save');
    $routes->get('android_man', 'SystemSetting::androidMan');
    $routes->match(['GET', 'POST'], 'android_man_edit', 'SystemSetting::androidManEdit');
    $routes->post('android_man_upload_apk', 'SystemSetting::androidManUploadApk');
});

// systemsetting alias (no separator)
$routes->group('systemsetting', static function ($routes) {
    $routes->get('/', 'SystemSetting::index');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'SystemSetting::query/$1');
    $routes->get('detail/(:num)', 'SystemSetting::detail/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'SystemSetting::edit/$1');
    $routes->match(['GET', 'POST'], 'edit', 'SystemSetting::edit');
    $routes->get('androidman', 'SystemSetting::androidMan');
    $routes->match(['GET', 'POST'], 'android_man_edit/(:num)', 'SystemSetting::androidManEdit');
    $routes->match(['GET', 'POST'], 'android_man_edit', 'SystemSetting::androidManEdit');
    $routes->post('android_man_upload_apk', 'SystemSetting::androidManUploadApk');
});

// repair_from alias
$routes->group('repair_from', static function ($routes) {
    $routes->get('/', 'RepairFrom::index');
    $routes->get('query/(:num)', 'RepairFrom::query/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'RepairFrom::edit/$1');
    $routes->match(['GET', 'POST'], 'edit', 'RepairFrom::edit');
    $routes->get('sendto/(:num)', 'RepairFrom::sendto/$1');
    $routes->match(['GET', 'POST'], 'sendto_save/(:num)', 'RepairFrom::sendtoSave/$1');
    $routes->match(['GET', 'POST'], 'sendto_save', 'RepairFrom::sendtoSave');
    $routes->match(['GET', 'POST'], 'goback/(:num)', 'RepairFrom::goback/$1');
    $routes->post('upload', 'RepairFrom::upload');
    $routes->get('detail/(:num)', 'RepairFrom::detail/$1');
    $routes->match(['GET', 'POST'], 'closed/(:num)', 'RepairFrom::closed/$1');
    $routes->get('select_sys01/(:num)', 'RepairFrom::selectSys01/$1');
    $routes->match(['GET', 'POST'], 'delete', 'RepairFrom::delete');
    $routes->match(['GET', 'POST'], 'delete/', 'RepairFrom::delete');
    $routes->match(['GET', 'POST'], 'export', 'RepairFrom::export');
});

// repair_to alias
$routes->group('repair_to', static function ($routes) {
    $routes->get('/', 'RepairTo::index');
    $routes->get('query/(:num)', 'RepairTo::query/$1');
    $routes->get('detail/(:num)', 'RepairTo::detail/$1');
    $routes->get('jiedan/(:num)', 'RepairTo::jiedan/$1');
    $routes->match(['GET', 'POST'], 'save_jiedan/(:num)', 'RepairTo::saveJiedan/$1');
    $routes->post('upload', 'RepairTo::upload');
    $routes->get('addpad06/(:num)', 'RepairTo::addpad06/$1');
    $routes->match(['GET', 'POST'], 'save_addpad06/(:num)', 'RepairTo::saveAddpad06/$1');
    $routes->get('jiean/(:num)', 'RepairTo::jiean/$1');
    $routes->match(['GET', 'POST'], 'save_jiean/(:num)', 'RepairTo::saveJiean/$1');
    $routes->match(['GET', 'POST'], 'goback/(:num)', 'RepairTo::goback/$1');
    $routes->match(['GET', 'POST'], 'closed/(:num)', 'RepairTo::closed/$1');
    $routes->get('select_sys01/(:num)', 'RepairTo::selectSys01/$1');
    $routes->match(['GET', 'POST'], 'export', 'RepairTo::export');
});

// query_report alias
$routes->group('query_report', static function ($routes) {
    $routes->get('/', 'QueryReport::index');
    $routes->get('index/(:segment)', 'QueryReport::index/$1');
    $routes->match(['GET', 'POST'], 'query', 'QueryReport::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'QueryReport::query/$1');
    $routes->get('select_report/(:segment)', 'QueryReport::selectReport/$1');
    $routes->get('detail/(:segment)', 'QueryReport::detail/$1');
    $routes->post('get_fmd01', 'QueryReport::getFmd01');
    $routes->post('send', 'QueryReport::send');
    $routes->get('(:segment)', 'QueryReport::index/$1');
});

// query_report_item alias
$routes->group('query_report_item', static function ($routes) {
    $routes->get('/', 'QueryReportItem::index');
    $routes->match(['GET', 'POST'], 'query', 'QueryReportItem::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'QueryReportItem::query/$1');
    $routes->get('select_report/(:segment)', 'QueryReportItem::selectReport/$1');
    $routes->get('download_excel', 'QueryReportItem::downloadExcel');
    $routes->get('export', 'QueryReportItem::downloadExcel');
});

// queryreport alias (no separator)
$routes->group('queryreport', static function ($routes) {
    $routes->get('/', 'QueryReport::index');
    $routes->get('index/(:segment)', 'QueryReport::index/$1');
    $routes->match(['GET', 'POST'], 'query', 'QueryReport::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'QueryReport::query/$1');
    $routes->get('select_report/(:segment)', 'QueryReport::selectReport/$1');
    $routes->get('detail/(:segment)', 'QueryReport::detail/$1');
    $routes->post('get_fmd01', 'QueryReport::getFmd01');
    $routes->post('send', 'QueryReport::send');
    $routes->get('(:segment)', 'QueryReport::index/$1');
});

// queryreportitem alias (no separator)
$routes->group('queryreportitem', static function ($routes) {
    $routes->get('/', 'QueryReportItem::index');
    $routes->match(['GET', 'POST'], 'query', 'QueryReportItem::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'QueryReportItem::query/$1');
    $routes->get('select_report/(:segment)', 'QueryReportItem::selectReport/$1');
    $routes->get('download_excel', 'QueryReportItem::downloadExcel');
    $routes->get('export', 'QueryReportItem::downloadExcel');
});

// generate_report alias
$routes->group('generate_report', static function ($routes) {
    $routes->get('/', 'GenerateReport::index');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'GenerateReport::query/$1');
    $routes->match(['GET', 'POST'], 'generate', 'GenerateReport::generate');
    $routes->match(['GET', 'POST'], 'generate_report', 'GenerateReport::generateReport');
    $routes->match(['GET', 'POST'], 'select_report/(:num)', 'GenerateReport::selectReport/$1');
    $routes->match(['GET', 'POST'], 'send_report', 'GenerateReport::sendReport');
    $routes->match(['GET', 'POST'], 'detail/(:any)', 'GenerateReport::detail/$1');
    $routes->get('(:segment)', 'GenerateReport::index/$1');
});

// generatereport alias (no separator)
$routes->group('generatereport', static function ($routes) {
    $routes->get('/', 'GenerateReport::index');
    $routes->match(['GET', 'POST'], 'query', 'GenerateReport::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'GenerateReport::query/$1');
    $routes->match(['GET', 'POST'], 'generate', 'GenerateReport::generate');
    $routes->match(['GET', 'POST'], 'generatereport', 'GenerateReport::generateReport');
    $routes->match(['GET', 'POST'], 'generate_report', 'GenerateReport::generateReport');
    $routes->match(['GET', 'POST'], 'select_report/(:segment)', 'GenerateReport::selectReport/$1');
    $routes->match(['GET', 'POST'], 'send_report', 'GenerateReport::sendReport');
    $routes->match(['GET', 'POST'], 'detail/(:any)', 'GenerateReport::detail/$1');
    $routes->get('(:segment)', 'GenerateReport::index/$1');
});

// approve_setting alias
$routes->group('approve_setting', static function ($routes) {
    $routes->get('/', 'ApproveSetting::index');
    $routes->get('query/(:num)', 'ApproveSetting::query/$1');
    $routes->get('detail/(:num)', 'ApproveSetting::detail/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'ApproveSetting::edit/$1');
    $routes->match(['GET', 'POST'], 'edit_fmd21/(:num)', 'ApproveSetting::editFmd21/$1');
    $routes->match(['GET', 'POST'], 'edit_fmd22/(:num)', 'ApproveSetting::editFmd22/$1');
    $routes->match(['GET', 'POST'], 'delete_fmd21', 'ApproveSetting::deleteFmd21');
    $routes->match(['GET', 'POST'], 'delete_fmd21/', 'ApproveSetting::deleteFmd21');
    $routes->match(['GET', 'POST'], 'delete_fmd22', 'ApproveSetting::deleteFmd22');
    $routes->match(['GET', 'POST'], 'delete_fmd22/', 'ApproveSetting::deleteFmd22');
    $routes->match(['GET', 'POST'], 'check_out', 'ApproveSetting::checkOut');
    $routes->match(['GET', 'POST'], 'check_out/', 'ApproveSetting::checkOut');
    $routes->match(['GET', 'POST'], 'commit', 'ApproveSetting::commit');
    $routes->match(['GET', 'POST'], 'commit/', 'ApproveSetting::commit');
});

// approvesetting alias (no separator - for CI3 compatibility)
$routes->group('approvesetting', static function ($routes) {
    $routes->get('/', 'ApproveSetting::index');
    $routes->get('query/(:num)', 'ApproveSetting::query/$1');
    $routes->get('detail/(:num)', 'ApproveSetting::detail/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'ApproveSetting::edit/$1');
    $routes->match(['GET', 'POST'], 'edit_fmd21/(:num)', 'ApproveSetting::editFmd21/$1');
    $routes->match(['GET', 'POST'], 'edit_fmd22/(:num)', 'ApproveSetting::editFmd22/$1');
    $routes->match(['GET', 'POST'], 'delete_fmd21', 'ApproveSetting::deleteFmd21');
    $routes->match(['GET', 'POST'], 'delete_fmd21/', 'ApproveSetting::deleteFmd21');
    $routes->match(['GET', 'POST'], 'delete_fmd22', 'ApproveSetting::deleteFmd22');
    $routes->match(['GET', 'POST'], 'delete_fmd22/', 'ApproveSetting::deleteFmd22');
    $routes->match(['GET', 'POST'], 'check_out', 'ApproveSetting::checkOut');
    $routes->match(['GET', 'POST'], 'check_out/', 'ApproveSetting::checkOut');
    $routes->match(['GET', 'POST'], 'commit', 'ApproveSetting::commit');
    $routes->match(['GET', 'POST'], 'commit/', 'ApproveSetting::commit');
});

// annual_checkup alias
$routes->group('annual_checkup', static function ($routes) {
    $routes->get('/', 'AnnualCheckup::index');
    $routes->match(['GET', 'POST'], 'query', 'AnnualCheckup::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'AnnualCheckup::query/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'AnnualCheckup::edit/$1');
    $routes->match(['GET', 'POST'], 'delete', 'AnnualCheckup::delete');
    $routes->match(['GET', 'POST'], 'delete/', 'AnnualCheckup::delete');
    $routes->match(['GET', 'POST'], 'revert', 'AnnualCheckup::revert');
    $routes->match(['GET', 'POST'], 'revert/', 'AnnualCheckup::revert');
    $routes->match(['GET', 'POST'], 'commit', 'AnnualCheckup::commit');
    $routes->match(['GET', 'POST'], 'commit/', 'AnnualCheckup::commit');
    $routes->match(['GET', 'POST'], 'check-out', 'AnnualCheckup::checkOut');
    $routes->match(['GET', 'POST'], 'check-out/', 'AnnualCheckup::checkOut');
    $routes->get('detail/(:num)', 'AnnualCheckup::detail/$1');
});

// device_message alias
$routes->group('device_message', static function ($routes) {
    $routes->get('/', 'DeviceMessage::index');
    $routes->match(['GET', 'POST'], 'query', 'DeviceMessage::query');
    $routes->get('query/(:num)', 'DeviceMessage::query/$1');
    $routes->get('detail/(:num)', 'DeviceMessage::detail/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'DeviceMessage::edit/$1');
    $routes->match(['GET', 'POST'], 'delete', 'DeviceMessage::delete');
    $routes->match(['GET', 'POST'], 'delete/', 'DeviceMessage::delete');
    $routes->match(['GET', 'POST'], 'push', 'DeviceMessage::push');
    $routes->match(['GET', 'POST'], 'push/', 'DeviceMessage::push');
});

// devicemessage alias (no separator)
$routes->group('devicemessage', static function ($routes) {
    $routes->get('/', 'DeviceMessage::index');
    $routes->match(['GET', 'POST'], 'query', 'DeviceMessage::query');
    $routes->get('query/(:num)', 'DeviceMessage::query/$1');
    $routes->get('detail/(:num)', 'DeviceMessage::detail/$1');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'DeviceMessage::edit/$1');
    $routes->match(['GET', 'POST'], 'delete', 'DeviceMessage::delete');
    $routes->match(['GET', 'POST'], 'delete/', 'DeviceMessage::delete');
    $routes->match(['GET', 'POST'], 'push', 'DeviceMessage::push');
    $routes->match(['GET', 'POST'], 'push/', 'DeviceMessage::push');
});

// device_log alias
$routes->group('device_log', static function ($routes) {
    $routes->get('/', 'DeviceLog::index');
    $routes->match(['GET', 'POST'], 'query', 'DeviceLog::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'DeviceLog::query/$1');
});

// devicelog alias (no separator)
$routes->group('devicelog', static function ($routes) {
    $routes->get('/', 'DeviceLog::index');
    $routes->match(['GET', 'POST'], 'query', 'DeviceLog::query');
    $routes->match(['GET', 'POST'], 'query/(:num)', 'DeviceLog::query/$1');
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
    $routes->match(['GET', 'POST'], 'menu/edit/(:num)', 'Menu::edit/$1');
    $routes->match(['GET', 'POST'], 'menu/edit/(:num)/(:num)', 'Menu::edit/$1/$2');
    $routes->get('menu/tree', 'Menu::tree');

    // Page Management
    $routes->get('page', 'Page::index');
    $routes->match(['GET', 'POST'], 'page/query', 'Page::query');
    $routes->match(['GET', 'POST'], 'page/query/(:num)', 'Page::query/$1');
    $routes->get('page/detail/(:num)', 'Page::detail/$1');
    $routes->match(['GET', 'POST'], 'page/edit', 'Page::edit');
    $routes->match(['GET', 'POST'], 'page/edit/(:num)', 'Page::edit/$1');
    $routes->post('page/delete', 'Page::delete');
    $routes->post('page/order', 'Page::order');

    // Table Management
    $routes->get('table', 'Table::index');
    $routes->match(['GET', 'POST'], 'table/query', 'Table::query');
    $routes->match(['GET', 'POST'], 'table/query/(:num)', 'Table::query/$1');
    $routes->get('table/detail/(:num)', 'Table::detail/$1');
    $routes->match(['GET', 'POST'], 'table/edit', 'Table::edit');
    $routes->match(['GET', 'POST'], 'table/edit/(:num)', 'Table::edit/$1');
    $routes->post('table/delete', 'Table::delete');
    $routes->get('table/get-table-list', 'Table::getTableList');
});
