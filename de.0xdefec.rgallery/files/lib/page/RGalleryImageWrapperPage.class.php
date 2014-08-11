<?php
require_once (WCF_DIR . 'lib/page/AbstractPage.class.php');
require_once (WCF_DIR . 'lib/page/util/menu/HeaderMenu.class.php');
require_once (WCF_DIR . 'lib/data/message/bbcode/MessageParser.class.php');
require_once (WBB_DIR . 'lib/system/RGallerySystem.class.php');
HeaderMenu::setActiveMenuItem('de.0xdefec.rgallery.header.menu');

/**
 * This class provides default implementations for the Page interface.
 * This includes the call of the default event listeners for a page: construct, readParameters, assignVariables and show.
 *
 * @author	Andreas Diendorfer
 * @package	de.0xdefec.RGallery
 */
class RGalleryImageWrapperPage extends AbstractPage {

	/**
	 * @see Page::show()
	 */
	public $templateName = 'RGalleryImagePage';

	public function show() {
		if (! WBBCore::getUser()->getPermission('user.rgallery.canView')) {
			// check if the user is allowed to view this image
			if (isset($_GET['type']) && $_GET['type'] == 'page') {
				require_once (WCF_DIR . 'lib/system/exception/PermissionDeniedException.class.php');
				throw new PermissionDeniedException();
			}
			else {
				$this->show_error_image();
				return false;
			}
		}
		if (empty($_GET['type'])) $_GET['type'] = 'page';
		if (empty($_GET['itemID']) && isset($_GET['catID'])) {
			$sql = "SELECT * FROM wcf" . WCF_N . "_rGallery_items as i, wcf" . WCF_N . "_rGallery_items_cat as ic WHERE i.itemID=ic.itemID AND ic.catID='" . RGallerySystem::prepInput($_GET['catID']) . "' ORDER BY i.itemAddedDate DESC LIMIT 1";
			$row = WCF::getDB()->getFirstRow($sql);
			$_GET['itemID'] = $row['itemID'];
			if (empty($_GET['itemID'])) {
				// when there is no image in this cat, return an default image
				$this->show_error_image();
				return false;
			}
		}
		// first we have to check if the item exists
		if (! isset($_GET['itemID']) || empty($_GET['itemID']) || ! is_numeric($_GET['itemID'])) {
			require_once (WCF_DIR . 'lib/system/exception/IllegalLinkException.class.php');
			throw new IllegalLinkException();
		}
		else {
			$sql = "SELECT * FROM wcf" . WCF_N . "_rGallery_items WHERE itemID = '" . RGallerySystem::prepInput($_GET['itemID']) . "'";
			$row = WCF::getDB()->sendQuery($sql);
			if (WCF::getDB()->countRows($row) == 0) {
				if ($_GET['type'] == 'page') {
					require_once (WCF_DIR . 'lib/system/exception/IllegalLinkException.class.php');
					throw new IllegalLinkException();
				}
				else {
					$this->show_error_image();
					return false;
				}
			}
		}
		$item = new RGalleryItem();
		$item->setItemID(intval($_GET['itemID']));
		switch ($_GET['type']) {
			case 'thumb' :
				$item->show('thumb');
				break;
			case 'tthumb' :
				$item->show('tthumb');
				break;
			case 'preview' :
				$item->show('preview');
				break;
			case 'original' :
				$item->show('original');
				break;
			case 'page' :
				if (isset($_POST['action'])) {
					if ($_POST['action'] == 'updateItem' && $item->checkPermissions()) $item->update($_POST);
					if ($_POST['action'] == 'commentItem' && WBBCore::getUser()->userID != 0 && trim($_POST['commentText']) != '') $item->addComment($_POST);
					if ($_POST['action'] == 'userrating' && WBBCore::getUser()->userID != 0 && $_POST['rating'] <= 5 && $_POST['rating'] >= 1) $item->setUserrating($_POST['rating']);
				}
				$this->generate_image_page($item);
				break;
			case 'delete' :
				$error = 0;
				if ($item->checkPermissions()) {
					if (! $item->deleteItem(RGallerySystem::prepInput($_GET['itemID']))) $error = 1;
				}
				else
					$error = 1;
				if ($error) {
					require_once (WCF_DIR . 'lib/system/exception/NamedUserException.class.php');
					throw new NamedUserException('Error while deleting!');
				}
				if (! isset($_GET['from']) || $_GET['from'] != 'user') $_GET['from'] = '';
				WCF::getTPL()->assign('from', $_GET['from']);
				WCF::getTPL()->display('RGalleryImageDeleted');
				break;
			default :
				$item->show('image');
		}
		return true;
	}

	private function generate_image_page($item) {
		$this->assignVariables();
		$data = $item->getData();
		// raise the click counter per 1 if the user is not the owner
		if ($data['ownerID'] != WBBCore::getUser()->userID) $item->raiseCounter();
		$data['count_tags'] = count($data['tags']);
		$userid = WBBCore::getUser()->userID;
		if (! isset($_GET['from'])) $_GET['from'] = '';
		// so if there is a "from" set we check if the current user is the owner
		if ($_GET['from'] == 'user') {
			if ($data['ownerID'] != WBBCore::getUser()->userID) $_GET['from'] = '';
		}
		$getCats = RGallerySystem::getCategories(1);
		$RGalleryCats_value = array();
		$RGalleryCats_name = array();
		foreach ( $getCats as $idx => $value ) {
			$RGalleryCats_value[] = $value['catID'];
			$RGalleryCats_name[] = $value['catName'];
		}
		$neighbors = $item->getNeighbors();
		$owner = new UserProfile($item->itemOwnerID);
		$tags_enc = array();
		foreach ( $data['tags'] as $idx => $value )
			$tags_enc[$idx] = urlencode($value);
		if ($_GET['from'] == 'user') {
			$cat = RGallerySystem::getCurrentCategorie('user_cat');
		}
		else {
			$cat = RGallerySystem::getCurrentCategorie();
		}
		if ($cat) {
			if (isset($getCats[$cat]['catName']))
				$current_cat = $getCats[$cat]['catName'];
			else {
				WCF::getSession()->unregister('user_cat');
				$current_cat = '';
			}
		}
		else
			$current_cat = '';
		$bbcode = new MessageParser();
		$data['itemCommentBBCode'] = $bbcode->parse($data['itemComment'], true, false, true);
		if (! isset($_GET['from']) || $_GET['from'] != 'user') $_GET['from'] = '';
		WCF::getTPL()->assign('RGalleryCats_value', $RGalleryCats_value);
		WCF::getTPL()->assign('RGalleryCats_name', $RGalleryCats_name);
		WCF::getTPL()->assign('neighbors', $neighbors);
		WCF::getTPL()->assign('data', $data);
		WCF::getTPL()->assign('tags_enc', $tags_enc);
		WCF::getTPL()->assign('from', $_GET['from']);
		WCF::getTPL()->assign('userid', $userid);
		WCF::getTPL()->assign('current_cat', $current_cat);
		WCF::getTPL()->assign('is_authorized', RGallerySystem::isGalleryModerator($userid));
		WCF::getTPL()->assign('owner', $owner);
		WCF::getTPL()->assign('user', new UserProfile(WBBCore::getUser()->userID));
		// call show event
		EventHandler::fireAction($this, 'show');
		// show template
		if (! empty($this->templateName)) {
			WCF::getTPL()->display($this->templateName);
		}
	}

	public function show_error_image() {
		header("Content-Type: image/png");
		header("Content-Transfer-Encoding: binary");
		header("Pragma: ");
		header("Cache-Control: private", false);
		header("Content-Disposition: inline; filename=\"rGalleryXL.png\";");
		header("Content-Length: " . filesize(WBB_DIR . "icon/rGalleryXL.png"));
		@set_time_limit(0);
		$fp = fopen(WBB_DIR . "icon/rGalleryXL.png", 'rb');
		@fpassthru($fp);
		return true;
	}
}
?>