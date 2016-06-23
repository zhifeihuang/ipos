<div id="required_fields_message">{$lang['common_fields_required_message']}</div>
<ul id="error_message_box" class="error_message_box"></ul>
<form action="home.php?act=config&f={nocache}{if isset($id)}update_role&role={$id}{else}save_role{/if}{/nocache}" id="role_form" class="form-horizontal" method="post" accept-charset="utf-8">
	<fieldset>
		<div class="form-group form-group-sm">
			{nocache}{if isset($id)}
			<div align="center">{$id}</div>
			{else}
			<label for="role" class="control-label col-xs-3 required">{$lang['config_role']}</label>
			<div class='col-xs-6'><input class="form-control input-sm" id="role" type="text" name="role" /></div>
			{/if}{/nocache}
		</div>
		
		{include file='role/role.tpl'}
	</fieldset>
</form>
<script type='text/javascript'>
//validation and submit handling
$(document).ready(function() {
	$.validator.setDefaults( { ignore: [] } );
	
	$.validator.addMethod("module", function (value, element) {
		var result = false;
		$("ul#permission_list > li > input").each(function() {
			if ($(this).is(":checked")) result = true;
		} );
		
		return result;
	}, "{$lang['config_grant_required']}");
	
	
	{nocache}{if isset($id)}
	
	{/if}{/nocache}
	
	$("ul#permission_list > li > input").change(function() {
		if (!$(this).is(":checked")) {
			$(this).parent().find("ul > li > input").each(function() {
				if ($(this).is(":checked")) $(this).prop("checked", false);
			} );
		}
	} );
	
	$("ul#permission_list > li > ul > li > input").change(function() {
		if ($(this).is(":checked")) {
			$(this).parent().parent().parent().children("input").each(function() {
				if (!$(this).is(":checked")) $(this).prop("checked", true);
			} );
		}
	} );
	
	var check_submit = false;
	$('#role_form').validate($.extend( {
		submitHandler:function(form) {
			if (check_submit === true) return false;
			
			check_submit = true;
			$(form).ajaxSubmit( {
			success:function(response) {
				dialog_support.hide();
				post_data_form_submit(response, {nocache}{if isset($id)}"update"{else}"save"{/if}{/nocache});
			},
			dataType:'json'
		} );
		},
		
		rules: {
		{nocache}{if !isset($id)}role: "required"{/if}{/nocache}
		},
		messages: {
		{nocache}{if !isset($id)}role: "{$lang['config_role_required']}"{/if}{/nocache}
		}
	}, dialog_support.error));
});
</script>