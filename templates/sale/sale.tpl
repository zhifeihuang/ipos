<form class="form-horizontal" id="sale_form" action="home.php?act=sales&f=sale" method="post" accept-charset="utf-8">
	<fieldset>
		<div class="form-group" id="table_action_header">
			<ul>
			<li class="pull-left">
				<label class="control-label" for="print">{$lang['common_print']}</label>
			</li>
			<li class="pull-left">
				<input name="print" class="form-control input-sm" id="print" type="checkbox" {if $config['print_silently']}checked="checked"{/if} value="1">
			</li>
			<li class="pull-left">
				<label class="control-label sr-only" for="customer">{$lang['sales_customer']}</label>
				<input class="form-control input-sm ui-autocomplete-input" placeholder="{$lang['sales_customer_info']}" id="customer" size="20" type="text">
				<input name="customer" type="hidden">
			</li>
			<li class="pull-right">
				<label class="control-label sr-only" for="item_name">{$lang['sales_start_typing_item_name']}</label>
				<input class="form-control input-sm ui-autocomplete-input" placeholder="{$lang['sales_start_typing_item_name']}" id="item_name" size="32" type="text">
			</li>
			<li class="pull-right">
				<a id="sales_takings" onclick="$('#sale_form').submit();return false;"><div class="btn btn-info btn-sm"><span>{$lang['sales_takings']}</span></div></a>
			</li>
			</ul>
		</div>
		
		<div id="table_holder">
			<table class="tablesorter table table-striped table-hover" id="sale_items">
				<thead>
					<tr>
						<th width="10%">{$lang['common_delete']}</th>
						<th width="15%">{$lang['items_item_number']}</th>
						<th width="20%">{$lang['items_name']}</th>
						<th width="15%">{$lang['recvs_quantity']}</th>
						<th width="20%">{$lang['items_unit_price']}({$config['currency_symbol']})</th>
						<th width="20%">{$lang['sales_sub_total']}({$config['currency_symbol']})</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<input name="suspend" type="hidden" id="suspend" value="-1">
	</fieldset>
</form>
<script>
$(document).ready(function() {
	$.validator.addMethod('min_1' , function(value, element) { return value >= 1; }, "{$lang['recvs_min_1']}");
	
	$('#customer').autocomplete({
		source: 'home.php?act=customers&f=suggest_sale',
		autoFocus: false,
		delay:500,
		appendTo: '.modal-content',
		select: function(e, ui) {
			$(this).next().val(ui.item.value);
		}
	});
	
	$('#item_name').autocomplete({
		source: 'home.php?act=items&f=suggest_sale',
		autoFocus: false,
		delay:500,
		appendTo: '.modal-content',
		select: function(e, ui) {
			var data = JSON.parse(ui.item.value);
			if (!data)
				return false;
			
			var id = data['id'];
			var ht = data['data'];
			var qy = parseFloat(data['kg']);
			var input = '#sale_item_' + id;
			if ($(input).length == 0) {
				$('#sale_items > tbody').append(ht);
				$(input).rules("add", { min_1: true, messages: { min_1: "{$lang['recvs_min_1']}" } } );
				$(input).blur(function() {
					var tr = $(this).closest('tr');
					var t6 = $('td:eq(6)', tr);
					if (parseInt($(this).val()) > parseInt(t6.text()))
						tr.addClass('warning');
					else
						tr.removeClass('warning');
					
					calc_tr(this);
				});
				
				var tr = $(input).closest('tr');
				var t1 = $('td:eq(1)', tr);
				var t4 = $('td:eq(4)', tr);
				$(t4).attr("value", $(t4).text()).text($.number($(t4).text(), {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
				if ($(t1).text().length > {$config['kg_barcode']})
					$(input).number(true, 0, "", "{$config['thousands_separator']}");
				else
					$(input).number(true, {$config['kg_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}");
					
			} else {
				$(input).val(parseFloat($(input).val()) + qy);
			}
			
			calc_tr(input);
			$('#item_name').val('');
			$('#item_name').focus();
			return false;
		}
	});
	
	var suspend = function() {
		var tr = $('#sale_items > tbody > tr');
		if (tr.length == 0)
			return false;
			
		$.post('home.php?act=sales&f=suspend', $('#sale_form').serialize(), function(response) {
			if (response.success) {
				clear();
			} else {
				set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);
			}
		},
		'json');
	};
	
	var suspend_change = function(data) {
		$.post('home.php?act=sales&f=suspend_change&suspend=' + (parseInt($('#suspend').val()) + data), function(response) {
			if (response.success) {
				$('#suspend').val(response.id);
				$('#print').prop('checked', response.print);
				$('#customer').val(response.customer);
				$('#sale_items > tbody > tr input').each(function() {
					$(this).rules('removes');
				});
				$('#sale_items > tbody').empty().append(response.data);
				$('#item_name').focus();
			} else {
				set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);
			}
		},
		'json');
	};
	
	var calc = function() {
		$.post('home.php?act=sales&f=calc', function(response) {
			if (response.success) {
				change_dialog(response.data, "{$lang['sales_calc']}", 'calc');
			} else {
				set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);
			}
		},
		'json');
	};
	
	$('body').delegate('#sale_form', 'keydown', function (e) {
        if (e.ctrlKey) {
			if (e.which == 13) {
				$('#sale_form').trigger('submit');
			} else if (e.which == 83) {
				suspend();
			} else if (e.which == 38) {
				suspend_change(1);
			} else if (e.which == 40) {
				suspend_change(-1);
			} else if (e.which == 71) {
				clear();
			}
        } else if (e.altKey && e.which == 13) {
				calc();
		}
    });

    $('#sale_form').validate({
		submitHandler: function(form) {
			var tr = $('#sale_items > tbody > tr');
			if (tr.length == 0)
				return false;
						
			$(form).ajaxSubmit({
				success: function(response)	{
					if (!response.success) {
						alert(response.msg);
						return false;
					}
						
					dialog_pay = new BootstrapDialog({
						closable: true,
						closeByBackdrop: false,
						closeByKeyboard: false,
						title: "{$lang['sales_pay']}",
						message: (function() {
							var node =  $('<div></div>').html(response.data);
							return node;
						}),
						buttons: [{
							label: "{$lang['common_submit']}",
							cssClass: 'btn-primary',
							hotkey: 13,
							action: function(dialogRef) {
								$('form', dialogRef.$modalBody).first().submit();
							}
						}]
					});
					
					dialog_pay.open();
				},
				dataType: 'json'
			});
		},

		errorClass: 'has-error',
		errorLabelContainer: '#sale_error_message_box',
		wrapper: 'li',
		highlight: function (e)	{
			$(e).parent().addClass('has-error');
		},
		unhighlight: function (e) {
			$(e).parent().removeClass('has-error');
		}
    });
});

var dialog_pay = false;
var dialog_change = false;
function change_dialog(data, title, fun) {
	dialog_change = new BootstrapDialog({
		closable: false,
		title: title,
		message: (function() {
			var node =  $('<div></div>').html(data);
			return node;
		}),
		buttons: [{
			label: "{$lang['common_submit']}",
			cssClass: 'btn-primary',
			hotkey: 13,
			action: function(dialogRef) {
				if ('change_ok' == fun) {
					change_ok();
				} else if ('calc' == fun) {
					dialogRef.close();
					$('#item_name').focus();
				}
			}
		}]
	});
	
	dialog_change.setClosable(false);
	dialog_change.open();
}

function clear() {
	$('#sale_form :text, :password, :file').val('');
	$('#sale_items > tbody > tr input').each(function() {
		$(this).rules('removes');
	});
	$('#sale_items > tbody').empty();
	$('#suspend').val(-1);
	$('#item_name').focus();
}

function change_ok() {
	dialog_change.close();
	clear();
}

function calc_tr(input) {
	var tr = $(input).closest('tr');
	var q = parseFloat($(input).val());
	var u = parseFloat($('td:eq(4)', tr).attr('value'));
	var t = q * u;
	$('td:eq(5)', tr).attr('value', t).text($.number(t, {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
}

function calc_total() {
	var total = 0;
	$("#sale_items > tbody > tr").each(function() {
		var t5 = $("td:eq(5)", $(this));
		total += parseFloat(t5.attr("value"));
	});
	
	$("#sale_total").val(total);
}

function delete_row(link) {
	delete_tr_row(link);
	return false;
}

function delete_tr_row(link) {
	var tr = $(link).closest("tr");
	$("input", tr).rules("removes");
	tr.remove();
	return false;
}
</script>