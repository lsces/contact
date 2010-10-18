{* if isset( $pageInfo.$source ) *}
		{assign var=xrefcnt value=$pageInfo.$source|@count}
		{jstab title="$source_title ($xrefcnt)"}
		{legend legend=$source_title}
		<div class="row">
			<table>
				<thead>
					<tr>
						<th>ID</th>
						<th>Link</th>
						<th>Data</th>
						<th>Note</th>
						{if $gBitSystem->isFeatureActive( 'contact_list_last_modified' )}
							<th>Updated</th>
						{/if}
						<th>Edit</th>
					</tr>
				</thead>
				<tbody>
					{section name=xref loop=$pageInfo.$source}
						<tr class="{cycle values="even,odd"}" title="{$pageInfo.title|escape}">
							<td>
								{$pageInfo.$source[xref].source_title|escape}
							</td>
							<td>
								{if isset($pageInfo.$source[xref].xref) && ($pageInfo.$source[xref].xref <> '') }
									{$pageInfo.$source[xref].xref|escape}
									{smartlink ititle="Link to" ifile="display_contact.php" ibiticon="icons/accessories-text-editor" content_id=$pageInfo.$source[xref].xref}
								{else}
									------
								{/if}
							</td>
							<td>
								{$pageInfo.$source[xref].xkey|escape} {$pageInfo.$source[xref].xkey_ext|escape}
							</td>
							<td>
								{$pageInfo.$source[xref].data|escape}
							</td>
							{if $gBitSystem->isFeatureActive( 'contact_list_last_modified' )}
								<td>
									{$pageInfo.xref[xref].last_update_date|bit_long_date}
								</td>
							{/if}
							<td>
								<span class="actionicon">
									{if $gBitUser->hasPermission( 'p_contact_view_detail' )}
										{smartlink ititle="View" ifile="view_xref.php" ibiticon="icons/view-fullscreen" xref_id=$pageInfo.$source[xref].xref_id}
									{/if}	
									{if $gBitUser->hasPermission( 'p_contact_update' )}
										{smartlink ititle="Edit" ifile="edit_xref.php" ibiticon="icons/accessories-text-editor" xref_id=$pageInfo.$source[xref].xref_id}
									{/if}	
									{if $gBitUser->hasPermission( 'p_contact_expunge' )}
										{smartlink ititle="Delete" ifile="edit_xref.php" ibiticon="icons/edit-delete" xref_id=$pageInfo.$source[xref].xref_id expunge=1}
									{/if}	
								</span>
							</td>
						</tr>
					{sectionelse}
						<tr class="norecords">
							<td colspan="3">
								{tr}No {$source} records found{/tr}
							</td>
						</tr>
					{/section}
				</tbody>
			</table>
		</div>
		<div>
			{smartlink ititle="Add additional detail record" ifile="add_xref.php" ibiticon="icons/bookmark-new" content_id=$pageInfo.content_id xref_type=1}
		</div>
		{/legend}
		{/jstab}
{* /if *}