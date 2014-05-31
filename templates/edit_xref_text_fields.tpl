{strip}
<div class="row">
	{formlabel label="`$xrefInfo.template_title` Text" for="`$output`xkey_ext"}
	{forminput}
		<input type="text" name="{$output}xkey_ext" id="xkey_ext" value="{$xrefInfo.xkey_ext|escape}" />
		{formhelp note="Simple free format text entries like email or web addresses."}
	{/forminput}
	<div class="clear"></div>
</div>
{/strip}
