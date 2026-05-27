{strip}
{if $packageMenuTitle}<a class="dropdown-toggle" data-toggle="dropdown" href="#"> {tr}{$packageMenuTitle}{/tr} <b class="caret"></b></a>{/if}
<ul class="{$packageMenuClass}">
	<li><a class="item" href="{$smarty.const.CONTACT_PKG_URL}list.php">{booticon ipackage="icons" iname="icon-telephone" iexplain="List contacts" ilocation=menu}</a></li>
	{if $gBitUser->isAdmin() || $gBitUser->hasPermission( 'p_contact_edit' ) }
		<li><a class="item" href="{$smarty.const.CONTACT_PKG_URL}edit.php">{booticon ipackage="icons" iname="icon-telephone-edit" iexplain="Create/Edit a Contact" ilocation=menu}</a></li>
	{/if}
	{if !empty($gContent->mContentId) && $gBitUser->hasPermission( 'p_liberty_post_comments' ) }
		<li><a class="item" href="{$smarty.const.CONTACT_PKG_URL}display_contact.php?content_id={$gContent->mContentId}#addcomment">{booticon ipackage="icons" iname="icon-telephone-error" iexplain="Log a call" ilocation=menu}</a></li>
	{/if}
	{if !empty($gContent->mContentId) && $gBitSystem->isPackageActive('order') && ( $gBitUser->isAdmin() || $gBitUser->hasPermission( 'p_contact_update' ) ) }
		<li><a class="item" href="{$smarty.const.ORDER_PKG_URL}edit.php?content_id={$gContent->mContentId}">{booticon ipackage="icons" iname="icon-build_add" iexplain="Create an Order" ilocation=menu}</a></li>
	{/if}
</ul>
{/strip}
