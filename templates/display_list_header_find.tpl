	<div class="navbar">
		{form  class="find" legend="Find in Contact entries" id="data_options"}
			{foreach from=$hidden item=value key=name}
				<input type="hidden" name="{$name}" value="{$value}" />
			{/foreach}
			<input type="hidden" name="sort_mode" value="{$sort_mode|default:$smarty.request.sort_mode}" />

			<label class="col-md-5 col-sm-5 col-xs-12">{tr}Title{/tr}:&nbsp;<input size="20" type="text" name="find_title" value="{$find_title|default:$smarty.request.find_title|escape}" /></label>
			<label class="col-md-4 col-sm-4 col-xs-12">{tr}Number{/tr}:&nbsp;<input size="10" type="text" name="find_xref" value="{$find_xref|default:$smarty.request.find_xref|escape}" /></label>
			<div  class="col-md-1 col-sm-3 col-xs-12">
				<input type="submit" name="search" value="{tr}Find{/tr}" />&nbsp;
				<input type="button" onclick="location.href='{$smarty.server.PHP_SELF}{if $hidden}?{/if}{foreach from=$hidden item=value key=name}{$name}={$value}&amp;{/foreach}'" value="{tr}Reset{/tr}" />
			</div>
		{/form}
	</div>
