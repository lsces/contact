<div class="floaticon">
  {if $lock}
    {booticon iname="icon-lock" ipackage="icons" iexplain="Locked"}{$info.editor|userlink}
  {/if}
  {if $print_page ne 'y'}
    {if !$lock}
      {if $gBitUser->hasPermission('p_edit_contact')}
		{smartlink ititle='Add additional crossref record' ifile="add_xref.php" booticon="icon-note-add" content_id=$gContent->mInfo.content_id xref_type=-1}
		{smartlink ititle="Edit Contact" ifile="edit.php" booticon="icon-user-edit" content_id=$gContent->mInfo.content_id}
      {/if}
    {/if}
    <a title="{tr}print{/tr}" href="print.php?content_id={$gContent->mInfo.content_id}">{booticon ipackage="icons" iname="icon-print" iexplain="print"}</a>
      {if $gBitUser->hasPermission('p_remove_contact')}
        <a title="{tr}remove this contact{/tr}" href="remove_contact.php?content_id={$gContent->mInfo.content_id}">{booticon ipackage="icons" iname="icon-user-delete" iexplain="delete"}</a>
      {/if}
  {/if} {* end print_page *}
</div> {*end .floaticon *}
<div class="date">
	{tr}Created by{/tr} {displayname user=$gContent->mInfo.creator_user user_id=$gContent->mInfo.user_id real_name=$gContent->mInfo.creator_real_name}, {tr}Last modification by{/tr} {displayname user=$gContent->mInfo.modifier_user user_id=$gContent->mInfo.modifier_user_id real_name=$gContent->mInfo.modifier_real_name} on {$gContent->mInfo.last_modified|bit_long_datetime}
</div>
