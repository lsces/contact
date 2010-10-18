{strip}

<div class="floaticon">{bithelp}</div>

<div class="listing contacts">
	<div class="header">
		<h1>{tr}Contacts{/tr}</h1>
	</div>

	<div class="body">

		{include file="bitpackage:contact/display_list_header.tpl"}

		<div class="navbar">
			<ul>
				<li>{biticon ipackage="icons" iname="emblem-symbolic-link" iexplain="sort by"}</li>
		{*		<li>{smartlink ititle="Contact Number" isort="content_id" idefault=1 iorder=desc ihash=$listInfo.ihash}</li>		
				<li>{smartlink ititle="Forename" isort="forename" ihash=$listInfo.ihash}</li>		*}
				<li>{smartlink ititle="Surname" isort="surname" ihash=$listInfo.ihash}</li>
				<li>{smartlink ititle="Organisation" isort="organisation" ihash=$listInfo.ihash}</li>
				<li>{smartlink ititle="Address" isort="street" ihash=$listInfo.ihash}</li>
				<li>{smartlink ititle="Town" isort="town" ihash=$listInfo.ihash}</li>
				<li>{smartlink ititle="Postcode" isort="postcode" ihash=$listInfo.ihash}</li>
			</ul>
		</div>

		<ul class="clear data">
			{section name=content loop=$listcontacts}
				<li class="item {cycle values='odd,even'}">
						<a href="display_contact.php?content_id={$listcontacts[content].content_id}" title="ci_{$listcontacts[content].content_id}">
						{$listcontacts[content].content_id}&nbsp;-&nbsp;
						{if isset($listcontacts[content].surname) && ($listcontacts[content].surname <> '') }
							{$listcontacts[content].prefix}&nbsp;
							{$listcontacts[content].forename}&nbsp;
							{$listcontacts[content].surname}
						{else}
							{$listcontacts[content].title}
						{/if}	 
						</a>&nbsp;&nbsp;&nbsp;
						{if isset($listcontacts[content].organisation) && ($listcontacts[content].organisation <> '') }Company: {$listcontacts[content].organisation}&nbsp;&nbsp;{/if} 
						{if isset($listcontacts[content].dob) && ($listcontacts[content].dob <> '')  }DOB: {$listcontacts[content].dob}&nbsp;&nbsp;{/if}
						{if isset($listcontacts[content].nino) && ($listcontacts[content].nino <> '') }NI: {$listcontacts[content].nino}&nbsp;&nbsp;{/if}
						{if $gBitSystem->isFeatureActive( 'contact_list_last_modified' ) }Edited: {$listcontacts[content].last_modified|bit_short_date}&nbsp;&nbsp;{/if}
						
					<div class="footer">
						{if isset($listcontacts[content].uprn) && ($listcontacts[content].uprn <> '') }UPRN: {$listcontacts[content].uprn}&nbsp;&nbsp;{/if}
						{if isset($listcontacts[content].postcode) && ($listcontacts[content].postcode <> '') }
							{tr}Address{/tr}&nbsp;-&nbsp; 
							{if isset($listcontacts[content].sao) && ($listcontacts[content].sao <> '') }
								{$listcontacts[content].sao},&nbsp;{/if}
							{if isset($listcontacts[content].pao) && ($listcontacts[content].pao <> '') }
								{$listcontacts[content].pao},&nbsp;{/if}
							{if isset($listcontacts[content].number) && ($listcontacts[content].number <> '') }
								{$listcontacts[content].number},&nbsp;{/if}
							{if isset($listcontacts[content].street) && ($listcontacts[content].street <> '') }
								{$listcontacts[content].street},&nbsp;{/if}
							{if isset($listcontacts[content].locality) && ($listcontacts[content].locality <> '') }
								{$listcontacts[content].locality},&nbsp;{/if}
							{if isset($listcontacts[content].town) && ($listcontacts[content].town <> '') }
								{$listcontacts[content].town},&nbsp;{/if}
							{if isset($listcontacts[content].county) && ($listcontacts[content].county <> '') }
								{$listcontacts[content].county},&nbsp;{/if}
							{$listcontacts[content].postcode}&nbsp;&nbsp;
						{/if}
						<br />
						{tr}Refs{/tr}: {$listcontacts[content].refs|default:0}&nbsp;&nbsp;
						{tr}Tasks{/tr}: {$listcontacts[content].tasks|default:0}&nbsp;&nbsp;
						{tr}Addresses{/tr}: {$listcontacts[content].addresses|default:0}
					</div>
					<div class="clear"></div>
				</li>
			{sectionelse}
				<li class="item norecords">
					{tr}No records found{/tr}
				</li>
			{/section}
		</ul>

		{pagination}
	</div><!-- end .body -->
</div><!-- end .irlist -->

{/strip}
