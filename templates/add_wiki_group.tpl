{strip}
<div class="edit contact">
	<div class="header">
		<h1>{tr}Add Wiki Group{/tr}</h1>
	</div>

	<div class="body">
		{formfeedback error=$errors}

		{form id="wikiFetchForm"}
			<div class="form-group">
				{formlabel label="Wikidata ID or URL" for="wikidata_input"}
				{forminput}
					<input type="text" class="form-control" name="wikidata_input" id="wikidata_input" placeholder="Q106648 or https://www.wikidata.org/wiki/Q106648" value="{$smarty.request.wikidata_input|escape}" />
				{/forminput}
			</div>
			<div class="form-group submit">
				<input type="submit" class="btn btn-secondary" name="fFetchWikidata" value="{tr}Fetch{/tr}" />
			</div>
		{/form}

		{form id="addWikiGroupForm"}
			{* Contact::store()'s type-marker write is gated on this - see edit_type_header.tpl's
			   own copy for why it's needed even alongside a real checkbox picker below. *}
			<input type="hidden" name="fContactTypesSubmitted" value="1" />
			<input type="hidden" name="wikidata_qid" value="{$wikiQid|escape}" />
			<input type="hidden" name="formed" value="{$wikiFormed|escape}" />
			<input type="hidden" name="disbanded" value="{$wikiDisbanded|escape}" />
			<input type="hidden" name="wikidata_image" value="{$wikiImageFilename|escape}" />
			{foreach from=$wikiExternalIds key=item item=value}
				<input type="hidden" name="ext_{$item}" value="{$value|escape}" />
			{/foreach}

			{if $wikiSitelink}
				<div class="form-group">
					<p>{tr}Wikipedia{/tr}: <a href="{$wikiSitelink|escape}" target="_blank">{$wikiSitelink|escape}</a></p>
				</div>
			{/if}
			{if $wikiExternalIds}
				<div class="form-group">
					<p>{tr}Found external links{/tr}: {foreach from=$wikiExternalIds key=item item=value name=extlist}{$item|escape}{if !$smarty.foreach.extlist.last}, {/if}{/foreach}</p>
				</div>
			{/if}
			{if $wikiFormed}
				<div class="form-group">
					<p>{tr}Formed{/tr}: {$wikiFormed|escape}</p>
				</div>
			{/if}
			{if $wikiDisbanded}
				<div class="form-group">
					<p>{tr}Disbanded{/tr}: {$wikiDisbanded|escape}</p>
				</div>
			{/if}
			{if $wikiImagePreviewUrl}
				<div class="form-group">
					<p>{tr}Image found{/tr} ({$wikiImageFilename|escape}) - {tr}saved locally on Save{/tr}:</p>
					<img src="{$wikiImagePreviewUrl|escape}" alt="{$wikiImageFilename|escape}" style="max-height:150px;" />
				</div>
			{/if}

			<div class="form-group">
				{formlabel label="Organisation" for="organisation"}
				{forminput}
					<input type="text" class="form-control" name="organisation" id="organisation" value="{$smarty.request.organisation|escape}" />
				{/forminput}
			</div>

			{if $groupTypes}
			<div class="form-group">
				{formlabel label="Type"}
				{forminput}
					{foreach from=$groupTypes item=type}
						<label class="checkbox">
							<input type="checkbox" name="contact_types[]" value="{$type.item|escape}"{if $wikiSuggestedTypes[$type.item]} checked="checked"{/if} /> {$type.name|escape}
						</label>
					{/foreach}
				{/forminput}
			</div>
			{/if}

			<div class="form-group">
				{formlabel label="Note / Biography" for="edit"}
				{forminput}
					<textarea class="form-control" name="edit" id="edit" rows="6">{$smarty.request.edit|escape}</textarea>
				{/forminput}
			</div>

			<div class="form-group submit">
				<input type="submit" class="btn btn-default" name="fCancel"      value="{tr}Cancel{/tr}" />
				<input type="submit" class="btn btn-primary" name="fSaveContact" value="{tr}Save{/tr}" />
			</div>
		{/form}
	</div>
</div>
{/strip}
