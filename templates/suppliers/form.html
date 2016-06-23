<div><div id="required_fields_message">{$lang['common_fields_required_message']}</div>

<ul class="error_message_box" id="error_message_box"></ul>

<form class="form-horizontal" id="supplier_form" action="home.php?act=suppliers&f={nocache}{if isset($person['person_id'])}update&id={$person['person_id']}{else}save{/if}{/nocache}" method="post" accept-charset="utf-8" novalidate="novalidate">
	<fieldset id="supplier_basic_info">
		<div class="form-group form-group-sm">
			<label class="required control-label col-xs-3" aria-required="true" for="company_name">{$lang['suppliers_company_name']}</label>			<div class="col-xs-6">
				<input name="company_name" class="form-control input-sm" id="company_name_input" type="text" value="{nocache}{if isset($person['company_name'])}{$person['company_name']}{/if}{/nocache}">
			</div>
		</div>
	
		<div class="form-group form-group-sm">	
			<label class="required control-label col-xs-3" aria-required="true" for="account_number">{$lang['suppliers_account_number']}</label>	<div class="col-xs-6">
			<input name="account_number" class="form-control input-sm" id="account_number" type="text" value="{nocache}{if isset($person['account_number'])}{$person['account_number']}{/if}{/nocache}">
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<label class="control-label col-xs-3" for="agency_name"> {$lang['suppliers_agency_name']}</label>			<div class="col-xs-6">
				<input name="agency_name" class="form-control input-sm" id="agency_name_input" type="text" value="{nocache}{if isset($person['agency_name'])}{$person['agency_name']}{/if}{/nocache}">
			</div>
		</div>

		{include file="person/form_basic_info.tpl"}
	
	</fieldset>
<script type="text/javascript">
//validation and submit handling
$(document).ready(function()
{
	$('#supplier_form').validate($.extend({
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
			company_name: "required",
			first_name: "required",
			last_name: "required",
			email: "email",
    		account_number:
			{
				remote:
				{
					url: "home.php?act=suppliers&f=check_account_number",
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
			company_name: "{$lang['suppliers_company_name_required']}",
			first_name: "{$lang['common_first_name_required']}",
			last_name: "{$lang['common_last_name_required']}",
			email: "{$lang['common_email_invalid_format']}",
			account_number: "{$lang['common_account_number_duplicate']}"
		}
	}, dialog_support.error));
});

</script></div>