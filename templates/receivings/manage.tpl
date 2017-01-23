{extends file='header.tpl'}
{block name="container"}
<div class="row">
	<div class="col-xs-2">
		<ul class="nav nav-tabs nav-stacked" id='recvs'>
{nocache}
{if !empty($subgrant["receivings_insert"])}
			<li><a data-toggle="tab" onclick="get('order'); return false;">{$lang['recvs_order']}</a></li>
{/if}
{if !empty($subgrant["receivings_update"])}
			<li><a data-toggle="tab" onclick="get('receive'); return false;">{$lang['recvs_receive']}</a></li>
{/if}
{if !empty($subgrant["receivings_delete"])}
			<li><a data-toggle="tab" onclick="get('return'); return false;">{$lang['recvs_return']}</a></li>
{/if}
{/nocache}
		</ul>
	</div>
	<div class="tab-content col-xs-10" id='recv_contain'>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('#recvs :first-child>a').click();
});

function get(data) {
	$.post('home.php?act=receivings', { get:data }, function(response) {
		if (response.success) {
			$('#recv_contain').empty().append(response.data);
		} else {
			set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);
		}
	}, 'json');
}

function calc_ret_total() {
	var total = 0;
	$("#ret_items > tbody > tr").each(function() {
		var t9 = $("td:eq(9)", $(this));
		total += parseFloat(t9.attr("value"));
	});
	
	$("#ret_total").val(total);
}

function delete_ret_row(link) {
	delete_tr_row(link);
	calc_ret_total();
	return false;
}

function delete_tr_row(link) {
	var tr = $(link).closest("tr");
	$("input", tr).rules("removes");
	tr.remove();
	return false;
}
</script>
{/block}