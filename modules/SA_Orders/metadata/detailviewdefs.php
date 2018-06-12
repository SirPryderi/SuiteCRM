<?php
$module_name = 'SA_Orders';
$viewdefs [$module_name] =
    array(
        'DetailView' =>
            array(
                'templateMeta' =>
                    array(
                        'form' =>
                            array(
                                'buttons' =>
                                    array(
                                        0 => 'EDIT',
                                        1 => 'DUPLICATE',
                                        2 => 'DELETE',
                                        3 => 'FIND_DUPLICATES',
                                    ),
                            ),
                        'maxColumns' => '2',
                        'widths' =>
                            array(
                                0 =>
                                    array(
                                        'label' => '10',
                                        'field' => '30',
                                    ),
                                1 =>
                                    array(
                                        'label' => '10',
                                        'field' => '30',
                                    ),
                            ),
                        'useTabs' => false,
                        'tabDefs' =>
                            array(
                                'DEFAULT' =>
                                    array(
                                        'newTab' => false,
                                        'panelDefault' => 'expanded',
                                    ),
                            ),
                    ),
                'panels' =>
                    array(
                        'default' =>
                            array(
                                0 =>
                                    array(
                                        0 => 'name',
                                        1 => 'assigned_user_name',
                                    ),
                                1 =>
                                    array(
                                        0 => 'date_entered',
                                        1 => 'date_modified',
                                    ),
                                2 =>
                                    array(
                                        0 =>
                                            array(
                                                'name' => 'order_type',
                                                'studio' => 'visible',
                                                'label' => 'LBL_ORDER_TYPE',
                                            ),
                                        1 => '',
                                    ),
                                3 =>
                                    array(
                                        0 =>
                                            array(
                                                'name' => 'amount',
                                                'label' => 'LBL_AMOUNT',
                                            ),
                                        1 =>
                                            array(
                                                'name' => 'amount_usdollar',
                                                'label' => 'LBL_AMOUNT_USDOLLAR',
                                            ),
                                    ),
                                4 =>
                                    array(
                                        0 =>
                                            array(
                                                'name' => 'order_date',
                                                'label' => 'LBL_ORDER_DATE',
                                            ),
                                        1 =>
                                            array(
                                                'name' => 'delivery_date',
                                                'label' => 'LBL_DELIVERY_DATE',
                                            ),
                                    ),
                                5 =>
                                    array(
                                        0 =>
                                            array(
                                                'name' => 'overdue',
                                                'label' => 'LBL_OVERDUE',
                                            ),
                                    ),
                                6 =>
                                    array(
                                        0 =>
                                            array(
                                                'name' => 'line_items',
                                                'label' => 'LBL_LINE_ITEM',
                                            ),
                                    ),
                            ),
                    ),
            ),
    );;
?>
