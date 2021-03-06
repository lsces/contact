<div class="display contact">
{include file="bitpackage:contact/contact_header.tpl"}
{include file="bitpackage:contact/contact_date_bar.tpl"}
	<div class="body">
		{include file="bitpackage:contact/display_contact.tpl"}
	{jstabs}
		{jstab title="Local Notes" class="contact_notes"}
			{include file="bitpackage:liberty/comments.tpl"}
		{/jstab}
		{if $pageInfo.client_list }
			{jstab title="Client List" class="client_list"}
				{include file="bitpackage:contact/list_clients.tpl" client_list=$pageInfo.client_list}
			{/jstab}
		{/if}
		{if $gBitSystem->isFeatureActive('package_tasks')}
			{jstab title="Activity" class="todo_list"}
				{include file="bitpackage:tasks/list_tasks.tpl"}
			{/jstab}
		{/if}
		{jstab title="Images" class="contact_pics"}
			{include file="`$smarty.const.FISHEYE_PKG_PATH`gallery_views/fixed_grid/fisheye_fixed_grid_test.tpl" }
		{/jstab}
	{/jstabs}
	</div> {* end .body *}
</div> {* end .contact *}
