<?php
$module_name = 'SA_Orders';
$listViewDefs [$module_name] =
    array(
        'NAME' =>
            array(
                'width' => '30%',
                'label' => 'LBL_NAME',
                'default' => true,
                'link' => true,
            ),
        'ASSIGNED_USER_NAME' =>
            array(
                'width' => '9%',
                'label' => 'LBL_ASSIGNED_TO_NAME',
                'module' => 'Employees',
                'id' => 'ASSIGNED_USER_ID',
                'default' => true,
            ),
        'AMOUNT' =>
            array(
                'type' => 'currency',
                'label' => 'LBL_AMOUNT',
                'currency_format' => true,
                'width' => '9%',
                'default' => true,
            ),
        'DELIVERY_DATE' =>
            array(
                'type' => 'date',
                'label' => 'LBL_DELIVERY_DATE',
                'width' => '9%',
                'default' => true,
            ),
        'OVERDUE' =>
            array(
                'type' => 'checkbox',
                'label' => 'LBL_OVERDUE',
                'width' => '4%',
                'default' => true,
            ),
    );;
?>
