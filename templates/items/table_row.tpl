{nocache}
{foreach $items as $v}
<tr id="{$v['item_id']}">
<td width="2%"><input type="checkbox" value="{$v['item_id']}"/></td>
<td width="10%">{$v['item_number']}</td>
<td width="15%">{$v['name']}</td>
<td width="10%">{$v['category']}</td>
<td width="15%">{$v['company_name']}</td>
<td width="10%">{currency number=$v['cost_price'] thousands_separator=$config['thousands_separator'] decimal_point=$config['decimal_point'] decimals=$config['currency_decimals']}</td>
<td width="10%">{currency number=$v['unit_price'] thousands_separator=$config['thousands_separator'] decimal_point=$config['decimal_point'] decimals=$config['currency_decimals']}</td>
<td width="8%">{quantity_decimals number=$v['quantity'] thousands_separator=$config['thousands_separator'] decimal_point=$config['decimal_point'] decimals=$config['quantity_decimals']}</td>
<td width="10%">{$v['tax_name']}</td>

{if !empty($subgrant["items_update"])}
<td width="8%">{if !empty($v['pic'])}<a data-html="true" data-toggle="popover" data-content="<img src={"{$item_pic_dir}{$v['pic']}"} width=100% />" data-trigger="hover" data-placement="left"><img class="img-responsive" style="max-width: 32px; max-height: 32px;" src="{$item_pic_dir}{$v['pic']}" /></a>{/if}</td>
<td width="3%"><a title="{$lang["items_update"]}" class="modal-dlg modal-btn-submit" href="home.php?act=items&f=get&id={$v['item_id']}"><span class="glyphicon glyphicon-edit"></span></a></td>
{/if}
{foreachelse}
<tr><td colspan="13"><div class="alert alert-dismissible alert-info">{$lang['common_no_persons_to_display']}</div></td></tr>
{/foreach}
{/nocache}