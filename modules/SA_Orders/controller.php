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

    public function action_add_line_items()
    {
        // Is this the proper way of not returning any html?
        $this->view = '';
        $bean = $this->get_line_item_bean_from_post();
        if ($bean === null) {
            die('error');
        }
        $bean->save();
    }

    /**
     * @return SA_LineItems|null
     */
    private function get_line_item_bean_from_post()
    {
        /** @var SA_LineItems $bean */
        $bean = BeanFactory::newBean('SA_LineItems');

        // TODO is there a built-in validation I could use?

        $bean->name = filter_input(INPUT_POST, 'line-item-name', FILTER_SANITIZE_STRING);
        $bean->quantity = filter_input(INPUT_POST, 'line-item-quantity', FILTER_VALIDATE_INT);
        $bean->price = filter_input(INPUT_POST, 'line-item-price', FILTER_VALIDATE_FLOAT);
        $bean->order_id = filter_input(INPUT_POST, 'line-item-order-id', FILTER_SANITIZE_STRING);

        if (
            empty($bean->name) ||
            empty($bean->quantity) ||
            empty($bean->price) ||
            empty($bean->order_id)
        ) {
            return null;
        }

        return $bean;
    }
}