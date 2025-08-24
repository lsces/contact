{strip}

<div class="floaticon">{bithelp}</div>

<div class="listing contacts">
	<div class="header">
		<h1>{tr}Contacts{/tr}</h1>
	</div>

	<div class="body">

		{include file="bitpackage:contact/display_list_header.tpl"}
	<div class="table-responsive">
		<table class="col-xs-12">
			<caption>{tr}Available Content{/tr} <span class="total">[ {$listInfo.total_records} ]</span></caption>
			<tr>
				<th class="width2p">{smartlink ititle="Contract" isort="xkey" ihash=$listInfo.ihash|default:''}</th>
				<th>{smartlink ititle="Title" isort="title" idefault=1 iorder=desc ihash=$listInfo.ihash|default:''}</th>
				<th>{smartlink ititle="Address" isort="street" ihash=$listInfo.ihash|default:''}</th>
			</tr>
			{section name=content loop=$listcontacts}
				<tr class="first">
					<td class="alignright">
						<a href="display_contact.php?content_id={$listcontacts[content].content_id}" title="ci_{$listcontacts[content].content_id}">
						   {$listcontacts[content].xkey}
						</a>
					</td>
					<td class="alignleft">
						<a href="display_contact.php?content_id={$listcontacts[content].content_id}" title="ci_{$listcontacts[content].content_id}">
							{$listcontacts[content].title}
						</a>
					</td>
					<td>{if isset($listcontacts[content].house) && ($listcontacts[content].house <> '') }
						{$listcontacts[content].house},&nbsp;{/if}
						{if isset($listcontacts[content].add1) && ($listcontacts[content].add1 <> '') }
							{$listcontacts[content].add1},&nbsp;{/if}
						{if isset($listcontacts[content].add2) && ($listcontacts[content].add2 <> '') }
							{$listcontacts[content].add2},&nbsp;{/if}
						{if isset($listcontacts[content].add3) && ($listcontacts[content].add3 <> '') }
							{$listcontacts[content].add3},&nbsp;{/if}
						{if isset($listcontacts[content].add4) && ($listcontacts[content].add4 <> '') }
							{$listcontacts[content].add4},&nbsp;{/if}
						{if isset($listcontacts[content].town) && ($listcontacts[content].town <> '') }
							{$listcontacts[content].town},&nbsp;{/if}
						{$listcontacts[content].postcode}</td>
				</tr>
				<tr class="second">
					<td>{$item.display_link|default:'Not Set'}</td>
					<td>{assign var=content_type_guid value=$item.content_type_guid|default:'bitpage'}{$gLibertySystem->getContentTypeName($content_type_guid|default:'bitpage')}</td>
					<td>
						{tr}Refs{/tr}: {$listcontacts[content].refs|default:0}&nbsp;&nbsp;
						{tr}Tasks{/tr}: {$listcontacts[content].tasks|default:0}&nbsp;&nbsp;
						{tr}Addresses{/tr}: {$listcontacts[content].addresses|default:0}
					</td>
					{if $gBitUser->hasPermission( 'p_liberty_view_all_status' )}
						<td>{if isset($listcontacts[content].dob) && ($listcontacts[content].dob <> '')  }DOB: {$listcontacts[content].dob}{/if}</td>
						<td>{if isset($listcontacts[content].nino) && ($listcontacts[content].nino <> '') }NI: {$listcontacts[content].nino}{/if}</td>
					{/if}
				</tr>
			{sectionelse}
				<tr class="norecords">
					<td colspan="8">{tr}No records found{/tr}</td>
				</tr>
			{/section}
		</table>
		</div>
		{pagination}
	</div><!-- end .body -->
</div><!-- end .contacts -->

{/strip}
