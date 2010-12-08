<table class="data">
{cycle values="even,odd" print=false}
{section name=changes loop=$client_list}
<tr class="{cycle}">
<td><a href="{$gBitLoc.CONTACT_PKG_URL}index.php?content_id={$client_list[changes].content_id|escape:"url"}" title="{$client_list[changes].content_id}">{$client_list[changes].title}</a>
	{if $gBitUser->hasPermission('p_edit_contact')}
		<br />(<a href="{$gBitLoc.CONTACT_PKG_URL}edit.php?content_id={$client_list[changes].content_id|escape:"url"}">{tr}edit{/tr}</a>)
	{/if}
</td>
	<td style="text-align:center;"></td>
{if $client_list_title eq 'y'}
	<td style="text-align:center;">{$client_list[changes].town}</td>
	<td style="text-align:center;">{$client_list[changes].county}</td>
	<td style="text-align:center;">{$client_list[changes].postcode}<br />
	{$client_list[changes].pcdetail}</td>
{/if}
<td style="text-align:center;">{$client_list[changes].data}</td>
</tr>
{sectionelse}
	<tr class="norecords"><td colspan="16">
		{tr}No records found{/tr}
	</td></tr>
{/section}
</table>
