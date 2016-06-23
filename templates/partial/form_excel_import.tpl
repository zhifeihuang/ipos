<ul class="error_message_box" id="error_message_box"></ul>

<form class="form-horizontal" id="item_form" action="home.php?act={nocache}{$controller_name}{/nocache}&f=do_excel_import" enctype="multipart/form-data" method="post" accept-charset="utf-8" novalidate="novalidate">
	<fieldset id="item_basic_info">
		<div class="form-group form-group-sm">
			<div class="col-xs-12">
				<a href="home.php?act={nocache}{$controller_name}{/nocache}&f=excel">{$lang['common_download_import_template']}</a>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<div class="col-xs-12">
				<div class="fileinput fileinput-new input-group" data-provides="fileinput">
					<div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i><span class="fileinput-filename"></span></div>
					<span class="input-group-addon input-sm btn btn-default btn-file"><span class="fileinput-new">{$lang['common_import_select_file']}</span><span class="fileinput-exists">{$lang['common_import_change_file']}</span><input type="hidden"><input name="file_path" id="file_path" type="file" accept=".csv"></span>
					<a class="input-group-addon input-sm btn btn-default fileinput-exists" href="#" data-dismiss="fileinput">{$lang['common_import_remove_file']}</a>
				</div>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">
//validation and submit handling
$(document).ready(function()
{
	$('#item_form').validate($.extend({
		submitHandler:function(form)
		{
			$(form).ajaxSubmit({
			success:function(response)
			{
				dialog_support.hide();
				post_data_form_submit(response, "excel");
			},
			dataType:'json'
		});

		},
		errorLabelContainer: "#error_message_box",
 		wrapper: "li",
		rules: 
		{
			file_path:"required"
   		},
		messages: 
		{
   			file_path:"{$lang['common_import_full_path']}"
		}
	}, dialog_support.error));
});
</script>