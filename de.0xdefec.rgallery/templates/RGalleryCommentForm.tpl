<div class="RGalleryUploadForm">
	<form action="{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&amp;itemID={$data[itemID]}&amp;type=page{@SID_ARG_2ND}" method="post">
		<input type="hidden" name="action" value="commentItem" />
		<textarea name="commentText" rows="5" cols="50"></textarea>
		<input type="submit" name="submit" value="{lang}de.0xdefec.rgallery.comment_add.send{/lang}" />
	</form>
</div>