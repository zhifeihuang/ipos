{extends file='header.tpl'}
{block name="container"}
<div class="row">
<div class="col-xs-2">
<ul class="nav nav-tabs nav-stacked" id='config'>
    <li><a data-toggle="tab" href="#general">{$lang['config_general']}</a></li>
    <li><a data-toggle="tab" href="#locale">{$lang['config_locale']}</a></li>
    <li><a data-toggle="tab" href="#barcode">{$lang['config_barcode']}</a></li>
    <li><a data-toggle="tab" href="#receipt">{$lang['config_receipt']}</a></li>
{* not support
	{if !empty($subgrant['stock'])}
    <li><a data-toggle="tab" href="#stock">{$lang['config_stock']}</a></li>
	{/if}
*}
	{nocache}
	{if !empty($subgrant['grants'])}
    <li><a data-toggle="tab" href="#role">{$lang['config_role']}</a></li>
	{/if}
	{/nocache}
</ul>
</div>
<div class="tab-content col-xs-10">
    <div class="tab-pane fade" id="general">{include "config/general.tpl"}</div>
    <div class="tab-pane fade" id="locale">{include "config/locale.tpl"}</div>
    <div class="tab-pane fade" id="barcode">{include "config/barcode.tpl"}</div>
    <div class="tab-pane fade" id="receipt">{include "config/receipt.tpl"}</div>
{* not support
	{if !empty($subgrant['stock'])}
    <div class="tab-pane fade" id="stock">{include "config/stock.tpl"}</div>
	{/if}
*}
	{nocache}
	{if !empty($subgrant['grants'])}
    <div class="tab-pane fade" id="role">{include "config/role.tpl"}</div>
	{/if}
	{/nocache}
</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('#config :first-child>a').click();
});
</script>
{/block}