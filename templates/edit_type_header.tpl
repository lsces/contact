<div class="form-group">
	{formlabel label="Contact Types"}
	{forminput}
		{foreach from=$gContent->mInfo.contact_type_list item=type}
			<label class="checkbox-inline">
				<input type="checkbox" name="contact_types[]" value="{$type.item|escape}"{if $type.checked} checked="checked"{/if} /> {$type.name|escape}
			</label>
		{/foreach}
	{/forminput}
	<div class="clear"></div>
</div>

