{if $upload_error != 'no_error' && $upload_error != ''}
	<div class="rGallery-error">
		{lang}de.0xdefec.rgallery.upload_error{/lang}
	<p>{@$error_msg}</p>
	</div>
{/if}
{if $delete_error == 1}
	<div class="rGallery-error">
		{lang}de.0xdefec.rgallery.delete_error{/lang}
	</div>
{/if}

{if $has_elements != 0}
	<script>
		wasteitems = { {$wasteitems} };
	</script>
	<ul class="gallery-thumbs">
	{foreach from=$itemArray item=value key=key}
		<li>
		  <div class="thumb">
		    <img id="rG_item_{$value.itemID}" class="element" style="height:{RGALLERY_THUMB_SIZE}px;width:{RGALLERY_THUMB_SIZE}px" src="index.php?page=RGalleryImageWrapper&amp;itemID={$value.itemID}&amp;type=thumb{@SID_ARG_2ND}" alt="" />
		  </div>
		  <div class="meta">
		    <a id="rG_item_name_{$value.itemID}" href="index.php?page=RGalleryImageWrapper&amp;itemID={$value.itemID}&amp;type=page&amp;from=user{@SID_ARG_2ND}">
		    	{$value[itemName]|truncate:20:'..'}
		    	</a><br />
		    {*$value.itemResizedSize|filesize} <br /> *}{@$value.itemAddedDate|shortTime}<br />
		    {if $value.commentsCount != 0}<strong>{$value.commentsCount}</strong> {lang}de.0xdefec.rgallery.comment_s{/lang}{/if}<br />
		  </div>
		</li>
		{literal}
		<script type="text/javascript">
			//<![CDATA[
			new Draggable("rG_item_{/literal}{$value.itemID}{literal}", {revert:true, scroll: window});
			if(wasteitems['rG_item_{/literal}{$value.itemID}{literal}']==1) new Effect.Opacity(document.getElementById('rG_item_{/literal}{$value.itemID}{literal}'), {delay: 0.0, from: 1.0, to: 0.4, duration: 0.0});
			{/literal}
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
	<div style="clear:both"></div>
	{pages page=$rGalleryPage pages=$totalpages link='javascript:generate_item_listening(%d)'}
{else}
	{lang}de.0xdefec.rgallery.no_images_yet{/lang}
{/if}