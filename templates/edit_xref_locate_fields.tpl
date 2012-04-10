{strip}
<div class="row">
	{formlabel label="Geographic Location" for="`$output`xref"}
	{forminput}
		<a class="item" href="http://www.openstreetmap.org/?lat={$xrefInfo.xkey_ext}&lon={$xrefInfo.xkey}&zoom=15&layers=MN" target="_blank">{tr}OpenStreetMap Link{/tr}</a>
		{formhelp note="Use one of the graphical tools like multimap or google maps to identify actual co-ordinates."}
	{/forminput}
</div>

<div class="row">
	{formlabel label="Easting/Longitude" for="`$output`xkey"}
	{forminput}
		<input type="text" name="{$output}xkey" id="xkey" value="{$xrefInfo.xkey|escape}" />
		{formhelp note="Longitude for the location."}
	{/forminput}
</div>

<div class="row">
	{formlabel label="Northing/Latitude" for="`$output`xkey_ext"}
	{forminput}
		<input type="text" name="{$output}xkey_ext" id="xkey_ext" value="{$xrefInfo.xkey_ext|escape}" />
		{formhelp note="Latitude for the location."}
	{/forminput}
</div>
{/strip}
