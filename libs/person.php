<?php
require_once '../libs/help/common.php';
	
class person {
private $db;
public $flt = array('first_name' => FILTER_SANITIZE_SPECIAL_CHARS,
					'last_name' => FILTER_SANITIZE_SPECIAL_CHARS,
					'gender' => array('filter'=>FILTER_VALIDATE_INT, 'options'=>array('min_range'=>0,'max_range'=>1)),
					'phone_number' => FILTER_SANITIZE_NUMBER_INT,
					'email' => FILTER_VALIDATE_EMAIL,
					'address_1' => FILTER_SANITIZE_SPECIAL_CHARS,
					'address_2' => FILTER_SANITIZE_SPECIAL_CHARS,
					'city' => FILTER_SANITIZE_SPECIAL_CHARS,
					'state' => FILTER_SANITIZE_SPECIAL_CHARS,
					'zip' => FILTER_SANITIZE_SPECIAL_CHARS,
					'country' => FILTER_SANITIZE_SPECIAL_CHARS,
					'comments' => FILTER_SANITIZE_SPECIAL_CHARS);

public $conv = array('name'=>'CONCAT(first_name,last_name)',
					'id' => 'p.person_id', 
					'company'=>'company_name', 
					'account'=>'account_number', 
					'phone'=>'phone_number',
					'person_id' => 'p.person_id');
public $sconv = array('first_name'=>'CONCAT(first_name,last_name)',
					'last_name'=>'CONCAT(first_name,last_name)',
					'person_id' => 'p.person_id');
							
public $security = array('password');

public function __construct($db) {
	$this->db = $db;
}

public function maxid() {
	$this->db->query("SELECT MAX(person_id) FROM person");
	return $this->db->max();
}

public function filter($data) {
	return filter_var_array($data, $this->flt);
}

public function save(&$pdata, &$tdata, $table, $id = false) {
	if (!empty($pdata['phone_number'])) $pdata['phone_number'] = str_replace('-', '', $pdata['phone_number']);
	if ($pdata['email'] == null || $pdata['email'] == false)
		$pdata['email'] = '';
	
	if (($result = $this->save_table($pdata, 'person', $id)) === false
		|| $this->save_table($tdata, $table, $id, $result) === false)
		return false;
		
	return $result;
}

public function get_all($table, $offset=0, $limit=100) {
	$this->db->query('SELECT * FROM '. $table .' as c JOIN person as p ON c.person_id=p.person_id WHERE c.deleted=0 ORDER BY c.person_id ASC limit '. $offset .','. $limit);
	if (($result = $this->db->select()) === false) return false;
	
	foreach ($result as &$row) {
		foreach ($this->security as $v) {
			if (isset($row[$v])) unset($row[$v]);
		}
	}
	
	return $result;
}
		
private function save_table(&$data, $table, $id=false, $pre_id=false) {
	$result = true;
	if ($id) {
		$str = null;
		$tmp = array();
		foreach ($data as $k => $v) {
			if ($v === false || $v === null) continue;
			$str .= $k . '=?,';
			$tmp[] = $v;
		}
		if (empty($str)) return false;
		
		$tmp[] = $id;
		$query ='UPDATE '. $table .' SET ' . rtrim($str, ',') . ' WHERE person_id=?';
		$this->db->query($query);
		if ($this->db->update(array($tmp)) === false) return false;
	} else {
		if ($pre_id) {
			$result = $pre_id;
		} else {
			if (($result = $this->maxid()) === false) return false;
			++$result;
		}
		
		$q1 = null;
		$tmp = array();
		$tmp[] = $result;
		foreach ($data as $k => $v) {
			if ($v === false || $v === null) continue;
			$q1 .= ','. $k;
			$tmp[] = $v;
		}
		if (empty($q1)) return false;
		
		$query ='INSERT INTO '. $table .' (person_id' . $q1 . ') VALUES(?' . str_repeat(',?', count($tmp) - 1) . ')';
		$this->db->query($query);
		if ($this->db->insert(array($tmp)) === false) return false;
	}

	return $result;
}
	
public function delete($table, $ids) {
	$tmp = array();
	if (is_array($ids)) {
		foreach ($ids as $v) {
			$tmp[] = array(intval($v));
		}
	} else {
		$tmp[] = array(intval($ids));
	}
	
	$this->db->query('UPDATE '. $table .' SET deleted=1 WHERE person_id=?');
	if ($this->db->update($tmp))
		return true;
	else
		return false;
}

public function check_account_number($table, $account_number, $id=false) {
	$this->db->query('SELECT person_id from '. $table .' WHERE account_number=?');
	return $this->db->check($account_number, $id);
}
	
public function get_info($table, $id) {
	$this->db->query('SELECT * FROM '. $table .' as c JOIN person as p ON c.person_id=p.person_id WHERE c.deleted=0 AND c.person_id=?');
	if (($result = $this->db->select(array(array($id)))) === false) return false;
	
	foreach ($result as &$row) {
		foreach ($this->security as $v) {
			if (isset($row[$v])) unset($row[$v]);
		}
	}
	
	return $result;
}

public function search($table, $var, $tstruct, $offset=0, $limit=100) {
	$this->db->query('SELECT * FROM '. $table .'  as c JOIN person as p ON c.person_id=p.person_id 
			WHERE c.deleted=0 
			AND ');
	$this->db->order('ORDER BY c.person_id');
	$result = $this->db->search($var, $this->conv, array($this, 'conversion'), $offset, $limit);
	if ($result === -1) {
		$result = $this->search_suggestions($table, $var['label'], $tstruct, false, $offset, $limit);
	}
	
	return $result === false ? array() : $result;
}

public function search_suggestions($table, $search, $tstruct, $sugg=true, $offset=0, $limit=25) {
	$this->db->query('SELECT * FROM '. $table .' as c JOIN person as p ON c.person_id=p.person_id 
				WHERE c.deleted=0 
				AND ');
	$this->db->order('ORDER BY c.person_id');
	return $this->db->search_suggestions($search, $tstruct, $this->sconv, array($this, 'sugg_conv'), $sugg, $offset, $limit);
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
}
?>
