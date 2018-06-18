<table class="list view table-responsive" style="margin-top: 40px">
    <thead>
    <tr>
        <td>Name</td>
        <td>Item ID</td>
        <td>Quantity</td>
        <td>Price</td>
    </tr>
    </thead>
    <tbody>
    {foreach from=$items item=lineItem}
    <tr>
        <td>{$lineItem->name}</td>
        <td>{$lineItem->item_id}</td>
        <td>{$lineItem->quantity}</td>
        <td>{$lineItem->price}</td>
    </tr>
    </tbody>
    {/foreach}
</table>