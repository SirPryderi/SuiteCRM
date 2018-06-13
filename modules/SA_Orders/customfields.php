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
    $template = new Sugar_Smarty();

    $template->assign('items', $focus->get_line_items());

    if ($view === "EditView") {
        return $template->fetch('modules/SA_LineItems/templates/edit.list.view.tpl');
    }

    return $template->fetch('modules/SA_LineItems/templates/list.view.tpl');
}