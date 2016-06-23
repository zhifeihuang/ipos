<div id="required_fields_message">{$lang['common_fields_required_message']}</div>
<ul class="error_message_box" id="error_message_box"></ul>

<form class="form-horizontal" id="giftcard_form" action="home.php?act=giftcards&f={nocache}{if isset($gift['giftcard_id'])}update&id={$gift['giftcard_id']}{else}save{/if}{/nocache}" method="post" accept-charset="utf-8">
	<fieldset>
		<div class="form-group form-group-sm">
			<label class="control-label col-xs-3 required" for="person_name">{$lang['giftcards_person_id']}</label>			<div class="col-xs-6">
				<input class="form-control input-sm ui-autocomplete-input" id="person_name" type="text" value="{nocache}{if isset($gift['person_name'])}{$gift['person_name']}{/if}{/nocache}" {nocache}{if isset($gift['giftcard_id'])}readonly{/if}{/nocache}>
<input name="person_id" class="sr-only required" id="person_id" type="text" value="{nocache}{if isset($gift['person_id'])}{$gift['person_id']}{/if}{/nocache}">
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label class="required control-label col-xs-3" for="number">{$lang['giftcards_giftcard_number']}</label>			<div class="col-xs-6">
				<input name="number" class="form-control input-sm" id="number" type="text" value="{nocache}{if isset($gift['giftcard_number'])}{$gift['giftcard_number']}{/if}{/nocache}">
			</div>
		</div>

		<div class="form-group form-group-sm">
			<label class="required control-label col-xs-3" for="value">{$lang['giftcards_card_value']}({$config['currency_symbol']})</label>			<div class="col-xs-6">
				<div class="input-group input-group-sm">
					<input name="value" class="form-control input-sm" id="value" type="text" value="{nocache}{if isset($gift['val'])}{$gift['val']}{/if}{/nocache}">
				</div>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">
//validation and submit handling
$(document).ready(function() {
	$("#person_name").autocomplete({
		source: 'home.php?act=customers&f=suggest_gift',
		autoFocus: false,
		delay:500,
		appendTo: ".modal-content",
		select: function(e, ui) {
			var data = ui.item.label.split(',');
			if (!data || data.length == 0)
				return false;
				
			$("#person_name").val(data[0]);
			$("#person_id").val(ui.item.value);
			return false;
		}
	});
	
	$('#giftcard_form').validate($.extend({
		submitHandler:function(form) {
			$(form).ajaxSubmit({
				success: function(response)	{
						dialog_support.hide();
						post_data_form_submit(response, {nocache}{if isset($gift['giftcard_id'])}"update"{else}"save"{/if}{/nocache});
				},
				dataType: 'json'
			});
		},
		rules:
		{
			person_id: {
				required:true
			},
			number: {
				required:true,
				number:true,
				remote: {
					url: "home.php?act=giftcards&f=check_number",
					type: "post",
					data:
					{
						"id" : "{nocache}{if isset($gift['giftcard_id'])}{$gift['giftcard_id']}{/if}{/nocache}",
						"number" : function()
						{
							return $("#number").val();
						}
					}
				}
			},
			value: {
				required:true,
				number:true
			}
   		},
		messages:
		{
			person_id: {
				required:"{$lang['giftcards_person_required']}",
			},
			number: {
				required:"{$lang['giftcards_number_required']}",
				number:"{$lang['giftcards_number']}",
				remote:"{$lang['giftcards_number_duplicate']}"
			},
			value: {
				required:"{$lang['giftcards_value_required']}",
				number:"{$lang['giftcards_value']}"
			}
		}
	}, dialog_support.error));
});
</script>