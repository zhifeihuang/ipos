{nocache}
{foreach $gift as $v}
<tr id="charge_{$v['giftcard_id']}">
	<td><a onclick="return delete_row(this);"><span class='glyphicon glyphicon-trash'></span></a></td>
	<td>{$v['first_name']|truncate:15}</td>
	<td>{$v['last_name']|truncate:15}</td>
	<td>{$v['giftcard_number']}</td>
	<td>{currency number=$v['val'] thousands_separator=$config['thousands_separator'] decimal_point=$config['decimal_point'] decimals=$config['currency_decimals']}</td>
	<td><input name="giftcard[{$v['giftcard_id']}]" class="form-control input-sm" id="charge_item_{$v['giftcard_id']}" type="text" value=""><input name="person[{$v['giftcard_id']}]" value="{$v['person_id']}" type='hidden'></td>
</tr>
{foreachelse}
<tr><td colspan="13"><div class="alert alert-dismissible alert-info">{$lang['common_no_data_display']}</div></td></tr>
{/foreach}
{/nocache}