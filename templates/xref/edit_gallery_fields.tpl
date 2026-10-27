{* Fields for a 'gallery' item: the gallery this contact's own gallery grid shows. *}
{strip}
{assign var=xid value=$xid|default:''}
<div class="form-group">
	{formlabel label="Gallery Content ID" for="`$xid`xref"}
	{forminput}
		<input type="text" class="form-control input-small" name="xref" id="{$xid}xref" value="{$xrefInfo.xref|escape}" />
		{formhelp note="The content_id of the FisheyeGallery this contact's own gallery grid should show - found on that gallery's own edit page."}
	{/forminput}
</div>

<div class="form-group">
	{formlabel label="Note" for="`$xid`edit"}
	{forminput}
		<input type="text" class="form-control" name="edit" id="{$xid}edit" value="{$xrefInfo.data|escape}" />
	{/forminput}
</div>
{/strip}
