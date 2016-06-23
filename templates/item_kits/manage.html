{extends file='header.tpl'}
{block name='container'}
<script type="text/javascript">
$(document).ready(function()
{
    init_table_sorting();
    enable_select_all();
    enable_checkboxes();
    enable_row_selection();
    enable_search({ suggest_url: "home.php?act=item_kits&f=suggest_search",
        confirm_search_message: "{$lang['common_confirm_search']}",
		on_complete:search_more
	});
    enable_delete("{$lang["item_kits_confirm_delete"]}","{$lang["item_kits_none_selected"]}");
	
    $('#generate_barcodes').click(function() {
        var selected = get_selected_values();
        if (selected.length == 0)
        {
            alert("{$lang['items_must_select_item_for_barcode']}");
            return false;
        }
		
        $(this).attr('href','home.php?act=item_kits&f=generate_barcodes&ids='+selected.join(':'));
    });

	more('home.php?act=item_kits&f=more');
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
	{if !empty($subgrant["item_kits_insert"])}
	<a title="{$lang["item_kits_new"]}" class="modal-dlg modal-btn-submit" href="home.php?act=item_kits&f=create"><div class="btn btn-info btn-sm pull-right" style="margin-right: 10px;"><span>{$lang["item_kits_new"]}</span></div></a>
	{/if}
	</div>

<form class="form-horizontal" id="search_form" action="home.php?act=item_kits&f=search" method="post" accept-charset="utf-8">
	<fieldset>
		<div class="form-group" id="table_action_header">
			<ul>
				{if !empty($subgrant["item_kits_delete"])}
				<li class="pull-left"><a id="delete" href="home.php?act=item_kits&f=delete"><div class="btn btn-default btn-sm"><span>{$lang['common_delete']}</span></div></a></li>
				{/if}
				<li class="pull-left"><a id="generate_barcodes" href="home.php?act=items&f=generate_barcodes" target="_blank"><div class="btn btn-default btn-sm"><span>{$lang['items_generate_barcodes']}</span></div></a></li>
				<li class="pull-right">
					<input name="search" class="form-control input-sm ui-autocomplete-input" id="search" placeholder="{$lang['common_search']}" type="text" value="">
					<input id="offset" value="{nocache}{$offset}{/nocache}" data-type="more" data-val="" data-lab="" type="hidden">
				</li>
			</ul></div>
	</fieldset>
</form>
<div id="table_holder">
{include file="item_kits/table.tpl"}
</div>
{/block}