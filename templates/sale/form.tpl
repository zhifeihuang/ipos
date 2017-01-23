<ul class="error_message_box" id="pay_error_message_box"></ul>
<form class="form-horizontal" id="pay_form" action="home.php?act=sales&f=pay" enctype="multipart/form-data" method="post" accept-charset="utf-8">
	<fieldset>
		<div class="form-group">
			<label class="control-label col-xs-3" for="sales_total">{$lang['sales_total']}({$config['currency_symbol']})</label>			<div class="col-xs-6">
				<input name="total" class="form-control" id="sales_total" type="text" value="{nocache}{$sale['ttotal']}{/nocache}" readonly>
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label col-xs-3 required" for="payment">{$lang['sales_payment']}({$config['currency_symbol']})</label>			<div class="col-xs-6">
				<input name="payment" class="form-control autofocus" id="payment" type="text" value="">
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label col-xs-3" for="type">{$lang['sales_payment_title']}</label>			<div class="col-xs-6">
				<select name="type" class="form-control input-sm" id='payment_type'>
				{foreach $lang['sales_payment_type'] as $k=>$val}
					<option value="{$k}" {if ($k == 'cash')}selected="selected"{/if}>{$val}</option>
				{/foreach}
				</select>
			</div>
		</div>
		
		<div class="form-group sr-only">
			<label class="control-label col-xs-3 text-danger" for="giftcard"></label>
			<div class="col-xs-6">
				<input class="form-control input-sm ui-autocomplete-input" placeholder="{$lang['sales_giftcard_search']}" id="giftcard" size="32" type="text">
			</div>
		</div>
		
		<div class="form-group sr-only">
			<label class="control-label col-xs-3 required" for="cash">{$lang['sales_payment_type']['cash']}({$config['currency_symbol']})</label>			
			<div class="col-xs-6">
				<div class="input-group input-group-sm">
					<input name="cash" class="form-control input-sm autofocus" id="cash" type="text" value="">
					<span class='input-group-addon input-sm text-danger'></span>
				</div>
			</div>
		</div>
		
		<input name='invoice' id='invoice' type='hidden'>
		<div class="form-group sr-only" id='payment_qr'>
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
	$('#cash').number(true, {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}");
	
	$('#cash').change(function() {
		$('#payment').val(parseFloat($('#payment').val()) + parseFloat($(this).val()));
	} );
	
	$('#giftcard').autocomplete({
		source: 'home.php?act=giftcards&f=suggest_sale',
		autoFocus: false,
		delay:500,
		appendTo: '.modal-content',
		response: function(e, ui) {
			if (ui.content.length == 1 && $(this).val().localeCompare(ui.content[0]['label']) == 0) {
				var data = ui.content[0]['value'].split(' ');
				if (data.length != 2) {
					ui.content.shift();
					return false;
				}
					
				$(this).val(data[0]);
				$('#invoice').val(data[0]);
				var money = parseFloat(data[1]);
				if (money >= parseFloat($('#sales_total').val())) {
					$('#payment').val($('#sales_total').val());
					$('#giftcard').parent().prev().text('');
					$('#cash').val(0);
					$('#cash').next().text('');
					$('#cash').parent().parent().parent().addClass('sr-only');
				} else {
					$('#payment').val(money);
					$('#giftcard').parent().prev().text("{$lang['sales_not_enough_money']}");
					$('#cash').val(0);
					$('#cash').next().text('>=' + (parseFloat($('#sales_total').val()) - money));
					$('#cash').parent().parent().parent().removeClass('sr-only');
				}
			}
			
			ui.content.splice(0, ui.content.length);
			return false;
		}
	});
	
	$('#payment_type').change(function() {
		$('#payment').val('');
		$('#giftcard').parent().prev().text('');
		$('#giftcard').val('');
		$('#cash').val(0);
		$('#cash').next().text('');
		$('#invoice').val('');
		$('#payment_qr').empty();
			
		var type = $(this).val();
		switch (type) {
		case 'cash':
			$('#payment').focus().prop('readonly', false);
			$('#giftcard').parent().parent().addClass('sr-only');
			$('#cash').parent().parent().parent().addClass('sr-only');
			$('#payment_qr').addClass('sr-only');
		break;
		case 'giftcard':
			$('#payment').prop('readonly', true);
			$('#giftcard').focus();
			$('#giftcard').parent().parent().removeClass('sr-only');
			$('#payment_qr').addClass('sr-only');
		break;
		case 'card':
			$('#payment').prop('readonly', true).val($('#sales_total').val());
			$('#giftcard').parent().parent().addClass('sr-only');
			$('#cash').parent().parent().parent().addClass('sr-only');
			$('#payment_qr').addClass('sr-only');
		break;
		case 'wx':
		case 'alipay':
			$('#payment').prop('readonly', true).val($('#sales_total').val());
			$('#giftcard').parent().parent().addClass('sr-only');
			$('#cash').parent().parent().parent().addClass('sr-only');
			$('#payment_qr').removeClass('sr-only');
			$.post('home.php?act=sales', { f: 'qrcode', type: type, payment: $('#sales_total').val() } , function(response) {
				if (response.success) {
					$('#payment_qr').empty().append(response.data);
				} else {
					$('#payment_qr').empty().append(response.msg);
				}
			} ,
			'json');
		break;
		}
	});
	
	$('#pay_form').validate({
		submitHandler: function(form, event) {
			$(form).ajaxSubmit({
				success: function(response) {
					if (!response.success) {
						alert(response.msg);
						return false;
					}
					
					if (response.print) {
						browser_print(response.print);
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