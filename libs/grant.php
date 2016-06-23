<?php
class grant {
private $db;
private $permission;
private $default_permission;

public function __construct($db, $permission, $default_permission) {
	$this->db = $db;
	$this->permission = $permission;
	$this->default_permission = $default_permission;
}

public function get($id) {
	$this->db->query('SELECT hex(g.permission) FROM grants AS g JOIN employees AS e on e.role=g.role WHERE e.person_id=? AND e.deleted=0');
	if (($result = $this->db->select(array(array($id)))) === false)
		return $this->default_permission;
	
	return $this->p2dec($result[0]['hex(g.permission)']);
}

public function get_permission($role) {
	$this->db->query('SELECT hex(permission) FROM grants WHERE role=?');
	if (($sel = $this->db->select(array(array($role)))) === false) return false;
	
	return $this->p2dec($sel[0]['hex(permission)']);
}

public function get_all($lang) {
	$this->db->query('SELECT role, hex(permission) FROM grants');
	if (($sel = $this->db->select()) === false)
		return array();
	
	$opt = $this->p2opt();
	$result = array();
	foreach ($sel as $v) {
		$pm = $this->p2str($v['hex(permission)'], $opt, $lang);
		$result[] = array('role'=>$v['role'], 'permission'=>$pm);
	}
	
	return $result;
}

public function get_role($role) {
	$this->db->query('SELECT hex(permission) FROM grants WHERE role=?');
	if (($sel = $this->db->select(array(array($role)))) === false) return false;
	
	$row = $sel[0];
	$result = array();
	$dec = $this->p2dec($row['hex(permission)']);
	$result = array();
	foreach ($this->permission as $k => $v) {
		$num = $v['num'] - 1;
		$bit = 1 << ($num & 0x7);
		if ($dec[$num >> 3] & $bit) $result[$k] = true;
	}
	
	return $result;
}

public function save($role, $val, $lang) {
	$role = filter_var($role, FILTER_SANITIZE_SPECIAL_CHARS);
	$pval = $this->set_grant_bit($val);
	$this->db->query('INSERT INTO grants VALUES (?,unhex(?))');
	if ($this->db->insert(array(array($role, $pval))) === false) return false;
	
	$opt = $this->p2opt();
	return array('role'=>$role, 'permission'=>$this->p2str($pval, $opt, $lang));
}

public function update($role, $val, $lang) {
	$role = filter_var($role, FILTER_SANITIZE_SPECIAL_CHARS);
	$pval = $this->set_grant_bit($val);
	$this->db->query('UPDATE grants SET permission=unhex(?) WHERE role=?');
	if ($this->db->update(array(array($pval, $role))) === false) return false;
	
	$opt = $this->p2opt();
	return array('role'=>$role, 'permission'=>$this->p2str($pval, $opt, $lang));
}

public function delete($ids) {
	$del = array();
	if (is_array($ids)) {
		foreach ($ids as $v)
			$del[] = array(filter_var($v, FILTER_SANITIZE_SPECIAL_CHARS));
	} else {
		$del[] = array(filter_var($ids, FILTER_SANITIZE_SPECIAL_CHARS));
	}

	$this->db->beginTransaction();
	$this->db->query('DELETE FROM grants WHERE role=?');
	if ($this->db->delete($del)) {
		$this->db->commit();
		return true;
	} else {
		$this->db->rollBack();
		return false;
	}
}

/*
 * val as (customers, customers_insert, suppliers ...), but report must send allways 0;
*/
private function set_grant_bit($val) {
	$pval = $this->default_permission;
	foreach ($val as $v) {
		if (isset($this->permission[$v])) {
			$num = $this->permission[$v]['num'] - 1;
			$bit = 1 << ($num & 0x7);
			$pval[$num >> 3] |= $bit;
		}
	}
	
	$result = '';
	$cnt = 0;
	while ($cnt < count($pval)) {
		$result .= substr("0". dechex($pval[$cnt++]), -2);
	}
	
	return $result;
}

private function p2dec($permission) {
	$ret = array();
	$cnt = 0;
	$len = strlen($permission);
	while ($cnt < $len) {
		$ret[] = hexdec(substr($permission, $cnt, 2));
		$cnt += 2;
	}
	
	return $ret;
}

private function p2str($permission, $opt, $lang) {
	$dec = $this->p2dec($permission);
	$str = '';
	foreach ($opt as $val) {
		foreach ($val as $v) {
			$num = $this->permission[$v]['num'] - 1;
			$bit = 1 << ($num & 0x7);
			if ($dec[$num >> 3] & $bit) {
				$str .= isset($lang['role_'. $val[0]]) ? $lang['role_'. $val[0]] .'    ' : '';
				break;
			}
		}
	}
	
	return $str;
}

/*
 * return like ((customers, customers_insert, customers_delete, customers_update) ...)
*/
private function p2opt() {
	$result = array();
	$len = 1;
	$key_id = '';
	$c = null;
	foreach ($this->permission as $k => $v) {
		if (strncmp($key_id, $k, $len)) {
			if ($c !== null)
				$result[] = $c;
				
			$c = array($k);
			$key_id = $k;
			$len = strlen($k);
			continue;
		}
		
		$c[] = $k;
	}
	
	if ($c !== null) $result[] = $c;
	
	return $result;
}

}
?>