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
class RGalleryUserProfileDHTMLItemsPage extends AbstractPage {

	/**
	 * Name of the template for the called page.
	 */
	public $templateName = 'RGalleryDHTMLProfileItems';

	/**
	 * @see Page::show()
	 */
	public function show() {
		// assign variables
		$this->assignVariables();
		if (!WBBCore::getUser()->getPermission('user.rgallery.canView')) {
			require_once (WCF_DIR . 'lib/system/exception/PermissionDeniedException.class.php');
			throw new PermissionDeniedException();
		}
		if (!is_numeric($_GET['userID'])) {
			require_once (WCF_DIR . 'lib/system/exception/NamedUserException.class.php');
			throw new NamedUserException('Wrong ID!');
		}
		$data['itemArray'] = RGalleryItem::getUserItemsListing(1, $_GET['userID']);
		WCF::getTPL()->assign('userID', $_GET['userID']);
		WCF::getTPL()->assign('itemArray', $data['itemArray']);
		WCF::getTPL()->assign('has_elements', count($data['itemArray']));
		// call show event
		EventHandler::fireAction($this, 'show');
		WCF::getTPL()->display($this->templateName);
	}
}
?>