<?php

class LineItemsQuickCreateView extends SugarView
{
    private $order_id;

    public function __construct($order_id)
    {
        $this->init();
        $this->order_id = $order_id;
        $this->ss->assign('order_id', $order_id);
    }

    public function display()
    {
        return $this->fetchTemplate('modules/SA_LineItems/templates/QuickCreate.tpl');
    }
}