{* $Header$ *}

{strip}
<div class="floaticon">{bithelp}</div>

<div class="admin articles">
	<div class="header">
		<h1>{tr}Admin Xref Type List{/tr}</h1>
	</div>

	<div class="body">
		{form legend="Edit an Xref Type" enctype="multipart/form-data"}
      		<input type="hidden" name="topic_id" value="{$gContent->mTopicId}" />

			{formfeedback success=$gContent->mSuccess error=$gContent->mErrors}

			<div class="form-group">
				{formlabel label="Topic Name" for="topic_name"}
				{forminput}
					<input type="text" id="topic_name" name="topic_name" value="{$gContent->mInfo.topic_name}" />
					{formhelp note=""}
				{/forminput}
				<div class="clear"></div>
			</div>

			<div class="form-group">
				{formlabel label="Topic Enabled" for="topic_enabled"}
				{forminput}
					<input type="checkbox" id="topic_enabled" name="active_topic" {if $gContent->mInfo.active_topic == 'y'}checked="checked"{/if} />
					{formhelp note=""}
				{/forminput}
				<div class="clear"></div>
			</div>

			<div class="form-group">
				{formlabel label="Upload Image" for="t-image"}
				{forminput}
					<input name="upload" id="t-image" type="file" />
					{formhelp note=""}
				{/forminput}
				<div class="clear"></div>
			</div>

			<div class="form-group">
				{formlabel label="Current Image"}
				{forminput}
					{if $gContent->mInfo.has_topic_image eq 'y'}
						<img src="{$gContent->mInfo.topic_image_url}" /> <br/>
						<a href="{$smarty.server.PHP_SELF}?topic_id={$gContent->mTopicId}&amp;fRemoveTopicImage=1">Remove Topic Image</a>
					{else}
						{tr}No Image found{/tr}
					{/if}
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="form-group submit">
				<input type="submit" name="fSubmitSaveTopic" value="{tr}Update Topic{/tr}" />
			</div>
		{/form}
	</div><!-- end .body -->
</div><!-- end .admin -->
{/strip}
