<ul class="error_message_box" id="recv_error_message_box"></ul>

<form class="form-horizontal" id="recv_form" action="home.php?act=receivings&f=receive" method="post" accept-charset="utf-8">
	<fieldset>
		<div class="form-group">
			<div class="col-xs-3">
				<input class="form-control input-sm" placeholder="{$lang['recvs_recv_date']}" id="order_date" type="text" readonly>
			</div>
			<div class="col-xs-3">
				<input class="form-control input-sm" placeholder="{$lang['recvs_order_number']}" id="order_number1" type="text" readonly>
				<input name="id" id="order_id" type="hidden">
			</div>
			<div class="col-xs-3">
				<input class="form-control input-sm" placeholder="{$lang['recvs_emp']}" id="order_emp" type="text" readonly>
			</div>
			<div class="col-xs-3">
				<input class="form-control input-sm" placeholder="{$lang['recvs_total']}({$config['currency_symbol']})" id="order_total" type="text" readonly>
			</div>
		</div>
	
		<div class="form-group" id="table_action_header1">
			<ul>
				<li class="pull-right">
					<input class="form-control input-sm ui-autocomplete-input" placeholder="{$lang['recvs_find_order_number']}" id="recv_find_number" type="text">
				</li>
			</ul>
		</div>
		
		<div id="table_holder1">
			<table class="tablesorter table table-striped table-hover" id="recv_items">
				<thead>
					<tr>
						<th width="15%">{$lang['items_item_number']}</th>
						<th width="15%">{$lang['items_name']}</th>
						<th width="15%">{$lang['common_company_name']}</th>
						<th width="10%">{$lang['recvs_order_quantity']}</th>
						<th width="10%">{$lang['recvs_recv_quantity']}</th>
						<th width="10%">{$lang['recvs_cost_price']}({$config['currency_symbol']})</th>
						<th width="10%">{$lang['recvs_discount']}</th>
						<th width="15%">{$lang['recvs_total']}({$config['currency_symbol']})</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</fieldset>
	<input class="btn btn-primary btn-sm pull-right" type="submit" value="{$lang['common_submit']}">
</form>