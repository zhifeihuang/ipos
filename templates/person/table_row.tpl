{nocache}
{foreach $person as $p}
<tr id="{$p['person_id']}">
<td><input type='checkbox' value="{$p['person_id']}"/></td>
<td>{$p['person_id']}</td>
<td>{$p['last_name']|truncate:15}</td>
<td>{$p['first_name']|truncate:15}</td>
<td>{$p['email']|truncate:22}</td>
<td>{format_phone_number phone={$p['phone_number']}}</td>
{if !empty($subgrant["{$controller_name}_update"])}
<td>
<a title="{$lang["{$controller_name}_update"]}" class="modal-dlg modal-btn-submit" href="home.php?act={$controller_name}&f=get&id={$p['person_id']}"><span class="glyphicon glyphicon-edit"></span></a></td>
{/if}
</tr>
{foreachelse}
<tr><td colspan='6'><div class='alert alert-dismissible alert-info'>{$lang['common_no_persons_to_display']}</div></td>
</tr>
{/foreach}
{/nocache}