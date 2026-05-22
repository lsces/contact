{strip}
<div class="display fisheye contact-gallery container-fluid">
	{if $gGallery->mGalleryId}
		<div class="row">
		{counter assign="imageCount" start="0" print=false}
		{foreach from=$gGallery->mItems item=galItem key=itemContentId}
			<div class="col-md-3 col-sm-6 col-xs-12"> <!-- Begin Image Cell -->
				<div class="gallery-box">
					<a href="{$galItem->getDisplayUrl()|escape}">
						<div class="gallery-img">
							<img class="img-responsive thumb" src="{$galItem->getThumbnailUri()}" alt="{$galItem->mInfo.title|escape|default:'image'}" />
						</div>
						{if $galItem->mInfo.title}
							<div class="gallery-img-title center">
								<small>{$galItem->mInfo.title|escape}</small>
							</div>
						{/if}
					</a>
				</div>
			</div> <!-- End Image Cell -->
			{counter}
		{foreachelse}
			<div class="col-xs-12 norecords">{tr}Gallery is empty{/tr}. <a href="{$smarty.const.FISHEYE_PKG_URL}upload.php?gallery_id={$gGallery->mGalleryId}">Upload pictures!</a></div>
		{/foreach}
		</div>
		<div class="row">
			<div class="col-xs-12 text-right">
				<a href="{$smarty.const.FISHEYE_PKG_URL}gallery.php?gallery_id={$gGallery->mGalleryId}">{tr}View full gallery{/tr}</a>
			</div>
		</div>
	{else}
		<p class="norecords">{tr}No gallery for this contact{/tr}. <a href="{$smarty.const.FISHEYE_PKG_URL}create.php?title={$gContent->mInfo.organisation|escape}&amp;contact={$gContent->mInfo.content_id}">Create Contact Gallery!</a></p>
	{/if}
</div>
{/strip}
