<div class="form-group form-group-sm">	
	<label for="first_name" class="required control-label col-xs-3">{$lang['common_first_name']}</label>	<div class='col-xs-6'>
		<input type="text" name="first_name" value="{nocache}{if isset($person['first_name'])}{$person['first_name']}{/if}{/nocache}" id="first_name" class="form-control input-sm" />
	</div>
</div>

<div class="form-group form-group-sm">	
	<label for="last_name" class="required control-label required col-xs-3">{$lang['common_last_name']}</label>	<div class='col-xs-6'>
		<input type="text" name="last_name" value="{nocache}{if isset($person['last_name'])}{$person['last_name']}{/if}{/nocache}" id="last_name" class="form-control input-sm"  />
	</div>
</div>

<div class="form-group form-group-sm">	
	<label for="gender" class="control-label col-xs-3">{$lang['common_gender']}</label>	<div class="col-xs-4">
		<label class="radio-inline">
			<input type="radio" name="gender" value="1" id="gender" {nocache}{if isset($person['gender']) && $person['gender'] == 1}checked="checked"{/if}{/nocache}  />
{$lang['common_gender_male']}		</label>
		<label class="radio-inline">
			<input type="radio" name="gender" value="0" id="gender" {nocache}{if isset($person['gender']) && $person['gender'] == 0}checked="checked"{/if}{/nocache} />
 {$lang['common_gender_female']}		</label>

	</div>
</div>

<div class="form-group form-group-sm">	
	<label for="email" class="control-label col-xs-3">{$lang['common_email']}</label>	<div class='col-xs-6'>
		<div class="input-group">
			<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-envelope"></span></span>
			<input type="text" name="email" value="{nocache}{if isset($person['email'])}{$person['email']}{/if}{/nocache}" id="email" class="form-control input-sm"  />
		</div>
	</div>
</div>

<div class="form-group form-group-sm">	
	<label for="phone_number" class="control-label col-xs-3">{$lang['common_phone_number']}</label>	<div class='col-xs-6'>
		<div class="input-group">
			<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-phone-alt"></span></span>
			<input type="text" name="phone_number" value="{nocache}{if isset($person['phone_number'])}{format_phone_number phone=$person['phone_number']}{/if}{/nocache}" id="phone_number" class="form-control input-sm"  />
		</div>
	</div>
</div>

<div class="form-group form-group-sm">	
	<label for="address_1" class="control-label col-xs-3">{$lang['common_address_1']}</label>	<div class='col-xs-6'>
		<input type="text" name="address_1" value="{nocache}{if isset($person['address_1'])}{$person['address_1']}{/if}{/nocache}" id="address_1" class="form-control input-sm"  />
	</div>
</div>

<div class="form-group form-group-sm">	
	<label for="address_2" class="control-label col-xs-3">{$lang['common_address_2']}</label>	<div class='col-xs-6'>
		<input type="text" name="address_2" value="{nocache}{if isset($person['address_2'])}{$person['address_2']}{/if}{/nocache}" id="address_2" class="form-control input-sm"  />
	</div>
</div>

<div class="form-group form-group-sm">	
	<label for="city" class="control-label col-xs-3">{$lang['common_city']}</label>	<div class='col-xs-6'>
		<input type="text" name="city" value="{nocache}{if isset($person['city'])}{$person['city']}{/if}{/nocache}" id="city" class="form-control input-sm"  />
	</div>
</div>

<div class="form-group form-group-sm">	
	<label for="state" class="control-label col-xs-3">{$lang['common_state']}</label>	<div class='col-xs-6'>
		<input type="text" name="state" value="{nocache}{if isset($person['state'])}{$person['state']}{/if}{/nocache}" id="state" class="form-control input-sm"  />
	</div>
</div>

<div class="form-group form-group-sm">	
	<label for="zip" class="control-label col-xs-3">{$lang['common_zip']}</label>	<div class='col-xs-6'>
		<input type="text" name="zip" value="{nocache}{if isset($person['zip'])}{$person['zip']}{/if}{/nocache}" id="postcode" class="form-control input-sm"  />
	</div>
</div>

<div class="form-group form-group-sm">	
	<label for="country" class="control-label col-xs-3">{$lang['common_country']}</label>	<div class='col-xs-6'>
		<input type="text" name="country" value="{nocache}{if isset($person['country'])}{$person['country']}{/if}{/nocache}" id="country" class="form-control input-sm"  />
	</div>
</div>

<div class="form-group form-group-sm">	
	<label for="comments" class="control-label col-xs-3">{$lang['common_comments']}</label>	<div class='col-xs-6'>
		<textarea name="comments" cols="40" rows="3" id="comments" class="form-control input-sm" value="{nocache}{if isset($person['comments'])}{$person['comments']}{/if}{/nocache}" ></textarea>
	</div>
</div>

<script type='text/javascript' language="javascript">
//validation and submit handling
$(document).ready(function()
{
{*	nominatim.init({
		fields : {
			postcode : {
				dependencies :  ["postcode", "city", "state", "country"],
				response : {
					field : 'postalcode',
					format: ["postcode", "village|town|hamlet|city_district|city", "state", "country"]
				}
			},

			city : {
				dependencies :  ["postcode", "city", "state", "country"],
				response : {
					format: ["postcode", "village|town|hamlet|city_district|city", "state", "country"]
				}
			},

			state : {
				dependencies :  ["state", "country"]
			},

			country : {
				dependencies :  ["state", "country"]
			}
		},
		language : "{$language}"
	});*}
});
</script>