{* Fields for a 'locate' item: a map position (xkey = longitude/easting, xkey_ext = latitude/northing). *}
{strip}
{assign var=xid value=$xid|default:''}
{if $xrefInfo.xkey ne '' and $xrefInfo.xkey_ext ne ''}
<div class="form-group">
	{formlabel label="Geographic Location"}
	{forminput}
		<a class="item" href="http://www.openstreetmap.org/?mlat={$xrefInfo.xkey_ext|escape}&amp;mlon={$xrefInfo.xkey|escape}&amp;zoom=17&amp;layers=MN" target="_blank">{tr}OpenStreetMap Link{/tr}</a>
		{formhelp note="Use one of the graphical tools like multimap or google maps to identify actual co-ordinates."}
	{/forminput}
</div>
{/if}

<div class="form-group">
	{formlabel label="Easting/Longitude" for="`$xid`xkey"}
	{forminput}
		<input type="text" class="form-control" name="xkey" id="{$xid}xkey" value="{$xrefInfo.xkey|escape}" />
		{formhelp note="Longitude for the location."}
	{/forminput}
</div>

<div class="form-group">
	{formlabel label="Northing/Latitude" for="`$xid`xkey_ext"}
	{forminput}
		<input type="text" class="form-control" name="xkey_ext" id="{$xid}xkey_ext" value="{$xrefInfo.xkey_ext|escape}" />
		{formhelp note="Latitude for the location."}
	{/forminput}
</div>

<div class="form-group">
	{formlabel label="Location Directions" for="`$xid`edit"}
	{forminput}
		<textarea class="form-control" name="edit" id="{$xid}edit" rows="4">{$xrefInfo.data|escape}</textarea>
		{formhelp note="Directions to assist finding the actual location where site is not accessible via the postcode."}
	{/forminput}
</div>
{/strip}
