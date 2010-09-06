{* $Header: /cvsroot/bitweaver/_bit_contact/templates/edit.tpl,v 1.4 2010/04/17 04:28:30 wjames5 Exp $ *}
<div class="floaticon">{bithelp}</div>

{assign var=serviceEditTpls value=$gLibertySystem->getServiceValues('content_edit_tpl')}

<div class="admin contact">
	<div class="header">
		<h1>
		{* this weird dual assign thing is cause smarty wont interpret backticks to object in assign tag - spiderr *}
		{if $contactInfo.content_id}
			{assign var=editLabel value="{tr}Edit{/tr} $conDescr"}
			{tr}{tr}Edit{/tr} {$contactInfo.title}{/tr}
		{else}
			{assign var=editLabel value="{tr}Create{/tr} $conDescr"}
			{tr}{$editLabel}{/tr}
		{/if}
		</h1>
	</div>

	{* Check to see if there is an editing conflict *}
	{if $errors.edit_conflict}
		<script language="javascript" type="text/javascript">
			<!--
				alert( "{$errors.edit_conflict|strip_tags}" );
			-->
		</script>
		{formfeedback warning=`$errors.edit_conflict`}
	{/if}

	{strip}
	<div class="body">
		{form enctype="multipart/form-data" id="editpageform"}
			{jstabs}
				{jstab title="$editLabel Body"}
					{legend legend="`$editLabel` Details"}
						<input type="hidden" name="content_id" value="{$contactInfo.content_id}" />
						
						<div class="row">
							{formfeedback warning=`$errors.names`}
							{formfeedback warning=`$errors.store`}

							{formlabel label="$conDescr Contact" for="contentno"}
							{if !$contactInfo.contact_id}
								{forminput}
									New Contact Entry
								{/forminput}
							{else}
								{forminput}
									Edit Contact Entry No : {$contactInfo.contact_id}
								{/forminput}
							{/if}

							{formlabel label="Contact Type" for="contact_type"}
							{forminput}
								{html_options name="$contactInfo.contact_type_list[$contactInfo.contact_type]" options=$contactInfo.contact_type_list selected=`$contactInfo.contact_type`}
								{formhelp note="Users of only this group can view the content of this category."}
							{/forminput}
							
						</div>
						<div class="row">
							{formlabel label="Title" for="prefix"}
							{forminput}
								<input size="60" type="text" name="prefix" id="prefix" value="{$contactInfo.prefix|escape}" />
							{/forminput}
						</div>
						<div class="row">
							{formlabel label="Forename" for="forename"}
							{forminput}
								<input size="60" type="text" name="forename" id="forename" value="{$contactInfo.forename|escape}" />
							{/forminput}
						</div>
						<div class="row">
							{formlabel label="Surname" for="surname"}
							{forminput}
								<input size="60" type="text" name="surname" id="surname" value="{$contactInfo.surname|escape}" />
							{/forminput}
						</div>
						<div class="row">
							{formlabel label="Suffix" for="suffix"}
							{forminput}
								<input size="60" type="text" name="suffix" id="suffix" value="{$contactInfo.suffix|escape}" />
							{/forminput}
						</div>
						<div class="row">
							{formlabel label="Organisation" for="organisation"}
							{forminput}
								<input size="60" type="text" name="organisation" id="organisation" value="{$contactInfo.organisation|escape}" />
							{/forminput}
						</div>
						<div class="row">
							{formlabel label="NI Number" for="nino"}
							{forminput}
								<input size="10" type="text" name="nino" id="nino" value="{$contactInfo.nino|escape}" />
							{/forminput}
						</div>
						<div class="row">
							{formlabel label="Date of Birth" for="dob"}
							{forminput}
								<input size="10" type="text" name="dob" id="dob" value="{$contactInfo.dob|escape}" />
							{/forminput}
						</div>
						<div class="row">
							{formlabel label="Date of eighteen" for="eighteenth"}
							{forminput}
								<input size="10" type="text" name="eighteenth" id="eighteenth" value="{$contactInfo.eighteenth|escape}" />
							{/forminput}
						</div>
						<div class="row">
							{formlabel label="Date of Death" for="dod"}
							{forminput}
								<input size="10" type="text" name="dod" id="dod" value="{$contactInfo.dod|escape}" />
							{/forminput}
						</div>
						<div class="row">
							{formlabel label="Note" for="description"}
							{forminput}
								<input size="60" type="text" name="description" id="description" value="{$contactInfo.description|escape}" />
							{/forminput}
						</div>
					{/legend}
				{/jstab}

				{jstab title="Contact Address"}
					{include file="bitpackage:contact/display_address.tpl"}
				{/jstab}

				{jstab title="Contact Notes"}
					{legend legend="Notes Body"}
						<div class="row">
							{forminput}
								<textarea id="{$textarea_id}" name="edit" rows="{$rows|default:20}" cols="{$cols|default:80}">{$contactInfo.data|escape:html}</textarea>
							{/forminput}
						</div>

						{if $page ne 'SandBox'}
							<div class="row">
								{formlabel label="Comment" for="comment"}
								{forminput}
									<input size="50" type="text" name="comment" id="comment" value="{$contactInfo.comment}" />
									{formhelp note="Add a comment to illustrate your most recent changes."}
								{/forminput}
							</div>
						{/if}

					{/legend}
				{/jstab}

				{jstab title="Liberty Extensions"}
					{if $serviceEditTpls.categorization }
						{legend legend="Categorize"}
							{include file=$serviceEditTpls.categorization}
						{/legend}
					{/if}
				{/jstab}
			{/jstabs}

			{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_mini_tpl"}

			<div class="row submit">
				<input type="submit" name="fCancel" value="{tr}Cancel{/tr}" />&nbsp;
				<input type="submit" name="fSaveContact" value="{tr}Save{/tr}" />
			</div>
		{/form}

	</div><!-- end .body -->
</div><!-- end .admin -->

{/strip}
