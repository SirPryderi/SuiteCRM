<?php

require_once 'modules/SA_LineItems/views/LineItemsViewList.php';
require_once 'modules/SA_LineItems/views/LineItemsQuickCreateView.php';

/**
 * @param $focus SA_Orders
 * @param $field string
 * @param $value string
 * @param $view string
 * @return string
 */
function line_items_function($focus, $field, $value, $view)
{
    if ($view === "EditView") {
        return line_items_edit_function($focus);
    }

    $template = new Sugar_Smarty();

    $template->assign('items', $focus->get_line_items());

    return $template->fetch('modules/SA_LineItems/templates/list.view.tpl');
}

/**
 * @param $focus SA_Orders current object
 * @param $field string
 * @param $value string
 * @param $view string the view it is called from
 * @return string
 */
function line_items_quick_create_function($focus, $field, $value, $view)
{
    $template = new LineItemsQuickCreateView($focus->id);
    return $template->display();
}

/**
 * @param $focus SA_Orders
 * @return string html view
 */
function line_items_edit_function($focus)
{
    $listView = new LineItemsViewList($focus->id);
    return $listView->get_html();
}