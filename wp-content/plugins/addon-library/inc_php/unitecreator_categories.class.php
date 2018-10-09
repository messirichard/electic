<?php
/**
 * @package Addon Library
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('ADDON_LIBRARY_INC') or die('Restricted access');


class UniteCreatorCategories extends UniteElementsBaseUC{
	
	private $titleBase = "New Category";
	
	public function __construct(){
		parent::__construct();
		
	}
	
	/**
	 * 
	 * validate that category exists
	 */
	public function validateCatExist($catID){
		$this->getCat($catID);
	}
	
	
	/**
	 * validate if category not exists
	 */
	private function validateCatTitleNotExists($title, $type, $catID=null){
		
		$isExists = $this->isCatExistsByTitle($title, $type, $catID);
		
		if($isExists == true)
			UniteFunctionsUC::throwError("Category with title: $title already exists");
		
	}
	
	/**
	 * validate category title
	 */
	private function validateTitle($title){
		
		UniteFunctionsUC::validateNotEmpty($title, "Category Title");
		
		UniteFunctionsUC::validateNoTags($title, "Category Title");
		
	}
	
	
	private function ______________GETTERS____________(){}
	
	
	/**
	 * get uncategorised category
	 */
	private function getFirstCats($type){

		$objAddons = new UniteCreatorAddons();
		
		$filterActive = UniteCreatorAddons::getStateFilterActive();
		
		$numAddonsZero = $objAddons->getNumAddons(0, $filterActive, $type);
		
		//all
		
		$arrCatAll = array();
		$arrCatAll["id"] = "all";
		$arrCatAll["title"] = __("All Addons", ADDONLIBRARY_TEXTDOMAIN);
		$arrCatAll["alias"] = "";
		$arrCatAll["ordering"] = 0;
		$arrCatAll["parent_id"] = "";
		$arrCatAll["params"] = "";
		$arrCatAll["type"] = "";
		$arrCatAll["num_addons"] = "set";
		
		
		//uncategorized
		
		$arrCatZero = array();
		$arrCatZero["id"] = 0;
		$arrCatZero["title"] = __("Uncategorized", ADDONLIBRARY_TEXTDOMAIN);
		$arrCatZero["alias"] = "";
		$arrCatZero["ordering"] = 0;
		$arrCatZero["parent_id"] = "";
		$arrCatZero["params"] = "";
		$arrCatZero["type"] = "";
		$arrCatZero["num_addons"] = $numAddonsZero;
		
		$arrCats = array();
		$arrCats[] = $arrCatAll;
		$arrCats[] = $arrCatZero;
		
		
		return($arrCats);
	}
	
	
	/**
	 * get category list simple
	 */
	private function getListSimple($type=""){
		
		if(empty($type))
			$type = UniteCreatorDB::ISNULL;
		
		$where = array();
		$where["type"] = $type;
		
		$response = $this->db->fetch(GlobalsUC::$table_categories, $where);
		
		return($response);
	}
	
	
	/**
	 * 
	 * get categories list with "all" and "uncategorised" and num addons
	 */
	public function getListExtra($type){
		
		$whereFilterActive = UniteCreatorAddons::getFilterActiveWhere(null, " and addons");
		
		$type = $this->db->escape($type);
		
		if(empty($type))
			$whereType = "where cats.type is null or cats.type=''";
		else
			$whereType = "where cats.type='$type'";
				
		$tableCats = GlobalsUC::$table_categories;
		$tableAddons = GlobalsUC::$table_addons;
		
		$query = "select cats.*, count(addons.id) as num_addons from {$tableCats} as cats";
		$query .= " left join $tableAddons as addons on addons.catid=cats.id $whereFilterActive $whereType GROUP BY cats.id order by ordering";
				
		$arrCats = $this->db->fetchSql($query);
		
		if(empty($arrCats))
			$arrCats = array();
		
		$arrFirstCats = $this->getFirstCats($type);
				
		$arrCats = array_merge($arrFirstCats, $arrCats);
		
		//set number of all addons
		$numAddons = 0;
		foreach($arrCats as $cat){
			$numCatAddons = $cat["num_addons"];
			if(!is_numeric($numCatAddons))
				continue;
			
			$numAddons += $numCatAddons;
		}
		
		$arrCats[0]["num_addons"] = $numAddons;
		
		
		return($arrCats);
	}
	
	
	/**
	 * 
	 * get category records simple without num items
	 */
	public function getCatRecords($type){
		
		if(empty($type))
			$type = UniteCreatorDB::ISNULL;
		
		$where = array();
		if($type != "all"){
			$where["type"] = $type;
		}
		
		$arrCats = $this->db->fetch(GlobalsUC::$table_categories, $where, "ordering");
		
		return($arrCats);
	}
	
	
	/**
	 * 
	 * get categories list short
	 * addtype: empty (empty category), new (craete new category)
	 */
	public function getCatsShort($addType = "", $type){
		
		$arrCats = $this->getCatRecords($type);
		$arrCatsOutput = array();
		
		switch($addType){
			case "empty":
				$arrCatsOutput[""] = __("[Not Selected]", ADDONLIBRARY_TEXTDOMAIN);
			break;
			case "new":
				$arrCatsOutput["new"] = __("[Add New Category]", ADDONLIBRARY_TEXTDOMAIN);
			break;
			case "component":
				$arrCatsOutput[""] = __("[From Gallery Settings]", ADDONLIBRARY_TEXTDOMAIN);
			break;
			case "all_uncat":
				$arrCatsOutput["all"] = __("All Addons", ADDONLIBRARY_TEXTDOMAIN);
				$arrCatsOutput[0] = __("Uncategorized", ADDONLIBRARY_TEXTDOMAIN);
			break;
		}
		
		foreach($arrCats as $cat){
			$catID = UniteFunctionsUC::getVal($cat, "id");
			$title = UniteFunctionsUC::getVal($cat, "title");
			$arrCatsOutput[$catID] = $title;
		}
		
		return($arrCatsOutput);
	}
	
	
	/**
	 * 
	 * get assoc value of category name
	 */
	private function getArrCatTitlesAssoc($type=""){
		
		$arrCats = $this->getListSimple($type);
		$arrAssoc = array();
		foreach($arrCats as $cat){
			$arrAssoc[$cat["title"]] = true;
		}
		return($arrAssoc);
	}
	
	
	/**
	 * 
	 * get max order from categories list
	 */
	private function getMaxOrder($type=""){
		
		$type = $this->db->escape($type);
		
		if(empty($type))
			$where = "where type='' or type is null";
		else			
			$where = "where type='$type'";
		
		$query = "select MAX(ordering) as maxorder from ".GlobalsUC::$table_categories." $where";
		
		///$query = "select * from ".self::TABLE_CATEGORIES;
		$rows = $this->db->fetchSql($query);
		
		$maxOrder = 0;
		if(count($rows)>0) 
			$maxOrder = $rows[0]["maxorder"];
		
		if(!is_numeric($maxOrder))
			$maxOrder = 0;
		
		return($maxOrder);
	}
	
	
	/**
	 * get true/false if some category exists
	 */
	public function isCatExists($catID){
		
		$arrCat = null;
		
		try{
		
			$arrCat = $this->db->fetchSingle(GlobalsUC::$table_categories,"id=$catID");
			
		}catch(Exception $e){
					
		}
		
		return !empty($arrCat);		
	}
	
	
	/**
	 * check if category exists by title
	 * check in all cats except the current category id
	 */
	private function isCatExistsByTitle($title, $type = "", $catID = null){
		
		if(empty($type))
			$type = UniteCreatorDB::ISNULL;
		
		$arrWhere = array();
		$arrWhere["title"] = $title;
		$arrWhere["type"] = $type;
		
		$response = $this->db->fetch(GlobalsUC::$table_categories, $arrWhere);
		
		if(empty($response))
			return(false);
		
		//check by catID
		if(empty($catID))
			return(true);
		
		$cat = $response[0];
		if($cat["id"] == $catID)
			return(false);
		else
			return(true);		
	}
	
	
	/**
	 * 
	 * get category
	 */
	public function getCat($catID){
		$catID = (int)$catID;
		
		$arrCat = $this->db->fetchSingle(GlobalsUC::$table_categories,"id=$catID");
		if(empty($arrCat))
			UniteFunctionsUC::throwError("Category with id: $catID not found");
			
		return($arrCat);
	}
	
	/**
	 * get category by title
	 * if not found - return null
	 */
	public function getCatByTitle($title, $type=""){
		
		if(empty($type))
			$type = UniteCreatorDB::ISNULL;
		
		$arrWhere = array();
		$arrWhere["title"] = $title;
		$arrWhere["type"] = $type;
		
		try{
			$arrCat = $this->db->fetchSingle(GlobalsUC::$table_categories, $arrWhere);
		
		if(empty($arrCat))
			return(null);
		
		return($arrCat);
		
		}catch(Exception $e){
			return(null);
		}
	}
	
	
	/**
	 *
	 * get html of category
	 */
	private function getCatHTML($cat, $class = ""){
	
		$id = $cat["id"];
		$title = $cat["title"];
		$numAddons = $cat["num_addons"];
	
		$title = $cat["title"];
		$numAddons = $cat["num_addons"];
	
		$showTitle = $title;
	
		if(!empty($numAddons))
			$showTitle .= " ($numAddons)";
	
		$html = "";
		$html .= "<li id=\"category_{$id}\" {$class} data-id=\"{$id}\" data-numaddons=\"{$numAddons}\" data-title=\"{$title}\">\n";
		$html .= "	<span class=\"cat_title\">{$showTitle}</span>\n";
		$html .= "</li>\n";
	
		return($html);
	}
	
	
	/**
	 * get list of categories
	 */
	public function getHtmlCatList($selecteCatID = null, $type){
				
		$arrCats = $this->getListExtra($type);
		
		$html = "";
		
		foreach($arrCats as $index => $cat):
			$id = $cat["id"];
			
			$class = "";
			if($index == 0)
				$class = "first-item";
			
			if(is_numeric($selecteCatID))
				$selecteCatID = (int)$selecteCatID;
			
			if(is_numeric($id))
				$id = (int)$id;
			
			//select item
			if($selecteCatID !== null && $id === $selecteCatID){
				if(!empty($class))
					$class .= " ";
				$class .= "selected-item";
				
			}
	
			if(!empty($class))
				$class = "class=\"{$class}\"";
		
			$html .= $this->getCatHTML($cat, $class);
			
		endforeach;
	
		return($html);
	}
	
	
	/**
	 *
	 * get items for select categories
	 */
	public function getHtmlSelectCats($type){
		
		$arrCats = $this->getListSimple($type);
		
		$html = "";
		foreach($arrCats as $cat):
			$catID = $cat["id"];
			$title = $cat["title"];
			$html .= "<option value=\"{$catID}\">{$title}</option>";
		endforeach;
	
		return($html);
	}
	
	
	/**
	 * get categories list from data
	 */
	public function getCatListFromData($data){
		
		$selectedCat = UniteFunctionsUC::getVal($data, "selected_catid");
		$filterActive = UniteFunctionsUC::getVal($data, "filter_active");
		$type = UniteFunctionsUC::getVal($data, "addontype");
		
		UniteCreatorAddons::setStateFilterActive($filterActive);
		
		$htmlCats = $this->getHtmlCatList($selectedCat, $type);
		
		$response = array();
		$response["htmlCats"] = $htmlCats;
		
		return($response);
	}
	
	
	private function ______________SETTERS____________(){}
	
	
	/**
	 *
	 * add category
	 */
	public function add($title, $type=""){
		
		$maxOrder = $this->getMaxOrder();
	
		$this->validateTitle($title, $type);
			
		$this->validateCatTitleNotExists($title, $type);
	
		/*
		 // get name automatically:
	
		$titleBase = $this->titleBase;
	
		//set title
		$arrTitles = $this->getArrCatTitlesAssoc();
		$title = $titleBase;
		$titleExists = array_key_exists($title, $arrTitles);
	
		if($titleExists == true){
		$counter = 1;
		do{
		$counter++;
		$title = $titleBase." ".$counter;
		$titleExists = array_key_exists($title, $arrTitles);
		}while($titleExists == true);
		}
		*/
	
		//prepare insert array
		$arrInsert = array();
		$arrInsert["title"] = $title;
		$arrInsert["type"] = $type;
		$arrInsert["ordering"] = $maxOrder+1;
		
		//insert the category
		$catID = $this->db->insert(GlobalsUC::$table_categories,$arrInsert);
	
		//prepare output
		$returnData = array("id"=>$catID,"title"=>$title);
		return($returnData);
	}
	
	
	/**
	 * get category id by title. If the title not exists - create it 
	 */
	public function getCreateCatByTitle($title, $type=""){
		
		//if found, return id
		$arrCat = $this->getCatByTitle($title, $type);
		if(!empty($arrCat)){
			$catID = $arrCat["id"];
			return($catID);
		}
		
		try{
			
			$createData = $this->add($title, $type);
			$catID = $createData["id"];
			
			return($catID);
			
		}catch(Exception $e){
			
			return(0);
		}
		
	}
	
	
	/**
	 * 
	 * remove the category.
	 */
	private function remove($catID){
		$catID = (int)$catID;
		
		//remove category
		$this->db->delete(GlobalsUC::$table_categories,"id=".$catID);
		
		//remove addons
		$this->db->delete(GlobalsUC::$table_addons,"catid=".$catID);
	}
	
	
	/**
	 * 
	 * update category
	 */
	private function update($catID,$title){
		$catID = (int)$catID;
		
		$this->validateTitle($title);
		
		$arrUpdate = array();
		$arrUpdate["title"] = $title;
		$this->db->update(GlobalsUC::$table_categories,$arrUpdate,array("id"=>$catID));
	}
	
	
	/**
	 * update category type
	 */
	public function updateType($catID, $type){
		$catID = (int)$catID;
		
		$this->validateCatExist($catID);
		
		$arrUpdate = array();
		$arrUpdate["type"] = $type;
		$this->db->update(GlobalsUC::$table_categories,$arrUpdate,array("id"=>$catID));
	}
	
	
	/**
	 * 
	 * update categories order
	 */
	private function updateOrder($arrCatIDs){
		
		foreach($arrCatIDs as $index=>$catID){
			$order = $index+1;
			$arrUpdate = array("ordering"=>$order);
			$where = array("id"=>$catID);
			$this->db->update(GlobalsUC::$table_categories,$arrUpdate,$where);
		}
	}
	
	
	/**
	 * 
	 * remove category from data
	 */
	public function removeFromData($data){
		
		$catID = UniteFunctionsUC::getVal($data, "catID");
		$type = UniteFunctionsUC::getVal($data, "type");
		
		$this->remove($catID);
		
		$response = array();
		$response["htmlSelectCats"] = $this->getHtmlSelectCats($type);
		
		return($response);
	}
	
	
	/**
	 * 
	 * update category from data
	 */
	public function updateFromData($data){
		$catID = UniteFunctionsUC::getVal($data, "catID");
		$title = UniteFunctionsUC::getVal($data, "title");
		
		$this->update($catID, $title);
	}
	
	
	
	/**
	 * 
	 * update order from data
	 */
	public function updateOrderFromData($data){
		$arrCatIDs = UniteFunctionsUC::getVal($data, "cat_order");
		if(is_array($arrCatIDs) == false)
			UniteFunctionsUC::throwError("Wrong categories array");
			
		$this->updateOrder($arrCatIDs);
	}
	
	
	/**
	 * 
	 * add catgory from data, return cat select html list
	 */
	public function addFromData($data){
		
		$title = UniteFunctionsUC::getVal($data, "catname");
		$type = UniteFunctionsUC::getVal($data, "type");
		
		$response = $this->add($title, $type);
		
		$arrCat = array("id"=>$response["id"],"title"=>$response["title"],"num_addons"=>0);
		$html = $this->getCatHTML($arrCat);
		
		$response["message"] = __("Category Added", ADDONLIBRARY_TEXTDOMAIN);
		$response["htmlSelectCats"] = $this->getHtmlSelectCats($type);
		$response["htmlCat"] = $html;
		
		return($response);
		
	}
	
	
}

?>