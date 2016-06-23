{extends file='header.tpl'}
{block name="container"}
<div class="row">
<div class="col-xs-2">
<ul class="nav nav-tabs nav-stacked" data-tabs="tabs">
    <li class="active" role="presentation">
        <a data-toggle="tab" href="#general">{$lang['config_general']}</a>
    </li>
    <li role="presentation">
        <a data-toggle="tab" href="#locale">{$lang['config_locale']}</a>
    </li>
    <li role="presentation">
        <a data-toggle="tab" href="#barcode">{$lang['config_barcode']}</a>
    </li>
    <li role="presentation">
        <a data-toggle="tab" href="#receipt">{$lang['config_receipt']}</a>
    </li>
{* not support
	{if !empty($subgrant['stock'])}
    <li role="presentation">
        <a data-toggle="tab" href="#stock">{$lang['config_stock']}</a>
    </li>
	{/if}
*}
	{nocache}
	{if !empty($subgrant['grants'])}
    <li role="presentation">
        <a data-toggle="tab" href="#role">{$lang['config_role']}</a>
    </li>
	{/if}
	{/nocache}
</ul>
</div>

<div class="tab-content col-xs-10">
    <div class="tab-pane fade in  active" id="general">
		{include "config/general.tpl"}
    </div>
    <div class="tab-pane" id="locale">
		{include "config/locale.tpl"}
    </div>
    <div class="tab-pane" id="barcode">
		{include "config/barcode.tpl"}
    </div>
    <div class="tab-pane" id="receipt">
		{include "config/receipt.tpl"}
    </div>
{* not support
	{if !empty($subgrant['stock'])}
    <div class="tab-pane" id="stock">
		{include "config/stock.tpl"}
    </div>
	{/if}
*}
	{nocache}
	{if !empty($subgrant['grants'])}
    <div class="tab-pane" id="role">
		{include "config/role.tpl"}
    </div>
	{/if}
	{/nocache}
</div>
</div>
{/block}