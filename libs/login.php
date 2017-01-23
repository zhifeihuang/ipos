<?php
require_once('../libs/employee.php');

class login 
{
var $err = null;
var $last_check_time = null;
var $err_cnt = 0;
var $ok = 0;  // 0 nologin   1 login=1day   2 login>1day 
var $session_id = '';
var $tt;
var $day; 
var $daytime; 
var $db = null;
var $filter = array(
	'usrid'		=> array('filter' => FILTER_VALIDATE_INT,
						'options' => array('min_range' => 1)),
	'password'	=> FILTER_SANITIZE_SPECIAL_CHARS
);

public function __construct($db) {
	$this->db = $db;
	$this->tt = $_SERVER['REQUEST_TIME'];
	$this->day = strtotime(date("Y-m-d", $this->tt)); 
	$this->daytime = date("Y-m-d H:i:s", $this->tt);
}

public function filter($data, $lang) {
	if (!isset($data['usrid']) || !isset($data['password'])) {
		$this->err = $lang['id_password_empty'];
		return false;
	}
	
	$vars = ['usrid' => ltrim(trim($data['usrid']), "0"),
			'password' => trim($data['password'])];
		
	if ($vars['usrid'] == '') {
		$this->err = $lang['id_empty'];
		return false;
	}
	if ($vars['password'] == '') {
		$this->err = $lang['password_empty'];
		return false;
	}
	
	return filter_var_array($vars, $this->filter);
}

public function check($var, $ipos, $app_con) {
	$lang = $ipos->lang;
	if (($check = $this->check_cnt($var['usrid'], $app_con['deadline'], $app_con['try_cnt']))
		&& ($ret = employee::login($this->db, $var['usrid'], $var['password']))) {
		if ($this->ok != 0) { //  relogin, we should destory session. 
			$this->logout($ipos, $var['usrid'], $this->session_id);
			$this->err = $lang['relogin'];
			return false;
		}
		
		if ($ipos->session->usrdata('person_id')) {
			$this->logout($ipos, $ipos->session->usrdata('person_id'));
			$this->err = $lang['relogin'];
			return false;
		}
		
		return $ret;
	} else {
		if (!$check) {
			$check_time = $app_con['deadline'] - ($this->tt - $this->last_check_time);
			$this->err = $lang['login_check'].' '.$this->format($check_time, $lang).$lang['check_time'];
		} else {
			$cnt = $app_con['try_cnt'] - $this->err_cnt;
			$msg = $lang['auth_err'];
			if ($cnt <= $app_con['show_cnt']) {
				$msg .= ' ' . $cnt . $lang['cnt_err'];
			}
			$this->err = $msg;
			$this->error($var['usrid']);
		}
		return false;
	}
}

public function logout($ipos, $id, $session_id='') {
	$ipos->session->destory($session_id);
	$this->db->query('UPDATE login_tmp SET ok=0,session_id='. "''" .' WHERE person_id='. $id);
	$this->db->execute();
}

public function success($usrid, $session_id) {
	$ip = isset($_SERVER['REMOTE_ADDR']) ? inet_pton($_SERVER['REMOTE_ADDR']) : 0;
	$proxy = empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? '' : filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_SANITIZE_SPECIAL_CHARS);
	$os	= empty($_SERVER['REMOTE_HOST']) ? '' : filter_var($_SERVER['REMOTE_HOST'], FILTER_SANITIZE_SPECIAL_CHARS);
	
	$data = array();
	$data[] = $usrid;
	$data[] = $this->daytime;
	$data[] = $this->err_cnt;
	$data[] = $ip;
	$data[] = $session_id;
	$data[] = $proxy;
	$data[] = $os;
	
	$this->db->query('INSERT INTO login_tmp (person_id, dt, err_cnt, ok, ip, session_id, proxy, os) 
				VALUES (?,?,?,1,?,?,?,?) 
				ON DUPLICATE KEY UPDATE 
				dt=VALUES(dt), err_cnt=VALUES(err_cnt), ok=1, ip=VALUES(ip), session_id=VALUES(session_id), proxy=VALUES(proxy), os=VALUES(os)');
	if ($this->db->insert(array($data)) === false) return false;
	
	if ($this->ok != 1) {
		unset($data[2], $data[4]);
		$data = array_values($data);
		$this->db->query('INSERT INTO login (person_id, dt, ip, proxy, os) VALUES (?,?,?,?,?)');
		if ($this->db->insert(array($data)) === false) return false;
	}
}

private function error($usrid) {
	++$this->err_cnt;
	$ip = isset($_SERVER['REMOTE_ADDR']) ? inet_pton($_SERVER['REMOTE_ADDR']) : 0;
	$proxy = empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? '' : filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_SANITIZE_SPECIAL_CHARS);
	$os	= empty($_SERVER['REMOTE_HOST']) ? '' : filter_var($_SERVER['REMOTE_HOST'], FILTER_SANITIZE_SPECIAL_CHARS);
	
	$data = array();
	$data[] = $usrid;
	$data[] = $this->daytime;
	$data[] = $this->err_cnt;
	$data[] = $this->ok;
	$data[] = $ip;
	$data[] = $proxy;
	$data[] = $os;
	
	$this->db->query('INSERT INTO login_tmp (person_id, dt, err_cnt, ok, ip, proxy, os) 
				VALUES (?,?,?,?,?,?,?) 
				ON DUPLICATE KEY UPDATE 
				dt=VALUES(dt), err_cnt=VALUES(err_cnt), ok=VALUES(ok), ip=VALUES(ip), proxy=VALUES(proxy), os=VALUES(os)');
	if ($this->db->insert(array($data)) === false) return false;
}

private function check_cnt($usrid, $deadline, $try_cnt) {
	$this->db->query('SELECT dt, err_cnt, ok, session_id FROM login_tmp WHERE person_id=? ORDER BY dt DESC LIMIT 0,1');
	if ($result = $this->db->select(array(array($usrid)))) {
		$this->last_check_time = strtotime($result[0]['dt']);
		$this->session_id = $result[0]['session_id'];
		if ($this->last_check_time < $this->day) {
			$this->err_cnt = 0;
			$this->ok = $result[0]['ok'] == 1 ? 2 : 0;
			return true;
		}
		
		$this->err_cnt = $result[0]['err_cnt'];
		$this->ok = $result[0]['ok'];
		return ($this->err_cnt >= $try_cnt && $this->tt < $this->last_check_time + $deadline) ? false : true;
	} else {
		return true;
	}
}

private function format($time, $lang) {
	$msg = '';
	if ($time >= 86400) {
		$msg = floor($time/86400).$lang['d'];
		$time %= 86400;
	}
	if ($time >= 3600) {
		$msg .= floor($time/3600).$lang['h'];
		$time %= 3600;
	}
	if ($time >= 60) {
		$msg .= floor($time/60).$lang['m'];
		$time %= 60;
	}
	$msg .= ' '.$time.$lang['s'];
	return $msg;
}
}
?>