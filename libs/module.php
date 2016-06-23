<?php
class module
{
private $db;
public $default_permission;
public $module;
public $permission;

public function __construct($db) {
	$this->db = $db;
	$this->default_permission = array(0,0,0,0,0,0,0,0);
	$this->module = $this->get_all_info('modules', 'module_id', ' ORDER BY sort asc');
	$this->permission = $this->get_all_info('permissions', 'permission_id', ' ORDER BY num asc');
}

public function get_module_name($module_id) {
	return isset($this->module[$module_id]) ? $this->module[$module_id]['name_lang_key'] : 'err_unknown';
}
	
public function get_module_desc($module_id) {
	return isset($this->module[$module_id]) ? $this->module[$module_id]['desc_lang_key'] : 'err_unknown';
}

public function get_all_subpermissions() {
	return false;
}
	
public function get_allowed_modules($grant) {
	$result = array();
	foreach ($this->module as $k => $v) {
		$num = $this->permission[$k]['num'] - 1;
		$ch = $num >> 3;
		$bit = 1 << ($num & 0x7);
		if ($grant[$ch] & $bit) {
			$result[] = $k;
		}
	}
	
	return $result;
}

private function get_all_info($table, $key_id, $order = null) {
	$this->db->query('SELECT * FROM ' . $table .  ' ' . $order);
	$sel = $this->db->select();
	
	$result = array();
	foreach ($sel as $row) {
		foreach($row as $k => $v) {
			if ($key_id == $k) continue;
				
			$c[$k] = $v;
		}
		$result[$row[$key_id]] = $c;
	}
	
	return $result;
}
}
?>