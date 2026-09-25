{strip}
<div class="edit contact">
	<div class="header">
		<h1>{tr}Add Detail{/tr}: {$gContent->getTitle()|escape}</h1>
	</div>

	<div class="body">
		{formfeedback error=$errors}

		{form id="addXrefForm"}
			<input type="hidden" name="content_id" value="{$gContent->mContentId}" />
			<input type="hidden" name="group"  value="{$group}" />

			<div class="form-group">
				{formlabel label="Type" for="item"}
				{forminput}
					{html_options name="item" id="item" options=$xrefTypeList.list}
				{/forminput}
			</div>

			<div class="form-group">
				{formlabel label="Gallery Content ID" for="xref"}
				{forminput}
					<input type="text" class="form-control input-small" name="xref" id="xref" value="" />
					{formhelp note="The content_id of the FisheyeGallery this contact's own gallery grid should show - found on that gallery's own edit page."}
				{/forminput}
			</div>

			<div class="form-group">
				{formlabel label="Note" for="edit"}
				{forminput}
					<input type="text" class="form-control" name="edit" id="edit" value="" />
				{/forminput}
			</div>

			<div class="form-group submit">
				<input type="submit" class="btn btn-default" name="fCancel" value="{tr}Cancel{/tr}" />
				<input type="submit" class="btn btn-primary" name="fAddXref" value="{tr}Save{/tr}" />
			</div>
		{/form}
	</div><!-- end .body -->
</div>
{/strip}
