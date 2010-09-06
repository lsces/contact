<div class="body">
	<div class="content">

		{if isset($contactInfo.usn) && ($contactInfo.usn <> '') }
		<div class="row">
			{formlabel label="USN" for="usn"}
			{forminput}
				{$contactInfo.usn|escape} 
			{/forminput}
		</div>
		{/if}
		{if isset($contactInfo.organisation) && ($contactInfo.organisation <> '') }
		<div class="row">
			{formlabel label="Organisation" for="organisation"}
			{forminput}
				{$contactInfo.organisation|escape} 
			{/forminput}
		</div>
		{/if}
		{if isset($contactInfo.dob) && ($contactInfo.dob <> '') }
		<div class="row">
			{formlabel label="Date of Birth" for="dob"}
			{forminput}
				{$contactInfo.dob|bit_long_date}
			{/forminput}
		</div>
		{/if}
		{if isset($contactInfo.nino) && ($contactInfo.nino <> '') }
		<div class="row">
			{formlabel label="National Insurance Number" for="nino"}
			{forminput}
				{$contactInfo.nino|escape}
			{/forminput}
		</div>
		{/if}
		{include file="bitpackage:contact/display_address.tpl"}
		{jstabs}
			{include file="bitpackage:contact/list_xref.tpl"}
			{include file="bitpackage:contact/list_ticket.tpl"}
			{include file="bitpackage:contact/list_appoint.tpl"}
		{/jstabs}
	</div><!-- end .content -->
</div><!-- end .body -->
