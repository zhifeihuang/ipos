<ul class="error_message_box" id="order_error_message_box"></ul>

<form class="form-horizontal" id="order_form" action="home.php?act=receivings&f=order" method="post" accept-charset="utf-8">
	<fieldset>
		<div class="form-group">
			<label class="control-label col-xs-2" for="order_comment">{$lang['common_comments']}</label> <div class="col-xs-6">
			<textarea name="comment" class="form-control input-sm" id="order_comment" rows="3" value=""></textarea>
			</div>
		</div>
		<div class="form-group" id="table_action_header">
			<ul>
			<li class="pull-left">
				<label class="control-label sr-only" for="order_number">{$lang['recvs_order_number']}</label>
				<input class="form-control input-sm" title="{$lang['recvs_order_number_title']}" placeholder="{$lang['recvs_order_number']}" id="order_number" type="text" readonly>
			</li>
			<li class="pull-left">
				<label class="control-label sr-only" for="order_person">{$lang['recvs_order_person']}</label>
				<input class="form-control input-sm ui-autocomplete-input" title="{$lang['recvs_order_person']}" placeholder="{$lang['recvs_order_person']}" id="order_person" name="order_person" type="text" value="{$emp_id}">
			</li>
			<li class="pull-right">
				<label class="control-label sr-only" for="item_name">{$lang['sales_start_typing_item_name']}</label>
				<input class="form-control input-sm ui-autocomplete-input" placeholder="{$lang['sales_start_typing_item_name']}" id="item_name" size="32" type="text">
			</li>
			</ul>
		</div>
		
		<div id="table_holder">
			<table class="tablesorter table table-striped table-hover" id="order_items">
				<thead>
					<tr>
						<th width="10%">{$lang['common_delete']}</th>
						<th width="15%">{$lang['items_item_number']}</th>
						<th width="35%">{$lang['items_name']}</th>
						<th width="20%">{$lang['common_company_name']}</th>
						<th width="20%">{$lang['recvs_order_quantity']}</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<input class="btn btn-primary btn-sm pull-right" type="submit" value="{$lang['common_submit']}">
	</fieldset>
</form>
<script type="text/javascript">
$(document).ready(function() {
	$.validator.addMethod('min_1' , function(value, element) { return value >= 1; }, "{$lang['recvs_min_1']}");
	
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
});
</script>
