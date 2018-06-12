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

    // TODO implement proper view

    return json_encode($allItems['list']);
}