<script type="text/javascript">
$(document).ready(function() 
{
    init_table_sorting();
    enable_select_all();
    enable_row_selection();
    enable_delete("{$lang["config_confirm_delete"]}","{$lang["config_none_selected"]}");
	enable_checkboxes();
});

function init_table_sorting()
{
	//Only init if there is more than one row
	if($('.tablesorter tbody tr').length >1)
	{
		$("#sortable_table").tablesorter(
		{ 
			sortList: [[1,0]], 
			headers: 
			{ 
				0: { sorter: 'false'},
				2: { sorter: 'false'},
				3: { sorter: 'false'}
			} 
		}); 
	}
}
</script>

<div id="table_action_header">
	<ul>
		<li class="pull-left">
			<a id="delete" href="home.php?act=config&f=delete_role">
				<div class="btn btn-default btn-sm"><span>{$lang['common_delete']}</span></div>
			</a>
		</li>
		<li class="pull-right">
			<a title="{$lang['common_new']}" class="modal-dlg modal-btn-submit" href="home.php?act=config&f=create_role">
				<div class="btn btn-info btn-sm pull-right" style="margin-right: 10px;"><span>{$lang['common_new']}</span></div>
			</a>
		</li>
	</ul>
</div>

<div id="table_holder">
	<table class="tablesorter table table-striped table-hover" id="sortable_table">
		<thead>
			<tr>
				<th><input type="checkbox" id="select_all" /></th>
				<th>{$lang['config_role']}</th>
				<th>{$lang['config_grant']}</th>
				<th> &nbsp; </th>
			</tr>
		</thead>
		<tbody>
		{include file='role/table_row.tpl'}
		</tbody>
	</table>
</div>