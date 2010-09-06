<div class="header">
{if $is_categorized eq 'y' and $gBitSystem->isFeatureActive('package_categories') and $gBitSystem->isFeatureActive('feature_categorypath')}
<div class="category">
  <div class="path">{$display_catpath}</div>
</div> {* end category *}
{/if}

	<h1>{$contactInfo.content_id}&nbsp;-&nbsp;
		{if isset($contactInfo.organisation) && ($contactInfo.organisation <> '') }
		{$contactInfo.organisation}
		{else}
		{$contactInfo.prefix}&nbsp;
		{$contactInfo.forename}&nbsp;
		{$contactInfo.surname}
		{/if}</h1>
	<div class="description">{$contactInfo.description}</div>

</div> {* end .header *}
