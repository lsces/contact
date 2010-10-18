<div class="row">
	{formlabel label="Content Types" for=content_types}
	{forminput}
		{foreach from=$pageInfo.contact_types key=type_id item=type}
			{if isset($type.content_id) }{$type.cross_ref_title}<br/> {/if}
		{/foreach}
	{/forminput}
</div>

