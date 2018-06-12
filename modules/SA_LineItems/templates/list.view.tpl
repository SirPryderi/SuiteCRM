<table class="list view table-responsive">
    <thead>
    <td>Item ID</td>
    <td>Quantity</td>
    <td>Price</td>
    </thead>
    <tbody>
    {foreach from=$items item=lineItem}
    <tr>
        <td>{$lineItem->item_id}</td>
        <td>{$lineItem->quantity}</td>
        <td>{$lineItem->price}</td>
    </tr>
    </tbody>
    {/foreach}
</table>