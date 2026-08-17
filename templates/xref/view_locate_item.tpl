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
{include file="bitpackage:liberty/xref/dates_cell.tpl"}
{include file="bitpackage:liberty/xref/action_icons.tpl"}
{/strip}
