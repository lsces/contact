{strip}
<div class="row">
	{formlabel label="`$xrefInfo.template_title` Number" for="xkey"}
	{forminput}
		<input type="text" name="xkey" id="xkey" value="{$xrefInfo.xkey|escape}" />
		{formhelp note="Telephone number. Use (+xx) for country code and include leading zero."}
	{/forminput}
</div>
{/strip}
