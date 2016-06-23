<div id="required_fields_message">{$lang['common_fields_required_message']}</div>

<ul id="error_message_box" class="error_message_box"></ul>

<form action="home.php?act=customers&f={nocache}{if isset($person['person_id'])}update&id={$person['person_id']}{else}save{/if}{/nocache}" id="customer_form" class="form-horizontal" method="post" accept-charset="utf-8">
	<fieldset id="customer_basic_info">
	
		<div class="form-group form-group-sm">
			<label for="account_number" class="required control-label col-xs-3" aria-required="true">{$lang['customers_account_number']}</label>			<div class='col-xs-6'>
				<input type="text" name="account_number" value="{nocache}{if isset($person['account_number'])}{$person['account_number']}{/if}{/nocache}" id="account_number" class="account_number form-control"  />
			</div>
		</div>
		
		{include file='person/form_basic_info.tpl'}
		
		<div class="form-group form-group-sm">
			<label for="company_name" class="control-label col-xs-3">{$lang['customers_company_name']}</label>			<div class='col-xs-6'>
				<input type="text" name="company_name" value="{nocache}{if isset($person['company_name'])}{$person['company_name']}{/if}{/nocache}" class="form-control input-sm"  />
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label for="taxable" class="control-label col-xs-3">{$lang['customers_taxable']}</label>			<div class='col-xs-1'>
				<input type="checkbox" name="taxable" value="1" {nocache}{if !isset($person['taxable']) || $person['taxable']==1}checked="checked"{/if}{/nocache}  />
			</div>
		</div>
	</fieldset>
</form>
<script type='text/javascript'>

//validation and submit handling
$(document).ready(function()
{

	$('#customer_form').validate($.extend({
		submitHandler:function(form)
		{
			$(form).ajaxSubmit({
			success:function(response)
			{
				dialog_support.hide();
				post_data_form_submit(response, {nocache}{if isset($person['person_id'])}"update"{else}"save"{/if}{/nocache});
			},
			dataType:'json'
		});

		},
		rules:
		{
			first_name: "required",
			last_name: "required",
    		email: "email",
    		account_number:
			{
				remote:
				{
					url: "home.php?act=customers&f=check_account_number",
					type: "post",
					data:
					{
						"person_id" : "{nocache}{if isset($person['person_id'])}{$person['person_id']}{/if}{/nocache}",
						"account_number" : function()
						{
							return $("#account_number").val();
						}
					}
				}
			}
   		},
		messages: 
		{
     		first_name: "{$lang['common_first_name_required']}",
     		last_name: "{$lang['common_last_name_required']}",
     		email: "{$lang['common_email_invalid_format']}",
			account_number: "{$lang['common_account_number_duplicate']}"
		}
	}, dialog_support.error));
});
</script>