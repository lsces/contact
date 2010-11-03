{if $gBitUser->hasPermission( 'p_liberty_post_comments' ) || $gBitUser->hasPermission( 'p_liberty_read_comments' ) }
	{strip}
	<div class="display comment">
		<div class="header">
			<h2>{tr}Notes{/tr}</h2>
		</div>

		<div class="body">
			{include file="bitpackage:liberty/comments_display_option_bar.tpl"}

			{if $gBitUser->hasPermission( 'p_liberty_post_comments' )}
				<div class="row">
					{form enctype="multipart/form-data" action="`$comments_return_url`#editcomments" id="editcomment-form"}
						<input type="submit" name="post_comment_request" value="{tr}Add Note{/tr}" onclick="LibertyComment.attachForm('comment_{$gContent->mContentId}', '{$gContent->mContentId}', {if $gContent->mContentId}{$gContent->mContentId}{elseif $commentsParentId}{$commentsParentId}{else}null{/if})"/>
					{/form}
				</div>
			{/if}

			<div id="comment_{$gContent->mContentId}"></div>
				{foreach name=comments_loop key=key item=item from=$comments}
					{displaycomment comment="$item"}
				{/foreach}
			<div id="comment_{$gContent->mContentId}_footer"></div>

			{libertypagination ihash=$commentsPgnHash}
		</div><!-- end .body -->
	</div><!-- end .comment -->
	{/strip}
{/if}
