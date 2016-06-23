<div id="required_fields_message">{$lang['common_fields_required_message']}</div>

<ul class="error_message_box" id="error_message_box"></ul>

<form class="form-horizontal" id="employee_form" action="home.php?act=employees&f={nocache}{if isset($person['person_id'])}update&id={$person['person_id']}{else}save{/if}{/nocache}" method="post" accept-charset="utf-8" novalidate="novalidate">
	<ul class="nav nav-tabs nav-justified" data-tabs="tabs">
		<li class="active" role="presentation">
			<a aria-expanded="true" href="#employee_basic_info" data-toggle="tab">{$lang['employees_basic_information']}</a>
		</li>
		<li role="presentation">
			<a aria-expanded="false" href="#employee_login_info" data-toggle="tab">{$lang['employees_login_info']}</a>
		</li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane fade active in" id="employee_basic_info">
			<fieldset>
			<div class="form-group form-group-sm">	
				<label class="required control-label col-xs-3" aria-required="true" for="role">{$lang['employees_role']}</label>					<div class="col-xs-6">
					{nocache}
					{if !empty($role_arr)}
					<select name="role" class="form-control input-sm">
					{foreach $role_arr as $val}
						<option value="{$val}" {if (isset($role) && $role == $val)}selected="selected"{/if}>{$val}</option>
					{/foreach}
</select>
					{else}
					<p class="alert-danger">{$lang['employees_no_role']}</p>
					{/if}
					{/nocache}
				</div>
			</div>
			
			{include file="person/form_basic_info.tpl"}
			</fieldset>
		</div>

		<div class="tab-pane" id="employee_login_info">
			<fieldset>
				<div class="form-group form-group-sm">	
					<label class="required control-label col-xs-3" aria-required="true" for="username">{$lang['employees_username']}</label>					<div class="col-xs-6">
						<div class="input-group">
							<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-user"></span></span>
							<input name="username" class="form-control input-sm" id="username" type="text" value="{nocache}{if isset($person['usrname'])}{$person['usrname']}{/if}{/nocache}">
						</div>
					</div>
				</div>
				
				<div class="form-group form-group-sm">	
					<label class="col-xs-3 control-label {nocache}{if empty($person['person_id'])}required{/if}{/nocache}" for="password">{$lang['employees_password']}</label>					<div class="col-xs-6">
						<div class="input-group">
							<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-asterisk"></span></span>
							<input name="password" class="form-control input-sm" id="password" type="password" value="">
						</div>
					</div>
				</div>

				<div class="form-group form-group-sm">	
				<label class="col-xs-3 control-label" for="repeat_password">{$lang['employees_repeat_password']}</label>					<div class="col-xs-6">
						<div class="input-group">
							<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-asterisk"></span></span>
							<input name="repeat_password" class="form-control input-sm" id="repeat_password" type="password" value="">
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	</form>
<script type="text/javascript">
//validation and submit handling
$(document).ready(function()
{
	$.validator.setDefaults({ ignore: [] });
	
	$('#employee_form').validate($.extend( {
		submitHandler:function(form)
		{
			$(form).ajaxSubmit( {
				success:function(response)
				{
					dialog_support.hide();
					post_data_form_submit(response, {nocache}{if isset($person['person_id'])}"update"{else}"save"{/if}{/nocache});
				},
				dataType:'json'
			} );
		},
		rules:
		{
			role: "required",
			first_name: "required",
			last_name: "required",
    		username:
			{
				required: true,
				minlength: 5,
				remote:
				{
					url: "home.php?act=employees&f=check_username",
					type: "post",
					data:
					{
						"person_id": "{nocache}{if isset($person['person_id'])}{$person['person_id']}{/if}{/nocache}",
						"username": function()
						{
							return $("#username").val();
						}
					}
				}
			},
			password:
			{
				{nocache}{if empty($person['person_id'])}required:true,{/if}{/nocache}
				minlength: 8
			},	
			repeat_password:
			{
 				equalTo: "#password"
			},
    		email: "email"
   		},
		messages: 
		{
     		role: "{$lang['employees_permission_required']}",
     		first_name: "{$lang['common_first_name_required']}",
     		last_name: "{$lang['common_last_name_required']}",
     		username: 
			{
				required: "{$lang['employees_username_required']}",
				minlength: "{$lang['employees_username_minlength']}",
				remote: "{$lang['employees_username_duplicate']}"
			},
			password:
			{
				{nocache}{if empty($person['person_id'])}required:"{$lang['employees_password_required']}",{/if}{/nocache}
				minlength: "{$lang['employees_password_minlength']}"
			},
			repeat_password:
			{
				equalTo: "{$lang['employees_password_must_match']}"
     		},
     		email: "{$lang['common_email_invalid_format']}"
		}
	}, dialog_support.error));
} );
</script>