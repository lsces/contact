<div class="display contact">
<header>
	{include file="bitpackage:contact/contact_icons.tpl"}
	{include file="bitpackage:contact/contact_header.tpl"}
</header>
{include file="bitpackage:contact/contact_date_bar.tpl"}
	<div class="body">
		{include file="bitpackage:contact/display_contact.tpl"}
	{jstabs}
		{jstab title="Local Notes" class="contact_notes"}
			{include file="bitpackage:contact/comments.tpl"}
		{/jstab}
	{/jstabs}
	{include file="bitpackage:contact/fisheye_fixed_grid_contact.tpl"}
	</div> {* end .body *}
</div> {* end .contact *}
