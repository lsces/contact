{* $Header$ *}
{strip}

<div class="floaticon">{bithelp}</div>

<div class="admin articles">
	<div class="header">
		<h1>{tr}Admin Contact Type{/tr}</h1>
	</div>

	<div class="body">
		{formfeedback error=$gContent->mErrors}

		{form legend="Create a new Topic" enctype="multipart/form-data"}
			<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />

			<div class="control-group column-group gutters">
				{formlabel label="Contact Type Title" for="type_name"}
				{forminput}
					<input type="text" id="topic_name" name="type_name" />
					{formhelp note=""}
				{/forminput}
			</div> 

			<div class="control-group submit">
				<input type="submit" class="ink-button" name="fSubmitAddType" value="{tr}Add Contact Type{/tr}" />
			</div>
		{/form}

		<table class="table data">
			<caption>{tr}List of Contact Types{/tr}</caption>
			<tr>
				<th>{tr}Title{/tr} [ {tr}Number of Types{/tr} ]</th>
				<th>{tr}Role{/tr}</th>
				<th>{tr}Actions{/tr}</th>
			</tr>

			{section name=user loop=$contacttype}
				<tr class="{cycle values="even,odd"}">
					<td>
						<h2>
							<a href="{$smarty.const.CONTACT_PKG_URL}index.php?xref_type={$contacttype[user].xref_type}">{$contacttype[user].source}</a>
							&nbsp; <small>[ {$contacttype[user].num_types} ]</small>
						</h2>

					</td>

					<td style="text-align:center;">
						{$contacttype[user].role_id}
					</td>

					<td align="right">
						{smartlink ititle='edit' booticon="icon-edit" ifile='edit_contact_type.php' xref_type=$contacttype[user].xref_type}
						{* smartlink ititle='permissions' booticon="icon-key" ipackage='kernel' ifile='object_permissions.php' objectName="Topic `$topics[user].name`" object_type=topic permType=topics object_id=$topics[user].topic_id *}
						<br />
						<a href="{$smarty.const.CONTACT_PKG_URL}admin/admin_contact_type.php?fRemoveType=1&amp;topic_id={$contacttype[user].xref_type}">{booticon iname="icon-trash" ipackage="icons" iforce=icon_text iexplain="Remove Contact Type"}</a>
					</td>
				</tr>
			{sectionelse}
				<tr class="norecords">
					<td colspan="4">{tr}No records found{/tr}</td>
				</tr>
			{/section}
		</table>
	</div><!-- end .body -->
</div><!-- end .admin -->
{/strip}
