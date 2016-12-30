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
	<style>
	input[type="text"] 
	{
		margin-top:4%;
		margin-bottom:4%;
		padding:4%;
	}
	label 
	{
		margin-top:4%;
	}
	textarea 
	{
		margin-bottom:4%;
	}
	</style>
	<script src="js/function.js"></script>
	<script type="text/javascript">
	window.addEventListener("load", function() {
		document.getElementById("sel").addEventListener("change", function() {
			switch ($("#sel option:selected").attr("a")) {
			case "bcryte":
				if ($("#sel option:selected").attr("f") == "verify") {
					$("#val2").removeClass("sr-only");
				} else {
					$("#val2").val('');
					$("#val2").addClass("sr-only");
				}
				if (!$("#textarea").hasClass("sr-only"))
					$("#textarea").addClass("sr-only");
			break;
			case "add_num":
			case "app_config":
			case "role":
			case "filter":
				$("#textarea").removeClass("sr-only");
			break;
			}
		});
	});
	</script>
	<div class="container">
		<div class="row">
			<div class="col-md-2">
				<select id="sel" class="selectpicker">
					<option a="bcryte" f="hash" selected="selected">bcryte hash</option>
					<option a="bcryte" f="verify">bcryte verify</option>
					<option a="add_num">add number</option>
					<option a="app_config">app config</option>
					<option a="role">role</option>
					<option a="filter">filter</option>
				</select>
				<input id="val" class="form-control" type="text" placeholder="value" required="required" />
				<input id="val2" class="form-control sr-only" type="text" placeholder="value" />
				<textarea id="textarea" class="form-control sr-only"></textarea>
				<input class="btn btn-primary btn-block" value="go"  type="submit" onclick="fun()" />
			</div>
			<div id= "content" class="col-md-8">
			</div>
		</div>
	</div>
</body>
</html>
