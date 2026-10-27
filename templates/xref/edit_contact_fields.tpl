{* Fields for a 'contact' item: a phone number with the name it belongs to. *}
{strip}
{assign var=xid value=$xid|default:''}
<div class="form-group">
	{formlabel label="`$xrefInfo.template_title` Phone Number" for="`$xid`xkey"}
	{forminput}
		<input type="text" class="form-control" name="xkey" id="{$xid}xkey" value="{$xrefInfo.xkey|escape}" />
		{formhelp note="Phone number for `$xrefInfo.template_title` type of xref record."}
	{/forminput}
</div>
{include file="bitpackage:contact/xref/phone_tidy_inc.tpl"}

<div class="form-group">
	{formlabel label="`$xrefInfo.template_title` Name" for="`$xid`xkey_ext"}
	{forminput}
		<input type="text" class="form-control" name="xkey_ext" id="{$xid}xkey_ext" value="{$xrefInfo.xkey_ext|escape}" />
		{formhelp note="Name for `$xrefInfo.template_title` type of xref record."}
	{/forminput}
</div>

<div class="form-group">
	{formlabel label="`$xrefInfo.template_title` Notes" for="`$xid`edit"}
	{forminput}
		<textarea class="form-control" name="edit" id="{$xid}edit" rows="4">{$xrefInfo.data|escape}</textarea>
		{formhelp note="Keep the text attached to reference items short and use comment records to add larger volumes of text."}
	{/forminput}
</div>
{/strip}
