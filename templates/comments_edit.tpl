{if $gBitUser->hasPermission( 'p_liberty_post_comments' ) }
	{strip}
	<div class="display comment">
		<div class="body"{if !( $post_comment_request || $post_comment_preview )} id="editcomments"{/if}>
			<div id="edit_comments" {if $comments_ajax}style="display:none"{/if}>
				{include file="bitpackage:contact/comments_post_inc.tpl" post_title="Post Note"}
			</div>
		</div><!-- end .body -->
	</div><!-- end .comment -->
	{/strip}
{/if}
