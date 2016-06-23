{extends file='header.tpl'}
{block name="container"}
<div class="row">
<div class="col-xs-2">
<ul class="nav nav-tabs nav-stacked" data-tabs="tabs">
    <li class="active" role="presentation">
        <a data-toggle="tab" onclick="report('category'); return false;">{$lang['reports_category']}</a>
    </li>
    <li role="presentation">
        <a data-toggle="tab" onclick="report('supplier'); return false;">{$lang['reports_supplier']}</a>
    </li>
    <li role="presentation">
        <a data-toggle="tab" onclick="report('payment'); return false;">{$lang['reports_payments']}</a>
    </li>
</ul>
</div>
<div class="col-xs-10" id="report_contain">
{include file='report/category.tpl'}
</div>
</div>
<script>
function report(data) {
	$.post('home.php?act=reports', { f:data }, function(response) {
		if (response.success) {
			$('#report_contain').empty().append(response.data);
		} else {
			set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);
		}
	}, 'json');
}
</script>
{/block}