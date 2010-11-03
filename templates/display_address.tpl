		<div class="row">
			{formlabel label="$header" for="lpi"}
			{forminput}
				{if isset($pageInfo.sao) && ($pageInfo.sao <> '') }
					{$pageInfo.sao},&nbsp;{/if}
				{if isset($pageInfo.pao) && ($pageInfo.pao <> '') }
					{$pageInfo.pao},<br />{/if}
				{if isset($pageInfo.number) && ($pageInfo.number <> '') }
					{$pageInfo.number},<br />{/if}
				{if isset($pageInfo.street) && ($pageInfo.street <> '') }
					{$pageInfo.street},<br />{/if}
				{if isset($pageInfo.locality) && ($pageInfo.locality <> '') }
					{$pageInfo.locality},&nbsp;{/if}
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
				&nbsp;&lt;<a href="http://www.multimap.com/maps/?map={$pageInfo.prop_lat},{$pageInfo.prop_lng}|17|4&loc=GB:{$pageInfo.prop_lat}:{$pageInfo.prop_lng}:17" title="{$pageInfo.title}">
					Multimap
				</a>&gt;<br />
				{$pageInfo.rpa|escape}
			{/forminput}
		</div>
		{/if}
		
