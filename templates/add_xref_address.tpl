{strip}
<div class="floaticon">{bithelp}</div>
<div class="edit contact_xref">
	<div class="header">
		<h1>{tr}Add Address Details{/tr}: {$title|escape}</h1>
	</div>

	{formfeedback hash=$feedback}
	{formfeedback warning=$errors.title}

	<div class="body">
		{form enctype="multipart/form-data" id="writexref"}
			<input type="hidden" name="content_id" value="{$xrefInfo.content_id}" />
			<input type="hidden" name="xref_type" value="{$xrefInfo.xref_type}" />

			{jstabs}
				{jstab title="Address Details"}
					{legend legend="Contents"}
						<div class="form-group">
							{formlabel label="Address Type" for="source"}
							{forminput}
								{html_options name="$xrefInfo.xref_type_list[$xrefInfo.source]" options=$xrefInfo.xref_type_list selected=`$xrefInfo.source`}
								{formhelp note="Type of cross link reference to add."}
							{/forminput}
						</div>
		
						<div class="form-group">
							{formlabel label="House Name/Number" for="xkey_ext"}
							{forminput}
								<input type="text" name="xkey_ext" id="xkey_ext" value="{$xrefInfo.xkey_ext|escape}" />
								{formhelp note="House name and/or number when used with postcode, or free format full address."}
							{/forminput}
						</div>
		
						<div class="form-group">
							{formlabel label="Postcode" for="xkey"}
							{forminput}
								<input type="text" name="xkey" id="xkey" value="{$xrefInfo.xkey|escape}" />&nbsp;<input type="submit" name="fPostcode" value="{tr}Postcode Lookup{/tr}" />
								{formhelp note="Postcode for address."}
							{/forminput}
						</div>
		
						{formlabel label="`$xrefInfo.template_title` Notes" for="data"}
						{capture assign=textarea_help}
							{tr}Keep the text attached to reference items short and use comment records to add larger volumns of text. This should be reserved for simple notes such 'as use after 5PM' or the link.{/tr}
						{/capture}
						{textarea rows=5 noformat=1 edit=$xrefInfo.data}
					{/legend}
				{/jstab}

				{jstab title="Time period"}
					{legend legend="Start and Stop Dates"}
						<div class="form-group">
							<input type="hidden" name="startDateInput" value="1" />
							&nbsp;Ignore Date <input type="checkbox" name="ignore_start_date" />
							{formlabel label="Start Date" for=""}
							{forminput}
								{html_select_date prefix="start_" time=$xrefInfo.start_date start_year="-5" end_year="+10"} {tr}at{/tr}&nbsp;
								<span dir="ltr">{html_select_time prefix="start_" time=$xrefInfo.start_date display_seconds=false}&nbsp;{$siteTimeZone}</span>
								{formhelp note="This xref record becomes valid on this date."}
							{/forminput}
						</div>
		
						<div class="form-group">
							<input type="hidden" name="endDateInput" value="1" />
							&nbsp;Ignore Date <input type="checkbox" name="ignore_end_date" checked />
							{formlabel label="End Date" for=""}
							{forminput}
								{html_select_date prefix="end_" time=$xrefInfo.end_date start_year="-5" end_year="+10"} {tr}at{/tr}&nbsp;
								<span dir="ltr">{html_select_time prefix="end_" time=$xrefInfo.end_date display_seconds=false}&nbsp;{$siteTimeZone}</span>
								{formhelp note="This xref record finishes on this date."}
							{/forminput}
						</div>
					{/legend}
				{/jstab}
			
				<div class="form-group submit">
					<input type="submit" name="fCancel" value="{tr}Cancel{/tr}" />&nbsp;
					<input type="submit" name="fAddXref" value="{tr}Save{/tr}" />
				</div>
			{/jstabs}
		{/form}
	</div><!-- end .body -->
</div><!-- end .article -->
{/strip}
