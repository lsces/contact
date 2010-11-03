{strip}
<ul>
	<li><a class="item" href="{$smarty.const.CONTACT_PKG_URL}list.php">{tr}List Contacts{/tr}</a></li>
	{if $gBitUser->isAdmin() or $gBitUser->hasPermission( 'p_contact_edit' ) }
		<li><a class="item" href="{$smarty.const.CONTACT_PKG_URL}edit.php">{biticon ipackage="icons" iname="document-new" iexplain="create contact" iforce="icon"} {tr}Create/Edit a Contact{/tr}</a></li>
	{/if}
	{if $gBitUser->hasPermission('p_contact_admin')}
		<li><a class="item" href="{$smarty.const.CONTACT_PKG_URL}load_contacts.php">{tr}Load Contact Index Dump{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=contact">{tr}Admin contacts{/tr}</a></li>
	{/if}
</ul>
{/strip}
