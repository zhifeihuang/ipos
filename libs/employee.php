<?php
require_once('../libs/pwd.php');
require_once('../libs/person.php');
require_once('../libs/module.php');
require_once('../libs/grant.php');
require_once('../libs/secure.php');
require_once('../libs/sale.php');

class employee extends secure
{
public $grant;
private $module;
private $role;
private $person;
private $person_id;
private $flt = array('username' => FILTER_SANITIZE_SPECIAL_CHARS,
					'password' => FILTER_SANITIZE_SPECIAL_CHARS,
					'role' => FILTER_SANITIZE_SPECIAL_CHARS,
					'repeat_password' => FILTER_SANITIZE_SPECIAL_CHARS);
private $flt_more = array('offset' => FILTER_VALIDATE_INT, 
						'limit' => FILTER_VALIDATE_INT, 
						'type' => FILTER_SANITIZE_SPECIAL_CHARS, 
						'label' => FILTER_SANITIZE_SPECIAL_CHARS, 
						'value' => FILTER_SANITIZE_SPECIAL_CHARS);
private $flt_search = array('limit' => FILTER_VALIDATE_INT,
							'label' => FILTER_SANITIZE_SPECIAL_CHARS,
							'value' => FILTER_SANITIZE_SPECIAL_CHARS);
private $table_struct = array('number'=>array('phone_number','p.person_id'),
							'string'=>array('first_name', 'last_name'));
							
public function __construct($db, $id) {
	$this->func_permission = array('view'=>'employees',
									'more'=>'employees',
									'create'=>'employees_insert',
									'save'=>'employees_insert',
									'delete'=>'employees_delete',
									'get'=>'employees',
									'suggest_search'=>'employees',
									'search'=>'employees',
									'update'=>'employees_update',
									'check_username'=>'employees_update',
									'suggest_order'=>'receivings_insert',
									'suggest_pay'=>'reports_payments');
	$this->module = new module($db);
	$this->role = new grant($db, $this->get_all_permissions(), $this->module->default_permission);
	$this->grant = $this->role->get($id);
	parent::__construct($db, $this->grant, $this->get_all_permissions());
	$this->person = new person($db);
	$this->person_id = $id;
}

public function get_allowed_modules() {
	return $this->module->get_allowed_modules($this->grant);
}

public function get_all_permissions() {
	return $this->module->permission;
}

public function get_default_permission() {
	return $this->module->default_permission;
}

public function get_grant($person_id) {
	return $this->role->get($person_id);
}

public function view(&$ipos) {
	if (($person = $this->person->get_all("employees")) === false) {
		$ipos->err_page('employees_err');
		return;
	}
	
	$ipos->assign('controller_name', 'employees');
	$ipos->assign('manage_table', 'person');
	$ipos->assign('offset', 100);
	$ipos->assign('person', $person);
	$ipos->assign('subgrant', $this->subgrant);
	$ipos->display('person/manage.tpl');
}

public function more(&$ipos) {
	$var = filter_var_array($_REQUEST, $this->flt_more);
	$var['offset'] = empty($var['offset']) ? 0 : $var['offset'];
	$var['limit'] = empty($var['limit']) ? 100 : $var['limit'];
	
	$data = false;
	if ('more' == $var['type']) {
		$data = $this->person->get_all('employees', $var['offset'], $var['limit']);
	} else if ('search' == $var['type']) {
		$data = $this->person->search('employees', $var, $this->table_struct, $var['offset'], $var['limit']);
	}
	
	if (!empty($data)) {
		$ipos->assign('controller_name', 'employees');
		$ipos->assign('manage_table', 'person');
		$ipos->assign('person', $data);
		$ipos->assign('subgrant', $this->subgrant);
		echo json_encode(array('success'=>true, 'msg'=>'', 'data'=>$ipos->fetch('person/table_row.tpl'), 'offset'=>($var['offset'] + $var['limit'])));
	} else if ($data === false) {
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['employees_err_get']));
	} else {
		echo json_encode(array('success'=>true, 'msg'=>$ipos->lang['common_no_more_data']));
	}
}

public function get(&$ipos) {
	if (empty($_REQUEST['id'])) {
		$ipos->assign('err', $ipos->lang['employees_err_get']);
		echo $ipos->fetch('err_msg.tpl');
		return;
	}
	
	$data = $this->person->get_info('employees', intval($_REQUEST['id']));
	if (!empty($data[0])) {
		$result = $data[0];
		$ipos->language(array('role'));
		$role = $this->role->get_all($ipos->lang);
		$role_arr = array();
		foreach ($role as $v) {
			$role_arr[] = $v['role'];
		}
		
		$ipos->assign('role_arr', $role_arr);
		$ipos->assign('role', $result['role']);
		$ipos->assign('language', $ipos->session->usrdata('lang'));
		$ipos->assign('person', $result);
		echo $ipos->fetch('employees/form.tpl');
	} else {
		$ipos->assign('err', $ipos->lang['employees_err_get']);
		echo $ipos->fetch('err_msg.tpl');
	}
}

public function create(&$ipos) {
	$ipos->language(array('role'));
	$role = $this->role->get_all($ipos->lang);
	$role_arr = array();
	foreach ($role as $v) {
		$role_arr[] = $v['role'];
	}
	
	$ipos->assign('role_arr', $role_arr);
	$ipos->assign('language', $ipos->session->usrdata('lang'));
	echo $ipos->fetch('employees/form.tpl');
}

public function check_username() {
	$usrname = filter_var($_REQUEST['username'], FILTER_SANITIZE_SPECIAL_CHARS);
	$id = empty($_REQUEST['person_id']) ? false : intval($_REQUEST['person_id']);
	
	$this->db->query('SELECT person_id from employees WHERE usrname=?');
	$result = $this->db->check($usrname, $id) ? 'true' : 'false';
	
	echo $result;
}

public function save(&$ipos) {
	$pdata = $this->person->filter($_REQUEST);
	$cdata = filter_var_array($_REQUEST, $this->flt);
	
	if (empty($cdata['username']) || empty($cdata['password']) || empty($cdata['role']) || strcmp($cdata['password'], $cdata['repeat_password'])) {
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['employees_err_save']));
		return;
	}
	
	unset($cdata['repeat_password']);
	$cdata['password'] = pwd_hash($cdata['password']);
	$cdata['usrname'] = $cdata['username'];
	unset($cdata['username']);
	
	$this->db->beginTransaction();
	if ($result = $this->person->save($pdata, $cdata, 'employees')) {
		$this->db->commit();
		$this->check_sale($result, $cdata['role'], 'save');
		$pdata['person_id'] = $result;
		$ipos->assign('person', array(array_merge($pdata, $cdata)));
		$ipos->assign('controller_name', 'employees');
		$ipos->assign('subgrant', $this->subgrant);
		echo json_encode(array('success'=>true, 'msg'=>$ipos->lang['employees_msg_save'], 'id'=>$result, 'row'=>$ipos->fetch('person/table_row.tpl')));
	} else {
		$this->db->rollBack();
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['employees_err_save']));
	}
}

public function update(&$ipos) {
	if (empty($_REQUEST['id'])) {
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['employees_err_update']));
		return;
	}
	$id = intval($_REQUEST['id']);
	
	$pdata = $this->person->filter($_REQUEST);
	$cdata = filter_var_array($_REQUEST, $this->flt);
	
	if (empty($cdata['username']) || empty($cdata['role']) || strcmp($cdata['password'], $cdata['repeat_password'])) {
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['employees_err_update']));
		return;
	} else if (empty($cdata['password'])) {
		unset($cdata['password'], $cdata['repeat_password']);
	} else {
		$cdata['password'] = pwd_hash($cdata['password']);
		unset($cdata['repeat_password']);
	}
	
	$cdata['usrname'] = $cdata['username'];
	unset($cdata['username']);
	
	$this->db->beginTransaction();
	if ($this->person->save($pdata, $cdata, 'employees', $id)) {
		$this->db->commit();
		$this->check_sale($id, $cdata['role'], 'update');
		$pdata['person_id'] = $id;
		$ipos->assign('person', array(array_merge($pdata, $cdata)));
		$ipos->assign('controller_name', 'employees');
		$ipos->assign('subgrant', $this->subgrant);
		echo json_encode(array('success'=>true, 'msg'=>$ipos->lang['employees_msg_update'], 'id'=>$id, 'row'=>$ipos->fetch('person/table_row.tpl')));
	} else {
		$this->db->rollBack();
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['employees_err_update']));
	}
}

public function delete(&$ipos) {
	if (empty($_REQUEST['ids'])) {
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['employees_err_deleted']));
		return;
	}
	
	// do not delete youself
	if (in_array($this->person_id, $_REQUEST['ids'])) {
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['employees_err_delete_yourself']));
		return;
	}
	
	if ($this->person->delete('employees', $_REQUEST['ids'])) {
		foreach ($_REQUEST['ids'] as $v) {
			$this->check_sale(intval($v), null, 'delete');
		}
		echo json_encode(array('success'=>true, 'msg'=>$ipos->lang['employees_msg_deleted']));
	} else {
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['employees_err_deleted']));
	}
}

public function suggest_search(&$ipos) {
	if (empty($_REQUEST['term'])) return;
	
	$var = filter_var($_REQUEST['term'], FILTER_SANITIZE_SPECIAL_CHARS);
	if (!empty($result = $this->person->search_suggestions('employees', $var, $this->table_struct)))
		echo json_encode($result);
}

public function search(&$ipos) {
	$var = filter_var_array($_REQUEST, $this->flt_search);
	$limit = empty($var['limit']) ? 100 : $var['limit'];
	$suggestions  = $this->person->search('employees', $var, $this->table_struct, 0, $limit);
	
	$ipos->assign('controller_name', 'employees');
	$ipos->assign('person', $suggestions);
	$ipos->assign('subgrant', $this->subgrant);
	echo json_encode(array('rows' => $ipos->fetch('person/table_row.tpl'), 'offset' => $limit, 'total_rows' => count($suggestions)));
}

public function suggest_order() {
	if (empty($_REQUEST['term'])) return;
	
	$var = filter_var($_REQUEST['term'], FILTER_SANITIZE_SPECIAL_CHARS);
	if (!empty($result = $this->person->search_suggestions('employees', $var, $this->table_struct, false))) {
		$suggestions = array();
		foreach ($result as $v) {
			$suggestions[] = array('label'=>$v['person_id'] .' '. $v['first_name'] .' '. $v['last_name']);
		}
		
		echo json_encode($suggestions);
	}
}

public function suggest_pay() {
	if (empty($_REQUEST['term'])) return;
	
	$var = filter_var($_REQUEST['term'], FILTER_SANITIZE_SPECIAL_CHARS);
	if (!empty($result = $this->person->search_suggestions('employees', $var, $this->table_struct, false))) {
		$suggestions = array();
		foreach ($result as $v) {
			$suggestions[] = array('label'=>$v['person_id'] .' '. $v['first_name'] .' '. $v['last_name'], 'value'=>$v['person_id']);
		}
		
		echo json_encode($suggestions);
	}
}
/*
Attempts to login employee and set session. Returns boolean based on outcome.
*/
public static function login($db, $id, $pwd) {
	$db->query('SELECT usrname, password FROM employees WHERE person_id=? AND deleted=0');
	if (($result = $db->select(array(array($id)))) &&  pwd_verify($pwd, $result[0]['password'])) {
		return array('person_id'=>$id, 'name'=>$result[0]['usrname']);
	} else {
		return false;
	}
}

public function get_stock() {
	$this->db->query("SELECT stock FROM employees WHERE person_id=? AND deleted=0");
	$result = $this->db->select(array(array($this->person_id)));
	
	return empty($result[0]['stock']) ? array() : explode(':', $result[0]['stock']);
}

public static function get_info($db, $id) {
	$person = new person($db);
	return $person->get_info('employees', $id);
}

private function check_sale($id, $role, $mode) {
	$name = sale::table_name($id);
	if ($mode == 'save') {
		if (($pm = $this->role->get_permission($role)) && $this->check_grant($pm, 'sales_insert')) {
			$this->db->query('CREATE TABLE IF NOT EXISTS '. $name .' LIKE sale_suspend');
			return $this->db->execute();
		}
	} else if ($mode == 'delete') {
		$this->db->query('DROP TABLE IF EXISTS '. $name);
		return $this->db->execute();
	} else if ($mode == 'update') {
		if (($pm = $this->role->get_permission($role)) && $this->check_grant($pm, 'sales_insert')) {
			$this->db->query('CREATE TABLE IF NOT EXISTS '. $name .' LIKE sale_suspend');
			return $this->db->execute();
		} else {
			$this->db->query('DROP TABLE IF EXISTS '. $name);
			return $this->db->execute();
		}
	}
	
	return true;
}
}
?>
