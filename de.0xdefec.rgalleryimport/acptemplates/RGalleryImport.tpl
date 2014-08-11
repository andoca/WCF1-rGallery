{include file='header'}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Suggestion.class.js"></script>
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabMenu.class.js"></script>

<div class="mainHeadline">
	<img src="{@RELATIVE_WBB_DIR}icon/rGalleryL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wcf.acp.rgalleryimport.maintitle{/lang}</h2>
	</div>
</div>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

<fieldset>
	<legend>{lang}wcf.acp.rgalleryimport.maintitle{/lang}</legend>

		<form method="post" action="index.php?form=RGalleryImport{@SID_ARG_2ND}">
			<fieldset>
				<legend><strong>{lang}wcf.acp.rgalleryimport.local{/lang}</strong></legend>
				<p class="description">{lang}wcf.acp.rgalleryimport.local.desc{/lang}</p>
				<div class="formElement">
					<div class="formFieldLabel">
						<label for="path">{lang}wcf.acp.rgalleryimport.local.path{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="path" name="path" value="" />
					</div>
					<div class="formFieldDesc">
						{lang}wcf.acp.rgalleryimport.local.path.desc{/lang}
					</div>
					
					<div class="formFieldLabel">
						<label for="userID">{lang}wcf.acp.rgalleryimport.local.userID{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="userID" name="userID" value="" />
					</div>
					<div class="formFieldDesc">
						{lang}wcf.acp.rgalleryimport.local.userID.desc{/lang}
					</div>
					
					<div class="formFieldLabel">
						<label for="tags">{lang}wcf.acp.rgalleryimport.local.tags{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="tags" name="tags" value="" />
					</div>
					<div class="formFieldDesc">
						{lang}wcf.acp.rgalleryimport.local.tags.desc{/lang}
					</div>
					
					<div class="formFieldLabel">
						<label for="store_orig">{lang}wcf.acp.rgalleryimport.local.store_orig{/lang}</label>
					</div>
					<div class="formField">
						<input type="checkbox" id="store_orig" name="store_orig" value="1" />
					</div>
					<div class="formFieldDesc">
						{lang}wcf.acp.rgalleryimport.local.store_orig.desc{/lang}
					</div>
					
					<div class="formFieldLabel">
						<label for="cat">{lang}wcf.acp.rgalleryimport.local.cat{/lang}</label>
					</div>
					<div class="formField">
						<select name="cat" id="cat">
							{htmloptions options=$cats selected=1}
						</select>
					</div>
					<div class="formFieldDesc">
						{lang}wcf.acp.rgalleryimport.local.cat.desc{/lang}
					</div>
				</div>
				<div class="formSubmit">
					<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
					<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
					<input type="hidden" name="import" value="local" />
					{@SID_INPUT_TAG}
				</div>
			</fieldset>
		</form>

		<form method="post" action="index.php?form=RGalleryImport{@SID_ARG_2ND}">
			<fieldset>
				<legend><strong>{lang}wcf.acp.rgalleryimport.jgs{/lang}</strong></legend>
				<p class="description">{lang}wcf.acp.rgalleryimport.jgs.desc{/lang}</p>
				<div class="formElement">
					<div class="formFieldLabel">
						<label for="path">{lang}wcf.acp.rgalleryimport.jgs.mysql.server{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="mysql_server" name="mysql_server" value="localhost" />
					</div>
					<div class="formFieldDesc">
						{lang}wcf.acp.rgalleryimport.jgs.mysql.server.desc{/lang}
					</div>
					
					<div class="formFieldLabel">
						<label for="userID">{lang}wcf.acp.rgalleryimport.jgs.mysql.username{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="mysql_username" name="mysql_username" value="" />
					</div>
					<div class="formFieldDesc">
						{lang}wcf.acp.rgalleryimport.jgs.mysql.username.desc{/lang}
					</div>
					
					<div class="formFieldLabel">
						<label for="userID">{lang}wcf.acp.rgalleryimport.jgs.mysql.password{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="mysql_password" name="mysql_password" value="" />
					</div>
					<div class="formFieldDesc">
						{lang}wcf.acp.rgalleryimport.jgs.mysql.password.desc{/lang}
					</div>
					
					<div class="formFieldLabel">
						<label for="userID">{lang}wcf.acp.rgalleryimport.jgs.mysql.db{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="mysql_db" name="mysql_db" value="" />
					</div>
					<div class="formFieldDesc">
						{lang}wcf.acp.rgalleryimport.jgs.mysql.db.desc{/lang}
					</div>
					
					<div class="formFieldLabel">
						<label for="userID">{lang}wcf.acp.rgalleryimport.jgs.mysql.pre{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="mysql_pre" name="mysql_pre" value="bb1_" />
					</div>
					<div class="formFieldDesc">
						{lang}wcf.acp.rgalleryimport.jgs.mysql.pre.desc{/lang}
					</div>
					
					<div class="formFieldLabel">
						<label for="userID">{lang}wcf.acp.rgalleryimport.jgs.wbbpath{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="wbbpath" name="wbbpath" value="" />
					</div>
					<div class="formFieldDesc">
						{lang}wcf.acp.rgalleryimport.jgs.wbbpath.desc{/lang}
					</div>
					
					<div class="formFieldLabel">
						<label for="store_orig">{lang}wcf.acp.rgalleryimport.local.store_orig{/lang}</label>
					</div>
					<div class="formField">
						<input type="checkbox" id="store_orig" name="store_orig" value="1" />
					</div>
					<div class="formFieldDesc">
						{lang}wcf.acp.rgalleryimport.local.store_orig.desc{/lang}
					</div>
				</div>
				<div class="formSubmit">
					<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
					<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
					<input type="hidden" name="import" value="jgs" />
					{@SID_INPUT_TAG}
				</div>
			</fieldset>
		</form>
		
		<form method="post" action="index.php?form=RGalleryImport{@SID_ARG_2ND}">
			<fieldset>
				<legend><strong>{lang}wcf.acp.rgalleryimport.wcf{/lang}</strong></legend>
				<p class="description">{lang}wcf.acp.rgalleryimport.wcf.desc{/lang}</p>
				<div class="formSubmit">
					<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
					<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
					<input type="hidden" name="import" value="wcf" />
					{@SID_INPUT_TAG}
				</div>
			</fieldset>
		</form>
</fieldset>

{include file='footer'}