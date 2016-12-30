<?php
require_once('../libs/secure.php');
require_once('../libs/person.php');
require_once('../libs/config.php');

class supplier extends secure {
private $person;

private $flt = array('company_name' => FILTER_SANITIZE_SPECIAL_CHARS,
					'account_number' => FILTER_SANITIZE_SPECIAL_CHARS,
					'agency_name' => FILTER_SANITIZE_SPECIAL_CHARS);
private $flt_more = array('offset' => FILTER_VALIDATE_INT, 
						'limit' => FILTER_VALIDATE_INT, 
						'type' => FILTER_SANITIZE_SPECIAL_CHARS, 
						'label' => FILTER_SANITIZE_SPECIAL_CHARS, 
						'value' => FILTER_SANITIZE_SPECIAL_CHARS);
private $flt_account = array('account_number' => FILTER_SANITIZE_SPECIAL_CHARS,
							'person_id' => FILTER_VALIDATE_INT);
private $flt_search = array('limit' => FILTER_VALIDATE_INT,
							'label' => FILTER_SANITIZE_SPECIAL_CHARS,
							'value' => FILTER_SANITIZE_SPECIAL_CHARS);
private $table_struct = array('number'=>array('account_number','phone_number','p.person_id'),
							'string'=>array('first_name', 'last_name', 'company_name'));
							
public function __construct($db, $grant, $permission) {
	$this->func_permission = array('view' => 'suppliers',
								'more' => 'suppliers',
								'create' => 'suppliers_insert',
								'save' => 'suppliers_insert',
								'delete' => 'suppliers_delete',
								'suggest_search'=>'suppliers',
								'search'=>'suppliers',
								'check_account_number' => 'suppliers_update',
								'get' => 'suppliers',
								'update' => 'suppliers_update',
								'suggest_supplier' => 'reports_suppliers');
	parent::__construct($db, $grant, $permission);
	$this->person = new person($db);
}

public static function get_info($db, $key, $val) {
	$db->query('SELECT * FROM suppliers as c JOIN person as p ON c.person_id=p.person_id WHERE c.deleted=0 AND c.'. $key .'=?');
	return $db->select(array(array($val)));
}

public function view(&$ipos) {
	if (($person = $this->person->get_all("suppliers")) === false) {
		$ipos->err_page('customers_err');
		return;
	}

	$ipos->assign('controller_name', 'suppliers');
	$ipos->assign('manage_table', 'suppliers');
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
		$data = $this->person->get_all('suppliers', $var['offset'], $var['limit']);
	} else if ('search' == $var['type']) {
		$data = $this->person->search('suppliers', $var, $this->table_struct, $var['offset'], $var['limit']);
	}
	
	if (!empty($data)) {
		$ipos->assign('controller_name', 'suppliers');
		$ipos->assign('manage_table', 'suppliers');
		$ipos->assign('person', $data);
		$ipos->assign('subgrant', $this->subgrant);
		echo json_encode(array('success'=>true, 'msg'=>'', 'data'=>$ipos->fetch('suppliers/table_row.tpl'), 'offset'=>($var['offset'] + $var['limit'])));
	} else if ($data === false) {
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['suppliers_err_get']));
	} else {
		echo json_encode(array('success'=>true, 'msg'=>$ipos->lang['common_no_more_data']));
	}
}

public function create(&$ipos) {
	$ipos->assign('language', $ipos->session->usrdata('lang'));
	echo $ipos->fetch('suppliers/form.tpl');
}

public function save(&$ipos) {
	$pdata = $this->person->filter($_REQUEST);
	$cdata = filter_var_array($_REQUEST, $this->flt);
	
	$this->db->beginTransaction();
	if ($result = $this->person->save($pdata, $cdata, 'suppliers')) {
		$this->db->commit();
		$pdata['person_id'] = $result;
		$ipos->assign('person', array(array_merge($pdata, $cdata)));
		$ipos->assign('controller_name', 'suppliers');
		$ipos->assign('subgrant', $this->subgrant);
		echo json_encode(array('success'=>true, 'msg'=>$ipos->lang['suppliers_msg_save'], 'id'=>$result, 'row'=>$ipos->fetch('suppliers/table_row.tpl')));
	} else {
		$this->db->rollBack();
		echo json_encode(array("success" => false, "msg" => $ipos->lang['suppliers_err_save']));
	}
}

public function update(&$ipos) {
	if (!isset($_REQUEST['id']) || $_REQUEST['id'] == null) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['suppliers_err_update']));
		return;
	}
	$id = intval($_REQUEST['id']);
	
	$pdata = $this->person->filter($_REQUEST);
	$cdata = filter_var_array($_REQUEST, $this->flt);
	
	$this->db->beginTransaction();
	if ($this->person->save($pdata, $cdata, 'suppliers', $id)) {
		$this->db->commit();
		$pdata['person_id'] = $id;
		$ipos->assign('person', array(array_merge($pdata, $cdata)));
		$ipos->assign('controller_name', 'suppliers');
		$ipos->assign('subgrant', $this->subgrant);
		echo json_encode(array('success'=>true, 'msg'=>$ipos->lang['suppliers_msg_update'], 'id'=>$id, 'row'=>$ipos->fetch('suppliers/table_row.tpl')));
	} else {
		$this->db->rollBack();
		echo json_encode(array("success" => false, "msg" => $ipos->lang['suppliers_err_update']));
	}
}

public function delete(&$ipos) {
	if (($key = array_search(1, $_REQUEST['ids'])) !== false) {
		unset($_REQUEST['ids'][$key]);
	}
	
	if (empty($_REQUEST['ids'])) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['suppliers_err']));
		return;
	}
	
	if ($this->person->delete('suppliers', $_REQUEST['ids']))
		echo json_encode(array("success" => true, "msg" => $ipos->lang['suppliers_msg_delete']));
	else
		echo json_encode(array("success" => false, "msg" => $ipos->lang['suppliers_err']));
	
}

public function check_account_number() {
	$var = filter_var_array($_REQUEST, $this->flt_account);
	if (empty($var['account_number'])) {
		echo 'true';
		return;
	}
	
	$result = $this->person->check_account_number('suppliers', $var['account_number'], $var['person_id']) ? 'true': 'false';
	echo $result;
}

public function get(&$ipos) {
	if (empty($_REQUEST['id'])) {
		$ipos->assign('err', $ipos->lang['suppliers_err_get']);
		echo $ipos->fetch('err_msg.tpl');
		return;
	}
	
	$result = $this->person->get_info('suppliers', intval($_REQUEST['id']));
	if (!empty($result)) {
		$ipos->assign('language', $ipos->session->usrdata('lang'));
		$ipos->assign('person', $result[0]);
		echo $ipos->fetch('suppliers/form.tpl');
	} else {
		$ipos->assign('err', $ipos->lang['suppliers_err_get']);
		echo $ipos->fetch('err_msg.tpl');
	}
}

public function suggest_search(&$ipos) {
	if (empty($_REQUEST['term'])) return;
	
	$var = filter_var($_REQUEST['term'], FILTER_SANITIZE_SPECIAL_CHARS);
	if (!empty($result = $this->person->search_suggestions('suppliers', $var, $this->table_struct)))
		echo json_encode($result);
}

public function search(&$ipos) {
	$var = filter_var_array($_REQUEST, $this->flt_search);
	$limit = empty($var['limit']) ? 100 : $var['limit'];
	$suggestions  = $this->person->search('suppliers', $var, $this->table_struct, 0, $limit);
	
	$ipos->assign('controller_name', 'suppliers');
	$ipos->assign('person', $suggestions);
	$ipos->assign('subgrant', $this->subgrant);
	echo json_encode(array('rows' => $ipos->fetch('suppliers/table_row.tpl'), 'offset' => $limit, 'total_rows' => count($suggestions)));
}

public function suggest_supplier() {
	if (empty($_REQUEST['term'])) return;
	
	$var = filter_var($_REQUEST['term'], FILTER_SANITIZE_SPECIAL_CHARS);
	$this->db->query('SELECT distinct(company_name), person_id FROM suppliers WHERE deleted=0 AND company_name LIKE ? LIMIT 0,25');
	if (!empty($sel = $this->db->select(array(array('%'. $var .'%'))))) {
		$result = array();
		foreach ($sel as $v) {
			$result[] = array('label'=>$v['company_name'], 'value'=>$v['person_id']);
		}
		
		echo json_encode($result);
	}
}
}
?>