<?php
/**
 * Smarty {currency} plugin
 * Type:     function
 * Name:     currency
 * Purpose: format money
 *
 */
function smarty_function_currency($params) {
	$number = $params['number'];
	$thousands_separator = empty($params['thousands_separator']) ? '' : $params['thousands_separator'];
	$decimal_point = empty($params['decimal_point']) ? '.' : $params['decimal_point'];
	$decimals = empty($params['decimals']) ? 0 : $params['decimals'];
	
	return number_format($number, $decimals, $decimal_point, $thousands_separator);
}
?>