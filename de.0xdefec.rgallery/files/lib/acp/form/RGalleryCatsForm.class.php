<?php
require_once (WCF_DIR . 'lib/acp/form/ACPForm.class.php');
require_once (WBB_DIR . 'lib/system/RGallerySystem.class.php');

/**
 * Shows the rGallery category form
 * 
 * @author	Andreas Diendorfer
 * @package	de.0xdefec.rgallery
 */
class RGalleryCatsForm extends ACPForm {

	public $templateName = 'RGalleryCats';

	public $activeMenuItem = 'wcf.acp.rgallery_cats';

	// 	public $neededPermissions = 'mod.rgallery.canModerate';
	public $cats = '';

	public $groups = '';

	public $catID = '';

	public $catName = '';

	public $catComment = '';

	public $catAuthorized_group = '';

	public $catWriteable = '';

	public $catDelete = '';

	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		if (isset($_POST['catID']))
			$this->catID = escapeString($_POST['catID']);
		if (isset($_POST['catName']))
			$this->catName = escapeString($_POST['catName']);
		if (isset($_POST['catComment']))
			$this->catComment = escapeString($_POST['catComment']);
		if (isset($_POST['catAuthorized_group']))
			$this->catAuthorized_group = escapeString($_POST['catAuthorized_group']);
		if (isset($_POST['catWriteable']))
			$this->catWriteable = escapeString($_POST['catWriteable']);
		if (isset($_POST['catDelete']))
			$this->catDelete = escapeString($_POST['catDelete']);
	}

	/**
	 * @see Form::validate()
	 */
	public function validate() {
		parent::validate();
		if (!$this->catDelete) { // we have to check the data if we don't want to delete the item
			$this->groups = Group::getAllGroups();
			if (!isset($this->groups[$this->catAuthorized_group]) && $this->catAuthorized_group != '') {
				throw new UserInputException('catAuthorized_group');
			}
			if (empty($this->catName)) { // there must be a catname!
				throw new UserInputException('catName');
			}
		}
	}

	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		if ($this->catID) {
			if ($this->catDelete) {
				$sql = "DELETE FROM wcf" . WCF_N . "_rGallery_cats WHERE catID=" . $this->catID;
				// move images from this cat to the default cat
				$sql2 = "UPDATE wcf" . WCF_N . "_rGallery_items_cat SET catID=1 WHERE catID=" . $this->catID;
				WCF::getDB()->sendQuery($sql2);
			} else {
				// update the cat data
				$sql = "UPDATE wcf" . WCF_N . "_rGallery_cats SET catName='" . $this->catName . "',catComment='" . $this->catComment . "',catAuthorized_group='" . $this->catAuthorized_group . "',catWriteable='" . $this->catWriteable . "' WHERE catID=" . $this->catID;
			}
			WCF::getDB()->sendQuery($sql);
		} else {
			// insert a new cat
			$sql = "INSERT INTO wcf" . WCF_N . "_rGallery_cats (catName, catComment, catAuthorized_group, catWriteable) VALUES ('" . $this->catName . "', '" . $this->catComment . "', '" . $this->catAuthorized_group . "', '" . $this->catWriteable . "')";
			WCF::getDB()->sendQuery($sql);
		}
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		$this->cats = RGallerySystem::getCategories();
		$this->groups = Group::getAllGroups();
		$groupIDs = array_keys($this->groups);
		WCF::getTPL()->assign(array(
			'cats' => $this->cats, 
			'groups' => $this->groups, 
			'groupIDs' => $groupIDs));
	}
}
?>