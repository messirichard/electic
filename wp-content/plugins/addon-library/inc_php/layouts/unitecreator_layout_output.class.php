<?php
/**
 * @package Addon Library
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('ADDON_LIBRARY_INC') or die('Restricted access');

class UniteCreatorLayoutOutputWork extends HtmlOutputBaseUC{
	
	protected $layout, $gridHtmlID, $isScriptsHardCoded = true;
	protected $cssToBody = false;	//put addon and layout css to body
	protected $gridOptionsDiff, $gridOptionsAll;
	protected static $serial = 0;
	protected $addonType = null;
	
	
	/**
	 * validate that the layout output inited
	 */
	private function validateInited(){
		
		if(empty($this->layout))
			UniteFunctionsUC::throwError("The layout output is not inited");
		
	}
	
	
	/**
	 * init by layout object
	 */
	public function initByLayout(UniteCreatorLayout $objLayout){
		
		//init grid ID
		$prefix = "uc_grid_";
		self::$serial++;
		
		$this->gridHtmlID = $prefix.self::$serial;
		
		$this->layout = $objLayout;
		
		$this->addonType = $this->layout->getAddonType();
				
		$this->gridOptionsDiff = $this->layout->getGridOptionsDiff();
		$this->gridOptionsAll = $this->layout->getAllGridOptions();
		
	}
	
	
	/**
	 * output layout css
	 */
	public static function putIncludeScripts(){
		$urlCss = GlobalsUC::$url_assets_internal."css/uc_front.css";
	
		HelperUC::addStyleAbsoluteUrl($urlCss, "unitecreator_css_front");
	}
	
	
	/**
	 * get option
	 */
	private function getOption($name = ""){
		
		if(empty($name))
			return($this->gridOptionsAll);
		
		$value = UniteFunctionsUC::getVal($this->gridOptionsAll, $name);
		
		return($value);
	}
	
	
	/**
	 * get col class according the number of cols
	 */
	private function getColSizeClass($numCols){
		
		$colSize = "";
		
		switch($numCols){
			case 1:
				$colSize = "uc-colsize-1_1";
			break;
			case 2:
				$colSize = "uc-colsize-1_2";
			break;
			case 3:
				$colSize = "uc-colsize-1_3";
			break;
			case 4:
				$colSize = "uc-colsize-1_4";
			break;
			case 5:
				$colSize = "uc-colsize-1_5";
			break;
			case 6:
				$colSize = "uc-colsize-1_6";
			break;
			default:
				UniteFunctionsUC::throwError("Invalid number of columns: $numCols");
			break;
		}
		
		
		return($colSize);
	}
	
	
	/**
	 * put column
	 */
	private function getHtmlColContent($col, $sizeClass){
		
		$html = "";
	
		return($html);
	}
	
	
	/**
	 * get addon html
	 */
	private function getAddonHtml($addonData){
		
		//if no addon name - return empty string
		if(empty($addonData))
			return("");
		
		$addonName = UniteFunctionsUC::getVal($addonData, "name");
		
		if(empty($addonName))
			return("");
		
		$html = "";
		
		//output addon
		try{
			//init addon
			$objAddon = new UniteCreatorAddon();
			
			if(empty($this->addonType))
				$objAddon->initByName($addonName);
			else 
				$objAddon->initByAlias($addonName, $this->addonType);
		
		}catch(Exception $e){
			
			//if addon not found - return it's name
			$html .= $addonName .__(" addon not found", ADDONLIBRARY_TEXTDOMAIN);
			
			return($html);
		}
		
		//set addon data
		
		$arrConfig = UniteFunctionsUC::getVal($addonData, "config");
		if(!empty($arrConfig))
			$objAddon->setParamsValues($arrConfig);
		
		$arrItems = UniteFunctionsUC::getVal($addonData, "items");
		if(!empty($arrItems))
			$objAddon->setArrItems($arrItems);
		
		//process includes and get html
		
		$objOutput = new UniteCreatorOutput();
		$objOutput->initByAddon($objAddon);
		$objOutput->processIncludes();
		$htmlAddon = $objOutput->getHtmlBody($this->isScriptsHardCoded, $this->cssToBody);
		
		$html .= $htmlAddon;
		
		return($html);
	}
	
	
	/**
	 * get column addon html 
	 */
	private function getColHtml($col, $styleColAddons = ""){
		
		$addonsData = UniteFunctionsUC::getVal($col, "addon_data");
		
		$isSingle = isset($addonsData["config"]);
		
		$addonHtml = "";
		
		if($isSingle == false){
			
			foreach($addonsData as $index => $addonData){
				
				$addStyle = "";
				if(!empty($styleColAddons) && $index > 0)
					$addStyle = " ".$styleColAddons;
				$addonHtml .= "<div class='uc-grid-col-addon'{$addStyle}>";
				
				$addonHtml .= $this->getAddonHtml($addonData);
				
				$addonHtml .= "</div>";
			}
			
		}else{
			$addonHtml .= "<div class='uc-grid-col-addon'>";
			
			$addonHtml .= $this->getAddonHtml($addonsData);
			
			$addonHtml .= "</div>";
		}
				
		$html = "";
		$html .= "<div class=\"uc-grid-col-inner\">";
		$html .= $addonHtml;
		$html .= "</div>";
		
		return($html);
	}
	
	
	/**
	 * get columns html
	 */
	private function getHtmlCols($arrCols, $styleCols = "", $styleColAddons=""){
		
		if(!is_array($arrCols))
			UniteFunctionsUC::throwError("The columns should be array");
		
		if(empty($arrCols))
			UniteFunctionsUC::throwError("The row should have at least one column");

		$numCols = count($arrCols);
		
		$colSizeClass = $this->getColSizeClass($numCols);
		if(!empty($styleCols))
			$styleCols = " ".$styleCols;
		
		$html = "";
		foreach($arrCols as $numCol => $col){
			
			$isFirst = ($numCol == 0);
			$isLast = ($numCol == ($numCols-1));
			
			$class = "uc-grid-col ";
			
			if($isFirst)
				$class .= "uc-col-first ";
			
			if($isLast)
				$class .= "uc-col-last ";
			
			$class .= $colSizeClass;
			
			$html .= self::TAB3."<div class=\"{$class}\"{$styleCols}>";
			
			$colHtml = $this->getColHtml($col, $styleColAddons);
			
			$html .= $colHtml;
			$html .= "</div>".self::BR;
			
		}
		
		if(!empty($arrCols)){
			$html .= "<div class=\"uc-col-clear\"></div>".self::BR;
		}
		
		return($html);
	}
	
	
	/**
	 * get row inline css
	 * 
	 */
	private function getRowInlineCss($row){
		
		$arrRow = array();
		$arrContainer = array();
		$arrCols = array();
		$arrColAddons = array();
		
		$settings = UniteFunctionsUC::getVal($row, "settings", array());
		if(empty($settings))
			$settings = array();
		
		foreach($settings as $key=>$value){
			
			$value = trim($value);
			
			switch($key){
				case "row_container_width":
					if($value !== "")
						$arrContainer["max-width"] = UniteFunctionsUC::normalizeSize($value);
				break;
				case "row_padding_top":
					if($value !== "")
						$arrRow["padding-top"] = UniteFunctionsUC::normalizeSize($value);
				break;
				case "row_padding_bottom":
					if($value !== "")
						$arrRow["padding-bottom"] = UniteFunctionsUC::normalizeSize($value);
				break;
				case "row_background_color":
					if($value !== "")
						$arrRow["background-color"] = $value;
				break;
				case "col_gutter":
					if($value !== ""){
						$arrCols["padding-left"] = UniteFunctionsUC::normalizeSize($value);
						$arrCols["padding-right"] = UniteFunctionsUC::normalizeSize($value);
					}
				break;
				case "space_between_addons":
					if($value !== "")
						$arrColAddons["margin-top"] = UniteFunctionsUC::normalizeSize($value);
				break;
			}
			
		}
		
		
		$rowAddCss = UniteFunctionsUC::getVal($settings, "row_css");
		$containerAddCss = UniteFunctionsUC::getVal($settings, "row_container_css");
		
		$cssRow = UniteFunctionsUC::arrStyleToStrInlineCss($arrRow, $rowAddCss);
		$cssContainer = UniteFunctionsUC::arrStyleToStrInlineCss($arrContainer, $containerAddCss);
		
		$cssCols = UniteFunctionsUC::arrStyleToStrInlineCss($arrCols);
		$cssColAddons = UniteFunctionsUC::arrStyleToStrInlineCss($arrColAddons);
		$output = array();
		$output["row"] = $cssRow;
		$output["container"] = $cssContainer;
		$output["cols"] = $cssCols;
		$output["coladdons"] = $cssColAddons;
		return($output);
	}
	
	
	/**
	 * return if show titles or not
	 */
	private function isShowTitles(){
		$showTitles = $this->getOption("show_row_titles");
		
		if($showTitles == "default")
			$showTitles = $this->getOption("show_row_titles_global");
		
		$showTitles = ($showTitles == "yes");
		
		return($showTitles);
	}
	
	
	/**
	 * output front rows
	 */
	private function getHtmlRows($rows){
		
		$html = "";
		
		$numRows = count($rows);
		
		$showTitlesGlobal = $this->isShowTitles();
		
		foreach($rows as $key => $row){
			
			$isFirst = ($key == 0);
			$isLast = $key == ($numRows-1);
			
			$arrRowCss = $this->getRowInlineCss($row);
			
			$styleRow = $arrRowCss["row"];
			$styleContainer = $arrRowCss["container"];
			$styleCols = $arrRowCss["cols"];
			$styleColAddons = $arrRowCss["coladdons"];
			
			$settings = UniteFunctionsUC::getVal($row, "settings");
			
			//get row class and attribute
			$rowID = UniteFunctionsUC::getVal($settings, "row_id");
			$rowClass = UniteFunctionsUC::getVal($settings, "row_class");
			
			$rowID = UniteFunctionsUC::sanitizeAttr($rowID);
			$rowClass = UniteFunctionsUC::sanitizeAttr($rowClass);
			
			$class = "uc-grid-row";
			
			if($isFirst)
				$class .= " uc-row-first";
			
			if($isLast)
				$class .= " uc-row-last";
			
			if(!empty($rowClass))
				$class .= " ".$rowClass;
			
			if(!empty($rowID))
				$rowID = "id=\"{$rowID}\"";
			
			$html .= self::TAB2."<div {$rowID} class=\"{$class}\" {$styleRow}>".self::BR;
			$html .= self::TAB3."<div class=\"uc-grid-row-container\" {$styleContainer}>".self::BR;
			
			
			//------------ draw title---------------
			
			$showTitle = true;
			$showTitleLocal = UniteFunctionsUC::getVal($settings, "row_show_title");
			switch($showTitleLocal){
				case "default":
					$showTitle = $showTitlesGlobal;
				break;
				case "no":
					$showTitle = false;
				break;
			}

			$rowTitle = UniteFunctionsUC::getVal($settings, "row_title");
			$rowTitle = trim($rowTitle);
			if(empty($rowTitle))
				$showTitle = false;
			
			if($showTitle)
				$html .= self::TAB3."<h2 class=\"uc-grid-row-title\">$rowTitle</h2>".self::BR;
			
			
			//------------ draw columns---------------
			
			$arrCols = UniteFunctionsUC::getVal($row, "cols");
			UniteFunctionsUC::validateNotEmpty($arrCols, "row columns");
						
			$html .= $this->getHtmlCols($arrCols, $styleCols, $styleColAddons);
			
			$html .= self::TAB3."</div>".self::BR;
			$html .= self::TAB2."</div>".self::BR;
			
		}
		
		return($html);
	}
	
	
	/**
	 * get grid inline css
	 */
	private function getGridCss($wrap = false){
		
		$css = "";
		$options = $this->gridOptionsDiff;
		
		if(empty($options))
			return("");
		
		$arrRowStyles = array();
		$arrContainerStyles = array();
		$arrColStyles = array();
		$arrFirstColStyles = array();
		$arrLastColStyles = array();
		$arrAddonsStyles = array();
		
		
		foreach($options as $key => $value){
			
			switch($key){
				case "row_container_width":
					$arrContainerStyles["max-width"] = UniteFunctionsUC::normalizeSize($value);
				break;
				case "col_gutter":
					$arrColStyles["padding-left"] = UniteFunctionsUC::normalizeSize($value);
					$arrColStyles["padding-right"] = UniteFunctionsUC::normalizeSize($value);
				break;
				case "col_border_gutter":
					$arrFirstColStyles["padding-left"] = UniteFunctionsUC::normalizeSize($value);
					$arrLastColStyles["padding-right"] = UniteFunctionsUC::normalizeSize($value);
				break;
				case "row_gutter":
					$arrRowStyles["padding-top"] = UniteFunctionsUC::normalizeSize($value);
					$arrRowStyles["padding-bottom"] = UniteFunctionsUC::normalizeSize($value);
				break;
				case "space_between_addons":
					$arrAddonsStyles["margin-top"] = UniteFunctionsUC::normalizeSize($value);
				break;
			}
		}
		
		$gridID = "#".$this->gridHtmlID;
		
		$css = "";
		
		$css .= UniteFunctionsUC::arrStyleToStrStyle($arrRowStyles, "{$gridID} .uc-grid-row");
		$css .= UniteFunctionsUC::arrStyleToStrStyle($arrContainerStyles, "{$gridID} .uc-grid-row .uc-grid-row-container");
		$css .= UniteFunctionsUC::arrStyleToStrStyle($arrColStyles, "{$gridID} .uc-grid-row .uc-grid-col");
		$css .= UniteFunctionsUC::arrStyleToStrStyle($arrFirstColStyles, "{$gridID} .uc-grid-row .uc-grid-col.uc-col-first");
		$css .= UniteFunctionsUC::arrStyleToStrStyle($arrLastColStyles, "{$gridID} .uc-grid-row .uc-grid-col.uc-col-last");
		$css .= UniteFunctionsUC::arrStyleToStrStyle($arrAddonsStyles, "{$gridID} .uc-grid-col .uc-grid-col-addon");
		
		
		//row title
		$rowTitleCss = $this->getOption("row_title_global_css");
		$rowTitleLocalCss = $this->getOption("row_title_css");
		
		$rowTitleCss = trim($rowTitleCss);
		$rowTitleLocalCss = trim($rowTitleLocalCss);
		
		$rowTitleCssType = $this->getOption("row_titles_css_type");
		if($rowTitleCssType == "override")
			$rowTitleCss = $rowTitleLocalCss;
		else{
			if($rowTitleLocalCss)
				$rowTitleCss .= " ".$rowTitleLocalCss;
		}
		
		if($rowTitleCss)
			$css .= "{$gridID} .uc-grid-row .uc-grid-row-container{".$rowTitleCss."}";
		
		if($wrap == false || empty($css))
			return($css);
		
		//wrap the css with the style tag
		$cssWrap = "<style type='text/css'>".self::BR;
		$cssWrap .= self::TAB2."/* layout grid styles */".self::BR;
		$cssWrap .= $css;
		$cssWrap .= "</style>".self::BR;
			
		return($cssWrap);
	}
	
	
	/**
	 * put grid inline css
	 */
	private function putGridCss(){
		
		$css = $this->getGridCss();
		
		HelperUC::putInlineStyle($css);
		
	}
	
	
	/**
	 * get html output
	 */
	public function getHtml(){
		
		$this->validateInited();
		
		self::putIncludeScripts();
		
		$css = "";
		
		if($this->cssToBody == false)
			$this->putGridCss();
		else
			$css = $this->getGridCss(true);
		
		
		$rows = $this->layout->getRowsFront();
				
		$gridID = $this->gridHtmlID;
				
		$html = "";
		if(!empty($css))
			$html .= $css;
		
		$html .= self::TAB."<div id=\"{$gridID}\" class=\"uc-grid-front\">".self::BR;
		$html .= $this->getHtmlRows($rows);
		$html .= self::TAB.'</div>'.self::BR;
		
		return($html);
	}
	
	
	/**
	 * output layout front
	 */
	public function putHtml(){
		
		$html = $this->getHtml();
		
		echo $html;
	}
	
	
}