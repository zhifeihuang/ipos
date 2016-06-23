<?php
class item_tax {
const needle = ':';
private $db;

public function __construct($db) {
	$this->db = $db;
}

public function save($data) {
	$tmp = array();
	foreach ($data as $k => $v) {
		$tmp[] = array($k);
	}
	
	$key_id = 'name';
	$tax = array();
	if ($tt = $this->get($tmp)) {
		foreach ($tt as $row) {
			foreach ($row as $k => $v) {
				if ($key_id == $k) {
					$key_val = $v;
					continue;
				}
			
				$c[$k] = $v;
			}

			$tax[$key_val] = $c;
		}
	}
	
	$update = array();
	$insert = array();
	foreach ($data as $k => $v) {
		if (isset($tax[$k])) {
			if ($v == $tax[$k]['percent']) continue;
			$update[] = array($v, $k);
		}
		else {
			$insert[] = array($k, $v);
		}
	}
	
	if ((empty($update) || $this->update($update)) && (empty($insert) || $this->insert($insert)))
		return true;
	else
		return false;
}

private function insert($data) {
	$this->db->query('INSERT INTO items_taxes (name,percent) VALUES(?,?)');
	return $this->db->insert($data);
}

private function update($data) {
	$this->db->query('UPDATE items_taxes SET percent=? WHERE name=?');
	return $this->db->update($data);
}

public function get($names) {
	$this->db->query('SELECT * FROM items_taxes WHERE name=?');
	return $this->db->select($names);
}

public function get_all() {
	$this->db->query("SELECT * FROM items_taxes");
	if ($sel = $this->db->select()) {
		$key_id = 'name';
		$result = array();
		foreach ($sel as $row) {
			foreach ($row as $k => $v) {
				if ($key_id == $k) {
					$key_val = $v;
					continue;
				}
			
				$c[$k] = $v;
			}
		
			$result[$key_val] = $c;
		}
		
		return $result;
	} else {
		return false;
	}
}
}
?>