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
    $lineItems = BeanFactory::getBean('SA_LineItems');

    // Should a relationship be used here instead? Probably. Do I know how to use a relationship properly. Nope.

    $allItems = $lineItems->get_list(
        $order_by = "",
        $where = "sa_lineitems.order_id = '" . $focus->id . "'",
        $row_offset = 0,
        $limit = -1,
        $max = -1,
        $show_deleted = 0);

    $view = new Sugar_Smarty();

    $view->assign('items', $allItems['list']);

    return $view->fetch('modules/SA_LineItems/templates/list.view.tpl');
}