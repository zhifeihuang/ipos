<?php
require_once('../libs/secure.php');

class giftcard extends secure {
private $flt = array('person_id'=>FILTER_VALIDATE_INT,
					'number'=>FILTER_VALIDATE_INT,
					'value'=>FILTER_VALIDATE_FLOAT);
private $flt_more = array('offset' => FILTER_VALIDATE_INT, 
						'limit' => FILTER_VALIDATE_INT, 
						'type' => FILTER_SANITIZE_SPECIAL_CHARS, 
						'label' => FILTER_SANITIZE_SPECIAL_CHARS, 
						'value' => FILTER_SANITIZE_SPECIAL_CHARS);
private $flt_number = array('id'=>FILTER_VALIDATE_INT,
							'number'=>FILTER_VALIDATE_INT);

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
									'more'=>'giftcards',
									'create'=>'giftcards_insert',
									'save'=>'giftcards_insert',
									'delete'=>'giftcards_delete',
									'check_number'=>'giftcards_insert',
									'search'=>'giftcards',
									'suggest_search'=>'giftcards');
	parent::__construct($db, $grant, $permission);
}

public function view(&$ipos) {
	$result = $this->get_all();
	$result = $result === false ? array() : $result;
	
	$ipos->assign('controller_name', 'giftcards');
	$ipos->assign('subgrant', $this->subgrant);
	$ipos->assign('offset', 100);
	$ipos->assign('gift', $result);
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
	if (in_array(false, $var, true) || ($maxid = $this->maxid()) === false) {
		echo json_encode(array("success" => false, "msg" => $ipos->lang['giftcards_err_param']));
		return;
	}
	
	$data = array();
	$data[] = date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']);
	$data[] = ++$maxid;
	$data[] = $var['number'];
	$data[] = $var['value'];
	$data[] = $var['person_id'];
	$data[] = $ipos->session->usrdata('person_id');
	
	$this->db->beginTransaction();
	$this->db->query('INSERT INTO giftcards (record_time,giftcard_id,giftcard_number,val,person_id,emp_id)
						VALUES(?,?,?,?,?,?)');
	if ($this->db->insert(array($data)) === false) {
		$this->db->rollBack();
		echo json_encode(array("success" => false, "msg" => $ipos->lang['giftcards_err_save']));
		return;
	}
	
	$this->db->query('SELECT p.first_name as first_name, p.last_name as last_name FROM customers as c
		JOIN person as p ON p.person_id=c.person_id
		WHERE c.deleted=0 AND c.person_id=?');
	if ($person = $this->db->select(array(array($var['person_id'])))) {
		$this->db->commit();
		$person[0]['giftcard_id'] = $maxid;
		$person[0]['val'] = $var['value'];
		$person[0]['giftcard_number'] = $var['number'];
		$ipos->assign('gift', $person);
		echo json_encode(array("success" => true, "msg" => $ipos->lang['giftcards_msg_save'], "id"=>$maxid, "row"=>$ipos->fetch('giftcard/table_row.tpl')));
	} else {
		$this->db->rollBack();
		echo json_encode(array("success" => false, "msg" => $ipos->lang['giftcards_err_save']));
	}
}

public function delete(&$ipos) {
	if (!isset($_REQUEST['ids']) || !is_array($_REQUEST['ids'])) {
		$ipos->assign('err', $ipos->lang['giftcards_err_param']);
		echo $ipos->fetch('err_msg.tpl');
		return;
	}
	
	$ids = array();
	foreach ($_REQUEST['ids'] as $v)
		$ids[] = array(intval($v));
	
	$this->db->beginTransaction();
	$this->db->query('UPDATE giftcards SET deleted=1 WHERE giftcard_id=?');
	if ($this->db->update($ids)) {
		$this->db->commit();
		echo json_encode(array("success" => true, "msg" => $ipos->lang['giftcards_msg_delete']));
	} else {
		$this->db->rollBack();
		echo json_encode(array("success" => false, "msg" => $ipos->lang['giftcards_err_delete']));
	}
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
			JOIN customers as c ON c.person_id=g.person_id
			JOIN person as p ON g.person_id=p.person_id 
			WHERE c.deleted=0 AND g.deleted=0
			AND (');
	$this->db->order('ORDER BY g.giftcard_id ASC');
	if (!empty($result = $this->db->search_suggestions($var, $this->table_struct, $this->sconv, array($this, 'sugg_conv'))))
		echo json_encode($result);
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

private function maxid() {
	$this->db->query('SELECT MAX(giftcard_id) FROM giftcards');
	return $this->db->max();
}

private function get_all($offset=0, $limit=100) {
	$this->db->query('SELECT g.*, p.first_name as first_name, p.last_name as last_name FROM giftcards as g
		JOIN customers as c ON c.person_id=g.person_id
		JOIN person as p ON p.person_id=c.person_id
		WHERE g.deleted=0 AND c.deleted=0 ORDER BY g.giftcard_id ASC LIMIT '. $offset .','. $limit);
		
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
		JOIN customers as c ON c.person_id=g.person_id
		JOIN person as p ON g.person_id=p.person_id 
		WHERE c.deleted=0 AND g.deleted=0
		AND ');
	$this->db->order('ORDER BY g.giftcard_id ASC');
	$result = $this->db->search($var, $this->conv, array($this, 'conversion'), $offset, $limit);
	if ($result === -1) {
		$this->db->query('SELECT * FROM giftcards as g
			JOIN customers as c ON c.person_id=g.person_id
			JOIN person as p ON g.person_id=p.person_id 
			WHERE c.deleted=0 AND g.deleted=0
			AND (');
		$this->db->order('ORDER BY g.giftcard_id ASC');
		$result = $this->db->search_suggestions($var['label'], $this->table_struct, $this->sconv, array($this, 'sugg_conv'), false, $offset, $limit);
	}
	
	return $result;
}
}
?>