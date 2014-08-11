<?php
require_once (WCF_DIR . 'lib/page/AbstractPage.class.php');
require_once (WCF_DIR . 'lib/system/session/UserSession.class.php');
require_once (WBB_DIR . 'lib/system/RGallerySystem.class.php');

/**
 * This class provides default implementations for the Page interface.
 * This includes the call of the default event listeners for a page: construct, readParameters, assignVariables and show.
 *
 * @author	Andreas Diendorfer
 * @package	de.0xdefec.RGallery
 */
class RGalleryUserStatusPage extends AbstractPage {

	public $templateName = 'RGalleryUserStatus';

	/**
	 * @see Page::show()
	 */
	public function show() {
		$this->assignVariables();
		if (!WBBCore::getUser()->getPermission('user.rgallery.canView')) {
			require_once (WCF_DIR . 'lib/system/exception/PermissionDeniedException.class.php');
			throw new PermissionDeniedException();
		}
		$user_status = RGallerySystem::getUserStatus();
		WCF::getTPL()->assign('user_status', $user_status);
		EventHandler::fireAction($this, 'show');
		WCF::getTPL()->display($this->templateName);
	}
}
?>