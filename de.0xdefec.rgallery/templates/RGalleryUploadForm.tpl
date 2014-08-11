				<form name="uploadForm" action="{@RELATIVE_WBB_DIR}index.php?page=RGalleryUser" method="post" enctype="multipart/form-data" onsubmit="upload(this); return true">
					<!--form name="uploadForm" action="{@RELATIVE_WBB_DIR}index.php?page=RGalleryUser{@SID_ARG_2ND}" method="post" enctype="multipart/form-data"-->
						<input type="hidden" name="action" value="itemUpload" />
						{@SID_INPUT_TAG}
						<table>
							<tr>
								<td style="vertical-align: top">{lang}de.0xdefec.rgallery.upload.file{/lang}</td>
								<td id="files_uploads"><input id="file_upload" type="file" name="file_0" /><br />
								<div id="files_list"></div>
								<script>
									delete(my_upload);
									var my_upload = new MultiSelector( document.getElementById( 'files_list' ), {@RGALLERY_UPLOADS_AT_ONCE} );
									my_upload.addElement( document.getElementById( 'file_upload' ) );
								</script>
								</td>
							</tr>
							<tr>
								<td>{lang}de.0xdefec.rgallery.upload.title{/lang}</td>
								<td><input class="inputText" type="text" name="itemName" maxlength="64" /></td>
							</tr>
							<tr>
								<td>{lang}de.0xdefec.rgallery.upload.categorie{/lang}</td>
								<td>{htmloptions output=$RGalleryCats_name values=$RGalleryCats_value name=itemCat selected=1}</td>
							</tr>
							<tr>
								<td>{lang}de.0xdefec.rgallery.upload.comment{/lang}</td>
								<td><textarea name="itemComment" rows="5"></textarea></td>
							</tr>
							<tr>
								<td>{lang}de.0xdefec.rgallery.upload.tags{/lang}</td>
								<td>
								<input class="inputText" type="text" id="autocomplete" name="itemTags"  id="itemTags" maxlength="2000" style="width: 80%" /> <span id="indicator1" style="display: none;vertical-align:middle"><img src="{@RELATIVE_WBB_DIR}icon/indicator.gif" alt="" /></span>
								<div id="autocomplete_choices" class="autocomplete"></div>
								<br />{lang}de.0xdefec.rgallery.upload.tags_desc{/lang}</td>
							
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td><input type="submit" name="submit" value="{lang}de.0xdefec.rgallery.upload.save{/lang}" /> <div id="loading" style="display: none"><img src="{@RELATIVE_WBB_DIR}icon/indicator.gif" /> {lang}de.0xdefec.rgallery.upload.loading{/lang}</div></td>
							</tr>
						</table>
					</form>
				<script language="JavaScript" type="text/javascript">
					new Ajax.Autocompleter("autocomplete", "autocomplete_choices", "{@RELATIVE_WBB_DIR}index.php?page=RGalleryAction{@SID_ARG_2ND}", { paramName: "tagStr", minChars: 2, indicator: 'indicator1', parameters:''});
				</script>