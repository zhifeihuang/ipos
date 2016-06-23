<div id="required_fields_message">{$lang['items_edit_fields_you_want_to_update']}</div>

<ul class="error_message_box" id="error_message_box"></ul>

<form class="form-horizontal" id="item_form" action="home.php?act=items&f=bulk_update" method="post" accept-charset="utf-8">
	<fieldset>
		<div class="form-group form-group-sm">	
			<label class="control-label col-xs-3" for="category">{$lang['items_category']}</label>			<div class="col-xs-6">
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-tag"></span></span>
					<input name="category" class="form-control input-sm ui-autocomplete-input" id="category" type="text" value="">
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<label class="control-label col-xs-3" for="supplier">{$lang['items_supplier']}</label>			<div class="col-xs-6">
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-tag"></span></span>
					<input name="supplier" class="form-control input-sm ui-autocomplete-input" id="supplier" type="text" value="">
				</div>
			</div>
		</div>

	<div class="form-group form-group-sm">
			<label class="control-label col-xs-3" for="cost_price">{$lang['items_cost_price']}({$config['currency_symbol']})</label>			<div class="col-xs-4">
				<div class="input-group input-group-sm">
					<input name="cost_price" class="form-control input-sm" id="cost_price" type="text" value="">
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label class="control-label col-xs-3" for="unit_price">{$lang['items_unit_price']}({$config['currency_symbol']})</label>			<div class="col-xs-4">
				<div class="input-group input-group-sm">
					<input name="unit_price" class="form-control input-sm" id="unit_price" type="text" value="">
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label class="control-label col-xs-3" for="tax_percent_1">{$lang['items_tax_1']}</label>			<div class="col-sm-3">
				<input name="tax_names[]" class="form-control input-sm ui-autocomplete-input" id="tax_name_1" type="text" value="" autocomplete="off">
			</div>
			<div class="col-sm-3">
				<div class="input-group input-group-sm">
					<input name="tax_percents[]" class="form-control input-sm" id="tax_percent_1" type="text" value="">
					<span class="input-group input-group-addon"><b>%</b></span>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label class="control-label col-xs-3" for="tax_percent_2">{$lang['items_tax_2']}</label>			<div class="col-sm-3">
				<input name="tax_names[]" class="form-control input-sm ui-autocomplete-input" id="tax_name_2" type="text" value="" autocomplete="off">
			</div>
			<div class="col-sm-3">
				<div class="input-group input-group-sm">
					<input name="tax_percents[]" class="form-control input-sm" id="tax_percent_2" type="text" value="">
					<span class="input-group input-group-addon"><b>%</b></span>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<label class="control-label col-xs-3" for="reorder_level">{$lang['items_reorder_level']}</label>			<div class="col-xs-3">
				<input name="reorder_level" class="form-control input-sm" id="reorder_level" type="text" value="">
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<label class="control-label col-xs-3" for="description">{$lang['items_description']}</label>			<div class="col-xs-5">
				<textarea name="description" class="form-control input-sm" id="description" rows="3" cols="40"></textarea>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label class="control-label col-xs-3" for="allow_alt_description">{$lang['items_allow_alt_description']}</label>			<div class="col-xs-5">
				<select name="allow_alt_description" class="form-control">
				{foreach $allow_alt_description as $k => $v}
<option value="{$k}">{$v}</option>
				{/foreach}
</select>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label class="control-label col-xs-3" for="is_serialized">{$lang['items_is_serialized']}</label>			<div class="col-xs-5">
				<select name="is_serialized" class="form-control">
				{foreach $is_serialized as $k => $v}
<option value="{$k}">{$v}</option>
				{/foreach}
</select>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">
//validation and submit handling
$(document).ready(function() {
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

	var confirm_message = false;
	$("#tax_percent_2, #tax_name_2").prop('disabled', true),
	$("#tax_percent_1, #tax_name_1").blur(function() {
		var disabled = !($("#tax_percent_1").val() + $("#tax_name_1").val());
		$("#tax_percent_2, #tax_name_2").prop('disabled', disabled);
		confirm_message =  disabled ? "" : "{$lang['items_confirm_bulk_edit_wipe_taxes']}";
	});

	$('#item_form').validate($.extend({
		submitHandler:function(form) {
			if(!confirm_message || confirm(confirm_message))
			{
				//Get the selected ids and create hidden fields to send with ajax submit.
				var selected_item_ids=get_selected_values();
				for(k=0;k<selected_item_ids.length;k++)
				{
					$(form).append("<input type='hidden' name='ids[]' value='"+selected_item_ids[k]+"' >");
				}

				$(form).ajaxSubmit({
					success:function(response)
					{
						dialog_support.hide();
						post_bulk_form_submit(response);
					},
					dataType:'json'
				});
			}
		}
	}, dialog_support.error));
});
</script>