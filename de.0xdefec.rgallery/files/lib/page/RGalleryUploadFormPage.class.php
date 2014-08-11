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
class RGalleryUploadFormPage extends AbstractPage {

	/**
	 * Name of the template for the called page.
	 */
	public $templateName = 'RGalleryUploadForm';

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
		WCF::getTPL()->assign('itemArray', 0);
		$gallery = new RGallerySystem();
		$getCats = $gallery->getCategories(1);
		$RGalleryCats_value = array();
		$RGalleryCats_name = array();
		foreach ($getCats as $idx => $value) {
			$RGalleryCats_value[] = $value['catID'];
			$RGalleryCats_name[] = $value['catName'];
		}
		WCF::getTPL()->assign('RGalleryCats_value', $RGalleryCats_value);
		WCF::getTPL()->assign('RGalleryCats_name', $RGalleryCats_name);
		// call show event
		EventHandler::fireAction($this, 'show');
		// show template
		if (!empty($this->templateName)) {
			WCF::getTPL()->display($this->templateName);
		}
	}
}
?>