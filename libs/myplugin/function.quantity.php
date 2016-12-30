<?php
/**
 * Smarty {quantity} plugin
 * Type:     function
 * Name:     quantity
 * Purpose: format quantity
 *
 */
function smarty_function_quantity($params) {
	$number = $params['number'];
	$thousands_separator = empty($params['thousands_separator']) ? '' : $params['thousands_separator'];
	
	return number_format($number, 0, '', $thousands_separator);
}
?>