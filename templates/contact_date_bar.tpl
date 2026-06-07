<div class="floaticon">
  {if $lock}
    {biticon ipackage="icons" iname="lock" iexplain="Locked"}{$info.editor|userlink}
  {/if}
  {if $print_page ne 'y'}
    {if !$lock}
      {if $gBitUser->hasPermission('p_edit_contact')}
		{smartlink ititle="Edit Contact" ifile="edit.php" biticon="edit" content_id=$gContent->mInfo.content_id}
      {/if}
    {/if}
    <a title="{tr}print{/tr}" href="print.php?content_id={$gContent->mInfo.content_id}">{biticon ipackage="icons" iname="document-print" iexplain="print"}</a>
      {if $gBitUser->hasPermission('p_contact_expunge')}
        {smartlink ititle="Delete Contact" ifile="edit.php" biticon="user-trash" content_id=$gContent->mInfo.content_id expunge=1}
      {/if}
  {/if} {* end print_page *}
</div> {*end .floaticon *}
<div class="date">
	{tr}Created by{/tr} {displayname user=$gContent->mInfo.creator_user user_id=$gContent->mInfo.user_id real_name=$gContent->mInfo.creator_real_name}, {tr}Last modification by{/tr} {displayname user=$gContent->mInfo.modifier_user user_id=$gContent->mInfo.modifier_user_id real_name=$gContent->mInfo.modifier_real_name} on {$gContent->mInfo.last_modified|bit_long_datetime}
</div>
