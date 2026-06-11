<div class="form-group">
	{formlabel label="{if $isPerson}Personal Contact{else}Business Contact{/if}"}
	{forminput}
		{foreach from=$gContent->mInfo.contact_types key=type_id item=type}
			{if isset($type.content_id) && $type.item neq 'P01'}{$type.cross_ref_title}<br/>{/if}
		{/foreach}
	{/forminput}
	<div class="clear"></div>
</div>

