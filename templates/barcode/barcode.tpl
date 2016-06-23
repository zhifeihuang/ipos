<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>{$lang['items_generate_barcodes']}</title>
	<link href="css/barcode_font.css" rel="stylesheet" rev="stylesheet">
</head>
<body class="font_{$barcode->get_font_name($config['barcode_font'])}" style="font-size:{$config['barcode_font_size']}px">
	<table width="{$config['barcode_page_width']}%" cellspacing="{$config['barcode_page_cellspacing']}">
		<tbody><tr>
		{nocache}
		{$cnt = 0}
		{foreach $items as $v}
			{if $cnt++ % $config['barcode_num_in_row'] == 0 && $cnt != 0}
			</tr><tr>
			{/if}
			<td>{$barcode->display_barcode($v, $config, $lang)}</td>
		{/foreach}
		{/nocache}
		</tr></tbody>
	</table>
</body>
</html>