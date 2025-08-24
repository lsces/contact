
		{assign var=xrefcnt value=$gContent->mInfo.xref|@count}
		{jstab title="Cross reference ($xrefcnt)"}
		{legend legend="Information References"}
		<div class="form-group">
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
					{section name=xref loop=$gContent->mInfo.xref}
						<tr class="{cycle values="even,odd"}" title="{$list[county].title|escape}">
							<td>
								{$gContent->mInfo.xref[xref].source_title|escape}
							</td>
							<td>
								{$gContent->mInfo.xref[xref].data|escape}
							</td>
							<td>
								{if isset($gContent->mInfo.xref[xref].usn) && ($gContent->mInfo.xref[xref].usn <> '') }
									{$gContent->mInfo.xref[xref].usn|escape}
									{smartlink ititle="Link to" ifile="../property/display_property.php" booticon="icon-note-edit" property_id=$gContent->mInfo.xref[xref].usn}
								{/if}
							</td>
							{if $gBitSystem->isFeatureActive( 'contact_list_last_modified' )}
								<td>
									{$gContent->mInfo.xref[xref].last_update_date|bit_long_date}
								</td>
							{/if}
							<td>
								<span class="actionicon">
									{smartlink ititle="View" ifile="view_xref.php" booticon="icon-note-go" source=$gContent->mInfo.xref[xref].source xref=$gContent->mInfo.xref[xref].cross_reference}
								</span>
								<span class="actionicon">
									{smartlink ititle="Edit" ifile="edit_xref.php" booticon="icon-note-edit" source=$gContent->mInfo.xref[xref].source xref=$gContent->mInfo.xref[xref].cross_reference}								</span>
								</span>
								<label for="ev_{$gContent->mInfo.xref[xref].cross_reference}">	
									{$gContent->mInfo.xref[xref].cross_reference}
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
