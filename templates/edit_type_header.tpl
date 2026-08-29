<div class="form-group">
	{formlabel label="Contact Types"}
	{forminput}
		{* Marks this section as genuinely submitted, even with every box unchecked - an
		   all-unchecked checkbox group sends no contact_types[] key at all, which is
		   otherwise indistinguishable from a caller that doesn't touch type tags at all
		   (Contact::store() needs to tell those two cases apart). *}
		<input type="hidden" name="fContactTypesSubmitted" value="1" />
		{foreach from=$gContent->mInfo.contact_type_list item=type}
			<label class="checkbox-inline">
				<input type="checkbox" name="contact_types[]" value="{$type.item|escape}"{if $type.checked} checked="checked"{/if} /> {$type.name|escape}
			</label>
		{/foreach}
	{/forminput}
	<div class="clear"></div>
</div>

