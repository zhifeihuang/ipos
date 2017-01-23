<ul class="error_message_box" id="recv_error_message_box"></ul>

<form class="form-horizontal" id="recv_form" action="home.php?act=receivings&f=receive" method="post" accept-charset="utf-8">
	<fieldset>
		<div class="form-group">
			<div class="col-xs-3">
				<input class="form-control input-sm" placeholder="{$lang['recvs_recv_date']}" id="order_date" type="text" readonly>
			</div>
			<div class="col-xs-3">
				<input class="form-control input-sm" placeholder="{$lang['recvs_order_number_title']}" id="order_number1" type="text" readonly>
				<input name="id" id="order_id" type="hidden">
			</div>
			<div class="col-xs-3">
				<input class="form-control input-sm" placeholder="{$lang['recvs_emp']}" id="order_emp" type="text" readonly>
			</div>
			<div class="col-xs-3">
				<input class="form-control input-sm" placeholder="{$lang['recvs_total']}({$config['currency_symbol']})" id="order_total" type="text" readonly>
			</div>
		</div>
	
		<div class="form-group" id="table_action_header">
			<ul>
				<li class="pull-right">
					<input class="form-control input-sm ui-autocomplete-input" placeholder="{$lang['recvs_find_order_number']}" id="recv_find_number" type="text">
				</li>
			</ul>
		</div>
		
		<div id="table_holder">
			<table class="tablesorter table table-striped table-hover" id="recv_items">
				<thead>
					<tr>
						<th width="15%">{$lang['items_item_number']}</th>
						<th width="15%">{$lang['items_name']}</th>
						<th width="15%">{$lang['common_company_name']}</th>
						<th width="10%">{$lang['recvs_order_quantity']}</th>
						<th width="10%">{$lang['recvs_recv_quantity']}</th>
						<th width="10%">{$lang['recvs_cost_price']}({$config['currency_symbol']})</th>
						<th width="10%">{$lang['recvs_discount']}</th>
						<th width="15%">{$lang['recvs_total']}({$config['currency_symbol']})</th>
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
	$.validator.addMethod('min_0' , function(value, element) { return value >= 0; }, "{$lang['recvs_min_0']}");
	$("#order_total").number(true, {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}");
	
	var calc_recv_total = function() {
		var total = 0;
		$("#recv_items > tbody > tr").each(function() {
			var t7 = $("td:eq(7)", $(this));
			total += parseFloat($(t7).attr("value"));
		});
		
		$("#order_total").val(total);
	};
	
	$("#recv_find_number").autocomplete({
		source: 'home.php?act=receivings&f=suggest_search',
		autoFocus: false,
		delay:500,
		appendTo: ".modal-content",
		select: function(e, ui) {
			$.post('home.php?act=receivings&f=get', { id: ui.item.value }, function(response) {
				if (!response.success) {
					set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);
					return;
				}
				
				$("#order_date").val(response.recv['date']);
				$("#order_total").val(response.recv['total']);
				$("#order_number1").val(response.recv['number']);
				$("#order_id").val(response.recv['id']);
				$("#order_emp").val(response.recv['emp']);
				$('#recv_items > tbody > tr input').each(function() {
					$(this).rules('removes');
				});
				$("#recv_items > tbody").empty().append(response.row);
				
				$("#recv_items > tbody > tr").each(function() {
					var input = $("td:eq(4) input", $(this)).eq(0);
					$(input).blur(function() {
						var tr = $(this).closest("tr");
						var q = parseInt($(this).val());
						var c = parseFloat($("td:eq(5)", $(tr)).attr("value"));
						var d = parseFloat($("td:eq(6)", $(tr)).attr("value"));
						var t = q * c * (100 - d) / 100;
						$("td:eq(7)", $(tr)).attr("value", t).text($.number(t, {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
						calc_recv_total();
					});
					
					$(input).rules("add", { min_0: true, required: true, messages: { min_0: "{$lang['recvs_min_0']}", required: "{$lang['recvs_quantity_required']}" } } );

					$(input).number(true, 0, "", "{$config['thousands_separator']}");
					$(input).val($(input).val());
					
					var t3 = $("td:eq(3)", $(this));
					var t5 = $("td:eq(5)", $(this));
					var t6 = $("td:eq(6)", $(this));
					var t7 = $("td:eq(7)", $(this));
					$(t3).attr("value", $(t3).text()).text($.number($(t3).text(), 0, "", "{$config['thousands_separator']}"));
					$(t5).attr("value", $(t5).text()).text($.number($(t5).text(), {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
					$(t6).attr("value", $(t6).text()).text($.number($(t6).text(), 0, "", "{$config['thousands_separator']}"));
					$(t7).attr("value", $(t7).text()).text($.number($(t7).text(), {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
				});
				
				set_feedback(response.msg, 'alert alert-dismissible alert-success', false);	
			},
			'json');
			
			$("#recv_find_number").val('');
			return false;
		}
	});

    $('#recv_form').validate({
		submitHandler: function(form) {
			var tr = $("#recv_items > tbody > tr");
			if (tr.length == 0)
				return false;
		
			$(form).ajaxSubmit({
				success: function(response)	{
					if(response.success) {
						$('#recv_form :text, :password, :file').val('');
						$('#recv_items > tbody > tr input').each(function() {
							$(this).rules('removes');
						});
						$("#recv_items > tbody").empty();
						set_feedback(response.msg, 'alert alert-dismissible alert-success', false);		
					} else {
						set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);		
					}
				},
				dataType: 'json'
			});
		},

		errorClass: "has-error",
		errorLabelContainer: "#recv_error_message_box",
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