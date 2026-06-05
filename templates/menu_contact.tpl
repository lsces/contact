{strip}
{if $packageMenuTitle}<a class="dropdown-toggle" data-toggle="dropdown" href="#"> {tr}{$packageMenuTitle}{/tr} <b class="caret"></b></a>{/if}
<ul class="{$packageMenuClass}">
	<li><a class="item" href="{$smarty.const.CONTACT_PKG_URL}list_contacts.php">{biticon ipackage="icons" iname="preferences-system-network-proxy" iexplain="List contacts" ilocation=menu}</a></li>
	{if $gBitUser->isAdmin() || $gBitUser->hasPermission( 'p_contact_edit' ) }
		<li><a class="item" href="{$smarty.const.CONTACT_PKG_URL}add_person.php">{biticon ipackage="icons" iname="edit" iexplain="Add Person" ilocation=menu}</a></li>
		<li><a class="item" href="{$smarty.const.CONTACT_PKG_URL}add_business.php">{biticon ipackage="icons" iname="edit" iexplain="Add Business" ilocation=menu}</a></li>
	{/if}
	{if !empty($gContent->mContentId) && $gBitUser->hasPermission( 'p_liberty_post_comments' ) }
		<li><a class="item" href="{$smarty.const.CONTACT_PKG_URL}display_contact.php?content_id={$gContent->mContentId}#addcomment">{biticon ipackage="icons" iname="dialog-error" iexplain="Log a call" ilocation=menu}</a></li>
	{/if}
	{if !empty($gContent->mContentId) && $gBitSystem->isPackageActive('order') && ( $gBitUser->isAdmin() || $gBitUser->hasPermission( 'p_contact_update' ) ) }
		<li><a class="item" href="{$smarty.const.ORDER_PKG_URL}edit.php?content_id={$gContent->mContentId}">{biticon ipackage="icons" iname="list-add" iexplain="Create an Order" ilocation=menu}</a></li>
	{/if}
</ul>
{/strip}
