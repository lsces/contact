<div class="form-group">
	{formlabel label="Content Types" for=content_types}
	{forminput}
		{foreach from=$gContent->mInfo.contact_types key=type_id item=type}
			{if isset($type.content_id) }{$type.cross_ref_title}<br/> {/if}
		{/foreach}
	{/forminput}
	<div class="clear"></div>
</div>

