<span style="color: #000">{if $value.itemName|isset}{$value.itemName|encodejs}{/if}<br /><img id="rG_item_{$value.itemID}" src="index.php?page=RGalleryImageWrapper&amp;itemID={$value.itemID}&amp;type=preview{@SID_ARG_2ND}" alt="" /><br /><span class="smallFont">{if $value.itemClicks|isset}{#$value.itemClicks} {lang}de.0xdefec.rgallery.clicks{/lang}{/if}</span></span>