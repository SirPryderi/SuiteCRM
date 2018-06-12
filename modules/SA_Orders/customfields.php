<?php
function line_items_function($focus, $field, $value, $view)
{
    $bean = BeanFactory::getBean('SA_LineItems');

    $allItems = $bean->get_list(
        $order_by = "",
        $where = "",
        $row_offset = 0,
        $limit = -1,
        $max = -1,
        $show_deleted = 0);

    $view = new Sugar_Smarty();

    $view->assign('items', $allItems['list']);

    return $view->fetch('modules/SA_LineItems/templates/list.view.tpl');
}