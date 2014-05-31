{strip}
<div class="row">
	{formlabel label="`$xrefInfo.template_title` Phone Number" for="`$output`xkey"}
	{forminput}
		<input type="text" name="{$output}xkey" id="xkey" value="{$xrefInfo.xkey|escape}" />
		{formhelp note="Phone number for `$xrefInfo.template_title` type of xref record."}
	{/forminput}
	<div class="clear"></div>
</div>

<div class="row">
	{formlabel label="`$xrefInfo.template_title` Name" for="`$output`xkey_ext"}
	{forminput}
		<input type="text" name="{$output}xkey_ext" id="xkey_ext" value="{$xrefInfo.xkey_ext|escape}" />
		{formhelp note="Name for `$xrefInfo.template_title` type of xref record."}
	{/forminput}
	<div class="clear"></div>
</div>
{/strip}
