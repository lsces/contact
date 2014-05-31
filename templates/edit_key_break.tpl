{strip}
<div class="floaticon">{bithelp}</div>
<div class="edit contact_xref">
	<div class="header">
		<h1>{tr}Edit Contact Xref{/tr}: {$title|escape}-{$xref_title|escape}</h1>
	</div>

	{formfeedback hash=$feedback}
	{formfeedback warning=$errors.title}

	<div class="body">
		{form enctype="multipart/form-data" id="writexref"}
			<input type="hidden" name="content_id" value="{$xrefInfo.content_id}" />
			<input type="hidden" name="xref_id" value="{$xrefInfo.xref_id}" />

			{legend legend="Break Seal Contents"}
				<div class="row">
					{formlabel label="Seal Break Job Reference" for="xref"}
					{forminput}
						<input type="text" name="xref" id="xref" value="{$xrefInfo.xref|escape}" />
						{formhelp note="Link to job that caused seal break."}
					{/forminput}
					<div class="clear"></div>
				</div>

				<div class="row">
					{formlabel label="Reference Key" for="xkey"}
					{forminput}
						<input type="text" name="xkey" id="xkey" value="{$xrefInfo.xkey|escape}" />
						{formhelp note="ID Key use to access data in other systems identified by the xref type."}
					{/forminput}
					<div class="clear"></div>
				</div>

				<div class="row">
					{formlabel label="Reference Text" for="xkey_ext"}
					{forminput}
						<input type="text" name="xkey_ext" id="xkey_ext" value="{$xrefInfo.xkey_ext|escape}" />
						{formhelp note="Variable text element such as url or email address."}
					{/forminput}
					<div class="clear"></div>
				</div>

				{formlabel label="Reference Notes" for="data"}
				{capture assign=textarea_help}
					{tr}Keep the text attached to reference items short and use comment records to add larger volumns of text. This should be reserved for simple notes such 'as use after 5PM' or the link.{/tr}
				{/capture}
				{textarea rows=5 noformat=1 edit=$xrefInfo.data}
			{/legend}
				<div class="row submit">
					<input type="submit" name="fCancel" value="{tr}Cancel{/tr}" />&nbsp;
					<input type="submit" name="fSaveXref" value="{tr}Save{/tr}" />
				</div>
		{/form}
	</div><!-- end .body -->
</div><!-- end .article -->
{/strip}
