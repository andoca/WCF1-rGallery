<?PHP
require_once(WBB_DIR.'lib/system/RGallerySystem.class.php');

class RGalleryRandomBox {
	protected $boxdata = array();

	public function __construct($data, $boxname = "") {
		$this->getBoxStatus($data);
		$this->boxdata['boxID'] = $data['boxID'];

		$centerboxdata = array();
		$sideboxdata = array();

		$sql = "SELECT 	i.itemClicks as itemClicks,
					io.ownerID as userID,
					i.itemName as itemName,
					i.itemID as itemID
			FROM 	wcf".WCF_N."_rGallery_items as i,
				wcf".WCF_N."_rGallery_items_owner as io
			WHERE 	io.itemID=i.itemID
			ORDER BY rand()
			LIMIT 4";

		$result = WCF::getDB()->sendQuery($sql);
		$rgallerydata = array();
		while($row = WCF::getDB()->fetchArray($result)) {
			$rgallerydata[] = array('user' => new UserProfile($row['userID'], null, null, null),
					'itemClicks'=>round($row['itemClicks'],1),
					'itemName'=>$row['itemName'],
					'itemID'=>$row['itemID']);
			}

		$items = count($rgallerydata);
		WCF::getTPL()->assign('randomitems', $items);


		if($data['boxType'] == 1) {
			$this->boxdata['templatename'] = "RGalleryRandomCenterPortalBox";
			$centerboxdata = $rgallerydata;
			WCF::getTPL()->assign('randomcenterboxdata', $centerboxdata);
		}
		if($data['boxType'] == 2 || $data['boxType'] == 3) {
			$rgallerydata = array(array_shift($rgallerydata));
			$sideboxdata = $rgallerydata;
			$this->boxdata['templatename'] = "RGalleryRandomSidePortalBox";
			WCF::getTPL()->assign('randomsideboxdata', $sideboxdata);
		}
		else return false;

		return true;
	}

	protected function getBoxStatus($data) {
		// get box status
		$this->boxdata['Status'] = 1;
		if (WBBCore::getUser()->userID) {
			$this->boxdata['Status'] = intval(WBBCore::getUser()->rgalleryrandombox);
		}
		else {
			if (WBBCore::getSession()->getVar('rgalleryrandombox') !== false) {
				$this->boxdata['Status'] = WBBCore::getSession()->getVar('rgalleryrandombox');
			}
		}
	}

	public function getData() {
		return $this->boxdata;
	}

}
?>