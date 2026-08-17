{strip}
<div class="edit contact_xref">
	<div class="floaticon">{bithelp}</div>
	<div class="header">
		<h1>{tr}Edit Location{/tr}: {$gContent->getTitle()|escape}</h1>
	</div>

	<div class="body">
		{formfeedback error=$errors}

		{form id="editXrefForm"}
			<input type="hidden" name="content_id" value="{$xrefInfo.content_id|escape}" />
			<input type="hidden" name="xref_id"    value="{$xrefInfo.xref_id|escape}" />
			<input type="hidden" name="item"       value="{$xrefInfo.item|escape}" />

			{jstabs}
				{jstab title="Location Details"}
					{legend legend="Contents"}
						<div class="form-group">
							{formlabel label="Geographic Location" for="xref"}
							{forminput}
								<a class="item" href="http://www.openstreetmap.org/?mlat={$xrefInfo.xkey_ext}&mlon={$xrefInfo.xkey}&zoom=17&layers=MN" target="_blank">{tr}OpenStreetMap Link{/tr}</a>
								{formhelp note="Use one of the graphical tools like multimap or google maps to identify actual co-ordinates."}
							{/forminput}
							<div class="clear"></div>
						</div>

						<div class="form-group">
							{formlabel label="Easting/Longitude" for="xkey"}
							{forminput}
								<input type="text" class="form-control" name="xkey" id="xkey" value="{$xrefInfo.xkey|escape}" />
								{formhelp note="Longitude for the location."}
							{/forminput}
							<div class="clear"></div>
						</div>

						<div class="form-group">
							{formlabel label="Northing/Latitude" for="xkey_ext"}
							{forminput}
								<input type="text" class="form-control" name="xkey_ext" id="xkey_ext" value="{$xrefInfo.xkey_ext|escape}" />
								{formhelp note="Latitude for the location."}
							{/forminput}
							<div class="clear"></div>
						</div>

						<div class="form-group">
							{formlabel label="Location Directions" for="edit"}
							{forminput}
								<textarea class="form-control" name="edit" id="edit" rows="4">{$xrefInfo.data|escape}</textarea>
								{formhelp note="Directions to assist finding the actual location where site is not accessible via the postcode."}
							{/forminput}
						</div>
					{/legend}
				{/jstab}

				{include file="bitpackage:liberty/edit_xref_dates.tpl"}
			{/jstabs}

			<div class="form-group submit">
				<input type="submit" class="btn btn-default" name="fCancel"   value="{tr}Cancel{/tr}" />
				<input type="submit" class="btn btn-primary" name="fSaveXref" value="{tr}Save{/tr}" />
			</div>
		{/form}
	</div><!-- end .body -->
</div><!-- end .contact_xref -->
{/strip}
