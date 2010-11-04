<div class="row">
	{formlabel label="Content Types" for=content_types}
	{forminput}
		{if isset( $pageInfo.contact_types ) }
			{foreach from=$pageInfo.contact_types key=type_id item=type}
				<input type="checkbox" name="contact_types[{$type_id}]" value={$type.source} {if isset($type.content_id) } checked="checked"{/if} /> {$type.cross_ref_title}<br/>
			{/foreach}
		{else}
			{foreach from=$pageInfo.contact_type_list key=type_id item=type}
				<input type="checkbox" name="contact_types[$type_id]" value={$type.source} />{$type.name}<br/>
			{/foreach}
		{/if}
	{/forminput}
	{formhelp note=""}
</div>

