{strip}
<div class="display contact wiki-profile">
	{include file="bitpackage:contact/contact_header.tpl"}
	{include file="bitpackage:contact/contact_date_bar.tpl"}

	<div class="body">
		<div class="row">
			<div class="col-md-8 wiki-profile-info">
				{if $roleFlags|@count}
					<p class="wiki-profile-roles">
						{foreach from=$roleFlags item=role}<span class="label label-default">{$role|escape}</span> {/foreach}
					</p>
				{/if}

				{if $wikiDob || $wikiDod}
					<p class="wiki-profile-dates">
						{if $wikiDob}<strong>{tr}Born{/tr}:</strong> {$wikiDob|escape}{/if}
						{if $wikiDod}&nbsp;&middot;&nbsp;<strong>{tr}Died{/tr}:</strong> {$wikiDod|escape}{/if}
					</p>
				{/if}

				{if $gContent->mInfo.data}
					<div class="wiki-profile-bio">{$gContent->mInfo.data}</div>
				{/if}

				{if $externalLinks|@count}
					<p class="wiki-profile-links">
						{foreach from=$externalLinks item=link name=externalLinks}
							<a href="{$link.url|escape}" target="_blank" rel="noopener">{$link.title|escape}</a>{if !$smarty.foreach.externalLinks.last} &middot; {/if}
						{/foreach}
					</p>
				{/if}
			</div>
			{if $wikiThumbnailUrl}
				<div class="col-md-4 wiki-profile-thumb">
					<img class="img-responsive" src="{$wikiThumbnailUrl|escape}" alt="{$gContent->getTitle()|escape}" />
				</div>
			{/if}
		</div>

		{if $gMusicGallery && $gMusicGallery->mGalleryId}
			<div class="wiki-profile-gallery">
				<h2>{tr}Discography{/tr}</h2>
				<div class="row">
					{foreach from=$gMusicGallery->mItems item=galItem}
						<div class="col-md-3 col-sm-6 col-xs-12">
							<div class="gallery-box">
								<a href="{$galItem->getDisplayUrl()|escape}">
									<div class="gallery-img">
										<img class="img-responsive thumb" src="{$galItem->getThumbnailUri()}" alt="{$galItem->mInfo.title|escape|default:'image'}" />
									</div>
									{if $galItem->mInfo.title}
										<div class="gallery-img-title center"><small>{$galItem->mInfo.title|escape}</small></div>
									{/if}
								</a>
							</div>
						</div>
					{/foreach}
				</div>
				<div class="row">
					<div class="col-xs-12 text-right">
						<a href="{$smarty.const.FISHEYE_PKG_URL}gallery.php?gallery_id={$gMusicGallery->mGalleryId}">{tr}View full gallery{/tr}</a>
					</div>
				</div>
			</div>
		{/if}

		{if $gXrefInfo->mGroups && $gContent->isValid()}
			{jstabs}
				{foreach $gXrefInfo->mGroups as $xrefGroup}
					{include file=$gContent->getXrefListTemplate($xrefGroup->mTemplate)
						xrefGroup=$xrefGroup
						allow_edit=false}
				{/foreach}
			{/jstabs}
		{/if}
	</div><!-- end .body -->
</div><!-- end .contact -->
{/strip}
