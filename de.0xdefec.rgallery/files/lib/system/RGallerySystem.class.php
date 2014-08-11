<?php
/**
 * @author	Andreas Diendorfer
 * @package	de.0xdefec.rgallery
 */
if (version_compare(PACKAGE_VERSION, "3", "<")) {
	// this is a wbblite2 installation
	require_once (WCF_DIR . 'lib/data/message/bbcode/MessageParser.class.php');
} else
	require_once (WCF_DIR . 'lib/data/message/pm/PMEditor.class.php');

require_once (WCF_DIR . 'lib/data/user/UserProfile.class.php');

class RGallerySystem {

	function RGallerySystem() {
		
		// first check if all needed configuration variables are defined
		if (!defined('RGALLERY_IMAGE_PATH') || !defined('RGALLERY_HTTP_PATH') || !defined('RGALLERY_USE_CONVERT') || !defined('RGALLERY_SHOW_TOOLTIPS') || !defined('RGALLERY_THUMB_SIZE') || !defined('RGALLERY_THUMB_COMPRESSION') || !defined('RGALLERY_TTHUMB_SIZE') || !defined('RGALLERY_TTHUMB_COMPRESSION') || !defined('RGALLERY_PREVIEW_SIZE_W') || !defined('RGALLERY_PREVIEW_SIZE_H') || !defined('RGALLERY_PREVIEW_COMPRESSION') || !defined('RGALLERY_IMAGE_SIZE_W') || !defined('RGALLERY_IMAGE_SIZE_H') || !defined('RGALLERY_MIN_SIZE_W') || !defined('RGALLERY_MIN_SIZE_H') || !defined('RGALLERY_IMAGE_COMPRESSION') || !defined('RGALLERY_ALLOWED_EXTENSIONS') || !defined('RGALLERY_IMAGES_PER_USER_PAGE') || !defined('RGALLERY_COLOR_TAGS') || !defined('RGALLERY_WATERMARK') || !defined('RGALLERY_IMAGES_PER_PAGE'))
			$this->initError('Could not find all configuration constants! Please check your installation!');
		if (RGALLERY_IMAGE_PATH == '')
			$this->initError('No Image Path defined! Please set it in the ACP.');
		if (RGALLERY_HTTP_PATH == '')
			$this->initError('No HTTP-Path defined! Please set it in the ACP.');
		if (RGALLERY_THUMB_SIZE == '')
			$this->initError('No Thumb-Size defined! Please set it in the ACP.');
		if (RGALLERY_THUMB_COMPRESSION == '')
			$this->initError('No Thumbs-Compression defined! Please set it in the ACP.');
		if (RGALLERY_TTHUMB_SIZE == '')
			$this->initError('No Tiny-Thumb-Size defined! Please set it in the ACP.');
		if (RGALLERY_TTHUMB_COMPRESSION == '')
			$this->initError('No Tiny-Thumb-Compression defined! Please set it in the ACP.');
		if (RGALLERY_PREVIEW_SIZE_W == '')
			$this->initError('No Preview-Size (width) defined! Please set it in the ACP.');
		if (RGALLERY_PREVIEW_SIZE_H == '')
			$this->initError('No Preview-Size (height) defined! Please set it in the ACP.');
		if (RGALLERY_PREVIEW_COMPRESSION == '')
			$this->initError('No Preview-Compression defined! Please set it in the ACP.');
		if (RGALLERY_IMAGE_SIZE_W == '')
			$this->initError('No Image-Size (width) defined! Please set it in the ACP.');
		if (RGALLERY_IMAGE_SIZE_H == '')
			$this->initError('No Image-Size (height) defined! Please set it in the ACP.');
		if (RGALLERY_MIN_SIZE_W == '')
			$this->initError('No minium Image-Size (width) defined! Please set it in the ACP.');
		if (RGALLERY_MIN_SIZE_H == '')
			$this->initError('No minimum Image-Size (height) defined! Please set it in the ACP.');
		if (RGALLERY_IMAGE_COMPRESSION == '')
			$this->initError('No Image-Compression defined! Please set it in the ACP.');
		if (RGALLERY_ALLOWED_EXTENSIONS == '')
			$this->initError('Allowed-Extensions not defined! Please set it in the ACP.');
		if (RGALLERY_IMAGES_PER_USER_PAGE == '')
			$this->initError('Images-Per-User-Page defined! Please set it in the ACP.');
		if (RGALLERY_IMAGES_PER_PAGE == '')
			$this->initError('Image-Per-Page not defined! Please set it in the ACP.');
		/***
		 * Check for PHP Safe Mode. Uncomment the following line by adding two slashes to remove this check.
		 ***/
		if (ini_get('safe_mode') && RGALLERY_SAFE_MODE_CHECK == 1)
			$this->initError('PHP safe mode is activated. rGallery can not handle uploads with this configuration. Set safe mode to off or uncomment this error message in the RGallerySystem.class.php file (between line 50 to 60), if you know what you\'re doing!<br /> Otherwise <strong>contact your webspace provider</strong> and ask him to deactivate safe mode for your WCF installation.');
		if (!is_readable(RGALLERY_IMAGE_PATH)) {
			// maybe the directory just does not exist?
			if (!mkdir(RGALLERY_IMAGE_PATH, 0777))
				$this->initError('Image Path "' . RGALLERY_IMAGE_PATH . '" is not readable and could not be created! Please change your filepermissions or select another directory! Did you forget to use an absolut path?<br /> Try using "' . $_SERVER ["DOCUMENT_ROOT"] . '/' . RGALLERY_IMAGE_PATH . '"');
			else
				chmod(RGALLERY_IMAGE_PATH, 0777);
		}
		if (!is_writable(RGALLERY_IMAGE_PATH))
			$this->initError('Image Path "' . RGALLERY_IMAGE_PATH . '" is not writeable! Please change your filepermissions or select another directory!');
		if (RGALLERY_WATERMARK == 1 && !is_readable(RGALLERY_IMAGE_PATH . '/watermark.png')) {
			$this->initError('Could not open watermark image! Did you put it on the right place? Put it at ' . RGALLERY_IMAGE_PATH . '/watermark.png. Only user png files!!!');
		}
	}

	private function initError($message = false) {
		if (!$message)
			$message = "Sorry - No further traceback to this error.";
		require_once (WCF_DIR . 'lib/system/exception/NamedUserException.class.php');
		throw new NamedUserException('rGallery could not be initialized. <br /><strong>Information:</strong> ' . $message);
	}

	public static function prepInput($input) {
		return escapeString($input);
	}

	public function logger($logvalue) {
		return true;
		$logfile = WCF_DIR . "rgallery.log";
		$fh = fopen($logfile, 'a');
		$timestring = date("c");
		$ip = $_SERVER ['REMOTE_ADDR'];
		$text = $ip . ' at ' . $timestring . ' - ' . $logvalue . "\n";
		fwrite($fh, $text);
		fclose($fh);
	}

	public static function getUserPermissions($userID = false) {
		if ($userID == false)
			$userID = WBBCore::getUser()->userID;
		if (!$userID)
			return false;
		$rights_array = array ();
		$rights_array ['canView'] = WBBCore::getUser($userID)->getPermission('user.rgallery.canView');
		$rights_array ['canUpload'] = WBBCore::getUser($userID)->getPermission('user.rgallery.canUpload');
		$rights_array ['store_orig'] = WBBCore::getUser($userID)->getPermission('user.rgallery.keepOriginal');
		$rights_array ['quota'] = WBBCore::getUser($userID)->getPermission('user.rgallery.quota');
		$rights_array ['quota_disabled'] = WBBCore::getUser($userID)->getPermission('user.rgallery.quota_disabled');
		$rights_array ['uploads_per_week'] = WBBCore::getUser($userID)->getPermission('user.rgallery.uploadsPerWeek');
		$rights_array ['uploads_per_week_disabled'] = WBBCore::getUser($userID)->getPermission('user.rgallery.uploadsPerWeek_disabled');
		return $rights_array;
	}

	public static function getUserStatus($userID = false) {
		if ($userID == false)
			$userID = WBBCore::getUser()->userID;
		if (!$userID)
			return false;
		$result = array ();
		$userrights = RGallerySystem::getUserPermissions($userID);
		// check uploads per week
		$oneweek = time() - (60 * 60 * 24 * 7); // get the timestamp of NOW minus OneWeek
		$sql = "SELECT count(i.itemID) as count FROM wcf" . WCF_N . "_rGallery_items as i, wcf" . WCF_N . "_rGallery_items_owner as io WHERE i.itemID=io.itemID AND io.ownerID='" . intval($userID) . "' AND i.itemAddedDate >= '$oneweek'";
		$count = WCF::getDB()->getFirstRow($sql);
		$result ['current_uploads_per_week'] = $count ['count'];
		if ($userrights ['uploads_per_week_disabled'] == 1) {
			// if the user has unlimited uploads
			$result ['left_uploads_per_week'] = '-1';
			$result ['percent_uploads_per_week'] = 100;
		} else {
			$result ['left_uploads_per_week'] = $userrights ['uploads_per_week'] - $count ['count'];
			if ($result ['left_uploads_per_week'] < 0)
				$result ['left_uploads_per_week'] = 0;
			if ($userrights ['uploads_per_week'] <= 0)
				$result ['percent_uploads_per_week'] = 100;
			else
				$result ['percent_uploads_per_week'] = round(100 * $result ['left_uploads_per_week'] / ($userrights ['uploads_per_week']), 2);
		}
		$result ['color_uploads_per_week'] = RGallerySystem::getPercentColor($result ['percent_uploads_per_week']);
		// check the user quota
		$sql = "SELECT sum(i.itemResizedSize) as size FROM wcf" . WCF_N . "_rGallery_items as i, wcf" . WCF_N . "_rGallery_items_owner as io WHERE i.itemID=io.itemID AND io.ownerID='" . intval($userID) . "'";
		$count = WCF::getDB()->getFirstRow($sql);
		$result ['current_quota'] = $count ['size'];
		if ($userrights ['quota_disabled'] == 1) {
			// if the user has unlimited space
			$result ['left_quota'] = '-1';
			$result ['percent_quota'] = 100;
		} else {
			$result ['left_quota'] = $userrights ['quota'] * 1000 * 1000 - $count ['size'];
			if ($result ['left_quota'] < 0)
				$result ['left_quota'] = 0;
			if ($userrights ['quota'] <= 0)
				$result ['percent_quota'] = 0;
			else
				$result ['percent_quota'] = round(100 * $result ['left_quota'] / ($userrights ['quota'] * 1000 * 1000), 2);
		}
		$result ['color_quota'] = RGallerySystem::getPercentColor($result ['percent_quota']);
		// get the count() of all the users images and the sum of his clicks
		$sql = "SELECT count(i.itemID) as totalitems, sum(i.itemClicks) as totalclicks FROM wcf" . WCF_N . "_rGallery_items_owner as io, wcf" . WCF_N . "_rGallery_items as i WHERE i.itemID = io.itemID AND io.ownerID=" . intval($userID);
		$row = WCF::getDB()->getFirstRow($sql);
		$result ['totalitems'] = $row ['totalitems'];
		$result ['totalclicks'] = $row ['totalclicks'];
		if (empty($result ['totalclicks']))
			$result ['totalclicks'] = 0;
			// lets find out how many comments this user has
		$sql = "SELECT count(ic.commentID) as totalcomments " . "FROM wcf" . WCF_N . "_rGallery_items_owner as io," . "wcf" . WCF_N . "_rGallery_items_comment as ic " . " WHERE io.ownerID=" . intval($userID) . " " . " AND ic.itemID = io.itemID";
		$row = WCF::getDB()->getFirstRow($sql);
		$result ['totalcomments'] = $row ['totalcomments'];
		if ($result ['left_uploads_per_week'] == 0) {
			// find out when this user is allowed to upload his next picture!
			$now = time();
			$lastweek = $now - (7 * 24 * 60 * 60);
			$sql = "SELECT i.itemAddedDate as lastAdded FROM wcf" . WCF_N . "_rGallery_items as i, wcf" . WCF_N . "_rGallery_items_owner as io WHERE i.itemID = io.itemID AND i.itemAddedDate > $lastweek AND io.ownerID=" . intval($userID) . " ORDER BY i.itemAddedDate ASC LIMIT 1";
			$row = WCF::getDB()->getFirstRow($sql);
			$result ['nextupload'] = $row ['lastAdded'] + (7 * 24 * 60 * 60);
		}
		return $result;
	}

	public function checkUserUpload($userID = false, $filepath) {
		if ($userID == false)
			$userID = WBBCore::getUser()->userID;
		if (!$userID)
			return false;
		$current_status = RGallerySystem::getUserStatus($userID);
		// check the uploads per week
		if ($current_status ['left_uploads_per_week'] == 0)
			return false;
			// check the left space
		$image_name = $this->upload_dir . '/tmp_' . $this->filepath . '.jpg';
		RGallerySystem::resizeImage($filepath, $image_name, false, RGALLERY_IMAGE_COMPRESSION, RGALLERY_IMAGE_SIZE_W, RGALLERY_IMAGE_SIZE_H);
		$filesize = filesize($image_name);
		unlink($image_name);
		if ($current_status ['left_quota'] < $filesize && $current_status ['left_quota'] != -1)
			return false;
		return true;
	}

	public static function getPercentColor($percent) {
		$colors = array (
				'E05B74',  // very small value - red
				'E37D90', 
				'E1A8B2', 
				'A8DFAA', 
				'80BE83', 
				'57A35B' 
		); // very large value - green
		$colorcode = floor($percent / 20);
		return $colors [$colorcode];
	}

	public function checkInput($input, $type = 'str', $len_min = 0, $len_max = -1) {
		if ($type == 'str' || $type == 'text') {
			if ($len_max == -1)
				$len_max = strlen($input);
			if (strlen($input) < $len_min || strlen($input) > $len_max)
				return false;
			else {
				return true;
			}
		} elseif ($type == 'int') {
			if ($len_max == -1)
				$len_max = $input;
			if ($len_min == 0)
				$len_min = $input;
			if ($input < $len_min || $input > $len_max)
				return false;
			else {
				if (is_numeric($input))
					return true;
				else
					return false;
			}
		}
		return false;
	}

	public static function cutText($value, $length) {
		if (is_array($value))
			list ( $string, $match_to ) = $value;
		else {
			$string = $value;
			$match_to = $value {0};
		}
		$match_start = stristr($string, $match_to);
		$match_compute = strlen($string) - strlen($match_start);
		if (strlen($string) > $length) {
			if ($match_compute < ($length - strlen($match_to))) {
				$pre_string = substr($string, 0, $length);
				$pos_end = strrpos($pre_string, " ");
				if ($pos_end === false)
					$string = $pre_string . "...";
				else
					$string = substr($pre_string, 0, $pos_end) . "...";
			} else if ($match_compute > (strlen($string) - ($length - strlen($match_to)))) {
				$pre_string = substr($string, (strlen($string) - ($length - strlen($match_to))));
				$pos_start = strpos($pre_string, " ");
				$string = "..." . substr($pre_string, $pos_start);
				if ($pos_start === false)
					$string = "..." . $pre_string;
				else
					$string = "..." . substr($pre_string, $pos_start);
			} else {
				$pre_string = substr($string, ($match_compute - round(($length / 3))), $length);
				$pos_start = strpos($pre_string, " ");
				$pos_end = strrpos($pre_string, " ");
				$string = "..." . substr($pre_string, $pos_start, $pos_end) . "...";
				if ($pos_start === false && $pos_end === false)
					$string = "..." . $pre_string . "...";
				else
					$string = "..." . substr($pre_string, $pos_start, $pos_end) . "...";
			}
			$match_start = stristr($string, $match_to);
			$match_compute = strlen($string) - strlen($match_start);
		}
		return $string;
	}

	public function setCurrentCategorie($cat, $view = 'pub_cat') {
		$cats = RGallerySystem::getCategories(0, 1);
		if (in_array($cat, $cats)) {
			WCF::getSession()->register($view, $cat);
			return true;
		} else
			WCF::getSession()->unregister($view);
		return false;
	}

	public static function getCurrentCategorie($view = 'pub_cat') {
		$cat = WCF::getSession()->getVar($view);
		// check if there is at least on image in this cat
		if ($cat != '') {
			$sql = "SELECT count(*) as items FROM wcf" . WCF_N . "_rGallery_items_cat WHERE catID=" . intval($cat);
			$result = WCF::getDB()->getFirstRow($sql);
			if ($result ['items'] == 0) {
				WCF::getSession()->unregister($view);
				return false;
			}
		}
		return $cat;
	}

	public function setCurrentTag($tag, $view = 'pub_tag') {
		$tags = RGallerySystem::getTagsArray();
		if (key_exists($tag, $tags)) {
			WCF::getSession()->register($view, $tag);
			return true;
		} else
			WCF::getSession()->unregister($view);
		return false;
	}

	public static function getCurrentTag($view = 'pub_tag') {
		$cat = WCF::getSession()->getVar($view);
		return $cat;
	}

	public function getCurrentCategorieString($view = 'pub_cat') { // short function to extend sql
		$cat = WCF::getSession()->getVar($view);
		if (!empty($cat)) {
			// check if there is at least on image in this cat
			$sql = "SELECT count(*) as items FROM wcf" . WCF_N . "_rGallery_items_cat WHERE catID=" . intval($cat);
			$result = WCF::getDB()->getFirstRow($sql);
			if ($result ['items'] == 0) {
				WCF::getSession()->unregister($view);
				return false;
			}
			return " AND ic.catID=" . intval($cat) . " ";
		}
		return false;
	}

	public static function getCategories($upload = 0, $ids = 0) { // set $ids to 1 if you only want the cat ids!
		$sql = "SELECT * FROM wcf" . WCF_N . "_rGallery_cats";
		if ($upload)
			$sql .= " WHERE catWriteable=1";
		$sql .= " ORDER BY catName ASC";
		$result = WCF::getDB()->sendQuery($sql);
		$cats = array ();
		while ($row = WCF::getDB()->fetchArray($result)) {
			if ($row ['catAuthorized_group'] && $upload) {
				if (in_array($row ['catAuthorized_group'], WBBCore::getUser()->getGroupIDs()) || $upload == 0) {
					if ($ids == 0)
						$cats [] = $row;
					else
						$cats [$row ['catID']] = $row ['catID'];
				}
			} else {
				if ($ids == 0)
					$cats [$row ['catID']] = $row;
				else
					$cats [] = $row ['catID'];
			}
		}
		return $cats;
	}

	public function isWriteableCat($cat) {
		$sql = "SELECT * FROM wcf" . WCF_N . "_rGallery_cats WHERE catID='" . intval($cat) . "' AND catWriteable=1";
		$row = WCF::getDB()->getFirstRow($sql);
		if ($row ['catAuthorized_group']) {
			if (in_array($row ['catAuthorized_group'], WBBCore::getUser()->getGroupIDs())) {
				return true;
			} else
				return false;
		} elseif ($row ['catName'])
			return true;
		return false;
	}

	public function isGalleryModerator($userID = false) {
		if (!$userID)
			$userID = WBBCore::getUser()->userID;
		if (WBBCore::getUser($userID)->getPermission('mod.rgallery.canModerate')) {
			return true;
		}
		return false;
	}

	public function checkPermissions($userID = false) {
		
		// by default we check the logged in user and the current object
		if ($userID == false) {
			$userID = WBBCore::getUser()->userID;
		}
		if ($this->isGalleryModerator($userID))
			return true;
		if ($this->itemOwnerID === $userID)
			return true;
		return false;
	}

	public static function getElementTags($itemID) {
		$sql = "SELECT t.tag as tag FROM wcf" . WCF_N . "_rGallery_items_tag as it, wcf" . WCF_N . "_rGallery_tags as t WHERE it.itemID='" . intval($itemID) . "' AND t.tagID=it.tagID ORDER BY t.tag ASC";
		$result = WCF::getDB()->sendQuery($sql);
		$return = array ();
		$i = 1;
		while ($row = WCF::getDB()->fetchArray($result)) {
			$return [$i] = $row ['tag'];
			$i++;
		}
		return $return;
	}

	public static function getUserItemsListing($cur_page = 1, $userID = false) {
		if ($cur_page > 0)
			$cur_page--;
		elseif ($cur_page == 0)
			$cur_page = 1;
		if ($userID == false)
			$images_per_page = RGALLERY_IMAGES_PER_USER_PAGE;
		else
			$images_per_page = RGALLERY_IMAGES_PER_PAGE;
		$start = $cur_page * $images_per_page;
		if ($userID == true)
			$other_user = 1;
		else
			$other_user = 0;
		if ($userID == false)
			$userID = WBBCore::getUser()->userID;
		$userID = RGallerySystem::prepInput($userID);
		$add_sql = '';
		if (RGallerySystem::getCurrentCategorie('user_cat'))
			$add_sql .= " AND ic.catID=" . intval(RGallerySystem::getCurrentCategorie('user_cat'));
		if (defined('ACTIVE_TAG')) {
			$active_tag_id = RGallerySystem::getTagId(ACTIVE_TAG);
			$add_sql .= " AND it.tagID='" . intval($active_tag_id) . "'";
		}
		if ($other_user) {
			$add_sql = ''; // we don't want to sort after categories, tags in the users profile
		}
		$sql = "SELECT *
				FROM
				((wcf" . WCF_N . "_rGallery_items as i
					LEFT JOIN wcf" . WCF_N . "_rGallery_items_tag as it
					ON i.itemID = it.itemID)
				INNER JOIN wcf" . WCF_N . "_rGallery_items_cat as ic
				on i.itemID = ic.itemID)
				INNER JOIN wcf" . WCF_N . "_rGallery_items_owner as io
				on i.itemID = io.itemID

				WHERE io.ownerID=" . intval($userID) . "
				" . $add_sql . "

				GROUP BY i.itemID
				ORDER BY i.itemAddedDate DESC
				LIMIT " . $start . ", " . $images_per_page;
		$result = WCF::getDB()->sendQuery($sql);
		$itemArray = array ();
		while ($row = WCF::getDB()->fetchArray($result)) {
			$row ['tags'] = RGallerySystem::getElementTags($row ['itemID']);
			$row ['commentsCount'] = RGallerySystem::getElementCommentsCount($row ['itemID']);
			$itemArray [$row ['itemID']] = $row;
		}
		return $itemArray;
	}

	public static function getTagId($tag) {
		$tag = RGallerySystem::prepInput($tag);
		$sql = "SELECT t.tagID as id FROM wcf" . WCF_N . "_rGallery_tags as t WHERE t.tag = '" . $tag . "'";
		$tagID = WCF::getDB()->getFirstRow($sql);
		if ($tagID ['id'])
			return $tagID ['id'];
		return false;
	}

	public function getItemsListing($cur_page = 1) {
		if ($cur_page > 0)
			$cur_page--;
		elseif ($cur_page == 0)
			$cur_page = 1;
		$start = $cur_page * RGALLERY_IMAGES_PER_PAGE;
		$add_sql = '';
		if (RGallerySystem::getCurrentCategorie())
			$add_sql .= " AND ic.catID=" . intval(RGallerySystem::getCurrentCategorie());
		if (defined('ACTIVE_TAG')) {
			$active_tag_id = RGallerySystem::getTagId(ACTIVE_TAG);
			$add_sql .= " AND it.tagID='" . intval($active_tag_id) . "'";
		}
		$show_elements = RGALLERY_IMAGES_PER_PAGE;
		if (WBBCore::getUser()->getPermission('user.rgallery.canUpload') == 1) {
			if ($cur_page == 0)
				$show_elements--;
			else // show -1 element if the user can upload and we're on the first page. we need space to display the "click here to upload" link
				$start--; // because we displayed -1 image on the first page, we have to view one more on each page!
		}
		$sql = "SELECT *
				FROM
				(wcf" . WCF_N . "_rGallery_items as i
					LEFT JOIN wcf" . WCF_N . "_rGallery_items_tag as it
					ON i.itemID = it.itemID)
				INNER JOIN wcf" . WCF_N . "_rGallery_items_cat as ic
				on i.itemID = ic.itemID

				WHERE 1=1
				" . $add_sql . "

				GROUP BY i.itemID
				ORDER BY i.itemAddedDate DESC
				LIMIT " . $start . ", " . $show_elements;
		$result = WCF::getDB()->sendQuery($sql);
		$itemArray = array ();
		while ($row = WCF::getDB()->fetchArray($result)) {
			$tmpItem = new RGalleryItem();
			$tmpItem->setItemID($row ['itemID']);
			$itemArray [$row ['itemID']] = $tmpItem->getData();
		}
		return $itemArray;
	}

	public function getGalleryPages($userID = false) {
		$add_sql = '';
		if (RGallerySystem::getCurrentCategorie())
			$add_sql .= " AND ic.catID=" . RGallerySystem::getCurrentCategorie();
		if (defined('ACTIVE_TAG')) {
			$active_tag_id = RGallerySystem::getTagId(ACTIVE_TAG);
			$add_sql .= " AND it.tagID='" . intval($active_tag_id) . "'";
		}
		if ($userID) {
			$userID = RGallerySystem::prepInput($userID);
			$other_user = 1;
		} else
			$other_user = 0;
		if ($other_user) {
			$add_sql = "WHERE io.ownerID=" . intval($userID); // we don't want to sort after categories, tags in the users profile
			//			$images_per_page = RGALLERY_IMAGES_PER_PAGE;
		}
		//		else {
		//			$images_per_page = RGALLERY_IMAGES_PER_USER_PAGE;
		//		}
		$sql = "SELECT i.itemID
				FROM
				((wcf" . WCF_N . "_rGallery_items as i
					LEFT JOIN wcf" . WCF_N . "_rGallery_items_tag as it
					ON i.itemID = it.itemID)
				INNER JOIN wcf" . WCF_N . "_rGallery_items_cat as ic
				on i.itemID = ic.itemID)
				INNER JOIN wcf" . WCF_N . "_rGallery_items_owner as io
				on i.itemID = io.itemID

				" . $add_sql . "

				GROUP BY i.itemID";
		#list($pages) = WCF::getDB()->getFirstRow($sql);
		$pages = WCF::getDB()->sendQuery($sql);
		$pages = WCF::getDB()->countRows($pages);
		
		/*		$sql = "SELECT count(i.itemID) as count
				FROM wcf" . WCF_N . "_rGallery_items as i,
					wcf" . WCF_N . "_rGallery_items_tag as it,
					wcf" . WCF_N . "_rGallery_items_cat as ic,
					wcf" . WCF_N . "_rGallery_items_owner as io
				WHERE ic.itemID = i.itemID
					AND io.itemID = i.itemID
					AND it.itemID=i.itemID
				" . $add_sql; */
		
		// 		$pages = WCF::getDB()->getFirstRow($sql);
		// 		$pages = $pages['count'];
		// 		echo mysql_error();
		

		if (WBBCore::getUser()->getPermission('user.rgallery.canUpload') == 1) {
			// because we display -1 image per page we have to add one to display the correct pages!
			$pages++;
		}
		if ($pages == 0)
			return false;
		return ceil($pages / RGALLERY_IMAGES_PER_PAGE);
	}

	public function checkVariableInput($chk) {
		
		// we're going to do some basic checks on our input data - no check if data is submitted!
		if (!empty($chk ['commentText']) && !$this->checkInput($chk ['commentText'], 'str', 0, 2000))
			return false;
		if (!empty($chk ['itemTitle']) && !$this->checkInput($chk ['itemTitle'], 'str', 0, 64))
			return false;
		if (!empty($chk ['itemTags']) && (!$this->checkInput($chk ['itemTags'], 'str', 0, 2000)))
			return false;
		return true;
	}

	public function getElementComments($itemID) {
		$sql = "SELECT c.commentText as commentText,
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
				AND ic.itemID=" . intval($itemID) . '

				ORDER BY c.commentAddedDate ASC';
		$result = WCF::getDB()->sendQuery($sql);
		$commentArray = array ();
		$bbcode = new MessageParser();
		while ($row = WCF::getDB()->fetchArray($result)) {
			$user = new UserProfile($row ['userID']);
			// 			$user = new User($row['userID']);
			if (!$user->username)
				$row ['userID'] = '';
			else
				$row ['userName'] = $user->username;
			$row ['commentText'] = $bbcode->parse($row ['commentText'], true, false, true);
			$row ['author'] = $user;
			$commentArray [$row ['commentID']] = $row;
		}
		return $commentArray;
	}

	public static function getElementCommentsCount($itemID) {
		$sql = "SELECT count(*) as count FROM `wcf" . WCF_N . "_rGallery_items_comment` WHERE itemID='" . intval($itemID) . "'";
		$result = WCF::getDB()->getFirstRow($sql);
		return $result ['count'];
	}

	public function getUserLastComments($userID = false) {
		if (!$userID)
			$userID = WBBCore::getUser()->userID;
		$sql = "SELECT c.commentText as commentText,
				c.commentID as commentID,
				c.commentAddedDate as commentAddedDate,
				cu.userID as userID,
				cu.userName as userName,
				i.itemID as itemID,
				i.itemName as itemName
				FROM
				`wcf" . WCF_N . "_rGallery_items` as i,
				`wcf" . WCF_N . "_rGallery_comments` as c,
				`wcf" . WCF_N . "_rGallery_items_comment` as ic,
				`wcf" . WCF_N . "_rGallery_comments_user` as cu,
				`wcf" . WCF_N . "_rGallery_items_owner` as io
				WHERE
				io.ownerID=" . intval($userID) . "
				AND ic.itemID = io.itemID
				AND cu.commentID = ic.commentID
				AND c.commentID = ic.commentID
				AND i.itemID = io.itemID

				ORDER BY c.commentAddedDate DESC

				LIMIT 3";
		$result = WCF::getDB()->sendQuery($sql);
		$commentsArray = array ();
		while ($row = WCF::getDB()->fetchArray($result)) {
			$commentsArray [] = $row;
		}
		return $commentsArray;
	}

	public static function getTagsArray($userID = false) { // get an array with the tags and how often they are used
		$add_sql = '';
		if ($userID) {
			$add_sql = " AND io.ownerID=" . intval($userID);
			$current_cat = intval(RGallerySystem::getCurrentCategorie('user_cat'));
			if ($current_cat != 0)
				$add_sql .= " AND ic.catID=" . $current_cat . ' ';
		} else {
			$current_cat = RGallerySystem::getCurrentCategorie();
			if ($current_cat != 0)
				$add_sql .= " AND ic.catID=" . $current_cat . ' ';
		}
		$sql = "SELECT
				count(it.tagID) as anzahl,
				t.tag as tag
			FROM `wcf" . WCF_N . "_rGallery_items_tag` as it,
			`wcf" . WCF_N . "_rGallery_tags` as t,
			wcf" . WCF_N . "_rGallery_items as i,
			wcf" . WCF_N . "_rGallery_items_cat as ic,
			wcf" . WCF_N . "_rGallery_items_owner as io
			WHERE it.tagID = t.tagID
			AND i.itemID = it.itemID
			AND io.itemID = i.itemID
			AND ic.itemID = i.itemID
			" . $add_sql . "
			GROUP BY it.tagID";
		$result = WCF::getDB()->sendQuery($sql);
		$tagsArray = array ();
		while ($row = WCF::getDB()->fetchArray($result)) {
			$tagsArray [$row ['tag']] = $row ['anzahl'];
		}
		arsort($tagsArray);
		return $tagsArray;
	}

	public function generateTagCloud($userID = false, $page) {
		$tags = RGallerySystem::getTagsArray($userID);
		$return = "<ol>";
		$font_min = "90";
		$font_max = "200";
		$color_min = "180"; // light
		$color_max = "30"; // dark
		$max = current($tags);
		$min = end($tags);
		if ($max - $min == 0)
			$min++;
		ksort($tags);
		foreach ($tags as $tag=>$val) {
			$font = round(-($font_max - $font_min) * ($max - $val) / ($max - $min) + $font_max, 0);
			$color = round(-($color_max - $color_min) * ($max - $val) / ($max - $min) + $color_max, 0);
			if (RGALLERY_COLOR_TAGS == 1)
				$color_str = 'style="color: rgb(' . $color . ',' . $color . ',' . $color . ');"';
			else
				$color_str = "";
			$return .= '<li style="font-size: ' . $font . '%;display: inline"><a ' . $color_str . ' href="' . RELATIVE_WBB_DIR . 'index.php?page=' . $page . '&amp;tag=' . urlencode($tag) . SID_ARG_2ND . '" title="' . $val . '">' . htmlspecialchars(RGallerySystem::cutText($tag, 15)) . '</a> </li>';
		}
		$return .= "</ol>";
		return $return;
	}

	public function deleteComment($commentID) {
		$sql = "DELETE FROM wcf" . WCF_N . "_rGallery_items_comment WHERE commentID='" . intval($commentID) . "'";
		if (!WCF::getDB()->sendQuery($sql))
			return false;
		$sql = "DELETE FROM wcf" . WCF_N . "_rGallery_comments WHERE commentID='" . intval($commentID) . "'";
		if (!WCF::getDB()->sendQuery($sql))
			return false;
		$sql = "DELETE FROM wcf" . WCF_N . "_rGallery_comments_user WHERE commentID='" . intval($commentID) . "'";
		if (!WCF::getDB()->sendQuery($sql))
			return false;
		return true;
	}

	public function deleteAllUserItems($userID = false) {
		if (!$userID)
			$userID = WBBCore::getUser()->userID;
		$sql = "SELECT i.itemID as itemID, " . "io.ownerID as ownerID " . "FROM wcf" . WCF_N . "_rGallery_items AS i, " . "wcf" . WCF_N . "_rGallery_items_owner AS io " . "WHERE io.ownerID='" . intval($userID) . "' " . "AND i.itemID = io.itemID";
		$query = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($query)) {
			$item = new RGalleryItem();
			$item->setItemID($row ['itemID']);
			$item->deleteItem();
		}
		return true;
	}

	public static function resizeImage($input, $output, $squared, $compression = 65, $X, $Y = false, $watermark = false) {
		list ( $width, $height, $imagetype ) = getimagesize($input);
		// first check if we need to resize
		// we would not accept images smaller than thumbsize - so no squared sizes have to be checked
		if (!$squared) {
			if ($width <= $X)
				$X = $width;
			if ($height <= $Y)
				$Y = $height;
		}
		if (RGALLERY_USE_CONVERT == 1) {
			// user imagemagicks convert tool
			$retval = true;
			if ($squared) {
				$tmp_large_image_size = 2 * $X;
				system("convert " . $input . " -thumbnail x" . $tmp_large_image_size . " -resize '" . $tmp_large_image_size . "x<' -resize 50% -gravity center -crop " . $X . "x" . $X . "+0+0 +repage -quality " . $compression . " " . $output, $retval);
				return $retval;
			} else {
				system("convert " . $input . " -thumbnail \"" . $X . "x" . $Y . ">\" -quality " . $compression . " " . $output, $retval);
				return $retval;
			}
		} else {
			// use gd!
			if ($imagetype == 1)
				$image = @imagecreatefromgif($input);
			elseif ($imagetype == 2)
				$image = @imagecreatefromjpeg($input);
			elseif ($imagetype == 3)
				$image = @imagecreatefrompng($input);
			else
				return false;
			if (!$image) {
				return false;
			}
			// check if the image needs to be rotated
			$rotate = RGallerySystem::getRotationStatus($input);
			if ($rotate) {
				$image = imagerotate($image, $rotate, 0);
				$width = imagesx($image);
				$height = imagesy($image);
			}
			
			if ($squared) {
				if ($width > $height) {
					$new_width = round($X * ($width / $height));
					$new_height = $X;
					$thumb = imagecreatetruecolor($X, $X);
					imagecopyresampled($thumb, $image, 0, 0, ($width - $height) / 2, 0, $new_width, $new_height, $width, $height);
				} else if ($width == $height) { // If a square image
					$thumb = imagecreatetruecolor($X, $X);
					imagecopyresampled($thumb, $image, 0, 0, 0, 0, $X, $X, $width, $height);
				} else { // Must be a portrait image
					$new_width = $X;
					$new_height = round($X * ($height / $width));
					$thumb = imagecreatetruecolor($new_width, $new_width);
					imagecopyresampled($thumb, $image, 0, 0, 0, ($height - $width) / 2, $new_width, $new_height, $width, $height);
				}
			} else {
				// calculate the new height and width
				if ($width > $height) {
					// landscape
					$new_width = $X;
					$new_height = $height * ($X / $width);
					if ($new_height > $Y) {
						$new_height = $Y;
						$new_width = $width * ($Y / $height);
					}
				} elseif ($width < $height) {
					// portrait
					$new_width = $width * ($Y / $height);
					$new_height = $Y;
					if ($new_width > $X) {
						$new_width = $X;
						$new_height = $height * ($X / $width);
					}
				} else { // ($width == $height)
					if ($X > $Y) {
						$new_width = $Y;
						$new_height = $Y;
					} elseif ($X < $Y) {
						$new_width = $X;
						$new_height = $X;
					} else {
						$new_width = $X;
						$new_height = $Y;
					}
				}
				$thumb = imagecreatetruecolor($new_width, $new_height);
				imagecopyresampled($thumb, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			}
			// watermark the image!
			if ($watermark == 1) {
				$thumb = RGallerySystem::watermarkImage($thumb, RGALLERY_IMAGE_PATH . '/watermark.png');
			}
			if (!imagejpeg($thumb, $output, $compression))
				return false;
			chmod($output, 0666);
			imagedestroy($image);
			imagedestroy($thumb);
			return true;
		}
	}

	public static function watermarkImage($im, $watermark) {
		$watermark = @imagecreatefrompng($watermark);
		if (!$watermark) {
			require_once (WCF_DIR . 'lib/system/exception/NamedUserException.class.php');
			throw new NamedUserException('rGallery could not be initialized. <br /><strong>Information:</strong> watermark image could not be opened!');
		}
		$watermark_width = imagesx($watermark);
		$watermark_height = imagesy($watermark);
		$size_x = imagesx($im);
		$size_y = imagesy($im);
		$dest_x = $size_x - $watermark_width - 5;
		$dest_y = $size_y - $watermark_height - 5;
		imagealphablending($im, TRUE);
		$return = imagecopy($im, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height);
		if ($return)
			return $im;
		return false;
	}

	public function getAllowedFileExtensions() {
		$extensions = explode(",", RGALLERY_ALLOWED_EXTENSIONS);
		foreach ($extensions as $idx=>$value)
			$extensions [$idx] = strtolower(trim($value));
		return $extensions;
	}

	public function checkMaxImageSize($imagedata) {
		$k64 = 65536; // number of bytes in 64K
		$mb = 1048576;
		$fudge = 1.75; // "fudge" factor. mostly between 1.5 and 2. 1.75 should fit most systems and we're on the safe side
		if (!isset($imagedata ['bits']))
			$imagedata ['bits'] = 24; // set to truecolor, so we're on the safe side
		elseif (!$imagedata ['bits'])
			$imagedata ['bits'] = 24; // set to truecolor, so we're on the safe side
		if (!isset($imagedata ['channels']))
			$imagedata ['channels'] = 3; // default for RGB images
		elseif (!$imagedata ['channels'])
			$imagedata ['channels'] = 3; // default for RGB images
		$memoryNeeded = round(($imagedata [0] * $imagedata [1] * $imagedata ['bits'] * $imagedata ['channels'] / 8 + $k64) * $fudge);
		$memoryLimitMB = (integer) @ini_get('memory_limit');
		$memoryLimit = $memoryLimitMB * $mb;
		if ($memoryLimit < 0)
			return true;
		if (!$memoryLimit)
			$memoryLimit = 8 * $mb; // use default 8mb
		if ($memoryNeeded > $memoryLimit)
			return false;
		return true;
	}

	/**
	 * Get the orientation of an image if exif is available
	 *
	 * @param string $image
	 * @return int degree of rotation cw needed or false if no rotation is needed or exif is not available
	 */
	public static function getRotationStatus($image) {
		if (!function_exists('exif_read_data')) {
			return false;
		}
		$exif = @exif_read_data($image);
		if (!isset($exif ["Orientation"]))
			return false;
		$exifOrientation = $exif ["Orientation"];
		$rotate = 0;
		$flip = false;
		switch ($exifOrientation) {
			case 1:
				$rotate = false;
				$flip = false;
			break;
			
			case 2:
				$rotate = false;
				$flip = true;
			break;
			
			case 3:
				$rotate = 180;
				$flip = false;
			break;
			
			case 4:
				$rotate = 180;
				$flip = true;
			break;
			
			case 5:
				$rotate = 270;
				$flip = true;
			break;
			
			case 6:
				$rotate = 270;
				$flip = false;
			break;
			
			case 7:
				$rotate = 90;
				$flip = true;
			break;
			
			case 8:
				$rotate = 90;
				$flip = false;
			break;
		}
		return $rotate;
	}
}

class RGalleryItem extends RGallerySystem {

	public $itemID;

	public $filename;

	public $upload_dir;

	public $itemOwnerID;

	function RGalleryItem() { // constructor
		return true;
	}

	public function setItemID($itemID) {
		$this->itemID = $itemID;
		$this->getItemParameters();
	}

	private function getItemParameters() {
		
		// Get the path of the image
		$sql = "SELECT ownerID FROM wcf" . WCF_N . "_rGallery_items_owner WHERE itemID='" . intval($this->itemID) . "'";
		$owner = WCF::getDB()->getFirstRow($sql);
		$this->itemOwnerID = $owner ['ownerID'];
		$image_path = RGALLERY_IMAGE_PATH;
		if ($image_path [0] == '/' || $image_path [1] == ':')
			$this->upload_dir = RGALLERY_IMAGE_PATH . '/' . $this->itemOwnerID;
		else
			$this->upload_dir = WBB_DIR . RGALLERY_IMAGE_PATH . '/' . $this->itemOwnerID;
		// 		$this->upload_dir = RGALLERY_IMAGE_PATH.'/'.$this->itemOwnerID;
	}

	private function deleteTags() {
		$sql = "DELETE FROM wcf" . WCF_N . "_rGallery_items_tag WHERE itemID='" . intval($this->itemID) . "'";
		if (WCF::getDB()->sendQuery($sql))
			return true;
		return false;
	}

	private function deleteCategory() {
		$sql = "DELETE FROM wcf" . WCF_N . "_rGallery_items_cat WHERE itemID='" . intval($this->itemID) . "'";
		if (WCF::getDB()->sendQuery($sql))
			return true;
		return false;
	}

	public function setTags($tags_str) {
		
		// 		if(empty(trim($tags_str))) return true;
		$tags = array_unique(explode(",", $tags_str));
		$tagIDs = array ();
		// first delete tag links for this item
		$this->deleteTags();
		// then let us get all the needed tag ids
		foreach ($tags as $idx=>$value) {
			$value = trim($tags [$idx]);
			
			// remove multiple whitespaces
			$value = preg_replace('/\s+/', ' ', $value);
			
			// remove commas from tags
			// $value = str_replace(',', '', $value);
			if (!empty($value)) {
				$sql = "SELECT * FROM wcf" . WCF_N . "_rGallery_tags WHERE tag='" . $value . "'";
				$result = WCF::getDB()->getFirstRow($sql);
				if ($result ['tagID'])
					$tagIDs [] = $result ['tagID'];
				else {
					$sql = "INSERT INTO wcf" . WCF_N . "_rGallery_tags (tag) VALUES ('" . $value . "')";
					$result = WCF::getDB()->sendQuery($sql);
					$tagIDs [] = WCF::getDB()->getInsertID($result);
				}
			}
		}
		// lets write it into the db
		foreach ($tagIDs as $idx=>$value) {
			$sql = "INSERT INTO wcf" . WCF_N . "_rGallery_items_tag (itemID, tagID) VALUES ('" . intval($this->itemID) . "', '" . $value . "')";
			WCF::getDB()->sendQuery($sql);
		}
		return true;
	}

	public function getUserPages() {
		$add_sql = '';
		if (RGallerySystem::getCurrentCategorie('user_cat'))
			$add_sql .= " AND ic.catID=" . intval(RGallerySystem::getCurrentCategorie('user_cat'));
		if (defined('ACTIVE_TAG')) {
			$active_tag_id = RGallerySystem::getTagId(ACTIVE_TAG);
			$add_sql .= " AND it.tagID='" . intval($active_tag_id) . "'";
		}
		$sql = "SELECT *
				FROM
				((wcf" . WCF_N . "_rGallery_items as i
					LEFT JOIN wcf" . WCF_N . "_rGallery_items_tag as it
					ON i.itemID = it.itemID)
				INNER JOIN wcf" . WCF_N . "_rGallery_items_cat as ic
				on i.itemID = ic.itemID)
				INNER JOIN wcf" . WCF_N . "_rGallery_items_owner as io
				on i.itemID = io.itemID

				WHERE io.ownerID=" . WBBCore::getUser()->userID . "
				" . $add_sql . "

				GROUP BY i.itemID";
		// `rGallery_items_cat` as ic    RGallerySystem::getCurrentCategorieString('user_cat')
		$pages = WCF::getDB()->sendQuery($sql);
		$pages = WCF::getDB()->countRows($pages);
		if ($pages == 0)
			return false;
		return ceil($pages / RGALLERY_IMAGES_PER_USER_PAGE);
	}

	public function getWasteItems($array = 0) {
		$return = '';
		$images = WCF::getSession()->getVar('images');
		if (is_array($images)) {
			foreach ($images as $idx=>$value) {
				if ($array == 1)
					$return .= "-'" . $idx . "':'1'-";
				else
					$return .= "-" . $idx . "-";
			}
			// nasty but fast trick to get correct commas
			$return = str_replace('-', '', str_replace("--", ', ', $return));
			return $return;
		}
		return false;
	}

	public function upload($userID = false, $import = 0) {
		
		// set the memory limit to 128MB - should help some users with memory errors
		@ini_set('memory_limit', '128M');
		@set_time_limit(0);
		if ($userID == false)
			$userID = WBBCore::getUser()->userID;
		$this->filename = $_FILES ['file'] ['name'];
		$this->filesize = $_FILES ['file'] ['size'];
		$this->filetype = $_FILES ['file'] ['type'];
		$this->filepath = md5($_FILES ['file'] ['tmp_name'] . microtime());
		$this->fileextension = $this->getFileExtension();
		if ($import == 0)
			$this->upload_dir = RGALLERY_IMAGE_PATH . '/' . $userID;
		else {
			// we have to check if the image_path is absolute, when we import images
			$image_path = RGALLERY_IMAGE_PATH;
			if ($image_path [0] == '/' || $image_path [1] == ':')
				$this->upload_dir = RGALLERY_IMAGE_PATH . '/' . $userID;
			else
				$this->upload_dir = WBB_DIR . RGALLERY_IMAGE_PATH . '/' . $userID;
		}
		if (!file_exists(RGALLERY_IMAGE_PATH)) {
			if (!mkdir(RGALLERY_IMAGE_PATH, 0777))
				$this->initError('RGALLERY_IMAGE_PATH does not exist and could not be created! Check your config and filepermissions!');
			else
				chmod(RGALLERY_IMAGE_PATH, 0777);
		}
		if (!file_exists($this->upload_dir)) {
			if (!mkdir($this->upload_dir, 0777))
				$this->initError('Userdirectory could not be created! Check your config and filepermissions!');
			else
				chmod($this->upload_dir, 0777);
		}
		$error = '';
		if (!in_array(strtolower($this->fileextension), $this->getAllowedFileExtensions())) {
			$error = 'wrongfiletype';
			return $error;
		}
		if ($import == 0)
			if (!$this->checkUserUpload($userID, $_FILES ['file'] ['tmp_name'])) {
				$error = 'nospaceleft';
				return $error;
			}
			// check if the image has the minimum/max file sizes
		$imagedata = @getimagesize($_FILES ['file'] ['tmp_name']);
		$width = $imagedata [0];
		$height = $imagedata [1];
		if (!$width || !$height) {
			$error = 'wrongfiletype';
			return $error;
		}
		if (!$this->checkMaxImageSize($imagedata)) {
			$error = 'toolarge';
			return $error;
		}
		if ($import == 0)
			if ($width < RGALLERY_MIN_SIZE_W || $height < RGALLERY_MIN_SIZE_H) {
				$error = 'toosmall';
				return $error;
			}
			// check if the choosen cat is valid
		if ($import == 0)
			if (!$this->isWriteableCat($this->prepInput($_POST ['itemCat']))) {
				$error = 'catnotallowd';
				return $error;
			}
			// now lets do the input data check
		if (!$this->checkVariableInput($_POST)) {
			$error = 'inputnotallowed';
			return $error;
		}
		if (empty($_POST ['itemName']))
			$this->name = $this->filename;
		else
			$this->name = $_POST ['itemName'];
		$this->fullfilepath = $this->upload_dir . '/' . $this->filepath . '.' . $this->fileextension;
		//		if($import==0)	move_uploaded_file($_FILES['file']['tmp_name'], $this->fullfilepath);
		// cause we also allow tar archives, we have to user copy instead of move_uploaded_file
		if ($import == 0)
			copy($_FILES ['file'] ['tmp_name'], $this->fullfilepath);
		else
			copy($_FILES ['file'] ['tmp_name'], $this->fullfilepath);
		chmod($this->fullfilepath, 0666);
		// thumbnail erstellen
		$thumbnails = $this->makeResizedImages();
		if (!$thumbnails) { // the upload failed
			$error = 'thumbnails';
			return $error;
		}
		$mime = 'image/jpeg'; // for now we only serve jpegs to the user
		// element in db eintragen
		$sql = "INSERT INTO `wcf" . WCF_N . "_rGallery_items` (
						`itemOrigName`,
						`itemName`,
						`itemAddedDate`,
						`itemPath`,
						`itemOrigExtension`,
						`itemSize`,
						`itemType`,
						`itemMime`,
						`itemOrigMime`,
						`itemComment`,
						`itemResizedSize`,
						`itemDimW`,
						`itemDimH`,
						`itemOrigDimW`,
						`itemOrigDimH`) VALUES (
						'" . $this->prepInput($this->filename) . "',
						'" . $this->prepInput($this->name) . "',
						" . time() . ",
						'" . $this->filepath . "',
						'" . $this->getFileExtension() . "',
						'" . $this->filesize . "',
						'image',
						'" . $mime . "',
						'" . $this->filetype . "',
						'" . $this->prepInput($_POST ['itemComment']) . "',
						'" . $this->itemResizedSize . "',
						'" . $this->itemDimW . "',
						'" . $this->itemDimH . "',
						'" . $this->itemOrigDimW . "',
						'" . $this->itemOrigDimH . "'
						)";
		$result = WCF::getDB()->sendQuery($sql);
		$this->setItemID(WCF::getDB()->getInsertID($result));
		$this->setOwner($userID);
		$this->getItemParameters(); // we have to update our parameters after adding the owner
		$this->setCategorie($this->prepInput($_POST ['itemCat']));
		$this->setTags($this->prepInput($_POST ['itemTags']));
		if ($import == 0)
			$rights = $this->getUserPermissions();
		else
			$rights = $GLOBALS ['rights'];
		if (!$rights ['store_orig'])
			unlink($this->fullfilepath);
		$this->logger('item added - rights: ' . $rights ['store_orig']);
		return true;
	}

	public function update($data) {
		if (empty($data))
			return false;
		$old_data = $this->getData();
		if (empty($data ['itemName']))
			$data ['itemName'] = $old_data ['itemName'];
		$sql = "UPDATE `wcf" . WCF_N . "_rGallery_items` SET `itemName`='" . $this->prepInput($data ['itemName']) . "',
							`itemModDate`=" . time() . ",
							`itemComment`='" . $this->prepInput($data ['itemComment']) . "'
						WHERE itemID='" . intval($this->itemID) . "'";
		$result = WCF::getDB()->sendQuery($sql);
		if (!$result)
			return false;
		if (!$this->setTags($this->prepInput($data ['itemTags'])))
			return false;
		$this->deleteCategory();
		if (!$this->setCategorie($this->prepInput($data ['itemCat'])))
			return false;
		;
		return true;
	}

	private function setOwner($owner = false) {
		if (!$owner)
			$owner = WBBCore::getUser()->userID;
		$sql = "INSERT INTO `wcf" . WCF_N . "_rGallery_items_owner` (
						`itemID`,
						`ownerID`) VALUES (
						'" . intval($this->itemID) . "',
						'" . intval($owner) . "'
						)";
		$result = WCF::getDB()->sendQuery($sql);
		if ($result)
			return true;
		else
			return false;
	}

	private function setCategorie($cat) {
		$sql = "INSERT INTO `wcf" . WCF_N . "_rGallery_items_cat` (
						`itemID`,
						`catID`) VALUES (
						'" . intval($this->itemID) . "',
						'" . intval($cat) . "'
						)";
		if (WCF::getDB()->sendQuery($sql))
			return true;
		else
			return false;
	}

	public static function deleteComments($itemID = false) {
		if ($itemID == false)
			$itemID = $this->itemID;
		$sql = "SELECT commentID FROM wcf" . WCF_N . "_rGallery_items_comment WHERE itemID='" . intval($itemID) . "'";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			RGallerySystem::deleteComment($row ['commentID']);
		}
		return true;
	}

	public function deleteItem() {
		
		// first we start deleting the image itself
		$sql = "SELECT * FROM wcf" . WCF_N . "_rGallery_items WHERE itemID='" . intval($this->itemID) . "' LIMIT 1";
		$row = WCF::getDB()->getFirstRow($sql);
		if (!$row)
			return false;
		$sql = "DELETE FROM wcf" . WCF_N . "_rGallery_items WHERE itemID='" . intval($this->itemID) . "'";
		$result1 = WCF::getDB()->sendQuery($sql);
		$sql = "DELETE FROM wcf" . WCF_N . "_rGallery_items_owner WHERE itemID='" . intval($this->itemID) . "'";
		$result2 = WCF::getDB()->sendQuery($sql);
		$sql = "DELETE FROM wcf" . WCF_N . "_rGallery_items_cat WHERE itemID='" . intval($this->itemID) . "'";
		$result3 = WCF::getDB()->sendQuery($sql);
		$sql = "DELETE FROM wcf" . WCF_N . "_rGallery_items_tag WHERE itemID='" . intval($this->itemID) . "'";
		$result4 = WCF::getDB()->sendQuery($sql);
		$result5 = $this->deleteComments();
		if (!$result1 || !$result2 || !$result3 || !$result4 || !$result5)
			return false;
		$thumb_name = $this->upload_dir . '/thumb_' . $row ['itemPath'] . '.jpg';
		$tthumb_name = $this->upload_dir . '/tthumb_' . $row ['itemPath'] . '.jpg';
		$preview_name = $this->upload_dir . '/preview_' . $row ['itemPath'] . '.jpg';
		$image_name = $this->upload_dir . '/image_' . $row ['itemPath'] . '.jpg';
		$orig_name = $this->upload_dir . '/' . $row ['itemPath'] . '.' . $row ['itemOrigExtension'];
		if (!unlink($thumb_name) || !unlink($tthumb_name) || !unlink($preview_name) || !unlink($image_name)) {
			return false;
		}
		if (file_exists($orig_name))
			unlink($orig_name);
		return true;
	}

	private function getFileExtension() {
		$endung = array ();
		preg_match("/\.([a-zA-Z0-9]{1,4})$/i", $this->filename, $endung);
		if (!empty($endung [1]))
			return $endung [1];
		else
			return false;
	}

	private function makeResizedImages() {
		
		// Get the current info on the file
		$current_size = getimagesize($this->fullfilepath);
		// This part gets the new name
		// generate thumbnail
		$thumb_name = $this->upload_dir . '/thumb_' . $this->filepath . '.jpg';
		$retval1 = RGallerySystem::resizeImage($this->fullfilepath, $thumb_name, true, RGALLERY_THUMB_COMPRESSION, RGALLERY_THUMB_SIZE);
		// generate tiny thumbnail
		$tthumb_name = $this->upload_dir . '/tthumb_' . $this->filepath . '.jpg';
		$retval2 = RGallerySystem::resizeImage($this->fullfilepath, $tthumb_name, true, RGALLERY_TTHUMB_COMPRESSION, RGALLERY_TTHUMB_SIZE);
		// generate preview
		$preview_name = $this->upload_dir . '/preview_' . $this->filepath . '.jpg';
		$retval3 = RGallerySystem::resizeImage($this->fullfilepath, $preview_name, false, RGALLERY_PREVIEW_COMPRESSION, RGALLERY_PREVIEW_SIZE_W, RGALLERY_PREVIEW_SIZE_H);
		// generate image
		$image_name = $this->upload_dir . '/image_' . $this->filepath . '.jpg';
		$retval4 = RGallerySystem::resizeImage($this->fullfilepath, $image_name, false, RGALLERY_IMAGE_COMPRESSION, RGALLERY_IMAGE_SIZE_W, RGALLERY_IMAGE_SIZE_H, RGALLERY_WATERMARK);
		$image_size = @getimagesize($image_name);
		// Did it work?
		if ($retval1 && $retval2 && $retval3 && $retval4) {
			$this->itemResizedSize = filesize($image_name);
			$this->itemDimW = $image_size [0];
			$this->itemDimH = $image_size [1];
			$this->itemOrigDimW = $current_size [0];
			$this->itemOrigDimH = $current_size [1];
			return true;
		} else {
			// something went wrong.. so we have to undo all our work
			// delete all the data we recently created
			@unlink($thumb_name);
			@unlink($tthumb_name);
			@unlink($preview_name);
			@unlink($image_name);
			@unlink($this->filepath);
			return false;
		}
	}

	public function getData($itemID = false) {
		if (!$itemID)
			$itemID = $this->itemID;
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
		$row ['tags'] = RGalleryItem::getElementTags($itemID);
		$row ['comments'] = RGalleryItem::getElementComments($itemID);
		$row ['commentsCount'] = count($row ['comments']);
		$owner = new UserProfile($row ['ownerID']);
		$row ['ownerName'] = $owner->username;
		$row ['itemOwner'] = $owner;
		$row ['itemOrigPath'] = RGALLERY_IMAGE_PATH . '/' . $row ['ownerID'] . '/' . $row ['itemPath'] . '.' . $row ['itemOrigExtension'];
		$row ['itemDimW_h'] = $row ['itemDimW'] / 2;
		$row ['itemDimW_h_l'] = $row ['itemDimW_h'] - 1;
		$row ['itemDimH_h'] = $row ['itemDimH'] / 2;
		$row ['itemDimH_h_l'] = $row ['itemDimH_h'] - 1;
		if (file_exists($row ['itemOrigPath']))
			$row ['hasFullsize'] = 1;
		else
			$row ['hasFullsize'] = 0;
			// get user rating
		$row ['userRating'] = $this->getUserrating();
		$row ['userRatingRound'] = round($row ['userRating'], 0);
		return $row;
	}

	private function getUserrating() {
		if (!WBBCore::getUser()->userID)
			return false;
		$sql = "SELECT avg(ratingValue) as rating FROM wcf" . WCF_N . "_rGallery_items_rating WHERE itemID='" . intval($this->itemID) . "'";
		$result = WCF::getDB()->getFirstRow($sql);
		return $result ['rating'];
	}

	public function setUserrating($rating, $itemID = false, $userID = false) {
		if (!$itemID)
			$itemID = $this->itemID;
		if (!$userID)
			$userID = WBBCore::getUser()->userID;
		$sql = "SELECT count(*) as count FROM wcf" . WCF_N . "_rGallery_items_rating WHERE itemID='" . intval($itemID) . "' AND userID='" . intval($userID) . "'";
		$count = WCF::getDB()->getFirstRow($sql);
		$count = $count ['count'];
		if ($count == 0) {
			$sql = "INSERT INTO wcf" . WCF_N . "_rGallery_items_rating
						(itemID, userID, ratingValue)
						VALUES
						('" . intval($itemID) . "', '" . intval($userID) . "', '" . intval($rating) . "')";
			return WCF::getDB()->sendQuery($sql);
		} else {
			$sql = "UPDATE wcf" . WCF_N . "_rGallery_items_rating
						SET ratingValue=" . intval($rating) . "
						WHERE itemID='" . intval($itemID) . "' AND userID='" . intval($userID) . "'
						LIMIT 1";
			return WCF::getDB()->sendQuery($sql);
		}
	}

	public function addComment($comment) {
		if (!$this->checkVariableInput($comment))
			return false;
		$sql = "INSERT INTO wcf" . WCF_N . "_rGallery_comments (commentText, commentAddedDate) VALUES ('" . $this->prepInput($comment ['commentText']) . "', '" . time() . "')";
		$result1 = WCF::getDB()->sendQuery($sql);
		$commentID = WCF::getDB()->getInsertID($result1);
		if (!$result1)
			return false;
		$sql = "INSERT INTO wcf" . WCF_N . "_rGallery_items_comment (itemID, commentID) VALUES ('" . intval($this->itemID) . "', '" . intval($commentID) . "')";
		$result2 = WCF::getDB()->sendQuery($sql);
		if (!$result2)
			return false;
		$sql = "INSERT INTO wcf" . WCF_N . "_rGallery_comments_user (commentID, userID, userName) VALUES ('" . intval($commentID) . "', '" . WBBCore::getUser()->userID . "', '" . $this->prepInput(WBBCore::getUser()->username) . "')";
		$result3 = WCF::getDB()->sendQuery($sql);
		if (!$result3)
			return false;
			// notify the owner
		$item = $this->getData();
		$to = new UserProfile($item ['ownerID'], null, null, null);
		if ($to->rgallery_notifyPN && !version_compare(PACKAGE_VERSION, "3", "<")) {
			$from_userID = WBBCore::getUser()->userID;
			$from_username = WBBCore::getUser()->username;
			if ($from_userID != $to->userID) {
				$lang = new Language($to->languageID);
				$subject = $lang->get("de.0xdefec.rgallery.pn_subject");
				$message = $lang->get("de.0xdefec.rgallery.pn_message");
				$subject = str_replace('[itemID]', $item ['itemID'], $subject);
				$message = str_replace('[itemID]', $item ['itemID'], $message);
				$message = str_replace('[itemLink]', "[url=" . RGALLERY_HTTP_PATH . "/index.php?page=RGalleryImageWrapper&itemID=" . $item ['itemID'] . "]" . $item ['itemName'] . "[/url]", $message);
				$subject = str_replace('[itemName]', $item ['itemName'], $subject);
				$message = str_replace('[itemName]', $item ['itemName'], $message);
				PMEditor::create(false, array (
						array (
								"username" => $to->username, 
								'userID' => $to->userID 
						) 
				), array (), $subject, $message, $from_userID, $from_username);
			}
		}
		return true;
	}

	public function show($type = 'image') {
		$row = $this->getData();
		if ($type == 'image')
			$itemPath = 'image_' . $row ['itemPath'] . '.jpg';
		elseif ($type == 'tthumb')
			$itemPath = 'tthumb_' . $row ['itemPath'] . '.jpg';
		elseif ($type == 'preview')
			$itemPath = 'preview_' . $row ['itemPath'] . '.jpg';
		elseif ($type == 'original')
			$itemPath = $row ['itemPath'] . '.' . $row ['itemOrigExtension'];
		else // show me the original uploaded file
			$itemPath = 'thumb_' . $row ['itemPath'] . '.jpg';
		header("Content-Type: " . $row ['itemMime']);
		header("Content-Transfer-Encoding: binary");
		header("Cache-Control: max-age=86400, must-revalidate"); // leave in cache for one day
		header("Content-Disposition: inline; filename=\"" . $itemPath . "\";");
		header("Content-Length: " . filesize($this->upload_dir . '/' . $itemPath));
		@set_time_limit(0);
		if (!file_exists($this->upload_dir . '/' . $itemPath))
			return false;
		$fp = fopen($this->upload_dir . '/' . $itemPath, 'rb');
		@fpassthru($fp);
		return true;
	}

	public function getNeighbors() {
		$add = '';
		if ($_GET ['from'] == 'user') {
			$add .= " AND io.ownerID=" . WBBCore::getUser()->userID . "";
			$current_cat = RGallerySystem::getCurrentCategorie('user_cat');
			if ($current_cat)
				$add .= " AND ic.catID='" . intval($current_cat) . "' ";
			$tag = RGallerySystem::getCurrentTag('user_tag');
			if ($tag) {
				$active_tag_id = RGallerySystem::getTagId($tag);
				$add .= " AND it.tagID='" . intval($active_tag_id) . "'";
			}
		} else {
			$current_cat = RGallerySystem::getCurrentCategorie();
			if ($current_cat)
				$add .= " AND ic.catID='" . intval($current_cat) . "' ";
			$tag = RGallerySystem::getCurrentTag();
			if ($tag) {
				$active_tag_id = RGallerySystem::getTagId($tag);
				$add .= " AND it.tagID='" . intval($active_tag_id) . "'";
			}
		}
		$sql = "SELECT i.itemID as itemID FROM `wcf" . WCF_N . "_rGallery_items` as i, " . "`wcf" . WCF_N . "_rGallery_items_cat` as ic, " . "`wcf" . WCF_N . "_rGallery_items_owner` as io " . "WHERE i.itemID=ic.itemID " . "AND i.itemID=io.itemID " . "" . $add . " " . "ORDER BY i.itemAddedDate DESC";
		$sql = "SELECT *
				FROM
				((wcf" . WCF_N . "_rGallery_items as i
					LEFT JOIN wcf" . WCF_N . "_rGallery_items_tag as it
					ON i.itemID = it.itemID)
				INNER JOIN wcf" . WCF_N . "_rGallery_items_cat as ic
				on i.itemID = ic.itemID)
				INNER JOIN wcf" . WCF_N . "_rGallery_items_owner as io
				on i.itemID = io.itemID

				" . $add . "

				GROUP BY i.itemID ORDER BY i.itemAddedDate DESC";
		$result = WCF::getDB()->sendQuery($sql);
		$total = WCF::getDB()->countRows($result);
		$current = 0;
		$counter = 0;
		$first = 0;
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!$first)
				$first = $row ['itemID'];
			$prev = $current;
			$current = $row ['itemID'];
			$counter++;
			if ($current == $this->itemID)
				break;
		}
		$next = WCF::getDB()->fetchArray($result);
		$next = $next ['itemID'];
		//$sql = "SELECT i.itemID as itemID FROM `rGallery_items` as i, `rGallery_items_cat` as ic WHERE i.itemID=ic.itemID ORDER BY i.itemAddedDate ASC LIMIT 1";
		$sql = "SELECT *
				FROM
				((wcf" . WCF_N . "_rGallery_items as i
					LEFT JOIN wcf" . WCF_N . "_rGallery_items_tag as it
					ON i.itemID = it.itemID)
				INNER JOIN wcf" . WCF_N . "_rGallery_items_cat as ic
				on i.itemID = ic.itemID)
				INNER JOIN wcf" . WCF_N . "_rGallery_items_owner as io
				on i.itemID = io.itemID

				" . $add . "

				GROUP BY i.itemID ORDER BY i.itemAddedDate ASC";
		$result = WCF::getDB()->getFirstRow($sql);
		$last = $result ['itemID'];
		return array (
				$counter, 
				$total, 
				$prev, 
				$next, 
				$first, 
				$last 
		);
	}

	public function raiseCounter($itemID = false) {
		if (!$itemID)
			$itemID = $this->itemID;
		$sql = "UPDATE wcf" . WCF_N . "_rGallery_items SET itemClicks=itemClicks+1 WHERE itemID='" . intval($itemID) . "'";
		if (WCF::getDB()->sendQuery($sql))
			return true;
		return false;
	}
}
?>