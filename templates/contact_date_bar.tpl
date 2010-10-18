<div class="floaticon">
  {if $lock}
    {biticon ipackage="icons" iname="locked" iexplain="locked"}{$info.editor|userlink}
  {/if}
  {if $print_page ne 'y'}
    {if !$lock}
      {if $gBitUser->hasPermission('p_edit_contact')}
		{smartlink ititle="Add additional crossref record" ifile="add_xref.php" ibiticon="icons/bookmark-new" content_id=$pageInfo.content_id xref_type=-1}
		{smartlink ititle="Edit Contact" ifile="edit.php" ibiticon="icons/accessories-text-editor" content_id=$pageInfo.content_id}
      {/if}
    {/if}
    <a title="{tr}print{/tr}" href="print.php?content_id={$pageInfo.content_id}">{biticon ipackage="icons" iname="document-print" iexplain="print"}</a>
      {if $gBitUser->hasPermission('p_remove_contact')}
        <a title="{tr}remove this contact{/tr}" href="remove_contact.php?content_id={$pageInfo.content_id}">{biticon ipackage="icons" iname="edit-delete" iexplain="delete"}</a>
      {/if}
  {/if} {* end print_page *}
</div> {*end .floaticon *}
<div class="date">
	{tr}Created by{/tr} {displayname user=$pageInfo.creator_user user_id=$pageInfo.user_id real_name=$pageInfo.creator_real_name}, {tr}Last modification by{/tr} {displayname user=$pageInfo.modifier_user user_id=$pageInfo.modifier_user_id real_name=$pageInfo.modifier_real_name} on {$pageInfo.last_modified|bit_long_datetime}
</div>
