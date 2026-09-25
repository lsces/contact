{strip}
<div class="edit contact">
	<div class="header">
		<h1>{tr}Add Business{/tr}</h1>
	</div>

	<div class="body">
		{formfeedback error=$errors}

		{form id="addBusinessForm"}
			{* Contact::store()'s type-marker write is gated on this - see edit_type_header.tpl's
			   own copy for why it's needed even alongside a real checkbox picker below. *}
			<input type="hidden" name="fContactTypesSubmitted" value="1" />
			<div class="form-group">
				{formlabel label="Organisation" for="organisation"}
				{forminput}
					<input type="text" class="form-control" name="organisation" id="organisation" value="{$smarty.request.organisation|escape}" />
				{/forminput}
			</div>

			{if $businessTypes}
			<div class="form-group">
				{formlabel label="Type"}
				{forminput}
					{foreach from=$businessTypes item=type}
						<label class="checkbox">
							<input type="checkbox" name="contact_types[]" value="{$type.item|escape}" /> {$type.name|escape}
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
