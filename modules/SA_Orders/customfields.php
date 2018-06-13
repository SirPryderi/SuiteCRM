<?php
/**
 * @param $focus SA_Orders
 * @param $field string
 * @param $value string
 * @param $view string
 * @return string
 */
function line_items_function($focus, $field, $value, $view)
{
    $view = new Sugar_Smarty();

    $view->assign('items', $focus->get_line_items());

    return $view->fetch('modules/SA_LineItems/templates/list.view.tpl');
}