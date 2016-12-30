 <table class="tablesorter table table-striped table-hover" id="sortable_table">
	 <thead>
		 <tr>
			 <th><input id="select_all" type="checkbox"></th>
			 <th>{$lang['item_kits_kit']}</th>
			 <th>{$lang['item_kits_name']}</th>
			 <th>{$lang['item_kits_quantity']}</th>
			 <th>{$lang['items_cost_price']}({$config['currency_symbol']})</th>
			 <th>{$lang['items_unit_price']}({$config['currency_symbol']})</th>
			 <th>{$lang['item_kits_description']}</th>
			<th>&nbsp;</th>
		 </tr>
	 </thead>
	<tbody>
	{include file="item_kits/table_row.tpl"}
	</tbody>
</table>