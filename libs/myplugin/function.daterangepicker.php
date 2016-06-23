<?php
/**
 * Smarty {daterangepicker} plugin
 * Type:     function
 * Name:     daterangepicker
 * Purpose: 
 *
 */
function smarty_function_daterangepicker($params) {
	$dateformat = $params['dateformat'];
	$time = $params['time'];
	$result = '';
	switch($time) {
	case 'today':
		$result ='"'. date($dateformat, mktime(0,0,0,date("m"),date("d"),date("Y"))) .'","'. date($dateformat, mktime(0,0,0,date("m"),date("d")+1,date("Y"))) .'"';
	break;
	case 'today_last_year':
		$result ='"'. date($dateformat, mktime(0,0,0,date("m"),date("d"),date("Y")-1)) .'","'. date($dateformat, mktime(0,0,0,date("m"),date("d")+1,date("Y")-1)-1) .'"';
	break;
	case 'yesterday':
		$result ='"'. date($dateformat, mktime(0,0,0,date("m"),date("d")-1,date("Y"))) .'","'. date($dateformat, mktime(0,0,0,date("m"),date("d"),date("Y"))-1) .'"';
	break;
	case 'last_7':
		$result ='"'. date($dateformat, mktime(0,0,0,date("m"),date("d")-6,date("Y"))) .'","'. date($dateformat, mktime(0,0,0,date("m"),date("d")+1,date("Y"))-1) .'"';
	break;
	case 'last_30':
		$result ='"'. date($dateformat, mktime(0,0,0,date("m"),date("d")-29,date("Y"))) .'","'. date($dateformat, mktime(0,0,0,date("m"),date("d")+1,date("Y"))-1) .'"';
	break;
	case 'this_month':
		$result ='"'. date($dateformat, mktime(0,0,0,date("m"),1,date("Y"))) .'","'. date($dateformat, mktime(0,0,0,date("m")+1,1,date("Y"))-1) .'"';
	break;
	case 'this_month_to_today_last_year':
		$result ='"'. date($dateformat, mktime(0,0,0,date("m"),1,date("Y")-1)) .'","'. date($dateformat, mktime(0,0,0,date("m"),date("d")+1,date("Y")-1)-1) .'"';
	break;
	case 'this_month_last_year':
		$result ='"'. date($dateformat, mktime(0,0,0,date("m"),1,date("Y")-1)) .'","'. date($dateformat, mktime(0,0,0,date("m")+1,1,date("Y")-1)-1) .'"';
	break;
	case 'last_month':
		$result ='"'. date($dateformat, mktime(0,0,0,date("m")-1,1,date("Y"))) .'","'. date($dateformat, mktime(0,0,0,date("m"),1,date("Y"))-1) .'"';
	break;
	case 'this_year':
		$result ='"'. date($dateformat, mktime(0,0,0,1,1,date("Y"))) .'","'. date($dateformat, mktime(0,0,0,date("m"),1,date("Y")+1)-1) .'"';
	break;
	case 'last_year':
		$result ='"'. date($dateformat, mktime(0,0,0,1,1,date("Y")-1)) .'","'. date($dateformat, mktime(0,0,0,1,1,date("Y"))-1) .'"';
	break;
	case 'all':
		$start = $params['start'];
		$result ='"'. date($dateformat, mktime(substr($start, 8, 2), substr($start, 10, 2), substr($start, 12, 2), substr($start, 4, 2), substr($start, 6, 2), substr($start, 0, 4))) .'","'. date($dateformat, mktime(0,0,0,date("m"),date("d")+1,date("Y"))-1) .'"';
	break;
	case 'start':
	case 'end':
	case 'max':
		$result ='"'.  date($dateformat, mktime(0,0,0,date("m"),date("d")+1,date("Y"))-1) .'"';
	break;
	case 'min':
		$start = $params['start'];
		$result = '"'. date($dateformat, mktime(substr($start, 8, 2), substr($start, 10, 2), substr($start, 12, 2), substr($start, 4, 2), substr($start, 6, 2), substr($start, 0, 4))) .'"';
	break;
	}
	
	return $result;
}
?>