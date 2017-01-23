function quit() {
	$.post('home.php?act=logout');
	
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
		window.location.href = 'about:blank ';
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

function browser_print(data) {
	//window.open(response.print, '_blank', 'scrollbars=no,menubar=no,toolbar=no,status=no,titlebar=no');
	if (navigator.userAgent.indexOf("Firefox") > 0) {
		var print_win = window.open();
	} else if (navigator.userAgent.indexOf("Chrome") > 0) {
		var print_win = window.open('', '_blank');
	} else {
		var print_win = window.open();
	}
	
	print_win.document.write(data);
	print_win.document.close();
	print_win.focus();
	print_win.print();
	print_win.close();
}