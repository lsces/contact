{strip}
{if $packageMenuTitle}<a href="#"> {tr}{$packageMenuTitle|capitalize}{/tr}</a>{/if}
<ul class="{$packageMenuClass}">
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=contact">{tr}Contact Manager List Settings{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.CONTACT_PKG_URL}admin/admin_contact_type.php">{tr}Contact Type List{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.CONTACT_PKG_URL}admin/admin_xref_types.php">{tr}Contact Xref List{/tr}</a></li>
</ul>
{/strip}
