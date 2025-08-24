<div class="form-group">
	{formlabel label="Content Types" for=content_types}
	{forminput}
		{if isset( $gContent->mInfo.contact_types ) }
			{foreach from=$gContent->mInfo.contact_types key=type_id item=type}
				<input type="checkbox" name="contact_types[{$type_id}]" value={$type.source} {if isset($type.content_id) } checked="checked"{/if} /> {$type.cross_ref_title}<br/>
			{/foreach}
		{else}
			{foreach from=$gContent->mInfo.contact_type_list key=type_id item=type}
				<input type="checkbox" name="contact_types[$type_id]" value={$type.source} />{$type.name}<br/>
			{/foreach}
		{/if}
	{/forminput}
	{formhelp note=""}
	<div class="clear"></div>
</div>

