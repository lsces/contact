{strip}
{form legend="Contact List Features"}
	<input type="hidden" name="page" value="{$page}" />

	{foreach from=$formContactListFeatures key=item item=output}
		<div class="form-group">
			{formlabel label=$output.label for=$item}
			{forminput}
				{html_checkboxes name="$item" values="y" checked=$gBitSystem->getConfig($item) labels=false id=$item}
			{/forminput}
			{formhelp note=$output.help page=$output.page}
		</div>
	{/foreach}

		<div class="form-group">
			{formlabel label="Default Contact Types"}
			{forminput}
				{html_checkboxes name="defaultTypes" options=$contactTypeDefaults selected=$contactTypesSelected separator="<br />"}
				{formhelp note="Default contact types to show on the contact filter when users do not have permission to change types of content viewed."}
			{/forminput}
		</div>

	<div class="form-group submit">
		<input type="submit" name="contactlistfeatures" value="{tr}Change preferences{/tr}" />
	</div>
{/form}
{/strip}