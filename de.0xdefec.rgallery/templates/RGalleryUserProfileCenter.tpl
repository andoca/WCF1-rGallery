<link rel="stylesheet" type="text/css" media="screen" href="{@RELATIVE_WBB_DIR}style/rGallery.css">
<script type='text/javascript' src='{@RELATIVE_WBB_DIR}js/prototip-min.js'></script>

<div class="contentBox">
	<h3 class="subHeadline"><a href="index.php?page=RGallery{@SID_ARG_2ND}">{lang}de.0xdefec.rgallery.maintitle{/lang}</h3>
	
	<ul class="dataList floatContainer container-1 gallery-thumbs">
			{if $has_elements != 0}
				{foreach from=$itemArray item=value key=key}
					<li style="float: left">
						<div class="thumb">
							<a id="rG_item_name_{$value.itemID}" href="index.php?page=RGalleryImageWrapper&amp;itemID={$value.itemID}&amp;type=page{@SID_ARG_2ND}"><img id="rG_item_{$value.itemID}" class="element" style="height:{RGALLERY_THUMB_SIZE}px;width:{RGALLERY_THUMB_SIZE}px" src="index.php?page=RGalleryImageWrapper&amp;itemID={$value.itemID}&amp;type=thumb{@SID_ARG_2ND}" alt="" /></a>
						</div>
						<div class="meta">
							{if $value.commentsCount != 0}<strong>{$value.commentsCount}</strong> {lang}de.0xdefec.rgallery.comment_s{/lang}
							{else}<br />{/if}
						</div>
					</li>
					{if RGALLERY_SHOW_TOOLTIPS == 1}
						<script type="text/javascript">
							//<![CDATA[									
								new Tip("rG_item_{$value.itemID}", 
									'{include file='RGalleryDHTMLPreview'}',
									{ 	effect: 'appear',
										delay: 0.3,
										title: false,
										viewport: true});
							//]]>
						</script>
					{/if}
				{/foreach}
				<div style="clear:both"></div>
			{else}
				{lang}de.0xdefec.rgallery.no_images_yet{/lang}
			{/if}
	</ul>
	
	<div class="buttonBar">
		<div class="smallButtons">
			<ul>
				<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /> <span class="hidden">{lang}wcf.global.scrollUp{/lang}</span></a></li>
				<li><a href="index.php?page=RGalleryUserGallery&userID={@$user->userID}{@SID_ARG_2ND}"><img src="{icon}rGalleryS.png{/icon}" alt="{lang}de.0xdefec.rgallery.maintitle{/lang}" /> {lang}de.0xdefec.rgallery.maintitle{/lang}</a></li>
			</ul>
		</div>
	</div>
</div>