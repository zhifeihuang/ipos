{nocache}
{foreach $items as $item}
<tr>
	<td><a onclick="return delete_row(this);"><span class="glyphicon glyphicon-trash"></span></a></td>
	<td>{$item['item_number']}</td>
	<td>{$item['name']}</td>
	<td><input name="item[{$item['item_id']}]" class="form-control input-sm" id="sale_item_{$item['item_id']}" type="text" value="{$item['sale_quantity']}"></td>
	<td>{$item['unit_price']}</td>
	<td>{$item['unit_price'] * $item['sale_quantity']}</td>
	<td class="sr-only">{$item['quantity']}</td>
</tr>
{/foreach}
{/nocache}