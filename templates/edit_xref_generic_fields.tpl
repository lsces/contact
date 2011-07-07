{strip}
<div class="row">
	{formlabel label="Cross Reference Link" for="xref"}
	{forminput}
		<input type="text" name="xref" id="xref" value="{$xrefInfo.xref|escape}" />
		{formhelp note="Link to other contact/content entries."}
	{/forminput}
</div>
<div class="row">
	{formlabel label="Reference Key" for="xkey"}
	{forminput}
		<input type="text" name="xkey" id="xkey" value="{$xrefInfo.xkey|escape}" />
		{formhelp note="ID Key use to access data in other systems identified by the xref type."}
	{/forminput}
</div>
<div class="row">
	{formlabel label="Reference Text" for="xkey_ext"}
	{forminput}
		<input type="text" name="xkey_ext" id="xkey_ext" value="{$xrefInfo.xkey_ext|escape}" />
		{formhelp note="Variable text element such as url or email address."}
	{/forminput}
</div>
{/strip}
