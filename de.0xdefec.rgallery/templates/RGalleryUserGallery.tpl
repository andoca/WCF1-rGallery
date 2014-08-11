{include file='documentHeader' sandbox='false'}
<head>
	<title>{lang}de.0xdefec.rgallery.maintitle{/lang} - {PAGE_TITLE}</title>
	<link rel="stylesheet" type="text/css" media="screen" href="{@RELATIVE_WBB_DIR}style/rGallery.css">
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
	<script type='text/javascript' src='{@RELATIVE_WBB_DIR}js/prototip-min.js'></script>
	{include file='headInclude' sandbox=false}
	<style type="text/css">
	.prototip .default .content { 
		width: {RGALLERY_PREVIEW_SIZE_W}px;
		height: {RGALLERY_PREVIEW_SIZE_H}+50px;
		}
	</style>
</head>
<body>
{include file='header' sandbox=false}
<div id="main">
	<ul class="breadCrumbs">
		<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img src="icon/indexS.png" alt="" /> <span>{PAGE_TITLE}</span></a> &raquo;</li>
		<li><a href="index.php?page=RGallery&amp;rGalleryCat=none{@SID_ARG_2ND}"><img src="icon/rGalleryS.png" alt="" /> <span>{lang}de.0xdefec.rgallery.maintitle{/lang}</span></a> &raquo;</li>
	</ul>
	<div class="mainHeadline">
		<a href="index.php?page=RGallery&amp;rGalleryCat=none{@SID_ARG_2ND}"><img src="{@RELATIVE_WBB_DIR}icon/rGalleryL.png" alt="" title="{lang}de.0xdefec.rgallery.maintitle{/lang}" /></a>
		<div class="headlineContainer">
			<h2><a href="index.php?page=RGallery&amp;rGalleryCat=none{@SID_ARG_2ND}">{lang}de.0xdefec.rgallery.usercp_images_from{/lang}</a></h2>
		</div>
	</div>
	<div class="content border">
		{if $RGallery_items|empty}
			<div class="message content">
				<div class="messageInner">
					{lang}de.0xdefec.rgallery.no_images_yet{/lang}
				</div>
			</div>
		{else}
		<div class="container-1">
			<h2>{lang}de.0xdefec.rgallery.elements{/lang}</h2>
			<div class="border" style="padding: 10px">
			<ul class="gallery-thumbs">
				{foreach from=$RGallery_items item=value key=key}
					<li>
						<div class="thumb">
							<a id="rG_item_name_{$value.itemID}" href="index.php?page=RGalleryImageWrapper&amp;itemID={$value.itemID}&amp;type=page{@SID_ARG_2ND}">
								<img id="rG_item_{$value.itemID}" class="element" style="height:{RGALLERY_THUMB_SIZE}px;width:{RGALLERY_THUMB_SIZE}px" src="index.php?page=RGalleryImageWrapper&amp;itemID={$value.itemID}&amp;type=thumb{@SID_ARG_2ND}" alt="" />
							</a>
						</div>
						<div class="meta">
							{if $value.commentsCount != 0}<strong>{$value.commentsCount}</strong> {lang}de.0xdefec.rgallery.comment_s{/lang}
							{else}<br />{/if}
						</div>
					</li>
					<script type="text/javascript">
						//<![CDATA[
						{if RGALLERY_SHOW_TOOLTIPS == 1}
							new Tip("rG_item_{$value.itemID}", 
								'{include file='RGalleryDHTMLPreview'}',
								{ 	effect: 'appear',
									delay: 0.3,
									title: false,
									viewport: true});
						{/if}
						//]]>
					</script>
				{/foreach}
			</ul>
			<div style="clear: both"></div>
			</div>
			{pages page=$rGalleryPage pages=$totalpages link="index.php?page=RGalleryUserGallery&userID=$userID&rGalleryPage=%d"}<br />
		</div>
		{/if}
	</div>
{lang}de.0xdefec.rgallery.copyright{/lang}
</div>
{include file='footer' sandbox=false}
</body>
</html>