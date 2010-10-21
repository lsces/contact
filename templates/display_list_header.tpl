	<div class="navbar">
		{form  class="find" legend="Find in Contact entries" id="data_options"}
			{foreach from=$hidden item=value key=name}
				<input type="hidden" name="{$name}" value="{$value}" />
			{/foreach}
			<input type="hidden" name="sort_mode" value="{$sort_mode|default:$smarty.request.sort_mode}" />

			{include file="bitpackage:contact/contact_options_inc.tpl"}
			<input type="submit" name="refresh" value="{tr}Update Contact Filter{/tr}" />&nbsp;&nbsp;&nbsp;&nbsp;
			{if $gBitUser->hasPermission('p_edit_contact')}
				<input type="hidden" name="active" value="{$active|default:$smarty.request.active}" />
				<input type="submit" name="active" value="{tr}Active{/tr}" />&nbsp;
				<input type="submit" name="active" value="{tr}Inactive{/tr}" />&nbsp;
				<input type="submit" name="active" value="{tr}All{/tr}" />
			{/if}
			<br /><br />

			{biticon ipackage="icons" iname="edit-find" iexplain="Search"} &nbsp;
			<label>{tr}Key{/tr}:&nbsp;<input size="6" type="text" name="find_key" value="{$find_key|default:$smarty.request.find_key|escape}" /></label> &nbsp;
			<label>{tr}Title{/tr}:&nbsp;<input size="30" type="text" name="find_title" value="{$find_title|default:$smarty.request.find_title|escape}" /></label> &nbsp;
			<label>{tr}Location{/tr}:&nbsp;<input size="30" type="text" name="find_location" value="{$find_location|default:$smarty.request.find_location|escape}" /></label> &nbsp;
			<label>{tr}Postcode{/tr}:&nbsp;<input size="10" type="text" name="find_postcode" value="{$find_postcode|default:$smarty.request.find_postcode|escape}" /></label> &nbsp;
			<input type="submit" name="search" value="{tr}Find{/tr}" />&nbsp;
			<input type="button" onclick="location.href='{$smarty.server.PHP_SELF}{if $hidden}?{/if}{foreach from=$hidden item=value key=name}{$name}={$value}&amp;{/foreach}'" value="{tr}Reset{/tr}" />
		{/form}
	</div>