<?PHP
require_once(WBB_DIR.'lib/system/RGallerySystem.class.php');

class RGalleryBox {
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
			ORDER BY i.itemAddedDate DESC
			LIMIT 5";
				
		$result = WCF::getDB()->sendQuery($sql);
		$rgallerydata = array();
		while($row = WCF::getDB()->fetchArray($result)) {
			$rgallerydata[] = array('user' => new UserProfile($row['userID'], null, null, null),
					'itemClicks'=>round($row['itemClicks'],1),
					'itemName'=>$row['itemName'],
					'itemID'=>$row['itemID']);
			}
		
		$items = count($rgallerydata);
		WCF::getTPL()->assign('items', $items);
		
		
		if($data['boxType'] == 1) {
			$this->boxdata['templatename'] = "RGalleryCenterPortalBox";
			$centerboxdata = $rgallerydata;
			WCF::getTPL()->assign('centerboxdata', $centerboxdata);
		}
		if($data['boxType'] == 2 || $data['boxType'] == 3) {
			$rgallerydata = array(array_shift($rgallerydata));
			$sideboxdata = $rgallerydata;
			$this->boxdata['templatename'] = "RGallerySidePortalBox";
			WCF::getTPL()->assign('sideboxdata', $sideboxdata);
		}
		else return false;
		
		return true;
	}

	protected function getBoxStatus($data) {
		// get box status
		$this->boxdata['Status'] = 1;
		if (WBBCore::getUser()->userID) {
			$this->boxdata['Status'] = intval(WBBCore::getUser()->rgallerybox);
		}
		else {
			if (WBBCore::getSession()->getVar('rgallerybox') !== false) {
				$this->boxdata['Status'] = WBBCore::getSession()->getVar('rgallerybox');
			}
		}
	}

	public function getData() {
		return $this->boxdata;
	}

}
?>