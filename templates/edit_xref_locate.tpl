{strip}
<div class="floaticon">{bithelp}</div>
<div class="edit contact_xref">
	<div class="header">
		<h1>{tr}Edit Location{/tr}: {$title|escape}-{$xref_title|escape}</h1>
	</div>

	{formfeedback hash=$feedback}
	{formfeedback warning=$errors.title}

	<div class="body">
		{form enctype="multipart/form-data" id="writexref"}
			<input type="hidden" name="content_id" value="{$xrefInfo.content_id}" />
			<input type="hidden" name="xref_id" value="{$xrefInfo.xref_id}" />

			{jstabs}
				{jstab title="Location Details"}
					{legend legend="Contents"}
						<div class="form-group">
							{formlabel label="Geographic Location" for="xref"}
							{forminput}
								<a class="item" href="http://www.openstreetmap.org/?mlat={$xrefInfo.xkey_ext}&mlon={$xrefInfo.xkey}&zoom=17&layers=MN" target="_blank">{tr}OpenStreetMap Link{/tr}</a>
								{formhelp note="Use one of the graphical tools like multimap or google maps to identify actual co-ordinates."}
							{/forminput}
							<div class="clear"></div>
						</div>

						<div class="form-group">
							{formlabel label="Easting/Longitude" for="xkey"}
							{forminput}
								<input type="text" name="xkey" id="xkey" value="{$xrefInfo.xkey|escape}" />
								{formhelp note="Longitude for the location."}
							{/forminput}
							<div class="clear"></div>
						</div>
		
						<div class="form-group">
							{formlabel label="Northing/Latitude" for="xkey_ext"}
							{forminput}
								<input type="text" name="xkey_ext" id="xkey_ext" value="{$xrefInfo.xkey_ext|escape}" />
								{formhelp note="Latitude for the location."}
							{/forminput}
							<div class="clear"></div>
						</div>
		
						{formlabel label="Location Directions" for="data"}
						{capture assign=textarea_help}
							{tr}Directions to assist finding the actual location where site is not accessable via the postcode.{/tr}
						{/capture}
						{textarea rows=5 noformat=1 edit=$xrefInfo.data}
					{/legend}
				{/jstab}

				{jstab title="Time period"}
					{legend legend="Start and Stop Dates"}
						<div class="form-group">
							<input type="hidden" name="startDateInput" value="1" />
							&nbsp;Ignore Date <input type="checkbox" name="ignore_start_date" {if $xrefInfo.ignore_start_date eq "y"}checked{/if} />
							{formlabel label="Start Date" for=""}
							{forminput}
								{html_select_date prefix="start_" time=$xrefInfo.start_date start_year="-5" end_year="+10"} {tr}at{/tr}&nbsp;
								<span dir="ltr">{html_select_time prefix="start_" time=$xrefInfo.start_date display_seconds=false}&nbsp;{$siteTimeZone}</span>
								{formhelp note="This address becomes valid on this date."}
							{/forminput}
							<div class="clear"></div>
						</div>
		
						<div class="form-group">
							<input type="hidden" name="endDateInput" value="1" />
							&nbsp;Ignore Date <input type="checkbox" name="ignore_end_date" {if $xrefInfo.ignore_end_date eq "y"}checked{/if} />
							{formlabel label="End Date" for=""}
							{forminput}
								{html_select_date prefix="end_" time=$xrefInfo.end_date start_year="-5" end_year="+10"} {tr}at{/tr}&nbsp;
								<span dir="ltr">{html_select_time prefix="end_" time=$xrefInfo.end_date display_seconds=false}&nbsp;{$siteTimeZone}</span>
								{formhelp note="This address finishes on this date."}
							{/forminput}
							<div class="clear"></div>
						</div>
					{/legend}
				{/jstab}
			
				<div class="form-group submit">
					<input type="submit" name="fCancel" value="{tr}Cancel{/tr}" />&nbsp;
					<input type="submit" name="fSaveXref" value="{tr}Save{/tr}" />
				</div>
			{/jstabs}
		{/form}
	</div><!-- end .body -->
</div><!-- end .article -->
{/strip}
