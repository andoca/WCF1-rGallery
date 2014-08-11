{include file='documentHeader' sandbox='false'}
<head>
	<title>{lang}de.0xdefec.rgallery.maintitle{/lang} - {PAGE_TITLE}</title>
	<link rel="stylesheet" type="text/css" media="screen" href="{@RELATIVE_WBB_DIR}style/rGallery.css" />
	{include file='headInclude' sandbox='false'}
	{if $userid == $data.ownerID || $is_authorized}
	<script type="text/javascript">
	<!--
		function deleteComment(commentID) {
			if(confirm('{lang}de.0xdefec.rgallery.ask_deleteComment{/lang}')) {
				var url = '{@RELATIVE_WBB_DIR}index.php?page=RGalleryAction&type=deleteComment&id='+commentID+'{if $from != ''}&from={$from}{/if}{@SID_ARG_2ND_NOT_ENCODED}';
				new Ajax.Request(url, {
					method: 'get',
					onSuccess: function() {
						Effect.Fade('comment_'+commentID);
						}
					});
				}
			}
		function deleteItem(itemID) {
			if(confirm('{lang}de.0xdefec.rgallery.ask_deleteItem{/lang}')) {
				top.location.href='{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&itemID={$data.itemID}&type=delete{if $from != ''}&from={$from}{/if}{@SID_ARG_2ND_NOT_ENCODED}';
				}
			}
	-->
	</script>
	{/if}
</head>
<body>
{include file='header' sandbox='false'}
<div id="main">
	<ul class="breadCrumbs">
		<li><a href="index.php{@SID_ARG_1ST}"><img src="icon/indexS.png" alt="" /> <span>{PAGE_TITLE}</span></a> &raquo;</li>
		<li><a href="index.php?page={if $from == 'user'}RGalleryUser{else}RGallery{/if}&amp;rGalleryCat=none{@SID_ARG_2ND}"><img src="icon/rGalleryS.png" alt="" /> <span>{lang}de.0xdefec.rgallery.maintitle{/lang}</span></a> &raquo;</li>
		{if !$current_cat|empty}<li><a href="index.php?page={if $from == 'user'}RGalleryUser{else}RGallery{/if}{@SID_ARG_2ND}">{*lang}de.0xdefec.rgallery.upload.categorie{/lang*} <span>{$current_cat}</span></a> &raquo;</li>{/if}
	</ul>
	<div class="mainHeadline">
		<a href="index.php?page={if $from == 'user'}RGalleryUser{else}RGallery{/if}{@SID_ARG_2ND}"><img src="{@RELATIVE_WBB_DIR}icon/rGalleryL.png" alt="" title="{lang}de.0xdefec.rgallery.maintitle{/lang}" /></a>
		<div class="headlineContainer">
			<h2><a href="index.php?page={if $from == 'user'}RGalleryUser{else}RGallery{/if}{@SID_ARG_2ND}">{lang}de.0xdefec.rgallery.image.title{/lang} <em>{$data[itemName]}</em></a></h2>
		</div>
	</div>
	<div class="border content RGalleryImage">
		<div class="container-1">
			<a id="imageTop" />
			<h3>{lang}de.0xdefec.rgallery.upload.categorie{/lang} '<a href="{@RELATIVE_WBB_DIR}index.php?page=RGallery&amp;rGalleryCat={$data.catID}{@SID_ARG_2ND}">{$data.catName}</a>'</h3>
			<table style="margin: auto">
				{* i know this table is dirty, but it's the only solution to get the prev/next arrows working in opera! *}
				<tr>
					<td>
						{if $neighbors.2 != 0}
							<a href="{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&amp;itemID={$neighbors.2}&amp;type=page{if $from != ''}&from={$from}{/if}{@SID_ARG_2ND}#imageTop" id="prevArrow" style="position: absolute; margin: 30px 0 0 30px; visibility: hidden" onmouseover="document.getElementById('prevArrow').style.visibility='visible'" onmouseout="document.getElementById('prevArrow').style.visibility='hidden'">
								<img src="{@RELATIVE_WCF_DIR}icon/previousS.png" alt="previous" /></a>
						{/if}
						
							<img src="{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&amp;itemID={$data.itemID}&amp;type=image{@SID_ARG_2ND}" class="element" alt="{foreach from=$data.tags item=value key=idx}{$value} {/foreach}" usemap="#prevnext" />
						
						{if $neighbors.3 != 0}
							<a href="{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&amp;itemID={$neighbors.3}&amp;type=page{if $from != ''}&from={$from}{/if}{@SID_ARG_2ND}#imageTop" id="nextArrow" style="position: absolute; margin: 30px 0 0 -50px; visibility: hidden" onmouseover="document.getElementById('nextArrow').style.visibility='visible'" onmouseout="document.getElementById('nextArrow').style.visibility='hidden'">
								<img src="{@RELATIVE_WCF_DIR}icon/nextS.png" alt="next" /></a>
						{/if}
					<map id="prevnext" name="prevnext">
						{if $neighbors.2 != 0}
						<area shape="rect" coords="0,0,{$data.itemDimW_h_l},{$data.itemDimH}"
							href="{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&amp;itemID={$neighbors.2}&amp;type=page{if $from != ''}&from={$from}{/if}{@SID_ARG_2ND}#imageTop"
							alt="previous"
							onmouseover="document.getElementById('prevArrow').style.visibility='visible'"
							onmouseout="document.getElementById('prevArrow').style.visibility='hidden'"/>
						{/if}
						{if $neighbors.3 != 0}
						<area shape="rect" coords="{$data.itemDimW_h},0,{$data.itemDimW},{$data.itemDimH}"
							href="{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&amp;itemID={$neighbors.3}&amp;type=page{if $from != ''}&from={$from}{/if}{@SID_ARG_2ND}#imageTop"
							alt="next"
							onmouseover="document.getElementById('nextArrow').style.visibility='visible'"
							onmouseout="document.getElementById('nextArrow').style.visibility='hidden'"/>
						{/if}
					</map>
					</td>
				</tr>
			</table>
			<p class="smallFont light">[gallery]{$data.itemID}[/gallery]</p>
			
			{if $owner->rgallery_allowRating && $this->user->userID}
				<script type="text/javascript" src="{@RELATIVE_WBB_DIR}js/Rating.class.js"></script>
				<form method="post" action="index.php?page=RGalleryImageWrapper&amp;itemID={$data.itemID}&amp;type=page">
					<div title="{#$data.userRating}">
						<input type="hidden" name="action" value="userrating" />
						{@SID_INPUT_TAG}
						<input type="hidden" id="itemRating" name="rating" value="0" />
						
						<span class="hidden" id="itemRatingSpan"></span>
						<noscript>
							<div>
								<select id="threadRatingSelect" name="rating">
									<option value="1"{if $data.userRatingRound == 1} selected="selected"{/if}>1</option>
									<option value="2"{if $data.userRatingRound == 2} selected="selected"{/if}>2</option>
									<option value="3"{if $data.userRatingRound == 3} selected="selected"{/if}>3</option>
									<option value="4"{if $data.userRatingRound == 4} selected="selected"{/if}>4</option>
									<option value="5"{if $data.userRatingRound == 5} selected="selected"{/if}>5</option>
								</select>
								<input type="image" class="inputImage" src="{@RELATIVE_WCF_DIR}icon/submitS.png" alt="{lang}wcf.global.button.submit{/lang}" />
							</div>
						</noscript>
					</div>
				</form>
				
				<script type="text/javascript">
					//<![CDATA[
					new Rating('itemRating', {@$data.userRating|intval});
					//]]>
				</script>
			{/if}
			<p>
				{if $neighbors.4 != 0 && $neighbors.4 != $data.itemID}<a href="{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&amp;itemID={$neighbors.4}&amp;type=page{if $from != ''}&from={$from}{/if}{@SID_ARG_2ND}#imageTop" style="text-decoration: none">|<img src="{@RELATIVE_WCF_DIR}icon/previousS.png" alt="first" /></a>&nbsp;&nbsp;{/if}
				{if $neighbors.2 != 0 && $neighbors.2 != $neighbors.4}<a href="{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&amp;itemID={$neighbors.2}&amp;type=page{if $from != ''}&from={$from}{/if}{@SID_ARG_2ND}#imageTop"><img src="{@RELATIVE_WCF_DIR}icon/previousS.png" alt="previous" /></a>{/if}
				{$neighbors.0} von {$neighbors.1}
				{if $neighbors.3 != 0 && $neighbors.3 != $neighbors.5}<a href="{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&amp;itemID={$neighbors.3}&amp;type=page{if $from != ''}&from={$from}{/if}{@SID_ARG_2ND}#imageTop"><img src="{@RELATIVE_WCF_DIR}icon/nextS.png" alt="next" /></a>{/if}
				{if $neighbors.5 != 0 && $neighbors.5 != $data.itemID}&nbsp;&nbsp;<a href="{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&amp;itemID={$neighbors.5}&amp;type=page{if $from != ''}&from={$from}{/if}{@SID_ARG_2ND}#imageTop" style="text-decoration: none"><img src="{@RELATIVE_WCF_DIR}icon/nextS.png" alt="last" />|</a>{/if}
			</p><br />
		</div>
	</div>



	{if $this->getStyle()->getVariable('messages.color.cycle')}
		{cycle name=messageCycle values='2,1' print=false}
	{else}
		{cycle name=messageCycle values='1' print=false}
	{/if}
	
	{if $this->getStyle()->getVariable('messages.sidebar.color.cycle')}
		{if $this->getStyle()->getVariable('messages.color.cycle')}
			{cycle name=postCycle values='1,2' print=false}
		{else}
			{cycle name=postCycle values='3,2' print=false}
		{/if}
	{else}
		{cycle name=postCycle values='3' print=false}
	{/if}
	
	{capture assign='messageClass'}message{if $this->getStyle()->getVariable('messages.framed')}Framed{/if}{@$this->getStyle()->getVariable('messages.sidebar.alignment')|ucfirst}{if $this->getStyle()->getVariable('messages.sidebar.divider.use')} dividers{/if}{/capture}
	{capture assign='messageFooterClass'}messageFooter{@$this->getStyle()->getVariable('messages.footer.alignment')|ucfirst}{/capture}
	
	
	
	<div id="comment_{$data.itemID}" class="message border">
		<div class="messageInner {@$messageClass} container-{cycle name=postCycle}{if !$data.itemOwner->userID} guestPost{/if}">
			<div class="messageSidebar">
				{if $data.itemOwner->userID}
					<div class="messageAuthor">
						<p class="userName">
							{if MESSAGE_SIDEBAR_ENABLE_ONLINE_STATUS}
								{if $data.itemOwner->isOnline()}
									<img src="{@RELATIVE_WCF_DIR}icon/onlineS.png" alt="" title="{lang username=$data.itemOwner->username}wcf.user.online{/lang}" />
								{else}
									<img src="{@RELATIVE_WCF_DIR}icon/offlineS.png" alt="" title="{lang username=$data.itemOwner->username}wcf.user.offline{/lang}" />
								{/if}
							{/if}
						
							<a href="index.php?page=User&amp;userID={@$data.itemOwner->userID}{@SID_ARG_2ND}" title="{lang username=$data.itemOwner->username}wcf.user.viewProfile{/lang}">
								<span>{$data.itemOwner->username}</span>
							</a>
						</p>
					</div>
					
					{if $this->getStyle()->getVariable('messages.sidebar.alignment') == 'top'}
						{if $data.itemOwner->getAvatar()}
							{assign var=dummy value=$data.itemOwner->getAvatar()->setMaxSize(76, 76)}
						{else}
							<div class="userAvatar{if $this->getStyle()->getVariable('messages.sidebar.avatar.framed')}Framed{/if}">
								<a href="index.php?page=User&amp;userID={@$data.itemOwner->userID}{@SID_ARG_2ND}" title="{lang username=$data.itemOwner->username}wcf.user.viewProfile{/lang}"><img src="{@RELATIVE_WCF_DIR}images/avatars/avatar-default.png" alt=""
									style="width: 76px; height: 76px;{if $this->getStyle()->getVariable('messages.sidebar.avatar.framed')} margin-top: -38px; margin-left: -38px{/if}" /></a>
							</div>
						{/if}
					{/if}
					
					{if $data.itemOwner->getAvatar()}
						<div class="userAvatar{if $this->getStyle()->getVariable('messages.sidebar.avatar.framed')}Framed{/if}">
							<a href="index.php?page=User&amp;userID={@$data.itemOwner->userID}{@SID_ARG_2ND}" title="{lang username=$data.itemOwner->username}wcf.user.viewProfile{/lang}"><img src="{$data.itemOwner->getAvatar()->getURL()}" alt=""
								style="width: {@$data.itemOwner->getAvatar()->width}px; height: {@$data.itemOwner->getAvatar()->height}px;{if $this->getStyle()->getVariable('messages.sidebar.avatar.framed')} margin-top: -{@$data.itemOwner->getAvatar()->height/2|intval}px; margin-left: -{@$data.itemOwner->getAvatar()->width/2|intval}px{/if}" /></a>
						</div>
					{else}
						<div class="userAvatar{if $this->getStyle()->getVariable('messages.sidebar.avatar.framed')}Framed{/if}">
							<a href="index.php?page=User&amp;userID={@$data.itemOwner->userID}{@SID_ARG_2ND}" title="{lang username=$data.itemOwner->username}wcf.user.viewProfile{/lang}"><img src="{@RELATIVE_WCF_DIR}images/avatars/avatar-default.png" alt="" style="width: 100px; height: 100px;{if $this->getStyle()->getVariable('messages.sidebar.avatar.framed')} margin-top: -{@100/2|intval}px; margin-left: -{100/2|intval}px{/if}" /></a>
						</div>
					{/if}
					
					{capture assign=userContacts}
						{if $user->userID}
							<li><a href="index.php?form=PMNew&amp;userID={@$data.ownerID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/pmEmptyS.png" alt="" title="{lang}wcf.pm.profile.sendPM{/lang}" /></a></li>
						{/if}
					{/capture}
				{else}
					<div class="messageAuthor">
						<p class="userName">{$data.itemOwner->username}</p>
						<p class="userTitle smallFont">{lang}wcf.user.guest{/lang}</p>
					</div>
				{/if}
			</div>
			
			<div class="messageContent">
				<div class="messageContentInner color-{cycle name=messageCycle}">
					<div class="messageHeader">
						<div class="containerIcon">
							<img id="postEdit" src="{@RELATIVE_WBB_DIR}icon/rGalleryM.png" alt="" />
						</div>
						<div class="containerContent">
							<p class="smallFont light">{@$data.itemAddedDate|time} -
							{#$data.itemClicks} {lang}de.0xdefec.rgallery.clicks{/lang}
							</p>
						</div>
					</div>
					
					<h3>
						<span>{$data.itemName} 
						{if $data.hasFullsize == 1} 
							(<a href="{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&amp;itemID={$data.itemID}&amp;type=original{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/exportS.png" alt="" /> {lang}de.0xdefec.rgallery.view_fullsize{/lang}</a>)
						{/if}
						</span>
					</h3>
						
					<div class="messageBody">
						{@$data.itemCommentBBCode}
					</div>
					
					{if $data.itemModDate > 0}
						<p class="editNote smallFont light">
							{lang}de.0xdefec.rgallery.image_lastchanged{/lang}: {@$data.itemModDate|shortTime}
						</p>
					{/if}
					
					<div class="{@$messageFooterClass}">
					
						{if $data.count_tags != 0}
							<div style="float: left;margin-top: 5px">
								<strong>{lang}de.0xdefec.rgallery.tags{/lang}:</strong>
								{foreach from=$data.tags item=value key=idx}
									<a href="{@RELATIVE_WBB_DIR}index.php?page=RGallery&amp;tag={@$tags_enc.$idx}" title="{$value}">{$value}</a>
								{/foreach}
							</div>
						{/if}
						<div class="smallButtons">
							<ul>
								<li class="extraButton"><a href="#top"><img src="{@RELATIVE_WCF_DIR}icon/upS.png" alt="{lang}wcf.global.scrollUp{/lang}" title="{lang}wcf.global.scrollUp{/lang}" /></a></li>
								{if $is_authorized}<li><a href="javascript: deleteItem({$data.itemID})"><img src="{@RELATIVE_WBB_DIR}icon/rGallery_delete.png" style="border: 0; padding: 0; background: none; margin: 0" alt="delete" /></a></li>{/if}
							</ul>
						</div>
					</div>
					<hr />
				</div>
			</div>
			
		</div>
	</div>
	
	
	
	
	
	{if $userid == $data.ownerID || $is_authorized == 1}
		<div class="message border">
			<div class="messageInner {@$messageClass} container">
				<div class="RGalleryContainer" style="margin: 10px;">
					<h2 onclick="Effect.toggle('editDiv', 'slide');" style="cursor: pointer;">{lang}de.0xdefec.rgallery.image_edit{/lang}</h2>
					<div id="editDiv" style="display: none">
						<div class="messageContent">
							<form action="{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&amp;type=page&amp;itemID={$data.itemID}{if $from != ''}&amp;from={$from}{/if}{@SID_ARG_2ND}" method="post">
								<table style="text-align: left">
									<tr>
										<td>{lang}de.0xdefec.rgallery.upload.title{/lang}</td>
										<td><input class="inputText" type="text" name="itemName" maxlength="64" value="{$data.itemName}" /></td>
									</tr>
									<tr>
										<td>{lang}de.0xdefec.rgallery.upload.categorie{/lang}</td>
										<td>
										{htmloptions output=$RGalleryCats_name values=$RGalleryCats_value name=itemCat selected=$data.catID}</td>
									</tr>
									<tr>
										<td>{lang}de.0xdefec.rgallery.upload.comment{/lang}</td>
										<td><textarea name="itemComment" rows="5" cols="30">{$data.itemComment}</textarea></td>
									</tr>
									<tr>
										<td>{lang}de.0xdefec.rgallery.upload.tags{/lang}</td>
										<td>
										<input class="inputText" type="text" value="{foreach from=$data.tags item=value key=idx}{$value}{if $idx != $data.tags|count}, {/if}{/foreach}" id="autocomplete" name="itemTags"  id="itemTags" maxlength="2000" style="width: 80%" /> <span id="indicator1" style="display: none;vertical-align:middle"><img src="{@RELATIVE_WBB_DIR}icon/indicator.gif" alt="" /></span>
										<div id="autocomplete_choices" class="autocomplete"></div>
										<br />{lang}de.0xdefec.rgallery.upload.tags_desc{/lang}</td>
									</tr>
									<tr>
										<td>&nbsp;<input type="hidden" name="action" value="updateItem" /></td>
										<td><input type="submit" name="submit" value="{lang}de.0xdefec.rgallery.upload.save{/lang}" /> <input type="button" name="" value="{lang}de.0xdefec.rgallery.image_delete{/lang}" onclick="deleteItem({$data.itemID})" /></td>
									</tr>
								</table>
							</form>
						
							<script language="JavaScript" type="text/javascript">
								new Ajax.Autocompleter("autocomplete", "autocomplete_choices", "{@RELATIVE_WBB_DIR}index.php?page=RGalleryAction{@SID_ARG_2ND}", { paramName: "tagStr", minChars: 2, indicator: 'indicator1', parameters:''});
							</script>
						</div>
					</div>
				</div>
			</div>
		</div>
	{/if}
	
	
	
	{if $user->userID && $owner->rgallery_allowComments}
		<div class="message border">
			<div class="messageInner {@$messageClass} container">
				<div class="RGalleryContainer" style="margin: 10px;">
					<form action="{@RELATIVE_WBB_DIR}index.php?page=RGalleryImageWrapper&amp;itemID={$data[itemID]}&amp;type=page{@SID_ARG_2ND}" method="post">
						<h3><span>{lang}de.0xdefec.rgallery.comment_add.title{/lang}</span></h3>
						<div class="messageBody">
									<input type="hidden" name="action" value="commentItem" />
									<textarea name="commentText" rows="5" cols="50"></textarea>
							<input type="submit" name="submit" value="{lang}de.0xdefec.rgallery.comment_add.send{/lang}" />
						</div>
							
						{*if $post->getSignature()}
							<div class="signature">
								{@$post->getSignature()}
							</div>
						{/if*}
						
						<div class="{@$messageFooterClass}">
							<div class="smallButtons">
								<ul>
									<li class="extraButton"><a href="#top"><img src="{@RELATIVE_WCF_DIR}icon/upS.png" alt="{lang}wcf.global.scrollUp{/lang}" title="{lang}wcf.global.scrollUp{/lang}" /></a></li>
								</ul>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	{/if}
	
		
	{* build messages css classes *}
	{assign var="startIndex" value=1}
	
	{foreach from=$data.comments item=comment key=idx}
		{include file='RGalleryComment' sandbox='false'}
	{/foreach}
	
	{lang}de.0xdefec.rgallery.copyright{/lang}
</div>

{include file='footer' sandbox='false'}
</body>
</html>