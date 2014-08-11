<?php
require_once (WCF_DIR . 'lib/acp/action/WorkerAction.class.php');
require_once (WBB_DIR . 'lib/system/RGallerySystem.class.php');
require_once (WCF_DIR . 'lib/system/language/LanguageEditor.class.php');
require_once (WCF_DIR . 'lib/data/user/UserProfile.class.php');
require_once (WCF_DIR . 'lib/system/exception/NamedUserException.class.php');

ini_set('display_errors', 1);

/**
 * exports rGallery images to woltlab's user gallery
 *
 * @author	Andreas Diendorfer
 * @package	de.0xdefec.rgalleryimport
 */
class RGalleryImportWCFAction extends WorkerAction {
	public $action = 'RGalleryImportWCF';

	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {

		parent::readParameters();
	}

	public function getData($itemID) {
		require_once (WCF_DIR . 'lib/data/user/UserProfile.class.php');

		$sql = "SELECT o.ownerID as ownerID,
				ic.catID as catID,
				c.catName as catName,
				i.*
			FROM 	`wcf" . WCF_N . "_rGallery_items` as i,
				`wcf" . WCF_N . "_rGallery_items_owner` as o,
				`wcf" . WCF_N . "_rGallery_items_cat` as ic,
				`wcf" . WCF_N . "_rGallery_cats` as c
			WHERE 	i.itemID='" . intval($itemID) . "'
				AND o.itemID=i.itemID
				AND ic.itemID=i.itemID
				AND c.catID=ic.catID";
		$result = WCF::getDB()->sendQuery($sql);
		$row = WCF::getDB()->fetchArray($result);

		$row['tags'] = RGalleryItem::getElementTags($itemID);

		if (! isset($row['ownerID']) || !$row['ownerID']) return false;
		$owner = new UserProfile($row['ownerID']);
		$row['ownerName'] = $owner->username;
		$row['itemOwner'] = $owner;
		$row['itemOrigPath'] = RGALLERY_IMAGE_PATH . '/' . $row['ownerID'] . '/' . $row['itemPath'] . '.' . $row['itemOrigExtension'];
		$row['itemDimW_h'] = $row['itemDimW'] / 2;
		$row['itemDimW_h_l'] = $row['itemDimW_h'] - 1;
		$row['itemDimH_h'] = $row['itemDimH'] / 2;
		$row['itemDimH_h_l'] = $row['itemDimH_h'] - 1;
		if (file_exists($row['itemOrigPath']))
			$row['hasFullsize'] = 1;
		else
			$row['hasFullsize'] = 0;
		return $row;
	}

	/**
	 * imports files from the given directory
	 */
	public function import() {

		$errors = array(
				'messages' => array(),
				'errorDescriptions' => array()
		);

		// get the file to export
		$sql = "SELECT * FROM wcf" . WCF_N . "_rGallery_items LIMIT " . $this->sessionData['imageCount'] . ",1";
		error_log($sql);
		$query = WCF::getDB()->sendQuery($sql);
		$row = WCF::getDB()->fetchArray($query);

		error_log("Fetching data for ".$row['itemID']);

		$item = new RGalleryItem();
		$itemData = $this->getData($row['itemID']);
		error_log("OK");

		if (! $itemData) {
			error_log("could not read itemData for " . $row['itemID']);
			$done = 1;
			$imageCount = 1;
				$errors['messages'][] = 'Image data could not be read';
				$errors['errorDescriptions'][] = $row['itemID'];
		}
		else {
			if ($itemData['hasFullsize'])
				$itemPath = $itemData['itemOrigPath'];
			else
				$itemPath = RGALLERY_IMAGE_PATH . '/' . $itemData['ownerID'] . '/image_' . $row['itemPath'] . '.jpg';

			if(substr($itemPath, 0, 1) != '/') {
				$itemPath = WBB_DIR . '/'.$itemPath;
			}

			if (! @is_readable($itemPath)) {
				error_log("could not read $itemPath");
				$errors['messages'][] = 'Image could not be read';
				$errors['errorDescriptions'][] = $itemPath;
				$done = 1;
				$imageCount = 1;
			}
			else {
				// when the file exists
				$error = 0;
				//try {
				if ($itemData['ownerName']) {

					// the user still exists, so we import this image
					require_once (WCF_DIR . 'lib/data/user/gallery/UserGalleryPhotoEditor.class.php');


		error_log("creating UserGaleryPhotoEditor object");
					try {
						$photo = UserGalleryPhotoEditor::create($itemData['itemOwner']->userID, $itemData['itemOwner']->username, $itemPath, $row['itemOrigName'], $row['itemName'], $row['itemComment']);
		error_log("OK");

						if (isset($itemData['catName'])) $itemData['tags'][] = $itemData['catName'];

						// tags
						$tagsArray = ArrayUtil::trim($itemData['tags']);
						sort($tagsArray);

		error_log("Updating tags");
						if (count($tagsArray) > 0) {
							$photo->updateTags($tagsArray);
						}
		error_log("OK");

		error_log("Updating clicks");
						// update the klicks
						$clicksSql = "UPDATE wcf" . WCF_N . "_user_gallery SET views = '" . $row['itemClicks'] . "' WHERE photoID = '" . $photo->photoID . "'";
						WCF::getDB()->sendQuery($clicksSql);

		error_log("OK");
		error_log("updating upload time");
						// update upload time
						$uploadSql = "UPDATE wcf" . WCF_N . "_user_gallery SET uploadTime = '" . $row['itemAddedDate'] . "' WHERE photoID = '" . $photo->photoID . "'";
						WCF::getDB()->sendQuery($uploadSql);

		error_log("OK");
						// insert the comments
		error_log("inserting comments");
						$commentSql = "SELECT c.commentText as commentText,
									c.commentID as commentID,
									c.commentAddedDate as commentAddedDate,
									cu.userID as userID,
									cu.userName as userName
									FROM
									`wcf" . WCF_N . "_rGallery_comments` as c,
									`wcf" . WCF_N . "_rGallery_items_comment` as ic,
									`wcf" . WCF_N . "_rGallery_comments_user` as cu
									WHERE
									ic.commentID=c.commentID
									AND cu.commentID=c.commentID
									AND ic.itemID=" . intval($row['itemID']) . '

									ORDER BY c.commentAddedDate ASC';
						$commentResult = WCF::getDB()->sendQuery($commentSql);
						$commentArray = array();
						while ( $commentRow = WCF::getDB()->fetchArray($commentResult) ) {
							require_once (WCF_DIR . 'lib/data/user/UserProfile.class.php');
							$user = new UserProfile($commentRow['userID']);
							require_once (WCF_DIR . 'lib/data/user/gallery/comment/UserGalleryPhotoCommentEditor.class.php');

		error_log("creating userGalleryPhotoCommentEditor for ".$photo->photoID." from ".$user->userID);
							UserGalleryPhotoCommentEditor::create($photo->photoID, $photo->ownerID, $commentRow['commentText'], $user->userID, $user->username, $commentRow['commentAddedDate']);

		error_log("done");
						}


		error_log("insert comments all done");
						$done = 1;
						$imageCount = 1;
					}
					catch (Exception $e) {
						$done = 1;
						$imageCount = 1;
						error_log("Catched an Exception after creating the object. ".$e->getMessage());
					}
				}
				else {
					error_log("user not found for $itemPath");
					$done = 1;
					$imageCount = 1;
				}
				/*}
				catch (Exception $e) {
					$errors['messages'][] = 'Error importing image';
					$errors['errorDescriptions'][] = $itemPath;
					$done = 1;
					$imageCount = 1;
				}*/

			}
		}
		$returnarray = array(
				'errors' => $errors,
				'done' => $done,
				'imageCount' => $imageCount
		);
		return $returnarray;
	}

	/**
	 * @see Action::execute()
	 */
	public function execute() {

		parent::execute();
		// get session data
		$this->sessionData = WCF::getSession()->getVar('RGalleryImportWCFData');

		$stepInfo = array();
		// start export operations
		$loopStart = time();
		// import database operations (only up to $this->limit)
		$loopInfo = $this->import();
		// save errors
		$errors = array(
				'messages' => array_merge($this->sessionData['errors']['messages'], $loopInfo['errors']['messages']),
				'errorDescriptions' => array_merge($this->sessionData['errors']['errorDescriptions'], $loopInfo['errors']['errorDescriptions'])
		);
		$this->sessionData['errors'] = $errors;
		// refresh session data
		$this->sessionData['remain'] -= $loopInfo['done'];
		$this->sessionData['imageCount'] += $loopInfo['imageCount'];
		// calculate progressbar
		$this->calcProgress(($this->sessionData['count'] - $this->sessionData['remain']), $this->sessionData['count']);
		// show finish
		if ($this->sessionData['remain'] <= 0) {
			// cleanup session data
			WCF::getSession()->unregister('RGalleryImportWCFData');
			// clear wcf cache
			WCF::getCache()->clear(WCF_DIR . 'cache', '*.php', true);
			// set data for template
			WCF::getTPL()->assign(array(
					'import' => true,
					'success' => (empty($errors['messages']) && $this->sessionData['imageCount'] > 0),
					'imageCount' => $this->sessionData['imageCount'],
					'count' => $this->sessionData['count'],
					'errors' => $errors
			));
			WCF::getTPL()->append('message', WCF::getTPL()->fetch('RGalleryMessage'));
			// show finish template
			$title = 'wcf.acp.rgallery.import.progress.finish';
			$this->finish($title, 'index.php?form=RGalleryImport&packageID=' . PACKAGE_ID . SID_ARG_2ND_NOT_ENCODED);
		}
		WCF::getSession()->register('RGalleryImportWCFData', $this->sessionData);
		// next loop
		$title = 'wcf.acp.rgallery.import.progress.working';
		$this->nextLoop($title);
	}
}
?>