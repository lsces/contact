{strip}
<div class="edit contact">
	<div class="header">
		<h1>{tr}Add Person{/tr}</h1>
	</div>

	<div class="body">
		{formfeedback error=$errors}

		{form id="addPersonForm"}
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
