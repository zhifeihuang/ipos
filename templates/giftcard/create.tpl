<ul class="error_message_box" id="create_error_message_box"></ul>
<form class="form-horizontal" id="create_form" action="home.php?act=giftcards&f=save" method="post" accept-charset="utf-8">
	<fieldset>
		<div class="form-group form-group-sm">
			<label class="required control-label col-xs-3" for="number">{$lang['giftcards_giftcard_number']}</label>			<div class="col-xs-6">
				<input name="number" class="form-control input-sm" id="number" type="text" value="">
			</div>
		</div>
		<div class="form-group form-group-sm">
			<label class="required control-label col-xs-3" for="number">{$lang['giftcards_charge']}({$config['currency_symbol']})</label>			<div class="col-xs-4">
				<input name="val" class="form-control input-sm min_0" id="create_val" type="text" value="0">
			</div>
			<div class="col-xs-2">
				<input class="form-control input-sm" type="checkbox" id='create_ch' title="{$lang['giftcards_not_val']}">
			</div>
		</div>
	</fieldset>
	<input class="btn btn-primary btn-sm pull-right" type="submit" value="{$lang['common_submit']}">
</form>
<script type="text/javascript">
$(document).ready(function() {
	$.validator.addMethod('min_0' , function(value, element) { return parseInt(value) >= 0; }, "{$lang['giftcards_min_0']}");
	$('#create_val').number(true, 0, '', "{$config['thousands_separator']}");
	$('#number').focus();
	
	$('#create_form').validate({
		submitHandler: function(form) {
			$(form).ajaxSubmit({
				success: function(response)	{
					if(response.success) {
						$('#number').val('');
						if ($('#create_ch').is(':checked') == false) $('#create_val').val(0);
						
						$('#number').focus();
						set_feedback(response.msg, 'alert alert-dismissible alert-success', false);		
					} else {
						set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);		
					}
				},
				dataType: 'json'
			});
		},
		errorClass: "has-error",
		errorLabelContainer: "#create_error_message_box",
		wrapper: "li",
		highlight: function (e)	{
			$(e).parent().addClass('has-error');
		},
		unhighlight: function (e) {
			$(e).parent().removeClass('has-error');
		},
		rules: {
			number: {
				required: true,
				remote: "home.php?act=giftcards&f=check_number"
			}
		},
		messages: {
			number: { required:"{$lang['giftcards_create_number_required']}", remote:"{$lang['common_account_number_duplicate']}" }
		}
    });
});
</script>