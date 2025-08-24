{* $Header: /cvsroot/bitweaver/_bit_contact/templates/edit_contact.tpl,v 1.1 2008/08/27 16:20:01 lsces Exp $ *}
{popup_init src="`$gBitLoc.THEMES_PKG_URL`overlib.js"}
{strip}
<div class="floaticon">{bithelp}</div>

{* Check to see if there is an editing conflict *}
{if $editpageconflict == 'y'}
	<script>
		<!-- Hide Script
			alert("{tr}This page is being edited by{/tr} {$semUser}. {tr}Proceed at your own peril{/tr}.")
		//End Hide Script-->
	</script>
{/if}

<div class="admin contact">

	{if $preview}
		<h2>Preview - {$gContent->mInfo.title}</h2>
		<div class="preview">
			{include file="bitpackage:contact/contact_display.tpl" page=`$gContent->mInfo.content_id`}
		</div>
	{/if}

	<div class="header">
		<h1>
		{if $gContent->mInfo.content_id}
			{tr}{tr}Edit - {/tr} {$gContent->mInfo.title}{/tr}
		{else}
			{tr}Create New Record{/tr}
		{/if}
		</h1>
	</div>

	<div class="body">
		{form legend="Edit/Create Contact Record" enctype="multipart/form-data" id="editpageform"}
			<input type="hidden" name="content_id" value="{$gContent->mInfo.content_id}" />

			{include file="bitpackage:contact/edit_type_header.tpl"}

{*			<div class="form-group">
				{formlabel label="Title" for="title"}
				{forminput}
					<input size="60" type="text" name="prefix" id="prefix" value="{$gContent->mInfo.prefix|escape}" />
				{/forminput}
				<div class="clear"></div>
			</div>
			<div class="form-group">
				{formlabel label="Forename" for="forename"}
				{forminput}
					<input size="60" type="text" name="forename" id="forename" value="{$gContent->mInfo.forename|escape}" />
				{/forminput}
				<div class="clear"></div>
			</div>
			<div class="form-group">
				{formlabel label="Surname" for="surname"}
				{forminput}
					<input size="60" type="text" name="surname" id="surname" value="{$gContent->mInfo.surname|escape}" />
				{/forminput}
				<div class="clear"></div>
			</div>
			<div class="form-group">
				{formlabel label="Suffix" for="suffix"}
				{forminput}
					<input size="60" type="text" name="suffix" id="suffix" value="{$gContent->mInfo.suffix|escape}" />
				{/forminput}
				<div class="clear"></div>
			</div>
			<div class="form-group">
				{formlabel label="Organisation" for="organisation"}
				{forminput}
					<input size="60" type="text" name="organisation" id="organisation" value="{$gContent->mInfo.organisation|escape}" />
				{/forminput}
				<div class="clear"></div>
			</div>
			<div class="form-group">
				{formlabel label="NI Number" for="nino"}
				{forminput}
					<input size="10" type="text" name="nino" id="nino" value="{$gContent->mInfo.nino|escape}" />
				{/forminput}
				<div class="clear"></div>
			</div>
*}
			<div class="form-group">
				{formlabel label="Note" for="description"}
				{forminput}
					<input size="60" type="text" name="description" id="description" value="{$gContent->mInfo.description|escape}" />
				{/forminput}
				<div class="clear"></div>
			</div>
			<div class="form-group">
				{formlabel label="Memo" for="$textarea_id"}
				{forminput}
					<input type="hidden" name="rows" value="{$rows}" />
					<input type="hidden" name="cols" value="{$cols}" />
					<textarea id="{$textarea_id}" name="edit" rows="{$rows|default:20}" cols="{$cols|default:80}">{if !$preview}{$gContent->mInfo.data|escape}{else}{$edit}{/if}</textarea>
				{/forminput}
				<div class="clear"></div>
			</div>

			<div class="form-group submit">
				<input type="submit" name="preview" value="{tr}Preview{/tr}" /> 
				<input type="submit" name="fSavePage" value="{tr}Save{/tr}" />&nbsp;
				<input type="submit" name="cancel" value="{tr}Cancel{/tr}" />
			</div>
		{/form}

	</div><!-- end .body -->
</div><!-- end .contact -->

{/strip}

<br />

