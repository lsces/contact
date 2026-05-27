<div class="header">
{if $is_categorized eq 'y' && $gBitSystem->isFeatureActive('package_categories') && $gBitSystem->isFeatureActive('feature_categorypath')}
<div class="category">
  <div class="path">{$display_catpath}</div>
</div> {* end category *}
{/if}

	<h1>{$gContent->mInfo.title}</h1>
	<div class="description">{$gContent->mInfo.description|default:''}</div>

</div> {* end .header *}
