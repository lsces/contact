{strip}
<div class="view contact_xref">
	<div class="header">
		<h1>{tr}Location{/tr}: {$xref_title|escape} for {$title|escape}</h1>
	</div>

	<div class="body">
		{legend legend="Contents"}
			<div class="form-group">
				{formlabel label="Geographic Location" for="xref"}
				{forminput}
					<a class="item" href="http://www.openstreetmap.org/?lat={$xrefInfo.xkey_ext}&lon={$xrefInfo.xkey}&zoom=15&layers=MN" target="_blank">{tr}OpenStreetMap Link{/tr}</a>
					{formhelp note="Use one of the graphical tools like multimap or google maps to identify actual co-ordinates."}
				{/forminput}
				<div class="clear"></div>
			</div>

			<div class="form-group">
				{formlabel label="Easting" for="xkey"}
				{forminput}
					{if $xrefInfo.xkey }
						{$xrefInfo.xkey|escape}
					{else}
						Not yet set
					{/if}
					{formhelp note="Easting for location (x_coord - longitude)."}
				{/forminput}
				<div class="clear"></div>
			</div>

			<div class="form-group">
				{formlabel label="Northing" for="xkey_ext"}
				{forminput}
					{if $xrefInfo.xkey_ext }
						{$xrefInfo.xkey_ext|escape}
					{else}
						Not yet set
					{/if}
					{formhelp note="Northing for location (y_coord - latitude)."}
				{/forminput}
				<div class="clear"></div>
			</div>

			<div class="form-group">
				{formlabel label="Location Directions" for="data"}
				{forminput}
					{$xrefInfo.data|escape}
					{formhelp note="Directions to assist finding the actual location where site is not accessable via the postcode."}
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
