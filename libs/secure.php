<?php
abstract class secure
{
	public $err;
	
	protected $db;
	protected $func_permission;
	protected $permission;
	protected $grant;
	protected $subgrant;
	
	function __construct($db, $grant, $permission) {
		$this->db = $db;
		$this->err = null;
		$this->grant = $grant;
		$this->permission = $permission;
		foreach ($this->func_permission as $v) {
			if ($this->check_grant($this->grant, $v)) $this->subgrant[$v] = true;
		}
	}
	
	public final function has_grant($func) {
		if (!isset($this->func_permission[$func])) return false;
		
		$ret = false;
		$permission_id = $this->func_permission[$func];
		if (isset($this->permission[$permission_id])) {
			$num = $this->permission[$permission_id]['num'] - 1;
			$bit = 1 << ($num & 0x7);
			if ($this->grant[$num >> 3] & $bit) {
				$ret = true;
			}
		}
		
		return $ret;
	}
	
	public final function check_grant($grant, $permission_id) {
		$ret = false;
		if (isset($this->permission[$permission_id])) {
			$num = $this->permission[$permission_id]['num'] - 1;
			$bit = 1 << ($num & 0x7);
			if ($grant[$num >> 3] & $bit) {
				$ret = true;
			}
		}
		
		return $ret;
	}
}
?>