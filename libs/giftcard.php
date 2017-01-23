<?php
require_once('../libs/secure.php');

class giftcard extends secure {
private $flt = array('number'=>FILTER_SANITIZE_SPECIAL_CHARS,
					'val'=>FILTER_VALIDATE_INT);
private $flt_more = array('offset' => FILTER_VALIDATE_INT, 
						'limit' => FILTER_VALIDATE_INT, 
						'type' => FILTER_SANITIZE_SPECIAL_CHARS, 
						'label' => FILTER_SANITIZE_SPECIAL_CHARS, 
						'value' => FILTER_SANITIZE_SPECIAL_CHARS);
private $flt_number = array('id'=>FILTER_VALIDATE_INT,
							'number'=>FILTER_SANITIZE_SPECIAL_CHARS);

private $flt_search = array('limit' => FILTER_VALIDATE_INT,
							'label' => FILTER_SANITIZE_SPECIAL_CHARS,
							'value' => FILTER_SANITIZE_SPECIAL_CHARS);
							
public $conv = array('name'=>'CONCAT(first_name,last_name)', 
					'id' => 'giftcard_number',
					'number' => 'giftcard_number');
public $sconv = array('first_name'=>'CONCAT(first_name,last_name)',
				'last_name'=>'CONCAT(first_name,last_name)');
private $table_struct = array('number'=>array('giftcard_number'),
							'string'=>array('first_name', 'last_name'));

public function __construct($db, $grant, $permission) {
	$this->func_permission = array('view'=>'giftcards',
									'save'=>'giftcards_insert',
									'delete'=>'giftcards_delete',
									'update'=>'giftcards_update',
									'check_number'=>'giftcards_insert',
									'search'=>'giftcards',
									'suggest_search'=>'giftcards',
									'suggest_sale'=>'sales_insert',
									'suggest_charge'=>'giftcards_update',
									'find'=>'giftcards',
									'charge'=>'giftcards_update',
									'create'=>'sales_insert',);
	parent::__construct($db, $grant, $permission);
}

public function view(&$ipos) {
	if (isset($_REQUEST['get'])) {
		if (parent::has_grant($_REQUEST['get'])) {
			switch ($_REQUEST['get']) {
				case 'find': $tpl = 'giftcard/find.tpl'; break;
				case 'charge': $tpl = 'giftcard/charge.tpl'; break;
				case 'create': $tpl = 'giftcard/create.tpl'; break;
			}
			
			if ($tpl)
				echo json_encode(array("success" => true, "data" => $ipos->fetch($tpl)));
			else
				echo json_encode(array("success" => false, "msg" => $ipos->lang['giftcards_err_param']));
				
			return;
		} else {
			echo json_encode(array("success" => false, "msg" => $ipos->lang['no_access']));
			return;
		}
	}
	
	$ipos->assign('controller_name', 'giftcards');
	$ipos->assign('subgrant', $this->subgrant);
	$ipos->display('giftcard/manage.tpl');
}

public function more(&$ipos) {
	$var = filter_var_array($_REQUEST, $this->flt_more);
	$var['offset'] = empty($var['offset']) ? 0 : $var['offset'];
	$var['limit'] = empty($var['limit']) ? 100 : $var['limit'];
	
	$data = false;
	if ('more' == $var['type']) {
		$data = $this->get_all($var['offset'], $var['limit']);
	} else if ('search' == $var['type']) {
		$data = $this->search_data($var, $var['offset'], $var['limit']);
	}
	
	if (!empty($data)) {
		$ipos->assign('controller_name', 'giftcards');
		$ipos->assign('gift', $data);
		$ipos->assign('subgrant', $this->subgrant);
		echo json_encode(array('success'=>true, 'msg'=>'', 'data'=>$ipos->fetch('giftcard/table_row.tpl'), 'offset'=>($var['offset'] + $var['limit'])));
	} else if ($data === false) {
		echo json_encode(array('success'=>false, 'msg'=>$ipos->lang['gifts_err']));
	} else {
		echo json_encode(array('success'=>true, 'msg'=>$ipos->lang['common_no_more_data']));
	}
}

public function create(&$ipos) {
	echo $ipos->fetch('giftcard/form.tpl');
}

public function save(&$ipos) {
	$var = filter_var_array($_REQUEST, $this->flt);
	if (empty($var['number']) || $var['val'] < 0 || ($maxid = $this->maxid()) === false) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['giftcards_err_param']));
		return;
	}
	
	$data = array();
	$data[] = date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']);
	$data[] = ++$maxid;
	$data[] = $var['number'];
	$data[] = $var['val'];
	$data[] = -1;
	$data[] = $ipos->session->usrdata('person_id');
	
	$this->db->beginTransaction();
	$this->db->query('INSERT INTO giftcards (record_time,giftcard_id,giftcard_number,val,person_id,emp_id)
						VALUES(?,?,?,?,?,?)');
	if ($this->db->insert(array($data)) === false) {
		$this->db->rollBack();
		echo json_encode(array("success" => false, "msg" => $ipos->lang['giftcards_err_save']));
		return;
	}
	
	unset($data[2]);
	$this->db->query('INSERT INTO giftcard_charge (record_time,giftcard_id,val,person_id,emp_id)
						VALUES(?,?,?,?,?)');
	if ($this->db->insert(array($data)) === false) {
		$this->db->rollBack();
		echo json_encode(array("success" => false, "msg" => $ipos->lang['giftcards_err_save']));
		return;
	}
	
	$this->db->commit();
	echo json_encode(array("success" => true, "msg" => $ipos->lang['giftcards_msg_save']));
}

public function delete(&$ipos) {
	if (!isset($_REQUEST['id']) || !isset($_REQUEST['status'])) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['giftcards_err_param']));
		return;
	}
	
	$id = intval($_REQUEST['id']);
	$delete = $_REQUEST['status'] === '+' ? 1 : 0;
	$this->db->query('UPDATE giftcards SET deleted='. $delete .' WHERE giftcard_id='. $id);
	if ($this->db->execute()) {
		$this->db->query('SELECT g.*, p.first_name as first_name, p.last_name as last_name FROM giftcards as g
		LEFT JOIN person as p ON p.person_id=g.person_id 
		WHERE g.giftcard_id='. $id);
		$result = $this->db->select();
		$ipos->assign('gift', $result);
		$ipos->assign('subgrant', $this->subgrant);
		echo json_encode(array('success'=>true, 'id'=>$id, 'row'=>$ipos->fetch('giftcard/table_row.tpl')));
	} else {
		echo json_encode(array("success"=>false, "msg" => $ipos->lang['giftcards_err_delete']));
	}
}

public function update(&$ipos) {
	if (empty($_REQUEST['giftcard'])) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['giftcards_err_param']));
		return;
	}
	
	$date = date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']);
	$emp = $ipos->session->usrdata('person_id');
	foreach ($_REQUEST['giftcard'] as $k=>$v) {
		$val = (int)$v;
		if ($val < 0) {
			echo json_encode(array("success" => false, "msg" => $ipos->lang['giftcards_err_param']));
			return;
		}
		
		$data[] = array($val, (int)$k);
		$cdata[] = array($date, (int)$k, $val, (int)$_REQUEST['person'][$k], $emp);
	}
	
	$this->db->beginTransaction();
	$this->db->query('UPDATE giftcards SET val=val+? WHERE giftcard_id=?');
	if ($this->db->update($data) === false) {
		$this->db->rollBack();
		echo json_encode(array("success" => false, "msg" => $ipos->lang['giftcards_err_charge']));
		return;
	}
	
	$this->db->query('INSERT INTO giftcard_charge (record_time,giftcard_id,val,person_id,emp_id)
						VALUES(?,?,?,?,?)');
	if ($this->db->update($cdata) === false) {
		$this->db->rollBack();
		echo json_encode(array("success" => false, "msg" => $ipos->lang['giftcards_err_charge']));
		return;
	}
	
	$this->db->commit();
	echo json_encode(array("success" => true, "msg" => $ipos->lang['giftcards_msg_charge']));
}

public function check_number() {
	$var = filter_var_array($_REQUEST, $this->flt_number);
	if (empty($var['number'])) {
		echo 'true';
		return;
	}
	
	$this->db->query('SELECT giftcard_id from giftcards WHERE giftcard_number=?');
	$result = $this->db->check($var['number'], $var['id']);
	echo $result ? 'true' : 'false';
}

public function suggest_search() {
	if (empty($_REQUEST['term'])) return;
	
	$var = filter_var($_REQUEST['term'], FILTER_SANITIZE_SPECIAL_CHARS);
	$this->db->query('SELECT * FROM giftcards as g
			LEFT JOIN person as p ON g.person_id=p.person_id 
			WHERE ');
	$this->db->order('ORDER BY g.giftcard_id ASC');
	if (!empty($result = $this->db->search_suggestions($var, $this->table_struct, $this->sconv, array($this, 'sugg_conv'))))
		echo json_encode($result);
}

public function suggest_sale() {
	if (empty($_REQUEST['term'])) return;
	
	$var = filter_var($_REQUEST['term'], FILTER_SANITIZE_SPECIAL_CHARS);
	$this->db->query('SELECT * FROM giftcards WHERE deleted=0 AND giftcard_number=?');
	$result = $this->db->select(array(array($var)));
	foreach ($result as $row) {
		$suggestion[] = array('value'=>$row['giftcard_number'] .' '. $row['val'], 'label'=>$row['giftcard_number']);
	}
	
	if (isset($suggestion[0])) echo json_encode($suggestion);
}

public function search(&$ipos) {
	$var = filter_var_array($_REQUEST, $this->flt_search);
	$limit = empty($var['limit']) ? 100 : $var['limit'];
	$result = $this->search_data($var, 0, $limit);
	
	$result = $result === false ? array() : $result;
	$ipos->assign('gift', $result);
	$ipos->assign('subgrant', $this->subgrant);
	echo json_encode(array('rows' => $ipos->fetch('giftcard/table_row.tpl'), 'offset' => 100, 'total_rows' => count($result)));
}

public function suggest_charge(&$ipos) {
	if (empty($_REQUEST['term'])) return;
	
	$var = filter_var($_REQUEST['term'], FILTER_SANITIZE_SPECIAL_CHARS);
	$this->db->query('SELECT g.*, p.first_name as first_name, p.last_name as last_name FROM giftcards as g
		LEFT JOIN person as p ON p.person_id=g.person_id 
		WHERE g.deleted=0 AND g.giftcard_number=?');
	if ($result = $this->db->select(array(array($var)))) {
		$ipos->assign('gift', $result);
		echo json_encode(array(array('value'=>json_encode(array('id'=>$result[0]['giftcard_id'],'rows'=>$ipos->fetch('giftcard/charge_row.tpl'))), 'label'=>$result[0]['giftcard_number'])));
	}
}

private function maxid() {
	$this->db->query('SELECT MAX(giftcard_id) FROM giftcards');
	return $this->db->max();
}

private function get_all($offset=0, $limit=100) {
	$this->db->query('SELECT g.*, p.first_name as first_name, p.last_name as last_name FROM giftcards as g
		LEFT JOIN person as p ON g.person_id=p.person_id
		WHERE g.deleted=0 ORDER BY g.giftcard_id ASC LIMIT '. $offset .','. $limit);
		
	return $this->db->select();
}

public function conversion(&$key, &$val, $index) {
	switch ($key[$index]) {
	case 'name':
		$key[$index] = $this->conv['name'];
		$val[$index] = preg_replace('/\s+/', '', strtolower($val[$index]), 1);
	break;
	default:
		$key[$index] = $this->conv[$key[$index]];
	break;
	}
}

public function sugg_conv(&$key, &$val, $index) {
	switch ($key[$index]) {
	case 'first_name':
		$key[$index] = $this->sconv['first_name'];
		$val[$index] = preg_replace('/\s+/', '', strtolower($val[$index]), 1);
		if (($idx = array_search('last_name', $key)) !== false) unset($key[$idx]);
	break;
	case 'last_name':
		$key[$index] = $this->sconv['last_name'];
		$val[$index] = preg_replace('/\s+/', '', strtolower($val[$index]), 1);
		if (($idx = array_search('first_name', $key)) !== false) unset($key[$idx]);
	break;
	default:
		$key[$index] = $this->conv[$key[$index]];
	break;
	}
}

private function search_data($var, $offset=0, $limit=100) {
	$this->db->query('SELECT g.*, p.first_name as first_name, p.last_name as last_name FROM giftcards as g
		LEFT JOIN person as p ON p.person_id=g.person_id 
		WHERE ');
	$this->db->order('ORDER BY g.giftcard_id ASC');
	$result = $this->db->search($var, $this->conv, array($this, 'conversion'), $offset, $limit);
	if ($result === -1) {
		$this->db->query('SELECT * FROM giftcards as g
			LEFT JOIN person as p ON p.person_id=g.person_id 
			WHERE ');
		$this->db->order('ORDER BY g.giftcard_id ASC');
		$result = $this->db->search_suggestions($var['label'], $this->table_struct, $this->sconv, array($this, 'sugg_conv'), false, $offset, $limit);
	}
	
	return $result;
}
}
?>