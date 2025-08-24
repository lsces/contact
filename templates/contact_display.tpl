<div class="body">
	<div class="content">
		{include file="bitpackage:liberty/storage_thumbs.tpl"}
		Title: {$gContent->mInfo.prefix}<br />
		Forename: {$gContent->mInfo.forename}<br />
		Surname: {$gContent->mInfo.surname}<br />
		Suffix: {$gContent->mInfo.suffix}<br />
		<br />
		Organisation: {$gContent->mInfo.organisation}<br />
		<br />
		NI Number:{$gContent->mInfo.nino} Date of Birth:{$gContent->mInfo.dob} Date of eighteenth:{$gContent->mInfo.eighteenth} Date of Death:{$gContent->mInfo.dod} <br />
		<br />
		Note: {$gContent->mInfo.note}<br />
		Memo:<br />
		{$gContent->mInfo.data}<br />
	</div><!-- end .content -->
</div><!-- end .body -->
