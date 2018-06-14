<?php
$module_name = 'SA_Orders';
$viewdefs [$module_name] =
    array(
        'EditView' =>
            array(
                'templateMeta' =>
                    array(
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
                                        'newTab' => true,
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
                                2 =>
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
                                3 =>
                                    array(
                                        0 =>
                                            array(
                                                'name' => 'order_type',
                                                'studio' => 'visible',
                                                'label' => 'LBL_ORDER_TYPE',
                                            ),
                                        array(
                                            0 =>
                                                array(
                                                    'name' => 'order_type',
                                                    'studio' => 'visible',
                                                    'label' => 'LBL_ORDER_TYPE',
                                                ),

                                        ),
                                    ),
                                4 => array(
                                    0 => array(
                                        'name' => 'overdue',
                                        'label' => 'LBL_OVERDUE',
                                    ),
                                )
                            ),
                        'LBL_LINE_ITEMS' =>
                            [
                                [
                                    0 => [
                                        'name' => 'line_items',
                                        'label' => 'LBL_LINE_ITEMS',
                                    ]
                                ],
                                [
                                    0 => [
                                        'name' => 'line_items_quick_create',
                                        'label' => 'LBL_LINE_ITEMS_QUICK_CREATE',
                                    ]
                                ],

                            ]

                    ),
            ),
    );;
