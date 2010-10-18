{strip}
<div class="body"{if $users_double_click eq 'y' and $dblclickedit eq 'y' and $gBitUser->hasPermission( 'bit_p_edit' )} ondblclick="location.href='{$smarty.const.CONTACT_PKG_URL}edit.php?content_id={$pageInfo.content_id}';"{/if}>
	<div class="header">
		{tr}<h1>Project-{$pageInfo.project_name}{/tr}{tr} Version-{$pageInfo.revision}</h1>{/tr}
	</div>
	{if $pageInfo.status=='C' }
	<div class="date">
		{tr}Closed by {displayname user=$pageInfo.closed_user user_id=$pageInfo.closed_user_id real_name=$pageInfo.closed_real_name} on {$pageInfo.closed|bit_short_datetime}{/tr}
	</div>
	{/if}
	{if $pageInfo.status=='O' }
	<div class="date">
		{tr}Incident Report Open - Priority {$pageInfo.priority} {/tr}
	</div>
	{/if}
	{if $pageInfo.status=='X' }
	<div class="date">
		{tr}Incident Report Cancelled{/tr}
	</div>
	{/if}
	<div class="header">
		{tr}<h1>{$pageInfo.title}</h1>{/tr}
	</div>
	<div class="content">
		{$parsed}
		<div class="clear"></div>
	</div> <!-- end .content -->
</div> <!-- end .body -->
{/strip}
