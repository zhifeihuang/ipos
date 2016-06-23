{strip}
{nocache}
<thead>
	<tr>
		<th width="15%">{$lang['reports_supplier']}</th>
		<th width="15%">{$lang['reports_recv_quantity']}</th>
		<th width="15%">{$lang['reports_receivings']}({$config['currency_symbol']})</th>
		<th width="15%">{$lang['reports_sale_subtotal']}({$config['currency_symbol']})</th>
		<th width="15%">{$lang['reports_profit']}({$config['currency_symbol']})</th>
	</tr>
</thead>
<tbody>
	<tr>
		<td>{$lang['reports_total']}</td>
		<td class="data-empty"></td>
		<td>{$rtotal}</td>
		<td>{$total}</td>
		<td>{$ptotal}</td>
	</tr>
{foreach $items as $v}
	<tr>
		<td>{$v['supplier']}</td>
		<td>{$v['quantity']}</td>
		<td>{$v['cost']}</td>
		<td>{$v['subtotal']}</td>
		<td>{$v['profit']}</td>
	</tr>
{/foreach}
</tbody>
{/nocache}
{/strip}
<script type="text/javascript">
$(document).ready(function() {
	$('#supp_items > tbody > tr').each(function() {
		var t1 = $('td:eq(1)', $(this));
		var t2 = $('td:eq(2)', $(this));
		var t3 = $('td:eq(3)', $(this));
		var t4 = $('td:eq(4)', $(this));
		t1.text($.number(t1.text(), {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
		t2.text($.number(t2.text(), {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
		t3.text($.number(t3.text(), {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
		t4.text($.number(t4.text(), {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
	});
	
	$('#supp_items td.data-empty').each(function() {
		$(this).text('');
	});
});
</script>