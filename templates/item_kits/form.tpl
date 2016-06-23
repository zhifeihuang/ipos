<div id="required_fields_message">{$lang['common_fields_required_message']}</div>

<ul class="error_message_box" id="error_message_box"></ul>

<form class="form-horizontal" id="item_kit_form" action="home.php?act=item_kits&f={nocache}{if isset($item['item_kit_id'])}update&id={$item['item_kit_id']}{else}save{/if}{/nocache}" method="post" accept-charset="utf-8">
	<fieldset>
		<div class="form-group form-group-sm">
			<label class="control-label col-xs-3 required" for="item_number">{$lang['items_item_number']}</label>			<div class="col-xs-6">
				<input name="item_number" class="form-control input-sm" id="item_number" type="text" value="{nocache}{if isset($item['item_number'])}{$item['item_number']}{/if}{/nocache}">
			</div>
		</div>
		
		<div class="form-group form-group-sm">
			<label class="control-label col-xs-3 required" for="name">{$lang['item_kits_name']}</label>			<div class="col-xs-6">
				<input name="name" class="form-control input-sm" id="name" type="text" value="{nocache}{if isset($item['name'])}{$item['name']}{/if}{/nocache}">
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label class="control-label col-xs-3 required" for="unit_price">{$lang['items_unit_price']}({$config['currency_symbol']})</label>			<div class="col-xs-6">
			<div class="input-group input-group-sm">
					<input name="unit_price" class="form-control input-sm number" id="unit_price" type="text" value="{nocache}{if isset($item['unit_price'])}{currency number=$item['unit_price'] thousands_separator=$config['thousands_separator'] decimal_point=$config['decimal_point'] decimals=$config['currency_decimals']}{/if}{/nocache}" readonly>
				</div>
			</div>
		</div>
		
		<div class="form-group form-group-sm">
			<label class="control-label col-xs-3" for="cost_price">{$lang['items_cost_price']}({$config['currency_symbol']})</label>			<div class="col-xs-6">
			<div class="input-group input-group-sm">
					<input name="cost_price" class="form-control input-sm number" id="cost_price" type="text" value="{nocache}{if isset($item['cost_price'])}{currency number=$item['cost_price'] thousands_separator=$config['thousands_separator'] decimal_point=$config['decimal_point'] decimals=$config['currency_decimals']}{/if}{/nocache}" readonly>
				</div>
			</div>
		</div>
		
		<div class="form-group form-group-sm">
			<label class="control-label col-xs-3" for="description">{$lang['item_kits_description']}</label>			<div class="col-xs-6">
				<textarea name="description" class="form-control input-sm" id="description" rows="3" cols="40" value="{nocache}{if isset($item['description'])}{$item['description']}{/if}{/nocache}"></textarea>
			</div>
		</div>
		
		<div class="form-group form-group-sm">
			<label class="control-label col-xs-3" for="item_name">{$lang['item_kits_add_item']}</label>			<div class="col-xs-6">
				<input class="form-control input-sm ui-autocomplete-input" id="item_name" type="text" value="">
			</div>
		</div>
				
		<table class="table table-striped table-hover" id="item_kit_items">
			<thead>
				<tr>
					<th width="10%">{$lang['common_delete']}</th>
					<th width="70%">{$lang['item_kits_item']}</th>
					<th width="20%">{$lang['item_kits_quantity']}</th>
				</tr>
			</thead>
			<tbody>
			{nocache}
			{foreach $kit_items as $v}
			<tr>
			<td><a onclick="return delete_item_kit_row(this);" value="{$v['item_id']}"><span class='glyphicon glyphicon-trash'></span></a></td>
			<td>{$v['name']}</td>
			<td><input type="text" class="quantity form-control input-sm" id="kit_item_{$v['item_id']}" name="item_kit_item[{$v['item_id']}]" value="{$v['quantity']}"></td>
			<td class="sr-only">{$v['cost_price']}</td>
			<td class="sr-only">{$v['cost_discount']}</td>
			<td class="sr-only">{$v['unit_price']}</td>
			</tr>
			{/foreach}
			{/nocache}
			</tbody>
		</table>
		<input class="sr-only" id="number-change" />
	</fieldset>
</form>

<script type="text/javascript">
//validation and submit handling
$(document).ready(function() {
	$.validator.addMethod('quantity' , function(value, element) {
		return value > 0;
    }, "{$lang['item_kits_quantity_0']}");
	
	$("input.quantity").blur(function() {
		calc_cost_price();
	});
	
	$("input.quantity").number(true, {$config['quantity_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}");
	$("input.number").number(true, {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}");
	
	$("#item_name").autocomplete({
		source: 'home.php?act=items&f=suggest_kit',
		autoFocus: false,
		delay:500,
		appendTo: ".modal-content",
		select: function(e, ui) {
			var data = ui.item.value.split(',');
			if (!data || data.length != 2)
				return false;
			
			var id = data[0];
			var row = data[1];
			if ($("#kit_item_" + id).length == 0) {
				$("#item_kit_items > tbody").append(row);
				$("#kit_item_" + id).rules("add", { quantity: true, messages: { quantity: "{$lang['item_kits_quantity_0']}" } });
				$("#kit_item_" + id).blur(function() {
					calc_cost_price();
				});
				$("#kit_item_" + id).number(true, {$config['quantity_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}");
			}
			$("#item_name").val("");
			return false;
		}
	} );

	$('#item_kit_form').validate($.extend({
		submitHandler:function(form) {
			$(form).ajaxSubmit({
				success:function(response)
				{
					dialog_support.hide();
					post_data_form_submit(response, {nocache}{if isset($item['item_kit_id'])}"update"{else}"save"{/if}{/nocache});
				},
				dataType:'json'
			});
		},
		rules:
		{
			name:"required",
			item_number:
			{
				required: true,
				remote:
				{
					url: "home.php?act=item_kits&f=check_item_number",
					type: "post",
					data:
					{
						"id" : "{nocache}{if isset($item['item_kit_id'])}{$item['item_kit_id']}{/if}{/nocache}",
						"item_number" : function()
						{
							return $("#item_number").val();
						}
					}
				}
			},
			unit_price:
			{
				required:true,
				number:true
			}
		},
		messages:
		{
			name:"{$lang['items_name_required']}",
			item_number: 
			{
				required:"{$lang['items_item_number_required']}",
				remote:"{$lang['items_item_number_duplicate']}"
			},
			unit_price:
			{
				required:"{$lang['items_unit_price_required']}",
				number:"{$lang['items_unit_price_number']}"
			}
		}
	}, dialog_support.error));
});
	
function calc_cost_price() {
	var total = 0;
	var ut = 0;
	$("#item_kit_items > tbody > tr").each(function() {
		var q = parseInt($("input", $(this)).eq(0).val());
		var c = parseFloat($("td:eq(3)", $(this)).text());
		var d = parseFloat($("td:eq(4)", $(this)).text());
		var u = parseFloat($("td:eq(5)", $(this)).text());
		if (q > 0 && c > 0)
			total += q * c * (100 - d) / 100;
		
		ut += q * u;
	});
	
	$("#cost_price").val(total);
	$("#unit_price").val(ut);
}

function delete_item_kit_row(link) {
	var tr = $(link).closest('tr');
	$("input.form-control", tr).rules("removes");
	tr.remove();
	calc_cost_price();
	return false;
}
</script>