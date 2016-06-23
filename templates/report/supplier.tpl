<ul class="error_message_box" id="error_message_box"></ul>
<form class="form-horizontal" id="supplier_form" action="home.php?act=reports&f=supp" enctype="multipart/form-data" method="post" accept-charset="utf-8">
	<fieldset>
		<div class="form-group" id="table_action_header">
			<ul>
				<li class="pull-right">
					<label class="control-label sr-only" for="supplier">{$lang['reports_supplier']}</label>
					<input class="form-control input-sm ui-autocomplete-input" placeholder="{$lang['reports_supplier_info']}" id="supplier" type="text">
					<input name="supplier" id="supplier_id" type="hidden">
				</li>
				<li class="pull-right">
					<input name="daterangepicker" class="form-control input-sm pull-right" id="daterangepicker" type="text" value="">
					<input name="start_date" id="start_date" type="hidden" value="">
					<input name="end_date" id="end_date" type="hidden" value="">
				</li>
			</ul>
		</div>
		<div id="table_holder">
			<table class="tablesorter table table-striped table-hover" id="supp_items">
				{include file="report/supplier_row.tpl"}
			</table>
		</div>
	</fieldset>
</form>
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
    });
	
	$('#supplier').keypress(function(e) {
		if (e.which == 13) {
			if (!$('#start_date').val() || !$('#end_date').val())
				return;
				
			$('#supplier_form').trigger('submit');
		}
	});
	
	$("#supplier").autocomplete({
		source: "home.php?act=suppliers&f=suggest_supplier",
		delay:500,
		appendTo: '.modal-content',
		select: function(a, ui) {
			$(this).val(ui.item.label);
			$("#supplier_id").val(ui.item.value);
			
			if ($('#start_date').val() || $('#end_date').val())
				$('#supplier_form').trigger('submit');
				
			return false;
		}
	});
	
	$('#supplier_form').validate({
		submitHandler: function(form) {
			$(form).ajaxSubmit({
				success: function(response)	{
					if(response.success) {
						$('#supp_items').empty().append(response.data);
						$('#supplier').val('');
						$('#supplier_id').val('')
						set_feedback(response.msg, 'alert alert-dismissible alert-success', false);		
					}
					else {
						set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);		
					}
				},
				dataType: 'json'
			});
		},
		errorClass: 'has-error',
		errorLabelContainer: '#error_message_box',
		wrapper: 'li',
		highlight: function (e)	{
			$(e).closest('.form-group').addClass('has-error');
		},
		unhighlight: function (e) {
			$(e).closest('.form-group').removeClass('has-error');
		},
		
		rules: {
			start_date: 'required'
		},
		messages: {
			start_date: "{$lang['reposts_start_date_required']}"
		}
	});
});
</script>