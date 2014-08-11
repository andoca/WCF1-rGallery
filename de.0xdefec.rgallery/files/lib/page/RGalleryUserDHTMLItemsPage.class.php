<?php
require_once (WCF_DIR . 'lib/page/AbstractPage.class.php');
require_once (WBB_DIR . 'lib/system/RGallerySystem.class.php');

/**
 * This class provides default implementations for the Page interface.
 * This includes the call of the default event listeners for a page: construct, readParameters, assignVariables and show.
 *
 * @author	Andreas Diendorfer
 * @package	de.0xdefec.RGallery
 */
class RGalleryUserDHTMLItemsPage extends AbstractPage {

	/**
	 * Name of the template for the called page.
	 */
	public $templateName = 'RGalleryDHTMLItems';

	/**
	 * @see Page::show()
	 */
	public function show() {
		if (!WBBCore::getUser()->getPermission('user.rgallery.canView')) {
			require_once (WCF_DIR . 'lib/system/exception/PermissionDeniedException.class.php');
			throw new PermissionDeniedException();
		}
		// assign variables
		$this->assignVariables();
		$upload_error = WCF::getSession()->getVar('upload_error');
		$delete_error = WCF::getSession()->getVar('delete_error');
		// 		$langTmp = new Language(WBBCore::getUser()->languageID);
		$gallery = new RGallerySystem();
		// 		WCF::getTPL()->assign('rGalleryMaxSize', $maxSize);
		// check for inconsistend databaseentries (like entries where files are missing)
		// 		if($gallery->checkDatabase(WBBCore::getUser()->userID) > 0) {
		// 			WCF::getTPL()->assign('rGalleryCheck', 'eintraege bereinigt');
		// 			}
		if ($upload_error != 'no_error' && !empty($upload_error)) { // when there was an error
			$gallery->logger("upload failed.. sry! $upload_error");
			$language = new Language(WBBCore::getUser()->languageID);
			$error_msg = $language->get('de.0xdefec.rgallery.error_' . $upload_error);
			unset($language);
		} else
			$error_msg = 0;
		if (!isset($_GET['rGalleryPage']))
			$_GET['rGalleryPage'] = 1; else {
			if (!is_numeric($_GET['rGalleryPage']))
				$_GET['rGalleryPage'] = 1;
		}
		$tag = $gallery->getCurrentTag('user_tag');
		if ($tag) {
			if ($gallery->getTagId($tag)) {
				define("ACTIVE_TAG", $tag);
			}
		}
		$data['itemArray'] = $gallery->getUserItemsListing($_GET['rGalleryPage']);
		// 		var_dump($data['itemArray']);
		WCF::getTPL()->assign('rGalleryPage', $_GET['rGalleryPage']);
		WCF::getTPL()->assign('itemArray', $data['itemArray']);
		WCF::getTPL()->assign('upload_error', $upload_error);
		WCF::getTPL()->assign('delete_error', $delete_error);
		WCF::getTPL()->assign('error_msg', $error_msg);
		WCF::getTPL()->assign('totalpages', RGalleryItem::getUserPages());
		WCF::getTPL()->assign('wasteitems', RGalleryItem::getWasteItems(1));
		WCF::getTPL()->assign('has_elements', count($data['itemArray']));
		if (defined('ACTIVE_TAG'))
			WCF::getTPL()->assign('active_tag', ACTIVE_TAG); else
			WCF::getTPL()->assign('active_tag', '');
		WCF::getSession()->unregister('upload_error');
		// call show event
		EventHandler::fireAction($this, 'show');
		WCF::getTPL()->display($this->templateName);
	}
}
?>