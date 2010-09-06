		<div class="row">
			{formlabel label="Address" for="lpi"}
			{forminput}
				{if isset($contactInfo.sao) && ($contactInfo.sao <> '') }
					{$contactInfo.sao},&nbsp;{/if}
				{if isset($contactInfo.pao) && ($contactInfo.pao <> '') }
					{$contactInfo.pao},<br />{/if}
				{if isset($contactInfo.number) && ($contactInfo.number <> '') }
					{$contactInfo.number},<br />{/if}
				{if isset($contactInfo.street) && ($contactInfo.street <> '') }
					{$contactInfo.street},<br />{/if}
				{if isset($contactInfo.locality) && ($contactInfo.locality <> '') }
					{$contactInfo.locality},&nbsp;{/if}
				{if isset($contactInfo.town) && ($contactInfo.town <> '') }
					{$contactInfo.town},&nbsp;{/if}
				{if isset($contactInfo.county) && ($contactInfo.county <> '') }
					{$contactInfo.county},&nbsp;{/if}
				{$contactInfo.postcode}&nbsp;&nbsp;
			{/forminput}
		</div>
		{if isset($contactInfo.x_coordinate) && ($contactInfo.x_coordinate <> '') }
		<div class="row">
			{formlabel label="Visual Centre Coordinates" for="street_start_x"}
			{forminput}
				Easting: {$contactInfo.x_coordinate|escape} Northing: {$contactInfo.y_coordinate|escape}
				&nbsp;&lt;<a href="http://www.multimap.com/maps/?map={$contactInfo.prop_lat},{$contactInfo.prop_lng}|17|4&loc=GB:{$contactInfo.prop_lat}:{$contactInfo.prop_lng}:17" title="{$contactInfo.title}">
					Multimap
				</a>&gt;<br />
				{$contactInfo.rpa|escape}
			{/forminput}
		</div>
		{/if}
		
