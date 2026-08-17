{strip}
<div class="edit contact_xref">
	<div class="floaticon">{bithelp}</div>
	<div class="header">
		<h1>{$xrefInfo.template_title|escape}: {$gContent->getTitle()|escape}</h1>
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
						<div class="form-group">
							{formlabel label="`$xrefInfo.template_title` Phone Number" for="xkey"}
							{forminput}
								<input type="text" class="form-control" name="xkey" id="xkey" value="{$xrefInfo.xkey|escape}" />
								{formhelp note="Phone number for `$xrefInfo.template_title` type of xref record."}
							{/forminput}
							<div class="clear"></div>
						</div>

						<div class="form-group">
							{formlabel label="`$xrefInfo.template_title` Name" for="xkey_ext"}
							{forminput}
								<input type="text" class="form-control" name="xkey_ext" id="xkey_ext" value="{$xrefInfo.xkey_ext|escape}" />
								{formhelp note="Name for `$xrefInfo.template_title` type of xref record."}
							{/forminput}
							<div class="clear"></div>
						</div>

						<div class="form-group">
							{formlabel label="`$xrefInfo.template_title` Notes" for="edit"}
							{forminput}
								<textarea class="form-control" name="edit" id="edit" rows="4">{$xrefInfo.data|escape}</textarea>
								{formhelp note="Keep the text attached to reference items short and use comment records to add larger volumes of text."}
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
