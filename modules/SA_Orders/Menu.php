<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

global $mod_strings, $app_strings, $sugar_config;

if (ACLController::checkAccess('SA_Orders', 'edit', true)) {
    $module_menu[] = array('index.php?module=SA_Orders&action=EditView&return_module=SA_Orders&return_action=DetailView', $mod_strings['LNK_NEW_RECORD'], 'Add', 'SA_Orders');
}
if (ACLController::checkAccess('SA_Orders', 'list', true)) {
    $module_menu[] = array('index.php?module=SA_Orders&action=index&return_module=SA_Orders&return_action=DetailView', $mod_strings['LNK_LIST'], 'View', 'SA_Orders');
}
if (ACLController::checkAccess('SA_Orders', 'import', true)) {
    $module_menu[] = array('index.php?module=Import&action=Step1&import_module=SA_Orders&return_module=SA_Orders&return_action=index', $mod_strings['LBL_IMPORT_ORDERS'], 'Import', 'SA_Orders');
}
if (ACLController::checkAccess('SA_Orders', 'import', true) &&
    ACLController::checkAccess('SA_OrderItems', 'import', true)) {
    $module_menu[] = array('index.php?module=Import&action=Step1&import_module=SA_OrderItems&return_module=SA_Orders&return_action=index', $mod_strings['LBL_IMPORT_ITEMS'], 'Import', 'SA_Orders');
}