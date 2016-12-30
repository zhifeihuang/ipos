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
	
	if (navigator.userAgent.indexOf("MSIE") > 0) {
            if (navigator.userAgent.indexOf("MSIE 6.0") > 0) {
                window.opener = null; window.close();
            } else {
                window.open('', '_top'); window.top.close();
            }
	} else if (navigator.userAgent.indexOf("Firefox") > 0) {
		window.location.href = 'about:blank ';
		//window.history.go(-2);  
	} else {
		window.opener = null;
		window.open('', '_self', '');
		window.close();
	}
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
