<div class="form-group" id='table_action_header'>
	<ul>
		<li class="pull-right"><input class="form-control input-sm ui-autocomplete-input" placeholder="{$lang['giftcards_number_required']}" id="find" type="text" size='32' value=""></li>
	</ul>
</div>
<div id='table_holder'>
	<table class="tablesorter table table-striped table-hover" id='find_table'>
		<thead>
			<tr>
				<th width="15%">{$lang['common_last_name']}</th>
				<th width="15%">{$lang['common_first_name']}</th>
				<th width="15%">{$lang['giftcards_giftcard_number']}</th>
				<th width="20%">{$lang['giftcards_card_value']}({$config['currency_symbol']})</th>
				<th width="8%">{$lang['giftcards_status']}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('#find').autocomplete({
		source: 'home.php?act=giftcards&f=suggest_search',
		autoFocus: false,
		delay:500,
		appendTo: ".modal-content",
		select: function(e, ui) {
			$.post('home.php?act=giftcards&f=search', { value: ui.item.value, label:ui.item.label }, function(response) {
				$("#find_table > tbody").empty().append(response.rows);
			},
			'json');
			
			$("#find").val('');
			return false;
		}
	});
});

function update(e) {
	var tr = e.closest('tr');
	$.post('home.php?act=giftcards', { f: 'delete', status: $(e).attr('data-func'), id: $(tr).attr('id') }, function(response) {
		if (response.success) {
			var row = '#' + response.id;
			if ($(row).length == 0)
				return;
				
			$(row).replaceWith(response.row);
		} else {
			set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);
		}
	}, 'json');
}
</script>