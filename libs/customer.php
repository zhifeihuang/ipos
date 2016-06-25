<?php
require_once('../libs/person.php');
require_once('../libs/secure.php');
require_once('../libs/config.php');
require_once '../libs/help/common.php';
	

class customer extends secure {
private $person;

private $flt_more = array('offset' => FILTER_VALIDATE_INT, 
						'limit' => FILTER_VALIDATE_INT, 
						'type' => FILTER_SANITIZE_SPECIAL_CHARS, 
						'label' => FILTER_SANITIZE_SPECIAL_CHARS, 
						'value' => FILTER_SANITIZE_SPECIAL_CHARS);
private $flt = array('company_name' => FILTER_SANITIZE_SPECIAL_CHARS,
					'account_number' => FILTER_SANITIZE_SPECIAL_CHARS,
					'taxable' => array('filter'=>FILTER_VALIDATE_INT, 'options'=>array('min_range'=>0,'max_range'=>1)));
private $flt_search = array('limit' => FILTER_VALIDATE_INT,
							'label' => FILTER_SANITIZE_SPECIAL_CHARS,
							'value' => FILTER_SANITIZE_SPECIAL_CHARS);
private $flt_account = array('account_number' => FILTER_SANITIZE_SPECIAL_CHARS,
							'person_id' => FILTER_VALIDATE_INT);
private $table_struct = array('number'=>array('account_number','phone_number','p.person_id'),
							'string'=>array('first_name', 'last_name'));
							
public function __construct($db, $grant, $permission) {
	$this->func_permission = array('view'=>'customers',
									'more'=>'customers',
									'excel'=>'customers_insert',
									'excel_import'=>'customers_insert',
									'do_excel_import'=>'customers_insert',
									'create'=>'customers_insert',
									'save'=>'customers_insert',
									'delete'=>'customers_delete',
									'get'=>'customers',
									'suggest_search'=>'customers',
									'search'=>'customers',
									'check_account_number'=>'customers_update',
									'update'=>'customers_update',
									'suggest_gift'=>'giftcards_insert',
									'suggest_sale'=>'sales');
	parent::__construct($db, $grant, $permission);
	$this->person = new person($db);
}

public function view(&$ipos) {
	if (($person = $this->person->get_all('customers')) === false) {
		$ipos->err_page('customers_err');
		return;
	}
	
	$ipos->assign('controller_name', 'customers');
	$ipos->assign('manage_table', 'person');
	$ipos->assign('person', $person);
	$ipos->assign('offset', 100);
	$ipos->assign('subgrant', $this->subgrant);
	$ipos->display('person/manage.tpl');
}

public function more(&$ipos) {
	$var = filter_var_array($_REQUEST, $this->flt_more);
	$var['offset'] = empty($var['offset']) ? 0 : $var['offset'];
	$var['limit'] = empty($var['limit']) ? 100 : $var['limit'];
	
	$data = false;
	if ('more' == $var['type']) {
		$data = $this->person->get_all('customers', $var['offset'], $var['limit']);
	} else if ('search' == $var['type']) {
		$data = $this->person->search('customers', $var, $this->table_struct, $var['offset'], $var['limit']);
	}
	
	if (!empty($data)) {
		$ipos->assign('controller_name', 'customers');
		$ipos->assign('manage_table', 'person');
		$ipos->assign('person', $data);
		$ipos->assign('subgrant', $this->subgrant);
		echo json_encode(array('success'=>true, 'msg'=>'', 'data'=>$ipos->fetch('person/table_row.tpl'), 'offset'=>($var['offset'] + $var['limit'])));
	} else if ($data === false) {
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['customers_err_get']));
	} else {
		echo json_encode(array('success'=>true, 'msg'=>$ipos->lang['common_no_more_data']));
	}
}

public function get(&$ipos) {
	if (empty($_REQUEST['id'])) {
		$ipos->assign('err', $ipos->lang['customers_err_get']);
		echo $ipos->fetch('err_msg.tpl');
		return;
	}
	
	$result = $this->person->get_info('customers', intval($_REQUEST['id']));
	if (!empty($result)) {
		$ipos->assign('language', $ipos->session->usrdata('lang'));
		$ipos->assign('person', $result[0]);
		echo $ipos->fetch('customers/form.tpl');
	} else {
		$ipos->assign('err', $ipos->lang['customers_err_get']);
		echo $ipos->fetch('err_msg.tpl');
	}
}

public function excel(&$ipos) {
	$app = include '../config/app_con.php';
	$name = 'import_customers.csv';
	$file = $app['download_dir'] . $name;
	$data = file_get_contents($file);
	
	include_once '../libs/help/common.php';
	force_download($name, $data);
}

public function excel_import(&$ipos) {
	$ipos->assign('controller_name', 'customers');
	echo $ipos->fetch('partial/form_excel_import.tpl');
}

public function do_excel_import(&$ipos) {
	$failCodes = array();
	if ($_FILES['file_path']['error'] != UPLOAD_ERR_OK) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['import_err']));
		return;
	}
	else {
		if (($handle = fopen($_FILES['file_path']['tmp_name'], "r")) !== false) {
			$this->db->beginTransaction();
		
			//Skip first row
			fgetcsv($handle);
			
			$i=1;
			while (($data = fgetcsv($handle)) !== false) {
				$person_data = array(
					'first_name'=>$data[0],
					'last_name'=>$data[1],
					'gender'=>$data[2],
					'email'=>$data[3],
					'phone_number'=>$data[4],
					'address_1'=>$data[5],
					'address_2'=>$data[6],
					'city'=>$data[7],
					'state'=>$data[8],
					'zip'=>$data[9],
					'country'=>$data[10],
					'comments'=>$data[11]);
				
				$customer_data=array('taxable'=>stripos("1yes", $data[13]) === false ? 0:1);
				
				$account_number = $data[12];
				$invalidated = false;
				if ($account_number != "") {
					$customer_data['account_number'] = $account_number;
					$invalidated = !$this->person->check_account_number($account_number);
				}
				
				$pdata = $this->person->filter($person_data);
				$cdata = filter_var_array($customer_data, $this->flt);
				if($invalidated || !$this->person->save($pdata, $cdata, 'customers')) {
					$failCodes[] = $i;
				}
				
				$i++;
			}
		}
		else {
			echo json_encode(array("success" => false, "msg" => $ipos->lang['import_err']));
			return;
		}
	}

	if(count($failCodes) > 0) {
		$this->db->rollBack();
		$msg = $ipos->lang['import_check'] . "(" .$cnt . "): " .implode(", ", $failCodes);
		echo json_encode(array('success'=>false, 'msg'=>$msg));
	} else {
		$this->db->commit();
		echo json_encode(array('success'=>true, 'msg'=>$ipos->lang['import_msg']));
	}
}

public function create(&$ipos) {
	$ipos->assign('language', $ipos->session->usrdata('lang'));
	echo $ipos->fetch('customers/form.tpl');
}

public function save(&$ipos) {
	$pdata = $this->person->filter($_REQUEST);
	$cdata = filter_var_array($_REQUEST, $this->flt);
	
	$this->db->beginTransaction();
	if ($result = $this->person->save($pdata, $cdata, 'customers')) {
		$this->db->commit();
		$pdata['person_id'] = $result;
		$ipos->assign('person', array(array_merge($pdata, $cdata)));
		$ipos->assign('controller_name', 'customers');
		$ipos->assign('subgrant', $this->subgrant);
		echo json_encode(array('success'=>true, 'msg'=>$ipos->lang['customers_msg_save'], 'id'=>$result, 'row'=>$ipos->fetch('person/table_row.tpl')));
	} else {
		$this->db->rollBack();
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['customers_err_save']));
	}
}

public function update(&$ipos) {
	if (empty($_REQUEST['id'])) {
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['customers_err_update']));
		return;
	}
	$id = intval($_REQUEST['id']);
	
	$pdata = $this->person->filter($_REQUEST);
	$cdata = filter_var_array($_REQUEST, $this->flt);
	
	$this->db->beginTransaction();
	if ($this->person->save($pdata, $cdata, 'customers', $id)) {
		$this->db->commit();
		$pdata['person_id'] = $id;
		$ipos->assign('person', array(array_merge($pdata, $cdata)));
		$ipos->assign('controller_name', 'customers');
		$ipos->assign('subgrant', $this->subgrant);
		echo json_encode(array('success'=>true, 'msg'=>$ipos->lang['customers_msg_update'], 'id'=>$id, 'row'=>$ipos->fetch('person/table_row.tpl')));
	} else {
		$this->db->rollBack();
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['customers_err_update']));
	}
}

public function delete(&$ipos) {
	if (empty($_REQUEST['ids'])) {
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['customers_err_delete']));
		return;
	}
	
	if ($this->person->delete('customers', $_REQUEST['ids']))
		echo json_encode(array('success'=>true, 'msg'=>$ipos->lang['customers_msg_delete']));
	else
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['customers_err_delete']));
	
}

public function suggest_search() {
	if (empty($_REQUEST['term'])) return;
	
	$var = filter_var($_REQUEST['term'], FILTER_SANITIZE_SPECIAL_CHARS);
	if (!empty($result = $this->person->search_suggestions('customers', $var, $this->table_struct)))
		echo json_encode($result);
}

public function search(&$ipos) {
	$var = filter_var_array($_REQUEST, $this->flt_search);
	$limit = empty($var['limit']) ? 100 : $var['limit'];
	$suggestions = $this->person->search('customers', $var, $this->table_struct, 0, $limit);
	
	$ipos->assign('controller_name', 'customers');
	$ipos->assign('person', $suggestions);
	$ipos->assign('subgrant', $this->subgrant);
	echo json_encode(array('rows' => $ipos->fetch('person/table_row.tpl'), 'offset' => $limit, 'total_rows' => count($suggestions)));
}

public function check_account_number() {
	$var = filter_var_array($_REQUEST, $this->flt_account);
	if (empty($var['account_number'])) {
		echo 'true';
		return;
	}
	
	$result = $this->person->check_account_number('customers', $var['account_number'], $var['person_id']) ? 'true': 'false';
	echo $result;
}

public function suggest_gift() {
	if (empty($_REQUEST['term'])) return;
	
	$var = filter_var($_REQUEST['term'], FILTER_SANITIZE_SPECIAL_CHARS);
	if (!empty($result = $this->person->search_suggestions('customers', $var, $this->table_struct, false))) {
		$suggestion = array();
		foreach ($result as $v) {
			$suggestion[] = array('label'=>$v['last_name'] .' '. $v['first_name'] .','. $v['phone_number'], 'value'=>$v['person_id']);
		}
		echo json_encode($suggestion);
	}
}

public function suggest_sale() {
	if (empty($_REQUEST['term'])) return;
	
	$var = filter_var($_REQUEST['term'], FILTER_SANITIZE_SPECIAL_CHARS);
	if (!empty($result = $this->person->search_suggestions('customers', $var, $this->table_struct, false))) {
		$suggestion = array();
		foreach ($result as $v) {
			$suggestion[] = array('label'=>$v['account_number'], 'value'=>$v['person_id']);
		}
		echo json_encode($suggestion);
	}
}

public static function discount($db, $id) {
	$db->query('SELECT discount FROM customers WHERE deleted=0 AND id=?');
	return $db->select(array(array($id)));
}
}
?>
