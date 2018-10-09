<?php
/**
 * @package Addon Library
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('ADDON_LIBRARY_INC') or die('Restricted access');

class UniteCreatorAddonConfig extends HtmlOutputBaseUC{
	
	const VIEW_COMBINED = "combined";
	const VIEW_TABS = "tabs";
	
	private $startWithAddon = false;
	private $isPreviewMode = false;
	private $startAddon;
	private $hasItems = null;
	private $view = null;
	
	
	/**
	 * construct the object
	 */
	public function __construct(){
		
		$this->view = self::VIEW_COMBINED;
	
	}
	
	
	/**
	 * validate start addon
	 */
	private function valdiateStartAddon(){
		
		if($this->startWithAddon == false)
			UniteFunctionsUC::throwError("No start addon found");
	
	}
	
	
	/**
	 * get preview html
	 */
	private function getHtmlPreview(){
		$html = "";
		
		//preview
		$html .= self::TAB2."<div class='uc-addon-config-preview' style='display:none'>".self::BR;
		$html .= 	self::TAB3."<div class='uc-addon-config-preview-title'>Preview".self::BR;
		$html .= 	self::TAB3."</div>".self::BR;
		
		$html .= 	self::TAB3."<div class='uc-preview-content'>".self::BR;
		
		$html .= 	self::TAB4."<iframe class='uc-preview-iframe'>".self::BR;
		$html .= 	self::TAB4."</iframe>".self::BR;
		
		$html .= 	self::TAB3."</div>".self::BR;
		
		$html .= 	self::TAB2."</div>".self::BR;
		
		return($html);
	}
	
	
	/**
	 * get items html
	 */
	public function getHtmlItems($putMode = false){
		
		$objManager = new UniteCreatorManagerInline();
		if($this->startWithAddon)
			$objManager->setStartAddon($this->startAddon);
		
		
		$html = "";
		
		$html .= self::TAB3."<div class='uc-addon-config-items'>".self::BR;
		
		if($this->view == self::VIEW_COMBINED)
			$html .= self::TAB3."<div class='uc-addon-config-title'>".__("Edit Items", ADDONLIBRARY_TEXTDOMAIN)."</div>".self::BR;
		
		if($this->startWithAddon){
			
			if($putMode == true){
				echo $html;
				$html = "";
				
				$objManager->outputHtml();
			
			}else{
				ob_start();
				$objManager->outputHtml();
				
				$itemsContent = ob_get_contents();
				
				$html .= $itemsContent;
				
				ob_clean();
				ob_end_clean();
			}
		
		}//only if start addon presents
		
		$html .= self::TAB3."</div>".self::BR;
		
		
		if($putMode == true){
			echo $html;
		}else		
			return($html);
	}

	
	/**
	 * get item settings html
	 */
	private function getHtmlSettings($putMode = false){
		
		$html = "";
		
		$html .= 	self::TAB3."<div class='uc-addon-config-settings unite-settings'>".self::BR;
		
		if($putMode == true){
			echo $html;
			$html = "";
		}
		
		if($this->startWithAddon == true){
		
			if($putMode == true)
				$this->startAddon->putHtmlConfig();
			else{
				$htmlConfig = $this->startAddon->getHtmlConfig();
				$html .= $htmlConfig;
			}
		}
		
		$html .= self::TAB3."</div>".self::BR;	//settings
		
		
		if($putMode == true)
			echo $html;
		else		
			return($html);
		
	}
	
	
	/**
	 * put html frame of the config
	 */
	public function getHtmlFrame($putMode = false){
				
		$title = __("Addon Title", ADDONLIBRARY_TEXTDOMAIN);
		$this->valdiateStartAddon();
		
		$addHtml = "";
		$title = $this->startAddon->getTitle(true);
		$title .= " - ".__("Config", ADDONLIBRARY_TEXTDOMAIN);
		
		$titleSmall = $this->startAddon->getTitle(true);
		
		$addonName = $this->startAddon->getNameByType();
		$addonID = $this->startAddon->getID();
		$addonType = $this->startAddon->getType();
		
		$options = $this->startAddon->getOptions();
		$urlIcon = $this->startAddon->getUrlIcon();
		
		$options["title"] = $this->startAddon->getTitle();
		$options["url_icon"] = $urlIcon;
		$options["addon_name"] = $addonName;
		$options["addon_id"] = $addonID;
		$options["addon_type"] = $addonType;
		
		$strOptions = UniteFunctionsUC::jsonEncodeForHtmlData($options,"options");
		
		$addHtml .= " data-name=\"{$addonName}\" data-addontype=\"{$addonType}\" {$strOptions} ";
		$addHtml .= " data-view=\"{$this->view}\"";
		
		$html = "";
		
		//settings
		$html .= self::TAB. "<div id='uc_addon_config' class='uc-addon-config' {$addHtml}>".self::BR;
		
		//set preview style
		$styleConfigTable = "";
		if($this->isPreviewMode == true)
			$styleConfigTable = "style='display:none'";
		
		if($this->view == self::VIEW_TABS){
			$html .= self::TAB2."<div id='uc_addon_config_tabs' class='uc-addon-config-tabs-wrapper'>".self::BR;
			$html .= self::TAB3."<a href='javascript:void(0)' data-name='config' onfocus='this.blur()' class='uc-addon-config-tab uc-tab-selected'>".__("Config", ADDONLIBRARY_TEXTDOMAIN)."</a>".self::BR;
			$html .= self::TAB3."<a href='javascript:void(0)' data-name='items' onfocus='this.blur()' class='uc-addon-config-tab uc-last-tab'>".__("Items", ADDONLIBRARY_TEXTDOMAIN)."</a>".self::BR;
			
			$html .= self::TAB2."<div class='uc-addon-config-tabs-addontitle'>{$titleSmall}</div>".self::BR;
			
			$html .= self::TAB2."</div>".self::BR;
						
		}
		
		//put table
		if($this->view == self::VIEW_COMBINED){
			
			if($this->hasItems == true){
				$html .= self::TAB2."<table id='uc_addon_config_table' class='uc-addon-config-table' {$styleConfigTable}>".self::BR;
				$html .= self::TAB3."<tr>".self::BR;
				$html .= self::TAB4."<td class='uc-addon-config-cell-left'>".self::BR;
				$html .= self::TAB5."<div class='uc-addon-config-left'>".self::BR;
			}
			
			//put title
			$html .= 	self::TAB3."<div class='uc-addon-config-title'>$title</div>".self::BR;
		}
		
		if($this->view == self::VIEW_TABS){
			
			$html .= self::TAB2."<div id='uc_addon_config_tab_config' class='uc-addon-config-tab-content'>".self::BR;
		}
		
		//put settings
		if($putMode == true){
			echo $html;
			$html = "";
			$this->getHtmlSettings(true);
		}else{
			$html .= $this->getHtmlSettings();
		}
		
		if($this->view == self::VIEW_TABS){
			$html .= self::TAB2."</div>";
		}
		
		if($this->view == self::VIEW_COMBINED && $this->hasItems){
		
			$html .= self::TAB5."</div>".self::BR;
			$html .= self::TAB4."</td>".self::BR;
		
			//end cell left
			$html .= self::TAB4."<td class='uc-addon-config-cell-right'>".self::BR;
		}
		
		if($this->view == self::VIEW_TABS){
			
			$html .= self::TAB2."<div id='uc_addon_config_tab_items' class='uc-addon-config-tab-content' style='display:none'>".self::BR;
		}
		
		//put items
		if($this->hasItems == true){
			
			if($putMode == true){
				echo $html;
				$html = "";
				$this->getHtmlItems(true);
			}else{
				$html .= $this->getHtmlItems();
			}
		}
		
		if($this->view == self::VIEW_TABS){
			$html .= self::TAB2."</div>";
		}
		
		if($this->view == self::VIEW_COMBINED && $this->hasItems){
			
			$html .= self::TAB4."</td>".self::BR;		
			
			//end right cell
		
			$html .= self::TAB3."</tr>".self::BR;
			$html .= self::TAB2."</table>".self::BR;
		}
			
		//end preview table
		$html .= $this->getHtmlPreview();

		$html .= self::TAB."</div>".self::BR;	//main wrapper
		
		if($putMode == true)
			echo $html;
		else
			return($html);
	}
	
	
	/**
	 * put html frame
	 */
	public function putHtmlFrame(){
		$this->getHtmlFrame(true);
	}
	
	
	
	/**
	 * set to start with preview
	 */
	public function startWithPreview($isPreview){
		
		$this->isPreviewMode = $isPreview;
	}
	
	
	/**
	 * check tabs view if relevant
	 */
	public function checkTabsView(){
		
		if($this->view == self::VIEW_TABS && $this->hasItems == false)
			$this->view = self::VIEW_COMBINED;
	}
	
	
	/**
	 * set start addon
	 */
	public function setStartAddon(UniteCreatorAddon $objAddon){
		$this->startWithAddon = true;
		
		$this->startAddon = $objAddon;
				
		$this->hasItems = $this->startAddon->isHasItems();
		
		$this->checkTabsView();
		
	}
	
	
	/**
	 * change view to tabs
	 */
	public function setViewTabs(){
		
		$this->view = self::VIEW_TABS;
		
		$this->checkTabsView();
	}
	
	
}