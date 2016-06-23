<form class="form-horizontal" id="barcode_config_form" action="home.php?act=config&f=barcode" method="post" accept-charset="utf-8">
	<fieldset>
		<div id="required_fields_message">{$lang['common_fields_required_message']}</div>
		<ul class="error_message_box" id="barcode_error_message_box"></ul>

		<div class="form-group form-group-sm">    
		<label class="control-label col-xs-2" for="barcode_type">{$lang['config_barcode_type']}</label>                <div class="col-xs-2">
				<select name="barcode_type" class="form-control input-sm">
				
				{foreach $tpl_config['barcode_type'] as $k => $val}
					<option value="{$k}" {if $config['barcode_type'] == $k} selected="selected"{/if}>{$val}</option>
				{/foreach}
				
</select>
			</div>
		</div>
		
		<div class="form-group form-group-sm">    
		<label class="control-label col-xs-2 required" for="barcode_quality">{$lang['config_barcode_quality']}</label>                <div class="col-xs-2">
			<input name="barcode_quality" class="form-control input-sm" id="barcode_quality" type="number" min="10" max="100" value="{$config['barcode_quality']}">
			</div>
		</div>
		
		<div class="form-group form-group-sm">    
		<label class="control-label col-xs-2 required" for="barcode_width">{$lang['config_barcode_width']}</label>                <div class="col-xs-2">
			<input name="barcode_width" class="form-control input-sm" id="barcode_width" type="number" min="60" max="350" step="5" value="{$config['barcode_width']}">
			</div>
		</div>

		<div class="form-group form-group-sm">    
		<label class="control-label col-xs-2 required" for="barcode_height">{$lang['config_barcode_height']}</label>                <div class="col-xs-2">
			<input name="barcode_height" class="form-control input-sm" id="barcode_height" type="number" min="10" max="120" value="{$config['barcode_height']}">
			</div>
		</div>
		
		<div class="form-group form-group-sm">    
		<label class="control-label col-xs-2 required" for="barcode_font">{$lang['config_barcode_font']}</label>                <div class="col-sm-2">
			<select name="barcode_font" class="form-control input-sm">
				
				{foreach $tpl_config['barcode_font'] as $k => $val}
					<option value="{$k}" {if $config['barcode_font'] == $k} selected="selected"{/if}>{$val}</option>
				{/foreach}
				
</select>
			</div>
			<div class="col-sm-2">
				<input name="barcode_font_size" class="form-control input-sm" id="barcode_font_size" type="number" min="1" max="30" value="{$config['barcode_font_size']}">
			</div>
		</div>
		
		<div class="form-group form-group-sm">
		<label class="control-label col-xs-2" for="barcode_content">{$lang['config_barcode_content']}</label>				<div class="col-xs-8">
				<label class="radio-inline">
					<input name="barcode_content" type="radio" {if $config['barcode_content'] == 'id'}checked="checked"{/if} value="id">
					{$lang['config_barcode_id']}                    </label>
				<label class="radio-inline">
					<input name="barcode_content" type="radio" {if $config['barcode_content'] == 'number'}checked="checked"{/if} value="number">
					{$lang['config_barcode_number']}                    </label>
				<label class="checkbox-inline">
					<input name="barcode_generate_if_empty" type="checkbox" {if $config['barcode_generate_if_empty'] == 1}checked="checked"{/if} value="1">
					{$lang['config_barcode_generate_if_empty']}                    </label>
			</div>
		</div>

		<div class="form-group form-group-sm">    
		<label class="control-label col-xs-2" for="barcode_layout">{$lang['config_barcode_layout']}</label>                <div class="col-sm-10">
				<div class="form-group form-group-sm row">
					<label class="control-label col-sm-1">{$lang['config_barcode_first_row']} </label>
					<div class="col-sm-2">
						<select name="barcode_first_row" class="form-control input-sm">
				
				{foreach $lang['config_barcode_row'] as $k => $val}
					<option value="{$k}" {if $config['barcode_first_row'] == $k} selected="selected"{/if}>{$val}</option>
				{/foreach}
				
</select>
					</div>
					<label class="control-label col-sm-1">{$lang['config_barcode_second_row']} </label>
					<div class="col-sm-2">
						<select name="barcode_second_row" class="form-control input-sm">
				
				{foreach $lang['config_barcode_row'] as $k => $val}
					<option value="{$k}" {if $config['barcode_second_row'] == $k} selected="selected"{/if}>{$val}</option>
				{/foreach}
				
</select>
					</div>
					<label class="control-label col-sm-1">{$lang['config_barcode_third_row']} </label>
					<div class="col-sm-2">
						<select name="barcode_third_row" class="form-control input-sm">
				
				{foreach $lang['config_barcode_row'] as $k => $val}
					<option value="{$k}" {if $config['barcode_third_row'] == $k} selected="selected"{/if}>{$val}</option>
				{/foreach}
				
</select>
					</div>
				</div>
			</div>
		</div>
		
		<div class="form-group form-group-sm">    
		<label class="control-label col-xs-2 required" for="barcode_num_in_row">{$lang['config_barcode_number_in_row']}</label>                <div class="col-xs-2">
			<input name="barcode_num_in_row" class="form-control input-sm" id="barcode_num_in_row" type="text" value="{$config['barcode_num_in_row']}">
			</div>
		</div>
		
		<div class="form-group form-group-sm">    
		<label class="control-label col-xs-2 required" for="barcode_page_width">{$lang['config_barcode_page_width']}</label>                <div class="col-sm-2">
				<div class="input-group">
					<input name="barcode_page_width" class="form-control input-sm" id="barcode_page_width" type="text" value="{$config['barcode_page_width']}">
					<span class="input-group-addon input-sm">%</span>
				</div>
			</div>
		</div>
		
		<div class="form-group form-group-sm">    
		<label class="control-label col-xs-2 required" for="barcode_page_cellspacing">{$lang['config_barcode_page_cellspacing']}</label>                <div class="col-sm-2">
				<div class="input-group">
					<input name="barcode_page_cellspacing" class="form-control input-sm" id="barcode_page_cellspacing" type="text" value="{$config['barcode_page_cellspacing']}">
					<span class="input-group-addon input-sm">px</span>
				</div>
			</div>
		</div>
		
		<input class="btn btn-primary btn-sm pull-right" type="submit" value="{$lang['common_submit']}">
	</fieldset>
</form>
<script type="text/javascript">
//validation and submit handling
$(document).ready(function()
{
    $('#barcode_config_form').validate({
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
		errorLabelContainer: "#barcode_error_message_box",
		wrapper: "li",
		highlight: function (e)	{
			$(e).closest('.form-group').addClass('has-error');
		},
		unhighlight: function (e) {
			$(e).closest('.form-group').removeClass('has-error');
		},

        rules: 
        {
            barcode_width: 
            {
                required:true,
                number:true
            },
            barcode_height: 
            {
                required:true,
                number:true
            },
            barcode_quality: 
            {
                required:true,
                number:true
            },
            barcode_font_size:
            {
                required:true,
                number:true
            },
            barcode_num_in_row:
            {
                required:true,
                number:true
            },
            barcode_page_width:
            {
                required:true,
                number:true
            },
            barcode_page_cellspacing:
            {
                required:true,
                number:true
            }        
        },

        messages: 
        {
            barcode_width:
            {
                required:"{$lang['config_default_barcode_width_required']}",
                number:"{$lang['config_default_barcode_width_number']}"
            },
            barcode_height:
            {
                required:"{$lang['config_default_barcode_height_required']}",
                number:"{$lang['config_default_barcode_height_number']}"
            },
            barcode_quality:
            {
                required:"{$lang['config_default_barcode_quality_required']}",
                number:"{$lang['config_default_barcode_quality_number']}"
            },
            barcode_font_size:
            {
                required:"{$lang['config_default_barcode_font_size_required']}",
                number:"{$lang['config_default_barcode_font_size_number']}"
            },
            barcode_num_in_row:
            {
                required:"{$lang['config_default_barcode_num_in_row_required']}",
                number:"{$lang['config_default_barcode_num_in_row_number']}"
            },
            barcode_page_width:
            {
                required:"{$lang['config_default_barcode_page_width_required']}",
                number:"{$lang['config_default_barcode_page_width_number']}"
            },
            barcode_page_cellspacing:
            {
                required:"{$lang['config_default_barcode_page_cellspacing_required']}",
                number:"{$lang['config_default_barcode_page_cellspacing_number']}"
            }                            
        }
    });
});
</script>