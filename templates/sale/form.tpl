<ul class="error_message_box" id="pay_error_message_box"></ul>
<form class="form-horizontal" id="pay_form" action="home.php?act=sales&f=pay" enctype="multipart/form-data" method="post" accept-charset="utf-8">
	<fieldset>
		<div class="form-group">
			<label class="control-label col-xs-3" for="sales_total">{$lang['sales_total']}({$config['currency_symbol']})</label>			<div class="col-xs-6">
				<input name="total" class="form-control" id="sales_total" type="text" value="{nocache}{$sale['ttotal']}{/nocache}" readonly>
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label col-xs-3" for="type">{$lang['sales_payment_title']}</label>			<div class="col-xs-6">
				<input class="form-control" id="type" type="text" value="{$lang['sales_payment_type']['cash']}" readonly>
				<input name="type" type="hidden" value="cash">
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label col-xs-3 required" for="payment">{$lang['sales_payment']}({$config['currency_symbol']})</label>			<div class="col-xs-6">
				<input name="payment" class="form-control autofocus" id="payment" type="text" value="">
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">
$(document).ready(function() {
	$.validator.addMethod('ddd' , function(value, element) {
		return parseFloat($('#payment').val()) >= parseFloat($('#sales_total').val()); 
	}, "{$lang['sales_more_than']}");
	
	$('#sales_total').number(true, {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}");
	$('#payment').number(true, {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}");
	
	$('#pay_form').validate({
		submitHandler: function(form, event) {
			$(form).ajaxSubmit({
				success: function(response) {
					if (!response.success) {
						alert(response.msg);
						return false;
					}
					
					if (response.print) {
						//window.open(response.print, '_blank', 'scrollbars=no,menubar=no,toolbar=no,status=no,titlebar=no');
						var print_win = window.open();
						print_win.document.write(response.print);
						print_win.document.close();
						print_win.focus();
						print_win.print();
						print_win.close();
					}
					
					change_dialog(response.data, "{$lang['sales_change']}", 'change_ok');
					dialog_pay.close();
				},
				data: { suspend: $('#suspend').val() },
				dataType: 'json'
			});
			
		},
		errorClass: 'has-error',
		errorLabelContainer: '#pay_error_message_box',
		wrapper: 'li',
		highlight: function (e)	{
			$(e).parent().addClass('has-error');
		},
		unhighlight: function (e) {
			$(e).parent().removeClass('has-error');
		}
	});
	
	$('#payment').rules("add", { ddd: true, messages: { ddd: "{$lang['sales_more_than']}" } } );
	
	$('.modal').on('shown.bs.modal', function() {
	  $(this).find('input.autofocus').focus();
	});
});
</script>