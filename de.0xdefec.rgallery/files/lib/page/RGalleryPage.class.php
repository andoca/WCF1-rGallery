<?php
require_once (WCF_DIR . 'lib/page/AbstractPage.class.php');
require_once (WCF_DIR . 'lib/page/util/menu/HeaderMenu.class.php');
require_once (WBB_DIR . 'lib/system/RGallerySystem.class.php');
HeaderMenu::setActiveMenuItem('de.0xdefec.rgallery.header.menu');

/**
 * This class provides default implementations for the Page interface.
 * This includes the call of the default event listeners for a page: construct, readParameters, assignVariables and show.
 *
 * @author	Andreas Diendorfer
 * @package	de.0xdefec.rgallery
 */
class RGalleryPage extends AbstractPage {

	/**
	 * Name of the template for the called page.
	 */
	public $templateName = 'RGalleryPage';

	/**
	 * @see Page::show()
	 */
	public function show() {
		if (!WBBCore::getUser()->getPermission('user.rgallery.canView')) {
			require_once (WCF_DIR . 'lib/system/exception/PermissionDeniedException.class.php');
			throw new PermissionDeniedException();
		}
		// assign variables
		$this->assignVariables();
		$gallery = new RGallerySystem();
		// now lets determine which page is going to be shown
		if (isset($_GET['subpage'])) {
			switch ($_GET['subpage']) {
				case "categories":
					$subpage = 'categories';
					break;
				case "tags":
					$subpage = 'tags';
					break;
				case "users":
					$subpage = 'users';
					break;
				case "stats":
					$subpage = 'stats';
					break;
				default:
					$subpage = '';
			}
		} else
			$subpage = '';
		WCF::getTPL()->assign('subpage', $subpage);
		// only get this content if the subpage is the users page - we want to list a gallery of all users with images in rgallery
		if ($subpage == 'users') {
			define('RGALLERY_USERS_PER_PAGE', RGALLERY_IMAGES_PER_PAGE);
			// get the pages we have to produce
			$sql = "SELECT distinct(wcfu.userID) as userID
					FROM wcf" . WCF_N . "_rGallery_items_owner as io,
						wcf" . WCF_N . "_user as wcfu
					WHERE
						io.ownerID = wcfu.userID
					ORDER BY wcfu.username DESC";
			$result = WCF::getDB()->sendQuery($sql);
			$count = WCF::getDB()->countRows($result);
			$pages = ceil($count / RGALLERY_USERS_PER_PAGE);
			if (isset($_GET['rGalleryPage']) && is_numeric($_GET['rGalleryPage']) && $_GET['rGalleryPage'] <= $pages && $_GET['rGalleryPage'] > 0) {
				$cur_page = $_GET['rGalleryPage'];
				$rGalleryPage = $_GET['rGalleryPage'];
			} else {
				$cur_page = 1;
				$rGalleryPage = 1;
			}
			if ($cur_page > 0)
				$cur_page--; elseif ($cur_page == 0)
				$cur_page = 1;
			$start = $cur_page * RGALLERY_USERS_PER_PAGE;
			$sql = "SELECT count(ownerID) as items, ownerID as userID
							FROM wcf" . WCF_N . "_rGallery_items_owner,
								wcf" . WCF_N . "_user as wcfu
							WHERE wcfu.userID = ownerID
							GROUP BY ownerID
							ORDER BY wcfu.username
							LIMIT $start, " . RGALLERY_USERS_PER_PAGE;
			$result = WCF::getDB()->sendQuery($sql);
			$rGalleryUsers = array();
			while ($row = WCF::getDB()->fetchArray($result)) {
				$user = new UserProfile($row['userID'], null, null, null);
				$sql = "SELECT i.itemID as itemID FROM wcf" . WCF_N . "_rGallery_items as i, wcf" . WCF_N . "_rGallery_items_owner as io WHERE io.itemID=i.itemID AND io.ownerID='" . intval($user->userID) . "' ORDER BY i.itemAddedDate DESC LIMIT 1";
				$highlight = WCF::getDB()->getFirstRow($sql);
				$rGalleryUsers[$row['userID']] = array(
					'user' => $user,
					'items' => $row['items'],
					'highlight' => $highlight['itemID']);
			}
			// assign the variables
			WCF::getTPL()->assign('rGalleryUsers', $rGalleryUsers);
			WCF::getTPL()->assign('pages', $pages);
			WCF::getTPL()->assign('cur_page', $rGalleryPage);
		} elseif ($subpage == 'stats') {
			/*** get some stats parameters
			 * parameters are:
			 * total images
			 * total MB
			 * total comments
			 * total klicks
			 * top 5 users with most images
			 * top 5 users with highest avg clicks
			 * top 5 images with most klicks
			 ***/
			$sql = "SELECT count(itemID) as totalimages FROM wcf" . WCF_N . "_rGallery_items";
			$result = WCF::getDB()->getFirstRow($sql);
			$stats['totalimages'] = $result['totalimages'];
			$sql = "SELECT sum(itemResizedSize) as totalmb FROM wcf" . WCF_N . "_rGallery_items";
			$result = WCF::getDB()->getFirstRow($sql);
			$stats['totalmb'] = $result['totalmb'];
			$sql = "SELECT count(commentID) as totalcomments FROM wcf" . WCF_N . "_rGallery_comments";
			$result = WCF::getDB()->getFirstRow($sql);
			$stats['totalcomments'] = $result['totalcomments'];
			$sql = "SELECT sum(itemClicks) as totalclicks FROM wcf" . WCF_N . "_rGallery_items";
			$result = WCF::getDB()->getFirstRow($sql);
			$stats['totalclicks'] = $result['totalclicks'];
			$sql = "SELECT 	count(i.itemID) as items,
							io.ownerID as userID
					FROM 	wcf" . WCF_N . "_rGallery_items as i,
							wcf" . WCF_N . "_rGallery_items_owner as io
					WHERE 	io.itemID=i.itemID
					GROUP BY io.ownerID
					ORDER BY items DESC
					LIMIT 5";
			$result = WCF::getDB()->sendQuery($sql);
			$stats['top5users'] = array();
			while ($row = WCF::getDB()->fetchArray($result)) {
				$stats['top5users'][] = array(
					'user' => new UserProfile($row['userID'], null, null, null),
					'items' => $row['items']);
			}
			$sql = "SELECT 	i.itemClicks as itemClicks,
							io.ownerID as userID,
							i.itemName as itemName,
							i.itemID as itemID
					FROM 	wcf" . WCF_N . "_rGallery_items as i,
							wcf" . WCF_N . "_rGallery_items_owner as io
					WHERE 	io.itemID=i.itemID
					ORDER BY itemClicks DESC
					LIMIT 5";
			$result = WCF::getDB()->sendQuery($sql);
			$stats['top5imagesclicks'] = array();
			while ($row = WCF::getDB()->fetchArray($result)) {
				$stats['top5imagesclicks'][] = array(
					'user' => new UserProfile($row['userID'], null, null, null),
					'itemClicks' => round($row['itemClicks'], 1),
					'itemName' => $row['itemName'],
					'itemID' => $row['itemID']);
			}
			$sql = "SELECT 	avg(i.itemClicks) as clicks,
							io.ownerID as userID
					FROM 	wcf" . WCF_N . "_rGallery_items as i,
							wcf" . WCF_N . "_rGallery_items_owner as io
					WHERE 	io.itemID=i.itemID
					GROUP BY io.ownerID
					ORDER BY clicks DESC
					LIMIT 5";
			$result = WCF::getDB()->sendQuery($sql);
			$stats['top5usersclicks'] = array();
			while ($row = WCF::getDB()->fetchArray($result)) {
				$stats['top5usersclicks'][] = array(
					'user' => new UserProfile($row['userID'], null, null, null),
					'clicks' => round($row['clicks'], 1));
			}
			// assign the variables
			WCF::getTPL()->assign('stats', $stats);
		}
		$getCats = $gallery->getCategories();
		$RGalleryCats_value = array();
		$RGalleryCats_name = array();
		foreach ($getCats as $idx => $value) {
			$RGalleryCats_value[] = $value['catID'];
			$RGalleryCats_name[$value['catID']] = $value['catName'];
		}
		$sql = "SELECT catID, count(*) as itemsCount FROM wcf" . WCF_N . "_rGallery_items_cat GROUP BY catID";
		$result = WCF::getDB()->sendQuery($sql);
		$RGalleryCats_items = array();
		while ($row = WCF::getDB()->fetchArray($result)) {
			$RGalleryCats_items[$row['catID']] = $row['itemsCount'];
		}
		if (!empty($_GET['rGalleryCat']))
			$gallery->setCurrentCategorie(urldecode($_GET['rGalleryCat']));
		if (!empty($_GET['tag']))
			$gallery->setCurrentTag(urldecode($_GET['tag']));
		if (isset($_GET['reset_tag']) && $_GET['reset_tag'] == 1)
			WCF::getSession()->unregister('pub_tag');
		$cat = $gallery->getCurrentCategorie();
		$tag = $gallery->getCurrentTag();
		if ($cat) {
			if (isset($getCats[$cat]['catName']))
				$current_cat = $getCats[$cat]['catName']; else {
				WCF::getSession()->unregister('pub_cat');
				$current_cat = '';
			}
		} else
			$current_cat = '';
		WCF::getTPL()->assign('current_cat', $current_cat);
		$tagCloud = $gallery->generateTagCloud(false, 'RGallery');
		if (!isset($_GET['rGalleryPage']))
			$_GET['rGalleryPage'] = 1; else {
			if (!is_numeric($_GET['rGalleryPage']))
				$_GET['rGalleryPage'] = 1;
		}
		WCF::getTPL()->assign('RGalleryCats', $getCats);
		WCF::getTPL()->assign('RGalleryCats_value', $RGalleryCats_value);
		WCF::getTPL()->assign('RGalleryCats_name', $RGalleryCats_name);
		WCF::getTPL()->assign('RGalleryCats_items', $RGalleryCats_items);
		WCF::getTPL()->assign('rGalleryCat', RGalleryItem::getCurrentCategorie());
		if ($tag) {
			if ($gallery->getTagId($tag)) {
				define("ACTIVE_TAG", $tag);
			}
		}
		$data['itemArray'] = $gallery->getItemsListing($_GET['rGalleryPage']);
		WCF::getTPL()->assign('rGalleryPage', $_GET['rGalleryPage']);
		WCF::getTPL()->assign('itemArray', $data['itemArray']);
		WCF::getTPL()->assign('totalpages', $gallery->getGalleryPages());
		WCF::getTPL()->assign('has_elements', count($data['itemArray']));
		WCF::getTPL()->assign('tagCloud', $tagCloud);
		WCF::getTPL()->assign('canUpload', WBBCore::getUser()->getPermission('user.rgallery.canUpload'));
		if (defined('ACTIVE_TAG'))
			WCF::getTPL()->assign('active_tag', ACTIVE_TAG); else
			WCF::getTPL()->assign('active_tag', '');
			// call show event
		EventHandler::fireAction($this, 'show');
		// show template
		if (!empty($this->templateName)) {
			WCF::getTPL()->display($this->templateName);
		}
	}
}
?>