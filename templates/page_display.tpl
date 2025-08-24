{strip}
<div class="body"{if $users_double_click eq 'y' and $dblclickedit eq 'y' and $gBitUser->hasPermission( 'bit_p_edit' )} ondblclick="location.href='{$smarty.const.CONTACT_PKG_URL}edit.php?content_id={$gContent->mInfo.content_id}';"{/if}>
	<div class="header">
		{tr}<h1>Project-{$gContent->mInfo.project_name}{/tr}{tr} Version-{$gContent->mInfo.revision}</h1>{/tr}
	</div>
	{if $gContent->mInfo.status=='C' }
	<div class="date">
		{tr}Closed by {displayname user=$gContent->mInfo.closed_user user_id=$gContent->mInfo.closed_user_id real_name=$gContent->mInfo.closed_real_name} on {$gContent->mInfo.closed|bit_short_datetime}{/tr}
	</div>
	{/if}
	{if $gContent->mInfo.status=='O' }
	<div class="date">
		{tr}Incident Report Open - Priority {$gContent->mInfo.priority} {/tr}
	</div>
	{/if}
	{if $gContent->mInfo.status=='X' }
	<div class="date">
		{tr}Incident Report Cancelled{/tr}
	</div>
	{/if}
	<div class="header">
		{tr}<h1>{$gContent->mInfo.title}</h1>{/tr}
	</div>
	<div class="content">
		{$parsed}
		<div class="clear"></div>
	</div> <!-- end .content -->
</div> <!-- end .body -->
{/strip}
