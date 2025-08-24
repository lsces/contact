<div class="header">
{if $is_categorized eq 'y' and $gBitSystem->isFeatureActive('package_categories') and $gBitSystem->isFeatureActive('feature_categorypath')}
<div class="category">
  <div class="path">{$display_catpath}</div>
</div> {* end category *}
{/if}

	<h1>{$gContent->mInfo.xkey}&nbsp;-&nbsp;
{*		{if isset(gContent->mInfo.organisation) && (gContent->mInfo.organisation <> '') }
			{$gContent->mInfo.organisation}
		{elseif isset(gContent->mInfo.surname) && (gContent->mInfo.surname <> '') }
			{$gContent->mInfo.prefix}&nbsp;
			{$gContent->mInfo.forename}&nbsp;
			{$gContent->mInfo.surname}
		{else}  *}
			{$gContent->mInfo.title}
{*		{/if}  *}
	</h1>
	<div class="description">{$gContent->mInfo.description|default:''}</div>

</div> {* end .header *}
