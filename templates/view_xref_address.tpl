{strip}
<div class="edit contact_xref">
	<div class="header">
		<h1>{$xrefInfo.template_title|escape}: {$xref_title|escape} for {$title|escape}</h1>
	</div>

	<div class="body">
		{legend legend="Contents"}
			<div class="form-group">
				{formlabel label="House Name/Number" for="xkey_ext"}
				{forminput}
					{if $xrefInfo.xkey_ext }
						{$xrefInfo.xkey_ext|escape}
					{else}
						Not yet set
					{/if}
					{formhelp note="House name and/or number when used with postcode, or free format full address."}
				{/forminput}
				<div class="clear"></div>
			</div>

			<div class="form-group">
				{formlabel label="Postcode" for="xkey"}
				{forminput}
					{if $xrefInfo.xkey }
						{$xrefInfo.xkey|escape}
					{else}
						Not yet set
					{/if}
					{formhelp note="Postcode for address."}
				{/forminput}
				<div class="clear"></div>
			</div>

			<div class="form-group">
				{formlabel label="`$xrefInfo.template_title` Notes" for="data"}
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
					{formhelp note="This address record becomes valid on this date."}
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
					{formhelp note="This address record finishes on this date."}
				{/forminput}
				<div class="clear"></div>
			</div>
		{/legend}
		<a class="item" href="{$smarty.const.CONTACT_PKG_URL}index.php?content_id={$xrefInfo.content_id}">{tr}Return{/tr}</a>
	</div><!-- end .body -->
</div><!-- end .article -->
{/strip}
