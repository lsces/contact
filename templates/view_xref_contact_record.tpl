{strip}
<td>
	{$gContent->mInfo.$source[xref].source_title|escape}
</td>
<td>
	{if isset($gContent->mInfo.$source[xref].xref) && $gContent->mInfo.$source[xref].xref <> '' && $gContent->mInfo.$source[xref].xref > 100 }
		{$gContent->mInfo.$source[xref].xref|escape}
		{smartlink ititle="Link to" ifile="display_contact.php" booticon="icon-note-edit" content_id=$gContent->mInfo.$source[xref].xref}
	{else}
		&nbsp;
	{/if}
</td>
<td>
	{$gContent->mInfo.$source[xref].xkey|escape} {$gContent->mInfo.$source[xref].xkey_ext|escape}
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
		{if $gBitUser->hasPermission( 'p_contact_update' ) and $source ne 'history' }
			{if $gContent->mInfo.$source[xref].source eq 'KEY_B' }
				{smartlink ititle="Callout" ifile="edit_key_break.php" booticon="icon-redo" expunge=0 content_id=$gContent->mInfo.content_id xref_id=$gContent->mInfo.$source[xref].xref_id}
				{smartlink ititle="Reseal" ifile="edit_key_break.php" booticon="icon-undo" expunge=2 content_id=$gContent->mInfo.content_id xref_id=$gContent->mInfo.$source[xref].xref_id}
			{else}
				{smartlink ititle="Edit" ifile="edit_xref.php" booticon="icon-note-edit" content_id=$gContent->mInfo.content_id xref_id=$gContent->mInfo.$source[xref].xref_id}
			{/if}
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
