<?php
/**
 * @package Addon Library
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('ADDON_LIBRARY_INC') or die('Restricted access');

class UniteCreatorLayoutsWork extends UniteElementsBaseUC{
	
	
	/**
	 * get galleries array
	 */
	private function getRecords($order = "ordering"){
			
		$where = "";
		
		$response = $this->db->fetch(GlobalsUC::$table_layouts, $where, $order);
		
		return($response);
	}
	
	
	/**
	 * get records with paging
	 */
	private function getRecordsPaging($pagingOptions, $order = "ordering"){
		
		$response = $this->db->fetchPage(GlobalsUC::$table_layouts, $pagingOptions, "", $order);
		
		return($response);
	}
	
	
	/**
	 * get layout from data
	 */
	private function getLayoutFromData($data){
	
		$layoutID = UniteFunctionsUC::getVal($data, "layout_id");
		UniteFunctionsUC::validateNumeric($layoutID);
		UniteFunctionsUC::validateNotEmpty($layoutID);
	
		$objLayout = new UniteCreatorLayout();
		$objLayout->initByID($layoutID);
	
		return($objLayout);
	}
	
	
	/**
	 * convert records to layouts objects
	 */
	private function recordsToLayouts($records){
		
		$arrLayouts = array();
		foreach($records as $record){
			$layoutID = UniteFunctionsUC::getVal($record, "id");
			$objLayout = new UniteCreatorLayout();
			$objLayout->initByID($layoutID);
		
			$arrLayouts[] = $objLayout;
		}
		
		return($arrLayouts);
	}
	
	
	/**
	 *
	 * get addons array
	 */
	public function getArrLayouts($order = "ordering"){
		
		$response = $this->getRecords($order);
		
		$arrLayouts = $this->recordsToLayouts($response);
		
		return($arrLayouts);
	}
	
	
	/**
	 * get layouts with paging data
	 */
	public function getArrLayoutsPaging($pagingOptions, $order = "ordering"){
		
		$response = $this->getRecordsPaging($pagingOptions, $order);
		
		$rows = $response["rows"];
		unset($response["rows"]);
		
		$arrLayouts = $this->recordsToLayouts($rows);
		
		$output = array();
		$output["paging"] = $response;
		$output["layouts"] = $arrLayouts;
		
		return($output);
	}
	
	/**
	 * get layouts array short version - without content
	 */
	public function getArrLayoutsShort($addEmpty = false){
		
		$arrLayouts = $this->db->fetchFields(GlobalsUC::$table_layouts, "id, title", null, "ordering");
		if(empty($arrLayouts))
			$arrLayouts = array();
		
		if($addEmpty == true){
			$arrItem = array("id"=>"empty", "title"=>"[Not Selected]");
			$arrAdd = array($arrItem);
			
			$arrLayouts = array_merge($arrAdd, $arrLayouts);
		}
		
		
		return($arrLayouts);
	}
	
	
	
	/**
	 * update layout from data
	 */
	public function updateLayoutFromData($data){
		
		$layoutID = UniteFunctionsUC::getVal($data, "layoutid");
		
		$objLayout = new UniteCreatorLayout();
		
		$isUpdate = false;
		
		if(empty($layoutID)){
			
			$layoutID = $objLayout->create($data);
			
		}else{
			
			$isUpdate = true;
			
			//update layout
			$objLayout->initByID($layoutID);
			$objLayout->update($data);
		}
		
		$response = array();
		$response["is_update"] = $isUpdate;
		$response["layout_id"] = $layoutID;
		
		return($response);
	}
	
	
	
	/**
	 * delete layout from data
	 */
	public function deleteLayoutFromData($data){
		
		$objLayout = $this->getLayoutFromData($data);
		
		$objLayout->delete();
			
	}
	
	/**
	 * duplicate layout from data
	 */
	public function duplicateLayoutFromData($data){
		
		$objLayout = $this->getLayoutFromData($data);
		
		$objLayout->duplicate();
		
	}
	
	
	/**
	 * check if layout exists by title
	 */
	public function isLayoutExistsByTitle($title){
	
		$response = $this->db->fetch(GlobalsUC::$table_layouts, array("title"=>$title));
	
		$isExists = !empty($response);
	
		return($isExists);
	}
	
	
	/**
	 * get layout name, and find name that don't exists in database using counter	 *
	 */
	public function getUniqueTitle($title){
	
		$counter = 1;
	
		$isExists = $this->isLayoutExistsByTitle($title);
		if($isExists == false)
			return($title);
				
		$limit = 1;
		while($isExists == true && $limit < 10){
			$limit++;
			$counter++;
			$newTitle = $title."-".$counter;
			$isExists = $this->isLayoutExistsByTitle($newTitle);
		}
		
		return($newTitle);
	}
	
	
	/**
	 *
	 * get max order from categories list
	 */
	public function getMaxOrder(){
	
		$tableLayouts = GlobalsUC::$table_layouts;
	
		$query = "select MAX(ordering) as maxorder from {$tableLayouts}";
	
		$rows = $this->db->fetchSql($query);
	
		$maxOrder = 0;
		if(count($rows)>0)
			$maxOrder = $rows[0]["maxorder"];
	
		if(!is_numeric($maxOrder))
			$maxOrder = 0;
	
		return($maxOrder);
	}
	
	
	
	/**
	 * shift addons in category from some order (more then the order).
	 */
	public function shiftOrder($order){
		
		UniteFunctionsUC::validateNumeric($order);
		
		$tableLayouts = GlobalsUC::$table_layouts;
		
		$query = "update {$tableLayouts} set ordering = ordering+1 where ordering > {$order}";
		
		$this->db->runSql($query);
	}
	
	
	/**
	 * export layout from get data
	 */
	public function exportLayout(){
		
		$layoutID = UniteFunctionsUC::getGetVar("id","",UniteFunctionsUC::SANITIZE_ID);
		$layoutID = (int)$layoutID;
		
		$objLayout = new UniteCreatorLayout();
		$objLayout->initByID($layoutID);
		
		$exporter = new UniteCreatorLayoutsExporter();
		$exporter->initByLayout($objLayout);
		$exporter->export();
	}
	
	
	/**
	 * import layouts
	 */
	public function importLayouts($data){
		
		$layoutID = UniteFunctionsUC::getVal($data, "layoutID");
		if(!empty($layoutID))
			$layoutID = (int)$layoutID;
		
		$arrTempFile = UniteFunctionsUC::getVal($_FILES, "import_layout");
		
		$isOverwriteAddons = UniteFunctionsUC::getVal($data, "overwrite_addons");
		
		
		$exporter = new UniteCreatorLayoutsExporter();
		$exporter->import($arrTempFile, $layoutID, $isOverwriteAddons);
		
		if(empty($layoutID))
			$urlRedirect = HelperUC::getViewUrl_LayoutsList();
		else
			$urlRedirect = HelperUC::getViewUrl_Layout($layoutID);

		
		return($urlRedirect);
	}
	
}