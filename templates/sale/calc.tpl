<form class="form-horizontal">
	<div class="form-group">
		<label class="control-label col-xs-3" for="change_total">{$lang['sales_total']}({$config['currency_symbol']})</label>			<div class="col-xs-6">
			<input class="form-control" id="calc_total" type="text" value="{nocache}{$total}{/nocache}" readonly>
		</div>
	</div>
	
	{nocache}
	{foreach $sub as $k => $v}
	<div class="form-group">
		<label class="control-label col-xs-3" for="{$k}">{$lang['sales_payment_type'][{$k}]}({$config['currency_symbol']})</label>			<div class="col-xs-6">
			<input class="form-control" id="{$k}" type="text" value="{nocache}{$v}{/nocache}" readonly>
		</div>
	</div>
	{/foreach}
	{/nocache}
</form>
<script type="text/javascript">
	//validation and submit handling
$(document).ready(function() {
	$('#calc_total').val($.number($('#calc_total').val(), {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
	{nocache}
	{foreach $sub as $k => $v}
	$("#{$k}").val($.number($("#{$k}").val(), {$config['currency_decimals']}, "{$config['decimal_point']}", "{$config['thousands_separator']}"));
	{/foreach}
	{/nocache}
	
	$('.modal').on('shown.bs.modal', function() {
	  $(this).find('input.autofocus').focus();
	});
});
</script>