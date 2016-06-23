{nocache}
<tr>
<td><a onclick="return delete_item_kit_row(this);" value="{$item['item_id']}"><span class='glyphicon glyphicon-trash'></span></a></td>
<td>{$item['name']}</td>
<td><input type="text" class="form-control input-sm" id="kit_item_{$item['item_id']}" name="item_kit_item[{$item['item_id']}]" value="0"></td>
<td class="sr-only">{$item['cost_price']}</td>
<td class="sr-only">{$item['cost_discount']}</td>
<td class="sr-only">{$item['unit_price']}</td>
</tr>
{/nocache}