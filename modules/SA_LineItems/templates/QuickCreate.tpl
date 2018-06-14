<div style="width: 100%">
    <form id="line-item-form" action="/index.php?module=SA_Orders&action=add_line_items" method="POST">
        <input type="hidden" value="{$order_id}" name="line-item-order-id" id="line-item-order-id">

        <div class="row edit-view-row">
            <div class="col-sm-6 edit-view-row-item">
                <input type="text" placeholder="Name" name="line-item-name" id="line-item-name">
            </div>
            <div class="col-sm-6 edit-view-row-item">
                <input type="text" min="1" placeholder="Quantity" name="line-item-quantity" id="line-item-quantity">
            </div>
        </div>

        <div class="row edit-view-row">
            <div class="col-xs-12 col-sm-6 edit-view-row-item">
                <input type="text" placeholder="Price" name="line-item-price" id="line-item-price">
            </div>
            <div class="col-xs-12 col-sm-6 edit-view-row-item">
                <input type="submit" placeholder="" value="Add new line item" id="line-item-submit">
            </div>
        </div>

    </form>
</div>

<script src="modules/SA_LineItems/templates/QuickCreate.js"></script>