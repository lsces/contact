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
		{if isset($gContent->mInfo.name) && ($gContent->mInfo.name <> '') }
		<div class="form-group">
			{formlabel label="Name" for="name"}
			{forminput}
				{$gContent->mInfo.name|escape} 
			{/forminput}
			<div class="clear"></div>
		</div>
		{/if}
		{if isset($gContent->mInfo.organisation) && ($gContent->mInfo.organisation <> '') }
		<div class="form-group">
			{formlabel label="Organisation" for="organisation"}
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
		{section name=address loop=$gContent->mInfo.address}
			{include file="bitpackage:contact/display_address.tpl" header=$gContent->mInfo.address[address].source_title address=$gContent->mInfo.address[address] locate=1}
		{sectionelse}
			<li class="item norecords">
				{tr}No addresses found{/tr}
				{if $gBitUser->hasPermission('p_edit_contact')}
					{smartlink ititle="Add an address record" ifile="add_xref_address.php" booticon="icon-note-add" content_id=$gContent->mInfo.content_id}
				{/if}
			</li>
		{/section}

		<div class="form-group">
			{formlabel label="General Notes" for="data"}
			{forminput}
				{$gContent->mInfo.data}
			{/forminput}
		</div>

		{jstabs}
			{section name=type loop=$gContent->mInfo.type}
				{include file="bitpackage:contact/list_xref_generic.tpl" source=$gContent->mInfo.type[type].source source_title=$gContent->mInfo.type[type].title xref_type=$gContent->mInfo.type[type].xref_type}
			{/section}
		{/jstabs}
