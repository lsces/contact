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
				<li>{smartlink ititle="Contract" isort="xkey" ihash=$listInfo.ihash}</li>		
		{*		<li>{smartlink ititle="Forename" isort="forename" ihash=$listInfo.ihash}</li>
				<li>{smartlink ititle="Surname" isort="surname" ihash=$listInfo.ihash}</li>		*}
				<li>{smartlink ititle="Title" isort="title" idefault=1 iorder=desc ihash=$listInfo.ihash}</li>
		{*		<li>{smartlink ititle="Address" isort="street" ihash=$listInfo.ihash}</li>
				<li>{smartlink ititle="Town" isort="town" ihash=$listInfo.ihash}</li>			*}
				<li>{smartlink ititle="Location" isort="location" ihash=$listInfo.ihash}</li>
				<li>{smartlink ititle="Postcode" isort="postcode" ihash=$listInfo.ihash}</li>
			</ul>
		</div>

		<ul class="clear data">
			{section name=content loop=$listcontacts}
				<li class="item {cycle values='odd,even'}">
						<a href="display_contact.php?content_id={$listcontacts[content].content_id}" title="ci_{$listcontacts[content].content_id}">
						{$listcontacts[content].xkey}&nbsp;-&nbsp;
						{$listcontacts[content].title}
						</a>&nbsp;&nbsp;&nbsp;
						{if isset($listcontacts[content].organisation) && ($listcontacts[content].organisation <> '') }Company: {$listcontacts[content].organisation}&nbsp;&nbsp;{/if} 
						{if isset($listcontacts[content].dob) && ($listcontacts[content].dob <> '')  }DOB: {$listcontacts[content].dob}&nbsp;&nbsp;{/if}
						{if isset($listcontacts[content].nino) && ($listcontacts[content].nino <> '') }NI: {$listcontacts[content].nino}&nbsp;&nbsp;{/if}
						{if $gBitSystem->isFeatureActive( 'contact_list_last_modified' ) }Edited: {$listcontacts[content].last_modified|bit_short_date}&nbsp;&nbsp;{/if}
						
					<div class="footer">
						{if isset($listcontacts[content].uprn) && ($listcontacts[content].uprn <> '') }UPRN: {$listcontacts[content].uprn}&nbsp;&nbsp;{/if}
						{if isset($listcontacts[content].postcode) && ($listcontacts[content].postcode <> '') }
							{tr}Address{/tr}&nbsp;-&nbsp; 
							{if isset($listcontacts[content].house) && ($listcontacts[content].house <> '') }
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
							{if isset($listcontacts[content].county) && ($listcontacts[content].county <> '') }
								{$listcontacts[content].county},&nbsp;{/if}
							{$listcontacts[content].postcode}&nbsp;&nbsp;
							{if isset($listcontacts[content].grideast) }
								&lt;{$listcontacts[content].grideast|escape}&nbsp;,&nbsp;{$listcontacts[content].gridnorth|escape}&gt;&nbsp;{/if}
						{/if}
						{if $listcontacts[content].x_coordinate }
								&nbsp;Exact Cords:&lt;{$listcontacts[content].x_coordinate|escape}&nbsp;,&nbsp;{$listcontacts[content].y_coordinate|escape}&gt;&nbsp;
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
