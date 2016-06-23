<?php
class stock {
private $db;

public function __construct($db) {
	$this->db = $db;
}

public function change($data) {
	$this->db->query("UPDATE stock_locations SET location_name=? WHERE location_name=?");
	return $this->db->update($data);
}

public function add($data) {
	if (($maxid = $this->maxid()) === false) return false;
	
	$tmp = array();
	foreach ($data as $v) {
		$tmp[] = array($v);
	}
	
	$this->db->query('SELECT * FROM stock_locations WHERE location_name=?');
	$result = $this->db->select($tmp);
	if (!empty($result)) {
		$tmp = array();
		foreach ($result as $v) {
			if (($idx = array_search($v['location_name'], $data)) !== false) unset($data[$idx]);
			
			if ($v['deleted'] === 0) continue;
			$tmp[] = array($v['location_id']);
		}
		
		$this->db->query("UPDATE stock_locations SET deleted=0 WHERE location_id=?");
		if  ($this->db->update($tmp) === false) return false;
	}
	
	$this->db->query('INSERT INTO stock_locations (location_id,location_name,deleted) VALUES (?,?,0)');
	$add = array();
	foreach ($data as $v) {
		$add[] = array(++$maxid, $v);
	}
	
	if (empty($add) || $this->db->insert($add))
		return true;
	else
		return false;
}

public function remove($data) {
	$this->db->query("UPDATE stock_locations SET deleted=1 WHERE location_name=?");
	$remove = array();
	foreach ($data as $v) {
		$remove[] = array($v);
	}
	return $this->db->update($remove);
}

public function get_undeleted() {
	$this->db->query('SELECT * FROM stock_locations WHERE deleted=0');
	if (($data = $this->db->select()) === false) return false;
	
	$result = array();
	foreach ($data as $row) {
		foreach ($row as $k => $v) {
			if ($k == 'location_id') continue;
			
			$c[$k] = $v;
		}
		$result[$row['location_id']] = $c;
	}
	
	return $result;
}

private function maxid() {
	$this->db->query('SELECT MAX(location_id) FROM stock_locations');
	return $this->db->max();
}
}
?>