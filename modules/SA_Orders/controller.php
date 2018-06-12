<?php

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class SA_OrdersController extends SugarController
{
    public function pre_save()
    {
        parent::pre_save();

        $this->check_overdue();
    }

    private function check_overdue()
    {
        $delivery_date = new DateTime($this->bean->delivery_date);
        $now = new DateTime();

        $this->bean->overdue = $delivery_date < $now;
    }
}