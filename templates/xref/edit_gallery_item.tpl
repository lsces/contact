{strip}
<div class="edit contact">
	<div class="header">
		<h1>{tr}Edit{/tr} {$xrefInfo.xref_title|escape}: {$gContent->getTitle()|escape}</h1>
	</div>
	<div class="body">
		{formfeedback error=$errors}
		{form id="editXrefForm"}
			<input type="hidden" name="content_id" value="{$xrefInfo.content_id|escape}" />
			<input type="hidden" name="xref_id"    value="{$xrefInfo.xref_id|escape}" />
			<input type="hidden" name="item"       value="{$xrefInfo.item|escape}" />

			<div class="form-group">
				{formlabel label="Gallery Content ID" for="xref"}
				{forminput}
					<input type="text" class="form-control input-small" name="xref" id="xref" value="{$xrefInfo.xref|escape}" />
					{formhelp note="The content_id of the FisheyeGallery this contact's own gallery grid should show - found on that gallery's own edit page."}
				{/forminput}
			</div>

			<div class="form-group">
				{formlabel label="Note" for="edit"}
				{forminput}
					<input type="text" class="form-control" name="edit" id="edit" value="{$xrefInfo.data|escape}" />
				{/forminput}
			</div>

			<div class="form-group submit">
				<input type="submit" class="btn btn-default" name="fCancel"   value="{tr}Cancel{/tr}" />
				<input type="submit" class="btn btn-primary" name="fSaveXref" value="{tr}Save{/tr}" />
			</div>
		{/form}
	</div>
</div>
{/strip}
