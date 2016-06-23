<?php
require_once('../libs/ipos_setup.php');
require_once('../libs/employee.php');
require_once('../libs/config.php');
require_once('../libs/customer.php');
require_once('../libs/supplier.php');
require_once('../libs/items.php');
require_once('../libs/item_kits.php');
require_once('../libs/receive.php');
require_once('../libs/giftcard.php');
require_once('../libs/sale.php');
require_once('../libs/report.php');

$ipos = new smarty_ipos;
if (!$ipos->db) {
	$ipos->langauge(array('common'));
	$ipos->err_page($ipos->err);
	return;
}

if (!($person_id = $ipos->session->usrdata('person_id'))) {
	header('Location: /index.php');
	exit();
}

$ipos->language(array('common', 'module'));
$ipos->assign('name', $ipos->session->usrdata('name'));

$grant = $ipos->session->usrdata('grant');
if ($grant === false) {
	$emp = new employee($ipos->db, $person_id);
	$grant['grant'] = $emp->grant;
	$grant['permission'] = $emp->get_all_permissions();
	$grant['allowed_module'] = $emp->get_allowed_modules();
	$grant['default_permission'] = $emp->get_default_permission();
	$ipos->session->param(array('grant' => $grant));
}
$ipos->assign('allowed_module', $grant['allowed_module']);

$c = $ipos->session->usrdata('config');
if ($c === false) {
	$config = new config($ipos->db, $grant['grant'], $grant['permission']);
	$ipos->session->param(array('config' => $config->get_all()));
}
$ipos->assign('config', $c);

$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : 'view';
switch ($act) {
case 'config':
	$ipos->language(array('config', 'items'));
	$config = new config($ipos->db, $grant['grant'], $grant['permission']);
	$func = isset($_REQUEST['f']) ? $_REQUEST['f'] : 'view';
	if ($config->has_grant($func)) {
		switch ($func) {
		case 'view': $config->view($ipos, $grant['default_permission']); break;
		case 'stock': $config->stock($ipos); break;
		case 'remove_logo': $config->remove_logo($ipos); break;
		case 'backup_db': $config->backup_db($ipos); break;
		case 'general': $config->general($ipos); break;
		case 'upload': $config->upload($ipos); break;
		case 'locale': $config->locale($ipos); break;
		case 'barcode': $config->barcode($ipos); break;
		case 'receipt': $config->receipt($ipos); break;
		case 'get_role': $config->get_role($ipos, $grant['default_permission']); break;
		case 'delete_role': $config->delete_role($ipos, $grant['default_permission']); break;
		case 'create_role': $config->create_role($ipos, $grant['default_permission']); break;
		case 'save_role': $config->save_role($ipos, $grant['default_permission']); break;
		case 'update_role': $config->update_role($ipos, $grant['default_permission']); break;
		case 'check_file': $config->check_file($ipos); break;
		default:
		break;
		}
	} else {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['no_access']));
	}
break;
case 'customers':
	$ipos->language(array('customers'));
	$customer = new customer($ipos->db, $grant['grant'], $grant['permission']);
	$func = isset($_REQUEST['f']) ? $_REQUEST['f'] : 'view';
	if ($customer->has_grant($func)) {
		switch ($func) {
		case 'view': $customer->view($ipos); break;
		case 'more': $customer->more($ipos); break;
		case 'excel': $customer->excel($ipos); break;
		case 'excel_import': $customer->excel_import($ipos); break;
		case 'do_excel_import': $customer->do_excel_import($ipos); break;
		case 'create': $customer->create($ipos); break;
		case 'save': $customer->save($ipos); break;
		case 'delete': $customer->delete($ipos); break;
		case 'get': $customer->get($ipos); break;
		case 'suggest_search': $customer->suggest_search(); break;
		case 'search': $customer->search($ipos); break;
		case 'check_account_number': $customer->check_account_number(); break;
		case 'update': $customer->update($ipos); break;
		case 'suggest_gift': $customer->suggest_gift(); break;
		case 'suggest_sale': $customer->suggest_sale(); break;
		default:
		break;
		}
	} else {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['no_access']));
	}
break;
case 'suppliers':
	$ipos->language(array('suppliers'));
	$supplier = new supplier($ipos->db, $grant['grant'], $grant['permission']);
	$func = isset($_REQUEST['f']) ? $_REQUEST['f'] : 'view';
	if ($supplier->has_grant($func)) {
		switch ($func) {
		case 'view': $supplier->view($ipos); break;
		case 'more': $supplier->more($ipos); break;
		case 'create': $supplier->create($ipos); break;
		case 'save': $supplier->save($ipos); break;
		case 'delete': $supplier->delete($ipos); break;
		case 'get': $supplier->get($ipos); break;
		case 'suggest_search': $supplier->suggest_search($ipos); break;
		case 'search': $supplier->search($ipos); break;
		case 'check_account_number': $supplier->check_account_number(); break;
		case 'update': $supplier->update($ipos); break;
		case 'suggest_supplier': $supplier->suggest_supplier(); break;
		default:
		break;
		}
	} else {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['no_access']));
	}
break;
case 'employees':
	$ipos->language(array('employees'));
	$employee = new employee($ipos->db, $person_id);
	$func = isset($_REQUEST['f']) ? $_REQUEST['f'] : 'view';
	if ($employee->has_grant($func)) {
		switch ($func) {
		case 'view': $employee->view($ipos); break;
		case 'more': $employee->more($ipos); break;
		case 'create': $employee->create($ipos); break;
		case 'check_username': $employee->check_username(); break;
		case 'save': $employee->save($ipos); break;
		case 'delete': $employee->delete($ipos); break;
		case 'get': $employee->get($ipos); break;
		case 'suggest_search': $employee->suggest_search($ipos); break;
		case 'search': $employee->search($ipos); break;
		case 'update': $employee->update($ipos); break;
		case 'suggest_order': $employee->suggest_order(); break;
		case 'suggest_pay': $employee->suggest_pay(); break;
		default:
		break;
		}
	} else {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['no_access']));
	}
break;
case 'items':
	$ipos->language(array('items'));
	$item = new items($ipos->db, $grant['grant'], $grant['permission']);
	$func = isset($_REQUEST['f']) ? $_REQUEST['f'] : 'view';
	if ($item->has_grant($func)) {
		switch ($func) {
		case 'view': $item->view($ipos); break;
		case 'more': $item->more($ipos); break;
		case 'create': $item->create($ipos); break;
		case 'save': $item->save($ipos); break;
		case 'delete': $item->delete($ipos); break;
		case 'get': $item->get($ipos); break;
		case 'suggest_search': $item->suggest_search(); break;
		case 'search': $item->search($ipos); break;
		case 'update': $item->update($ipos); break;
		case 'suggest_category': $item->suggest_category(); break;
		case 'suggest_supplier': $item->suggest_supplier(); break;
		case 'suggest_tax': $item->suggest_tax($ipos); break;
		case 'check_item_number': $item->check_item_number(); break;
		case 'upload': $item->upload($ipos); break;
		case 'excel': $item->excel(); break;
		case 'excel_import': $item->excel_import($ipos); break;
		case 'do_excel_import': $item->do_excel_import($ipos); break;
		case 'bulk_edit': $item->bulk_edit($ipos); break;
		case 'bulk_update': $item->bulk_update($ipos); break;
		case 'generate_barcodes': $item->generate_barcodes($ipos); break;
		case 'suggest_order': $item->suggest_order($ipos); break;
		case 'suggest_return': $item->suggest_return(); break;
		case 'suggest_kit': $item->suggest_kit($ipos); break;
		case 'suggest_sale': $item->suggest_sale($ipos); break;
		default:
		break;
		}
	} else {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['no_access']));
	}
break;
case 'item_kits':
	$ipos->language(array('items', 'item_kits'));
	$kit = new item_kits($ipos->db, $grant['grant'], $grant['permission']);
	$func = isset($_REQUEST['f']) ? $_REQUEST['f'] : 'view';
	if ($kit->has_grant($func)) {
		switch ($func) {
		case 'view': $kit->view($ipos); break;
		case 'more': $kit->more($ipos); break;
		case 'create': $kit->create($ipos); break;
		case 'save': $kit->save($ipos); break;
		case 'delete': $kit->delete($ipos); break;
		case 'get': $kit->get($ipos); break;
		case 'check_item_number': $kit->check_item_number(); break;
		case 'suggest_search': $kit->suggest_search(); break;
		case 'search': $kit->search($ipos); break;
		case 'update': $kit->update($ipos); break;
		case 'generate_barcodes': $kit->generate_barcodes($ipos); break;
		default:
		break;
		}
	} else {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['no_access']));
	}
break;
case 'receivings':
	$ipos->language(array('receivings', 'sales'));
	$recv = new receive($ipos->db, $grant['grant'], $grant['permission']);
	$func = isset($_REQUEST['f']) ? $_REQUEST['f'] : 'view';
	if ($recv->has_grant($func)) {
		switch ($func) {
		case 'view': $recv->view($ipos); break;
		case 'order': $recv->order($ipos); break;
		case 'get': $recv->get($ipos); break;
		case 'suggest_search': $recv->suggest_search(); break;
		case 'receive': $recv->recv($ipos); break;
		case 'item': $recv->item($ipos); break;
		case 'return': $recv->ret($ipos); break;
		default:
		break;
		}
	} else {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['no_access']));
	}
break;
case 'giftcards':
	$ipos->language(array('giftcards'));
	$gift = new giftcard($ipos->db, $grant['grant'], $grant['permission']);
	$func = isset($_REQUEST['f']) ? $_REQUEST['f'] : 'view';
	if ($gift->has_grant($func)) {
		switch ($func) {
		case 'view': $gift->view($ipos); break;
		case 'more': $gift->more($ipos); break;
		case 'create': $gift->create($ipos); break;
		case 'save': $gift->save($ipos); break;
		case 'delete': $gift->delete($ipos); break;
		case 'check_number': $gift->check_number(); break;
		case 'search': $gift->search($ipos); break;
		case 'suggest_search': $gift->suggest_search(); break;
		default:
		break;
		}
	} else {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['no_access']));
	}
break;
case 'sales':
	$ipos->language(array('sales'));
	$sale = new sale($ipos->db, $grant['grant'], $grant['permission']);
	$func = isset($_REQUEST['f']) ? $_REQUEST['f'] : 'view';
	if ($sale->has_grant($func)) {
		switch ($func) {
		case 'view': $sale->view($ipos); break;
		case 'sale': $sale->sale($ipos); break;
		case 'pay': $sale->pay($ipos); break;
		case 'suspend': $sale->suspend($ipos); break;
		case 'suspend_change': $sale->suspend_change($ipos); break;
		case 'calc': $sale->calc($ipos); break;
		case 'return': $sale->ret($ipos); break;
		case 'suggest': $sale->suggest(); break;
		case 'search': $sale->search($ipos); break;
		default:
		break;
		}
	} else {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['no_access']));
	}
break;
case 'reports':
	$ipos->language(array('reports', 'calendar', 'date', 'datepicker'));
	$report = new report($ipos->db, $grant['grant'], $grant['permission']);
	$func = isset($_REQUEST['f']) ? $_REQUEST['f'] : 'view';
	if ($report->has_grant($func)) {
		switch ($func) {
		case 'view': $report->view($ipos); break;
		case 'cate': $report->cate($ipos); break;
		case 'category': $report->category($ipos); break;
		case 'supplier': $report->supplier($ipos); break;
		case 'payment': $report->payment($ipos); break;
		case 'supp': $report->supp($ipos); break;
		case 'pay': $report->pay($ipos); break;
		default:
		break;
		}
	} else {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['no_access']));
	}
break;
case 'logout':
	$ipos->session->destory();
	break;
case 'view':
default:
	$ipos->assign('controller_name', '');
	$ipos->display('header.tpl');
break;
break;
}
?>