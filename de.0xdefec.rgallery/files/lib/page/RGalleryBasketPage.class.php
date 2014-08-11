<?php
require_once (WCF_DIR . 'lib/page/AbstractPage.class.php');
require_once (WCF_DIR . 'lib/system/session/UserSession.class.php');
require_once (WBB_DIR . 'lib/system/RGallerySystem.class.php');

/**
 * This class provides default implementations for the Page interface.
 * This includes the call of the default event listeners for a page: construct, readParameters, assignVariables and show.
 *
 * @author	Andreas Diendorfer
 * @package	de.0xdefec.rgallery
 */
class RGalleryBasketPage extends AbstractPage {

	/**
	 * @see Page::show()
	 */
	public function show() {

		function stringForJavascript($in_string) {
			$str = preg_replace("/[\r\n]/", " \\n\\\n", $in_string);
			$str = preg_replace('/"/', '\\"', $str);
			Return $str;
		}
		$images = WCF::getSession()->getVar('images');
		$id = "";
		$key = "";
		/*		$itemLine = "<div style='border: 1px solid #000;margin: 1px;float: left' onmouseover=\"ajax_showTooltip('".RELATIVE_WBB_DIR."index.php?page=RGalleryTooltip&amp;itemID=§§id§§',this);return false\"
		    	onmouseout='ajax_hideTooltip()'><img style='cursor:pointer;height:".RGALLERY_TTHUMB_SIZE."px;width:".RGALLERY_TTHUMB_SIZE."px;position:relative;' src='index.php?page=RGalleryImageWrapper&amp;itemID=§§id§§&amp;type=tthumb' alt='thumb' onclick=\"clearImage('§§key§§');\" /></div>";*/
		$itemLine = "<div style='border: 1px solid #000;margin: 1px;float: left'><img style='cursor:pointer;height:" . RGALLERY_TTHUMB_SIZE . "px;width:" . RGALLERY_TTHUMB_SIZE . "px;position:relative;' src='index.php?page=RGalleryImageWrapper&amp;itemID=§§id§§&amp;type=tthumb" . SID_ARG_2ND . "' alt='thumb' onclick=\"clearImage('§§key§§');\" /></div>";
		if (is_array($images)) {
			$delete = '';
			foreach ($images as $key => $value) {
				$id = str_replace('rG_item_', '', $key);
				$delete .= '-' . $id . '-';
			}
			$delete = str_replace('-', '', str_replace('--', ', ', $delete));
		}
		if (isset($_GET['delete_link'])) {
			if (count($images) != 0) {
				$language = new Language(WBBCore::getUser()->langaugeID);
				$var = $language->get('de.0xdefec.rgallery.delete_elements');
				$deleteform = '<div style="clear: both;margin: 4px;text-align: center">
					<form action="index.php?page=RGalleryUser" method="post" onsubmit="return AIM.submit(this, { \'onStart\' : startCallback, \'onComplete\' : generate_item_listening })">
						<input type="hidden" name="itemsDelete" value="' . $delete . '" />
						<input type="hidden" name="action" value="itemDelete" />
						<input type="submit" value="' . $var . '" />
						' . SID_INPUT_TAG . '
					</form>
				</div>';
				echo $deleteform;
			}
			die();
		}
		if (isset($_GET['clearImage'])) {
			unset($images[$_GET['id']]);
			WCF::getSession()->register('images', $images);
			// 			sleep(.5);
			foreach ($images as $key => $value) {
				$id = str_replace('rG_item_', '', $key);
				if ($value == 1)
					echo str_replace("§§id§§", $id, str_replace("§§key§§", $key, $itemLine));
			}
			die();
		}
		if (isset($_GET['loadWaste'])) {
			// 			sleep(.5);
			if (is_array($images)) {
				foreach ($images as $key => $value) {
					$id = str_replace('rG_item_', '', $key);
					if ($value == 1)
						echo str_replace("§§id§§", $id, str_replace("§§key§§", $key, $itemLine));
				}
			}
			die();
		}
		if (isset($_GET['clear'])) {
			$images = array();
			WCF::getSession()->unregister('images');
			// 			sleep(.5);
			die();
		}
		if (empty($images))
			$images = array();
		$images[$_GET['image_id']] = 1;
		foreach ($images as $key => $value) {
			$id = str_replace('rG_item_', '', $key);
			if ($value == 1)
				echo str_replace("§§id§§", $id, str_replace("§§key§§", $key, $itemLine));
		}
		$images = WCF::getSession()->register('images', $images);
		// 		sleep(.5);
	}
}
?>