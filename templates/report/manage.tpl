{extends file='header.tpl'}
{block name="container"}
<div class="row">
<div class="col-xs-2">
<ul class="nav nav-tabs nav-stacked" id='report'>
	{nocache}
	{if !empty($subgrant['reports_categories'])}<li><a data-toggle="tab" onclick="get('category'); return false;">{$lang['reports_category']}</a></li>{/if}
    {if !empty($subgrant['reports_suppliers'])}<li><a data-toggle="tab" onclick="get('supplier'); return false;">{$lang['reports_supplier']}</a></li>{/if}
   {if !empty($subgrant['reports_payments'])}<li><a data-toggle="tab" onclick="get('payment'); return false;">{$lang['reports_payments']}</a></li>{/if}
    {if !empty($subgrant['reports_giftcard'])}<li><a data-toggle="tab" onclick="get('giftcard'); return false;">{$lang['giftcards_giftcard']}</a></li>{/if}
	{/nocache}
</ul>
</div>
<div class="col-xs-10" id="report_contain">
</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('#report :first-child>a').click();
});

function get(data) {
	$.post('home.php?act=reports', { get:data }, function(response) {
		if (response.success) {
			$('#report_contain').empty().append(response.data);
		} else {
			set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);
		}
	}, 'json');
}
</script>
{/block}