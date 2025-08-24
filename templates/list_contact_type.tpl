{if isset( $gContent->mInfo.xref_type ) }
		<div class="form-group">
			<table>
				<thead>
					<tr>
						<th>Contact Type</th>
						<th>Updated</th>
						<th>&nbsp:</th>
					</tr>
				</thead>
				<tbody>
					{section name=xref loop=$gContent->mInfo.xref_type}
						<tr class="{cycle values="even,odd"}" title="{$list[county].title|escape}">
							<td>
								{$gContent->mInfo.xref0[xref].source_title|escape}
							</td>
							<td>
								{$gContent->mInfo.xref0[xref].last_update_date|bit_short_date}
							</td>
							<td>
								{smartlink ititle="Add additional contact types" ifile="add_xref.php" booticon="icon-page-add" content_id=$gContent->mInfo.content_id}
							</td>
						</tr>
					{/section}
				</tbody>
			</table>
		</div>
{/if}