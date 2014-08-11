{include file='documentHeader' sandbox='false'}
<head>
	<title>{lang}de.0xdefec.rgallery.maintitle{/lang} - {PAGE_TITLE}</title>
	<link rel="stylesheet" type="text/css" media="screen" href="{@RELATIVE_WBB_DIR}style/rGallery.css">
	{include file='headInclude' sandbox='false'}
</head>
<body>
{include file='header' sandbox='false'}
<div id="main">
	<ul class="breadCrumbs">
		<li><a href="index.php{@SID_ARG_1ST}"><img src="icon/indexS.png" alt="" /> <span>{PAGE_TITLE}</span></a> &raquo;</li>
	</ul>
	<div class="mainHeadline">
		<a href="index.php?page={if $from == 'user'}RGalleryUser{else}RGallery{/if}{@SID_ARG_2ND}"><img src="{@RELATIVE_WBB_DIR}icon/rGalleryL.png" alt="" title="{lang}de.0xdefec.rgallery.maintitle{/lang}" /></a>
		<div class="headlineContainer">
			<h2><a href="index.php?page={if $from == 'user'}RGalleryUser{else}RGallery{/if}{@SID_ARG_2ND}">{lang}de.0xdefec.rgallery.maintitle{/lang}</a></h2>
		</div>
	</div>
	<div class="border content">
		<div class="container-1">
			<h2>{lang}de.0xdefec.rgallery.image_delete{/lang}</h2>
			<p>{lang}de.0xdefec.rgallery.item_deleted{/lang}</p>
			<p><a href="index.php?page={if $from == 'user'}RGalleryUser{else}RGallery{/if}{@SID_ARG_2ND}">{lang}de.0xdefec.rgallery.back{/lang}</a></p>
		</div>
	</div>
		{lang}de.0xdefec.rgallery.copyright{/lang}
</div>
{include file='footer' sandbox='false'}
</body>
</html>