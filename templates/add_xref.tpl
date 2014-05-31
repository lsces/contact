{literal}
<script type="text/javascript">//<![CDATA[
function updateContactXrefFormat() { {/literal}
{foreach from=$xrefInfo.xref_format_list key=feature item=output}
	document.getElementById('{$output}-format').style.display = 'none';
{/foreach} {literal}
	var form = document.getElementById('editContactXrefForm');
	var input = form.source;
    var i  = input.selectedIndex; 
    var select = document.getElementById('format-'+input.options[i].value).value;
	document.getElementById(select+'-format').style.display = 'block';
}
//]]></script>
{/literal}
{strip}
<div class="floaticon">{bithelp}</div>
<div class="edit contact_xref">
	<div class="header">
		<h1>{tr}Add Contact Xref{/tr}: {$title|escape}</h1>
	</div>

	{formfeedback hash=$feedback}
	{formfeedback warning=$errors.title}

	<div class="body">
		{form enctype="multipart/form-data" id="editContactXrefForm"}
			<input type="hidden" name="content_id" value="{$xrefInfo.content_id}" />
			<input type="hidden" name="xref_type" value="{$xrefInfo.xref_type}" />
			{foreach from=$xrefInfo.xref_type_list.type key=feature item=output}
				<input type="hidden" id="format-{$feature}" name="format-{$feature}" value="{$output}" />
			{/foreach}

			{jstabs}
				{jstab title="Reference Details"}
					{legend legend="XRef Contents"}
						{formfeedback error=$errors warning=$contactWarnings success=$contactSuccess}
						
						<div class="row">
							{formlabel label="Reference Type" for="source"}
							{forminput}
								{html_options name="source" id="source" options=$xrefInfo.xref_type_list.list selected=$smarty.const.CONTACT_FORMAT_GENERIC onchange="updateContactXrefFormat();}
								{formhelp note="Select type of reference information to add"}
							{/forminput}
						</div>

						{foreach from=$xrefInfo.xref_format_list key=feature item=output}
							<div id="{$output}-format">
								{include file="bitpackage:contact/edit_xref_`$output`_fields.tpl"}
							</div>
						{/foreach}

						{formlabel label="Additional Notes" for="data"}
						{capture assign=textarea_help}
							{tr}Keep the text attached to reference items short and use comment records to add larger volumns of text. This should be reserved for simple notes such 'as use after 5PM' or the link.{/tr}
						{/capture}
						{textarea rows=5 noformat=1 edit=$xrefInfo.data}
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
