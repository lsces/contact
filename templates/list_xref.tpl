
		{assign var=xrefcnt value=$contactInfo.xref|@count}
		{jstab title="Cross reference ($xrefcnt)"}
		{legend legend="Information References"}
		<div class="row">
			{formlabel label="Cross reference" for="xref"}
			{forminput}
			<table>
				<caption>{tr}List of linked references{/tr}</caption>
				<thead>
					<tr>
						<th>Information</th>
						<th>Data</th>
						<th>Property</th>
						<th>Updated</th>
						<th>Reference</th>
					</tr>
				</thead>
				<tbody>
					{section name=xref loop=$contactInfo.xref}
						<tr class="{cycle values="even,odd"}" title="{$list[county].title|escape}">
							<td>
								{$contactInfo.xref[xref].source_title|escape}
							</td>
							<td>
								{$contactInfo.xref[xref].data|escape}
							</td>
							<td>
								{if isset($contactInfo.xref[xref].usn) && ($contactInfo.xref[xref].usn <> '') }
									{$contactInfo.xref[xref].usn|escape}
									{smartlink ititle="Link to" ifile="../property/display_property.php" ibiticon="icons/accessories-text-editor" property_id=$contactInfo.xref[xref].usn}
								{/if}
							</td>
							<td>
								{$contactInfo.xref[xref].last_update_date|bit_long_date}
							</td>
							<td>
								<span class="actionicon">
									{smartlink ititle="View" ifile="view_xref.php" ibiticon="icons/view-fullscreen" source=$contactInfo.xref[xref].source xref=$contactInfo.xref[xref].cross_reference}
								</span>
								<span class="actionicon">
									{smartlink ititle="Edit" ifile="edit_xref.php" ibiticon="icons/accessories-text-editor" source=$contactInfo.xref[xref].source xref=$contactInfo.xref[xref].cross_reference}
								</span>
								<label for="ev_{$contactInfo.xref[xref].cross_reference}">	
									{$contactInfo.xref[xref].cross_reference}
								</label>
							</td>
						</tr>
					{sectionelse}
						<tr class="norecords">
							<td colspan="3">
								{tr}No records found{/tr}
							</td>
						</tr>
					{/section}
				</tbody>
			</table>
			{/forminput}
		</div>
		{/legend}
		{/jstab}
