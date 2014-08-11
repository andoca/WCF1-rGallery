<?php
require_once (WCF_DIR . 'lib/acp/action/WorkerAction.class.php');
require_once (WBB_DIR . 'lib/system/RGallerySystem.class.php');
require_once (WCF_DIR . 'lib/system/language/LanguageEditor.class.php');
require_once (WCF_DIR . 'lib/data/user/UserProfile.class.php');
require_once (WCF_DIR . 'lib/system/exception/NamedUserException.class.php');

ini_set('display_errors', 1);

/**
 * Imports images to the rGallery
 *
 * @author	Andreas Diendorfer
 * @package	de.0xdefec.rgalleryimport
 */
class RGalleryImportJGSAction extends WorkerAction {
	public $action = 'RGalleryImportJGS';

	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {

		parent::readParameters();
	}

	/**
	 * imports files from the given directory
	 */
	public function import() {
		try {
			$errors = array(
					'messages' => array(),
					'errorDescriptions' => array()
			);
			// get the file to import
			$pre = $this->mysql['pre'];
			$idb = mysql_connect($this->mysql['server'], $this->mysql['username'], $this->mysql['password'], true);
			mysql_select_db($this->mysql['db'], $idb);

			$sql = "SELECT * FROM " . $pre . "jgs_galerie_bilder WHERE gesperrt!=1 LIMIT " . $this->sessionData['imageCount'] . ",1";
			$query = mysql_query($sql, $idb);
			$row = mysql_fetch_array($query);
			
			if(1 == 2) {
				$sql2 = "SELECT username FROM bb1_users WHERE userid = ".$row['user_id'];
				$query2 = mysql_query($sql2, $idb);
				$row2 = mysql_fetch_array($query2);
				
				if(isset($row2['username']) && !empty($row2['username'])) {
					$userID = new UserProfile(null, null, $row2['username']);
					$row['user_id'] = $userID->userID;
				}
			}

			$itemPath = $this->jgspath . 'bild-' . $row['bild_id'] . '.' . $row['typ'];
			if (! @is_readable($itemPath)) {
				$errors['messages'][] = 'Image could not be read';
				$errors['errorDescriptions'][] = $row['bild_name'];
				$done = 1;
				$imageCount = 1;
			}
			else {
				// when the file exists
				$error = 0;
				$itemName = 'bild-' . $row['bild_id'] . '.' . $row['typ'];
				$itemSize = filesize($itemPath);
				$itemTitle = escapeString($row['bild_name']);
				if (isset($this->cats_map[escapeString($row['kategorie'])]))
					$itemCat = $this->cats_map[escapeString($row['kategorie'])];
				else
					$itemCat = 1;
				list($width, $height, $imagetype) = @getimagesize($itemPath);
				if ($imagetype == 1)
					$imagetype = "image/gif";
				else if ($imagetype == 2)
					$imagetype = "image/jpeg";
				else if ($imagetype == 3)
					$imagetype = "image/png";
				else {
					$errors['messages'][] = 'Imagetype could not be read';
					$errors['errorDescriptions'][] = $itemTitle;
					$done = 1;
					$imageCount = 1; // we could not determine the image type!
				}
				if ($error == 0) {
					$newItem = new RGalleryItem();
					$_FILES = array();
					$_POST = array();
					$rights = array();
					$_FILES['file'] = array();
					$_FILES['file']['name'] = $itemName;
					$_FILES['file']['size'] = $itemSize;
					$_FILES['file']['type'] = $imagetype;
					$_FILES['file']['tmp_name'] = $itemPath;
					$_POST['itemName'] = utf8_encode($itemTitle);
					$_POST['itemCat'] = utf8_encode($itemCat);
					$_POST['itemComment'] = utf8_encode($row['beschreibung']);
					/*if ($this->tagsFromDesc == 1)
						$_POST['itemTags'] = utf8_encode($row['beschreibung']);
					else*/
					$_POST['itemTags'] = '';
					$GLOBALS['rights']['store_orig'] = $this->store_orig;

					$user = new UserProfile($row['user_id']);
					// 	$user = new User($row['userID']);
					if (! $user->username) {
						$errors['messages'][] = 'UserID not found';
						$errors['errorDescriptions'][] = $row['user_id'];
						$done = 1;
						$imageCount = 1;
					}
					else {
						$upload = $newItem->upload($row['user_id'], 1);
						if ($upload == 1) {
							// update clicks and upload date of this image
							WCF::getDB()->sendQuery("UPDATE wcf" . WCF_N . "_rGallery_items SET itemAddedDate = " . $row['datum'] . ", itemClicks = " . $row['views'] . " WHERE itemID=" . $newItem->itemID);
							// now lets get the comments for this image
							$comment_q = mysql_query("SELECT * FROM " . $pre . "jgs_galerie_kommentar WHERE imgid='" . $row['bild_id'] . "'", $idb);
							while ( $comment = mysql_fetch_array($comment_q) ) {
								// get the user name of the commenter
								$comment_author = new UserProfile($comment['userid']);
								$comment_author_name = $comment_author->username;
								$sql = "INSERT INTO wcf" . WCF_N . "_rGallery_comments (commentText, commentAddedDate) VALUES ('" . RGallerySystem::prepInput(html_entity_decode(utf8_encode($comment['comment']), ENT_COMPAT, 'UTF-8')) . "', '" . $comment['kommentarzeit'] . "')";
								$result1 = WCF::getDB()->sendQuery($sql);
								$commentID = WCF::getDB()->getInsertID($result1);
								$sql = "INSERT INTO wcf" . WCF_N . "_rGallery_items_comment (itemID, commentID) VALUES (" . $newItem->itemID . ", " . $commentID . ")";
								$result2 = WCF::getDB()->sendQuery($sql);
								$sql = "INSERT INTO wcf" . WCF_N . "_rGallery_comments_user (commentID, userID, userName) VALUES (" . $commentID . ", " . $comment_author->userID . ", '" . RGallerySystem::prepInput($comment_author->username) . "')";
								$result3 = WCF::getDB()->sendQuery($sql);
							}
							$done = 1;
							$imageCount = 1;
						}
						else {
							$errors['messages'][] = 'Image could not be read';
							$errors['errorDescriptions'][] = $itemTitle;
							$done = 1;
							$imageCount = 1;
						}
					}
				}
				else {
					$errors['messages'][] = 'Image could not be read';
					$errors['errorDescriptions'][] = $itemTitle;
					$done = 1;
					$imageCount = 1;
				}
			}
		}
		catch ( Exception $e ) {
			//$errors['messages'][] = 'Image could not be imported - unknown error';
			//$errors['errorDescriptions'][] = '';
			$done = 1;
			$imageCount = 1;
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
		$this->sessionData = WCF::getSession()->getVar('RGalleryImportJGSData');

		$this->mysql = $this->sessionData['mysql'];
		$this->wbbpath = $this->sessionData['wbbpath'];
		$this->cats_map = $this->sessionData['cats_map'];
		$this->store_orig = $this->sessionData['store_orig'];
		// $this->tagsFromDesc = $this->sessionData['tagsFromDesc'];
		$this->jgspath = $this->wbbpath . '/galerie/bilder/';
		$stepInfo = array();
		// start export operations
		$loopStart = time();
		// import database operations (only up to $this->limit)
		$loopInfo = $this->import();
		// save errors
		/*$errors = array(
				'messages' => array_merge($this->sessionData['errors']['messages'], $loopInfo['errors']['messages']),
				'errorDescriptions' => array_merge($this->sessionData['errors']['errorDescriptions'], $loopInfo['errors']['errorDescriptions'])
		);
		$this->sessionData['errors'] = $errors;
		*/
		// refresh session data
		$this->sessionData['remain'] -= $loopInfo['done'];
		$this->sessionData['imageCount'] += $loopInfo['imageCount'];
		// calculate progressbar
		$this->calcProgress(($this->sessionData['count'] - $this->sessionData['remain']), $this->sessionData['count']);
		// show finish
		if ($this->sessionData['remain'] <= 0) {
			// cleanup session data
			WCF::getSession()->unregister('RGalleryImportData');
			// clear wcf cache
			WCF::getCache()->clear(WCF_DIR . 'cache', '*.php', true);
			// set data for template
			WCF::getTPL()->assign(array(
					'import' => true,
					'success' => (empty($errors['messages']) && $this->sessionData['imageCount'] > 0),
					'imageCount' => $this->sessionData['imageCount'],
					'count' => $this->sessionData['count'],
					'errors' => array('messages' => array(), 'errorDescriptions' => array())
			));
			WCF::getTPL()->append('message', WCF::getTPL()->fetch('RGalleryMessage'));
			// show finish template
			$title = 'wcf.acp.rgallery.import.progress.finish';
			$this->finish($title, 'index.php?form=RGalleryImport&packageID=' . PACKAGE_ID . SID_ARG_2ND_NOT_ENCODED);
		}
		WCF::getSession()->register('RGalleryImportJGSData', $this->sessionData);
		// next loop
		$title = 'wcf.acp.rgallery.import.progress.working';
		$this->nextLoop($title);
	}
}
?>