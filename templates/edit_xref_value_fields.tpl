{strip}
<div class="row">
	{formlabel label="`$xrefInfo.template_title` Value" for="`$output`xkey_ext"}
	{forminput}
		<input type="text" name="{$output}xkey_ext" id="xkey_ext" value="{$xrefInfo.xkey_ext|escape}" />
		{formhelp note="Numeric values such as distaces and counts."}
	{/forminput}
</div>
{/strip}
