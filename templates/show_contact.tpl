<div class="display contact">
{include file="bitpackage:contact/contact_header.tpl"}
{include file="bitpackage:contact/contact_date_bar.tpl"}
{if $gContent->isCommentable()}
	{include file="bitpackage:contact/comments_edit.tpl"}
{/if}
{jstabs}
	{jstab title="General"}
		{include file="bitpackage:contact/display_contact.tpl"}
	{/jstab}
	{jstab title="Local Notes"}
		{if $gContent->isCommentable()}
			{include file="bitpackage:contact/comments.tpl"}
		{/if}
	{/jstab}
	{jstab title="Documents"}
		Link to private fisheye document gallery
	{/jstab}
{/jstabs}
</div> {* end .contact *}
