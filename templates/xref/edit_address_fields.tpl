{* Fields for an 'address' item. When the item's definition data names its parts (address, town, county, postcode,
   country, notes - see liberty's DEVELOPER.md, "liberty_xref_item.data"), the shared JSON form is used: the parts
   live as JSON in `data`, the postcode is mirrored to xkey and the address as one line to xkey_ext on save, and a
   row with no JSON yet starts from those columns. A site whose address item carries no such definition data keeps
   the plain boxes below, which edit xkey_ext, xkey and the notes directly. *}
{strip}
{assign var=xid value=$xid|default:''}
{if $xrefInfo.json_fields}
	{include file="bitpackage:liberty/xref/edit_json-list_fields.tpl"}
{else}
<div class="form-group">
	{formlabel label="House Name/Number" for="`$xid`xkey_ext"}
	{forminput}
		<input type="text" class="form-control" name="xkey_ext" id="{$xid}xkey_ext" value="{$xrefInfo.xkey_ext|escape}" />
		{formhelp note="House name and/or number when used with postcode, or free format full address."}
	{/forminput}
</div>

<div class="form-group">
	{formlabel label="Postcode" for="`$xid`xkey"}
	{forminput}
		<input type="text" class="form-control" name="xkey" id="{$xid}xkey" value="{$xrefInfo.xkey|escape}" />
		{formhelp note="Postcode for address."}
	{/forminput}
</div>

<div class="form-group">
	{formlabel label="`$xrefInfo.template_title` Notes" for="`$xid`edit"}
	{forminput}
		<textarea class="form-control" name="edit" id="{$xid}edit" rows="4">{$xrefInfo.data|escape}</textarea>
		{formhelp note="Keep the text attached to reference items short and use comment records to add larger volumes of text."}
	{/forminput}
</div>
{/if}
{/strip}
