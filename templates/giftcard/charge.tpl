<ul class="error_message_box" id="charge_error_message_box"></ul>
<form class="form-horizontal" id="charge_form" action="home.php?act=giftcards&f=update" method="post" accept-charset="utf-8">
	<fieldset>
		<div class="form-group" id='table_action_header'>
			<ul>
				<li class="pull-right">
					<input class="form-control input-sm ui-autocomplete-input" placeholder="{$lang['giftcards_number_required']}" id="charge" type="text" size='32' value="">
				</li>
			</ul>
		</div>
		<div id='table_holder'>
			<table class="tablesorter table table-striped table-hover" id='charge_table'>
				<thead>
					<tr>
						<th width="10%">{$lang['common_delete']}</th>
						<th width="15%">{$lang['common_last_name']}</th>
						<th width="15%">{$lang['common_first_name']}</th>
						<th width="15%">{$lang['giftcards_giftcard_number']}</th>
						<th width="20%">{$lang['giftcards_card_value']}({$config['currency_symbol']})</th>
						<th width="20%">{$lang['giftcards_charge']}({$config['currency_symbol']})</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</fieldset>
	<input class="btn btn-primary btn-sm pull-right" type="submit" value="{$lang['common_submit']}">
</form>
<script type="text/javascript">
$(document).ready(function() {
	$.validator.addMethod('min_1' , function(value, element) { return parseInt(value) >= 1; }, "{$lang['giftcards_min_1']}");
	$('#charge').focus();
	
	$('#charge').autocomplete({
		source: 'home.php?act=giftcards&f=suggest_charge',
		autoFocus: false,
		delay:500,
		appendTo: '.modal-content',
		response: function(e, ui) {
			if (ui.content.length == 1 && $(this).val().localeCompare(ui.content[0]['label']) == 0) {
				var data = JSON.parse(ui.content[0]['value']);
				if (!data) {
					ui.content.splice(0, ui.content.length);
					return false;
				}
				
				var input = '#charge_item_' + data['id'];
				if ($(input).length == 0) {
					$('#charge_table > tbody').append(data['rows']);
					$(input).rules("add", { min_1: true, messages: { min_1: "{$lang['giftcards_min_1']}" } } );
					$(input).number(true, 0, '', "{$config['thousands_separator']}");
					$(input).focus();
				}
				
				$(this).val('');
			}
			
			ui.content.splice(0, ui.content.length);
			return false;
		}
	});
	
	 $('#charge_form').validate({
		submitHandler: function(form) {
			var tr = $("#charge_table > tbody > tr");
			if (tr.length == 0)
				return false;
						
			$(form).ajaxSubmit({
				success: function(response)	{
					if(response.success) {
						$('#charge_table > tbody').empty();
						$('#charge').focus();
						set_feedback(response.msg, 'alert alert-dismissible alert-success', false);		
					} else {
						set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);		
					}
				},
				dataType: 'json'
			});
		},

		errorClass: "has-error",
		errorLabelContainer: "#charge_error_message_box",
		wrapper: "li",
		highlight: function (e)	{
			$(e).parent().addClass('has-error');
		},
		unhighlight: function (e) {
			$(e).parent().removeClass('has-error');
		},
		rules: {
		},
		messages: {
		}
    });
});
</script>