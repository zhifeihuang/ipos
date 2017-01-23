{extends file='header.tpl'}
{block name='container'}
<div class="row">
	<div class="col-xs-2">
		<ul class="nav nav-tabs nav-stacked">
{nocache}
{if !empty($subgrant["giftcards"])}
			<li class='active'><a data-toggle="tab" onclick="get('find'); return false;">{$lang['giftcards_find']}</a></li>
{/if}
{if !empty($subgrant["giftcards_update"])}
			<li><a data-toggle="tab" onclick="get('charge'); return false;">{$lang['giftcards_charge']}</a></li>
{/if}
{if !empty($subgrant["giftcards_insert"])}
			<li><a data-toggle="tab" onclick="get('create'); return false;">{$lang['giftcards_create']}</a></li>
{/if}
{/nocache}
		</ul>
	</div>
	<div class="tab-content col-xs-10" id='giftcard_contain'>
	{include file='giftcard/find.tpl'}
	</div>
</div>
<script type="text/javascript">
function get(data) {
	$.post('home.php?act=giftcards', { get:data }, function(response) {
		if (response.success) {
			$('#giftcard_contain').empty().append(response.data);
		} else {
			set_feedback(response.msg, 'alert alert-dismissible alert-danger', false);
		}
	}, 'json');
}

function delete_row(link) {
	var tr = $(link).closest('tr');
	$('input.form-control', tr).rules('removes');
	tr.remove();
	return false;
}
</script>
{/block}