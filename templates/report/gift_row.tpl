{strip}
{nocache}
<thead>
	<tr>
		<th width="10%">{$lang['common_date']}</th>
		<th width="10%">{$lang['giftcards_giftcard_number']}</th>
		<th width="10%">{$lang['giftcards_charge']}({$config['currency_symbol']})</th>
	</tr>
</thead>
<tbody>
	<tr>
		<td>{$lang['reports_total']}</td>
		<td class="data-empty"></td>
		<td>{$total}</td>
	</tr>
{foreach $items as $v}
	<tr>
		<td>{$v['record_time']}</td>
		<td>{$v['giftcard_number']}</td>
		<td>{$v['val']}</td>
	</tr>
{/foreach}
</tbody>
{/nocache}
{/strip}
<script type="text/javascript">
$(document).ready(function() {
	$('#giftcard_items > tbody > tr').each(function() {
		var t2 = $('td:eq(2)', $(this));
		t2.text($.number(t2.text(), {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
	});
});
</script>