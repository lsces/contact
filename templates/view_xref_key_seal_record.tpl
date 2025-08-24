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
				{smartlink ititle="Callout" ifile="edit_key_break.php" booticon="icon-redo" expunge=0 content_id=$gContent->mInfo.content_id xref_id=$gContent->mInfo.$source[xref].xref_id}
				{smartlink ititle="Reseal" ifile="edit_key_break.php" booticon="icon-undo" expunge=2 content_id=$gContent->mInfo.content_id xref_id=$gContent->mInfo.$source[xref].xref_id}
		{/if}	
	</span>
</td>
{/strip}
