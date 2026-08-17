{strip}
<td>
	{$xrefInfo.xref_title|escape}
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
	{$xrefInfo.start_date|bit_short_datetime}
{else}
	{$xrefInfo.end_date|bit_short_datetime}
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
			{smartlink ititle="Edit" ipackage="liberty" ifile="edit_xref.php" biticon="edit" content_id=$gContent->mInfo.content_id xref_id=$xrefInfo.xref_id}
		{/if}
		{if $gBitUser->hasPermission( 'p_contact_update' ) }
			{if $isHistory }
				{smartlink ititle="Restore" ipackage="liberty" ifile="edit_xref.php" biticon="edit" content_id=$gContent->mInfo.content_id xref_id=$xrefInfo.xref_id expunge=-1}
			{else}
				{smartlink ititle="Archive" ipackage="liberty" ifile="edit_xref.php" biticon="archive-insert" content_id=$gContent->mInfo.content_id xref_id=$xrefInfo.xref_id expunge=1}
			{/if}
		{/if}
		{if $gBitUser->hasPermission( 'p_contact_expunge' ) }
			{smartlink ititle="Delete" ipackage="liberty" ifile="edit_xref.php" biticon="user-trash" content_id=$gContent->mInfo.content_id xref_id=$xrefInfo.xref_id expunge=3}
		{/if}
	</span>
</td>
{/if}
{/strip}
