{* if isset( $gContent->mInfo.$source ) *}
		{assign var=xrefcnt value=$gContent->mInfo.$source|default:[]|@count}
		{jstab title="$source_title ($xrefcnt)"}
		{legend legend=$source_title}
		<div class="form-group table-responsive">
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
					{section name=xref loop=$gContent->mInfo.$source}
						<tr class="{cycle values="even,odd"}" title="{$gContent->mInfo.title|escape}">
							{include file="bitpackage:contact/view_xref_`$gContent->mInfo.$source[xref].template`_record.tpl"}
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
					{smartlink ititle="Add additional detail record" ifile="add_xref.php" booticon="icon-note-add" content_id=$gContent->mInfo.content_id xref_type=$xref_type}
				{/if}	
			</div>
		{/if}
		{/legend}
		{/jstab}
{* /if *}
