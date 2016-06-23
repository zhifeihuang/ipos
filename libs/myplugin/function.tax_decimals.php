<?php
/**
 * Smarty {tax_decimals} plugin
 * Type:     function
 * Name:     tax_decimals
 * Purpose: format tax
 *
 */
function smarty_function_tax_decimals($params) {
	if (empty($params['number'])) return;
	
	$number = $params['number'];
	$decimal_point = empty($params['decimal_point']) ? '.' : $params['decimal_point'];
	$decimals = empty($params['decimals']) ? 0 : $params['decimals'];
	
	return number_format($number, $decimals, $decimal_point, '');
}
?>