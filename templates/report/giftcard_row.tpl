{strip}
{nocache}
<thead>
	<tr>
		<th width="15%">{$lang['reports_emp_id']}</th>
		<th width="15%">{$lang['reports_employee']}</th>
		<th width="15%">{$lang['reports_sale_subtotal']}({$config['currency_symbol']})</th>
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
		<td>{$v['emp_id']}</td>
		<td>{$v['employee']}</td>
		<td>{$v['subtotal']}</td>
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