{strip}
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
			<div class="clear"></div>
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
			<div class="clear"></div>
		</div>
	{/legend}
{/jstab}
{/strip}
