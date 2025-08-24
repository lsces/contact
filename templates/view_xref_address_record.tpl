{strip}
<td>
	{$gContent->mInfo.$source[xref].source_title|escape}
</td>
<td>
&nbsp;
</td>
<td>
	{$gContent->mInfo.$source[xref].xkey_ext|escape}, {$gContent->mInfo.$source[xref].address|escape}, {$gContent->mInfo.$source[xref].xkey|escape}
</td>
<td>
	{$gContent->mInfo.$source[xref].data|escape}
</td>
<td>
{if $source ne 'history' }
	{$gContent->mInfo.$source[xref].start_date|bit_short_date}
{else}	
	{$gContent->mInfo.$source[xref].end_date|bit_short_date}
{/if}	
</td>
{if $gBitSystem->isFeatureActive( 'contact_list_last_modified' )}
	<td>
		{$gContent->mInfo.xref[xref].last_update_date|bit_long_date}
	</td>
{/if}
<td>
	<span class="actionicon">
		{if $gBitUser->hasPermission( 'p_contact_view_detail' )}
			{smartlink ititle="View" ifile="view_xref.php" booticon="icon-view" content_id=$gContent->mInfo.content_id xref_id=$gContent->mInfo.$source[xref].xref_id}
		{/if}	
		{if $gBitUser->hasPermission( 'p_contact_update' ) and $source ne 'history' }
				{smartlink ititle="Edit" ifile="edit_xref.php" booticon="icon-note-edit" content_id=$gContent->mInfo.content_id xref_id=$gContent->mInfo.$source[xref].xref_id}
		{/if}	
		{if $gBitUser->hasPermission( 'p_contact_expunge' ) and $gContent->mInfo.$source[xref].source ne 'KEY_B' }
			{if $source eq 'history' }
				{smartlink ititle="Restore" ifile="edit_xref.php" booticon="icon-note-edit" content_id=$gContent->mInfo.content_id xref_id=$gContent->mInfo.$source[xref].xref_id expunge=-1}
			{else}
				{smartlink ititle="Delete" ifile="edit_xref.php" booticon="icon-note-delete" content_id=$gContent->mInfo.content_id xref_id=$gContent->mInfo.$source[xref].xref_id expunge=1}
			{/if}	
		{/if}	
	</span>
</td>
{/strip}
