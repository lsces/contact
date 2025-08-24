	<div class="navbar">
		{form  class="find" legend="Find in Contact entries" id="data_options"}
			{foreach from=$hidden item=value key=name}
				<input type="hidden" name="{$name}" value="{$value}" />
			{/foreach}
			<input type="hidden" name="sort_mode" value="{$sort_mode|default:$smarty.request.sort_mode}" />

			<label class="col-md-1 col-sm-3 col-xs-12">{tr}Key{/tr}:&nbsp;<input size="4" type="text" name="find_key" value="{$find_key|default:$smarty.request.find_key|escape}" /></label>
			<label class="col-md-3 col-sm-3 col-xs-12">{tr}Title{/tr}:&nbsp;<input size="20" type="text" name="find_title" value="{$find_title|default:$smarty.request.find_title|escape}" /></label>
			<label class="col-md-3 col-sm-3 col-xs-12">{tr}Location{/tr}:&nbsp;<input size="20" type="text" name="find_location" value="{$find_location|default:$smarty.request.find_location|escape}" /></label>
			<label class="col-md-2 col-sm-3 col-xs-12">{tr}Postcode{/tr}:&nbsp;<input size="10" type="text" name="find_postcode" value="{$find_postcode|default:$smarty.request.find_postcode|escape}" /></label>
			<label class="col-md-2 col-sm-3 col-xs-12">{tr}Number{/tr}:&nbsp;<input size="10" type="text" name="find_xref" value="{$find_xref|default:$smarty.request.find_xref|escape}" /></label>
			<div  class="col-md-1 col-sm-3 col-xs-12">
				<input type="submit" name="search" value="{tr}Find{/tr}" />&nbsp;
				<input type="button" onclick="location.href='{$smarty.server.PHP_SELF}{if $hidden}?{/if}{foreach from=$hidden item=value key=name}{$name}={$value}&amp;{/foreach}'" value="{tr}Reset{/tr}" />
			</div>
		{/form}
	</div>