<?php
require_once (WCF_DIR . 'lib/page/AbstractPage.class.php');
require_once (WCF_DIR . 'lib/system/session/UserSession.class.php');
require_once (WBB_DIR . 'lib/system/RGallerySystem.class.php');

/**
 * This class provides default implementations for the Page interface.
 * This includes the call of the default event listeners for a page: construct, readParameters, assignVariables and show.
 *
 * @author	Andreas Diendorfer
 * @package	de.0xdefec.rgallery
 */
class RGalleryActionPage extends AbstractPage {

	/**
	 * @see Page::show()
	 */
	public function show() {
		if (RGallerySystem::isGalleryModerator(WBBCore::getUser()->userID)) { // the requesting user is gallery mod - he/she is allowed to do everything
			if (isset($_GET['id'])) {
				switch ($_GET['type']) {
					case 'deleteComment':
						RGallerySystem::logger('Comment ID:' . $_GET['id'] . ' deleted');
						if (RGallerySystem::deleteComment($_GET['id']))
							return true;
						break;
					case 'deleteItem':
						RGallerySystem::logger('Item ID:' . $_GET['id'] . ' deleted by mod ' . WBBCore::getUser()->userID);
						$item = new RGalleryItem();
						$item->setItemID($_GET['id']);
						if ($item->deleteItem())
							return true;
						break;
				}
			}
		} else { // not a mod - we have to check if the user is the owner!
			if (isset($_GET['id'])) {
				switch ($_GET['type']) {
					case 'deleteItem':
						RGallerySystem::logger('Item ID:' . $_GET['id'] . ' deleted by owner');
						$item = new RGalleryItem();
						$item->setItemID($_GET['id']);
						$data = $item->getData();
						if ($data['ownerID'] != WBBCore::getUser()->userID)
							return false; // user is not the owner of the item!!!!
						if ($item->deleteItem())
							return true;
						break;
				}
			}
		}
		if (isset($_POST['tagStr'])) {
			// start the tag suggest!
			$_POST['tagStr'] = str_replace('%', '', $_POST['tagStr']);
			$_POST['tagStr'] = str_replace('--', '', $_POST['tagStr']);
			$tags = explode(',', $_POST['tagStr']);
			
			$lasttag = trim($tags[count($tags) - 1]);
			if (empty($lasttag))
				return false;
				
			$othertags = '';
			$dontwant = '';
			
			for ($i = 0; $i < (count($tags) - 1); $i++) {
				$othertags .= trim($tags[$i]) . ', ';
				$dontwant .= " AND tag!='" . RGallerySystem::prepInput(trim($tags[$i])) . "'";
			}
			$othertags = trim($othertags);
			if (!empty($othertags))
				$othertags = $othertags . ' ';
			$sql = "SELECT tag FROM wcf" . WCF_N . "_rGallery_tags WHERE tag LIKE '" . RGallerySystem::prepInput($lasttag) . "%' " . $dontwant . " ORDER BY tag ASC LIMIT 15";
			$result = WCF::getDB()->sendQuery($sql);
			$results = array();
			echo "<ul>";
			while ($row = WCF::getDB()->fetchArray($result)) {
				$results[] = $row['tag'];
				echo "<li>" . htmlentities($othertags . $row['tag'], ENT_QUOTES, 'UTF-8') . "</li>";
			}
			echo "</ul>";
		}
		return false;
	}
}
?>