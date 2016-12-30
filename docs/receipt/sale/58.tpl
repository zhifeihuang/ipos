<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
	<style type='text/css'>
		body { font-family: Monaco, monospace; font-size:12px; text-align:left; color:#000; margin:0; padding:0; }
		.right { text-align:right; }
		.left { text-align:left; }
	    table { page-break-inside:auto }
	    tr    { page-break-inside:avoid; page-break-after:auto }
	    thead { display:table-header-group }
	    tfoot { display:table-footer-group }
		th, td { margin:0; padding:0; }
		.company {  font-weight:bold; text-align:center; font-size:15px; margin:0; padding:0;  }
		.letter { letter-spacing:-2px; }
		.page {
			width: 58mm;
			padding: 0;
			border: 1px #D3D3D3 solid;
			background: white;
		}
		@page {
		size: 58mm 1000mm;
		margin: 0;
		}
		@media print {
			.page {
				margin: 0;
				border: initial;
				border-radius: initial;
				width: initial;
				min-height: initial;
				box-shadow: initial;
				background: initial;
				page-break-after: always;
			}
		}
	</style>
</head>
<body>
<div class='page'>
	<div class='company'>{$config['company']}</div>
	<div class='left'>{nocache}{$data['invoice_number']}{/nocache}</div>
	<table>
		<thead>
			<tr>
				<th class='left' width='30%'>{$lang['print_name']}</th>
				<th class='right'  width='15%'>{$lang['print_quantity']}</th>
				<th class='left' width='20%'>{$lang['print_price']}({$config['currency_symbol']})</th>
				<th class='left' width='20%'>{$lang['print_subtotal']}({$config['currency_symbol']})</th>	
				<th class='center' width='15%'></th>
			</tr>
		</thead>
		<tbody>
		{nocache}
		{foreach $items as $item}
			<tr><td class='left letter'>{$item['name']|truncate:6:'*':false:true}</td><td class='right letter'>{if !$item['is_kg']}{quantity number=$item['quantity']}{else}{kg_decimals number=$item['quantity']  thousands_separator=$config['thousands_separator'] decimal_point=$config['decimal_point'] decimals=$config['kg_decimals']}{/if}</td><td class='left letter'>{currency number=$item['unit_price'] thousands_separator=$config['thousands_separator'] decimal_point=$config['decimal_point'] decimals=$config['currency_decimals']}</td><td class='left letter'>{currency number=$item['unit_price'] * $item['quantity']  thousands_separator=$config['thousands_separator'] decimal_point=$config['decimal_point'] decimals=$config['currency_decimals']}</td></tr>
			{/foreach}
			{/nocache}
		</tbody>
	</table>
	<div><label class='left'>{$lang['print_quantity']}:{nocache}{quantity number=$sum}{/nocache}</label>    <label class='right'>{$lang['sales_total']}:{nocache}{currency number=$total thousands_separator=$config['thousands_separator'] decimal_point=$config['decimal_point'] decimals=$config['currency_decimals']}{/nocache}</label></div>
	<div><label class='left'>{$lang['sales_payment_type']['cash']}:{nocache}{currency number=$payment thousands_separator=$config['thousands_separator'] decimal_point=$config['decimal_point'] decimals=$config['currency_decimals']}{/nocache}</label>    <label class='right'>{$lang['sales_change']}:{nocache}{currency number=($payment - $total) thousands_separator=$config['thousands_separator'] decimal_point=$config['decimal_point'] decimals=$config['currency_decimals']}{/nocache}</label></div>
	<div><p class='center'>{$config['print_footer']}<p></div>
	<div style="margin:30px 0px 0px 175px;">.</div>
</div>
</body>
</html>