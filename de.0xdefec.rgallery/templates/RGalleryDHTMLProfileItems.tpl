
		<ul class="gallery-thumbs">
			{if $has_elements != 0}
				{foreach from=$itemArray item=value key=key}
					<li>
						<div class="thumb">
							<a id="rG_item_name_{$value.itemID}" href="index.php?page=RGalleryImageWrapper&amp;itemID={$value.itemID}&amp;type=page{@SID_ARG_2ND}"><img id="rG_item_{$value.itemID}" class="element" style="height:{RGALLERY_THUMB_SIZE}px;width:{RGALLERY_THUMB_SIZE}px" src="index.php?page=RGalleryImageWrapper&amp;itemID={$value.itemID}&amp;type=thumb{@SID_ARG_2ND}" alt="" /></a>
						</div>
						<div class="meta">
							{if $value.commentsCount != 0}<strong>{$value.commentsCount}</strong> {lang}de.0xdefec.rgallery.comment_s{/lang}
							{else}<br />{/if}
						</div>
					</li>
				{/foreach}
				<div style="clear:both"></div>
				<a href="index.php?page=RGalleryUserGallery&amp;userID={$userID}{@SID_ARG_2ND}">{lang}de.0xdefec.rgallery.usercp_all{/lang}</a>
			{else}
				{lang}de.0xdefec.rgallery.no_images_yet{/lang}
			{/if}
		</ul>