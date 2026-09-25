{strip}
<td>{$xrefInfo.xref_title|escape}</td>
<td>
	{if $xrefInfo.xref > 0}
		<a href="{$smarty.const.FISHEYE_PKG_URL}view_album.php?content_id={$xrefInfo.xref|escape}">{$xrefInfo.linked_title|default:$xrefInfo.xref|escape}</a>
	{else}
		&nbsp;
	{/if}
</td>
<td>{$xrefInfo.data|escape}</td>
{include file="bitpackage:liberty/xref/dates_cell.tpl"}
{include file="bitpackage:liberty/xref/action_icons.tpl"}
{/strip}
