{nocache}
{foreach $stock_locations as $location_id=>$data}
	<div class="form-group form-group-sm" style="{if $data['deleted']}display:none;{else}display:block;{/if}">
		<label class="control-label col-xs-2 required" for="stock_location_{$location_id}">{$lang['config_stock_location']} {$location_id}</label>
		<div class="col-xs-2">
			<input name="stock_location[]" class="stock_location valid_chars form-control input-sm" id="stock_location_{$location_id}" type="text" value="{$data['location_name']}"  sign="{$data['location_name']}" {if $data['deleted']}disabled="disabled"{/if}>
		</div>
		<span class="add_stock_location glyphicon glyphicon-plus" style="padding-top: 0.5em;"></span>
		<span>&nbsp;&nbsp;</span>
		<span class="remove_stock_location glyphicon glyphicon-minus" style="padding-top: 0.5em; display: none;"></span>
	</div>
{/foreach}
{/nocache}