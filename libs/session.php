<?php
class session {
var $name;

public function __construct($name) {
	$this->name = $name;
}
	
public function usrdata($key) {
	// $this->check();
	return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
}

public function param($params) {
	// $this->check();
	foreach ($params as $k=>$v) {
		$_SESSION[$k] = $v;
	}
}

public function del($params) {
	// $this->check();
	foreach ($params as $v) {
		unset($_SESSION[$v]);
	}
}

public function close() {
	session_write_close();
}

public function destory() {
	// $this->check();
	$_SESSION = array();
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie($this->name, '', time() - 42000,
			$params['path'], $params['domain'],
			$params['secure'], $params['httponly']);
	}
	session_destroy();
	session_write_close();
}

public function check() {
	if (isset($_SESSION) || $this->name != session_name()) {
		session_write_close();
		session_name($this->name);
		@session_start();
	}
}
}
?>