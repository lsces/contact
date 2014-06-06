{* if isset( $pageInfo.$source ) *}
		{assign var=xrefcnt value=$pageInfo.$source|@count}
		{jstab title="$source_title ($xrefcnt)"}
		{legend legend=$source_title}
		<div class="form-group">
			<table>
				<thead>
					<tr>
						<th>ID</th>
						<th>Link</th>
						<th>Data</th>
						<th>Note</th>
						{if $source ne 'history' }
							<th>Started</th>
						{else}	
							<th>Ended</th>
						{/if}	
						</td>
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
								{if isset($pageInfo.$source[xref].xref) && $pageInfo.$source[xref].xref <> '' && $pageInfo.$source[xref].xref > 100 }
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
							<td>
							{if $source ne 'history' }
								{$pageInfo.$source[xref].start_date|bit_short_date}
							{else}	
								{$pageInfo.$source[xref].end_date|bit_short_date}
							{/if}	
							</td>
							{if $gBitSystem->isFeatureActive( 'contact_list_last_modified' )}
								<td>
									{$pageInfo.xref[xref].last_update_date|bit_long_date}
								</td>
							{/if}
							<td>
								<span class="actionicon">
									{if $gBitUser->hasPermission( 'p_contact_view_detail' )}
										{smartlink ititle="View" ifile="view_xref.php" ibiticon="icons/view-fullscreen" content_id=$pageInfo.content_id xref_id=$pageInfo.$source[xref].xref_id}
									{/if}	
									{if $gBitUser->hasPermission( 'p_contact_update' ) and $source ne 'history' }
										{if $pageInfo.$source[xref].source eq 'KEY_B' }
											{smartlink ititle="Callout" ifile="edit_key_break.php" ibiticon="icons/edit-redo" expunge=0 content_id=$pageInfo.content_id xref_id=$pageInfo.$source[xref].xref_id}
											{smartlink ititle="Reseal" ifile="edit_key_break.php" ibiticon="icons/edit-undo" expunge=2 content_id=$pageInfo.content_id xref_id=$pageInfo.$source[xref].xref_id}
										{else}
											{smartlink ititle="Edit" ifile="edit_xref.php" ibiticon="icons/accessories-text-editor" content_id=$pageInfo.content_id xref_id=$pageInfo.$source[xref].xref_id}
										{/if}
									{/if}	
									{if $gBitUser->hasPermission( 'p_contact_expunge' ) and $pageInfo.$source[xref].source ne 'KEY_B' }
										{if $source eq 'history' }
											{smartlink ititle="Restore" ifile="edit_xref.php" ibiticon="icons/edit-undo" content_id=$pageInfo.content_id xref_id=$pageInfo.$source[xref].xref_id expunge=-1}
										{else}
											{smartlink ititle="Delete" ifile="edit_xref.php" ibiticon="icons/edit-delete" content_id=$pageInfo.content_id xref_id=$pageInfo.$source[xref].xref_id expunge=1}
										{/if}	
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
		{if $gBitUser->hasPermission('p_contact_update')}
			<div>
				{if $source ne 'history' }
					{smartlink ititle="Add additional detail record" ifile="add_xref.php" ibiticon="icons/bookmark-new" content_id=$pageInfo.content_id xref_type=$xref_type}
				{/if}	
			</div>
		{/if}
		{/legend}
		{/jstab}
{* /if *}