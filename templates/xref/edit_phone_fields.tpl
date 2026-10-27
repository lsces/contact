{* Fields for a 'phone' item. Dates are added around the fields by the shared page when the content type offers them. *}
{strip}
{assign var=xid value=$xid|default:''}
<div class="form-group">
	{formlabel label="`$xrefInfo.template_title` Number" for="`$xid`xkey"}
	{forminput}
		<input type="text" class="form-control" name="xkey" id="{$xid}xkey" value="{$xrefInfo.xkey|escape}" />
		{formhelp note="Telephone number. Use (+xx) for country code and include leading zero."}
	{/forminput}
</div>
{include file="bitpackage:contact/xref/phone_tidy_inc.tpl"}

<div class="form-group">
	{formlabel label="`$xrefInfo.template_title` Number Notes" for="`$xid`edit"}
	{forminput}
		<textarea class="form-control" name="edit" id="{$xid}edit" rows="4">{$xrefInfo.data|escape}</textarea>
		{formhelp note="Keep the text attached to reference items short and use comment records to add larger volumes of text."}
	{/forminput}
</div>
{/strip}
