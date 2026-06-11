{strip}

<div class="floaticon">{bithelp}</div>

<div class="listing contacts">
	<div class="header">
		<h1>{if $listTitle}{$listTitle|escape}{else}{tr}Contacts{/tr}{/if}</h1>
	</div>

	<div class="body">

		{include file="bitpackage:contact/display_list_header.tpl"}
	<div class="table-responsive">
		<table class="col-xs-12">
			<caption>{tr}Available Content{/tr} <span class="total">[ {$listInfo.total_records} ]</span></caption>
			<tr>
				<th>{smartlink ititle="Title" isort="title" idefault=1 iorder=desc ihash=$listInfo.ihash|default:''}</th>
				<th>{tr}Address{/tr}</th>
			</tr>
			{section name=content loop=$listcontacts}
				{if $smarty.section.content.rownum % 2 != 0}{assign var=rowclass value="odd"}{else}{assign var=rowclass value="even"}{/if}
				<tr class="first {$rowclass}">
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
				<tr class="second {$rowclass}">
					<td colspan="2">
						{tr}Refs{/tr}: {$listcontacts[content].refs|default:0}&nbsp;&nbsp;
						{tr}Tasks{/tr}: {$listcontacts[content].tasks|default:0}&nbsp;&nbsp;
						{tr}Addresses{/tr}: {$listcontacts[content].addresses|default:0}
					</td>
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
