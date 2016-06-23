<form class="form-horizontal" id="locale_config_form" action="home.php?act=config&f=locale" method="post" accept-charset="utf-8">
	<fieldset>
		<div id="required_fields_message">{$lang['common_fields_required_message']}</div>
		<ul class="error_message_box" id="locale_error_message_box"></ul>

		<div class="form-group form-group-sm">
			<label class="control-label col-xs-2" for="currency_symbol">{$lang['config_currency_symbol']}</label>				<div class="col-xs-1">
				<input name="currency_symbol" class="form-control input-sm" id="currency_symbol" type="text" value="{$config['currency_symbol']}">
			</div>
			<div class="checkbox col-xs-2">
				<label>
					<input name="currency_side" id="currency_side" type="checkbox" value="1" {if $config['currency_side']}checked="checked"{/if}>
					{$lang['config_currency_side']}					</label>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label class="control-label col-xs-2" for="currency_decimals">{$lang['config_currency_decimals']}</label>				<div class="col-xs-2">
				<select name="currency_decimals" class="form-control input-sm">
				
				{foreach $tpl_config['config_currency_decimals'] as $val}
					<option value="{$val}" {if ($config['currency_decimals'] == $val)}selected="selected"{/if}>{$val}</option>
				{/foreach}
				
</select>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label class="control-label col-xs-2" for="quantity_decimals">{$lang['config_quantity_decimals']}</label>				<div class="col-xs-2">
				<select name="quantity_decimals" class="form-control input-sm">
				
				{foreach $tpl_config['config_quantity_decimals'] as $val}
					<option value="{$val}" {if ($config['quantity_decimals'] == $val)}selected="selected"{/if}>{$val}</option>
				{/foreach}
				
</select>
			</div>
		</div>
		
		<div class="form-group form-group-sm">
			<label class="control-label col-xs-2" for="tax_decimals">{$lang['config_tax_decimals']}</label>				<div class="col-xs-2">
				<select name="tax_decimals" class="form-control input-sm">
				
				{foreach $tpl_config['config_tax_decimals'] as $val}
					<option value="{$val}" {if ($config['tax_decimals'] == $val)}selected="selected"{/if}>{$val}</option>
				{/foreach}
				
</select>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label class="control-label col-xs-2" for="decimal_point">{$lang['config_decimal_point']}</label>				<div class="col-xs-2">
				<select name="decimal_point" class="form-control input-sm duplicate">	
									
				{foreach $tpl_config['config_decimal_point'] as $k => $val}
					<option value="{$k}" {if ($config['decimal_point'] == $k)}selected="selected"{/if}>{$val}</option>
				{/foreach}
				
</select>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label class="control-label col-xs-2" for="thousands_separator">{$lang['config_thousands_separator']}</label>				<div class="col-xs-2">
				<select name="thousands_separator" class="form-control input-sm duplicate">		
				
				{foreach $tpl_config['config_thousands_separator'] as $k => $val}
					<option value="{$k}" {if ($config['thousands_separator'] == $k)}selected="selected"{/if}>{$val}</option>
				{/foreach}
				
</select>
			</div>
		</div>
		
		<div class="form-group form-group-sm">	
		<label class="control-label col-xs-2" for="timezone">{$lang['config_timezone']}</label>				<div class="col-xs-4">
			<select name="timezone" class="form-control input-sm">
			
			{foreach $tpl_config['timezone'] as $val}
				<option value="{$val}" {if ($config['timezone'] == $val)}selected="selected"{/if}>{$val}</option>
			{/foreach}
			
</select>
			</div>
		</div>

		<div class="form-group form-group-sm">	
		<label class="control-label col-xs-2" for="dateformat">{$lang['config_datetimeformat']}</label>				<div class="col-sm-2">
			<select name="dateformat" class="form-control input-sm">
			
			{foreach $tpl_config['dateformat'] as $k => $val}
				<option value="{$k}" {if ($config['dateformat'] == $k)}selected="selected"{/if}>{$val}</option>
			{/foreach}
			
</select>
			</div>
			<div class="col-sm-2">
			<select name="timeformat" class="form-control input-sm">
			
			{foreach $tpl_config['timeformat'] as $k => $val}
				<option value="{$k}" {if ($config['timeformat'] == $k)}selected="selected"{/if}>{$val}</option>
			{/foreach}
			
</select>
			</div>
		</div>

		<input class="btn btn-primary btn-sm pull-right" type="submit" value="{$lang['common_submit']}">
	</fieldset>
</form>
<script type="text/javascript">
//validation and submit handling
$(document).ready(function()
{
	$.validator.addMethod('duplicate' , function(value, element) {
		var value_count = 0;
		$("select.duplicate").each (function() {
			value_count = $(this).val() == value ? value_count + 1 : value_count; 
		});
		return value_count < 2;
    }, "{$lang['config_local_num_duplicate']}");
	
	$('#locale_config_form').validate({
		submitHandler: function(form) {
			$(form).ajaxSubmit({
				success: function(response)	{
					if(response.success)
					{
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
		errorLabelContainer: "#locale_error_message_box",
		wrapper: "li",
		highlight: function (e)	{
			$(e).closest('.form-group').addClass('has-error');
		},
		unhighlight: function (e) {
			$(e).closest('.form-group').removeClass('has-error');
		},

		rules: 
		{
 		
   		},

		messages: 
		{

		}
	});
});
</script>
    