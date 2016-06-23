{extends file='header.tpl'}
{block name='container'}
<script type="text/javascript">
$(document).ready(function()
{
    init_table_sorting();
    enable_select_all();
    enable_checkboxes();
    enable_row_selection();
    enable_search({ suggest_url: 'home.php?act=giftcards&f=suggest_search',
		confirm_message: "{$lang['common_confirm_search']}",
		on_complete:search_more });
    enable_delete("{$lang['giftcards_confirm_delete']}?","{$lang['common_none_selected']}");
	
	more('home.php?act=giftcards&f=more');
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
{if !empty($subgrant["giftcards_insert"])}
	<a title="{$lang['giftcards_new']}" class="modal-dlg modal-btn-submit" href="home.php?act=giftcards&f=create"><div class="btn btn-info btn-sm pull-right"><span>{$lang['giftcards_new']}</span></div></a></div>
{/if}
{/nocache}
<form class="form-horizontal" id="search_form" action="home.php?act=giftcards&f=search" method="post" accept-charset="utf-8">
	<fieldset>
		<div class="form-group" id="table_action_header">
			<ul>
{nocache}
{if !empty($subgrant["giftcards_delete"])}
				<li class="pull-left"><a id="delete" href="home.php?act=giftcards&f=delete"><div class="btn btn-default btn-sm"><span>{$lang['common_delete']}</span></div></a></li>
{/if}
{/nocache}
				<li class="pull-right">
					<input name="search" class="form-control input-sm ui-autocomplete-input" id="search" type="text" value="">
					<input id="offset" value="{nocache}{$offset}{/nocache}" data-type="more" data-val="" data-lab="" type="hidden">
				</li>
			</ul>
		</div>
	</fieldset>
</form>
<div id="table_holder">
{include file='giftcard/table.tpl'}
</div>
{/block}