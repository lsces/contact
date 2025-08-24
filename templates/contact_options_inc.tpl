{strip}
{foreach from=$contContactTypes key=value item=type}
			<input type="checkbox" value="{$value}" name="contact_type_guid[]"
				{foreach from=$smarty.session.contact.contact_type_guid item=selected}
					{if $selected eq $value}
						checked="checked"
					{/if}
				{/foreach}
			/> {$type}&nbsp;&nbsp;
{/foreach}
<script>/* <![CDATA[ */
	document.write("<label><input name=\"switcher\" id=\"switcher\" type=\"checkbox\" onclick=\"BitBase.switchCheckboxes(this.form.id,'contact_type_guid[]','switcher')\" /> {tr}Select all{/tr}</label><br />");
/* ]]> */</script>
	<input type="submit" name="refresh" value="{tr}Update Contact Filter{/tr}" />&nbsp;&nbsp;&nbsp;&nbsp;
{/strip}
