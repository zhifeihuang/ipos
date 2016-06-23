{nocache}
<tr>
	<td><a onclick="return delete_tr_row(this);"><span class="glyphicon glyphicon-trash"></span></a></td>
	<td>{$item['item_number']}</td>
	<td>{$item['name']}</td>
	<td>{$item['company_name']}</td>
	<td><input name="item[{$item['item_id']}]" class="form-control input-sm" id="order_item_{$item['item_id']}" type="text" value="1"></td>
</tr>
{/nocache}