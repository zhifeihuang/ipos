{nocache}
{foreach $gift as $v}
<tr id="{$v['giftcard_id']}">
	<td>{$v['first_name']|truncate:15}</td>
	<td>{$v['last_name']|truncate:15}</td>
	<td>{$v['giftcard_number']}</td>
	<td>{currency number=$v['val'] thousands_separator=$config['thousands_separator'] decimal_point=$config['decimal_point'] decimals=$config['currency_decimals']}</td>
	<td>{if $v['deleted'] == 0}<a title="{$lang['giftcards_delete_title']}" {if !empty($subgrant['giftcards_delete'])} data-func='+' onclick='update(this); return false;' {/if}>{$lang['giftcards_active']}</a>{else}<a title="{$lang['giftcards_active_title']}" {if !empty($subgrant['giftcards_delete'])} data-func='-' onclick='update(this); return false;' {/if}>{$lang['giftcards_delete']}</a>{/if}</td>
</tr>
{foreachelse}
<tr><td colspan="13"><div class="alert alert-dismissible alert-info">{$lang['common_no_data_display']}</div></td></tr>
{/foreach}
{/nocache}