
		<script type="text/javascript" src="{@RELATIVE_WBB_DIR}js/RGallery.js"></script>
		<script type="text/javascript" src="{@RELATIVE_WBB_DIR}js/prototype.js"></script>
		<script type="text/javascript" src="{@RELATIVE_WBB_DIR}js/scriptaculous.js"></script>
		<script type='text/javascript' src='{@RELATIVE_WBB_DIR}js/prototip-min.js'></script>
		<link rel="stylesheet" type="text/css" media="screen" href="{@RELATIVE_WBB_DIR}style/rGallery.css" />
		<div class="border" id="box{$boxID}">
			<div class="containerHead">
				<div class="containerIcon">
			    	<a href="javascript: void(0)" onclick="openList('rgalleryrandombox', true)">
                	<img src="{@RELATIVE_WCF_DIR}icon/minusS.png" id="rgalleryrandomboxImage" alt="" /></a>
            	</div>
				<div class="containerContent"><span>{lang}de.0xdefec.rgallery.portalrandombox.title{/lang}</span>
				</div>
           	</div>
			<div class="container-1" id="rgalleryrandombox">
				<div class="containerContent" style="text-align: center;margin: 0 10px 0 10px;">
					{if $randomitems > 0}
						{foreach from=$randomsideboxdata item=value key=key}
							<div class="thumb">
								<a id="rG_item_name_{$value.itemID}" href="{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&amp;itemID={$value.itemID}&amp;type=page{@SID_ARG_2ND}">
									<img id="rG_item_{$value.itemID}" class="element" style="height:{RGALLERY_THUMB_SIZE}px;width:{RGALLERY_THUMB_SIZE}px;border: 1px solid #ccc;padding: 7px;-moz-border-radius: 5px;text-align: center;" src="index.php?page=RGalleryImageWrapper&amp;itemID={$value.itemID}&amp;type=thumb{@SID_ARG_2ND}" alt="" />
								</a>
							</div>
							<div class="meta">
									{lang}de.0xdefec.rgallery.by{/lang} <a href="{@RELATIVE_WBB_DIR}index.php?page=User&amp;userID={$value.user->userID}{@SID_ARG_2ND}">{$value.user->username}</a><br />
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
						{/foreach}
					{else}
						{lang}de.0xdefec.rgallery.no_images_yet{/lang}
					{/if}
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<script type="text/javascript">
		//<![CDATA[
		initList('rgalleryrandombox', {@$item.Status});
		//]]>
		</script>