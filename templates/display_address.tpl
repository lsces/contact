		<div class="row">
			{formlabel label="$header" for="lpi"}
			{forminput}
				{if isset($address.sao) && ($address.sao <> '') }
					{$address.sao},&nbsp;{/if}
				{if isset($address.pao) && ($address.pao <> '') }
					{$address.pao},<br />{/if}
				{if isset($address.number) && ($address.number <> '') }
					{$address.number},<br />{/if}
				{if isset($address.house) && ($address.house <> '') }
					{$address.house},<br />{/if}
				{if isset($address.add1) && ($address.add1 <> '') }
					{$address.add1},<br />{/if}
				{if isset($address.add2) && ($address.add2 <> '') }
					{$address.add2},&nbsp;{/if}
				{if isset($address.add3) && ($address.add3 <> '') }
					{$address.add3},<br />{/if}
				{if isset($address.add4) && ($address.add4 <> '') }
					{$address.add4},&nbsp;{/if}
				{if isset($address.town) && ($address.town <> '') }
					{$address.town},&nbsp;{/if}
				{if isset($address.county) && ($address.county <> '') }
					{$address.county},&nbsp;{/if}
				{$address.postcode}&nbsp;&nbsp;
			{/forminput}
		</div>
		{if $locate == 1 && isset($address.x_coordinate) && ($address.x_coordinate <> '') }
		<div class="row">
			{formlabel label="Visual Centre Coordinates" for="street_start_x"}
			{forminput}
				Lat: {$address.x_coordinate|escape} Lon: {$address.y_coordinate|escape}
				&nbsp;&lt;<a href="http://www.openstreetmap.org/index.html?mlat={$address.x_coordinate}&mlon={$address.y_coordinate}&zoom=16&layers=BOFT" title="{$propertyInfo.title}">
					OpenStreetMap
				</a>&gt;&nbsp;&lt;<a href="http://www.bing.com/maps/?cp={$address.x_coordinate}~{$address.y_coordinate}&lvl=16&style=r&q={$address.postcode}#" title="{$address.title}">
					Multimap
				</a>&gt;&nbsp;&lt;<a href="http://www.google.co.uk/maps?f=q&source=s_q&hl=en&q={$address.postcode}&ll={$address.x_coordinate},{$address.y_coordinate}&z=16" title="{$address.title}">
					Google
				</a>&gt;<br />
				Navigate to &lt;<a href="http://openrouteservice.org/index.php?start=-1.822164,52.073197&end={$address.y_coordinate},{$address.x_coordinate}&pref=Fastest&lang=en&unit=MI" title="{$propertyInfo.title}">
					OpenRouteService
				</a>&gt;
				{$address.rpa|escape}
			{/forminput}
		</div>
		{/if}
		
