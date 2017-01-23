<ul id="permission_list">
	<li><input class="module" name="grants[]" type="checkbox" {nocache}{if !empty($role["reports"])}checked="checked"{/if}{/nocache} value="reports"><span class="medium">{$lang["role_reports"]}</span>
		<ul class="row">
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["reports_giftcard"])}checked="checked"{/if}{/nocache} value="reports_suppliers"><span class="small">{$lang["role_reports_giftcard"]}</span></li>
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["reports_suppliers"])}checked="checked"{/if}{/nocache} value="reports_suppliers"><span class="small">{$lang["role_reports_suppliers"]}</span></li>
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["reports_categories"])}checked="checked"{/if}{/nocache} value="reports_categories"><span class="small">{$lang["role_reports_categories"]}</span></li>
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["reports_payments"])}checked="checked"{/if}{/nocache} value="reports_payments"><span class="small">{$lang["role_reports_payments"]}</span></li></ul></li>

	<li><input class="module" name="grants[]" type="checkbox" {nocache}{if !empty($role["customers"])}checked="checked"{/if}{/nocache} value="customers"><span class="medium">{$lang["role_customers"]}</span>
		<ul class="row">
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["customers_delete"])}checked="checked"{/if}{/nocache} value="customers_delete"><span class="small">{$lang["role_customers_delete"]}</span></li>
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["customers_update"])}checked="checked"{/if}{/nocache} value="customers_update"><span class="small">{$lang["role_customers_update"]}</span></li>
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["customers_insert"])}checked="checked"{/if}{/nocache} value="customers_insert"><span class="small">{$lang["role_customers_insert"]}</span></li></ul></li>

	<li><input class="module" name="grants[]" type="checkbox" {nocache}{if !empty($role["employees"])}checked="checked"{/if}{/nocache} value="employees"><span class="medium">{$lang["role_employees"]}</span>
		<ul class="row">
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["employees_delete"])}checked="checked"{/if}{/nocache} value="employees_delete"><span class="small">{$lang["role_employees_delete"]}</span></li>
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["employees_update"])}checked="checked"{/if}{/nocache} value="employees_update"><span class="small">{$lang["role_employees_update"]}</span></li>
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["employees_insert"])}checked="checked"{/if}{/nocache} value="employees_insert"><span class="small">{$lang["role_employees_insert"]}</span></li></ul></li>

	<li><input class="module" name="grants[]" type="checkbox" {nocache}{if !empty($role["giftcards"])}checked="checked"{/if}{/nocache} value="giftcards"><span class="medium">{$lang["role_giftcards"]}</span>
		<ul class="row">
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["giftcards_delete"])}checked="checked"{/if}{/nocache} value="giftcards_delete"><span class="small">{$lang["role_giftcards_delete"]}</span></li>
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["giftcards_update"])}checked="checked"{/if}{/nocache} value="giftcards_update"><span class="small">{$lang["role_giftcards_update"]}</span></li>
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["giftcards_insert"])}checked="checked"{/if}{/nocache} value="giftcards_insert"><span class="small">{$lang["role_giftcards_insert"]}</span></li></ul></li>

	<li><input class="module" name="grants[]" type="checkbox" {nocache}{if !empty($role["items"])}checked="checked"{/if}{/nocache} value="items"><span class="medium">{$lang["role_items"]}</span>
		<ul class="row">
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["items_delete"])}checked="checked"{/if}{/nocache} value="items_delete"><span class="small">{$lang["role_items_delete"]}</span></li>
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["items_update"])}checked="checked"{/if}{/nocache} value="items_update"><span class="small">{$lang["role_items_update"]}</span></li>
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["items_insert"])}checked="checked"{/if}{/nocache} value="items_insert"><span class="small">{$lang["role_items_insert"]}</span></li></ul></li>

	<li><input class="module" name="grants[]" type="checkbox" {nocache}{if !empty($role["item_kits"])}checked="checked"{/if}{/nocache} value="item_kits"><span class="medium">{$lang["role_item_kits"]}</span>
		<ul class="row">
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["item_kits_delete"])}checked="checked"{/if}{/nocache} value="item_kits_delete"><span class="small">{$lang["role_item_kits_delete"]}</span></li>
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["item_kits_update"])}checked="checked"{/if}{/nocache} value="item_kits_update"><span class="small">{$lang["role_item_kits_update"]}</span></li>
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["item_kits_insert"])}checked="checked"{/if}{/nocache} value="item_kits_insert"><span class="small">{$lang["role_item_kits_insert"]}</span></li></ul></li>

	<li><input class="module" name="grants[]" type="checkbox" {nocache}{if !empty($role["receivings"])}checked="checked"{/if}{/nocache} value="receivings"><span class="medium">{$lang["role_receivings"]}</span>
		<ul class="row">
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["receivings_delete"])}checked="checked"{/if}{/nocache} value="receivings_delete"><span class="small">{$lang["role_receivings_delete"]}</span></li>
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["receivings_update"])}checked="checked"{/if}{/nocache} value="receivings_update"><span class="small">{$lang["role_receivings_update"]}</span></li>
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["receivings_insert"])}checked="checked"{/if}{/nocache} value="receivings_insert"><span class="small">{$lang["role_receivings_insert"]}</span></li></ul></li>

	<li><input class="module" name="grants[]" type="checkbox" {nocache}{if !empty($role["sales"])}checked="checked"{/if}{/nocache} value="sales"><span class="medium">{$lang["role_sales"]}</span>
		<ul class="row">
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["sales_delete"])}checked="checked"{/if}{/nocache} value="sales_delete"><span class="small">{$lang["role_sales_delete"]}</span></li>
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["sales_update"])}checked="checked"{/if}{/nocache} value="sales_update"><span class="small">{$lang["role_sales_update"]}</span></li>
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["sales_insert"])}checked="checked"{/if}{/nocache} value="sales_insert"><span class="small">{$lang["role_sales_insert"]}</span></li></ul></li>

	<li><input class="module" name="grants[]" type="checkbox" {nocache}{if !empty($role["suppliers"])}checked="checked"{/if}{/nocache} value="suppliers"><span class="medium">{$lang["role_suppliers"]}</span>
		<ul class="row">
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["suppliers_delete"])}checked="checked"{/if}{/nocache} value="suppliers_delete"><span class="small">{$lang["role_suppliers_delete"]}</span></li>
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["suppliers_update"])}checked="checked"{/if}{/nocache} value="suppliers_update"><span class="small">{$lang["role_suppliers_update"]}</span></li>
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["suppliers_insert"])}checked="checked"{/if}{/nocache} value="suppliers_insert"><span class="small">{$lang["role_suppliers_insert"]}</span></li></ul></li>

	<li><input class="module" name="grants[]" type="checkbox" {nocache}{if !empty($role["config"])}checked="checked"{/if}{/nocache} value="config"><span class="medium">{$lang["role_config"]}</span></li>
	<li><input class="module" name="grants[]" type="checkbox" {nocache}{if !empty($role["stock"])}checked="checked"{/if}{/nocache} value="stock"><span class="medium">{$lang["role_stock"]}</span></li>
	<li><input class="module" name="grants[]" type="checkbox" {nocache}{if !empty($role["grants"])}checked="checked"{/if}{/nocache} value="grants"><span class="medium">{$lang["role_grants"]}</span></li>
</ul>
