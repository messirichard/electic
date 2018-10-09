<?php

/**
 * @package Addon Library
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('ADDON_LIBRARY_INC') or die('Restricted access');

class UniteCreatorLayoutWork extends UniteElementsBaseUC{
	
	protected $id;
	protected $title, $data, $gridData, $ordering, $gridDataFull, $record;
	protected static $arrCacheAddons = array();
	protected $addonType = null;
	
	
	const FIELDS_LAYOUTS = "title,ordering,layout_data";
	const LAYOUTS_GLOBAL_SETTINGS_OPTION = "unitecreator_layouts_general_settings";
	
	
	/**
	 * construct the layout
	 */
	public function __construct(){
		parent::__construct();
		
	}
	
	
	/**
	 * validate that the layout is inited
	 */
	public function validateInited(){
		
		if(empty($this->id))
			UniteFunctionsUC::throwError("The layout is not inited");
	}
	
	
	/**
	 * check if the layout inited
	 */
	public function isInited(){
		
		if(!empty($this->id))
			return(true);
		
		return(false);
	}
	
	private function a__________STATIC_FUNCTIONS___________(){}
	
	/**
	 * get stored values of grid options
	 */
	public static function getGridGlobalStoredOptions(){
		$arrValues = UniteProviderFunctionsUC::getOption(self::LAYOUTS_GLOBAL_SETTINGS_OPTION, array());
		
		return($arrValues);
	}
	
	
	/**
	 * get settings object
	 */
	public static function getGlobalSettingsObject($includeGlobalOptions = true){
		
		$filepathSettings = GlobalsUC::$pathSettings."layouts_global_settings.xml";
		
		$objSettings = new UniteCreatorSettings();
		$objSettings->loadXMLFile($filepathSettings);
		
		if($includeGlobalOptions == true){
			$arrValues = self::getGridGlobalStoredOptions();
			
			if(!empty($arrValues))
				$objSettings->setStoredValues($arrValues);
		}
		
		return($objSettings);
	}
	
	
	/**
	 * get grid default options - without loading values
	 */
	public static function getGridDefaultOptions(){
		
		$objGlobalSettings = self::getGlobalSettingsObject(false);
		$arrValuesGrid = self::getGridSettingsOptions();
		
		$arrValuesGlobal = $objGlobalSettings->getArrValues();
		
		$arrMerged = array_merge($arrValuesGlobal, $arrValuesGrid);
		
		return($arrMerged);
	}
	
	
	/**
	 * get global values - together with default
	 */
	public static function getGridGlobalOptions(){
		
		$settings = self::getGlobalSettingsObject();
		
		$arrValues = $settings->getArrValues();
		
		return($arrValues);
	}
	
	
	/**
	 * get grid settings object
	 */
	public static function getGridSettingsObject(){
	
		$filepathSettings = GlobalsUC::$pathSettings."layouts_grid_settings.xml";
	
		$objSettings = new UniteCreatorSettings();
		$objSettings->loadXMLFile($filepathSettings);
	
		return($objSettings);
	}
	
	
	/**
	 * get grid settings options
	 */
	public static function getGridSettingsOptions($arrInitValues = array()){
	
		$objSettings = self::getGridSettingsObject();
	
		if(!empty($arrInitValues))
			$objSettings->setStoredValues($arrInitValues);
		
		$arrValues = $objSettings->getArrValues();
		
		return($arrValues);
	}
	
	
	/**
	 * update general settings
	 */
	public static function updateLayoutGlobalSettingsFromData($data){
	
		$arrValues = UniteFunctionsUC::getVal($data, "settings_values");
		
		UniteProviderFunctionsUC::updateOption(self::LAYOUTS_GLOBAL_SETTINGS_OPTION, $arrValues);
	}
	
	
	
	/**
	 * init layout by id
	 */
	public function initByID($id){
		
		$id = (int)$id;
		if(empty($id))
			UniteFunctionsUC::throwError("Empty layout ID");
		
		$options = array();
		$options["tableName"] = GlobalsUC::$table_layouts;
		$options["where"] = "id=".$id;
		$options["errorEmpty"] = "layout with id: {$id} not found";
				
		$record = $this->db->fetchSingle($options);
		
		$this->id = $id;
		
		$this->initByRecord($record);
	}
	
	private function a__________SOME_METHODS___________(){}
	
	
	/**
	 * init layout by record
	 */
	protected function initByRecord($record){
		
		$this->title = UniteFunctionsUC::getVal($record, "title");
		$this->ordering = UniteFunctionsUC::getVal($record, "ordering");
		
		$data = UniteFunctionsUC::getVal($record, "layout_data");
		$data = UniteFunctionsUC::maybeUnserialize($data);
		
		$this->record = $record;
		$this->data = $data;
		
		$gridData = UniteFunctionsUC::getVal($data, "grid_data");
		
		$this->gridData = $gridData;
		
	}
	
	
	/**
	 * get arr addon
	 */
	private function getAddonObject($name){
		
		//take from cache
		if(isset(self::$arrCacheAddons[$name]))
			return(self::$arrCacheAddons[$name]);
	
		//init the obj - take from db
		try{
	
			$addon = new UniteCreatorAddon();
			
			if(empty($this->addonType))
				$addon->initByName($name);
			else 
				$addon->initByAlias($name, $this->addonType);
			
			self::$arrCacheAddons[$name] = $addon;
	
			return($addon);
	
		}catch(Exception $e){
			return(null);
		}
	
	}
	
	
	/**
	 * get extra data from addon like title
	 */
	private function getAddonExtraData($name){
	
		$addon = $this->getAddonObject($name);
	
		if(empty($addon))
			return(null);
	
		$arrExtraData = array();
		$arrExtraData["title"] = $addon->getTitle();
		$arrExtraData["url_icon"] = $addon->getUrlIcon();
	
		return($arrExtraData);
	}
	
	
	/**
	 * modify addon data for editor get
	 */
	private function createGridDataFull_modifyAddonData($addonData){
		
		if(empty($addonData))
			return($addonData);
		
		$addonName = UniteFunctionsUC::getVal($addonData, "name");
				
		if(empty($addonName))
			return($addonData);
		
		$addonExtraData = $this->getAddonExtraData($addonName);
		if(empty($addonExtraData)){
			$addonExtraData = array();
			$addonExtraData["missing"] = true;
		}
		
		$addonData = array_merge($addonData, $addonExtraData);
		
		return($addonData);
	}
	
	
	/**
	 * create grid data full
	 * complete the addon with it's data
	 */
	private function createGridDataFull(){
		
		if(!empty($this->gridDataFull))
			return($this->gridDataFull);
		
		$this->gridDataFull = $this->mapModifyLayoutDataAddons($this->gridData, "createGridDataFull_modifyAddonData");
		
		return($this->gridDataFull);
	}
	
	/**
	 * map layout data, call modify function to modify each addon
	 */
	public function mapModifyLayoutDataAddons($arrData, $modifyFunc){
	
		$rows = UniteFunctionsUC::getVal($arrData, "rows");
		if(empty($rows))
			return($arrData);
	
		foreach($rows as $keyRow=>$row){
			$cols = UniteFunctionsUC::getVal($row, "cols");
	
			foreach($cols as $keyCol=>$col){
				$addonData = UniteFunctionsUC::getVal($col, "addon_data");
				if(isset($addonData["config"]))
					$addonData = array($addonData);
	
				if(empty($addonData))
					$addonData = array();
	
				foreach($addonData as $keyAddon=>$addon){
					
					//modify addon
					if(is_array($modifyFunc))
						$addon = call_user_func($modifyFunc,$addon);
					else
						$addon = call_user_func(array($this, $modifyFunc),$addon);
					
					if(!empty($addon))
						$addonData[$keyAddon] = $addon;
				}
	
				$col["addon_data"] = $addonData;
	
				$cols[$keyCol] = $col;
			}
	
			$row["cols"] = $cols;
	
			$rows[$keyRow] = $row;
		}
	
		$arrData["rows"] = $rows;
	
		return($arrData);
	}
	
	
	/**
	 * modify grid data for save
	 */
	private function modifyAddonDataForSave($data){
	
		$name = UniteFunctionsUC::getVal($data, "name");
		
		try{
			
			unset($data["url_icon"]);
			unset($data["title"]);
						
			$config = UniteFunctionsUC::getVal($data, "config");
			$items = UniteFunctionsUC::getVal($data, "items");
			
			//init addon
			$addon = new UniteCreatorAddon();
			$addon->initByName($name);
			
			$addon->setParamsValues($config);
			
			if(!empty($items))
				$addon->setArrItems($items);
			
			
			if(!empty($config)){
				
				$arrImages = $addon->getProcessedMainParamsImages();
				if(!empty($arrImages)){
					$arrImages = $addon->modifyDataConvertToUrlAssets($arrImages);
					$data["config"] = array_merge($config, $arrImages);
				}
			}
			
			if(!empty($items) && is_array($items)){
				
				$arrItemsImages = $addon->getProcessedItemsData(false, "uc_image");
				
				foreach($arrItemsImages as $key=>$itemImage){
					$itemImage = $addon->modifyDataConvertToUrlAssets($itemImage);
					
					if(!empty($itemImage))
						$items[$key] = array_merge($items[$key],$itemImage);
				}
			
				$data["items"] =  $items;
			}
	
		}catch(Exception $e){
	
			return($data);
		}
		
		
		return($data);
	}
	
	
	/**
	 * sanitize layout data for save
	 */
	private function prepareDataForSave($data){
	
		unset($data["title"]);
		unset($data["layoutid"]);
	
		if(isset($data["id"]))
			unset($data["id"]);
	
		$gridData = UniteFunctionsUC::getVal($data, "grid_data");
	
		//decode the data:
		if(is_string($gridData)){
			$gridData = UniteFunctionsUC::decodeContent($gridData);
			$gridData = $this->modifyGridDataForSave($gridData);
			
			$data["grid_data"] = $gridData;
		}
		
		$data = serialize($data);
	
		return($data);
	}
	
	
	
	private function a__________EXTERNAL_GETTERS___________(){}
	
	
	/**
	 * get grid data
	 */
	public function getGridDataForEditor(){
		$this->validateInited();
				
		try{
			
			if(empty($this->gridDataFull))
				$this->createGridDataFull();
			
		}catch(Exception $e){
			
			HelperHtmlUC::outputException($e);
			
			return $this->gridData;
		}
		
				
		return($this->gridDataFull);
	}
	
	
	/**
	 * get grid data for front
	 */
	public function getRowsFront(){
		
		$this->validateInited();
		
		$rows = UniteFunctionsUC::getVal($this->gridData, "rows");
		
		UniteFunctionsUC::validateNotEmpty($rows, "Layout Rows");
		
		
		return($rows);
	}
	
	
	/**
	 * get all grid options
	 */
	public function getAllGridOptions(){
		
		$this->validateInited();
		
		$globalOptions = self::getGridGlobalOptions();

		$layoutOptions = UniteFunctionsUC::getVal($this->gridData, "options", array());
		
		$gridSettingsOptions = self::getGridSettingsOptions($layoutOptions);
		
		$allOptions = array_merge($globalOptions, $gridSettingsOptions);
		
		return($allOptions);
	}
	
	
	/**
	 * get grid options - only those that different from default
	 */
	public function getGridOptionsDiff(){
		
		$defaultOptions = self::getGridDefaultOptions();
		
		$allOptions = $this->getAllGridOptions();
		
		$arrDiffOptions = UniteFunctionsUC::getDiffArrItems($allOptions, $defaultOptions);
		
		return($arrDiffOptions);
	}
	
	
	/**
	 * get title
	 */
	public function getTitle($specialChars = false){
		
		$this->validateInited();
		
		return($this->title);
	}
	
	/**
	 * get layout ID
	 */
	public function getID(){
		
		$this->validateInited();
		
		return($this->id);
	}
	
	/**
	 * get record
	 */
	public function getRecord(){
		
		return($this->record);
	}
	
	
	/**
	 * get layout record for export
	 */
	public function getRecordForExport(){
		$this->validateInited();
		
		$record = array();
		$record["title"] = $this->title;
		
		$gridData = $this->modifyGridDataForSave($this->gridData);
		
		$this->data["grid_data"] = $gridData;
		$layoutData = serialize($this->data);
		$record["layout_data"] = $layoutData;
		
		return($record);
	}
	
	
	/**
	 * get shortcode
	 */
	public function getShortcode(){
		
		$title = $this->getTitle(true);
		$id = $this->id;
		
		$shortcode = GlobalsUC::$layoutShortcodeName." id={$id} title=\"{$title}\"";
		
		$shortcode = UniteProviderFunctionsUC::wrapShortcode($shortcode);
		
		return($shortcode);
	}
	
	
	/**
	 * get layout addon type
	 */
	public function getAddonType(){
		
		return($this->addonType);
	}
	
	
	/**
	 * get raw layout data
	 */
	public function getRawLayoutData(){
		$this->validateInited();
		
		$strLayoutData = $this->record["layout_data"];
		
		return($strLayoutData);
	}
	
	
	/**
	 * collect addon names
	 */
	private function modifyAddons_collectAddonNames($addonData){
	
		$name = UniteFunctionsUC::getVal($addonData, "name");
	
		$this->arrAddonNames[$name] = true;
		
		return(null);
	}
	
	
	private function a__________EXPORT_RELATED___________(){}
	
	
	
	/**
	 * modify grid data by some function
	 */
	public function modifyGridDataAddons($modifyFunc){
		
		$this->validateInited();
		if(is_array($modifyFunc) == false)
			UniteFunctionsUC::throwError("Wrong modify func");
		
		$this->gridData = $this->mapModifyLayoutDataAddons($this->gridData, $modifyFunc);
		
	}
	
	
	/**
	 * get all layout addons without content
	 */
	public function getArrAddons(){
		
		$this->validateInited();
	
		$this->arrAddonNames = array();
		$this->mapModifyLayoutDataAddons($this->gridData, "modifyAddons_collectAddonNames");
		
		$arrAddons = array();
		
		foreach($this->arrAddonNames as $name=>$var){
			
			try{
								
				$objAddon = new UniteCreatorAddon();
				$objAddon->initByMixed($name, $this->addonType);
				$arrAddons[] = $objAddon;
				
			}catch(Exception $e){
				
			}
		}
		
		return($arrAddons);
	}
	
	
	
	private function a__________EXTERNAL_SETTERS___________(){}
	
	
	/**
	 * modify grid data for save
	 */
	public function modifyGridDataForSave($gridData){
		
		$gridData = $this->mapModifyLayoutDataAddons($gridData, "modifyAddonDataForSave");
		
		return($gridData);
	}
	
	
	/**
	 * update layout in db
	 */
	public function createLayoutInDB($arrInsert){
		
		$objLayouts = new UniteCreatorLayouts();
		
		$maxOrder = $objLayouts->getMaxOrder();
		$arrInsert["ordering"] = $maxOrder+1;
		
		$id = $this->db->insert(GlobalsUC::$table_layouts, $arrInsert);
		
		return($id);
	}
	
	
	/**
	 * create layout from data
	 */
	public function create($data){
		
		$title = UniteFunctionsUC::getVal($data, "title");
		
		unset($data["title"]);
		unset($data["layoutid"]);
		UniteFunctionsUC::validateNotEmpty($title, "Layout Title");
		
		$arrInsert = array();
		$arrInsert["title"] = $title;
		$arrInsert["layout_data"] = $this->prepareDataForSave($data);
		
		$id = $this->createLayoutInDB($arrInsert);
		
		return($id);
	
	}
	
	
	/**
	 * update layout in db
	 */
	public function updateLayoutInDB($arrUpdate){
		
		$where = "id={$this->id}";
		
		$this->db->update(GlobalsUC::$table_layouts, $arrUpdate, $where);
	}
	
	
	/**
	 * update layout
	 */
	public function update($data){
	
		$this->validateInited();
	
		$title = UniteFunctionsUC::getVal($data, "title");
		
		$arrUpdate = array();
		$arrUpdate["title"] = $title;
		$arrUpdate["layout_data"] = $this->prepareDataForSave($data);
		
		$this->updateLayoutInDB($arrUpdate);
		
	}
	
	/**
	 * update grid data in db
	 */
	public function updateGridData($gridData = null){
		
		if($gridData !== null)
			$this->gridData = $gridData;
		
		$this->data["grid_data"] = $this->gridData;
		
		$arrUpdate = array();
		$arrUpdate["layout_data"] = $this->prepareDataForSave($this->data);
		
		$this->updateLayoutInDB($arrUpdate);
	}
	
	
	/**
	 * delete layout
	 */
	public function delete(){
		$this->validateInited();
		
		$this->db->delete(GlobalsUC::$table_layouts, "id=".$this->id);
		
	}
	
	
	/**
	 * get new name
	 */
	private function getDuplicateTitle(){
		
		$objLayouts = new UniteCreatorLayouts();
		
		$suffixTitle = " - copy";
	
		$title = $this->getTitle();
				
		$newTitle = $title.$suffixTitle;
		$isExists = $objLayouts->isLayoutExistsByTitle($newTitle);
		
		$num = 1;
		$limit = 1;
		while($isExists == true && $limit < 10){
			$limit++;
			$num++;
			$suffixTitle = " - copy".$num;
			$newTitle = $title.$suffixTitle;
			$isExists = $objLayouts->isLayoutExistsByTitle($newTitle);
		}
		
		
		return($newTitle);
	}
	
	
	/**
	 * duplicate layout
	 */
	public function duplicate(){
		
		$this->validateInited();
		
		$layouts = new UniteCreatorLayouts();
				
		$newTitle = $this->getDuplicateTitle();
		
		$layouts->shiftOrder($this->ordering);
		
		$newOrder = $this->ordering+1;
		
		//insert a new gallery
		$sqlSelect = "select ".self::FIELDS_LAYOUTS." from ".GlobalsUC::$table_layouts." where id={$this->id}";
		$sqlInsert = "insert into ".GlobalsUC::$table_layouts." (".self::FIELDS_LAYOUTS.") ($sqlSelect)";
		
		$this->db->runSql($sqlInsert);
		$lastID = $this->db->getLastInsertID();
		UniteFunctionsUC::validateNotEmpty($lastID);
		
		//update the new layout with the title and the name values
		$arrUpdate = array();
		$arrUpdate["title"] = $newTitle;
		$arrUpdate["ordering"] = $newOrder;
		
		$this->db->update(GlobalsUC::$table_layouts, $arrUpdate, array("id"=>$lastID));
		
		return($lastID);
		
	}
	
	
}