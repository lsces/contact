{strip}
{jstab title="Time period"}
	{legend legend="Start and Stop Dates"}
		<div class="form-group">
			{formlabel label="Start date/time" for="start_date"}
			<label class="checkbox-inline"><input type="checkbox" name="ignore_start_date" value="on" {if $xrefInfo.ignore_start_date eq 'y' or $xrefInfo.ignore_start_date eq 'on'}checked{/if} /> {tr}No start date{/tr}</label>
			{forminput}
				<input type="datetime-local" class="form-control" name="start_date" id="start_date"
					value="{$xrefInfo.start_date|replace:' ':'T'|truncate:16:'':true}" />
				{formhelp note="This xref record becomes valid on this date."}
			{/forminput}
		</div>
		<div class="form-group">
			{formlabel label="End date/time" for="end_date"}
			<label class="checkbox-inline"><input type="checkbox" name="ignore_end_date" value="on" {if $xrefInfo.ignore_end_date eq 'y' or $xrefInfo.ignore_end_date eq 'on'}checked{/if} /> {tr}No end date{/tr}</label>
			{forminput}
				<input type="datetime-local" class="form-control" name="end_date" id="end_date"
					value="{$xrefInfo.end_date|replace:' ':'T'|truncate:16:'':true}" />
				{formhelp note="This xref record finishes on this date."}
			{/forminput}
		</div>
	{/legend}
{/jstab}
{/strip}
