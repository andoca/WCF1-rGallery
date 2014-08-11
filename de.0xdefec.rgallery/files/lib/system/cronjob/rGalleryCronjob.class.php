<?php
require_once (WCF_DIR . 'lib/data/cronjobs/Cronjob.class.php');
require_once (WBB_DIR . 'lib/system/RGallerySystem.class.php');

/**
 * Checks hourly if a user was deleted and deletes his/her images as well.
 * also deletes unused tags from the db
 * do not use this cronjob too often.. could eat up your cpu in a large gallery
 *
 * @author	Andreas Diendorfer
 * @package	de.0xdefex.rgallery
 */
class rGalleryCronjob implements Cronjob {

	/**
	 * @see Cronjob::execute()
	 */
	public function execute($data) {
		// check for images from nonexisting users
		$sql = "SELECT distinct(io.ownerID) as ownerID FROM wcf" . WCF_N . "_rGallery_items_owner as io";
		$query = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($query)) {
			$user = new User($row['ownerID']);
			if (!$user->username) {
				// the user does not exist anymore! delete his items
				RGallerySystem::deleteAllUserItems($row['ownerID']);
			}
		}
		// check for unused tags in the DB
		$sql = "SELECT t.tagID as tagID FROM wcf" . WCF_N . "_rGallery_tags as t";
		$query = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($query)) {
			// now we check every tag if a image uses it
			$sql = "SELECT * FROM wcf" . WCF_N . "_rGallery_items_tag WHERE tagID = '" . $row['tagID']."'";
			$count = WCF::getDB()->countRows(WCF::getDB()->sendQuery($sql));
			if ($count == 0) {
				// delete this tag, because no image uses it anymore!
				WCF::getDB()->sendQuery("DELETE FROM wcf" . WCF_N . "_rGallery_tags WHERE tagID = '" . $row['tagID']."'");
			}
		}
	} // end execute
} // end class
?>