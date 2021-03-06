{strip}
<div class="floaticon">{bithelp}</div>

{assign var=serviceEditTpls value=$gLibertySystem->getServiceValues('content_edit_tpl')}

<div class="admin contact">
	<div class="header">
		<h1>
		{* this weird dual assign thing is cause smarty wont interpret backticks to object in assign tag - spiderr *}
		{if $pageInfo.content_id}
			{assign var=editLabel value="{tr}Edit{/tr} $conDescr"}
			{tr}{tr}Edit{/tr} {$pageInfo.title}{/tr}
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
		{formfeedback warning=$errors.edit_conflict}
	{/if}

	<div class="body">
		{form enctype="multipart/form-data" id="editpageform"}
			{jstabs}
				{jstab title="$editLabel Body"}
					{legend legend="$editLabel Details"}
						<input type="hidden" name="content_id" value="{$pageInfo.content_id}" />
						
						<div class="form-group">
							{formfeedback warning=$errors.names}
							{formfeedback warning=$errors.store}

							{formlabel label="$conDescr Contact" for="contentno"}
							{if !$pageInfo.content_id}
								{forminput}
									New Contact Entry
								{/forminput}
							{else}
								{forminput}
									Edit Contact Entry No : {$pageInfo.content_id}
								{/forminput}
							{/if}
							<div class="clear"></div>
						</div>

						{include file="bitpackage:contact/edit_type_header.tpl"}
							
						{if $pageInfo.name or $pageInfo.contact_types.0.content_id or !isset( $pageInfo.contact_types ) }
							<div class="form-group">
								{formlabel label="Title" for="prefix"}
								{forminput}
									<input size="60" type="text" name="prefix" id="prefix" value="{$pageInfo.prefix|escape}" />
								{/forminput}
								<div class="clear"></div>
							</div>
							<div class="form-group">
								{formlabel label="Forename" for="forename"}
								{forminput}
									<input size="60" type="text" name="forename" id="forename" value="{$pageInfo.forename|escape}" />
								{/forminput}
								<div class="clear"></div>
							</div>
							<div class="form-group">
								{formlabel label="Surname" for="surname"}
								{forminput}
									<input size="60" type="text" name="surname" id="surname" value="{$pageInfo.surname|escape}" />
								{/forminput}
								<div class="clear"></div>
							</div>
							<div class="form-group">
								{formlabel label="Suffix" for="suffix"}
								{forminput}
									<input size="60" type="text" name="suffix" id="suffix" value="{$pageInfo.suffix|escape}" />
								{/forminput}
								<div class="clear"></div>
							</div>
						{/if}
						{if $pageInfo.organisation or $pageInfo.contact_types.1.content_id or !isset( $pageInfo.contact_types ) }
							<div class="form-group">
								{formlabel label="Organisation" for="organisation"}
								{forminput}
									<input size="60" type="text" name="organisation" id="organisation" value="{$pageInfo.organisation|escape}" />
								{/forminput}
								<div class="clear"></div>
							</div>
						{/if}
{* include edit_personal.tpl *}

						<div class="form-group">
							{formlabel label="Key Number" for="xkey"}
							{forminput}
								<input size="10" type="text" name="xkey" id="xkey" value="{$pageInfo.xkey|escape}" />
							{/forminput}
							<div class="clear"></div>
						</div>

						<div class="form-group">
							{formlabel label="Note" for="description"}
							{forminput}
								<input size="60" type="text" name="description" id="description" value="{$pageInfo.description|escape}" />
							{/forminput}
							<div class="clear"></div>
						</div>
					{/legend}
				{/jstab}

				{jstab title="Contact Notes"}
					{legend legend="Notes Body"}
						<div class="form-group">
							{textarea rows=30 noformat=1 edit=$pageInfo.edit}
						</div>

						{if $page ne 'SandBox'}
							<div class="form-group">
								{formlabel label="Comment" for="comment"}
								{forminput}
									<input size="50" type="text" name="comment" id="comment" value="{$pageInfo.comment}" />
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
					{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_mini_tpl"}

				{/jstab}
			{/jstabs}

			<div class="form-group submit">
				<input type="submit" name="fCancel" value="{tr}Cancel{/tr}" />&nbsp;
				<input type="submit" name="fSaveContact" value="{tr}Save{/tr}" />
			</div>
		{/form}

	</div><!-- end .body -->
</div><!-- end .admin -->
{/strip}