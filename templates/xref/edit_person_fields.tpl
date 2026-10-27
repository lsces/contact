{* Fields for a 'person' item: the contact person it links to, with a search box (the shared link picker;
   enrichXrefDisplay() on Contact points it at the people lookup), and a note. *}
{strip}
{assign var=xid value=$xid|default:''}
{include file="bitpackage:liberty/xref/edit_link_picker_inc.tpl"}

<div class="form-group">
	{formlabel label="Notes" for="`$xid`edit"}
	{forminput}
		<textarea class="form-control" name="edit" id="{$xid}edit" rows="4">{$xrefInfo.data|escape}</textarea>
	{/forminput}
</div>
{/strip}
