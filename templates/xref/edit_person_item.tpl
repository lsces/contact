{strip}
<div class="edit contact_xref">
	<div class="floaticon">{bithelp}</div>
	<div class="header">
		<h1>{tr}Edit{/tr} {$xrefInfo.template_title|escape}: {$gContent->getTitle()|escape}</h1>
	</div>

	<div class="body">
		{formfeedback error=$errors}

		{form id="editXrefForm"}
			<input type="hidden" name="content_id" value="{$xrefInfo.content_id|escape}" />
			<input type="hidden" name="xref_id"    value="{$xrefInfo.xref_id|escape}" />
			<input type="hidden" name="item"       value="{$xrefInfo.item|escape}" />

			{jstabs}
				{jstab title="Details"}
					{legend legend="Contents"}
						{if $xrefInfo.xref}
						<div class="form-group">
							{formlabel label="Person"}
							{forminput}
								<p class="form-control-static">
									{smartlink ititle=$xrefInfo.linked_title|default:$xrefInfo.xref ifile="view.php" content_id=$xrefInfo.xref}
								</p>
								{formhelp note="Not editable here — delete this record and add a new one from the list to link a different person."}
							{/forminput}
						</div>
						{/if}

						<div class="form-group">
							{formlabel label="Notes" for="edit"}
							{forminput}
								<textarea class="form-control" name="edit" id="edit" rows="4">{$xrefInfo.data|escape}</textarea>
							{/forminput}
						</div>
					{/legend}
				{/jstab}

				{include file="bitpackage:liberty/edit_xref_dates.tpl"}
			{/jstabs}

			<div class="form-group submit">
				<input type="submit" class="btn btn-default" name="fCancel"   value="{tr}Cancel{/tr}" />
				<input type="submit" class="btn btn-primary" name="fSaveXref" value="{tr}Save{/tr}" />
			</div>
		{/form}
	</div><!-- end .body -->
</div><!-- end .contact_xref -->
{/strip}
