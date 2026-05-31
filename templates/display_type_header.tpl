<div class="form-group">
	{formlabel label="{if $gContent->mInfo.contact_types.0.content_id}Personal Contact{else}Business Contact{/if}"}
	{forminput}
		{foreach from=$gContent->mInfo.contact_types key=type_id item=type}
			{if isset($type.content_id) && $type.item gt '$01'}{$type.cross_ref_title}<br/>{/if}
		{/foreach}
	{/forminput}
	<div class="clear"></div>
</div>

