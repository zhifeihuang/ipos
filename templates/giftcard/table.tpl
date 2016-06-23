<table class="tablesorter table table-striped table-hover" id="sortable_table">
	<thead>
		<tr>
			<th width="8%"><input id="select_all" type="checkbox"></th>
			<th width="15%">{$lang['common_last_name']}</th>
			<th width="15%">{$lang['common_first_name']}</th>
			<th width="15%">{$lang['giftcards_giftcard_number']}</th>
			<th width="20%">{$lang['giftcards_card_value']}({$config['currency_symbol']})</th>
		</tr>
	</thead>
	<tbody>
	{include file='giftcard/table_row.tpl'}
	</tbody>
</table>