{strip}
<div class="row">
	{formlabel label="Key Seal Code" for="`$output`xkey"}
	{forminput}
		<input type="text" name="{$output}xkey" id="xkey" value="{$xrefInfo.xkey|escape}" />
		{formhelp note="Key Seal Code from key management system."}
	{/forminput}
</div>

<div class="row">
	{formlabel label="Contract Number" for="`$output`xkey_ext"}
	{forminput}
		<input type="text" name="{$output}xkey_ext" id="xkey_ext" value="{$xrefInfo.xkey_ext|escape}" />
		{formhelp note="Contract number."}
	{/forminput}
</div>
{/strip}
