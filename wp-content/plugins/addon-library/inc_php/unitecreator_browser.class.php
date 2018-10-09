<?php
/**
 * @package Addon Library
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('ADDON_LIBRARY_INC') or die('Restricted access');

class UniteCreatorBrowser extends HtmlOutputBaseUC{
	
	private $selectedCatID = null;
	private $inputIDForUpdate = null;		//input for field values update
	
	private $addonType = "";
	
	private $startWithAddon = false;
	private $startAddon = null;
	private $startError = null;
	
	
	/**
	 * get tabs html
	 */
	private function getHtmlTabs($arrCats){
		
		$html = "";
		
		//determine if tabs or dropdown
		$numCats = count($arrCats);
		
		$isDropdown = false;
		$tabsType = "tabs";
		if($numCats >= 7){
			$isDropdown = true;
			$tabsType = "dropdown";
		}
		
		$addHtml = "";
		if($this->startWithAddon == true)
			$addHtml .= "style='display:none'";
		
		$html .= self::TAB2."<div class=\"uc-browser-tabs-wrapper\" data-tabstype=\"$tabsType\" {$addHtml}>".self::BR;
			
		if($this->selectedCatID === null)
			$this->selectedCatID = "all";			
		
		if(is_numeric($this->selectedCatID))
			$this->selectedCatID = (int)$this->selectedCatID;
		
		//add title
		if($isDropdown == true){
			$textSelectCat = __("Select Category: ",ADDONLIBRARY_TEXTDOMAIN);
			$html .= self::TAB3."<div class='uc-browser-tabs-select-wrapper'>".self::BR;
			$html .= self::TAB3."<span class='uc-browser-tabs-title'>{$textSelectCat}</span>".self::BR;
			$html .= self::TAB3."<select class='uc-browser-select-category'>".self::BR;
		}
		
		foreach($arrCats as $catID=>$cat){
			
			$isSelected = false;
			
			if(is_numeric($catID))
				$catID = (int)$catID;
			
			if($this->selectedCatID === $catID)
				$isSelected = true;
			
			$catTitle = UniteFunctionsUC::getVal($cat, "title");
			$catTitle = htmlspecialchars($catTitle);
			
			if($isDropdown == false){		//add tab
					$addClass = "";
					if($isSelected == true)
						$addClass = " uc-tab-selected";
										
					$html .= self::TAB3."<a href=\"javascript:void(0)\" onfocus=\"this.blur()\" class=\"uc-browser-tab{$addClass}\" data-catid=\"{$catID}\">{$catTitle}</a>".self::BR;

			}else{		//add dropdown option
				
				$addHtml = "";
				if($isSelected == true)
						$addHtml = "selected='selected'";
				
				$html .= self::TAB4."<option value=\"{$catID}\" {$addHtml}>$catTitle</option>".self::BR;
				
			}
		
		}
		
		if($isDropdown == true){
			$html .= self::TAB3."</select>";
			$html .= self::TAB3."</div>";
		}
		
		$html .= "<div class='unite-clear'></div>";
		
		$html .= self::TAB2."</div>";	//tabs
		
		return($html);
	}

	
	
	
	/**
	 * get content html
	 */
	private function getHtmlContent($arrCats){
		
		$html = "";
				
		$numCats = count($arrCats);
		
		$addHtml = "";
		if($this->startWithAddon == true)
			$addHtml .= "style='display:none'";
		
		$html .= self::TAB2."<div class=\"uc-browser-content-wrapper\" {$addHtml}>".self::BR;
		
		
		//output addons
		foreach($arrCats as $catID=>$cat){
			
			$style = " style=\"display:none\"";
			if($catID === $this->selectedCatID || $numCats <= 1)
				$style = "";
			
			$html .= self::TAB3."<div id=\"uc_browser_content_{$catID}\" class=\"uc-browser-content\" {$style} >".self::BR;
			
			$arrAddons = UniteFunctionsUC::getVal($cat, "addons");
			if(empty($arrAddons)){
				
				$html .= __("No addons in this category", ADDONLIBRARY_TEXTDOMAIN);
			}
			else{
				
				if(is_array($arrAddons) == false)
					UniteFunctionsUC::throwError("The cat addons array should be array");
				
				foreach($arrAddons as $addon){
				
					$htmlAddon = $this->getHtmlAddon($addon);
				
					$html .= $htmlAddon;
				}
				
			}
		
			$html .= self::TAB3."</div>".self::BR;
		}
		
		$html .= self::TAB2."<div class='unite-clear'></div>".self::BR;
		
		$html .= self::TAB2."</div>".self::BR; //content wrapper
		
		return($html);
	}

	
	/**
	 * get addon html
	 * @param $addon
	 */
	private function getHtmlAddon(UniteCreatorAddon $addon){
	
		$html = "";
				
		$name = $addon->getNameByType();
				
		$name = UniteFunctionsUC::sanitizeAttr($name);
		
		$title = $addon->getTitle(true);
		$description = $addon->getDescription(true);
		$urlIcon = $addon->getUrlIcon();
		
		$id = $addon->getID();
				
		$html .= self::TAB4."<a class=\"uc-browser-addon\" data-id=\"$id\" data-name=\"{$name}\" data-title=\"{$title}\">".self::BR;
		
		$htmlIcon = "<img src='{$urlIcon}' alt='$title'>";
		
		$html .= self::TAB5."<div class=\"uc-browser-addon-icon\">{$htmlIcon}</div>".self::BR;
	
		$html .= self::TAB5."<div class=\"uc-browser-addon-right\">".self::BR;
		$html .= self::TAB6."<div class=\"uc-browser-addon-title\">{$title}</div>".self::BR;
		$html .= self::TAB6."<div class=\"uc-browser-addon-desc\">{$description}</div>".self::BR;
		$html .= self::TAB5."</div>".self::BR;

		$html .= self::TAB4."</a>".self::BR;
	
	
		return($html);
	}
	
	
	/**
	 * get browser html
	 */
	private function getHtml($putMode = false){

		$objAddons = new UniteCreatorAddons();
		
		$arrCats = $objAddons->getAddonsWidthCategories(true, false, $this->addonType);
				
		$numCats = count($arrCats);
		
		$html = "";
		//$html = self::TAB."<!----- START BROWSER ----> ".self::BR;
		
		$addHtml = "";
		if(!empty($this->inputIDForUpdate))
			$addHtml .= " data-inputupdate=\"".$this->inputIDForUpdate."\"";
		
		if($this->startWithAddon == true){
			
			$addonName = $this->startAddon->getNameByType();
			$addonName = htmlspecialchars($addonName);
			$addHtml .= " data-startaddon='{$addonName}'";
		}
		
		$addonType = $this->addonType;
		$addHtml .= " data-addontype='{$addonType}'";
		
		$html .= self::TAB."<div class=\"uc-browser-wrapper\" {$addHtml}>".self::BR;
		
		//output tabs
		if($numCats > 1)
			$html .= $this->getHtmlTabs($arrCats);
		
		//output content
		$html .= $this->getHtmlContent($arrCats);
		
		//output back button
		$buttonAddHtml = "style='display:none'";
		if($this->startWithAddon == true)
			$buttonAddHtml = "";
		
		$html .= self::TAB2."<a href='javascript:void(0)' class='uc-browser-button-back unite-button-secondary' {$buttonAddHtml}>".__("Choose Another Addon", ADDONLIBRARY_TEXTDOMAIN)."</a>".self::BR;
		
		$html .= self::TAB2."<div class='uc-browser-addon-config-wrapper'>".self::BR;
		
		//output config if needed
		if($this->startWithAddon){
			
			$objAddonConfig->setStartAddon($this->startAddon);
			
			if($putMode == true){
				echo $html;
				$html = "";
				$objAddonConfig->putHtmlFrame();
			}else{
				$htmlFrame = $objAddonConfig->getHtmlFrame();
				$html .= self::BR. $htmlFrame;
			}
		}
		
		$html .= "</div>";
		
		//put loader
		$html .= self::TAB3."<span id='uc_browser_loader' class='uc-browser-loader loader_text' style='display:none'>".__("Loading Addon...",ADDONLIBRARY_TEXTDOMAIN)."</span>".self::BR;
		$html .= self::TAB3."<div id='uc_browser_error' class='uc-browser-error unite_error_message' style='display:none'></div>".self::BR;
		
		$html .= self::TAB."</div>"; //wrapper

		if($putMode == true)
			echo $html;
		else
			return($html);
		
	}
	
	
	/**
	 * put html
	 */
	private function putHtml(){
		
		$this->getHtml(true);
	}
	
	
	/**
	 * put scripts
	 */
	public function putScripts(){
		
		UniteCreatorAdmin::onAddScriptsBrowser();
	}
	
	
	/**
	 * set browser addon type
	 */
	public function initAddonType($addonType){
		
		$this->addonType = $addonType;
		
	}
	
	
	/**
	 * put browser
	 */
	public function putBrowser($putMode = true){
				
		if($putMode == false){
			$html = $this->getHtml();
			return($html);
		}
		
		$this->putHtml();
	}
	
	
	/**
	 * put scripts and browser
	 */
	public function putScriptsAndBrowser($getHTML = false){
		
		try{
			
			$this->putScripts();
			$html = $this->putBrowser($getHTML);
			
			if($getHTML == true)
				return($html);
			else
				echo $html;
			
		}catch(Exception $e){
			
			$message = $e->getMessage();
			
			$trace = "";
			if(GlobalsUC::SHOW_TRACE == true)
				$trace = $e->getTraceAsString();
			
			$htmlError = HelperUC::getHtmlErrorMessage($message, $trace);
			
			return($htmlError);
		}
		
	}
	
	
	/**
	 * set input id for values update
	 */
	public function setInputIDForValuesUpdate($inputID){
		$this->inputIDForUpdate = $inputID;
	}
	
	
	
	/**
	 * set init data json format
	 */
	public function setJsonInitData($jsonData){
		
		if(empty($jsonData))
			return(false);
		
		$arrData = @json_decode($jsonData);
		if(!is_object($arrData))
			return(false);
		
		$arrData = UniteFunctionsUC::convertStdClassToArray($arrData);
		
		$addonName = UniteFunctionsUC::getVal($arrData, "name");
		
		if(empty($addonName))
			return(false);
		
		$this->startWithAddon = true;
		
		$settingsValues = UniteFunctionsUC::getVal($arrData, "values");
		
		try{
			
			$this->startAddon = new UniteCreatorAddon();
			$this->startAddon->initByName($addonName);
			if(!empty($settingsValues))
				$this->startAddon->setParamsValues($settingsValues);
			
		}catch(Exception $e){
			$message = $e->getMessage();
			$this->startError = $message;
			
		}
		
		
	}
	
	
}
