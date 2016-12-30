{nocache}
{foreach $gift as $v}
<tr id="{$v['giftcard_id']}">
	<td><input type="checkbox" value="{$v['giftcard_id']}"></td>
	<td>{$v['first_name']|truncate:15}</td>
	<td>{$v['last_name']|truncate:15}</td>
	<td>{$v['giftcard_number']}</td>
	<td>{currency number=$v['val'] thousands_separator=$config['thousands_separator'] decimal_point=$config['decimal_point'] decimals=$config['currency_decimals']}</td>
</tr>
{foreachelse}
<tr><td colspan="13"><div class="alert alert-dismissible alert-info">{$lang['common_no_data_display']}</div></td></tr>
{/foreach}
{/nocache}