<div class="form-group" id="table_action_header">
	<ul>
		<li class="pull-right">
			<input name="daterangepicker" class="form-control input-sm pull-right" id="daterangepicker" type="text" value="">
			<input name="start_date" id="start_date" type="hidden" value="">
			<input name="end_date" id="end_date" type="hidden" value="">
		</li>
	</ul>
</div>
<div id="table_holder">
	<table class="tablesorter table table-striped table-hover" id="cate_items">
		<thead>
			<tr>
				<th width="15%">{$lang['reports_category']}</th>
				<th width="15%">{$lang['reports_quantity']}</th>
				<th width="10%">{$lang['reports_subtotal']}({$config['currency_symbol']})</th>
				<th width="15%">{$lang['reports_profit']}({$config['currency_symbol']})</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<script type="text/javascript">
$(document).ready(function() {
	// load the preset daterange picker
	{include file='partial/daterangepicker.tpl'}
	
	// set the beginning of time as starting date
	$('#daterangepicker').data('daterangepicker').setStartDate("{$config['company_start']|date_format:"{$config['dateformat']}":'':'date'}");
	start_date = "{$config['company_start']|date_format:'Y-m-d':'':'date'}";

	// set default dates in the hidden inputs
	$('#start_date').val(start_date);
	$('#end_date').val(end_date);

	// update the hidden inputs with the selected dates before submitting the search data
	$('#daterangepicker').on('apply.daterangepicker', function(ev, picker) {
		$('#start_date').val(start_date);
		$('#end_date').val(end_date);
		$.post('home.php?act=reports&f=category', { start_date:start_date, end_date:end_date }, function(response) {
			if (response.success) {
				$('#cate_items > tbody').empty().append(response.data);
				format();
			} else {
				set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);
			}
		}, 'json');
    });
	
	var format = function() {
		$('#cate_items > tbody > tr').each(function() {
			var t1 = $('td:eq(1)', $(this));
			var t2 = $('td:eq(2)', $(this));
			var t3 = $('td:eq(3)', $(this));
			t1.text($.number(t1.text(), {$config['kg_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
			t2.text($.number(t2.text(), {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
			t3.text($.number(t3.text(), {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
		});
	};
	
	format();
});
</script>