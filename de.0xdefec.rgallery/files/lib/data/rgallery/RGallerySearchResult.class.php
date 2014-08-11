<?php
require_once (WCF_DIR . 'lib/data/message/util/SearchResultTextParser.class.php');

/**
 * This class extends the viewable post by function for a search result output.
 *
 * @package	de.0xdefec.rgallery
 * @author	Andreas Diendorfer
 */
class RGallerySearchResult {

	public $data = '';

	public $itemID = '';

	public $itemComment = '';

	public $itemName = '';

	public $itemAddedDate = '';

	public $itemOwner = '';

	public function __construct($row) {
		$this->data = $row;
		$this->itemID = $this->data['itemID'];
		$this->itemComment = $this->data['itemComment'];
		$this->itemName = $this->data['itemName'];
		$this->itemAddedDate = $this->data['itemAddedDate'];
		$this->ownerID = $this->data['ownerID'];
		$this->itemOwner = new UserProfile($this->ownerID);
	}

	/**
	 * @see ViewablePM::handleData();
	 */
	protected function handleData($data) {
		$data['messagePreview'] = true;
		parent::handleData($data);
	}

	/**
	 * @see ViewablePM::getFormattedMessage()
	 */
	public function getFormattedMessage() {
		return SearchResultTextParser::parse($this->data['itemComment']);
	}
}
?>