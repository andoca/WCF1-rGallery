<?php
require_once (WCF_DIR . 'lib/acp/action/WorkerAction.class.php');
require_once (WBB_DIR . 'lib/system/RGallerySystem.class.php');
require_once (WCF_DIR . 'lib/system/language/LanguageEditor.class.php');
require_once (WCF_DIR . 'lib/data/user/UserProfile.class.php');

/**
 * Imports images to the rGallery
 *
 * @author	Andreas Diendorfer
 * @package	de.0xdefec.rgalleryimport
 */
class RGalleryImportAction extends WorkerAction {
	public $action = 'RGalleryImport';

	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
	}

	/**
	 * reads the import directory
	 */
	public function readImportDir() {
		$this->allowed_extensions = RGallerySystem::getAllowedFileExtensions();
		$dir = opendir($this->path);
		while ($file = readdir($dir)) {
			list ($width, $height, $type, $attr) = @getimagesize($this->path . '/' . $file);
			preg_match("/\.([a-zA-Z0-9]{1,4})$/i", $file, $endung);
			if (!empty($endung[1]))
				$extension = $endung[1]; else
				$extension = '';
			if (is_readable($this->path . '/' . $file) && in_array(strtolower($extension), $this->allowed_extensions) && $type != 0) {
				$this->files[] = $file;
			}
		}
		sort($this->files);
	}

	/**
	 * imports files from the given directory
	 */
	public function import() {
		$errors = array(
			'messages' => array(
			),
			'errorDescriptions' => array(
			)
		);
		// get the files to import
		$this->readImportDir();
		$file = $this->files[$this->sessionData['imageCount']]; // current position
		$itemPath = $this->path . '/' . $file;
		$itemName = $file;
		$itemSize = filesize($itemPath);
		$itemTitle = '';
		$itemCat = $this->cat;
		list ($width, $height, $imagetype) = getimagesize($itemPath);
		$newItem = new RGalleryItem();
		$_FILES = array(
		);
		$_POST = array(
		);
		$rights = array(
		);
		$_FILES['file'] = array(
		);
		$_FILES['file']['name'] = $itemName;
		$_FILES['file']['size'] = $itemSize;
		$_FILES['file']['type'] = $imagetype;
		$_FILES['file']['tmp_name'] = $itemPath;
		$_POST['itemName'] = $itemName;
		$_POST['itemCat'] = $itemCat;
		$_POST['itemComment'] = '';
		$_POST['itemTags'] = $this->tags;
		$GLOBALS['rights']['store_orig'] = $this->store_orig;
		$upload = $newItem->upload($this->user->userID, 1);
		if ($upload == 1) {
			$done = 1;
			$imageCount = 1;
		} else {
			$errors['messages'][] = 'Image could not be read';
			$errors['errorDescriptions'][] = $itemPath;
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
		$this->sessionData = WCF::getSession()->getVar('RGalleryImportData');
		$this->path = $this->sessionData['path'];
		$this->userID = $this->sessionData['userID'];
		$this->tags = $this->sessionData['tags'];
		$this->user = new UserProfile($this->userID, null, null, null);
		$this->cat = $this->sessionData['cat'];
		$this->store_orig = $this->sessionData['store_orig'];
		$stepInfo = array(
		);
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
			WCF::getSession()->unregister('RGalleryImportData');
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
		WCF::getSession()->register('RGalleryImportData', $this->sessionData);
		// next loop
		$title = 'wcf.acp.rgallery.import.progress.working';
		$this->nextLoop($title);
	}
}
?>