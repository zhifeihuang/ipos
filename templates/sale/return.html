<form class="form-horizontal" id="return_form" action="home.php?act=sales&f=return" method="post" accept-charset="utf-8">
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
				<label class="control-label sr-only" for="ret_total">{$lang['sales_total']}({$config['currency_symbol']})</label>
				<input class="form-control input-sm" placeholder="{$lang['sales_total']}({$config['currency_symbol']})" id="ret_total" type="text" readonly>
			</li>
			<li class="pull-right">
				<label class="control-label sr-only" for="item_name">{$lang['sales_invoice_number']}</label>
				<input class="form-control input-sm ui-autocomplete-input" placeholder="{$lang['sales_invoice_number']}" id="item_name" size="32" type="text">
			</li>
			<li class="pull-right">
				<a id="sales_return" onclick="$('#return_form').submit();return false;"><div class="btn btn-info btn-sm"><span>{$lang['recvs_return']}</span></div></a>
			</li>
			</ul>
		</div>
		
		<div id="table_holder">
			<table class="tablesorter table table-striped table-hover" id="ret_items">
				<thead>
					<tr>
						<th width="10%">{$lang['common_delete']}</th>
						<th width="10%">{$lang['sales_line']}</th>
						<th width="15%">{$lang['items_item_number']}</th>
						<th width="15%">{$lang['items_name']}</th>
						<th width="10%">{$lang['sales_ret_quantity']}</th>
						<th width="10%">{$lang['sales_s_quantity']}</th>
						<th width="15%">{$lang['items_unit_price']}({$config['currency_symbol']})</th>
						<th width="15%">{$lang['sales_sub_total']}({$config['currency_symbol']})</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<input name="sale_id" type="hidden" id="sale_id">
	</fieldset>
</form>
<script>
$(document).ready(function() {
	$.validator.addMethod('min_1' , function(value, element) { return value > 0; }, "{$lang['recvs_min_1']}");
	$.validator.addMethod('more' , function(value, element) {
		var tr = $(element).closest('tr');
		return parseInt(value) <= parseInt($('td:eq(5)', tr).attr('value')); 
	}, "{$lang['sales_ret_q_s']}");
	
	$('#ret_total').number(true, {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}")
	
	$('body').delegate('#return_form', 'keydown', function (e) {
        if (e.ctrlKey && e.which == 13) {
			$('#return_form').trigger('submit');
        }
    });
	
	var ret = function(data) {
		$.post('home.php?act=sales&f=search', { term: data }, function(response) {
			$('#ret_form :text, :password, :file').val('');
			$('#sale_id').val(response.id);	
			$('#ret_items > tbody > tr input').each(function() {
				$(this).rules('removes');
			});
			$('#ret_items > tbody').empty().append(response.data);
			$('#ret_items > tbody > tr').each(function() {
				var input = $('td:eq(4) input', $(this)).eq(0);
				input.rules('add', { min_1:true, more:true, messages: { min_1:"{$lang['recvs_min_0']}", mire:"{$lang['sales_ret_q_s']}" } } );
				input.number(true, {$config['quantity_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}");
				input.blur(function() {
					calc_tr($(this));
					calc_total();
				});
				
				var t5 = $('td:eq(5)', $(this));
				var t6 = $('td:eq(6)', $(this));
				var t7 = $('td:eq(7)', $(this));
				t5.attr('value', t5.text()).text($.number(t5.text(), {$config['quantity_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
				t6.attr('value', t6.text()).text($.number(t6.text(), {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
				t7.attr('value', t7.text()).text($.number(t7.text(), {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
			});
		}, 'json');
		
		$('item_name').focus();
	};
	
	$('#item_name').autocomplete({
		source: 'home.php?act=sales&f=suggest',
		autoFocus: false,
		delay:500,
		appendTo: '.modal-content',
		response: function(e, ui) {
			if (ui.length == 1) {
				event.preventDefault();
				ret(ui[0].item.label);
			}
		},
		select: function(e, ui) {
			ret(ui.item.label);
			return false;
		}
	});
	
    $('#return_form').validate({
		submitHandler: function(form) {
			var tr = $('#ret_items > tbody > tr');
			if (tr.length == 0)
				return false;
						
			$(form).ajaxSubmit({
				success: function(response)	{
					if (response.success) {
						$('#return_form :text, :password, :file').val('');
						$('#ret_items > tbody > tr input').each(function() {
							$(this).rules('removes');
						});
						$('#ret_items > tbody').empty();
						set_feedback(response.msg, 'alert alert-dismissible alert-success', false);
					} else {
						set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);
					}
					
					$('item_name').focus();
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

function calc_tr(input) {
	var tr = input.closest('tr');
	var q = parseInt(input.val());
	var u = parseFloat($('td:eq(6)', tr).attr('value'));
	var t = q * u;
	$('td:eq(7)', tr).attr('value', t).text($.number(t, {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
}

function calc_total() {
	var total = 0;
	$('#ret_items > tbody > tr').each(function() {
		var t7 = $('td:eq(7)', $(this));
		total += parseFloat(t7.attr('value'));
	});
	
	$('#ret_total').val(total);
}

function delete_row(link) {
	delete_tr_row(link);
	calc_total();
	return false;
}

function delete_tr_row(link) {
	var tr = $(link).closest("tr");
	$("input", tr).rules("removes");
	tr.remove();
	return false;
}
</script>