<?php
require_once (WCF_DIR . 'lib/data/message/search/AbstractSearchableMessageType.class.php');
require_once (WBB_DIR . 'lib/data/rgallery/RGallerySearchResult.class.php');

/**
 * An implementation of SearchableMessageType for searching private messages.
 * 
 * @package	de.0xdefec.rgallery
 * @author	Andreas Diendorfer
 */
class RGallerySearch extends AbstractSearchableMessageType {

	protected $messageCache = array();

	/**
	 * Caches the data of the messages with the given ids.
	 */
	public function cacheMessageData($messageIDs, $additionalData) {
		$sql = "SELECT i.*, io.ownerID as ownerID FROM wcf" . WCF_N . "_rGallery_items as i, wcf" . WCF_N . "_rGallery_items_owner as io";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->messageCache[$row['itemID']] = array(
				'type' => 'rgallery', 
				'message' => new RGallerySearchResult($row));
		}
	}

	/**
	 * @see SearchableMessageType::getMessageData()
	 */
	public function getMessageData($messageID, $additionalData) {
		if (isset($this->messageCache[$messageID]))
			return $this->messageCache[$messageID];
		return null;
	}

	/**
	 * Returns the database table name for this search type.
	 */
	public function getTableName() {
		return 'wcf' . WCF_N . '_rGallery_items';
	}

	/**
	 * Returns the message id field name for this search type.
	 */
	public function getIDFieldName() {
		return 'itemID';
	}

	/**
	 * @see SearchableMessageType::isAccessible()
	 */
	public function isAccessible() {
		return (boolean) WCF::getUser()->userID;
	}

	/**
	 * @see SearchableMessageType::getResultTemplateName()
	 */
	public function getResultTemplateName() {
		return 'searchResultRGallery';
	}

	/**
	 * @see SearchableMessageType::getSubjectFieldNames()
	 */
	public function getSubjectFieldNames() {
		return array(
			'itemName');
	}

	/**
	 * @see SearchableMessageType::getMessageFieldNames()
	 */
	public function getMessageFieldNames() {
		return array(
			'itemComment');
	}
}
?>