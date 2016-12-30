<div id="required_fields_message">{$lang['common_fields_required_message']}</div>

<ul class="error_message_box" id="error_message_box"></ul>

<form class="form-horizontal" id="item_form" action="home.php?act=items&f={nocache}{if isset($item['item_id'])}update&id={$item['item_id']}{else}save{/if}{/nocache}" enctype="multipart/form-data" method="post" accept-charset="utf-8">
	<fieldset>
		<div class="form-group form-group-sm">
			<label class="required control-label col-xs-3" for="item_number">{$lang['items_item_number']}</label>			<div class="col-xs-6">
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-barcode"></span></span>
					<input name="item_number" class="form-control input-sm" id="item_number" type="text" {nocache}{if isset($item['item_number'])}value="{$item['item_number']}" readonly{else}value=""{/if}{/nocache}>
				</div>
			</div>
		</div>
		
		<div class="form-group form-group-sm">
			<label class="required control-label col-xs-3" for="name">{$lang['items_name']}</label>			<div class="col-xs-6">
				<input name="name" class="form-control input-sm" id="name" type="text" value="{nocache}{if isset($item['name'])}{$item['name']}{/if}{/nocache}">
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label class="required control-label col-xs-3" for="category">{$lang['items_category']}</label>			<div class="col-xs-6">
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-tag"></span></span>
					<input name="category" class="form-control input-sm ui-autocomplete-input" id="category" type="text" value="{nocache}{if isset($item['category'])}{$item['category']}{/if}{/nocache}">
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label class="control-label col-xs-3" for="supplier">{$lang['items_supplier']}</label>			<div class="col-xs-6">
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-tag"></span></span>
					<input name="supplier" class="form-control input-sm ui-autocomplete-input" id="supplier" type="text" value="{nocache}{$supplier}{/nocache}">
				</div>
			</div>
		</div>
		
		<div class="form-group form-group-sm">
			<label class="required control-label col-xs-3" for="cost_price">{$lang['items_cost_price']}({$config['currency_symbol']})</label>			<div class="col-xs-4">
				<div class="input-group input-group-sm">
					<input name="cost_price" class="form-control input-sm" id="cost_price" type="text" value="{nocache}{if isset($item['cost_price'])}{currency number=$item['cost_price'] thousands_separator=$config['thousands_separator'] decimal_point=$config['decimal_point'] decimals=$config['currency_decimals']}{/if}{/nocache}">
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label class="required control-label col-xs-3" for="unit_price">{$lang['items_unit_price']}({$config['currency_symbol']})</label>			<div class="col-xs-4">
				<div class="input-group input-group-sm">
					<input name="unit_price" class="form-control input-sm" id="unit_price" type="text" value="{nocache}{if isset($item['unit_price'])}{currency number=$item['unit_price'] thousands_separator=$config['thousands_separator'] decimal_point=$config['decimal_point'] decimals=$config['currency_decimals']}{/if}{/nocache}">
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label class="control-label col-xs-3" for="tax_percent_1">{$lang['items_tax_1']}</label>			<div class="col-xs-3">
				<input name="tax_names[]" class="form-control input-sm ui-autocomplete-input" id="tax_name_1" type="text" value="{nocache}{if isset($item_tax[0]['name'])}{$item_tax[0]['name']}{else}{$config['default_tax_1_name']}{/if}{/nocache}" >
			</div>
			<div class="col-xs-3">
				<div class="input-group input-group-sm">
					<input name="tax_percents[]" class="form-control input-sm" id="tax_percent_1" type="text" value="{nocache}{if isset($item_tax[0]['percent'])}{tax_decimals number=$item_tax[0]['percent'] decimal_point=$config['decimal_point'] decimals=$config['tax_decimals']}{else}{tax_decimals number=$config['default_tax_1_rate'] decimal_point=$config['decimal_point'] decimals=$config['tax_decimals']}{/if}{/nocache}">
					<span class="input-group-addon input-sm"><b>%</b></span>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label class="control-label col-xs-3" for="tax_percent_2">{$lang['items_tax_2']}</label>			<div class="col-xs-3">
				<input name="tax_names[]" class="form-control input-sm ui-autocomplete-input" id="tax_name_2" type="text" value="{nocache}{if isset($item_tax[1]['name'])}{$item_tax[1]['name']}{else}{$config['default_tax_2_name']}{/if}{/nocache}" >
			</div>
			<div class="col-xs-3">
				<div class="input-group input-group-sm">
					<input name="tax_percents[]" class="form-control input-sm" id="tax_percent_2" type="text" value="{nocache}{if isset($item_tax[1]['percent'])}{tax_decimals number=$item_tax[1]['percent'] decimal_point=$config['decimal_point'] decimals=$config['tax_decimals']}{else}{tax_decimals number=$config['default_tax_2_rate'] decimal_point=$config['decimal_point'] decimals=$config['tax_decimals']}{/if}{/nocache}">
					<span class="input-group-addon input-sm"><b>%</b></span>
				</div>
			</div>
		</div>
		
		<div class="form-group form-group-sm">
			<label class="required control-label col-xs-3" for="reorder_level">{$lang['items_reorder_level']}</label>			<div class="col-xs-3">
				<input name="reorder_level" class="form-control input-sm" id="reorder_level" type="text" value="{nocache}{if isset($item['reorder_level'])}{quantity number=$item['reorder_level']}{else}0{/if}{/nocache}">
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label class="control-label col-xs-3" for="description">{$lang['items_description']}</label>			<div class="col-xs-6">
				<textarea name="description" class="form-control input-sm" id="description" rows="3" cols="40" value="{nocache}{if isset($item['description'])}{$item['description']}{/if}{/nocache}"></textarea>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<input name="pic" id="pic" type="hidden" value="{nocache}{if isset($item['pic'])}{$item['pic']}{/if}{/nocache}" />
		</div>
		
	</fieldset>
</form>

<form class="form-horizontal" id="item_upload_form" action="home.php?act=items&f=upload{nocache}{if isset($item['item_id'])}&id={$item['item_id']}{/if}{/nocache}" enctype="multipart/form-data" method="post" accept-charset="utf-8">
	<div class="form-group form-group-sm">
		<label class="control-label col-xs-3 required" for="item_image">{$lang['items_image']}</label>			<div class="col-xs-8">
			<div class="fileinput {nocache}{if ($image_path == '')}fileinput-new{else} fileinput-exists{/if}{/nocache}" data-provides="fileinput">
				<div class="fileinput-new thumbnail" style="width: 100px; height: 100px;"></div>
				<div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 100px; max-height: 100px;">
					<img class="img-rounded img-responsive" alt="{$lang['items_image']}" src="{nocache}{$image_path}{/nocache}">
				</div>
				<div>
					<span class="btn btn-default btn-sm btn-file">
						<span class="fileinput-new">{$lang['items_select_image']}</span>
						<span class="fileinput-exists">{$lang['items_change_image']}</span>
						<input id="item_image" name="item_image" type="file" accept="image/*">
					</span>
					<a class="btn btn-default btn-sm fileinput-exists" href="#" data-dismiss="fileinput">{$lang['items_remove_image']}</a>
					<input name="submit" class="btn btn-primary btn-sm" type="submit" value="{$lang['common_upload']}">
				</div>
			</div>
		</div>
	</div>
</form>

<script type="text/javascript">
	//validation and submit handling
$(document).ready(function()
{
	$("#continue").click(function() {
		stay_open = true;
	});
	$("#submit").click(function() {
		stay_open = false;
	});
	
	$("#category").autocomplete( { source: "home.php?act=items&f=suggest_category",delay:500,appendTo: '.modal-content' } );
	$("#supplier").autocomplete( { source: "home.php?act=items&f=suggest_supplier",delay:500,appendTo: '.modal-content' } );
	$("#tax_name_1").autocomplete({
		source: "home.php?act=items&f=suggest_tax",
		delay:500,
		appendTo: '.modal-content',
		select: function(a, ui) {
			$(this).val(ui.item.label);
			$("#tax_percent_1").val(ui.item.value);
			return false;
		}
	});
	$("#tax_name_2").autocomplete({
		source: "home.php?act=items&f=suggest_tax",
		delay:500,
		appendTo: '.modal-content',
		select: function(a, ui) {
			$(this).val(ui.item.label);
			$("#tax_percent_2").val(ui.item.value);
			return false;
		}
	});
	
	$("a.fileinput-exists").click(function() {
		$.ajax({
			type: "GET",
			url: "home.php",
			data: "{nocache}act=items&f=remove_logo{if isset($item['item_id'])}&id={$item['item_id']}{/if}{/nocache}",
			success: function(response) {
				$("#pic").val('');
			},
			dataType: "json"
		})
	});
	
	$('#item_upload_form').validate({
		submitHandler: function(form) {
			$(form).ajaxSubmit({
				success: function(response)	{
					if(response.success)
					{
						$("#pic").val(response.pic);
						set_feedback(response.msg, 'alert alert-dismissible alert-success', false);		
					}
					else
					{
						set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);		
					}
				},
				dataType: 'json'
			});
		},
		errorClass: "has-error",
		errorLabelContainer: "#error_message_box",
		wrapper: "li",
		highlight: function (e)	{
			$(e).closest('.form-group').addClass('has-error');
		},
		unhighlight: function (e) {
			$(e).closest('.form-group').removeClass('has-error');
		},
		
		rules: 
		{
			item_image: "required"
		},
		
		messages:
		{
			item_image: "{$lang["items_image_required"]}"
		}
	});

	$('#item_form').validate($.extend({
		submitHandler: function(form, event) {
			$(form).ajaxSubmit({
				success: function(response) {
					var stay_open = dialog_support.clicked_id() != 'submit';
					if (stay_open)
					{
						// set action of item_form to url without item id, so a new one can be created
						$("#item_form").attr("action", "home.php?act=items&f=save");
						// use a whitelist of fields to minimize unintended side effects
						$(':text, :password, :file, #description, #item_form').not('#reorder_level, #tax_name_1, #tax_percent_1, #tax_name_2, #tax_percent_2, #name, #cost_price, #unit_price').val('');
						// de-select any checkboxes, radios and drop-down menus
						$(':input', '#item_form').removeAttr('checked').removeAttr('selected');
					}
					else
					{
						dialog_support.hide();
					}
					{nocache}
					post_data_form_submit(response, {if isset($item['item_id'])}"update"{else}"save"{/if});
					$("#" + response.id +" a[data-toggle=popover]").popover();
					{/nocache}
				},
				dataType: 'json'
			});
		},

		rules:
		{
			name:"required",
			category:"required",
			item_number:
			{
				required: true,
				remote:
				{
					url: "home.php?act=items&f=check_item_number",
					type: "post",
					data:
					{
						"id" : "{nocache}{if isset($item['item_id'])}{$item['item_id']}{/if}{/nocache}",
						"item_number" : function()
						{
							return $("#item_number").val();
						}
					}
				}
			},
			cost_price:
			{
				required:true,
				number:true
			},
			unit_price:
			{
				required:true,
				number:true
			},
			reorder_level:
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
			category:"{$lang['items_category_required']}",
			cost_price:
			{
				required:"{$lang['items_cost_price_required']}",
				number:"{$lang['items_cost_price_number']}"
			},
			unit_price:
			{
				required:"{$lang['items_unit_price_required']}",
				number:"{$lang['items_unit_price_number']}"
			},
			reorder_level:
			{
				required:"{$lang['items_reorder_level_required']}",
				number:"{$lang['items_reorder_level_number']}"
			}
		}
	}, dialog_support.error));
});
</script>