{* $Header: /cvsroot/bitweaver/_bit_contact/templates/list_contact.tpl,v 1.3 2010/02/09 17:21:21 wjames5 Exp $ *}
<div class="floaticon">
	{if $gBitUser->hasPermission('p_contact_admin')}
		<a title="{tr}configure listing{/tr}" href="{$gBitLoc.KERNEL_PKG_URL}admin/index.php?page=contact">{biticon ipackage="icons" iname="document-properties" iexplain="configure"}</a>
	{/if}
	{bithelp}
</div>

<div class="admin contact">
<div class="header">
<h1><a href="{$gBitLoc.CONTACT_PKG_URL}list_contracts.php?contract=2">{tr}Contract Records{/tr}</a></h1>
</div>

<div class="body">

{*
<table class="find">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="{$gBitLoc.CONTACT_PKG_URL}list_contacts.php">
     <input type="text" name="find" value="{$listInfo.find|escape}" />
     <input type="submit" name="search" value="{tr}find{/tr}" />
     <input type="hidden" name="sort_mode" value="{$listInfo.sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
*}

<form id="checkform" method="post" action="{$smarty.server.PHP_SELF}">
<input type="hidden" name="offset" value="{$listInfo.offset|escape}" />
<input type="hidden" name="sort_mode" value="{$listInfo.sort_mode|escape}" />
<table class="data">
<tr>
	<th style="text-align:center;"><a href="{$gBitLoc.CONTACT_PKG_URL}list_contracts.php?offset={$listInfo.offset}&amp;sort_mode={if $listInfo.sort_mode eq 'contract_desc'}contract_asc{else}contract_desc{/if}">{tr}Contract Number{/tr}</a></th>
	<th style="text-align:center;"><a href="{$gBitLoc.CONTACT_PKG_URL}list_contracts.php?offset={$listInfo.offset}&amp;sort_mode={if $listInfo.sort_mode eq 'content_id_desc'}content_id_asc{else}content_id_desc{/if}">{tr}Contact Id{/tr}</a></th>
	<th style="text-align:center;"><a href="{$gBitLoc.CONTACT_PKG_URL}list_contracts.php?offset={$listInfo.offset}&amp;sort_mode={if $listInfo.sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Title{/tr}</a></th>
	<th style="text-align:center;"><a href="{$gBitLoc.CONTACT_PKG_URL}list_contracts.php?offset={$listInfo.offset}&amp;sort_mode={if $listInfo.sort_mode eq 'town_desc'}town_asc{else}town_desc{/if}">{tr}Town{/tr}</a></th>
	<th style="text-align:center;"><a href="{$gBitLoc.CONTACT_PKG_URL}list_contracts.php?offset={$listInfo.offset}&amp;sort_mode={if $listInfo.sort_mode eq 'postcode_desc'}postcode_asc{else}postcode_desc{/if}">{tr}Postcode{/tr}</a></th>
</tr>

{cycle values="even,odd" print=false}
{section name=changes loop=$list}
<tr class="{cycle}">
<td style="text-align:center;">{$list[changes].contract}</td>
<td><a href="{$gBitLoc.CONTACT_PKG_URL}index.php?content_id={$list[changes].content_id|escape:"url"}" title="{$list[changes].content_id}">{$list[changes].content_id|truncate:20:"...":true}</a>
	{if $gBitUser->hasPermission('p_edit_contact')}
		<br />(<a href="{$gBitLoc.CONTACT_PKG_URL}edit.php?content_id={$list[changes].content_id|escape:"url"}">{tr}edit{/tr}</a>)
	{/if}
</td>
<td style="text-align:center;">{$list[changes].title}<br />
				{if isset($list[changes].house) && ($list[changes].house <> '') }
					{$list[changes].house},&nbsp;{/if}
				{if isset($list[changes].add1) && ($list[changes].add1 <> '') }
					{$list[changes].add1},&nbsp;{/if}
				{if isset($list[changes].add2) && ($list[changes].add2 <> '') }
					{$list[changes].add2},&nbsp;{/if}
				{if isset($list[changes].add3) && ($list[changes].add3 <> '') }
					{$list[changes].add3},&nbsp;{/if}
				{if isset($list[changes].add4) && ($list[changes].add4 <> '') }
					{$list[changes].add4},&nbsp;{/if}
<td style="text-align:center;">{$list[changes].town}<br />
	{$list[changes].county}</td>
<td style="text-align:center;">{$list[changes].postcode}</td>
</tr>
{sectionelse}
	<tr class="norecords"><td colspan="16">
		{tr}No records found{/tr}
	</td></tr>
{/section}
</table>
</form>

</div><!-- end .body -->

{pagination}

</div> {* end .admin *}
