{include file='documentHeader' sandbox='false'}
<head>
	<title>{lang}de.0xdefec.rgallery.maintitle{/lang} - {PAGE_TITLE}</title>
	<link rel="stylesheet" type="text/css" media="screen" href="{@RELATIVE_WBB_DIR}style/rGallery.css">
	{include file='headInclude' sandbox='false'}
	
	<script type="text/javascript" src="{@RELATIVE_WBB_DIR}js/RGallery.js"></script>
	<script type='text/javascript' src='{@RELATIVE_WBB_DIR}js/prototip-min.js'></script>
	<script type='text/javascript' src='{@RELATIVE_WBB_DIR}js/multifile.js'></script>
	<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>
	<script type="text/javascript">
			
		function generate_item_listening(rGalleryPage) {
			if(rGalleryPage == null) rGalleryPage = 1;
			var url = '{@RELATIVE_WBB_DIR}index.php?page=RGalleryUserDHTMLItems&tag={$active_tag}&rGalleryPage='+rGalleryPage+'&s={@SID}';
			var pars = '';
			var target = 'RGalleryImages';	
			var myAjax = new Ajax.Updater(target, url, { method: 'post', evalScripts: true, parameters: pars, onComplete: loadWaste});
			}
			
		function refresh_delete_link() {
			var url = '{@RELATIVE_WBB_DIR}index.php?page=RGalleryBasket&delete_link=1&s={@SID}';
			var pars = '';
			var target = 'RGalleryBasketDeleteLink';	
			var myAjax = new Ajax.Updater(target, url, { method: 'post', evalScripts: true, parameters: pars});
			}
		
		function refresh_user_status() {
			var url = '{@RELATIVE_WBB_DIR}index.php?page=RGalleryUserStatus&s={@SID}';
			var pars = '';
			var target = 'RGalleryUserStatus';	
			var myAjax = new Ajax.Updater(target, url, { method: 'post', evalScripts: true, parameters: pars});
			}
		
		function refresh_user_tags() {
			var url = '{@RELATIVE_WBB_DIR}index.php?page=RGalleryUserTags&s={@SID}';
			var pars = '';
			var target = 'RGalleryUserTags';	
			var myAjax = new Ajax.Updater(target, url, { method: 'post', evalScripts: true, parameters: pars});
			}
		
		function addImage(element, dropon, event) {
			sendData(element.id);
			new Effect.Opacity(element, { delay: 0.5, from: 1.0, to: 0.4});
			}
		function loadWaste () {
			var url    = '{@RELATIVE_WBB_DIR}index.php';
			var rand   = Math.random(9999);
			var pars   = 'page=RGalleryBasket&loadWaste=1&rand=' + rand+'&s={@SID}';
			var myAjax = new Ajax.Request( url, { method: 'get', parameters: pars, onComplete: showResponse} );
			}
		function sendData (prod) {
			var url    = '{@RELATIVE_WBB_DIR}index.php';
			var rand   = Math.random(9999);
			var pars   = 'page=RGalleryBasket&image_id=' + prod + '&rand=' + rand+'&s={@SID}';
			var myAjax = new Ajax.Request( url, { method: 'get', parameters: pars, onComplete: showResponse} );
			}
// 		function clearImages () {
// 			var url    = '{@RELATIVE_WBB_DIR}index.php';
// 			var rand   = Math.random(9999);
// 			var pars   = 'page=RGalleryBasket&clear=true&rand=' + rand+'&s={@SID}';
// 			var myAjax = new Ajax.Request( url, { method: 'get', parameters: pars, onComplete: showResponse} );
// 			}
		function clearImage (id) {
			element = document.getElementById(id);
			new Effect.Opacity(element, { delay: 0.5, from: 0.4, to: 1.0});
			var url    = '{@RELATIVE_WBB_DIR}index.php';
			var rand   = Math.random(9999);
			var pars   = 'page=RGalleryBasket&clearImage=true&id=' + id + '&rand=' + rand+'&s={@SID}';
			var myAjax = new Ajax.Request( url, { method: 'get', parameters: pars, onComplete: showResponse} );
			}
		
		function showResponse (originalRequest) {
			$('loading').style.display = "none";
			document.uploadForm.submit.disabled=false;
						
			if(originalRequest.responseText != '') {
				$('waste').innerHTML = originalRequest.responseText;
				}
			else {
				$('waste').innerHTML = "<div style='text-align:center;font-weight:bold; color: #bcbcbc;margin: 10px;margin-top:70px'>{lang}de.0xdefec.rgallery.waste_desc{/lang}</div>";
				}
			$('waste').scrollTop = $('waste').scrollHeight - $('waste').clientHeight;
			refresh_delete_link();
			refresh_user_status();
			refresh_user_tags();
			}
			
		function showLoad () {
			document.uploadForm.submit.disabled=true;
			$('loading').style.display = "inline";
			}
		function startUpload() {
			showLoad();
			startCallback();
			}
			
		function upload(my_form) {			
			AIM.submit(my_form, { 'onStart' : startUpload, 'onComplete' : function () { generate_item_listening(); reset_form();} })
			return true;
			}
			
		function reset_form() {
			var url = '{@RELATIVE_WBB_DIR}index.php?page=RGalleryUploadForm&s={@SID}';
			var pars = '';
			var target = 'uploaddiv';	
			var myAjax = new Ajax.Updater(target, url, { method: 'post', evalScripts: true});
			}
			
		Event.observe(window,'load',function(){
			generate_item_listening({$rGalleryPage});
			reset_form();
			}); 
	</script>
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
		<li><a href="index.php"><img src="icon/indexS.png" alt="" /> <span>{PAGE_TITLE}</span></a> &raquo;</li>
		<li><a href="index.php?page=RGallery{@SID_ARG_2ND}"><img src="icon/rGalleryS.png" alt="" /> <span>{lang}de.0xdefec.rgallery.maintitle{/lang}</span></a> &raquo;</li>
	</ul>
	<div class="mainHeadline">
		<img src="{@RELATIVE_WBB_DIR}icon/rGalleryL.png" alt="" />
		<div class="headlineContainer">
			<h2>{lang}de.0xdefec.rgallery.usertitle{/lang}</h2>
		</div>
	</div>
	<div class="border content">
		<div class="container-1">
			<div style="width: 69%;float: left;">		
				<h2>{lang}de.0xdefec.rgallery.elements{/lang}</h2>
					<div class="border">
						<div id="RGalleryImages"><em>Loading...</em></div>
						<div style="clear:both"></div>
					</div>
				<h2>{lang}de.0xdefec.rgallery.add_element{/lang}</h2>
					<div id="uploaddiv" class="border">
					   <em>Loading...</em>
					</div>
			</div>
			<div style="width: 29%; float: right;">
			<h2>&nbsp;</h2>
				<div class="border" style="width: 100%;">
					<div id="waste" style="background-image: url('{@RELATIVE_WBB_DIR}icon/rGallery_waste.png');"></div>
					<script>
						Droppables.add('waste', {literal}{onDrop:addImage}{/literal});
					</script>
				</div>
				<div style="clear: both;margin: 4px;text-align: center" id="RGalleryBasketDeleteLink"></div>
				<div class="border" style="width: 100%;padding: 0">
					<div style="padding: 4px;" {if $filter_active == 1}class="rGalleryActiveFilter"{/if}>
						<h3><b>{lang}de.0xdefec.rgallery.filter{/lang}{if $filter_active == 1} - <a href="{@RELATIVE_WBB_DIR}index.php?page=RGalleryUser&amp;rGalleryCat=none&amp;reset_tag=1{@SID_ARG_2ND}">reset</a>{/if}</b></h3>
						<form name="changecat" action="{@RELATIVE_WBB_DIR}index.php" method="get">
							{@SID_INPUT_TAG}
							<input type="hidden" name="page" value="RGalleryUser" />
							<input type="hidden" name="tag" value="{$active_tag}" />
							{lang}de.0xdefec.rgallery.upload.categorie{/lang}: <select name="rGalleryCat" onChange="changecat.submit()">
								<option value="none">&nbsp;</option>
								{htmloptions output=$RGalleryFilterCats_name values=$RGalleryFilterCats_value selected=$rGalleryCat}
								</select>
						</form>
						<div class="tag_cloud" id="RGalleryUserTags"><em>Loading...</em></div>
					</div>
				</div>
				<div class="border" style="width: 100%;clear: both">
					<div style="padding: 4px;">
						<h3 style="font-weight: bold">{lang}de.0xdefec.rgallery.last_comments{/lang}</h3>
						{foreach from=$last_comments item=value key=idx}
							<div class="RGalleryCommentPreview">
								<a id="comment_item_{$value.itemID}" href="{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&amp;from=user&amp;type=page&amp;itemID={$value.itemID}{@SID_ARG_2ND}">{$value.itemName|truncate:15:'..'}</a>:
								{if $value.userID != ''}
									{* the user still exists! so we link his profile *}
									<a href="{@RELATIVE_WBB_DIR}index.php?page=User&amp;userID={$value.userID}{@SID_ARG_2ND}">{$value.userName}</a>
								{else}
									{$value.userName}
								{/if}
								({@$value.commentAddedDate|shortTime})
								<p>{@$value.commentText}</p>
							</div>
							<script type="text/javascript">
								//<![CDATA[
								{if RGALLERY_SHOW_TOOLTIPS == 1}
									new Tip("comment_item_{$value.itemID}", 
										'{include file='RGalleryDHTMLPreview'}',
										{ 	effect: 'appear',
											delay: 0.3,
											title: false,
											viewport: true});
								{/if}
								//]]>
							</script>
						{/foreach}
					</div>
				</div>
				<div class="border" style="width: 100%;">
					<div style="padding: 4px;">
						<div id="RGalleryUserStatus"><em>Loading...</em></div>
					</div>
				</div>
			</div>
			<div style="clear:both"></div>
		</div>
		
		{*<div style="margin: 0px">
			
			{*if $tags}
			<div class="border" style="width: 29%; float: right; text-align: center">
				<h3><strong>{lang}de.0xdefec.rgallery.available_tags{/lang}</strong></h3>
				<select size="10" style="width: 90%;margin: 10px 0 10px 0">
					{foreach from=$tags item=count key=tag}
						<option value="{$tag}" onclick="if(getElementById('itemTags').value != ''){ getElementById('itemTags').value = getElementById('itemTags').value + ' ' } getElementById('itemTags').value = getElementById('itemTags').value + this.value">{$tag}</option>
					{/foreach}
				</select>
				{lang}de.0xdefec.rgallery.available_tags_desc{/lang}
			</div>
			{/if
			<div style="clear:both"></div>
		</div>*}
	</div>
{lang}de.0xdefec.rgallery.copyright{/lang}
</div>
{include file='footer' sandbox='false'}
</body>
</html>