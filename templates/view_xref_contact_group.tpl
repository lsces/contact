{assign var=xrefAllowEdit value=$allow_edit|default:true}
{jstab title="`$xrefGroup->mTitle` ({$xrefGroup->mXrefs|@count})"}
{legend legend=$xrefGroup->mTitle}
<div class="form-group table-responsive">
	<table>
		<thead>
			<tr>
				<th style="width:30%">{tr}Type{/tr}</th>
				<th style="width:30%">{tr}Value{/tr}</th>
				<th style="width:40%">{tr}Note{/tr}</th>
				{if $xrefAllowEdit}<th>{tr}Added{/tr}</th><th>{tr}Edit{/tr}</th>{/if}
			</tr>
		</thead>
		<tbody>
			{if $xrefGroup->mXrefs}
				{foreach $xrefGroup->mXrefs as $xrefInfo}
					<tr class="{cycle values="even,odd"}">
						{include file=$gContent->getXrefRecordTemplate($xrefInfo.template)}
					</tr>
				{/foreach}
			{else}
				<tr class="norecords">
					<td colspan="{if $xrefAllowEdit}5{else}3{/if}">{tr}No {$xrefGroup->mTitle} records found{/tr}</td>
				</tr>
			{/if}
		</tbody>
	</table>
</div>
{if $allow_add && $gContent->isValid() && $gContent->hasUpdatePermission() && $xrefGroup->mXGroup ne 'history'}
	<div>
		{smartlink ititle="Add record" ipackage="liberty" ifile="add_xref.php" biticon="list-add" content_id=$gContent->mInfo.content_id group=$xrefGroup->mSortOrder}
	</div>
{/if}
{/legend}
{/jstab}
