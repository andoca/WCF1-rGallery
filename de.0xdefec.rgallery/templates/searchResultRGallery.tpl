
<div class="message content">
	<div class="messageInner container-{cycle name='results' values='1,2'}">
		<div class="messageHeader">
			<div class="containerIcon">
				<a href="{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&amp;itemID={@$item.message->itemID}&amp;type=page{@SID_ARG_2ND}"><img src="{@RELATIVE_WBB_DIR}icon/rGalleryM.png" alt="" /></a>
			</div>
			<div class="containerContent">
				<p class="light smallFont">{@$item.message->itemAddedDate|time}</p>
				<p class="light smallFont">{lang}de.0xdefec.rgallery.by{/lang} <a href="http://v10.rennmaus.de/forum/index.php?page=User&amp;userID={@$item.message->itemOwner->userID}">{@$item.message->itemOwner->username}</a></p>
			</div>
		</div>
		
		<h3><a href="{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&amp;itemID={@$item.message->itemID}&amp;type=page{@SID_ARG_2ND}">{$item.message->itemName}</a></h3>
		<div class="messageBody">
			<a href="{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&amp;itemID={@$item.message->itemID}&amp;type=page{@SID_ARG_2ND}">
				<img style="height:{RGALLERY_THUMB_SIZE}px;width:{RGALLERY_THUMB_SIZE}px; border: 1px solid #ccc; margin: 5px; padding: 2px;vertical-align:top" src="{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&amp;itemID={@$item.message->itemID}&amp;type=thumb{@SID_ARG_2ND}" alt="" />
			</a>
			{@$item.message->getFormattedMessage()}
		</div>
		
		<div class="messageFooter">
			<div class="smallButtons">
				<ul>
					<li class="extraButton"><a href="#top"><img src="{@RELATIVE_WCF_DIR}icon/upS.png" alt="" title="{lang}wcf.global.scrollUp{/lang}" /><span class="hidden"> {lang}wcf.global.scrollUp{/lang}</span></a></li>
				</ul>
			</div>
			<ul class="breadCrumbs light">
				{*<li><img src="{@RELATIVE_WCF_DIR}icon/folderS.png" alt="" /> <a href="index.php?page=PMList&amp;folderID={@$item.message->folderID}{@SID_ARG_2ND}">{$item.message->folderName}</a> </li>*}
			</ul>
		</div>
		
		<hr />
	</div>
</div>
