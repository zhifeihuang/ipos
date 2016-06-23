{extends file='header.tpl'}
{block name='container'}
<script type="text/javascript">
$(document).ready(function()
{
	$("a[data-toggle=popover]").popover();
    init_table_sorting();
    enable_select_all();
    enable_checkboxes();
    enable_row_selection();
    enable_search({ suggest_url: "home.php?act=items&f=suggest_search",
        confirm_search_message: "{$lang['common_confirm_search']}",
		on_complete:search_more
	});
    enable_delete("{$lang["items_confirm_delete"]}","{$lang["items_none_selected"]}");
    enable_bulk_edit("{$lang["items_none_selected"]}");
	
    $('#generate_barcodes').click(function() {
        var selected = get_selected_values();
        if (selected.length == 0)
        {
            alert("{$lang['items_must_select_item_for_barcode']}");
            return false;
        }
		
        $(this).attr('href','home.php?act=items&f=generate_barcodes&ids='+selected.join(':'));
    });
	
	more('home.php?act=items&f=more');
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
                8: { sorter: 'false'},
                9: { sorter: 'false'},
                10: { sorter: 'false'},
                11: { sorter: 'false'},
                12: { sorter: 'false'}
            }
        });
    }
}

function post_bulk_form_submit(response)
{
    if(response.success)
    {
		var row;
		for (var i = 0; i < response.ids.length; ++i) {
			row = "#" + response.ids[i];
			$(row).remove();
		}
		
		$("tbody").append(response.row);
		for (var i = 0; i < response.ids.length; ++i) {
			row = "#" + response.ids[i];
			reinit_row($(row));
			animate_row($(row));
		}
		
        set_feedback(response.msg, 'alert alert-dismissible alert-success', false);
    }
    else
    {
        set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);
    }
}
</script>

<div id="title_bar">
	{nocache}
	{if !empty($subgrant["items_insert"])}
	<a title="{$lang['items_import_items_excel']}" class="modal-dlg modal-btn-submit none" href="home.php?act=items&f=excel_import"><div class="btn btn-info btn-sm pull-right"><span>{$lang['common_import_excel']}</span></div></a>
	<a title="{$lang["items_new"]}" class="modal-dlg modal-btn-continue modal-btn-submit" href="home.php?act=items&f=create"><div class="btn btn-info btn-sm pull-right" style="margin-right: 10px;"><span>{$lang["items_new"]}</span></div></a>
	{/if}
	{/nocache}
	</div>

<form class="form-horizontal" id="search_form" action="home.php?act=items&f=search" method="post" accept-charset="utf-8">
	<fieldset>
		<div class="form-group" id="table_action_header">
			<ul>
				{nocache}
				{if !empty($subgrant["items_delete"])}
				<li class="pull-left"><a id="delete" href="home.php?act=items&f=delete"><div class="btn btn-default btn-sm"><span>{$lang['common_delete']}</span></div></a></li>
				{/if}
				{if !empty($subgrant["items_update"])}
				<li class="pull-left"><a title="{$lang["items_bulk_edit"]}" class="modal-dlg modal-btn-submit bulk_check" id="bulk_edit" href="home.php?act=items&f=bulk_edit"><div class="btn btn-default btn-sm"><span>{$lang['items_bulk_edit']}</span></div></a></li>
				{/if}
				{/nocache}
				<li class="pull-left"><a id="generate_barcodes" href="home.php?act=items&f=generate_barcodes" target="_blank"><div class="btn btn-default btn-sm"><span>{$lang['items_generate_barcodes']}</span></div></a></li>
				<li class="pull-right">
					<input name="search" class="form-control input-sm ui-autocomplete-input" id="search" placeholder="{$lang['common_search']}" type="text" value="">
					<input id="offset" value="{nocache}{$offset}{/nocache}" data-type="more" data-val="" data-lab="" type="hidden">
				</li>
			</ul></div>
	</fieldset>
</form>
<div id="table_holder">
{include file="items/table.tpl"}
</div>
{/block}