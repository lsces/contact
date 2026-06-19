		{include file="bitpackage:contact/display_type_header.tpl"}

		{if isset($gContent->mInfo.usn) && ($gContent->mInfo.usn <> '') }
		<div class="form-group">
			{formlabel label="USN" for="usn"}
			{forminput}
				{$gContent->mInfo.usn|escape} 
			{/forminput}
			<div class="clear"></div>
		</div>
		{/if}
		{if $isPerson}
		<div class="form-group">
			{formlabel label="Name"}
			{forminput}
				{$gContent->mInfo.name|escape}
			{/forminput}
			<div class="clear"></div>
		</div>
		{else}
		<div class="form-group">
			{formlabel label="Organisation"}
			{forminput}
				{$gContent->mInfo.organisation|escape}
			{/forminput}
			<div class="clear"></div>
		</div>
		{/if}
		{if isset($gContent->mInfo.dob) && ($gContent->mInfo.dob <> '') }
		<div class="form-group">
			{formlabel label="Date of Birth" for="dob"}
			{forminput}
				{$gContent->mInfo.dob|bit_long_date}
			{/forminput}
			<div class="clear"></div>
		</div>
		{/if}
		{if isset($gContent->mInfo.nino) && ($gContent->mInfo.nino <> '') }
		<div class="form-group">
			{formlabel label="National Insurance Number" for="nino"}
			{forminput}
				{$gContent->mInfo.nino|escape}
			{/forminput}
			<div class="clear"></div>
		</div>
		{/if}
		{if $gContent->mInfo.linked_user_login}
		<div class="form-group">
			{formlabel label="Registered User"}
			{forminput}
				{$gContent->mInfo.linked_user_name|escape} ({$gContent->mInfo.linked_user_login|escape})
			{/forminput}
			<div class="clear"></div>
		</div>
		{/if}
		{if $gContent->mInfo.data}
		<div class="form-group">
			{formlabel label="Note"}
			{forminput}
				{$gContent->mInfo.data}
			{/forminput}
		</div>
		{/if}

		{section name=address loop=$gContent->mInfo.address}
			{include file="bitpackage:contact/display_address.tpl" header=$gContent->mInfo.address[address].source_title address=$gContent->mInfo.address[address] locate=1}
		{/section}

		{if $gXrefInfo->mGroups}
		{jstabs}
			{foreach $gXrefInfo->mGroups as $xrefGroup}
				{include file=$gContent->getXrefListTemplate($xrefGroup->mTemplate)
					xrefGroup=$xrefGroup
					allow_edit=false}
			{/foreach}
		{/jstabs}
		{/if}
