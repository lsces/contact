{strip}
<div class="view contact_xref">
	<div class="header">
		<h1>{tr}Contact Xref{/tr}: {$xref_title|escape} for {$title|escape}</h1>
	</div>

	<div class="body">
		{legend legend="XRef Contents"}
			<div class="form-group">
				{formlabel label="Cross Reference Link" for="xref"}
				{forminput}
					{if $xrefInfo.xref}
						{$xrefInfo.xref|escape}
					{else}
						&nbsp;
					{/if}
					{formhelp note="Link to other contact/content entries."}
				{/forminput}
				<div class="clear"></div>
			</div>

			<div class="form-group">
				{formlabel label="Reference Key" for="xkey"}
				{forminput}
					{if $xrefInfo.xkey }
						{$xrefInfo.xkey|escape}
					{else}
						&nbsp;
					{/if}
					{formhelp note="ID Key use to access data in other systems identified by the xref type."}
				{/forminput}
				<div class="clear"></div>
			</div>

			<div class="form-group">
				{formlabel label="Reference Text" for="xkey_ext"}
				{forminput}
					{if $xrefInfo.xkey_ext }
						{$xrefInfo.xkey_ext|escape}
					{else}
						&nbsp;
					{/if}
					{formhelp note="Variable text element such as url or email address."}
				{/forminput}
				<div class="clear"></div>
			</div>

			<div class="form-group">
				{formlabel label="Reference Notes" for="data"}
				{forminput}
					{$xrefInfo.data|escape}
					{formhelp note="Keep the text attached to reference items short and use comment records to add larger volumns of text. This should be reserved for simple notes such 'as use after 5PM' or the link."}
				{/forminput}
				<div class="clear"></div>
			</div>
		{/legend}

		{legend legend="Start and Stop Dates"}
			<div class="form-group">
				{formlabel label="Start Date" for=""}
				{forminput}
					{if $xrefInfo.ignore_start_date eq "y"}
						No start date set
					{else}
						{$xrefInfo.start_date|bit_long_datetime}
					{/if}
					{formhelp note="This xref record becomes valid on this date."}
				{/forminput}
				<div class="clear"></div>
			</div>

			<div class="form-group">
				{formlabel label="End Date" for=""}
				{forminput}
					{if $xrefInfo.ignore_end_date eq "y"}
						No end date set
					{else}
						{$xrefInfo.end_date|bit_long_datetime}
					{/if}
					{formhelp note="This xref record finishes on this date."}
				{/forminput}
				<div class="clear"></div>
			</div>
		{/legend}
		<a class="item" href="{$smarty.const.CONTACT_PKG_URL}index.php?content_id={$xrefInfo.content_id}">{tr}Return{/tr}</a>
	</div><!-- end .body -->
</div><!-- end .article -->
{/strip}
