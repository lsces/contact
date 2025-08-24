<div class="form-group">
	{formlabel label="NI Number" for="nino"}
	{forminput}
		<input size="10" type="text" name="nino" id="nino" value="{$gContent->mInfo.nino|escape}" />
	{/forminput}
</div>
<div class="form-group">
	{formlabel label="Date of Birth" for="dob"}
	{forminput}
		<input size="10" type="text" name="dob" id="dob" value="{$gContent->mInfo.dob|escape}" />
	{/forminput}
</div>
<div class="form-group">
	{formlabel label="Date of eighteen" for="eighteenth"}
	{forminput}
		<input size="10" type="text" name="eighteenth" id="eighteenth" value="{$gContent->mInfo.eighteenth|escape}" />
	{/forminput}
</div>
<div class="form-group">
	{formlabel label="Date of Death" for="dod"}
	{forminput}
		<input size="10" type="text" name="dod" id="dod" value="{$gContent->mInfo.dod|escape}" />
	{/forminput}
</div>

