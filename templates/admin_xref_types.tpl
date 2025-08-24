{* $Header$ *}
{strip}

<div class="floaticon">{bithelp}</div>

<div class="admin contact">
	<div class="header">
		<h1>{tr}Admin Contact Source Type{/tr}</h1>
	</div>

	<div class="body">
		{formfeedback error=$gContent->mErrors}

		{form legend="Create a new Source Type" enctype="multipart/form-data"}
			<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />

			<div class="form-group">
				{formlabel label="Source Type Title" for="cross_ref_title"}
				{forminput}
					<input type="text" id="cross_ref_title" name="cross_ref_title" />
					{formhelp note=""}
				{/forminput}
			</div> 

			<div class="form-group">
				{formlabel label="Source Template" for="topic_name"}
				{forminput}
					<input type="text" id="template" name="template" />
					{formhelp note=""}
				{/forminput}
			</div> 

			<div class="form-group submit">
				<input type="submit" class="btn btn-default" name="fSubmitAddTopic" value="{tr}Add Topic{/tr}" />
			</div>
		{/form}

		<table class="table data">
			<caption>{tr}List of Contact Source Types{/tr}</caption>
			<tr>
				<th>{tr}Source{/tr}</th>
				<th>{tr}Title{/tr} [ {tr}Number of Entries{/tr} ]</th>
				<th>{tr}Template{/tr}</th>
				<th>{tr}Href{/tr}</th>
				<th>{tr}Multi{/tr}</th>
				<th>{tr}Role{/tr}</th>
				<th>{tr}Actions{/tr}</th>
			</tr>

			{section name=type loop=$xref_types}
				<tr class="{cycle values="even,odd"}">
					<td class="">
						<h3>
							{$xref_types[type].source}
						</h3>
					</td>

					<td>
						<h3>
							<a href="{$smarty.const.CONTACT_PKG_URL}list.php?source={$xref_types[type].source}">{$xref_types[type].cross_ref_title}</a>
							&nbsp; <small>[ {$xref_types[type].num_entries} ]</small>
						</h3>

					</td>

					<td class="">
						{$xref_types[type].template}
					</td>

					<td class="">
						{$xref_types[type].cross_ref_href}
					</td>

					<td class="">
						{$xref_types[type].multi}
					</td>

					<td class="">
						{$xref_types[type].role}
					</td>

					<td class="">
						{smartlink ititle='edit' booticon="icon-edit" ifile='edit_xref_type.php' source=$xref_types[type].source}
						{* smartlink ititle='permissions' booticon="icon-key" ipackage='kernel' ifile='object_permissions.php' objectName="Topic `$xref_types[type].name`" object_type=topic permType=topics object_id=$xref_types[type].topic_id *}
						<a href="{$smarty.const.CONTACT_PKG_URL}admin/admin_topics.php?fRemoveSource=1&amp;source={$xref_types[type].topic_id}">{booticon iname="icon-trash" ipackage="icons" iforce=icon_text iexplain="Remove Source"}</a>
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
