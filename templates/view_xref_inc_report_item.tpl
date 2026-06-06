{strip}
<td>
	{if $isHistory }
		{$xrefInfo.xref_title|escape}
	{else}
		&nbsp;
	{/if}</td>
<td>
	{if isset($xrefInfo.xref) && $xrefInfo.xref <> '' && $xrefInfo.xref > 100 }
		{$xrefInfo.xref|escape}
		{smartlink ititle="Link to" ifile="view_form.php" biticon="edit" content_id=$xrefInfo.xref}
	{else}
		------
	{/if}
</td>
<td>
	{$xrefInfo.xkey|escape} {$xrefInfo.xkey_ext|escape}
</td>
<td>
	{$xrefInfo.data|escape}
</td>
<td>
	{$xrefInfo.start_date|bit_short_datetime}
</td>
<td>
	Completed: {$xrefInfo.last_update_date|bit_short_datetime}
</td>
<td>
	<span class="actionicon">
		{if $gBitUser->hasPermission( 'p_contact_view_detail' )}
			{smartlink ititle="View" ifile="view_form.php" biticon="view-fullscreen" content_id=$gContent->mInfo.content_id xref_id=$xrefInfo.xref_id}
		{/if}
		{if $xrefAllowEdit && $gBitUser->hasPermission( 'p_contact_expunge' ) }
			{if $isHistory }
				{smartlink ititle="Restore" ifile="edit_xref.php" biticon="edit" content_id=$gContent->mInfo.content_id xref_id=$xrefInfo.xref_id expunge=-1}
			{else}
				{smartlink ititle="Delete" ifile="edit_xref.php" biticon="user-trash" content_id=$gContent->mInfo.content_id xref_id=$xrefInfo.xref_id expunge=1}
			{/if}
		{/if}
	</span>
</td>
{/strip}
