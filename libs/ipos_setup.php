<?php
// hack version example that works on both *nix and windows
// Smarty is assumend to be in 'includes/' dir under current script
define('SMARTY_DIR', 'C:/Users/zhifei/server/smarty/libs/');
define('IPOS_DIR', 'C:/Users/zhifei/server/www/ospos/');

require_once(SMARTY_DIR . 'Smarty.class.php');
require_once('../libs/help/lang.php');
require_once('../libs/session.php');
require_once('../libs/db/database.php');

@ini_set('error_log', IPOS_DIR . 'log');
@session_save_path('C:/Users/zhifei/server/www/ospos/session/');

class smarty_ipos extends Smarty {
public $err = null;
public $db = null;
public $lang = null;
public $session = null;

public function __construct() {
	parent::__construct();
	$this->setTemplateDir(IPOS_DIR . 'templates');
	$this->setCompileDir(IPOS_DIR . 'templates_c');
	$this->setConfigDir(IPOS_DIR . 'configs');
	$this->setCacheDir(IPOS_DIR . 'cache');
	$this->addPluginsDir(IPOS_DIR .'libs/myplugin/');
	
	$this->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
	$this->setCacheLifetime(-1);
	$this->assign('app_name', 'ipos');
	
	$this->session = new session('ipos');
	$this->session->check();
	
	$this->init_db();
}

public function language($textpart = array()) {
	if (!($lag = $this->session->usrdata('lang'))) {
		$app = require '../config/app_con.php';
		$lag = getDefaultLanguage();
		$lag = isset($app['lang'][$lag]) ? $lag : 'en';
		$this->session->param(array('lang'=>$lag));
	}

	foreach ($textpart as $text) {
		include_once '../lang/' . $lag . '/' . $text . '_lang.php';
	}

	$this->lang = $this->lang == null ? $lang : $this->lang + $lang;
	$this->assign('lang', $this->lang, false);
}

public function err_page($err) {
	$this->assign('err', isset($this->lang[$err]) ? $this->lang[$err] : 'Sorry, very bad!');
	$this->display('err.tpl');
	exit();
}

public function __destruct() {
}

private function init_db() {
	if (!($config = include('../config/database.php'))) {
		$this->err = 'con_err';
		return;
	}
	
	$dns = '';
	$type = $config['default'];
	$vars = $config[$type];
	$dsn = $vars['driver'].':dbname='.$config['database'].';host='.$config['host'].';port='.$vars['port'];
	$options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_PERSISTENT => false);
	if (!empty($vars['charset'])) {
		$dsn .= ";charset=".$vars['charset'];
	}
	
	try {
		$db = new PDO($dsn, $config['usrname'], $config['password'], $options);
	} catch(PDOException $e) {
		$this->err = 'db_err';
		error_log($e->getMessage());
		return false;
	}
	
	$this->db = new database($db);
}
}
?>