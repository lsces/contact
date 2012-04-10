<div class="header">
{if $is_categorized eq 'y' and $gBitSystem->isFeatureActive('package_categories') and $gBitSystem->isFeatureActive('feature_categorypath')}
<div class="category">
  <div class="path">{$display_catpath}</div>
</div> {* end category *}
{/if}

	<h1>{$pageInfo.xkey}&nbsp;-&nbsp;
{*		{if isset($pageInfo.organisation) && ($pageInfo.organisation <> '') }
			{$pageInfo.organisation}
		{elseif isset($pageInfo.surname) && ($pageInfo.surname <> '') }
			{$pageInfo.prefix}&nbsp;
			{$pageInfo.forename}&nbsp;
			{$pageInfo.surname}
		{else}  *}
			{$pageInfo.title}
{*		{/if}  *}
	</h1>
	<div class="description">{$pageInfo.description}</div>

</div> {* end .header *}
