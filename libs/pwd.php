<?php
/*function pwd_hash($pwd, $str) {
	return password_hash($pwd.$str[0].substr($str, -1), PASSWORD_BCRYPT);
}

function pwd_verify($pwd, $str, $dbpwd) {
	$p = pwd_hash($pwd, $str);
	return password_verify($p, $dbpwd);
}*/
function pwd_hash($pwd) {
	return password_hash($pwd, PASSWORD_BCRYPT);
}

function pwd_verify($pwd, $hash) {
	return password_verify($pwd, $hash);
}
?>