<?php
require_once (WCF_DIR . 'lib/acp/form/ACPForm.class.php');
require_once (WBB_DIR . 'lib/system/RGallerySystem.class.php');
require_once (WCF_DIR . 'lib/data/user/UserProfile.class.php');
require_once (WCF_DIR . 'lib/system/exception/NamedUserException.class.php');

ini_set('display_errors', 1);
/**
 * Shows the rGallery import form
 *
 * @author	Andreas Diendorfer
 * @package	de.0xdefec.rgalleryimport
 */
class RGalleryImportForm extends ACPForm {
	public $templateName = 'RGalleryImport';
	public $activeMenuItem = 'wcf.acp.rgalleryimport';
	// 	public $neededPermissions = 'mod.rgallery.canModerate';
	public $cats = array(
	);
	public $import = '';
	public $path = '';
	public $cat = '';
	public $userID = '';
	public $tags = '';
	public $user = '';
	public $files = array(
	);
	public $allowed_extensions = '';
	public $store_orig = 0;
	public $error = '';
	public $imported = 0;
	public $mysql = array(
	);
	public $wbbpath = '';
	public $jgspath = '';

	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {

		parent::readFormParameters();
		if (isset($_POST['import']))
			$this->import = escapeString($_POST['import']);
		if (isset($_POST['path']))
			$this->path = escapeString($_POST['path']);
		if (isset($_POST['userID']))
			$this->userID = escapeString($_POST['userID']);
		if (isset($_POST['tags']))
			$this->tags = escapeString($_POST['tags']);
		if (isset($_POST['cat']))
			$this->cat = escapeString($_POST['cat']);
		if (isset($_POST['store_orig']))
			$this->store_orig = escapeString($_POST['store_orig']);
		if (isset($_POST['mysql_server']))
			$this->mysql['server'] = escapeString($_POST['mysql_server']);
		if (isset($_POST['mysql_username']))
			$this->mysql['username'] = escapeString($_POST['mysql_username']);
		if (isset($_POST['mysql_password']))
			$this->mysql['password'] = escapeString($_POST['mysql_password']);
		if (isset($_POST['mysql_db']))
			$this->mysql['db'] = escapeString($_POST['mysql_db']);
		if (isset($_POST['mysql_pre']))
			$this->mysql['pre'] = escapeString($_POST['mysql_pre']);
		if (isset($_POST['wbbpath']))
			$this->wbbpath = escapeString($_POST['wbbpath']);
	}

	/**
	 * @see Form::validate()
	 */
	public function validate() {

		parent::validate();
		if ($this->import == 'local') {
			// so we want to import from a local directory
			if (!is_readable($this->path)) {
				throw new UserInputException('path', 'invalid');
			}
			$this->user = new UserProfile($this->userID, null, null, null);
			if ($this->user->username == "") {
				throw new UserInputException('userID', 'invalid');
			}
		} elseif ($this->import == 'jgs') {
			if ($this->mysql['server'] == '')
				throw new UserInputException('mysql_server');
			if ($this->mysql['username'] == '')
				throw new UserInputException('mysql_username');
			if ($this->mysql['db'] == '')
				throw new UserInputException('mysql_db');
			if ($this->mysql['pre'] == '')
				throw new UserInputException('mysql_pre');
			if ($this->wbbpath == '')
				throw new UserInputException('wbbpath');

			$idb = mysql_connect($this->mysql['server'], $this->mysql['username'], $this->mysql['password'], true);
			if (!$idb || !mysql_select_db($this->mysql['db'], $idb)) {
				throw new NamedUserException('Could not connect with the database. Please check the server settings!');
			}
			// store our pre in a short var
			$pre = $this->mysql['pre'];
			// lets check if we find a jgs-xa installation
			$sql = "SELECT count(*) FROM " . $pre . "jgs_galerie_bilder";
			$query = mysql_query($sql, $idb);
			list ($this->images) = mysql_fetch_array($query);
			if ($this->images <= 0) {
				throw new NamedUserException('Images could not be loaded! Have you chosen the right database?');
			}
			// check if the given path is correct
			if (!@is_readable($this->wbbpath)) {
				throw new NamedUserException('The given WBB Path could not be read!');
			}
			// set the path where jgs stores its images
			$this->jgspath = $this->wbbpath . '/galerie/bilder/';
			if (!@is_readable($this->jgspath)) {
				throw new NamedUserException('"galerie/bilder" directory not found or not readable in the given WBB Path!');
			}
			mysql_close($idb);
		} else if ($this->import == 'wcf') {
			if (!defined('MODULE_USER_GALLERY')) throw new NamedUserException('Woltlab\'s user gallery plugin not found!');
		}
	}

	/**
	 * @see Form::save()
	 */
	public function save() {

		parent::save();
		if ($this->import == 'local') {
			// read files in the given directory
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
			// build session data array
			$sessionData = array(
			);
			$sessionData['errors'] = array(
				'messages' => array(
				),
				'errorDescriptions' => array(
				)
			);
			$sessionData['imageCount'] = 0;
			$sessionData['remain'] = $sessionData['count'] = count($this->files);
			$sessionData['path'] = $this->path;
			$sessionData['userID'] = $this->userID;
			$sessionData['tags'] = $this->tags;
			$sessionData['cat'] = $this->cat;
			$sessionData['store_orig'] = $this->store_orig;
			$sessionData['errors'] = array(
				'messages' => array(
				),
				'errorDescriptions' => array(
				)
			);
			WCF::getSession()->register('RGalleryImportData', $sessionData);
			$this->saved();
			WCF::getTPL()->assign(array(
				'pageTitle' => WCF::getLanguage()->get('wcf.acp.rgallery.import.pageHeadline'),
				'url' => 'index.php?action=RGalleryImport&packageID=' . PACKAGE_ID . SID_ARG_2ND_NOT_ENCODED,
				'progress' => 0
			));
			WCF::getTPL()->display('worker');
			exit();
		} elseif ($this->import == 'jgs') {
			/***
			 * JGS-XA importer
			 * imports images from the jgs-xa gallery V. 4.0.0
			 * we only can import stuff, that is supported by rgalleryimport
			 * things that will be imported:
			 * -) images (image, name, owner, category, views, uploaddate)
			 * -) categories (without: comments, structure (no subcats, etc. - all in one level), permissions, sort order); will be writeable by default
			 * -) locked images won't be imported
			 * -) comments
			 ***/
			// import categories
			$pre = $this->mysql['pre'];

			$idb = mysql_connect($this->mysql['server'], $this->mysql['username'], $this->mysql['password'], true);
			mysql_select_db($this->mysql['db'], $idb);

			$sql = "SELECT * FROM " . $pre . "jgs_galerie_kategorie";
			$query = mysql_query($sql, $idb);
			$cats_map = array(
			);

			while ($row = mysql_fetch_array($query)) {
				$rGalleryCats = RGallerySystem::getCategories();
				$cats = array(
				);
				foreach ($rGalleryCats as $cat) {
					$cats[$cat['catID']] = $cat['catName'];
				}
				$rGalleryCats = $cats;

				$sql = "SELECT count(*) as imagecount FROM " . $pre . "jgs_galerie_bilder WHERE kategorie=" . $row['id'];
				$catsquery = mysql_query($sql, $idb);
				$count = mysql_fetch_array($catsquery);
				$count = $count['imagecount'];

				if (trim($row['name']) != '' && $count != 0) {
					if (!in_array($row['name'], $rGalleryCats)) {
						$sql = "INSERT INTO
							wcf" . WCF_N . "_rGallery_cats
							(catName, catComment, catWriteable)
							VALUES
							('" . escapeString($row['name']) . "', '" . escapeString($row['beschreibung']) . "', 1)";
						$result = WCF::getDB()->sendQuery($sql);
						$id = WCF::getDB()->getInsertID($result);
					} else {
						$rGalleryCats = array_flip($rGalleryCats);
						$id = $rGalleryCats[$row['name']];
					}
					$cats_map[$row['id']] = $id;
				}
			}

			// build session data array
			$sessionData = array(
			);
			$sessionData['errors'] = array(
				'messages' => array(
				),
				'errorDescriptions' => array(
				)
			);

			$sessionData['imageCount'] = 0;
			$sessionData['remain'] = $sessionData['count'] = $this->images;
			$sessionData['mysql'] = $this->mysql;
			$sessionData['wbbpath'] = $this->wbbpath;
			$sessionData['store_orig'] = $this->store_orig;
			$sessionData['cats_map'] = $cats_map;
			$sessionData['errors'] = array(
				'messages' => array(
				),
				'errorDescriptions' => array(
				)
			);
			WCF::getSession()->register('RGalleryImportJGSData', $sessionData);
			$this->saved();
			WCF::getTPL()->assign(array(
				'pageTitle' => WCF::getLanguage()->get('wcf.acp.rgallery.import.pageHeadline'),
				'url' => 'index.php?action=RGalleryImportJGS&packageID=' . PACKAGE_ID . SID_ARG_2ND_NOT_ENCODED,
				'progress' => 0
			));
			WCF::getTPL()->display('worker');
			exit();
		} else if ($this->import == 'wcf') {
			/***
			 * WCF user.gallery EXPORTER
			 * exports rGallery Images to woltlab's user gallery
			 ***/

			$sql = "SELECT count(*) as images FROM wcf".WCF_N."_rGallery_items";
			$row = WCF::getDB()->getFirstRow($sql);
			$this->images = $row['images'];

			// build session data array
			$sessionData = array(
			);
			$sessionData['errors'] = array(
				'messages' => array(
				),
				'errorDescriptions' => array(
				)
			);

			$sessionData['imageCount'] = 0;
			$sessionData['remain'] = $sessionData['count'] = $this->images;

			WCF::getSession()->register('RGalleryImportWCFData', $sessionData);
			$this->saved();
			WCF::getTPL()->assign(array(
				'pageTitle' => WCF::getLanguage()->get('wcf.acp.rgallery.import.pageHeadline'),
				'url' => 'index.php?action=RGalleryImportWCF&packageID=' . PACKAGE_ID . SID_ARG_2ND_NOT_ENCODED,
				'progress' => 0
			));
			WCF::getTPL()->display('worker');
			exit();
		}

	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {

		parent::assignVariables();
		$cats = RGallerySystem::getCategories();
		foreach ($cats as $cat) {
			$this->cats[$cat['catID']] = $cat['catName'];
		}
		WCF::getTPL()->assign(array(
			'cats' => $this->cats,
			'imported' => $this->imported,
			'error' => $this->error
		));
	}
}
?>