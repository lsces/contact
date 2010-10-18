<div class="body">
	<div class="content">
		{include file="bitpackage:liberty/storage_thumbs.tpl"}
		Title: {$pageInfo.prefix}<br />
		Forename: {$pageInfo.forename}<br />
		Surname: {$pageInfo.surname}<br />
		Suffix: {$pageInfo.suffix}<br />
		<br />
		Organisation: {$pageInfo.organisation}<br />
		<br />
		NI Number:{$pageInfo.nino} Date of Birth:{$pageInfo.dob} Date of eighteenth:{$pageInfo.eighteenth} Date of Death:{$pageInfo.dod} <br />
		<br />
		Note: {$pageInfo.note}<br />
		Memo:<br />
		{$pageInfo.data}<br />
	</div><!-- end .content -->
</div><!-- end .body -->
