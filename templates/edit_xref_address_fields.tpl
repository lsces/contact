{strip}
<div class="form-group">
	{formlabel label="House Name/Number" for="`$output`xkey_ext"}
	{forminput}
		<input type="text" name="{$output}xkey_ext" id="xkey_ext" value="{$xrefInfo.xkey_ext|escape}" />
		{formhelp note="House name and/or number when used with postcode, or free format full address."}
	{/forminput}
	<div class="clear"></div>
</div>

<div class="form-group">
	{formlabel label="Postcode" for="`$output`xkey"}
	{forminput}
		<input type="text" name="{$output}xkey" id="xkey" value="{$xrefInfo.xkey|escape}" />&nbsp;<input type="submit" name="fPostcode" value="{tr}Postcode Lookup{/tr}" />
		{formhelp note="Postcode for address."}
	{/forminput}
	<div class="clear"></div>
</div>
{/strip}
