var start_date = "{$smarty.now|date_format:"Y-m-d":"":"date"}";
var end_date = "{$smarty.now|date_format:"Y-m-d":"":"date"}";
$("#daterangepicker").daterangepicker({
	"ranges": {
		"{$lang['datepicker_today']}": [
			{daterangepicker dateformat={$config['dateformat']} time="today"}
		],
		"{$lang['datepicker_today_last_year']}": [
			{daterangepicker dateformat={$config['dateformat']} time="today_last_year"}
		],
		"{$lang['datepicker_yesterday']}": [
			{daterangepicker dateformat={$config['dateformat']} time="yesterday"}
		],
		"{$lang['datepicker_last_7']}": [
			{daterangepicker dateformat={$config['dateformat']} time="last_7"}
		],
		"{$lang['datepicker_last_30']}": [
			{daterangepicker dateformat={$config['dateformat']} time="last_30"}
		],
		"{$lang['datepicker_this_month']}": [
			{daterangepicker dateformat={$config['dateformat']} time="this_month"}
		],
		"{$lang['datepicker_this_month_to_today_last_year']}": [
			{daterangepicker dateformat={$config['dateformat']} time="this_month_to_today_last_year"}
		],
		"{$lang['datepicker_this_month_last_year']}": [
			{daterangepicker dateformat={$config['dateformat']} time="this_month_last_year"}
		],
		"{$lang['datepicker_last_month']}": [
			{daterangepicker dateformat={$config['dateformat']} time="last_month"}
		],
		"{$lang['datepicker_this_year']}": [
			{daterangepicker dateformat={$config['dateformat']} time="this_year"}
		],
		"{$lang['datepicker_last_year']}": [
			{daterangepicker dateformat={$config['dateformat']} time="last_year"}
		],
		"{$lang['datepicker_all_time']}": [
			{daterangepicker dateformat={$config['dateformat']} time="all" start={$config['company_start']}}
		],
	},
	"locale": {
		"format": "{dateformat_momentjs format={$config['dateformat']}}",
		"separator": " - ",
		"applyLabel": "{$lang['datepicker_apply']}",
		"cancelLabel": "{$lang['datepicker_cancel']}",
		"fromLabel": "{$lang['datepicker_from']}",
		"toLabel": "{$lang['datepicker_to']}",
		"customRangeLabel": "{$lang['datepicker_custom']}",
		"daysOfWeek": [
			"{$lang['cal_su']}",
			"{$lang['cal_mo']}",
			"{$lang['cal_tu']}",
			"{$lang['cal_we']}",
			"{$lang['cal_th']}",
			"{$lang['cal_fr']}",
			"{$lang['cal_sa']}",
			"{$lang['cal_su']}"
		],
		"monthNames": [
			"{$lang['cal_january']}",
			"{$lang['cal_february']}",
			"{$lang['cal_march']}",
			"{$lang['cal_april']}",
			"{$lang['cal_mayl']}",
			"{$lang['cal_june']}",
			"{$lang['cal_july']}",
			"{$lang['cal_august']}",
			"{$lang['cal_september']}",
			"{$lang['cal_october']}",
			"{$lang['cal_november']}",
			"{$lang['cal_december']}"
		],
		"firstDay": "{$lang['datepicker_weekstart']}"
	},
	"alwaysShowCalendars": true,
	"startDate": {daterangepicker dateformat={$config['dateformat']} time="start"},
	"endDate": {daterangepicker dateformat={$config['dateformat']} time="end"},
	"minDate": {daterangepicker dateformat={$config['dateformat']} time="min" start={$config['company_start']}},
	"maxDate": {daterangepicker dateformat={$config['dateformat']} time="max"}
}, function(start, end, label) {
	start_date = start.format("YYYY-MM-DD");
	end_date = end.format("YYYY-MM-DD");
});