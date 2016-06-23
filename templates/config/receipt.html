<form class="form-horizontal" id="receipt_config_form" action="home.php?act=config&f=receipt" method="post" accept-charset="utf-8">
	<fieldset>
		<div id="required_fields_message">{$lang['common_fields_required_message']}</div>
		<ul class="error_message_box" id="receipt_error_message_box"></ul>

		<div class="form-group form-group-sm">	
		<label class="control-label col-xs-2" for="invoice_default_comments">{$lang['config_invoice_default_comments']}</label>				<div class="col-xs-5">
			<textarea name="invoice_default_comments" class="form-control input-sm" id="invoice_default_comments" rows="2" cols="40">{$config['invoice_default_comments']}</textarea>
			</div>
		</div>

		<div class="form-group form-group-sm">    
			<label class="control-label col-xs-2 required" for="sales_invoice_format">{$lang['config_sales_invoice_format']}</label>				<div class="col-xs-2">
				<input name="sales_invoice_format" class="form-control input-sm" id="sales_invoice_format" type="text" value="{$config['sales_invoice_format']}">
			</div>
		</div>

		<div class="form-group form-group-sm">    
			<label class="control-label col-xs-2 required" for="recv_invoice_format">{$lang['config_recv_invoice_format']}</label>				<div class="col-xs-2">
				<input name="recv_invoice_format" class="form-control input-sm" id="recv_invoice_format" type="text" value="{$config['recv_invoice_format']}">
			</div>
		</div>
		
		<div class="form-group form-group-sm">    
			<label class="control-label col-xs-2 required" for="order_invoice_format">{$lang['config_order_invoice_format']}</label>				<div class="col-xs-2">
				<input name="order_invoice_format" class="form-control input-sm" id="order_invoice_format" type="text" value="{$config['order_invoice_format']}">
			</div>
		</div>
		
		<div class="form-group form-group-sm">    
			<label class="control-label col-xs-2 required" for="ret_invoice_format">{$lang['config_ret_invoice_format']}</label>				<div class="col-xs-2">
				<input name="ret_invoice_format" class="form-control input-sm" id="ret_invoice_format" type="text" value="{$config['ret_invoice_format']}">
			</div>
		</div>
		
		<div class="form-group form-group-sm">	
		<label class="control-label col-xs-2" for="receipt_show_taxes">{$lang['config_receipt_show_taxes']}</label>				<div class="col-xs-1">
			<input name="receipt_show_taxes" id="receipt_show_taxes" type="checkbox" {if $config['receipt_show_taxes']}checked="checked"{/if} value="1">
			</div>
		</div>
		
		<div class="form-group form-group-sm">	
		<label class="control-label col-xs-2" for="show_total_discount">{$lang['config_show_total_discount']}</label>				<div class="col-xs-1">
			<input name="show_total_discount" id="show_total_discount" type="checkbox" {if $config['show_total_discount']}checked="checked"{/if} value="1">
			</div>
		</div>

		<div class="form-group form-group-sm">	
		<label class="control-label col-xs-2" for="print_silently">{$lang['config_print_silently']}</label>				<div class="col-xs-1">
			<input name="print_silently" id="print_silently" type="checkbox" {if $config['print_silently']}checked="checked"{/if} value="1">
			</div>
		</div>

		<div class="form-group form-group-sm">	
		<label class="control-label col-xs-2" for="print_header">{$lang['config_print_header']}</label>				<div class="col-xs-1">
			<input name="print_header" id="print_header" type="checkbox" {if $config['print_header']}checked="checked"{/if} value="1">
			</div>
		</div>

		<div class="form-group form-group-sm">	
		<label class="control-label col-xs-2" for="print_footer">{$lang['config_print_footer']}</label>				<div class="col-xs-1">
			<input name="print_footer" id="print_footer" type="checkbox" {if $config['print_footer']}checked="checked"{/if} value="1">
			</div>
		</div>

		<input class="btn btn-primary btn-sm pull-right" type="submit" value="{$lang['common_submit']}">
	</fieldset>
</form>
<script type="text/javascript">
//validation and submit handling
$(document).ready(function() {
	var sale_file = {$sale_file};
	var recv_file = {$recv_file};
	var order_file = {$order_file};
	var ret_file = {$ret_file};
	
	$("#sales_invoice_format").autocomplete( { source:sale_file, autoFocus: false, delay:500 } );
	$("#recv_invoice_format").autocomplete( { source:recv_file, autoFocus: false, delay:500 } );
	$("#order_invoice_format").autocomplete( { source:order_file, autoFocus: false, delay:500 } );
	$("#ret_invoice_format").autocomplete( { source:ret_file, autoFocus: false, delay:500 } );

	$('#receipt_config_form').validate({
		submitHandler: function(form) {
			$(form).ajaxSubmit({
				success: function(response) {
					if(response.success) {
						set_feedback(response.msg, 'alert alert-dismissible alert-success', false);		
					} else {
						set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);		
					}
				},
				dataType:'json'
			});
		},

		errorClass: "has-error",
		errorLabelContainer: "#receipt_error_message_box",
		wrapper: "li",
		highlight: function (e)	{
			$(e).closest('.form-group').addClass('has-error');
		},
		unhighlight: function (e) {
			$(e).closest('.form-group').removeClass('has-error');
		},
		rules:  {
			sales_invoice_format: { 
				required:true,
				remote: {
					url: "home.php?act=config&f=check_file",
					type: "post",
					data: {
						"file" : function() {
							return 'sale/' + $("#sales_invoice_format").val();
						}
					}
				}
			},
			recv_invoice_format: { 
				required:true,
				remote: {
					url: "home.php?act=config&f=check_file",
					type: "post",
					data: {
						"file" : function() {
							return 'recv/' + $("#recv_invoice_format").val();
						}
					}
				}
			},
			order_invoice_format: { 
				required:true,
				remote: {
					url: "home.php?act=config&f=check_file",
					type: "post",
					data: {
						"file" : function() {
							return 'order/' + $("#order_invoice_format").val();
						}
					}
				}
			},
			ret_invoice_format: { 
				required:true,
				remote: {
					url: "home.php?act=config&f=check_file",
					type: "post",
					data: {
						"file" : function() {
							return 'return/' + $("#ret_invoice_format").val();
						}
					}
				}
			}
   		},
		messages: {
			sales_invoice_format: { required:"{$lang['config_sales_invoice_required']}", remote:"{$lang['common_file_not_exist']}" },
			recv_invoice_format: { required:"{$lang['config_recv_invoice_required']}", remote:"{$lang['common_file_not_exist']}"  },
			order_invoice_format: { required:"{$lang['config_order_invoice_required']}", remote:"{$lang['common_file_not_exist']}"  },
			ret_invoice_format: { required:"{$lang['config_ret_invoice_required']}", remote:"{$lang['common_file_not_exist']}"  }
		}
	});
});
</script>