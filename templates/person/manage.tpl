{extends file='header.tpl'}
{block name='container'}
<script type="text/javascript">
$(document).ready(function() { 
    init_table_sorting();
    enable_select_all();
    enable_row_selection();
	{nocache}
    enable_search( { suggest_url: "home.php?act={$controller_name}&f=suggest_search",
		confirm_search_message: "{$lang['common_confirm_search']}",
		on_complete:search_more } );
    {* not support enable_email("home.php?act={$controller_name}&f=mailto"); *}
    enable_delete("{$lang["{$controller_name}_confirm_delete"]}","{$lang["{$controller_name}_none_selected"]}");
	{/nocache}
	enable_checkboxes();
	
	more("home.php?act={nocache}{$controller_name}{/nocache}&f=more");
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
				6: { sorter: 'false'} 
			} 
		}); 
	}
}
</script>

<div id="title_bar">
	{nocache}
	{if $controller_name == 'customers'}
		{if !empty($subgrant["customers_insert"])}
		<a title="{$lang['customers_import_items_excel']}" class="modal-dlg modal-btn-submit" href="home.php?act=customers&f=excel_import">
			<div class="btn btn-info btn-sm pull-right"><span>{$lang['common_import_excel']}</span>
			</div>
		</a>		
		<a title="{$lang['customers_new']}" class="modal-dlg modal-btn-submit" href="home.php?act=customers&f=create">
			<div class="btn btn-info btn-sm pull-right" style="margin-right: 10px;"><span>{$lang['customers_new']}</span>
			</div>
		</a>
		{/if}
	{else}
		{if !empty($subgrant["{$controller_name}_insert"])}
		<a title="{$lang["{$controller_name}_new"]}" class="modal-dlg modal-btn-submit" href="home.php?act={$controller_name}&f=create">
			<div class="btn btn-info btn-sm pull-right" style="margin-right: 10px;"><span>{$lang["{$controller_name}_new"]}</span>
			</div>
		</a>
		{/if}
	{/if}
	{/nocache}

</div>

<form class="form-horizontal" id="search_form" {nocache}action="home.php?act={$controller_name}&f=search"{/nocache} method="post" accept-charset="utf-8">
	<fieldset>
		<div class="form-group" id="table_action_header">
			<ul>
				{nocache}{if  !empty($subgrant["{$controller_name}_delete"])}
				<li class="pull-left"><a id="delete" {nocache}href="home.php?act={$controller_name}&f=delete"{/nocache}><div class="btn btn-default btn-sm"><span>{$lang['common_delete']}</span></div></a></li>
				{/if }{/nocache}
				{* not support 
				<li class="pull-left"><span><a id="email" href="#"><div class="btn btn-default btn-sm">{$lang['common_email']}</div></a></span></li> 
				*}

				<li class="pull-right">
					<input name="search" class="form-control input-sm ui-autocomplete-input" placeholder="{$lang['common_search']}" id="search" type="text">
					<input id="offset" value="{nocache}{$offset}{/nocache}" data-type="more" data-val="" data-lab="" type="hidden">
				</li>
			</ul>
		</div>
	</fieldset>
</form>

<div id="table_holder">
	{nocache}{include file="{$manage_table}/table.tpl"}{/nocache}
</div>
{/block}