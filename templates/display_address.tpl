		<div class="row">
			{formlabel label="$header" for="lpi"}
			{forminput}
				{if isset($pageInfo.sao) && ($pageInfo.sao <> '') }
					{$pageInfo.sao},&nbsp;{/if}
				{if isset($pageInfo.pao) && ($pageInfo.pao <> '') }
					{$pageInfo.pao},<br />{/if}
				{if isset($pageInfo.number) && ($pageInfo.number <> '') }
					{$pageInfo.number},<br />{/if}
				{if isset($pageInfo.house) && ($pageInfo.house <> '') }
					{$pageInfo.house},<br />{/if}
				{if isset($pageInfo.add1) && ($pageInfo.add1 <> '') }
					{$pageInfo.add1},<br />{/if}
				{if isset($pageInfo.add2) && ($pageInfo.add2 <> '') }
					{$pageInfo.add2},&nbsp;{/if}
				{if isset($pageInfo.add3) && ($pageInfo.add3 <> '') }
					{$pageInfo.add3},<br />{/if}
				{if isset($pageInfo.add4) && ($pageInfo.add4 <> '') }
					{$pageInfo.add4},&nbsp;{/if}
				{if isset($pageInfo.town) && ($pageInfo.town <> '') }
					{$pageInfo.town},&nbsp;{/if}
				{if isset($pageInfo.county) && ($pageInfo.county <> '') }
					{$pageInfo.county},&nbsp;{/if}
				{$pageInfo.postcode}&nbsp;&nbsp;
			{/forminput}
		</div>
		{if isset($pageInfo.x_coordinate) && ($pageInfo.x_coordinate <> '') }
		<div class="row">
			{formlabel label="Visual Centre Coordinates" for="street_start_x"}
			{forminput}
				Easting: {$pageInfo.x_coordinate|escape} Northing: {$pageInfo.y_coordinate|escape}
				&nbsp;&lt;<a href="http://www.openstreetmap.org/index.html?mlat={$pageInfo.x_coordinate}&mlon={$pageInfo.y_coordinate}&zoom=16&layers=BOFT" title="{$propertyInfo.title}">
					OpenStreetMap
				</a>&gt;&nbsp;&lt;<a href="http://www.bing.com/maps/?cp={$pageInfo.x_coordinate}~{$pageInfo.y_coordinate}&lvl=16&style=r#" title="{$pageInfo.title}">
					Multimap
				</a>&gt;&nbsp;&lt;<a href="http://www.google.co.uk/maps?f=q&source=s_q&hl=en&ll={$pageInfo.x_coordinate},{$pageInfo.y_coordinate}&z=16" title="{$pageInfo.title}">
					Google
				</a>&gt;<br />
				{$pageInfo.rpa|escape}
			{/forminput}
		</div>
		{/if}
		
