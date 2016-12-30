{strip}
{nocache}
{foreach $items as $item}
<tr>
	<td><a onclick="return delete_row(this);"><span class="glyphicon glyphicon-trash"></span></a></td>
	<td>{$item['line']}</td>
	<td>{$item['item_number']}</td>
	<td>{$item['name']}</td>
	<td><input name="item[{$item['item_id']}-{$item['line']}-{$item['is_kit']}]" class="form-control input-sm quantity" type="text" value="0"></td>
	<td>{$item['quantity']}</td>
	<td>{$item['unit_price']}</td>
	<td>0</td>
</tr>
{foreachelse}
<tr><td colspan="13"><div class="alert alert-dismissible alert-info">{$lang['common_no_data_display']}</div></td></tr>
{/foreach}
{/nocache}
{/strip}