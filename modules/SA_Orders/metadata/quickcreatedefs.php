<?php
$module_name = 'SA_Orders';
$viewdefs [$module_name] =
    array(
        'QuickCreate' =>
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
                                        1 => array(
                                            'name' => 'overdue',
                                            'label' => 'LBL_OVERDUE',
                                        ),
                                    ),
                            ),
                    ),
            ),
    );;
?>
