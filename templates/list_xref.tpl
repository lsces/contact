
		{assign var=xrefcnt value=$pageInfo.xref|@count}
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
						{if $gBitSystem->isFeatureActive( 'contact_list_last_modified' )}
							<th>Updated</th>
						{/if}
						<th>Reference</th>
					</tr>
				</thead>
				<tbody>
					{section name=xref loop=$pageInfo.xref}
						<tr class="{cycle values="even,odd"}" title="{$list[county].title|escape}">
							<td>
								{$pageInfo.xref[xref].source_title|escape}
							</td>
							<td>
								{$pageInfo.xref[xref].data|escape}
							</td>
							<td>
								{if isset($pageInfo.xref[xref].usn) && ($pageInfo.xref[xref].usn <> '') }
									{$pageInfo.xref[xref].usn|escape}
									{smartlink ititle="Link to" ifile="../property/display_property.php" ibiticon="icons/accessories-text-editor" property_id=$pageInfo.xref[xref].usn}
								{/if}
							</td>
							{if $gBitSystem->isFeatureActive( 'contact_list_last_modified' )}
								<td>
									{$pageInfo.xref[xref].last_update_date|bit_long_date}
								</td>
							{/if}
							<td>
								<span class="actionicon">
									{smartlink ititle="View" ifile="view_xref.php" ibiticon="icons/view-fullscreen" source=$pageInfo.xref[xref].source xref=$pageInfo.xref[xref].cross_reference}
								</span>
								<span class="actionicon">
									{smartlink ititle="Edit" ifile="edit_xref.php" ibiticon="icons/accessories-text-editor" source=$pageInfo.xref[xref].source xref=$pageInfo.xref[xref].cross_reference}
								</span>
								<label for="ev_{$pageInfo.xref[xref].cross_reference}">	
									{$pageInfo.xref[xref].cross_reference}
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
