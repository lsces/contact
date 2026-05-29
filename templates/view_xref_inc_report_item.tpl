{strip}
<td>
	{if $source eq 'history' }
		{$gContent->mInfo.$source[xref].source_title|escape}
	{else}
		&nbsp;
	{/if}</td>
<td>
	{if isset($gContent->mInfo.$source[xref].xref) && $gContent->mInfo.$source[xref].xref <> '' && $gContent->mInfo.$source[xref].xref > 100 }
		{$gContent->mInfo.$source[xref].xref|escape}
		{smartlink ititle="Link to" ifile="view_form.php" booticon="icon-note-edit" content_id=$gContent->mInfo.$source[xref].xref}
	{else}
		------
	{/if}
</td>
<td>
	{$gContent->mInfo.$source[xref].xkey|escape} {$gContent->mInfo.$source[xref].xkey_ext|escape}
</td>
<td>
	{$gContent->mInfo.$source[xref].data|escape}
</td>
<td>
	{$gContent->mInfo.$source[xref].start_date|bit_short_datetime}
</td>
<td>
	Completed: {$gContent->mInfo.$source[xref].last_update_date|bit_short_datetime}
</td>
<td>
	<span class="actionicon">
		{if $gBitUser->hasPermission( 'p_contact_view_detail' )}
			{smartlink ititle="View" ifile="view_form.php" booticon="icon-view" content_id=$gContent->mInfo.content_id xref_id=$gContent->mInfo.$source[xref].xref_id}
		{/if}	
		{if $gBitUser->hasPermission( 'p_contact_expunge' ) }
			{if $source eq 'history' }
				{smartlink ititle="Restore" ifile="edit_xref.php" booticon="icon-note-edit" content_id=$gContent->mInfo.content_id xref_id=$gContent->mInfo.$source[xref].xref_id expunge=-1}
			{else}
				{smartlink ititle="Delete" ifile="edit_xref.php" booticon="icon-note-delete" content_id=$gContent->mInfo.content_id xref_id=$gContent->mInfo.$source[xref].xref_id expunge=1}
			{/if}	
		{/if}	
	</span>
</td>
{/strip}
