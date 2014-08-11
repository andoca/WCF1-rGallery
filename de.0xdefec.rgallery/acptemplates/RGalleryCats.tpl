{include file='header'}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Suggestion.class.js"></script>
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabMenu.class.js"></script>

<div class="mainHeadline">
	<img src="{@RELATIVE_WBB_DIR}icon/rGalleryL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wcf.acp.rgallery.maintitle{/lang}</h2>
	</div>
</div>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

<fieldset>
	<legend>{lang}wcf.acp.rgallery.maintitle{/lang}</legend>

		<form method="post" action="index.php?form=RGalleryCats{@SID_ARG_2ND}">
			<fieldset>
				<legend><strong>{lang}wcf.acp.rgallery.newCat{/lang}</strong></legend>
				<div class="formElement">
					<div class="formFieldLabel">
						<label for="catName">{lang}wcf.acp.rgallery.catName{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="catName" name="catName" value="" />
					</div>
					
					<!--div class="formFieldLabel">
						<label for="catComment">{lang}wcf.acp.rgallery.catComment{/lang}</label>
					</div>
					<div class="formField">
						<textarea class="inputText" id="catComment" name="catComment" /></textarea>
					</div-->
					
					<div class="formFieldLabel">
						<label for="catAuthorized_group">{lang}wcf.acp.rgallery.catAuthorized_groups{/lang}</label>
					</div>
					<div class="formField">
						<select name="catAuthorized_group" id="catAuthorized_group">
							<option value="">&nbsp;</option>
							{htmloptions output=$groups values=$groupIDs}
						</select>
					</div>
					
					<div class="formFieldLabel">
						<label for="catWriteable">{lang}wcf.acp.rgallery.catWriteable{/lang}</label>
					</div>
					<div class="formField">
						<input type="checkbox" name="catWriteable" id="catWriteable" value="1" checked="checked" />
					</div>
				</div>
				<div class="formSubmit">
					<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
					<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
					{@SID_INPUT_TAG}
				</div>
			</fieldset>
		</form>


	{foreach from=$cats item=cat key=key}
		<form method="post" action="index.php?form=RGalleryCats{@SID_ARG_2ND}">
			<fieldset>
				<legend>{lang}de.0xdefec.rgallery.upload.categorie{/lang} "<strong>{$cat.catName}</strong>"</legend>
				<div class="formElement">
					<div class="formFieldLabel">
						<label for="catName">{lang}wcf.acp.rgallery.catName{/lang}</label>
					</div>
					<div class="formField">
						<input type="text" class="inputText" id="catName" name="catName" value="{$cat.catName}" />
					</div>
					
					<!--div class="formFieldLabel">
						<label for="catComment">{lang}wcf.acp.rgallery.catComment{/lang}</label>
					</div>
					<div class="formField">
						<textarea class="inputText" id="catComment" name="catComment" />{$cat.catComment}</textarea>
					</div-->
					
					<div class="formFieldLabel">
						<label for="catAuthorized_group">{lang}wcf.acp.rgallery.catAuthorized_groups{/lang}</label>
					</div>
					<div class="formField">
						<select name="catAuthorized_group" id="catAuthorized_group">
							<option value="">&nbsp;</option>
							{htmloptions output=$groups values=$groupIDs selected=$cat.catAuthorized_group}
						</select>
					</div>
					
					<div class="formFieldLabel">
						<label for="catWriteable">{lang}wcf.acp.rgallery.catWriteable{/lang}</label>
					</div>
					<div class="formField">
						<input type="checkbox" name="catWriteable" id="catWriteable" value="1"{if $cat.catWriteable} checked="checked"{/if} />
					</div>
					
					<div class="formFieldLabel">
						<label for="catDelete">{lang}wcf.acp.rgallery.catDelete{/lang}</label>
					</div>
					<div class="formField">
						{if $cat.catID != 1}
							<input type="checkbox" name="catDelete" id="catDelete" value="1" />
						{else}
							{lang}wcf.acp.rgallery.catNotDeletable{/lang}
						{/if}
					</div>
				</div>
				<div class="formSubmit">
					<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
					<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
					<input type="hidden" name="catID" value="{$cat.catID}" />
					{@SID_INPUT_TAG}
				</div>
			</fieldset>
		</form>
	{/foreach}
</fieldset>

{include file='footer'}