<?php
require_once('../libs/ipos_setup.php');
require('../libs/login.php');

$ipos = new smarty_ipos;
if (!$ipos->db) {
	$ipos->language(array('common'));
	$ipos->err_page($ipos->err);
	return;
}

$ipos->language(array('login'));
$app_con = include '../config/app_con.php';
if (!is_array($app_con)) {
	error_log('read app err.');
}

$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : 'view';
switch($act) {
case 'submit':
	$login = new login($ipos->db);
	$var = $login->filter($_POST, $ipos->lang);
	if ($var && ($ret = $login->check($var, $ipos->lang, $app_con))) {
		if ($ipos->session->usrdata('person_id')) {
			$ipos->session->destory();
		}
		$ipos->session->param($ret);
		header('Location: /home.php');
		exit();
	} else {
		$ipos->assign('err', $login->err);
		if (isset($var['usrid']) && $var['usrid'])
			$ipos->assign('usrid', $var['usrid']);
		
		$ipos->display('login.tpl');
	}
break;
case 'view':
default:
	$ipos->display('login.tpl');
break;
}
?>