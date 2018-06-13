<?php
/**
 * Created by PhpStorm.
 * User: viocolano
 * Date: 13/06/18
 * Time: 13:24
 */

class LineItemsViewList extends ViewList
{

    private $order_id;

    public function __construct($order_id)
    {
        $bean = BeanFactory::getBean('SA_LineItems');

        // How much of a bad practice this is remains a mystery.
        $GLOBALS['module'] = $bean->module_dir;
        $this->order_id = $order_id;

        parent::__construct();

        $this->headers = false;

        $this->init($bean);

        $this->preDisplay();
    }

    public function listViewProcess()
    {
        $this->lv->setup($this->seed, 'include/ListView/ListViewGeneric.tpl', $this->seed->table_name . ".order_id='$this->order_id'", $this->params);

        echo $this->lv->display();
    }

    public function get_html()
    {
        ob_start();
        $this->display();
        $return = ob_get_contents();
        ob_end_clean();

        return $return;
    }
}