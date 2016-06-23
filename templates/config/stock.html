 <form class="form-horizontal" id="location_config_form" method="post" accept-charset="utf-8">
	<fieldset>
		<div id="required_fields_message">{$lang['common_fields_required_message']}</div>
		<ul class="error_message_box" id="stock_error_message_box"></ul>
		
		<div id="stock_locations">
		{include file='partial/stock_part.tpl'}
		</div>
		
		<input class="btn btn-primary btn-sm pull-right" type="submit" value="{$lang['common_submit']}">
	</fieldset>
</form>
<script type="text/javascript">
//validation and submit handling
$(document).ready(function()
{
	var location_count = 1;
	var remove_stock = new Array();
	// change_stock.old_data = new_data
	var change_stock = new Object();
	var add_stock = new Array();
	
	$("input[type='text']").change(function() {
		var val = $(this).val();
		var attr = $(this).attr('sign');
		
		if ($(this).hasClass('add')) {
			var index = $.inArray(attr, add_stock);
			if (index != -1) {
				add_stock[index] = val;
			} else {
				add_stock.push(val);
			}
			
			$(this).attr('sign', val);
		} else {
			change_stock[attr] = val;
		}
	});

	var hide_show_remove = function() {
		if ($("input[name*='stock_location']:enabled").length > 1)
		{
			$(".remove_stock_location").show();
		} 
		else
		{
			$(".remove_stock_location").hide();
		}
	};

	var add_stock_location = function() {
		var id = $(this).parent().find('input').attr('id');
		id = id.replace(/.*?_(\d+)$/g, "$1");
		var block = $(this).parent().clone(true);
		var new_block = block.insertAfter($(this).parent());
		var new_block_id = 'stock_location_' + ++id;
		$(new_block).find('label').html("{$lang['config_stock_location']}" + ++location_count).attr('for', new_block_id).attr('class', 'control-label col-xs-2');
		$(new_block).find('input').attr('id', new_block_id).removeAttr('disabled').attr('name', 'stock_location[]').attr('class', 'form-control input-sm add').attr('sign', "").val('')
		.rules("add", { stock_location: true, valid_chars: true, messages: { stock_location: "{$lang['config_stock_location_duplicate']}", valid_chars: "{$lang['config_stock_location_invalid_chars']}" } } );
		hide_show_remove();
	};

	var remove_stock_location = function() {
		var input = $(this).parent().find('input');
		var val = $(input).val();
		var attr = $(input).attr('sign');
		
		if (change_stock.hasOwnProperty(attr)) {
			change_stock[attr] = undefined;
		}
		if (!$(input).hasClass('add')) {
			remove_stock.push(attr);
		} else {
			var index = $.inArray(val, add_stock);
			if (index != -1) add_stock.splice(index, 1);
		}
		
		$(input).rules("removes");
		$(this).parent().remove();
		hide_show_remove();
	};

	var init_stock_data = function() {
		add_stock.splice(0, add_stock.length);
		remove_stock.splice(0, remove_stock.length);
		change_stock = new Object();
	};
	
	var init_add_remove_locations = function() {
		$('.add_stock_location').click(add_stock_location);
		$('.remove_stock_location').click(remove_stock_location);
		hide_show_remove();
	};
	init_add_remove_locations();

	// run validator once for all fields
	$.validator.addMethod('stock_location' , function(value, element) {
		var value_count = 0;
		$("input[name='stock_location[]']").each(function() {
			value_count = $(this).val() == value ? value_count + 1 : value_count; 
		});
		return value_count < 2;
    }, "{$lang['config_stock_location_duplicate']}");

    $.validator.addMethod('valid_chars', function(value, element) {
		return value.indexOf('_') === -1;
    }, "{$lang['config_stock_location_invalid_chars']}");
	
	$('#location_config_form').validate({
		submitHandler: function(form) {
			var str = "";
			for (x in change_stock) {
				if (change_stock[x] !== undefined) {
					str += x;
					str += ",";
					str += change_stock[x];
					str += ",";
				}
			}
			if (str.length == 0 && add_stock.length == 0 && remove_stock.length == 0) return false;
				
			$.post("home.php?act=config&f=stock", { change: str.slice(0, -1), add: add_stock.toString(), remove: remove_stock.toString() }, 
				function(response) {
					if(response.success)
					{
						init_stock_data();
						$("#stock_locations").empty().append(response.part);
						init_add_remove_locations();
						set_feedback(response.msg, 'alert alert-dismissible alert-success', false);		
					}
					else
					{
						set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);		
					}
				},
				'json'
			);
		},
		
		errorClass: "has-error",
		errorLabelContainer: "#stock_error_message_box",
		wrapper: "li",
		highlight: function (e)	{
			$(e).closest('.form-group').addClass('has-error');
		},
		unhighlight: function (e) {
			$(e).closest('.form-group').removeClass('has-error');
		},

		rules: 
		{
			{foreach $stock_locations as $k=>$v}
			stock_location_{$k}:
			{
				required: true,
				stock_location: true,
				valid_chars: true
			},
			{/foreach}
		},

		messages: 
		{
			{foreach $stock_locations as $k=>$v}
			stock_location_{$k}: 
			{
				required:"{$lang['config_stock_location_required']}",
				stock_location: "{$lang['config_stock_location_duplicate']}",
				valid_chars: "{$lang['config_stock_location_invalid_chars']}"
			},
			{/foreach}
		}
	});
});
</script>