<?php
/**
 * Smarty {format_phone_number} plugin
 * Type:     function
 * Name:     format_phone_number
 * Purpose:  format phone number and display results
 *
 */
function smarty_function_format_phone_number($params)
{
	if (empty($params['phone'])) return;
	
	$needle = empty($params['needle']) ? ' ' : $params['needle'];
	$data = empty($params['maxlen']) ? $params['phone'] : substr($params['phone'], 0, $params['maxlen']); 

	$result = '';
	$len = strlen($data);
	if ($len < 6)
		$result =  $data;
	elseif ($len < 9) {
		$len >>= 1; 
		$result =  substr($data, 0, $len) . $needle . substr($data, $len);
	}
	elseif ($data[0] == '0') {
		$len = ($len - 4) >> 1;
		$result =  substr($data, 0, 4) . $needle . substr($data, 4, $len) . $needle . substr($data, 4 + $len);
	} else {
		$len = ($len - 3) >> 1;
		$result =  substr($data, 0, 3) . $needle . substr($data, 3, $len) . $needle . substr($data, 3 + $len);
	}
	
	return $result;
}
?>