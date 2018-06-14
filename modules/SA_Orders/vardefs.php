<?php
/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2017 SalesAgility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for  technical reasons, the Appropriate Legal Notices must
 * display the words  "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 */

$dictionary['SA_Orders'] = array(
    'table' => 'sa_orders',
    'audited' => true,
    'inline_edit' => true,
    'duplicate_merge' => true,
    'fields' => array(
        'amount' =>
            array(
                'required' => true,
                'name' => 'amount',
                'vname' => 'LBL_AMOUNT',
                'type' => 'currency',
                'massupdate' => 0,
                'no_default' => false,
                'comments' => '',
                'help' => '',
                'importable' => 'true',
                'duplicate_merge' => 'disabled',
                'duplicate_merge_dom_value' => '0',
                'audited' => false,
                'inline_edit' => true,
                'reportable' => true,
                'unified_search' => false,
                'merge_filter' => 'disabled',
                'len' => 26,
                'size' => '20',
                'enable_range_search' => false,
                'precision' => 6,
            ),
        'currency_id' =>
            array(
                'required' => false,
                'name' => 'currency_id',
                'vname' => 'LBL_CURRENCY',
                'type' => 'currency_id',
                'massupdate' => 0,
                'no_default' => false,
                'comments' => '',
                'help' => '',
                'importable' => 'true',
                'duplicate_merge' => 'disabled',
                'duplicate_merge_dom_value' => 0,
                'audited' => false,
                'inline_edit' => true,
                'reportable' => true,
                'unified_search' => false,
                'merge_filter' => 'disabled',
                'len' => 36,
                'size' => '20',
                'dbType' => 'id',
                'studio' => 'visible',
                'function' =>
                    array(
                        'name' => 'getCurrencyDropDown',
                        'returns' => 'html',
                    ),
            ),
        'amount_usdollar' =>
            array(
                'required' => true,
                'name' => 'amount_usdollar',
                'vname' => 'LBL_AMOUNT_USDOLLAR',
                'type' => 'currency',
                'massupdate' => 0,
                'no_default' => false,
                'comments' => '',
                'help' => '',
                'importable' => 'true',
                'duplicate_merge' => 'disabled',
                'duplicate_merge_dom_value' => '0',
                'audited' => false,
                'inline_edit' => true,
                'reportable' => true,
                'unified_search' => false,
                'merge_filter' => 'disabled',
                'len' => 26,
                'size' => '20',
                'enable_range_search' => false,
                'precision' => 6,
            ),
        'order_type' =>
            array(
                'required' => true,
                'name' => 'order_type',
                'vname' => 'LBL_ORDER_TYPE',
                'type' => 'enum',
                'massupdate' => 0,
                'no_default' => false,
                'comments' => '',
                'help' => '',
                'importable' => 'true',
                'duplicate_merge' => 'disabled',
                'duplicate_merge_dom_value' => '0',
                'audited' => false,
                'inline_edit' => true,
                'reportable' => true,
                'unified_search' => false,
                'merge_filter' => 'disabled',
                'len' => 100,
                'size' => '20',
                'options' => 'order_type_list',
                'studio' => 'visible',
                'dependency' => false,
            ),
        'order_date' =>
            array(
                'required' => true,
                'name' => 'order_date',
                'vname' => 'LBL_ORDER_DATE',
                'type' => 'date',
                'massupdate' => 0,
                'no_default' => false,
                'comments' => '',
                'help' => '',
                'importable' => 'true',
                'duplicate_merge' => 'disabled',
                'duplicate_merge_dom_value' => '0',
                'audited' => false,
                'inline_edit' => true,
                'reportable' => true,
                'unified_search' => false,
                'merge_filter' => 'disabled',
                'size' => '20',
                'enable_range_search' => false,
                'display_default' => 'now',
            ),
        'delivery_date' =>
            array(
                'required' => true,
                'name' => 'delivery_date',
                'vname' => 'LBL_DELIVERY_DATE',
                'type' => 'date',
                'massupdate' => 0,
                'no_default' => false,
                'comments' => '',
                'help' => '',
                'importable' => 'true',
                'duplicate_merge' => 'disabled',
                'duplicate_merge_dom_value' => '0',
                'audited' => true,
                'inline_edit' => true,
                'reportable' => true,
                'unified_search' => false,
                'merge_filter' => 'disabled',
                'size' => '20',
                'enable_range_search' => false,
                'display_default' => '+5 day',
            ),
        'overdue' =>
            array(
                'name' => 'overdue',
                'vname' => 'LBL_OVERDUE',
                'type' => 'bool',
                'massupdate' => 0,
                'no_default' => false,
                'comments' => '',
                'help' => '',
                'importable' => 'true',
                'duplicate_merge' => 'disabled',
                'duplicate_merge_dom_value' => '0',
                'audited' => false,
                'inline_edit' => true,
                'reportable' => true,
                'unified_search' => false,
                'merge_filter' => 'disabled',
                'studio' => 'visible',
                'dependency' => false,
            ),
        'line_items' =>
            array(
                'name' => 'line_items',
                'vname' => 'LBL_LINE_ITEMS',
                'type' => 'function',
                'inline_edit' => false,
                'source' => 'non-db',
                'studio' => 'visible',
                'function' =>
                    array(
                        'name' => 'line_items_function',
                        'returns' => 'html',
                        'include' => 'modules/SA_Orders/customfields.php',
                    ),
            ),
        'line_items_quick_create' =>
            array(
                'name' => 'line_items_quick_create',
                'vname' => 'LBL_LINE_ITEMS_QUICK_CREATE',
                'type' => 'function',
                'inline_edit' => false,
                'source' => 'non-db',
                'studio' => 'visible',
                'function' =>
                    array(
                        'name' => 'line_items_quick_create_function',
                        'returns' => 'html',
                        'include' => 'modules/SA_Orders/customfields.php',
                    ),
            ),
        'sa_orders_line_items' =>
            array(
                'name' => 'order_items',
                'vname' => 'LBL_LINE_ITEMS',
                'type' => 'relate',
                'inline_edit' => false,
                'source' => 'non-db',
            ),
    ),
    'relationships' => array(
        'sa_orders_line_items' => array(
            'lhs_module' => 'SA_Orders',
            'lhs_table' => 'sa_orders',
            'lhs_key' => 'id',

            'rhs_module' => 'SA_LineItems',
            'rhs_table' => 'sa_lineitems',
            'rhs_key' => 'order_id',

            'relationship_type' => 'one-to-many'
        )
    ),
    'optimistic_locking' => true,
    'unified_search' => true,
);
if (!class_exists('VardefManager')) {
    require_once('include/SugarObjects/VardefManager.php');
}
VardefManager::createVardef('SA_Orders', 'SA_Orders', array('basic', 'assignable', 'security_groups'));