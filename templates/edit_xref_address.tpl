{strip}
<div class="floaticon">{bithelp}</div>
<div class="edit contact_xref">
	<div class="header">
		<h1>{tr}Edit{/tr} {$xrefInfo.template_title|escape}: {$title|escape}-{$xref_title|escape}</h1>
	</div>

	{formfeedback hash=$feedback}
	{formfeedback warning=`$errors.title`}

	<div class="body">
		{form enctype="multipart/form-data" id="writexref"}
			<input type="hidden" name="content_id" value="{$xrefInfo.content_id}" />
			<input type="hidden" name="xref_id" value="{$xrefInfo.xref_id}" />

			{jstabs}
				{jstab title="Address Details"}
					{legend legend="Contents"}
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
					<input type="submit" name="fSaveXref" value="{tr}Save{/tr}" />
				</div>
			{/jstabs}
		{/form}
	</div><!-- end .body -->
</div><!-- end .article -->
{/strip}
