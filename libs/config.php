<?php
require_once('../libs/secure.php');
require_once '../libs/stock.php';
require_once '../libs/grant.php';
require_once('../libs/help/upload.php');

class config extends secure {
private $flt_general = array('company' => FILTER_SANITIZE_SPECIAL_CHARS,
				//	'company_logo' => array('filter'=>FILTER_SANITIZE_SPECIAL_CHARS, 'options'=>array('default'=>'')),
					'company_start' => FILTER_SANITIZE_SPECIAL_CHARS,
					'address' => FILTER_SANITIZE_SPECIAL_CHARS,
					'website' => FILTER_SANITIZE_SPECIAL_CHARS,
					'email' => FILTER_VALIDATE_EMAIL,
					'phone' => FILTER_SANITIZE_SPECIAL_CHARS,
					'fax' => FILTER_SANITIZE_SPECIAL_CHARS,
					'return_policy' => FILTER_SANITIZE_SPECIAL_CHARS,
					'default_tax_1_name' => FILTER_SANITIZE_SPECIAL_CHARS,
					'default_tax_1_rate' => FILTER_VALIDATE_INT,
					'default_tax_2_name' => FILTER_SANITIZE_SPECIAL_CHARS,
					'default_tax_2_rate' => FILTER_VALIDATE_INT,
					'tax_included' => array('filter'=>FILTER_VALIDATE_INT, 'options'=>array('default'=>0)),
					'default_sales_discount' => array('filter'=>FILTER_VALIDATE_INT, 'options'=>array('min_range'=>0,'max_range'=>100)),
					'receiving_calculate_average_price' => array('filter'=>FILTER_VALIDATE_INT, 'options'=>array('default'=>0)));
private $flt_locale = array('currency_symbol' => FILTER_SANITIZE_SPECIAL_CHARS,
					'currency_side' => FILTER_VALIDATE_INT,
					'currency_decimals' => FILTER_VALIDATE_INT,
					'quantity_decimals' => FILTER_VALIDATE_INT,
					'tax_decimals' => FILTER_VALIDATE_INT,
					'decimal_point' => FILTER_SANITIZE_SPECIAL_CHARS,
					'thousands_separator' => FILTER_SANITIZE_SPECIAL_CHARS,
					'timezone' => FILTER_SANITIZE_SPECIAL_CHARS,
					'dateformat' => FILTER_SANITIZE_SPECIAL_CHARS,
					'timeformat' => FILTER_SANITIZE_SPECIAL_CHARS);
private $flt_barcode = array('barcode_type' => FILTER_SANITIZE_SPECIAL_CHARS,
					'barcode_quality' => array('filter'=>FILTER_VALIDATE_INT, 'options'=>array('min_range'=>10,'max_rang'=>100)),
					'barcode_width' => array('filter'=>FILTER_VALIDATE_INT, 'options'=>array('min_range'=>60,'max_rang'=>350)),
					'barcode_height' => array('filter'=>FILTER_VALIDATE_INT, 'options'=>array('min_range'=>10,'max_rang'=>120)),
					'barcode_font' => FILTER_SANITIZE_SPECIAL_CHARS,
					'barcode_font_size' => array('filter'=>FILTER_VALIDATE_INT, 'options'=>array('min_range'=>1,'max_rang'=>30)),
					'barcode_content' => FILTER_SANITIZE_SPECIAL_CHARS,
					'barcode_generate_if_empty' => FILTER_VALIDATE_INT,
					'barcode_first_row' => FILTER_SANITIZE_SPECIAL_CHARS,
					'barcode_second_row' => FILTER_SANITIZE_SPECIAL_CHARS,
					'barcode_third_row' => FILTER_SANITIZE_SPECIAL_CHARS,
					'barcode_num_in_row' => FILTER_VALIDATE_INT,
					'barcode_page_width' => FILTER_VALIDATE_INT,
					'barcode_page_cellspacing' => FILTER_VALIDATE_INT);
private $flt_stock = array('change' => FILTER_SANITIZE_SPECIAL_CHARS,
					'add' => FILTER_SANITIZE_SPECIAL_CHARS,
					'remove' => FILTER_SANITIZE_SPECIAL_CHARS);
private $flt_receipt = array('invoice_default_comments' => FILTER_SANITIZE_SPECIAL_CHARS,
					'receipt_show_taxes' => FILTER_VALIDATE_INT,
					'show_total_discount' => FILTER_VALIDATE_INT,
					'print_silently' => FILTER_VALIDATE_INT,
					'print_header' => FILTER_VALIDATE_INT,
					'print_footer' => FILTER_VALIDATE_INT,
					'sales_invoice_format' => FILTER_SANITIZE_SPECIAL_CHARS,
					'recv_invoice_format' => FILTER_SANITIZE_SPECIAL_CHARS,
					'order_invoice_format' => FILTER_SANITIZE_SPECIAL_CHARS,
					'ret_invoice_format' => FILTER_SANITIZE_SPECIAL_CHARS);
private $check_grant = array('stock', 'grants');

public function __construct($db, $grant, $permission) {
	$this->func_permission = array('view' => 'config',
								'remove_logo' => 'config',
								'backup_db' => 'config',
								'general' => 'config',
								'upload' => 'config',
								'locale' => 'config',
								'barcode' => 'config',
								'stock' => 'stock',
								'get_undelete_stock' => 'stock',
								'receipt' => 'config',
								'get_all_role' => 'grants',
								'get_role' => 'grants',
								'delete_role' => 'grants',
								'create_role' => 'grants',
								'save_role' => 'grants',
								'update_role' => 'grants',
								'check_file' => 'config');
	parent::__construct($db, $grant, $permission);
}

public function get_all() {
	$this->db->query('SELECT * FROM app_config');
	if (($all = $this->db->select()) === false) return false;
	
	$result = array();
	foreach ($all as $v) {
		$result[$v['k']] = $v['val'];
	}
	
	if (!empty($result['company_logo'])) {
		$app = require '../config/app_con.php';
		$result['company_logo'] = $app['config_upload_dir'] . $result['company_logo'];
	}
	
	return $result;
}

public function view(&$ipos, $default_permission) {
	$app = require '../config/app_con.php';
	if (($data = $this->get_all()) === false || ($receipt = $this->receipt_file($app['receipt_dir'])) === false) {
		$ipos->err_page('config_err');
		return false;
	}
	
	$stock = $this->has_grant('get_undelete_stock')  ? $this->get_undelete_stock() : array();
	$role = $this->has_grant('get_all_role') ? $this->get_all_role($this->permission, $default_permission, $ipos) : array();
	
	$tpl_config = require '../config/tpl.php';
	$ipos->assign('tpl_config', $tpl_config);
	$ipos->assign('stock_locations', $stock);
	$ipos->assign('sale_file', json_encode($receipt['sale']));
	$ipos->assign('recv_file', json_encode($receipt['recv']));
	$ipos->assign('order_file', json_encode($receipt['order']));
	$ipos->assign('ret_file', json_encode($receipt['ret']));
	$ipos->assign('role', $role);
	$ipos->assign('config', $data);
	$ipos->assign('subgrant', $this->subgrant);
	$ipos->assign('controller_name', 'config');
	$ipos->display('config/manage.tpl');
}

public function backup_db(&$ipos) {
	$tables = isset($_REQUEST['val']) ? filter_var($_REQUEST['val'], FILTER_SANITIZE_SPECIAL_CHARS) : '*';
	if ($tables === false) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['config_err_param']));
		return;
	}
	
	require_once('../libs/db/mysqli.php');
	if (!($app = include '../config/app_con.php')) {
		error_log('read app connfig err.');
		echo json_encode(array("success" => false, "msg" => $ipos->lang['con_err']));
		return;
	}
	
	$backup = new backup_database($this->db->getdb());
	if ($backup->backup_tables($tables, $app['config_backup_db']))
		echo json_encode(array("success" => true, "msg" => $ipos->lang['config_msg_backup_db']));
	else 
		echo json_encode(array("success" => false, "msg" => $ipos->lang['config_err']));
}

public function remove_logo(&$ipos) {
	$this->db->query('UPDATE app_config SET val=? WHERE k=?');
	if ($this->db->update(array(array('', 'company_logo')))) {
		$ipos->clearCache('config/manage.tpl');
		echo json_encode(array("success" => true, "msg" => $ipos->lang['config_msg_remove_logo']));
	} else
		echo json_encode(array("success" => false, "msg" => $ipos->lang['config_err']));
}

public function upload(&$ipos) {
	if (!($app = include '../config/app_con.php')) {
		error_log('read app connfig err.');
		echo json_encode(array("success" => false, "msg" => $ipos->lang['con_err']));
		return;
	}
	$upload = new upload;
	$filename = $upload->load($app['cofig_upload_size'], $app['config_allowed_type'], 'company_logo', $app['config_upload_dir'], true);
	
	$this->db->query('UPDATE app_config SET val=? WHERE k=?');
	if ($filename && $this->db->update(array(array($filename[0], 'company_logo')))) {
		$ipos->clearCache('config/manage.tpl');
		echo json_encode(array("success" => true, "msg" => $ipos->lang['config_msg_upload']));
	 } else
		echo json_encode(array("success" => false, "msg" => $ipos->lang['config_err']));
}

public function general(&$ipos) {
	$var = filter_var_array($_REQUEST, $this->flt_general);
	// filter_var_array cant get default value, so do it for yourself.
	if (!$var['tax_included']) $var['tax_included'] = 0;
	if (!$var['receiving_calculate_average_price']) $var['receiving_calculate_average_price'] = 0;
	
	$update = array();
	foreach ($var as $k => $v) {
		$update[] = array($v, $k);
	}
	
	$this->db->query('UPDATE app_config SET val=? WHERE k=?');
	if ($this->db->update($update)) {
		$ipos->clearCache('config/manage.tpl');
		echo json_encode(array("success" => true, "msg" => $ipos->lang['config_msg_general']));
	} else
		echo json_encode(array("success" => false, "msg" => $ipos->lang['config_err']));
}

public function locale(&$ipos) {
	$var = filter_var_array($_REQUEST, $this->flt_locale);
	if (!$var['currency_side']) $var['currency_side'] = 0;
	
	$update = array();
	foreach ($var as $k => $v) {
		$update[] = array($v, $k);
	}
	
	$this->db->query('UPDATE app_config SET val=? WHERE k=?');
	if ($this->db->update($update)) {
		$ipos->clearCache('config/manage.tpl');
		echo json_encode(array("success" => true, "msg" => $ipos->lang['config_msg_locale']));
	} else
		echo json_encode(array("success" => false, "msg" => $ipos->lang['config_err']));
}

public function barcode(&$ipos) {
	$var = filter_var_array($_REQUEST, $this->flt_barcode);
	if (!$var['barcode_generate_if_empty']) $var['barcode_generate_if_empty'] = 0;
	
		$update = array();
	foreach ($var as $k => $v) {
		$update[] = array($v, $k);
	}
	
	$this->db->query('UPDATE app_config SET val=? WHERE k=?');
	if ($this->db->update($update)) {
		$ipos->clearCache('config/manage.tpl');
		echo json_encode(array("success" => true, "msg" => $ipos->lang['config_msg_barcode']));
	} else
		echo json_encode(array("success" => false, "msg" => $ipos->lang['config_err']));
}

public function stock(&$ipos) {
	$var = filter_var_array($_REQUEST, $this->flt_stock);
	$stock = new stock($this->db);
	
	$this->db->beginTransaction();
	if ($var['change']) {
		$arr = explode(',', $var['change']);
		$data = array();
		$i = 0;
		do {
			// new id,old id
			$data[] = array($arr[$i + 1], $arr[$i]);
			$i += 2;
		} while ($i < count($arr));
		
		if (!$stock->change($data)) {
			$this->db->rollBack();
			echo json_encode(array("success" => false, "msg" => $ipos->lang['config_err']));
			return;
		}
	}
	
	if ($var['add']) {
		$arr = explode(',', $var['add']);
		if (!$stock->add($arr))  {
			$this->db->rollBack();
			echo json_encode(array("success" => false, "msg" => $ipos->lang['config_err']));
			return;
		}
	}
	
	if ($var['remove']) {
		$arr = explode(',', $var['remove']);
		if (!$stock->remove($arr))  {
			$this->db->rollBack();
			echo json_encode(array("success" => false, "msg" => $ipos->lang['config_err']));
			return;
		}
	}
	
	$this->db->commit();
	$ipos->clearCache('config/manage.tpl');
	$st = $this->has_grant('get_undelete_stock')  ? $this->get_undelete_stock() : array();
	$ipos->assign('stock_locations', $st);
	$ipos->assign('subgrant', $this->subgrant);
	echo json_encode(array('success'=>true, 'msg'=>$ipos->lang['config_msg_stock'], 'part'=>$ipos->fetch('partial/stock_part.tpl')));
}

public function receipt(&$ipos) {
	$var = filter_var_array($_REQUEST, $this->flt_receipt);
	if (!$var['receipt_show_taxes']) $var['receipt_show_taxes'] = 0;
	if (!$var['show_total_discount']) $var['show_total_discount'] = 0;
	if (!$var['print_silently']) $var['print_silently'] = 0;
	if (!$var['print_header']) $var['print_header'] = 0;
	if (!$var['print_footer']) $var['print_footer'] = 0;
	
	$update = array();
	foreach ($var as $k => $v) {
		$update[] = array($v, $k);
	}
	
	$this->db->query('UPDATE app_config SET val=? WHERE k=?');
	if ($this->db->update($update)) {
		$ipos->clearCache('config/manage.tpl');
		echo json_encode(array("success" => true, "msg" => $ipos->lang['config_msg_receipt']));
	} else
		echo json_encode(array("success" => false, "msg" => $ipos->lang['config_err']));
}

public function get_role(&$ipos, $default_permission) {
	$id = filter_var($_REQUEST['id'], FILTER_SANITIZE_SPECIAL_CHARS);
	if (empty($id)) {
		$ipos->assign('err', $ipos->lang['config_err_get_role']);
		echo $ipos->fetch('err_msg.tpl');
		return;
	}
	
	$role = new grant($this->db, $this->permission, $default_permission);
	if ($result = $role->get_role($id)) {
		$ipos->language(array('role'));
		$ipos->assign('id', $id);
		$ipos->assign('role', $result);
		echo $ipos->fetch('role/form.tpl');
	} else {
		$ipos->assign('err', $ipos->lang['config_err_get_role']);
		echo $ipos->fetch('err_msg.tpl');
		return;
	}
}

public function delete_role(&$ipos, $default_permission) {
	if (empty($_REQUEST['ids'])) {
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['config_err']));
		return;
	}
	
	$role = new grant($this->db, $this->permission, $default_permission);
	if ($role->delete($_REQUEST['ids'])) {
		echo json_encode(array('success'=>true, 'msg'=>$ipos->lang['config_msg_delete_role']));
	} else
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['config_err']));
	
}

public function create_role(&$ipos, $default_permission) {
	$role = new grant($this->db, $this->permission, $default_permission);
	$ipos->language(array('role'));
	echo $ipos->fetch('role/form.tpl');
}

public function save_role(&$ipos, $default_permission) {
	if (empty($_REQUEST['role']) || empty($_REQUEST['grants'])) {
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['config_err']));
		return;
	}
	
	$ipos->language(array('role'));
	$role = new grant($this->db, $this->permission, $default_permission);
	if ($result = $role->save($_REQUEST['role'], $_REQUEST['grants'], $ipos->lang)) {
		$ipos->assign('role', array($result));
		echo json_encode(array('success'=>true, 'msg'=>$ipos->lang['config_msg_save_role'], 'id'=>$result['role'], 'row'=>$ipos->fetch('role/table_row.tpl')));
	} else {
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['config_err']));
	}
}

public function update_role(&$ipos, $default_permission) {
	if (empty($_REQUEST['role']) || empty($_REQUEST['grants'])) {
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['config_err']));
		return;
	}
	
	$ipos->language(array('role'));
	$role = new grant($this->db, $this->permission, $default_permission);
	if ($result = $role->update($_REQUEST['role'], $_REQUEST['grants'], $ipos->lang)) {
		$ipos->assign('role', array($result));
		echo json_encode(array('success'=>true, 'msg'=>$ipos->lang['config_msg_update_role'], 'id'=>$result['role'], 'row'=>$ipos->fetch('role/table_row.tpl')));
	} else {
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['config_err']));
		return;
	}
}

public function check_file(&$ipos) {
	if (empty($_REQUEST['file'])) {
		echo 'false';
		return;
	}
	
	$file = filter_var($_REQUEST['file'], FILTER_SANITIZE_SPECIAL_CHARS);
	if (strpos($file, '..')) {
		echo 'false';
		return;
	}
	
	$app = require '../config/app_con.php';
	if (file_exists($app['receipt_dir'] . $file))
		echo 'true';
	else
		echo 'false';
}

private function get_undelete_stock() {
	$stock = new stock($this->db);
	return $stock->get_undeleted();
}

private function get_all_role($permission, $default_permission, $ipos) {
	$ipos->language(array('role'));
	$role = new grant($this->db, $permission, $default_permission);
	$result = $role->get_all($ipos->lang);
	return $result;
}

private function receipt_file($dir) {
	require '../libs/help/common.php';
	if (($sale = files($dir . 'sale')) === false
		|| ($recv = files($dir . 'receive')) === false
		|| ($order = files($dir . 'order')) === false
		|| ($ret = files($dir . 'return')) === false) {
		return false;
	}
	
	return array('sale'=>$sale,'recv'=>$recv,'order'=>$order,'ret'=>$ret);
}
}
?>