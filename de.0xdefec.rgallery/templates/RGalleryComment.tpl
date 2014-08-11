		{assign var="author" value=$comment.author}
		{assign var="authorID" value=$author->userID}
		{assign var="messageID" value=$comment.commentID}
		

			<div id="comment_{$comment.commentID}" class="message border">
				<div class="messageInner {@$messageClass} container-{cycle name=postCycle}{if !$author->userID} guestPost{/if}">
					<div class="messageSidebar">
						{if $author->userID}
							<div class="messageAuthor">
								<p class="userName">
									{if 'THREAD_ENABLE_ONLINE_STATUS'|defined && THREAD_ENABLE_ONLINE_STATUS}
										{if $author->isOnline()}
											<img src="{@RELATIVE_WCF_DIR}icon/onlineS.png" alt="" title="{lang username=$author->username}wcf.user.online{/lang}" />
										{else}
											<img src="{@RELATIVE_WCF_DIR}icon/offlineS.png" alt="" title="{lang username=$author->username}wcf.user.offline{/lang}" />
										{/if}
									{/if}
								
									<a href="index.php?page=User&amp;userID={@$author->userID}{@SID_ARG_2ND}" title="{lang username=$author->username}wcf.user.viewProfile{/lang}">
										<span>{$author->username}</span>
									</a>
								</p>
							</div>
							
							{if $this->getStyle()->getVariable('messages.sidebar.alignment') == 'top'}
								{if $author->getAvatar()}
									{assign var=dummy value=$author->getAvatar()->setMaxSize(76, 76)}
								{else}
									<div class="userAvatar{if $this->getStyle()->getVariable('messages.sidebar.avatar.framed')}Framed{/if}">
										<a href="index.php?page=User&amp;userID={@$author->userID}{@SID_ARG_2ND}" title="{lang username=$author->username}wcf.user.viewProfile{/lang}"><img src="{@RELATIVE_WCF_DIR}images/avatars/avatar-default.png" alt=""
											style="width: 76px; height: 76px;{if $this->getStyle()->getVariable('messages.sidebar.avatar.framed')} margin-top: -38px; margin-left: -38px{/if}" /></a>
									</div>
								{/if}
							{/if}
							
							{if $author->getAvatar()}
								<div class="userAvatar{if $this->getStyle()->getVariable('messages.sidebar.avatar.framed')}Framed{/if}">
									<a href="index.php?page=User&amp;userID={@$author->userID}{@SID_ARG_2ND}" title="{lang username=$author->username}wcf.user.viewProfile{/lang}"><img src="{$author->getAvatar()->getURL()}" alt=""
										style="width: {@$author->getAvatar()->width}px; height: {@$author->getAvatar()->height}px;{if $this->getStyle()->getVariable('messages.sidebar.avatar.framed')} margin-top: -{@$author->getAvatar()->height/2|intval}px; margin-left: -{@$author->getAvatar()->width/2|intval}px{/if}" /></a>
								</div>
							{else}
								<div class="userAvatar{if $this->getStyle()->getVariable('messages.sidebar.avatar.framed')}Framed{/if}">
									<a href="index.php?page=User&amp;userID={@$author->userID}{@SID_ARG_2ND}" title="{lang username=$author->username}wcf.user.viewProfile{/lang}"><img src="{@RELATIVE_WCF_DIR}images/avatars/avatar-default.png" alt="" style="width: 100px; height: 100px;{if $this->getStyle()->getVariable('messages.sidebar.avatar.framed')} margin-top: -{@100/2|intval}px; margin-left: -{100/2|intval}px{/if}" /></a>
								</div>
							{/if}
							
							
							{capture assign=userContacts}
								{if $this->user->userID}
									<li><a href="index.php?form=PMNew&amp;userID={@$authorID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/pmEmptyS.png" alt="" title="{lang}wcf.pm.profile.sendPM{/lang}" /></a></li>
								{/if}
							{/capture}
						{else}
							<div class="messageAuthor">
								<p class="userName">{$author->username}</p>
								<p class="userTitle smallFont">{lang}wcf.user.guest{/lang}</p>
							</div>
						{/if}
					</div>
					
					<div class="messageContent">
						<div class="messageContentInner color-{cycle name=messageCycle}">
							<div class="messageHeader">
								<p class="messageCount">
									<span class="messageNumber">{#$startIndex}</span>
								</p>
								<div class="containerIcon">
									<img id="postEdit{$comment.commentID}" src="{@RELATIVE_WBB_DIR}icon/rGalleryM.png" alt="" />
								</div>
								<div class="containerContent">
									<p class="smallFont light">{@$comment.commentAddedDate|time}</p>
								</div>
							</div>
							
							<h3 id="postTopic{$comment.commentID}"><span>{*$post->subject*}</span></h3>
													
							<div class="messageBody" id="postText{$comment.commentID}">
								{@$comment.commentText}
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
										{if $is_authorized}<li><a href="javascript: deleteComment({$comment.commentID})"><img src="{@RELATIVE_WBB_DIR}icon/rGallery_delete.png" style="border: 0; padding: 0; background: none; margin: 0" alt="delete" /></a></li>{/if}
									</ul>
								</div>
							</div>
							<hr />
						</div>
					</div>
					
				</div>
			</div>
		{assign var="startIndex" value=$startIndex + 1}