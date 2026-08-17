{strip}
<td>
	{$xrefInfo.xref_title|escape}
</td>
<td>
	{if isset($xrefInfo.xref) && $xrefInfo.xref <> '' && $xrefInfo.xref > 100 }
		{smartlink ititle=$xrefInfo.xkey_ext|default:$xrefInfo.xkey ifile="view.php" content_id=$xrefInfo.xref}
	{else}
		{$xrefInfo.xkey|escape} {$xrefInfo.xkey_ext|escape}
	{/if}
</td>
<td>
	{$xrefInfo.data|escape}
</td>
{include file="bitpackage:liberty/xref/dates_cell.tpl"}
{if $xrefInfo.item eq 'KEY_B'}
	{if $xrefAllowEdit|default:true}
	<td>
		<span class="actionicon">
			{if $gContent->hasUpdatePermission() && !$isHistory }
				{smartlink ititle="Callout" ifile="edit_key_break.php" biticon="edit-redo" expunge=0 content_id=$gContent->mInfo.content_id xref_id=$xrefInfo.xref_id}
				{smartlink ititle="Reseal" ifile="edit_key_break.php" biticon="edit-undo" expunge=2 content_id=$gContent->mInfo.content_id xref_id=$xrefInfo.xref_id}
			{/if}
		</span>
	</td>
	{/if}
{else}
	{include file="bitpackage:liberty/xref/action_icons.tpl"}
{/if}
{/strip}
