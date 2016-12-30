{nocache}
{foreach $items as $item}
<tr>
	<td><a onclick="return delete_ret_row(this);"><span class="glyphicon glyphicon-trash"></span></a></td>
	<td>{$item['invoice_number']}</td>
	<td>{$item['item_number']}</td>
	<td>{$item['name']}</td>
	<td>{$item['company_name']}</td>
	<td>{$item['recv_quantity']}</td>
	<td><input class="form-control input-sm" type="text" name="item[{$item['recv_id']}-{$item['item_id']}-{$item['is_kit']}]" value="0" id="ret_{$item['recv_id']}-{$item['item_id']}}"></td>
	<td>{$item['cost_price']}</td>
	<td>{$item['discount']}</td>
	<td>0</td>
</tr>
{/foreach}
{/nocache}