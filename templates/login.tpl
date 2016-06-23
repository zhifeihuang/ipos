<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico"/>
	<link rel="stylesheet" type="text/css" href="dist/bootstrap.min.css?rel=9ed20b1ee8"/>
	<link href="css/login.css" rel="stylesheet" type="text/css">
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/ipos.js"></script>
</head>
<body>
	<div id="logo" align="center"><img src="images/logo.gif"></img></div>

	<div id="login">
		<form action="index.php?act=submit" method="post" accept-charset="UTF-8">
			<div id="container">
				<p align="center" class="alert-danger">
				{nocache}
				{if isset($err)}{$err}{/if}
				{/nocache}
				</p>
				<div id="login_form">
					<input class="form-control autofocus" type="text" placeholder="{$lang["usrid"]}" required="required" name="usrid" maxlength="10" {nocache}{if (isset($usrid))}value={$usrid}{/if}{/nocache}>
					<input class="form-control" type="password" placeholder="{$lang["password"]}" required="required" name="password" size="20">
					<input class="btn btn-primary btn-block" type="submit" value="login" />
				</div>
			</div>
		</form>
	</div>
	<script>
	$(document).ready(function() {
		  $('input.autofocus').focus();
	});
	</script>
</body>
</html>