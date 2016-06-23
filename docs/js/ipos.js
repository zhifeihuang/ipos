function createXMLHttpRequest() 
{
	xml = null;
	if (window.XMLHttpRequest) {
		xml = new XMLHttpRequest();
		if (xml.overrideMimeType)
			xml.overrideMimeType('text/xml');
	} else if (window.ActiveXObject) {
		try {
			xml = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				xml = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {
			}
		}
	}
	return xml;
}

function post(url, param, callback) 
{
	xhttp = createXMLHttpRequest();
	xhttp.open("post", url, true);
	xhttp.onreadystatechange = callback;
	xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;");  
	xhttp.send(param);
}

function quit() {
	post("home.php", "act=logout", null);
	window.open('', '_parent', '');
	window.opener = null;
	window.close();
}

function ipos_set_feedback(text, classname, keep_displayed)
{
	if(text)
	{
		$('#feedback_bar').removeClass().addClass(classname).html(text).css('opacity','1');

		if(!keep_displayed)
		{
			$('#feedback_bar').fadeTo(5000, 1).fadeTo("fast",0);
		}
	}
	else
	{
		$('#feedback_bar').css('opacity','0');
	}
}
