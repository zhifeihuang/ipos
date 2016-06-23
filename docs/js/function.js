function fun() {
	var param = "act=";
	var sel = $("#sel option:selected");
	switch (sel.attr("a")) {
	case "bcryte":
		if (!$("#val").val()) {
			$("#content").empty().append("<label class=\"alert-danger\">value is empty</label>");
			return;
		}
		param += "bcryte" + "&f=" + sel.attr("f") + "&val=" + $("#val").val();
		if (sel.attr("f") == "verify") {
			param += "&val2=" + encodeURIComponent($("#val2").val());
		}
	break;
	case "add_num":
		param += "add_num" + "&val=" + encodeURIComponent($("#textarea").val());
		if ($("#val").val()) {
			param += "&start=" + $("#val").val();
		}
	break;
	case "app_config":
		param += "app_config" + "&val=" + encodeURIComponent($("#textarea").val());
	break;
	case "role":
		param += "role" + "&val=" + encodeURIComponent($("#textarea").val());
	break;
	case "filter":
		param += "filter" + "&val=" + encodeURIComponent($("#textarea").val());
	break;
	}
	post("function.php", param, funback);
}

function funback() {
	if (xhttp.readyState == 4) {
		$("#content").empty().append(xhttp.responseText);
		if (!$("#return")) $("#content").empty();
	}
}