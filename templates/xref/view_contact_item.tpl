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
{include file="bitpackage:liberty/xref/action_icons.tpl"}
{/strip}
