{strip}
<td>{$xrefInfo.xref_title|escape}</td>
<td>
	{if isset($xrefInfo.xref) && $xrefInfo.xref > 0}
		{smartlink ititle=$xrefInfo.linked_title|default:$xrefInfo.xref ifile="view.php" content_id=$xrefInfo.xref}
	{else}
		&nbsp;
	{/if}
</td>
<td>{$xrefInfo.data|escape}</td>
{include file="bitpackage:liberty/xref/dates_cell.tpl"}
{include file="bitpackage:liberty/xref/action_icons.tpl"}
{/strip}
