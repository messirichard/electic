<?php
/**
 * @package Addon Library
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('ADDON_LIBRARY_INC') or die('Restricted access');


class UniteCreatorAddons extends UniteElementsBaseUC{
	
	const STATE_FILTER_ACTIVE = "fitler_active_addons";	
	const STATE_LAST_ADDONS_CATEGORY = "last_addons_cat";
	
	
	protected function ___________STATIC_METHODS__________(){}
	
	/**
	 * set filter active state
	 */
	public static function setStateFilterActive($filterActive){
		
		if(!empty($filterActive))
			HelperUC::setState(self::STATE_FILTER_ACTIVE, $filterActive);
		
	}
	
	/**
	 * get filter active statge
	 */
	public static function getStateFilterActive(){
		$filterActive = HelperUC::getState(self::STATE_FILTER_ACTIVE);
		
		return($filterActive);
	}
	
	
	/**
	 * get active filter where string
	 */
	public static function getFilterActiveWhere($filterActive = null, $prefix = null){
		
		if($filterActive === null)
			$filterActive = self::getStateFilterActive();
		
		$where = "";
		
		//set active fitler where
		switch($filterActive){
			case "active":
				$where = "is_active=1";
				break;
			case "not_active":
				$where = "is_active=0";
				break;
		}
		
		if(!empty($where) && !empty($prefix))
			$where = $prefix.".".$where;
		
		return($where);
	}
	
	
	protected function ___________GETTERS__________(){}
	
	
	/**
	 *
	 * get html of cate items
	 */
	public function getCatAddonsHtml($catID, $filterActive=null, $type=null){
		
		$addons = $this->getCatAddons($catID, false, $filterActive, $type);
		
		$htmlAddons = "";
		
		$objManager = new UniteCreatorManagerAddons();
		
		foreach($addons as $addon){
			
			$html = $objManager->getAddonAdminHtml($addon);
			$htmlAddons .= $html;
		}
	
		return($htmlAddons);
	}

	
	/**
	 *
	 * get items by id's
	 */
	private function getAddonsByIDs($addonIDs){
		$strAddons = implode(",", $addonIDs);
		$tableAddons = GlobalsUC::$table_addons;
		$sql = "select * from {$tableAddons} where id in({$strAddons})";
		$arrAddons = $this->db->fetchSql($sql);
	
		return($arrAddons);
	}
	
	
	/**
	 *
	 * get html of categories and items.
	 */
	private function getCatsAndAddonsHtml($catID, $type){
		
		$filterActive = self::getStateFilterActive();
		
		$htmlAddons = $this->getCatAddonsHtml($catID, $filterActive, $type);
		
		$objCats = new UniteCreatorCategories();
		$htmlCatList = $objCats->getHtmlCatList($catID, $type);
		
		$response = array();
		$response["htmlItems"] = $htmlAddons;
		$response["htmlCats"] = $htmlCatList;
	
		return($response);
	}
	
	
	/**
	 *
	 * get layouts array
	 */
	public function getArrAddonsShort($order = ""){
		
		$where = "";
		
		$response = $this->db->fetch(GlobalsUc::$table_addons, $where, $order);
		
		return($response);
	}
	
	
	/**
	 *
	 * get addons array
	 */
	public function getArrAddons($order = ""){
		
		$response = $this->getArrAddonsShort($order);
		
		$arrAddons = array();
		foreach($response as $record){
			$objAddon = new UniteCreatorAddon();
			$objAddon->initByDBRecord($record);
			$arrAddons[] = $objAddon;
		}
		
		return($arrAddons);
	}
	
	
	/**
	 * get sql addon type for quest string
	 */
	private function getSqlAddonType($addonType){
		
		$addonType = $this->db->escape($addonType);
		if(empty($addonType)){
			$where = "(addontype='' or addontype is null)";
		}else
			$where = "addontype='{$addonType}'";
		
		return($where);
	}
	
	
	/**
	 *
	 * get category items
	 */
	public function getCatAddons($catID, $isShort = false, $filterActive = null, $addonType = null){
		
		$arrWhere = array();
		
		if(is_numeric($catID))
			$catID = (int)$catID;
		
		if($catID === null)
			$catID = "all";
		
		//get catID where
		if($catID === "all"){
			$arrWhere = array();
		}
		else if(is_numeric($catID)){
			$catID = (int)$catID;
			$arrWhere[] = "catid=$catID";
		}
		else{			//multiple - array of id's
						
			if(is_array($catID) == false)
				UniteFunctionsUC::throwError("catIDs could be array or number");
			
			$strCats = implode(",", $catID);
			$strCats = $this->db->escape($strCats);		//for any case
			$arrWhere[] = "catid in($strCats)";
		}
				
		$whereFilterActive = self::getFilterActiveWhere($filterActive);
		if(!empty($whereFilterActive))
			$arrWhere[] = $whereFilterActive;
		
		//set addon type
		if($addonType !== null){
			
			$arrWhere[] = $this->getSqlAddonType($addonType);
		}
		
		$where = "";
		if(!empty($arrWhere))
			$where = implode($arrWhere," and ");
		
		
		$records = $this->db->fetch(GlobalsUC::$table_addons, $where, "catid, ordering");
				
		$arrAddons = array();
		foreach($records as $record){
						
			$objAddon = new UniteCreatorAddon();
			$objAddon->initByDBRecord($record);
			
			
			if($isShort == true){
				$arrAddons[] = $objAddon->getArrShort();
				
			}else{
				$arrAddons[] = $objAddon;
			}
		}
		
		
		return($arrAddons);
	}
	
	
	/**
	 * get addons by categories
	 * $publishedCatOnly - get only from published ones
	 */
	public function getAddonsWidthCategories($publishedCatOnly = true, $isShort = false, $type = ""){
		
		$objCats = new UniteCreatorCategories();
		$arrCats = $objCats->getCatsShort("all_uncat",$type);
		$arrIDs = array_keys($arrCats);
		
		
		//prepare structure
		foreach($arrCats as $catID=>$title){
			$cat = array();
			$cat["title"] = $title;
			$cat["addons"] = array();
			
			$arrCats[$catID] = $cat;
		}
		
		$filterActive = null;
		if($publishedCatOnly == true)
			$filterActive = "active";
		
		
		$arrAdons = array();
		if(!empty($arrCats))
			$arrAdons = $this->getCatAddons(null, false, $filterActive, $type);
		
		
		//put addons to category
		foreach($arrAdons as $addon){
			
			$addonCatID = $addon->getCatID();
			
			if($isShort == true)
				$addonForInsert = $addon->getArrShort();
			else
				$addonForInsert = $addon;
			
			$insertKey = $addonCatID;
			if(array_key_exists($addonCatID, $arrCats) == false)
				$insertKey = 0;
			
			$arrCats[$insertKey]["addons"][] = $addonForInsert;
			
			//add to all
			$arrCats["all"]["addons"][] = $addonForInsert;
			
		}
		
		return($arrCats);
	}
	
	
	/**
	 * get addons with categories by comfortable format
	 */
	public function getAddonsWidthCategoriesShort($publishedCatOnly = true, $type){
		
		$arrCats = $this->getAddonsWidthCategories($publishedCatOnly, true, $type);
		
		return $arrCats;
	}
	
	
	/**
	 * check if addon exists by name
	 */
	public function isAddonExistsByName($name){
	
		$response = $this->db->fetch(GlobalsUC::$table_addons,"name='{$name}'");
	
		return(!empty($response));
	}
	
	
	/**
	 * get addon type from data
	 */
	protected function getAddonTypeFromData($data){
		
		$type = UniteFunctionsUC::getVal($data, "addontype");
		
		if(empty($type))
			$type = UniteFunctionsUC::getVal($data, "type");
		
		HelperUC::runProviderFunc("validateDataAddonsType", $type, $data);
		
		return($type);
	}
	
	
	/**
	 *
	 * get category items html
	 */
	public function getCatAddonsHtmlFromData($data){
		$catID = UniteFunctionsUC::getVal($data, "catID");
		
		$type = $this->getAddonTypeFromData($data);
				
		
		if($catID != "all")
			UniteFunctionsUC::validateNumeric($catID,"category id");
		
		$resonseCombo = UniteFunctionsUC::getVal($data, "response_combo");
		$resonseCombo = UniteFunctionsUC::strToBool($resonseCombo);
		
		$filterActive = UniteFunctionsUC::getVal($data, "filter_active");
		
		self::setStateFilterActive($filterActive);
		HelperUC::setState(self::STATE_LAST_ADDONS_CATEGORY, $catID);
		
		
		if($resonseCombo == true){
			
			$response = $this->getCatsAndAddonsHtml($catID, $type);
						
		}else{
			$itemsHtml = $this->getCatAddonsHtml($catID, $filterActive, $type);
			$response = array("itemsHtml"=>$itemsHtml);
		}
		
		return($response);
	}
	
	
	
	/**
	 *
	 * get max order from categories list
	 */
	public function getMaxOrder($catID){
	
		UniteFunctionsUC::validateNotEmpty($catID,"category id");
	
		$tableAddons = GlobalsUC::$table_addons;
		$query = "select MAX(ordering) as maxorder from {$tableAddons} where catid={$catID}";
	
		$rows = $this->db->fetchSql($query);
	
		$maxOrder = 0;
		if(count($rows)>0) 
			$maxOrder = $rows[0]["maxorder"];
		
		if(!is_numeric($maxOrder))
			$maxOrder = 0;
	
		return($maxOrder);
	}
	
	
	/**
	 * get number of addons by category
	 */
	public function getNumAddons($catID=null, $filterActive = null, $addonType = null){
		
		$tableAddons = GlobalsUC::$table_addons;
		
		$arrWhere = array();
		if(!empty($filterActive)){
			$whereActive = UniteCreatorAddons::getFilterActiveWhere($filterActive,"a");
			if(!empty($whereActive))
				$arrWhere[] = $whereActive;
		}
		
		if($addonType !== null){
			$arrWhere[] = $this->getSqlAddonType($addonType);
		}
		
		//all addons
		if($catID === null){
			$query = "select count(*) as num_addons from {$tableAddons}";
		}
		else{
			$query = "select count(*) as num_addons from {$tableAddons} as a";
			$arrWhere[] = "a.catid=$catID";
		}
		
		
		if(!empty($arrWhere))
			$query .= " where ".implode($arrWhere," and ");
		
		
		$response = $this->db->fetchSql($query);
		
		if(empty($response))
			UniteFunctionsUC::throwError("Can't get number of zero addons");
	
		$numAddons = $response[0]["num_addons"];
		
		return($numAddons);
	}
	
	
	/**
	 * get item settings html
	 */
	public function getAddonItemsSettingsHTMLFromData($data){
		
		$addonID = UniteFunctionsUC::getVal($data, "addonid");
		
		$addon = new UniteCreatorAddon();
		$addon->initByID($addonID);
		
		$html = $addon->getHtmlItemConfig();
		
		return($html);
	}
	
	
	/**
	 * check if needed helper editor on admin addon output
	 */
	public function isHelperEditorNeeded(UniteCreatorAddon $addon){
		
		$hasItems = $addon->isHasItems();
		if($hasItems == false)
			return(false);
		
		$isItemsEditorExists = $addon->isEditorItemsAttributeExists();
		if($isItemsEditorExists == false)
			return(false);
		
		$isMainEditorExists = $addon->isEditorMainAttributeExists();
		if($isMainEditorExists == true)
			return(false);
		
		return(true);
	}
	
	
	protected function ___________SETTERS__________(){}
	
	/**
	 *
	 * delete addons
	 */
	private function deleteAddons($arrAddons){
	
		//sanitize
		foreach($arrAddons as $key=>$itemID)
			$arrAddons[$key] = (int)$itemID;
	
		$strAddonIDs = implode($arrAddons,",");
		$this->db->delete(GlobalsUC::$table_addons,"id in($strAddonIDs)");
	}
	
	/**
	 *
	 * save items order
	 */
	private function saveAddonsOrder($arrAddonIDs){
	
		//get items assoc
		$arrAddons = $this->getAddonsByIDs($arrAddonIDs);
		$arrAddons = UniteFunctionsUC::arrayToAssoc($arrAddons,"id");
	
		$order = 0;
		foreach($arrAddonIDs as $addonID){
			$order++;
	
			$arrAddon = UniteFunctionsUC::getVal($arrAddons, $addonID);
			if(!empty($arrAddon) && $arrAddon["ordering"] == $order)
				continue;
	
			$arrUpdate = array();
			$arrUpdate["ordering"] = $order;
			$this->db->update(GlobalsUC::$table_addons, $arrUpdate, array("id"=>$addonID));
		}
	
	}
	
	/**
	 *
	 * copy items to some category
	 */
	private function copyAddons($arrAddonIDs,$catID){
		$category = new UniteCreatorCategories();
		$category->validateCatExist($catID);
	
		foreach($arrAddonIDs as $addonID){
			$this->copyAddon($addonID, $catID);
		}
	}
	
	/**
	 *
	 * move multiple items to some category
	 */
	private function moveAddons($arrAddonIDs, $catID){
		$category = new UniteCreatorCategories();
		$category->validateCatExist($catID);
	
		foreach($arrAddonIDs as $addonID){
			$this->moveAddon($addonID, $catID);
		}
	}
	
	
	
	/**
	 *
	 * move addons to some category by change category id
	 */
	private function moveAddon($addonID, $catID){
		$addonID = (int)$addonID;
		$catID = (int)$catID;
	
		$arrUpdate = array();
		$arrUpdate["catid"] = $catID;
		$this->db->update(GlobalsUC::$table_addons, $arrUpdate, array("id"=>$addonID));
	}
	
	/**
	 *
	 * duplciate addons within same category
	 */
	private function duplicateAddons($arrAddonIDs, $catID){
	
		foreach($arrAddonIDs as $addonID){
			$addon = new UniteCreatorAddon();
			$addon->initByID($addonID);
			$addon->duplicate();
		}
	
	}
	
	
	/**
	 * create addon from data
	 */
	public function createFromData($data){
	
		$objAddon = new UniteCreatorAddon();
		$id = $objAddon->add($data);
	
		return($id);
	}
	
	
	/**
	 * create addon from manager
	 */
	public function createFromManager($data){
		
		$objAddon = new UniteCreatorAddon();
		
		$title = UniteFunctionsUC::getVal($data, "title");
		$name = UniteFunctionsUC::getVal($data, "name");
		$description = UniteFunctionsUC::getVal($data, "description");
		$catID = UniteFunctionsUC::getVal($data, "catid");
		$type = $this->getAddonTypeFromData($data);
		
		$newAddonID = $objAddon->addSmall($title, $name, $description, $catID, $type);
		
		$objManager = new UniteCreatorManagerAddons();
		
		$htmlItem = $objManager->getAddonAdminHtml($objAddon);
		
		$objCats = new UniteCreatorCategories();
		$htmlCatList = $objCats->getHtmlCatList($catID, $type);
		
		$output = array();
		$output["htmlItem"] = $htmlItem;
		$output["htmlCats"] = $htmlCatList;
		$output["url_addon"] = HelperUC::getViewUrl_EditAddon($newAddonID);
		
		return($output);
	}
	
	
	/**
	 * update addon from data
	 */
	public function updateAddonFromData($data){
		
		$addonID = UniteFunctionsUC::getVal($data, "id");
		
		$objAddon = new UniteCreatorAddon();
		$objAddon->initByID($addonID);
		$objAddon->update($data);
	}

	
	/**
	 * duplicate addon from data
	 */
	public function duplicateAddonFromData($data){

		$addonID = UniteFunctionsUC::getVal($data, "addonID");
		
		$objAddon = new UniteCreatorAddon();
		$objAddon->initByID($addonID);
		
		$response = $objAddon->duplicate(true);
		
		$htmlRow = HelperHtmlUC::getTableAddonsRow($response["id"], $response["title"]);
		
		return($htmlRow);
	}
	
	
	/**
	 * import addon from library
	 */
	public function importAddonFromLibrary($data){
		
		$path = UniteFunctionsUC::getVal($data, "path");
		if(empty($path))
			UniteFunctionsUC::throwError("Empty Path");
		
		$library = new UniteCreatorLibrary();
		$addonData = $library->getPluginDataByPath($path);
		
		$objAddon = new UniteCreatorAddon();
		$addonID = $objAddon->add($addonData);
		$title = $objAddon->getTitle(true);
		
		$htmlRow = HelperHtmlUC::getTableAddonsRow($addonID, $title);
		
		return($htmlRow);
	}
	
	
	/**
	 * delete addon from imput data
	 */
	public function deleteAddonFromData($data){
		
		$addonID = UniteFunctionsUC::getVal($data, "addonID");
		UniteFunctionsUC::validateNotEmpty($addonID, "Addon ID");
		
		$this->db->delete(GlobalsUC::$table_addons, "id=$addonID");
		
	}
	
	
	/**
	 * update item title
	 */
	public function updateAddonTitleFromData($data){
		
		$itemID = $data["itemID"];
		$title = $data["title"];
		$name = $data["name"];
		$description = $data["description"];
		
		$addon = new UniteCreatorAddon();
		$addon->initByID($itemID);
		$addon->updateNameTitle($name, $title, $description);
		
	}
	
	
	/**
	 * update items activation from data
	 * @param $data
	 */
	public function activateAddonsFromData($data){
		$arrIDs = UniteFunctionsUC::getVal($data, "addons_ids");
		if(is_array($arrIDs) == false)
			return(false);
		
		if(empty($arrIDs))
			return(fale);
		
		$strIDs = implode($arrIDs,",");
		
		UniteFunctionsUC::validateIDsList($strIDs,"id's list");
		
		$isActive = UniteFunctionsUC::getVal($data, "is_active");
		$isActive = (int)UniteFunctionsUC::strToBool($isActive);
		
		$tableAddons = GlobalsUC::$table_addons;
		$query = "update {$tableAddons} set is_active={$isActive} where id in($strIDs)";
		
		$this->db->runSql($query);
			
	}
	
	
	/**
	 * remove items from data
	 */
	public function removeAddonsFromData($data){
	
		$catID = UniteFunctionsUC::getVal($data, "catid");
		$type = $this->getAddonTypeFromData($data);
		
		$addonsIDs = UniteFunctionsUC::getVal($data, "arrAddonsIDs");
		
		$this->deleteAddons($addonsIDs);
		
		$response = $this->getCatsAndAddonsHtml($catID, $type);
	
		return($response);
	}
	
	
	
	
	
	
	/**
	 *
	 * save items order from data
	 */
	public function saveOrderFromData($data){
		$addonsIDs = UniteFunctionsUC::getVal($data, "addons_order");
		if(empty($addonsIDs))
			return(false);
	
		$this->saveAddonsOrder($addonsIDs);
	}

	
	/**
	 *
	 * copy / move addons to some category
	 * @param $data
	 */
	public function moveAddonsFromData($data){
		
		$targetCatID = UniteFunctionsUC::getVal($data, "targetCatID");
		$selectedCatID = UniteFunctionsUC::getVal($data, "selectedCatID");
		$type = $this->getAddonTypeFromData($data);
		
		$arrAddonIDs = UniteFunctionsUC::getVal($data, "arrAddonIDs");
	
		UniteFunctionsUC::validateNotEmpty($targetCatID,"category id");
		UniteFunctionsUC::validateNotEmpty($arrAddonIDs,"addon id's");
		
		$this->moveAddons($arrAddonIDs, $targetCatID);
	
		$repsonse = $this->getCatsAndAddonsHtml($selectedCatID, $type);
		return($repsonse);
	}
	
	
	/**
	 * duplicate items
	 */
	public function duplicateAddonsFromData($data){
	
		$catID = UniteFunctionsUC::getVal($data, "catID");
		$arrIDs = UniteFunctionsUC::getVal($data, "arrIDs");
		$type = $this->getAddonTypeFromData($data);
				
		$this->duplicateAddons($arrIDs, $catID);
	
		$response = $this->getCatsAndAddonsHtml($catID, $type);
	
		return($response);
	}
	
	
	/**
	 * shift addons in category from some order (more then the order).
	 */
	public function shiftOrder($catID, $order){
		
		$tableAddons = GlobalsUC::$table_addons;
		
		$query = "update $tableAddons set ordering = ordering+1 where catid={$catID} and ordering > {$order}";
		
		$this->db->runSql($query);
	}
	
	
	/**
	 * init addon by data
	 */
	public function initAddonByData($data){
		
		if(is_string($data)){
			$data = json_decode($data);
			$data = UniteFunctionsUC::convertStdClassToArray($data);
		}
				
		$addonID = UniteFunctionsUC::getVal($data, "id");
		$addonName = UniteFunctionsUC::getVal($data, "name");
		$arrConfig = UniteFunctionsUC::getVal($data, "config");
		$arrItemsData = UniteFunctionsUC::getVal($data, "items");
		$addonType = UniteFunctionsUC::getVal($data, "addontype");
		
		
		$objAddon = new UniteCreatorAddon();
		
		if(!empty($addonID))
			$objAddon->initByID($addonID);
		else{
			if(!empty($addonType))
				$objAddon->initByAlias($addonName, $addonType);
			else
				$objAddon->initByName($addonName);
		}
		
		if(is_array($arrConfig)){
			$objAddon->setParamsValues($arrConfig);
		}
		
		if(is_array($arrItemsData))
			$objAddon->setArrItems($arrItemsData);
		
		return($objAddon);
	}
	
	
	/**
	 * get addon config html by data
	 */
	public function getAddonConfigHTML($data){
				
		$objAddon = $this->initAddonByData($data);
		
		//init addon config
		$addonConfig = new UniteCreatorAddonConfig();
		$addonConfig->setStartAddon($objAddon);
		$addonConfig->setViewTabs();
		
		$html = $addonConfig->getHtmlFrame();
		
		return($html);
	}
	
	
	/**
	 * show preview by data
	 */
	public function showAddonPreviewFromData($data){
		
		try{
			$objAddon = $this->initAddonByData($data);
			
			$objOutput = new UniteCreatorOutput();
			$objOutput->initByAddon($objAddon);
			
			$html = $objOutput->getPreviewHtml();
			
			echo $html;
			
		}catch(Exception $e){
			$message = $e->getMessage();
			$errorMessage = HelperUC::getHtmlErrorMessage($message);
			echo $errorMessage;
		}
		
		exit();
	}
	
	
	/**
	 * save test addon data to some slot
	 */
	public function saveTestAddonData($data){
		
		$addonName = UniteFunctionsUC::getVal($data, "name");
		
		$config = UniteFunctionsUC::getVal($data, "config", array());
		$items = UniteFunctionsUC::getVal($data, "items", array());
		
		$objAddon = new UniteCreatorAddon();
		$objAddon->initByName($addonName);
		$objAddon->saveTestSlotData(1, $config, $items);
		
	}	
	
	
	/**
	 * get test addon data
	 * @param $data
	 */
	public function getTestAddonData($data){
		$objAddon = $this->initAddonByData($data);
		$slotNum = UniteFunctionsUC::getVal($data, "slotnum");
		
		$data = $objAddon->getTestData($slotNum);
		
		return($data);
	}
	
	/**
	 * delete test addon data
	 * @param $data
	 */
	public function deleteTestAddonData($data){
		$objAddon = $this->initAddonByData($data);
		$slotNum = UniteFunctionsUC::getVal($data, "slotnum");
		
		$objAddon->clearTestDataSlot($slotNum);
	}
	
	
	/**
	 * export addon
	 */
	public function exportAddon($data){
		
		try{
			$addon = $this->initAddonByData($data);
			$exporter = new UniteCreatorExporter();
			$exporter->initByAddon($addon);
			$exporter->export();
			
		}catch(Exception $e){
			$message = "Export addon error: " . $e->getMessage();
			echo $message;
		}
		
		$message = "Export addon error: addon not exported"; 
		echo $message;
		exit();
		
	}
	
	
	/**
	 * export category addons
	 */
	public function exportCatAddons($data, $exportType=""){
		
		try{
			$catID = UniteFunctionsUC::getVal($data, "catid");
			UniteFunctionsUC::validateNotEmpty($catID);
			
			$objCats = new UniteCreatorCategories();
			$objCats->validateCatExist($catID);
			
			$exporter = new UniteCreatorExporter();
			$exporter->exportCatAddons($catID, $exportType);
			
		}catch(Exception $e){
			$message = "Export category addons error: " . $e->getMessage();
			echo $message;
		}
		
		$message = "Export category addons error: addons not exported";
		echo $message;
		exit();
		
	}
	
	/**
	 * import addons
	 */
	public function importAddons($data){
				
		$catID = UniteFunctionsUC::getVal($data, "catid");
		$addonType = $this->getAddonTypeFromData($data);
		$isOverwrite = UniteFunctionsUC::getVal($data, "isoverwrite");
		$isOverwrite = UniteFunctionsUC::strToBool($isOverwrite);
		
		if(empty($catID))
			$catID = null;
		
		$arrTempFile = UniteFunctionsUC::getVal($_FILES, "import_addon");
		$exporter = new UniteCreatorExporter();
		$exporter->setMustImportAddonType($addonType);
		$importLog = $exporter->import($catID, $arrTempFile, $isOverwrite);
		
		$response = $this->getCatsAndAddonsHtml($catID, $addonType);
		$response["import_log"] = $importLog;
		
		return($response);
	}
	
	
}

?>