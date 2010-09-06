<div class="body">
	<div class="content">
		{include file="bitpackage:liberty/storage_thumbs.tpl"}
		Title: {$contactInfo.prefix}<br />
		Forename: {$contactInfo.forename}<br />
		Surname: {$contactInfo.surname}<br />
		Suffix: {$contactInfo.suffix}<br />
		<br />
		Organisation: {$contactInfo.organisation}<br />
		<br />
		NI Number:{$contactInfo.nino} Date of Birth:{$contactInfo.dob} Date of eighteenth:{$contactInfo.eighteenth} Date of Death:{$contactInfo.dod} <br />
		<br />
		Note: {$contactInfo.note}<br />
		Memo:<br />
		{$contactInfo.data}<br />
	</div><!-- end .content -->
</div><!-- end .body -->
