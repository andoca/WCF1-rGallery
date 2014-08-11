<?php
require_once (WCF_DIR . 'lib/system/event/EventListener.class.php');
require_once (WBB_DIR . 'lib/system/RGallerySystem.class.php');

/**
 * adds the rgallery to the userprofile
 *
 * @author      Andreas Diendorfer
 * @package     de.0xdefec.rgallery
 */
class RGalleryUserPageListener implements EventListener {

	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (WCF::getUser()->getPermission('user.rgallery.canView')) {
				$data['itemArray'] = RGalleryItem::getUserItemsListing(1, $eventObj->frame->getUserID());
				WCF::getTPL()->assign(array(
					'userID' => $eventObj->frame->getUserID(),
					'itemArray' => $data['itemArray'],
					'has_elements' => count($data['itemArray'])));
				WCF::getTPL()->append('additionalContent3', WCF::getTPL()->fetch('RGalleryUserProfileCenter'));
			}
	}
}
?>
