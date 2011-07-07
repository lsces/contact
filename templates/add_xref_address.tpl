{strip}
<div class="floaticon">{bithelp}</div>
<div class="edit contact_xref">
	<div class="header">
		<h1>{tr}Add Address Details{/tr}: {$title|escape}</h1>
	</div>

	{formfeedback hash=$feedback}
	{formfeedback warning=`$errors.title`}

	<div class="body">
		{form enctype="multipart/form-data" id="writexref"}
			<input type="hidden" name="content_id" value="{$xrefInfo.content_id}" />
			<input type="hidden" name="xref_type" value="{$xrefInfo.xref_type}" />

			{jstabs}
				{jstab title="Address Details"}
					{legend legend="Contents"}
						<div class="row">
							{formlabel label="Address Type" for="source"}
							{forminput}
								{html_options name="$xrefInfo.xref_type_list[$xrefInfo.source]" options=$xrefInfo.xref_type_list selected=`$xrefInfo.source`}
								{formhelp note="Type of cross link reference to add."}
							{/forminput}
						</div>

						{include file="bitpackage:contact/edit_xref_address_fields.tpl"}
		
						{formlabel label="`$xrefInfo.template_title` Notes" for="data"}
						{capture assign=textarea_help}
							{tr}Keep the text attached to reference items short and use comment records to add larger volumns of text. This should be reserved for simple notes such 'as use after 5PM' or the link.{/tr}
						{/capture}
						{textarea rows=5 noformat=1}{$xrefInfo.data}{/textarea}
					{/legend}
				{/jstab}

				{include file="bitpackage:contact/edit_xref_dates.tpl"}
			
				<div class="row submit">
					<input type="submit" name="fCancel" value="{tr}Cancel{/tr}" />&nbsp;
					<input type="submit" name="fAddXref" value="{tr}Save{/tr}" />
				</div>
			{/jstabs}
		{/form}
	</div><!-- end .body -->
</div><!-- end .article -->
{/strip}
