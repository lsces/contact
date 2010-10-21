{strip}
<div class="floaticon">{bithelp}</div>
<div class="edit contact_xref">
	<div class="header">
		<h1>{tr}Edit Contact Xref{/tr}: {$title|escape}-{$xref_title|escape}</h1>
	</div>

	{formfeedback hash=$feedback}
	{formfeedback warning=`$errors.title`}

	<div class="body">
		{form enctype="multipart/form-data" id="writexref"}
			<input type="hidden" name="content_id" value="{$xrefInfo.content_id}" />
			<input type="hidden" name="xref_id" value="{$xrefInfo.xref_id}" />

			{jstabs}
				{jstab title="Reference Details"}
					{legend legend="XRef Contents"}
						<div class="row">
							{formlabel label="Cross Reference Link" for="xref"}
							{forminput}
								<input type="text" name="xref" id="xref" value="{$xrefInfo.xref|escape}" />
								{formhelp note="Link to other contact/content entries."}
							{/forminput}
						</div>
		
						<div class="row">
							{formlabel label="Reference Key" for="xkey"}
							{forminput}
								<input type="text" name="xkey" id="xkey" value="{$xrefInfo.xkey|escape}" />
								{formhelp note="ID Key use to access data in other systems identified by the xref type."}
							{/forminput}
						</div>
		
						<div class="row">
							{formlabel label="Reference Text" for="xkey_ext"}
							{forminput}
								<input type="text" name="xkey_ext" id="xkey_ext" value="{$xrefInfo.xkey_ext|escape}" />
								{formhelp note="Variable text element such as url or email address."}
							{/forminput}
						</div>
		
						{formlabel label="Reference Notes" for="data"}
						{capture assign=textarea_help}
							{tr}Keep the text attached to reference items short and use comment records to add larger volumns of text. This should be reserved for simple notes such 'as use after 5PM' or the link.{/tr}
						{/capture}
						{textarea rows=5 noformat=1}{$xrefInfo.data}{/textarea}
					{/legend}
				{/jstab}

				{jstab title="Time period"}
					{legend legend="Start and Stop Dates"}
						<div class="row">
							<input type="hidden" name="startDateInput" value="1" />
							&nbsp;Ignore Date <input type="checkbox" name="ignore_start_date" {if $xrefInfo.ignore_start_date eq "y"}checked{/if} />
							{formlabel label="Start Date" for=""}
							{forminput}
								{html_select_date prefix="start_" time=$xrefInfo.start_date start_year="-5" end_year="+10"} {tr}at{/tr}&nbsp;
								<span dir="ltr">{html_select_time prefix="start_" time=$xrefInfo.start_date display_seconds=false}&nbsp;{$siteTimeZone}</span>
								{formhelp note="This xref record becomes valid on this date."}
							{/forminput}
						</div>
		
						<div class="row">
							<input type="hidden" name="endDateInput" value="1" />
							&nbsp;Ignore Date <input type="checkbox" name="ignore_end_date" {if $xrefInfo.ignore_end_date eq "y"}checked{/if} />
							{formlabel label="End Date" for=""}
							{forminput}
								{html_select_date prefix="end_" time=$xrefInfo.end_date start_year="-5" end_year="+10"} {tr}at{/tr}&nbsp;
								<span dir="ltr">{html_select_time prefix="end_" time=$xrefInfo.end_date display_seconds=false}&nbsp;{$siteTimeZone}</span>
								{formhelp note="This xref record finishes on this date."}
							{/forminput}
						</div>
					{/legend}
				{/jstab}
			
				<div class="row submit">
					<input type="submit" name="fCancel" value="{tr}Cancel{/tr}" />&nbsp;
					<input type="submit" name="fSaveXref" value="{tr}Save{/tr}" />
				</div>
			{/jstabs}
		{/form}
	</div><!-- end .body -->
</div><!-- end .article -->
{/strip}
