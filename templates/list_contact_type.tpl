{if isset( $pageInfo.xref_type ) }
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
					{section name=xref loop=$pageInfo.xref_type}
						<tr class="{cycle values="even,odd"}" title="{$list[county].title|escape}">
							<td>
								{$pageInfo.xref0[xref].source_title|escape}
							</td>
							<td>
								{$pageInfo.xref0[xref].last_update_date|bit_short_date}
							</td>
							<td>
								{smartlink ititle="Add additional contact types" ifile="add_xref.php" ibiticon="icons/bookmark-new" content_id=$pageInfo.content_id}
							</td>
						</tr>
					{/section}
				</tbody>
			</table>
		</div>
{/if}