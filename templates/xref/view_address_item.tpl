{strip}
<td>
	{$xrefInfo.xref_title|escape}
</td>
<td>
	{$xrefInfo.xkey_ext|escape}{if $xrefInfo.address}, {$xrefInfo.address|escape}{/if}{if $xrefInfo.xkey}, {$xrefInfo.xkey|escape}{/if}
</td>
<td>
	{$xrefInfo.data|escape}
</td>
{include file="bitpackage:liberty/xref/dates_cell.tpl"}
{include file="bitpackage:liberty/xref/action_icons.tpl" xrefProtected=($xrefInfo.item eq 'KEY_B')}
{/strip}
