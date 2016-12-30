<?php
require_once('../libs/ipos_setup.php');
require_once('../libs/pwd.php');

$ipos = new smarty_ipos;
if (!$ipos->db) {
	$ipos->langauge(array('common'));
	$ipos->assign('err', $ipos->lang[$ipos->err]);
	$ipos->display('err.tpl');
	exit();
}

$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : 'view';
switch($act) {
case 'bcryte':
	$ret = '';
	if (!empty($_REQUEST['val']) && isset($_REQUEST['f'])) {
		switch($_REQUEST['f']) {
		case 'hash':
			$ret = pwd_hash($_REQUEST['val']);
		break;
		case 'verify':
			$ret = isset($_REQUEST['val2']) ? pwd_verify($_REQUEST['val'], urldecode($_REQUEST['val2'])) : false;
			$ret = $ret ? 'ok' : 'fail';
		break;
		default:
			$ret = 'not support';
		break;
		}
	} else {
		$ipos->assign('err', 'please check input.');
	}
	
	$ipos->assign('val', isset($_REQUEST['val']) ? $_REQUEST['val'].' =>' : ' =>');
	$ipos->assign('ret', $ret);
	$ipos->display('fun/bcryte.tpl');
break;
case 'add_num':
	if (!empty($_REQUEST['val'])) {
		$arr = myexplode(urldecode($_REQUEST['val']), 3);
		$cnt = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
		$result = '';
		foreach ($arr as $v) {
			$result .= '(\''. $v[0] .'\', \''. $cnt++ .'\'';
			$result .= isset($v[2]) ? ', \''. $v[2] . '\'),' : '),';
			$result .= '</br>';
		}
		
		$ipos->assign('ret', $result);
		$ipos->display('fun/add_num.tpl');
	}
break;
case 'app_config':
	if (!empty($_REQUEST['val'])) {
		$arr = myexplode(urldecode($_REQUEST['val']));
		$result = '';
		foreach ($arr as $v) {
			$result .= '\'' . $v[0] .'\' => ';
			$type = 'FILTER_SANITIZE_SPECIAL_CHARS';
			if ($v[0] == 'email' || strpos($v[1], '@'))
				$type = 'FILTER_VALIDATE_EMAIL';
			else if (is_numeric($v[1]))
				$type = 'FILTER_VALIDATE_INT';
			
			$result .= $type . ',' . '</br>';
		}
		
		$ipos->assign('ret', $result);
		$ipos->display('fun/add_num.tpl');
	}
break;
case 'role':
	if (!empty($_REQUEST['val'])) {
		$arr = json_decode(urldecode($_REQUEST['val']));
		$html = '<ul id="permission_list">';
		$lang = '';
		$zh = '';
		foreach ($arr as $val) {
			$len =  count($val);
			if ($len > 1)
				$html .= '
	<li><input class="module" name="grants[]" type="checkbox" {nocache}{if !empty($role["'. $val[0] .'"])}checked="checked"{/if}{/nocache} value="'. $val[0] .'"><span class="medium">{$lang["role_'. $val[0] .'"]}</span>
		<ul class="row">';
			else if ($len == 1) {
				$lang .= '$lang["role_'. $val[0] .'"]="'. $val[0] .'";</br>';
				$zh .= '$lang["role_'. $val[0] .'"]="'. conver($val[0]) .'";</br>';
				$html .= '
	<li><input class="module" name="grants[]" type="checkbox" {nocache}{if !empty($role["'. $val[0] .'"])}checked="checked"{/if}{/nocache} value="'. $val[0] .'"><span class="medium">{$lang["role_'. $val[0] .'"]}</span></li>';
				continue;
			} else
				continue;
			
			$lang .= '$lang["role_'. $val[0] .'"]="'. $val[0] .'";</br>';
			$zh .= '$lang["role_'. $val[0] .'"]="'. conver($val[0]) .'";</br>';
			for ($i = 1; $i < $len; ++$i) {
				$end = substr($val[$i], strlen($val[0]) + 1);
				$lang .= '$lang["role_'. $val[$i] .'"]="'. $end .'";</br>';
				$zh .= '$lang["role_'. $val[$i] .'"]="'. conver($end) .'";</br>';
				$html .= '
		<li class="col-xs-2"><input name="grants[]" type="checkbox" {nocache}{if !empty($role["'. $val[$i] .'"])}checked="checked"{/if}{/nocache} value="'. $val[$i] .'"><span class="small">{$lang["role_'. $val[$i] .'"]}</span></li>';
			}
			
			$html .= '</ul></li>
';
		}
	
		$html .= '</ul>';
		error_log($html);
		$ipos->assign('ret', $lang .'</br></br>'. $zh);
		$ipos->display('fun/add_num.tpl');
	}
break;
case 'filter':
	if (!empty($_REQUEST['val'])) {
		$arr = myexplode(urldecode($_REQUEST['val']), 2, '`', 1, 1);
		$result = '';
		foreach ($arr as $v) {
			$result .= '\'' . $v[0] .'\' => ';
			if ($v[0] == 'email')
				$type = 'FILTER_VALIDATE_EMAIL';
			else if (stripos($v[1], 'int'))
				$type = 'FILTER_VALIDATE_INT';
			else if (stripos($v[1], 'decimal'))
				$type = 'FILTER_VALIDATE_FLOAT';
			else
				$type = 'FILTER_SANITIZE_SPECIAL_CHARS';
				
			$result .= $type . ',' . '</br>';
		}
		
		$ipos->assign('ret', $result);
		$ipos->display('fun/add_num.tpl');
	}
break;
default:
	$ipos->display('fun/function.tpl');
break;
}

function conver($end) {
	$con = array('insert'=>'添加','delete'=>'删除','update'=>'更新', 'reports'=>'报表', 'customers'=>'客户', 'employees'=>'员工', 'giftcards'=>'礼金券', 'items'=>'产品', 'item_kits'=>'产品套件', 'receivings'=>'进货', 'sales'=>'销售', 'suppliers'=>'供应商', 'config'=>'系统设置', 'stock'=>'仓库', 'grants'=>'权限', 'items_stock'=>'产品仓库', 'sales_stock'=>'出货仓库', 'receivings_stock'=>'进货仓库', 'discounts'=>'折扣', 'taxes'=>'税额', 'inventory'=>'库存', 'categories'=>'类别', 'payments'=>'付款');
	return isset($con[$end]) ? $con[$end] : $end;
}

function myexplode($str, $num=2, $needle='\'', $start=1, $step=2) {
	$split = $step * $num + 1;
	$arr = preg_split('/\r\n|\r|\n/', $str);
	$result = array();
	foreach ($arr as $v) {
		if ($v[0] == '-' && $v[1] == '-') continue;
		
		$c = array();
		$tmp = explode($needle, $v, $split);
		for ($i = $start; $i < count($tmp); $i += $step) {
				$c[] = $tmp[$i];
		}
		$result[] = $c;
	}
	
	return $result;
}
?>