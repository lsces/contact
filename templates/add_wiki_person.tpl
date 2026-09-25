{strip}
<div class="edit contact">
	<div class="header">
		<h1>{tr}Add Wiki Individual{/tr}</h1>
	</div>

	<div class="body">
		{formfeedback error=$errors}

		{form id="wikiFetchForm"}
			<div class="form-group">
				{formlabel label="Wikidata ID or URL" for="wikidata_input"}
				{forminput}
					<input type="text" class="form-control" name="wikidata_input" id="wikidata_input" placeholder="Q185165 or https://www.wikidata.org/wiki/Q185165" value="{$smarty.request.wikidata_input|escape}" />
				{/forminput}
			</div>
			<div class="form-group submit">
				<input type="submit" class="btn btn-secondary" name="fFetchWikidata" value="{tr}Fetch{/tr}" />
			</div>
		{/form}

		{form id="addWikiPersonForm"}
			{* Contact::store()'s type-marker write is gated on this - see edit_type_header.tpl's
			   own copy for why it's needed even alongside a real checkbox picker below. *}
			<input type="hidden" name="fContactTypesSubmitted" value="1" />
			<input type="hidden" name="wikidata_qid" value="{$wikiQid|escape}" />
			<input type="hidden" name="wikidata_raw" value="{$wikiRawJson|escape}" />
			<input type="hidden" name="dob" value="{$wikiDob|escape}" />
			<input type="hidden" name="dod" value="{$wikiDod|escape}" />
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
			{if $wikiDob}
				<div class="form-group">
					<p>{tr}Date of birth{/tr}: {$wikiDob|escape}</p>
				</div>
			{/if}
			{if $wikiDod}
				<div class="form-group">
					<p>{tr}Date of death{/tr}: {$wikiDod|escape}</p>
				</div>
			{/if}

			<div class="form-group">
				{formlabel label="Title" for="prefix"}
				{forminput}
					<input type="text" class="form-control input-small" name="prefix" id="prefix" value="{$smarty.request.prefix|escape}" placeholder="Mr / Mrs / Dr …" />
				{/forminput}
			</div>

			<div class="form-group">
				{formlabel label="Forename" for="forename"}
				{forminput}
					<input type="text" class="form-control" name="forename" id="forename" value="{$smarty.request.forename|escape}" />
				{/forminput}
			</div>

			<div class="form-group">
				{formlabel label="Surname" for="surname"}
				{forminput}
					<input type="text" class="form-control" name="surname" id="surname" value="{$smarty.request.surname|escape}" />
				{/forminput}
			</div>

			<div class="form-group">
				{formlabel label="Suffix" for="suffix"}
				{forminput}
					<input type="text" class="form-control input-small" name="suffix" id="suffix" value="{$smarty.request.suffix|escape}" />
				{/forminput}
			</div>

			{if $personTypes}
			<div class="form-group">
				{formlabel label="Role"}
				{forminput}
					{foreach from=$personTypes item=type}
						<label class="checkbox">
							<input type="checkbox" name="contact_types[]" value="{$type.item|escape}"{if $wikiSuggestedTypes[$type.item]} checked="checked"{/if} /> {$type.name|escape}
						</label>
					{/foreach}
				{/forminput}
			</div>
			{/if}

			<div class="form-group">
				{formlabel label="Note" for="edit"}
				{forminput}
					<input type="text" class="form-control" name="edit" id="edit" value="{$smarty.request.edit|escape}" />
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
