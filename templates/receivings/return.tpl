<ul class="error_message_box" id="ret_error_message_box"></ul>

<form class="form-horizontal" id="ret_form" action="home.php?act=receivings&f=return" method="post" accept-charset="utf-8">
	<fieldset>
		<div class="form-group" id="table_action_header">
			<ul>
				<li class="pull-left">
					<input class="form-control input-sm" placeholder="{$lang['recvs_total']}({$config['currency_symbol']})" id="ret_total" type="text" readonly>
				</li>
				<li class="pull-right">
					<input class="form-control input-sm ui-autocomplete-input" placeholder="{$lang['sales_start_typing_item_name']}" id="ret_find_number" size="32" type="text">
				</li>
			</ul>
		</div>
		
		<div id="table_holder">
			<table class="tablesorter table table-striped table-hover" id="ret_items">
				<thead>
					<tr>
						<th width="8%">{$lang['common_delete']}</th>
						<th width="10%">{$lang['recvs_order_number']}</th>
						<th width="10%">{$lang['items_item_number']}</th>
						<th width="12%">{$lang['items_name']}</th>
						<th width="10%">{$lang['common_company_name']}</th>
						<th width="10%">{$lang['recvs_recv_quantity']}</th>
						<th width="10%">{$lang['recvs_quantity']}</th>
						<th width="10%">{$lang['recvs_cost_price']}({$config['currency_symbol']})</th>
						<th width="10%">{$lang['recvs_discount']}</th>
						<th width="10%">{$lang['recvs_total']}({$config['currency_symbol']})</th>
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
	$.validator.addMethod('min_1' , function(value, element) { return value >= 1; }, "{$lang['recvs_min_1']}");
	$.validator.addMethod('less' , function(value, element) {
		var tr = $(element).closest('tr');
		var t5 = $("td:eq(5)", tr);
		return parseInt(value) <= parseInt(t5.text());
	}, "{$lang['recvs_ret_less']}");
	$("#ret_total").number(true, {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}");
	
	$("#ret_find_number").autocomplete({
		source: 'home.php?act=items&f=suggest_return',
		autoFocus: false,
		delay:500,
		appendTo: ".modal-content",
		select: function(e, ui) {
			$.post('home.php?act=receivings&f=item', { id: ui.item.value }, function(response) {
				if (!response.success) {
					set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);
					return;
				}
				
				var ids = response.ids;
				if (!ids || ids.length == 0)
					return;
				
				for (var i = 0; i < ids.length; ++i) {
					if ($("#ret_" + ids[i]).length !== 0)
						return;
				}
				
				$("#ret_items > tbody").append(response.row);
				for (var i = 0; i < ids.length; ++i) {
					var input = "#ret_"  + ids[i];
					$(input).rules("add", { min_1: true, less: true, messages: { min_1: "{$lang['recvs_min_1']}", less: "{$lang['recvs_ret_less']}" } });
					$(input).blur(function() {
						var tr = $(this).closest("tr");
						var q = parseInt($(this).val());
						var c = parseFloat($("td:eq(7)", tr).attr("value"));
						var d = parseFloat($("td:eq(8)", tr).attr("value"));
						var t = q * c * (100 - d) / 100;
						$("td:eq(9)", tr).attr("value", t).text($.number(t, {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
						
						calc_ret_total();
					});
					
					var tr = $(input).closest("tr");
					var t5 = $("td:eq(5)", tr);
					var t7 = $("td:eq(7)", tr);
					var t8 = $("td:eq(8)", tr);
					var t9 = $("td:eq(9)", tr);
					t5.attr("value", t5.text()).text($.number(t5.text(), 0, "", "{$config['thousands_separator']}"));
					t7.attr("value", t7.text()).text($.number(t7.text(), {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
					t8.attr("value", t8.text()).text($.number(t8.text(), {$config['tax_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
					t9.attr("value", t9.text()).text($.number(t9.text(), {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
					
					$(input).number(true, 0, "", "{$config['thousands_separator']}");
				}
			},
			'json');
			$("#ret_find_number").val("");
			return false;
		}
	});

    $('#ret_form').validate({
		submitHandler: function(form) {
			var tr = $("#ret_items > tbody > tr");
			if (tr.length == 0)
				return false;
						
			$(form).ajaxSubmit({
				success: function(response)	{
					if(response.success) {
						$("#ret_total").val('');
						$('#ret_items > tbody > tr input').each(function() {
							$(this).rules('removes');
						});
						$("#ret_items > tbody").empty();
						set_feedback(response.msg, 'alert alert-dismissible alert-success', false);		
					} else {
						set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);		
					}
				},
				dataType: 'json'
			});
		},

		errorClass: "has-error",
		errorLabelContainer: "#ret_error_message_box",
		wrapper: "li",
		highlight: function (e)	{
			$(e).parent().addClass('has-error');
		},
		unhighlight: function (e) {
			$(e).parent().removeClass('has-error');
		}
    });
});
</script>