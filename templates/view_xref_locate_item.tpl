{strip}
<td>
	{$xrefInfo.xref_title|escape}
</td>
<td>
	&nbsp;
</td>
<td>
	{$xrefInfo.xkey|escape} {$xrefInfo.xkey_ext|escape}
</td>
<td>
	{$xrefInfo.data|escape}
</td>
{if $xrefAllowEdit}
<td>
{if !$isHistory }
	{$xrefInfo.start_date|bit_short_date}
{else}
	{$xrefInfo.end_date|bit_short_date}
{/if}
</td>
{if $gBitSystem->isFeatureActive( 'contact_list_last_modified' )}
	<td>
		{$gContent->mInfo.xref[xref].last_update_date|bit_long_date}
	</td>
{/if}
<td>
	<span class="actionicon">
		{if $gBitUser->hasPermission( 'p_contact_update' ) && !$isHistory }
			{smartlink ititle="Edit" ifile="edit_xref.php" biticon="edit" content_id=$gContent->mInfo.content_id xref_id=$xrefInfo.xref_id}
		{/if}
		{if $gBitUser->hasPermission( 'p_contact_expunge' ) }
			{if $isHistory }
				{smartlink ititle="Restore" ifile="edit_xref.php" biticon="edit" content_id=$gContent->mInfo.content_id xref_id=$xrefInfo.xref_id expunge=-1}
			{else}
				{smartlink ititle="Delete" ifile="edit_xref.php" biticon="user-trash" content_id=$gContent->mInfo.content_id xref_id=$xrefInfo.xref_id expunge=1}
			{/if}
		{/if}
	</span>
</td>
{/if}
{/strip}
