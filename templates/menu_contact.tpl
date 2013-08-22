{strip}
{if $packageMenuTitle}<a class="dropdown-toggle" data-toggle="dropdown" href="#"> {tr}{$packageMenuTitle}{/tr} <b class="caret"></b></a>{/if}
<ul class="{$packageMenuClass}">
	<li><a class="item" href="{$smarty.const.CONTACT_PKG_URL}list.php">{tr}List Contacts{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.CONTACT_PKG_URL}list_contracts.php?contract=1">{tr}List Domestic Contracts{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.CONTACT_PKG_URL}list_contracts.php?contract=1">{tr}List Business Contracts{/tr}</a></li>
	{if $gBitUser->isAdmin() or $gBitUser->hasPermission( 'p_contact_edit' ) }
		<li><a class="item" href="{$smarty.const.CONTACT_PKG_URL}edit.php">{biticon ipackage="icons" iname="document-new" iexplain="create contact" iforce="icon"} {tr}Create/Edit a Contact{/tr}</a></li>
	{/if}
	{if $pageInfo.content_id and ( $gBitUser->isAdmin() or $gBitUser->hasPermission( 'p_task_edit' ) ) }
		<li><a class="item" href="{$smarty.const.TASKS_PKG_URL}edit.php?content_id={$pageInfo.content_id}">{biticon ipackage="icons" iname="document-new" iexplain="create task" iforce="icon"} {tr}Create/Edit a Job{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.TASKS_PKG_URL}add_callout.php?content_id={$pageInfo.content_id}&property={$pageInfo.content_id}">{biticon ipackage="icons" iname="document-new" iexplain="create task" iforce="icon"} {tr}Log a callout{/tr}</a></li>
	{/if}
	{if $gBitUser->hasPermission('p_contact_admin')}
		<li><a class="item" href="{$smarty.const.CONTACT_PKG_URL}load_contacts.php">{tr}Load Contact Index Dump{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=contact">{tr}Admin contacts{/tr}</a></li>
	{/if}
</ul>
{/strip}
