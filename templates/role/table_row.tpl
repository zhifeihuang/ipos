{nocache}
{foreach $role as $r}
<tr id="{$r['role']}">
<td width='5%'><input type='checkbox' value="{$r['role']}"/></td>
<td width="20%">{$r['role']}</td>
<td width="50%">{$r['permission']}</td>
<td width="5%">
<a title="{$lang["config_role_update"]}" class="modal-dlg modal-btn-submit" href="home.php?act=config&f=get_role&id={$r['role']}"><span class="glyphicon glyphicon-edit"></span></a></td>
</tr>
{foreachelse}
<tr><td colspan='6'><div class='alert alert-dismissible alert-info'>{$lang['common_no_data_display']}</div></td>
</tr>
{/foreach}
{/nocache}