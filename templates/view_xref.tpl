{strip}
<div class="edit contact_xref">
	<div class="header">
		<h1>{tr}Contact Xref{/tr}: {$xref_title|escape} for {$title|escape}</h1>
	</div>

	<div class="body">
		{legend legend="XRef Contents"}
			<div class="row">
				{formlabel label="Cross Reference Link" for="xref"}
				{forminput}
					{if $xrefInfo.xref}
						{$xrefInfo.xref|escape}
					{else}
						&nbsp;
					{/if}
					{formhelp note="Link to other contact/content entries."}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Reference Key" for="xkey"}
				{forminput}
					{if $xrefInfo.xkey }
						{$xrefInfo.xkey|escape}
					{else}
						&nbsp;
					{/if}
					{formhelp note="ID Key use to access data in other systems identified by the xref type."}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Reference Text" for="xkey_ext"}
				{forminput}
					{if $xrefInfo.xkey_ext }
						{$xrefInfo.xkey_ext|escape}
					{else}
						&nbsp;
					{/if}
					{formhelp note="Variable text element such as url or email address."}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Reference Notes" for="data"}
				{forminput}
					{$xrefInfo.data|escape}
					{formhelp note="Keep the text attached to reference items short and use comment records to add larger volumns of text. This should be reserved for simple notes such 'as use after 5PM' or the link."}
				{/forminput}
			</div>
		{/legend}

		{legend legend="Start and Stop Dates"}
			<div class="row">
				{formlabel label="Start Date" for=""}
				{forminput}
					{if $xrefInfo.ignore_start_date eq "y"}
						No start date set
					{else}
						{html_select_date prefix="start_" time=$xrefInfo.start_date start_year="-5" end_year="+10"} {tr}at{/tr}&nbsp;
						<span dir="ltr">{html_select_time prefix="start_" time=$xrefInfo.start_date display_seconds=false}&nbsp;{$siteTimeZone}</span>
					{/if}
					{formhelp note="This xref record becomes valid on this date."}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="End Date" for=""}
				{forminput}
					{if $xrefInfo.ignore_end_date eq "y"}
						No end date set
					{else}
						{html_select_date prefix="end_" time=$xrefInfo.end_date start_year="-5" end_year="+10"} {tr}at{/tr}&nbsp;
						<span dir="ltr">{html_select_time prefix="end_" time=$xrefInfo.end_date display_seconds=false}&nbsp;{$siteTimeZone}</span>
						{formhelp note="This xref record finishes on this date."}
					{/if}
				{/forminput}
			</div>
		{/legend}
	</div><!-- end .body -->
</div><!-- end .article -->
{/strip}
