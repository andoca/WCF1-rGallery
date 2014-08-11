<?php
require_once (WCF_DIR . 'lib/page/AbstractPage.class.php');
require_once (WCF_DIR . 'lib/page/util/menu/HeaderMenu.class.php');
require_once (WCF_DIR . 'lib/data/message/bbcode/MessageParser.class.php');
require_once (WCF_DIR . 'lib/system/io/Tar.class.php');
require_once (WCF_DIR . 'lib/system/io/ZipFile.class.php');
require_once (WBB_DIR . 'lib/system/RGallerySystem.class.php');
HeaderMenu::setActiveMenuItem('de.0xdefec.rgallery.header.menu');

/**
 * This class provides default implementations for the Page interface.
 * This includes the call of the default event listeners for a page: construct, readParameters, assignVariables and show.
 *
 * @author	Andreas Diendorfer
 * @package	de.0xdefec.rgallery
 */
class RGalleryUserPage extends AbstractPage {

	/**
	 * Name of the template for the called page.
	 */
	public $templateName = 'RGalleryUserPage';

	/**
	 * @see Page::show()
	 */
	public function show() {
		if (!WBBCore::getUser()->userID || !WBBCore::getUser()->getPermission('user.rgallery.canUpload')) { // check if the user is logged in
			require_once (WCF_DIR . 'lib/system/exception/PermissionDeniedException.class.php');
			throw new PermissionDeniedException();
		}
		// assign variables
		$this->assignVariables();
		$this->RGalleryInit();
		// remove images from waste if we only want to display
		// 		WCF::getSession()->unregister('images');
		// 		WCF::getSession()->unregister('delete_error');
		// 		WCF::getSession()->unregister('upload_error');
		WCF::getTPL()->assign('itemArray', 0);
		$gallery = new RGallerySystem();
		$getCats = $gallery->getCategories(1);
		$RGalleryCats_value = array();
		$RGalleryCats_name = array();
		foreach ($getCats as $idx => $value) {
			$RGalleryCats_value[] = $value['catID'];
			$RGalleryCats_name[] = $value['catName'];
		}
		if (count($getCats) == 0) { // check if there is at least one category
			require_once (WCF_DIR . 'lib/system/exception/NamedUserException.class.php');
			throw new NamedUserException("You're not allowed to upload in any category! Please contact the site admin to change the category permissions!");
		}
		$getFilterCats = $gallery->getCategories();
		$RGalleryFilterCats_value = array();
		$RGalleryFilterCats_name = array();
		foreach ($getFilterCats as $idx => $value) {
			$RGalleryFilterCats_value[] = $value['catID'];
			$RGalleryFilterCats_name[] = $value['catName'];
		}
		if (!empty($_GET['tag']))
			$gallery->setCurrentTag(urldecode($_GET['tag']), 'user_tag');
		if (isset($_GET['reset_tag']) && $_GET['reset_tag'] == 1)
			WCF::getSession()->unregister('user_tag');
		$tag = $gallery->getCurrentTag('user_tag');
		if ($tag) {
			if ($gallery->getTagId($tag)) {
				define("ACTIVE_TAG", $tag);
			}
		}
		WCF::getTPL()->assign('RGalleryCats_value', $RGalleryCats_value);
		WCF::getTPL()->assign('RGalleryCats_name', $RGalleryCats_name);
		WCF::getTPL()->assign('RGalleryFilterCats_value', $RGalleryFilterCats_value);
		WCF::getTPL()->assign('RGalleryFilterCats_name', $RGalleryFilterCats_name);
		WCF::getTPL()->assign('rGalleryCat', RGalleryItem::getCurrentCategorie('user_cat'));
		if (RGalleryItem::getCurrentCategorie('user_cat') || defined('ACTIVE_TAG'))
			$filter_active = 1; else
			$filter_active = 0;
		WCF::getTPL()->assign('filter_active', $filter_active);
		if (defined('ACTIVE_TAG'))
			WCF::getTPL()->assign('active_tag', ACTIVE_TAG); else
			WCF::getTPL()->assign('active_tag', '');
		$last_comments = $gallery->getUserLastComments();
		$bbcode = new MessageParser();
		foreach ($last_comments as $idx => $last_comment)
			$last_comments[$idx]['commentText'] = $bbcode->parse($last_comment['commentText'], true, false, true);
		WCF::getTPL()->assign('last_comments', $last_comments);
		if (!isset($_GET['rGalleryPage']))
			$_GET['rGalleryPage'] = 1; else {
			if (!is_numeric($_GET['rGalleryPage']))
				$_GET['rGalleryPage'] = 1;
		}
		WCF::getTPL()->assign('rGalleryPage', $_GET['rGalleryPage']);
		$all_tags = $gallery->getTagsArray();
		ksort($all_tags);
		WCF::getTPL()->assign('tags', $all_tags);
		// 		WCF::getTPL()->assign('rGalleryMaxSize', $gallery->getMaxImageSize());
		// call show event
		EventHandler::fireAction($this, 'show');
		if (empty($_POST)) {
			// show template
			if (!empty($this->templateName)) {
				WCF::getTPL()->display($this->templateName);
			}
		} else {
			if (isset($_POST['action'])) {
				if ($_POST['action'] == 'itemUpload') {
					$_FILES = array_reverse($_FILES); // revers the files array, cause our uploader twistes them!
					$onefile = 0;
					foreach ($_FILES as $file) {
						$_FILES['file'] = $file;
						if ($_FILES['file']['name']) {
							// set the memory limit to 128MB - should help some users with memory errors
							@ini_set('memory_limit', '128M');
							@set_time_limit(0);
							$onefile = 1;
							// check the file extension
							$endung = array();
							preg_match('/\.([a-zA-Z0-9]{1,4})$/i', $_FILES['file']['name'], $endung);
							if (strtolower($endung[1]) == 'tar') {
								// so we got an tar archiv..
								$archiv = new Tar($_FILES['file']['tmp_name']);
								$time = microtime();
								$contentList = $archiv->getContentList();
								$any_error = 0;
								foreach ($contentList as $idx => $file) {
									$error = 0;
									$fileIndex = $file['index'];
									$fileInfo = $archiv->getFileInfo($fileIndex);
									$tmp_name = WCF_DIR . 'tmp/' . md5($fileInfo['filename'] . $time);
									$archiv->extract($fileIndex, $tmp_name);
									$_FILES['file']['tmp_name'] = $tmp_name;
									$_FILES['file']['name'] = $fileInfo['filename'];
									$_FILES['file']['size'] = filesize($tmp_name);
									preg_match('/\.([a-zA-Z0-9]{1,4})$/i', $_FILES['file']['name'], $endung);
									switch (strtolower($endung[1])) {
										case 'png':
											$_FILES['file']['type'] = 'image/png';
											break;
										case 'gif':
											$_FILES['file']['type'] = 'image/gif';
											break;
										case 'jpg':
											$_FILES['file']['type'] = 'image/jpeg';
											break;
										case 'jpeg':
											$_FILES['file']['type'] = 'image/jpeg';
											break;
										default:
											WCF::getSession()->register('upload_error', 'wrongfiletype');
											$error = 1;
											$any_error = 1;
											break;
									}
									if ($error == 0) {
										$item = new RGalleryItem();
										$upload = $item->upload();
										if ($upload != 1) { // upload failed!
											WCF::getSession()->register('upload_error', $upload);
										} else {
											if ($any_error == 0) {
												WCF::getSession()->register('upload_error', 'no_error');
											}
										}
									}
									unlink($tmp_name);
								}
							} else {
								// do the normal upload
								$item = new RGalleryItem();
								$upload = $item->upload();
								if ($upload != 1) { // upload failed!
									WCF::getSession()->register('upload_error', $upload);
								} else {
									WCF::getSession()->register('upload_error', 'no_error');
								}
							}
						} // end if($_FILES['file'][name])
					} // end foreach $_FILES
					if ($onefile == 0)
						WCF::getSession()->register('upload_error', 'nofile');
				} elseif ($_POST['action'] == 'itemDelete') {
					$images = WCF::getSession()->getVar('images');
					if (count($images)) {
						$deleted_error = 0;
						foreach ($images as $idx => $value) {
							$item = new RGalleryItem();
							$item->setItemID(str_replace("rG_item_", '', $idx));
							if($item->checkPermissions()) {
								$delete_ok = $item->deleteItem();
								if ($delete_ok) {
									unset($images[$idx]);
								} else {
									$deleted_error = 1;
								}
							}
							else $deleted_error = 1;
						}
						WCF::getSession()->register('images', $images);
						WCF::getSession()->register('delete_error', $deleted_error);
					}
				}
			}
		}
	}

	private function RGalleryInit() {
		$data = array();
		$gallery = new RGallerySystem();
		if (!empty($_GET['rGalleryCat']))
			$gallery->setCurrentCategorie($_GET['rGalleryCat'], 'user_cat');
		$data['itemArray'] = $gallery->getUserItemsListing();
		return $data;
	}
}
?>