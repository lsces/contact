<div class="row">
	{formlabel label="NI Number" for="nino"}
	{forminput}
		<input size="10" type="text" name="nino" id="nino" value="{$pageInfo.nino|escape}" />
	{/forminput}
</div>
<div class="row">
	{formlabel label="Date of Birth" for="dob"}
	{forminput}
		<input size="10" type="text" name="dob" id="dob" value="{$pageInfo.dob|escape}" />
	{/forminput}
</div>
<div class="row">
	{formlabel label="Date of eighteen" for="eighteenth"}
	{forminput}
		<input size="10" type="text" name="eighteenth" id="eighteenth" value="{$pageInfo.eighteenth|escape}" />
	{/forminput}
</div>
<div class="row">
	{formlabel label="Date of Death" for="dod"}
	{forminput}
		<input size="10" type="text" name="dod" id="dod" value="{$pageInfo.dod|escape}" />
	{/forminput}
</div>

