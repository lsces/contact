		{include file="bitpackage:contact/display_type_header.tpl"}

		{if isset($pageInfo.usn) && ($pageInfo.usn <> '') }
		<div class="row">
			{formlabel label="USN" for="usn"}
			{forminput}
				{$pageInfo.usn|escape} 
			{/forminput}
		</div>
		{/if}
		{if isset($pageInfo.name) && ($pageInfo.name <> '') }
		<div class="row">
			{formlabel label="Name" for="name"}
			{forminput}
				{$pageInfo.name|escape} 
			{/forminput}
		</div>
		{/if}
		{if isset($pageInfo.organisation) && ($pageInfo.organisation <> '') }
		<div class="row">
			{formlabel label="Organisation" for="organisation"}
			{forminput}
				{$pageInfo.organisation|escape} 
			{/forminput}
		</div>
		{/if}
		{if isset($pageInfo.dob) && ($pageInfo.dob <> '') }
		<div class="row">
			{formlabel label="Date of Birth" for="dob"}
			{forminput}
				{$pageInfo.dob|bit_long_date}
			{/forminput}
		</div>
		{/if}
		{if isset($pageInfo.nino) && ($pageInfo.nino <> '') }
		<div class="row">
			{formlabel label="National Insurance Number" for="nino"}
			{forminput}
				{$pageInfo.nino|escape}
			{/forminput}
		</div>
		{/if}
		{include file="bitpackage:contact/display_address.tpl" header="Contact Address"}

		<div class="row">
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
