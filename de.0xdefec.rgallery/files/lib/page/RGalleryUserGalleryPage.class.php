<?php
require_once (WCF_DIR . 'lib/page/AbstractPage.class.php');
require_once (WCF_DIR . 'lib/page/util/menu/HeaderMenu.class.php');
require_once (WBB_DIR . 'lib/system/RGallerySystem.class.php');
HeaderMenu::setActiveMenuItem('de.0xdefec.rgallery.header.menu');

/**
 * This class provides default implementations for the Page interface.
 * This includes the call of the default event listeners for a page: construct, readParameters, assignVariables and show.
 *
 * @author	Andreas Diendorfer
 * @package	de.0xdefec.rgallery
 */
class RGalleryUserGalleryPage extends AbstractPage {

	/**
	 * Name of the template for the called page.
	 */
	public $templateName = 'RGalleryUserGallery';

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
		if (!isset($_GET['rGalleryPage']))
			$_GET['rGalleryPage'] = 1; else {
			if (!is_numeric($_GET['rGalleryPage']))
				$_GET['rGalleryPage'] = 1;
		}
		$data['itemArray'] = RGalleryItem::getUserItemsListing($_GET['rGalleryPage'], $_GET['userID']);
		$totalpages = RGallerySystem::getGalleryPages($_GET['userID']);
		$user = new User($_GET['userID']);
		WCF::getTPL()->assign('RGallery_items', $data['itemArray']);
		WCF::getTPL()->assign('user', $user);
		WCF::getTPL()->assign('totalpages', $totalpages);
		WCF::getTPL()->assign('rGalleryPage', $_GET['rGalleryPage']);
		WCF::getTPL()->assign('userID', $user->userID);
		// call show event
		EventHandler::fireAction($this, 'show');
		if (!empty($this->templateName)) {
			WCF::getTPL()->display($this->templateName);
		}
	}
}
?>