<ul class="error_message_box" id="error_message_box"></ul>
<form class="form-horizontal" id="pay_form" action="home.php?act=reports&f=pay" enctype="multipart/form-data" method="post" accept-charset="utf-8">
	<fieldset>
		<div class="form-group" id="table_action_header">
			<ul>
				<li class="pull-right">
					<label class="control-label sr-only" for="employee">{$lang['reports_employee']}</label>
					<input class="form-control input-sm ui-autocomplete-input" placeholder="{$lang['reports_emp_info']}" id="employee" type="text">
					<input name="employee" id="emp_id" type="hidden">
				</li>
				<li class="pull-right">
					<input name="daterangepicker" class="form-control input-sm pull-right" id="daterangepicker" type="text" value="">
					<input name="start_date" id="start_date" type="hidden" value="">
					<input name="end_date" id="end_date" type="hidden" value="">
				</li>
			</ul>
		</div>
		<div id="table_holder">
			<table class="tablesorter table table-striped table-hover" id="pay_items">
				{include file="report/payment_row.tpl"}
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
	
	$('#employee').keypress(function(e) {
		if (e.which == 13) {
			if (!$('#start_date').val() || !$('#end_date').val())
				return;
				
			$('#pay_form').trigger('submit');
		}
	});
	
	$("#employee").autocomplete({
		source: "home.php?act=employees&f=suggest_pay",
		delay:500,
		appendTo: '.modal-content',
		select: function(a, ui) {
			$(this).val(ui.item.label);
			$("#emp_id").val(ui.item.value);
			
			if ($('#start_date').val() || $('#end_date').val())
				$('#pay_form').trigger('submit');
				
			return false;
		}
	});
	
	$('#pay_form').validate({
		submitHandler: function(form) {
			$(form).ajaxSubmit({
				success: function(response)	{
					if(response.success) {
						$('#pay_items').empty().append(response.data);
						$('#employee').val('');
						$('#emp_id').val('')
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