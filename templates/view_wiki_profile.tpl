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

				{* Biography facts (dob/dod/pob/pod for an individual, formed/disbanded for a group) -
				   whichever items the 'biography' group actually has set, straight from mXrefInfo,
				   same data the xref tabs below use - no hardcoded item list, so a new biography
				   item added to the schema later just appears here with no template change. *}
				{foreach from=$gXrefInfo->mGroups item=xrefGroup}
					{if $xrefGroup->mXGroup eq 'biography' && $xrefGroup->mXrefs|@count}
						<p class="wiki-profile-dates">
							{foreach from=$xrefGroup->mXrefs item=xrefInfo name=bioFacts}
								<strong>{$xrefInfo.xref_title|escape}:</strong> {$xrefInfo.xkey_ext|default:$xrefInfo.xkey|escape}{if !$smarty.foreach.bioFacts.last}&nbsp;&middot;&nbsp;{/if}
							{/foreach}
						</p>
					{/if}
				{/foreach}

				{if $gContent->mInfo.data}
					<div class="wiki-profile-bio">{$gContent->mInfo.data}</div>
				{/if}

				{* External identity links - every item currently set in the 'external' group, same
				   xkey-falling-back-to-xkey_ext href-building as liberty's own view_href_item.tpl (a
				   MusicBrainz/URL-shaped value lives in xkey_ext, everything else in xkey). Adding a
				   new external source to the schema just shows up here, nothing to change. *}
				{foreach from=$gXrefInfo->mGroups item=xrefGroup}
					{if $xrefGroup->mXGroup eq 'external' && $xrefGroup->mXrefs|@count}
						<p class="wiki-profile-links">
							{foreach from=$xrefGroup->mXrefs item=xrefInfo name=extLinks}
								{if $xrefInfo.cross_ref_href && ( $xrefInfo.xkey || $xrefInfo.xkey_ext )}
									<a href="{$xrefInfo.cross_ref_href|escape}{$xrefInfo.xkey|default:$xrefInfo.xkey_ext|escape}" target="_blank" rel="noopener">{$xrefInfo.xref_title|escape}</a>{if !$smarty.foreach.extLinks.last} &middot; {/if}
								{/if}
							{/foreach}
						</p>
					{/if}
				{/foreach}
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

		{* The full xref tab grid (raw group/item rows, including things like the 'images' and
		   'gallery' groups this profile doesn't otherwise surface) is deliberately NOT repeated
		   here - it's already one click away on the edit page, and duplicating it on view just
		   re-shows the same data this profile has already presented more readably above. *}
	</div><!-- end .body -->
</div><!-- end .contact -->
{/strip}
