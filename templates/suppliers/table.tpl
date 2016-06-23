<table class="tablesorter table table-striped table-hover" id="sortable_table">
	<thead>
		<tr>
			<th><input type="checkbox" id="select_all" /></th>
			<th>{$lang['suppliers_company_name']}</th>
			<th>{$lang['suppliers_agency_name']}</th>
			<th>{$lang['common_last_name']}</th>
			<th>{$lang['common_first_name']}</th>
			<th>{$lang['common_email']}</th>
			<th>{$lang['common_phone_number']}</th>
			<th>{$lang['suppliers_supplier_id']}</th>
			<th> &nbsp; </th>
		</tr>
	</thead>
	<tbody>
		{include file="suppliers/table_row.tpl"}
	</tbody>
</table>