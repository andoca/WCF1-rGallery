<?php
require_once (WCF_DIR . 'lib/data/message/bbcode/BBCodeParser.class.php');
require_once (WCF_DIR . 'lib/data/message/bbcode/BBCode.class.php');

/**
 * Parses the [gallery] bbcode tag.
 *
 * @author	Andreas Diendorfer
 * @package	de.0xdefec.rgallery
 */
class RGalleryBBCode implements BBCode {
	/**
	 * @see BBCode::getParsedTag()
	 */
	public function getParsedTag($openingTag, $content, $closingTag, BBCodeParser $parser) {

		/**
		 * Get WBB constants if needed
		 * Credits go to madjoe
		 */
		if (! defined('RELATIVE_WBB_DIR')) {
			$sql = "SELECT packageDir FROM wcf" . WCF_N . "_package WHERE package='com.woltlab.wbb'";
			$result = WCF::getDB()->getFirstRow($sql);
			$TMPDIR = $result['packageDir'];
			define('RELATIVE_WBB_DIR', $TMPDIR);
		}

		if (! defined('RGALLERY_THUMB_SIZE')) {
			$configfile_array = file(constant('RELATIVE_WBB_DIR') . "options.inc.php");
			$configfile_length = count($configfile_array);
			$suchstring = "*RGALLERY_THUMB_SIZE*";

			for($zeile = 0; $zeile <= $configfile_length; $zeile ++) {
				if (preg_match($suchstring, $configfile_array[$zeile])) {
					$result = $configfile_array[$zeile];
					break;
				}
			}

			$result = substr($result, strpos($result, ','));
			$result = str_replace("');", "", $result);
			$result = str_replace(", '", "", $result);
			define('RGALLERY_THUMB_SIZE', $result);
		}
		// Constant fetch end


		if ($parser->getOutputType() == 'text/html') {
			// show template
			WCF::getTPL()->assign(array(

					'content'=>intval($content)
			));
			return WCF::getTPL()->fetch('RGalleryBBCodeTag');
		}
		else if ($parser->getOutputType() == 'text/plain') {
			return $content;
		}
	}
}
?>