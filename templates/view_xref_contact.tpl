{strip}
<div class="view contact_xref">
	<div class="header">
		<h1>{$xrefInfo.template_title|escape}: {$xref_title|escape} for {$title|escape}</h1>
	</div>

	<div class="body">
		{legend legend="Content"}
			<div class="row">
				{formlabel label="`$xrefInfo.template_title` Phone Number" for="xkey"}
				{forminput}
					{if $xrefInfo.xkey }
						{$xrefInfo.xkey|escape}
					{else}
						Not yet set
					{/if}
					{formhelp note="Phone number for `$xrefInfo.template_title` type of xref record."}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="`$xrefInfo.template_title` Name" for="xkey_ext"}
				{forminput}
					{if $xrefInfo.xkey_ext }
						{$xrefInfo.xkey_ext|escape}
					{else}
						Not yet set
					{/if}
					{formhelp note="Name for `$xrefInfo.template_title` type of xref record."}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="`$xrefInfo.template_title` Notes" for="data"}
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
						{$xrefInfo.start_date|bit_long_datetime}
					{/if}
					{formhelp note="This key seal code becomes valid on this date."}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="End Date" for=""}
				{forminput}
					{if $xrefInfo.ignore_end_date eq "y"}
						No end date set
					{else}
						{$xrefInfo.end_date|bit_long_datetime}
					{/if}
					{formhelp note="This key seal code finishes on this date."}
				{/forminput}
			</div>
		{/legend}
		<a class="item" href="{$smarty.const.CONTACT_PKG_URL}index.php?content_id={$xrefInfo.content_id}">{tr}Return{/tr}</a>
	</div><!-- end .body -->
</div><!-- end .article -->
{/strip}
