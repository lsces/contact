{strip}
{if $packageMenuTitle}<a class="dropdown-toggle" data-toggle="dropdown" href="#"> {tr}{$packageMenuTitle}{/tr} <b class="caret"></b></a>{/if}
<ul class="{$packageMenuClass}">
	<li><a class="item" href="{$smarty.const.CONTACT_PKG_URL}list.php">{booticon ipackage="icons" iname="icon-telephone" iexplain="List contacts" ilocation=menu}</a></li>
	<li><a class="item" href="{$smarty.const.CONTACT_PKG_URL}list_contracts.php?contract=1">{booticon ipackage="icons" iname="icon-telephone-key" iexplain="List domestic contacts" ilocation=menu}</a></li>
	<li><a class="item" href="{$smarty.const.CONTACT_PKG_URL}list_contracts.php?contract=1">{booticon ipackage="icons" iname="icon-telephone-link" iexplain="List business contacts" ilocation=menu}</a></li>
	{if $gBitUser->isAdmin() or $gBitUser->hasPermission( 'p_contact_edit' ) }
		<li><a class="item" href="{$smarty.const.CONTACT_PKG_URL}edit.php">{booticon ipackage="icons" iname="icon-telephone-edit" iexplain="Create/Edit a Contact" ilocation=menu}</a></li>
	{/if}
	{if $pageInfo.content_id and ( $gBitUser->isAdmin() or $gBitUser->hasPermission( 'p_task_edit' ) ) }
		<li><a class="item" href="{$smarty.const.TASKS_PKG_URL}edit.php?content_id={$pageInfo.content_id}">{booticon ipackage="icons" iname="icon-build_add" iexplain="Create/Edit a Job" ilocation=menu}</a></li>
		<li><a class="item" href="{$smarty.const.TASKS_PKG_URL}add_callout.php?content_id={$pageInfo.content_id}&property={$pageInfo.content_id}">{booticon ipackage="icons" iname="icon-telephone-error" iexplain="Log a callout" ilocation=menu}</a></li>
	{/if}
	{if $gBitUser->hasPermission('p_contact_admin')}
		<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=contact">{booticon ipackage="icons" iname="icon-telephone-go" iexplain="Admin contacts" ilocation=menu}</a></li>
	{/if}
</ul>
{/strip}
