{extends file='header.tpl'}
{block name="container"}
<link rel="stylesheet" type="text/css" href="css/tablett.css"/>
<div class="row">
	<div class="col-xs-2">
		<ul class="nav nav-tabs nav-stacked" data-tabs="tabs">
{nocache}
{if !empty($subgrant["receivings_insert"])}
			<li class="active" role="presentation">
				<a data-toggle="tab" href="#order">{$lang['recvs_order']}</a>
			</li>
{/if}
{if !empty($subgrant["receivings_update"])}
			<li role="presentation">
				<a data-toggle="tab" href="#receive">{$lang['recvs_receive']}</a>
			</li>
{/if}
{if !empty($subgrant["receivings_delete"])}
			<li role="presentation">
				<a data-toggle="tab" href="#ret">{$lang['recvs_return']}</a>
			</li>
{/if}
{/nocache}
		</ul>
	</div>

	<div class="tab-content col-xs-10">
{nocache}
{if !empty($subgrant["receivings_insert"])}
		<div class="tab-pane fade in active" id="order">
		{include file='receivings/order.tpl'}
		</div>
{/if}
{if !empty($subgrant["receivings_update"])}
		<div class="tab-pane" id="receive">
		{include file='receivings/receive.tpl'}
		</div>
{/if}
{if !empty($subgrant["receivings_delete"])}
		<div class="tab-pane" id="ret">
		{include file='receivings/return.tpl'}
		</div>
{/if}
{/nocache}
	</div>
</div>
<script>
$(document).ready(function() {
	$.validator.setDefaults({ ignore: [] });
	$.validator.addMethod('min_0' , function(value, element) { return value >= 0; }, "{$lang['recvs_min_0']}");
	$.validator.addMethod('min_1' , function(value, element) { return value >= 1; }, "{$lang['recvs_min_1']}");

{nocache}
{if !empty($subgrant["receivings_insert"])}
	$("#order_person").autocomplete({
		source: 'home.php?act=employees&f=suggest_order',
		autoFocus: false,
		delay:500,
		appendTo: ".modal-content",
		select: function(e, ui) {
			var data = ui.item.value.split(/\s+/);
			if (!data)
				return false;
			
			$("#order_person").val(data[0]);
			return false;
		}
	});
	
	$("#item_name").autocomplete({
		source: 'home.php?act=items&f=suggest_order',
		autoFocus: false,
		delay:500,
		appendTo: ".modal-content",
		select: function(e, ui) {
			var data = ui.item.value.split(',');
			if (!data || data.length != 2)
				return false;
			
			var id = data[0];
			var ht = data[1];
			var row = "#order_item_" + id;
			if ($(row).length == 0) {
				$("#order_items > tbody").append(ht);
				$(row).rules("add", { min_1: true, messages: { min_1: "{$lang['recvs_min_1']}" } } );
				$(row).number(true, 0, "", "{$config['thousands_separator']}");
			}
			$("#item_name").val("");
			return false;
		}
	});

    $('#order_form').validate({
		submitHandler: function(form) {
			var tr = $("#order_items > tbody > tr");
			if (tr.length == 0)
				return false;
						
			$(form).ajaxSubmit({
				success: function(response)	{
					if(response.success) {
						$('#order_number').val(response.number);
						$('#order_form :text, :password, :file').not('#order_number,#order_person').val('');
						$('#order_items > tbody > tr input').each(function() {
							$(this).rules('removes');
						});
						$('#order_items > tbody').empty();
						set_feedback(response.msg, 'alert alert-dismissible alert-success', false);		
					} else {
						set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);		
					}
				},
				dataType: 'json'
			});
		},

		errorClass: "has-error",
		errorLabelContainer: "#order_error_message_box",
		wrapper: "li",
		highlight: function (e)	{
			$(e).parent().addClass('has-error');
		},
		unhighlight: function (e) {
			$(e).parent().removeClass('has-error');
		},
		rules: {
			order_person: { required: true }
		},
		messages: {
			order_person: { required: "{$lang["recvs_order_person_required"]}" }
		}
    });
{/if}	

{if !empty($subgrant["receivings_update"])}
	{* receive *}
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
{/if}

{if !empty($subgrant["receivings_delete"])}	
	{* return *}
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
					var id = ids[i];
					$("#ret_" + id).rules("add", { min_1: true, less: true, messages: { min_1: "{$lang['recvs_min_1']}", less: "{$lang['recvs_ret_less']}" } });
					$("#ret_" + id).blur(function() {
						var tr = $(this).closest("tr");
						var q = parseInt($(this).val());
						var c = parseFloat($("td:eq(7)", tr).attr("value"));
						var d = parseFloat($("td:eq(8)", tr).attr("value"));
						var t = q * c * (100 - d) / 100;
						$("td:eq(9)", tr).attr("value", t).text($.number(t, {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
						
						calc_ret_total();
					});
					
					var tr = $("#ret_" + id).closest("tr");
					var t5 = $("td:eq(5)", tr);
					var t7 = $("td:eq(7)", tr);
					var t8 = $("td:eq(8)", tr);
					var t9 = $("td:eq(9)", tr);
					t5.attr("value", t5.text()).text($.number(t5.text(), 0, "", "{$config['thousands_separator']}"));
					t7.attr("value", t7.text()).text($.number(t7.text(), {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
					t8.attr("value", t8.text()).text($.number(t8.text(), {$config['tax_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
					t9.attr("value", t9.text()).text($.number(t9.text(), {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
					
					$("#ret_" + id).number(true, 0, "", "{$config['thousands_separator']}");
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
{/if}
{/nocache}	
});

function calc_ret_total() {
	var total = 0;
	$("#ret_items > tbody > tr").each(function() {
		var t9 = $("td:eq(9)", $(this));
		total += parseFloat(t9.attr("value"));
	});
	
	$("#ret_total").val(total);
}

function delete_ret_row(link) {
	delete_tr_row(link);
	calc_ret_total();
	return false;
}

function delete_tr_row(link) {
	var tr = $(link).closest("tr");
	$("input", tr).rules("removes");
	tr.remove();
	return false;
}
</script>
{/block}