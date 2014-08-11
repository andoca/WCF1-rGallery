{include file='documentHeader' sandbox='false'}
<head>
	<title>{lang}de.0xdefec.rgallery.maintitle{/lang} - {PAGE_TITLE}</title>
	<link rel="stylesheet" type="text/css" media="screen" href="{@RELATIVE_WBB_DIR}style/rGallery.css">
	{include file='headInclude' sandbox='false'}
	<script type='text/javascript' src='{@RELATIVE_WBB_DIR}js/prototip-min.js'></script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
	<style type="text/css">
	.prototip .default .content { 
		width: {RGALLERY_PREVIEW_SIZE_W}px;
		height: {RGALLERY_PREVIEW_SIZE_H}+50px;
		}
	</style>
</head>
<body>
{include file='header' sandbox='false'}
<div id="main">
	<ul class="breadCrumbs">
		<li><a href="index.php{@SID_ARG_1ST}"><img src="icon/indexS.png" alt="" /> <span>{PAGE_TITLE}</span></a> &raquo;</li>
		{if !$current_cat|empty}
			<li><a href="index.php?page=RGallery&amp;rGalleryCat=none{@SID_ARG_2ND}"><img src="icon/rGalleryS.png" alt="" /> <span>{lang}de.0xdefec.rgallery.maintitle{/lang}</span></a> &raquo;</li>
		{/if}
	</ul>
	
	<div class="mainHeadline">
		<a href="index.php?page=RGallery&amp;rGalleryCat=none{@SID_ARG_2ND}"><img src="{@RELATIVE_WBB_DIR}icon/rGalleryL.png" alt="" title="{lang}de.0xdefec.rgallery.maintitle{/lang}" /></a>
		<div class="headlineContainer">
			<h2><a href="index.php?page=RGallery{@SID_ARG_2ND}">{if !$current_cat|empty}{lang}de.0xdefec.rgallery.upload.categorie{/lang} <span><em>{$current_cat}</em></span>{else}{lang}de.0xdefec.rgallery.maintitle{/lang}{/if}</a></h2>
		</div>
	</div>
	
	<div class="tabMenu">
		<ul>
			<li {if $subpage == ''}class="activeTabMenu"{/if}>
				<a href="index.php?page=RGallery&amp;subpage={@SID_ARG_2ND}"><img src="{@RELATIVE_WBB_DIR}icon/rGalleryS.png" alt="" /> {lang}de.0xdefec.rgallery.elements{/lang}</a>
			</li>
			<li {if $subpage == 'categories'}class="activeTabMenu"{/if}>
				<a href="index.php?page=RGallery&amp;subpage=categories{@SID_ARG_2ND}"><img src="{@RELATIVE_WBB_DIR}icon/categoryS.png" alt="" /> {lang}de.0xdefec.rgallery.categories{/lang}</a>
			</li>
			{if $tagCloud != '<ol></ol>'}
				<li {if $subpage == 'tags'}class="activeTabMenu"{/if}>
					<a href="index.php?page=RGallery&amp;subpage=tags{@SID_ARG_2ND}"><img src="{@RELATIVE_WBB_DIR}icon/subscriptionsS.png" alt="" /> {lang}de.0xdefec.rgallery.tags{/lang}</a>
				</li>
			{/if}
			<li {if $subpage == 'users'}class="activeTabMenu"{/if}>
				<a href="index.php?page=RGallery&amp;subpage=users{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/usersS.png" alt="" /> {lang}de.0xdefec.rgallery.users{/lang}</a>
			</li>
			<li {if $subpage == 'stats'}class="activeTabMenu"{/if}>
				<a href="index.php?page=RGallery&amp;subpage=stats{@SID_ARG_2ND}"><img src="{@RELATIVE_WBB_DIR}icon/threadS.png" alt="" /> {lang}de.0xdefec.rgallery.stats{/lang}</a>
			</li>
		</ul>
	</div>
	<div class="subTabMenu">
		<div class="containerHead"></div>
	</div>
	
	<div class="border content">
		
		<div class="container-1">
			{*<div style="text-align: right;">
				<form name="changecat" action="{@RELATIVE_WBB_DIR}index.php" method="get">
					<input type="hidden" name="page" value="RGallery" />
					<input type="hidden" name="tag" value="{$active_tag}" />
					{lang}de.0xdefec.rgallery.upload.categorie{/lang}: <select name="rGalleryCat" onChange="changecat.submit()">
						<option value="none">&nbsp;</option>
						{htmloptions output=$RGalleryCats_name values=$RGalleryCats_value selected=$rGalleryCat}
						</select>
					{@SID_INPUT_TAG}
				</form>
			</div> *}
			
			
			{if $subpage == ''}
				{if $active_tag != ''}
					<div class="rGalleryTagsActive">{lang}de.0xdefec.rgallery.tag_active{/lang}</div>
				{/if}<br />
				<ul class="gallery-thumbs">
					{if $canUpload && $rGalleryPage == 1}
							<li>
								<div class="thumb">
									<a href="{@RELATIVE_WBB_DIR}index.php?page=RGalleryUser{@SID_ARG_2ND}">
										<div class="element" style="height:{RGALLERY_THUMB_SIZE}px;width:{RGALLERY_THUMB_SIZE}px">{lang}de.0xdefec.rgallery.clicktoadd{/lang}</div>
									</a>
								</div>
								<div class="meta">&nbsp;<br /></div>
							</li>
					{/if}
					{if $has_elements != 0}
						{foreach from=$itemArray item=value key=key}
							<li>
								<div class="thumb">
									<a id="rG_item_name_{$value.itemID}" href="{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&amp;itemID={$value.itemID}&amp;type=page{@SID_ARG_2ND}">
										<img id="rG_item_{$value.itemID}" class="element" style="height:{RGALLERY_THUMB_SIZE}px;width:{RGALLERY_THUMB_SIZE}px" src="index.php?page=RGalleryImageWrapper&amp;itemID={$value.itemID}&amp;type=thumb{@SID_ARG_2ND}" alt="" />
									</a>
								</div>
								<div class="meta">
										{* {lang}de.0xdefec.rgallery.by{/lang} <a href="{@RELATIVE_WBB_DIR}index.php?page=User&amp;userID={$value.ownerID}{@SID_ARG_2ND}">{$value.ownerName}</a><br /> *}
										{if $value.commentsCount != 0}
											<strong>{#$value.commentsCount}</strong> {lang}de.0xdefec.rgallery.comment_s{/lang}
										{/if}<br />
								</div>
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
							</li>
						{/foreach}
						<div class="clear"></div>
					{else}
						<div class="clear"></div>
						<p>{lang}de.0xdefec.rgallery.no_images_yet{/lang}</p>
					{/if}
				</ul>
				<p>
					{pages page=$rGalleryPage pages=$totalpages link="index.php?page=RGallery&tag=$active_tag&rGalleryPage=%d"}<br />
				</p>
			{elseif $subpage == 'categories'}
				<ul class="gallery-thumbs">
					{foreach from=$RGalleryCats_value item=value key=key}
							<li>
								<div class="thumb">
									<a href="{@RELATIVE_WBB_DIR}index.php?page=RGallery&amp;rGalleryCat={$value}{@SID_ARG_2ND}">
										<img class="element" style="height:{RGALLERY_THUMB_SIZE}px;width:{RGALLERY_THUMB_SIZE}px" src="index.php?page=RGalleryImageWrapper&amp;catID={$value}&amp;type=thumb{@SID_ARG_2ND}" alt="{$RGalleryCats_name.$value}" />
									</a>
								</div>
								<div class="meta">
									<div style="clear: both;text-align: center">
										{$RGalleryCats_name.$value|truncate:20:'..'}<br />
										{if $RGalleryCats_items.$value|isset}<strong>{#$RGalleryCats_items.$value}</strong> {lang}de.0xdefec.rgallery.element_s{/lang}{/if}<br />
									</div>
								</div>
							</li>
					{/foreach}
				</ul>
			{elseif $subpage == 'tags'}
				<div>{@$tagCloud}</div>
			{elseif $subpage == 'users'}
					{if $rGalleryUsers != 0}
						<ul class="gallery-thumbs">
						{foreach from=$rGalleryUsers item=value key=key}
							<li>
								<div class="thumb">
									<a href="{@RELATIVE_WBB_DIR}index.php?page=RGalleryUserGallery&amp;userID={$value.user->userID}{@SID_ARG_2ND}">
										<img class="element" style="height:{RGALLERY_THUMB_SIZE}px;width:{RGALLERY_THUMB_SIZE}px" src="index.php?page=RGalleryImageWrapper&amp;itemID={$value.highlight}&amp;type=thumb{@SID_ARG_2ND}" alt="{$value.highlight}" />
									</a>
								</div>
								<div class="meta">
									{#$value.items} {lang}de.0xdefec.rgallery.by{/lang} <a href="{@RELATIVE_WBB_DIR}index.php?page=User&amp;userID={$value.user->userID}{@SID_ARG_2ND}">{$value.user->username}</a><br />
									
								</div>
							</li>
						{/foreach}
						</ul>
						<p>
							{pages page=$cur_page pages=$pages link="index.php?page=RGallery&subpage=users&rGalleryPage=%d"}<br />
						</p>
						<div class="clear"></div>
					{else}
						<div class="clear"></div>
						<p>{lang}de.0xdefec.rgallery.no_images_yet{/lang}</p>
					{/if}
			{elseif $subpage == 'stats'}
			
				<div class="border">
					<div class="containerHead">
						{lang}de.0xdefec.rgallery.stats.common{/lang}
					</div>
					<div class="container-1">
						<div>{lang}de.0xdefec.rgallery.stats.totalimages{/lang}: {#$stats.totalimages}</div>
					</div>
					<div class="container-2">
						<div>{lang}de.0xdefec.rgallery.stats.totalmb{/lang}: {$stats.totalmb|filesize}</div>
					</div>
					<div class="container-1">
						<div>{lang}de.0xdefec.rgallery.stats.totalcomments{/lang}: {#$stats.totalcomments}</div>
					</div>
					<div class="container-2">
						<div>{lang}de.0xdefec.rgallery.stats.totalclicks{/lang}: {#$stats.totalclicks}</div>
					</div>
				</div>
				<div class="border">
					<div class="containerHead">
						{lang}de.0xdefec.rgallery.stats.top5users{/lang}
					</div>
					{cycle name=top5imagesCycle values='1,2' print=false}
					{foreach from=$stats['top5users'] item=value key=key}
						<div class="container-{cycle name=top5imagesCycle}">
							<div>&raquo; <a href="{@RELATIVE_WBB_DIR}index.php?page=RGalleryUserGallery&amp;userID={$value.user->userID}{@SID_ARG_2ND}">{$value.user->username}</a> ({#$value.items})</div>
						</div>
					{/foreach}
				</div>
				
				<div class="border">
					<div class="containerHead">
						{lang}de.0xdefec.rgallery.stats.top5imagesClicks{/lang}
					</div>
					<ul class="gallery-thumbs">
					{foreach from=$stats['top5imagesclicks'] item=value key=key}
							
						<li>
							<div class="thumb">
								<a id="rG_item_name_{$value.itemID}" href="{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&amp;itemID={$value.itemID}&amp;type=page{@SID_ARG_2ND}">
									<img id="rG_item_{$value.itemID}" class="element" style="height:{RGALLERY_THUMB_SIZE}px;width:{RGALLERY_THUMB_SIZE}px" src="index.php?page=RGalleryImageWrapper&amp;itemID={$value.itemID}&amp;type=thumb{@SID_ARG_2ND}" alt="" />
								</a>
							</div>
							<div class="meta">
									{lang}de.0xdefec.rgallery.by{/lang} <a href="{@RELATIVE_WBB_DIR}index.php?page=User&amp;userID={$value.user->userID}{@SID_ARG_2ND}">{$value.user->username}</a><br />
									{if $value.itemClicks|isset}{#$value.itemClicks} {lang}de.0xdefec.rgallery.clicks{/lang}{/if}
							</div>
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
						</li>
					{/foreach}
					</ul>
					<div class="clear"></div>
				</div>
				
				<div class="border">
					<div class="containerHead">
						{lang}de.0xdefec.rgallery.stats.top5usersClicks{/lang}
					</div>
					{cycle name=top5imagesCycle values='1,2' print=false}
					{foreach from=$stats['top5usersclicks'] item=value key=key}
						<div class="container-{cycle name=top5imagesCycle}">
							<div>&raquo; <a href="{@RELATIVE_WBB_DIR}index.php?page=RGalleryUserGallery&amp;userID={$value.user->userID}{@SID_ARG_2ND}">{$value.user->username}</a> ({#$value.clicks})</div>
						</div>
					{/foreach}
				</div>
			{/if}
			<div class="clear"></div>
		</div>
	</div>
{lang}de.0xdefec.rgallery.copyright{/lang}
</div>
{include file='footer' sandbox='false'}
</body>
</html>