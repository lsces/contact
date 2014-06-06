		{include file="bitpackage:contact/display_type_header.tpl"}

		{if isset($pageInfo.usn) && ($pageInfo.usn <> '') }
		<div class="form-group">
			{formlabel label="USN" for="usn"}
			{forminput}
				{$pageInfo.usn|escape} 
			{/forminput}
			<div class="clear"></div>
		</div>
		{/if}
		{if isset($pageInfo.name) && ($pageInfo.name <> '') }
		<div class="form-group">
			{formlabel label="Name" for="name"}
			{forminput}
				{$pageInfo.name|escape} 
			{/forminput}
			<div class="clear"></div>
		</div>
		{/if}
		{if isset($pageInfo.organisation) && ($pageInfo.organisation <> '') }
		<div class="form-group">
			{formlabel label="Organisation" for="organisation"}
			{forminput}
				{$pageInfo.organisation|escape} 
			{/forminput}
			<div class="clear"></div>
		</div>
		{/if}
		{if isset($pageInfo.dob) && ($pageInfo.dob <> '') }
		<div class="form-group">
			{formlabel label="Date of Birth" for="dob"}
			{forminput}
				{$pageInfo.dob|bit_long_date}
			{/forminput}
			<div class="clear"></div>
		</div>
		{/if}
		{if isset($pageInfo.nino) && ($pageInfo.nino <> '') }
		<div class="form-group">
			{formlabel label="National Insurance Number" for="nino"}
			{forminput}
				{$pageInfo.nino|escape}
			{/forminput}
			<div class="clear"></div>
		</div>
		{/if}
		{section name=address loop=$pageInfo.address}
			{include file="bitpackage:contact/display_address.tpl" header=$pageInfo.address[address].source_title address=$pageInfo.address[address] locate=1}
		{sectionelse}
			<li class="item norecords">
				{tr}No addresses found{/tr}
				{if $gBitUser->hasPermission('p_edit_contact')}
					{smartlink ititle="Add an address record" ifile="add_xref_address.php" ibiticon="icons/bookmark-new" content_id=$pageInfo.content_id}
				{/if}
			</li>
		{/section}

		<div class="form-group">
			{formlabel label="General Notes" for="data"}
			{forminput}
				{$pageInfo.data}
			{/forminput}
		</div>

		{jstabs}
			{section name=type loop=$pageInfo.type}
				{include file="bitpackage:contact/list_xref_generic.tpl" source=$pageInfo.type[type].source source_title=$pageInfo.type[type].title xref_type=$pageInfo.type[type].xref_type}
			{/section}
		{/jstabs}
