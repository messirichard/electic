<?php
/**
 * @package Addon Library
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('ADDON_LIBRARY_INC') or die('Restricted access');

class UniteCreatorOutput extends HtmlOutputBaseUC{
	
	private static $serial = 0;
	
	const TEMPLATE_HTML = "html";
	const TEMPLATE_CSS = "css";
	const TEMPLATE_JS = "js";
	const TEMPLATE_HTML_ITEM = "item";
	const TEMPLATE_HTML_ITEM2 = "item2";
	
	private $addon;
	private $isInited = false;
	private $objTemplate;
	private $isItemsExists = false;
	private $paramsCache = null;
	
	private static $arrUrlCacheCss = array();
	private static $arrHandleCacheCss = array();
	
	private static $arrUrlCacheJs = array();
	private static $arrHandleCacheJs = array();
	
	
	/**
	 * construct
	 */
	public function __construct(){
		$this->addon = new UniteCreatorAddon();
		$this->objTemplate = new UniteCreatorTemplateEngine();
	}
	
	/**
	 * validate inited
	 */
	private function validateInited(){
		if($this->isInited == false)
			UniteFunctionsUC::throwError("Output error: addon not inited");
		
	}
	
	private function ___________INCLUDES_________(){}
	
	
	/**
	 * cache include
	 */
	private function cacheInclude($url, $handle, $type){
		
		if($type == "css"){	  //cache css
			
			self::$arrUrlCacheCss[$url] = true;
			self::$arrHandleCacheCss[$handle] = true;
			
		}else{
				//cache js
				
			self::$arrUrlCacheJs[$url] = true;
			self::$arrHandleCacheJs[$handle] = true;
		
		}
		
	}
	
	/**
	 * check that the include located in cache
	 */
	private function isIncludeInCache($url, $handle, $type){
		
		if(empty($url) || empty($handle))
			return(false);
		
		if($type == "css"){
			
			if(isset(self::$arrUrlCacheCss[$url]))
				return(true);
			
			if(isset(self::$arrHandleCacheCss[$handle]))
				return(true);
			
		}else{	//js
			
			if(isset(self::$arrUrlCacheJs[$url]))
				return(true);
			
			if(isset(self::$arrHandleCacheJs[$handle]))
				return(true);
			
		}
		
		return(false);
	}
	
	
	
	/**
	 * check include condition
	 * return true  to include and false to not include
	 */
	private function checkIncludeCondition($condition){
		if(empty($condition))
			return(true);
		
		if(!is_array($condition))
			return(true);
		
		$name = UniteFunctionsUC::getVal($condition, "name");
		$value = UniteFunctionsUC::getVal($condition, "value");

		if(empty($name))
			return(true);

		if($name == "never_include")
			return(false);
		
		$params = $this->getAddonParams();
		
		if(array_key_exists($name, $params) == false)
			return(true);
		
		$paramValue = $params[$name];
			
		if($paramValue === $value)
			return(true);
		else
			return(false);
	}
	
	
	
	
	/**
	 * process includes list, get array("url", type)
	 */
	private function processIncludesList($arrIncludes, $type){
		
		$arrIncludesProcessed = array();
		
		foreach($arrIncludes as $handle => $include){
			$urlInclude = $include;
			
			if(is_numeric($handle))
				$handle = null;
			
			if(is_array($include)){
				$urlInclude = UniteFunctionsUC::getVal($include, "url");
				$condition = UniteFunctionsUC::getVal($include, "condition");
				$isIncludeByCondition = $this->checkIncludeCondition($condition);
				
				if($isIncludeByCondition == false)
					continue;
			}
		
			$arrIncludeNew = array();
			$arrIncludeNew["url"] = $urlInclude;
			$arrIncludeNew["type"] = $type;
			
			if(!empty($handle))
				$arrIncludeNew["handle"] = $handle;
			
			$arrIncludesProcessed[] = $arrIncludeNew;
			
		}
		
		return($arrIncludesProcessed);
	}
	
	
	/**
	 * get processed includes list
	 * includes type = js / css / all
	 */
	private function getProcessedIncludes($includeLibraries = false, $processProviderLibrary = false, $includesType = "all"){
		
		$this->validateInited();
		
		//get list of js and css
		$arrLibJs = array();
		$arrLibCss = array();
		
		if($includeLibraries == true){
			
			//get all libraries without provider process
			$arrLibraries = $this->addon->getArrLibraryIncludesUrls($processProviderLibrary);
		}
		
		$arrIncludesJS = array();
		$arrIncludesCss = array();
		
		//get js
		if($includesType != "css"){
			
			if($includeLibraries)
				$arrLibJs = $arrLibraries["js"];
			
			$arrIncludesJS = $this->addon->getJSIncludes();
			$arrIncludesJS = array_merge($arrLibJs, $arrIncludesJS);
			$arrIncludesJS = $this->processIncludesList($arrIncludesJS, "js");
			
		}
		
		//dmp($arrIncludesJS);exit();
		
		//get css
		if($includesType != "js"){
			if($includeLibraries)
				$arrLibCss = $arrLibraries["css"];
			
			$arrIncludesCss = $this->addon->getCSSIncludes();
			$arrIncludesCss = array_merge($arrLibCss, $arrIncludesCss);
			$arrIncludesCss = $this->processIncludesList($arrIncludesCss, "css");
		}
		
		$arrProcessedIncludes = array_merge($arrIncludesJS, $arrIncludesCss);
		
		return($arrProcessedIncludes);
	}
	
	
	/**
	 * get includes html
	 */
	private function getHtmlIncludes($arrIncludes = null){
		
		$this->validateInited();
		
		if(empty($arrIncludes))
			return("");
		
		$addonName = $this->addon->getName();
		
		$html = "";
		
		foreach($arrIncludes as $include){
			
			$type = $include["type"];
			$url = $include["url"];
			$handle = UniteFunctionsUC::getVal($include, "handle");
			
			if(empty($handle))
				$handle = HelperUC::getUrlHandle($url, $addonName);
			
			$isInCache = $this->isIncludeInCache($url, $handle, $type);
			if($isInCache == true)
				continue;
			
			$this->cacheInclude($url, $handle, $type);
			
			switch($type){
				case "js":
					$html .= self::TAB2."<script type='text/javascript' src='{$url}'></script>".self::BR;
					break;
				case "css":
					$cssID = "{$handle}-css";
					$html .= self::TAB2."<link id='{$cssID}' href='{$url}' type='text/css' rel='stylesheet' >".self::BR;
					break;
				default:
					UniteFunctionsUC::throwError("Wrong include type: {$type} ");
				break;
			}
			
		}
		
		return($html);
	}
	
	
	/**
	 * process includes
	 * includes type = "all,js,css"
	 */
	public function processIncludes($includesType = "all"){
		
		$arrIncludes = $this->getProcessedIncludes(true, true, $includesType);
		
		$addonName = $this->addon->getName();
		
		foreach($arrIncludes as $include){
							
			$type = $include["type"];
			$url = $include["url"];
			$handle = UniteFunctionsUC::getVal($include, "handle");
			
			if(empty($handle))
				$handle = HelperUC::getUrlHandle($url, $addonName);
			
			$isInCache = $this->isIncludeInCache($url, $handle, $type);
			if($isInCache == true)
				continue;
			
			switch($type){
				case "js":
					UniteProviderFunctionsUC::addScript($handle, $url);
				break;
				case "css":
						UniteProviderFunctionsUC::addStyle($handle, $url);
				break;
				default:
					UniteFunctionsUC::throwError("Wrong include type: {$type} ");
				break;
			}
		
		}
	
	
	}
	
	
	private function ___________GENERAL_________(){}
	
	
	/**
	 * place output by shortcode
	 */
	public function getHtmlBody($scriptHardCoded = true, $putCssIncludes = false){
		
		$this->validateInited();
		
		$title = $this->addon->getTitle(true);
		
		$html = $this->objTemplate->getRenderedHtml(self::TEMPLATE_HTML);
		$css = $this->objTemplate->getRenderedHtml(self::TEMPLATE_CSS);
		$js = $this->objTemplate->getRenderedHtml(self::TEMPLATE_JS);
		
		
		//get css includes if needed
		$arrCssIncludes = array();
		if($putCssIncludes == true)
			$arrCssIncludes = $this->getProcessedIncludes(true, true, "css");
		
			
		$output = "<!-- start {$title} -->";
		
		//add css includes if needed
		if(!empty($arrCssIncludes)){
			$htmlIncludes = $this->getHtmlIncludes($arrCssIncludes);
			$output .= "\n".$htmlIncludes;
		}
			
		
		//add css
		if(!empty($css)){
			$output .= "\n			<style type=\"text/css\">{$css}</style>";
		}
		
		//add html
		
		$output .= "\n\n			".$html;

		//output js
		if(!empty($js)){
			
			$title = $this->addon->getTitle();
			
			if($scriptHardCoded == false)
				$js = "// $title scripts: \n".$js;
			
			if($scriptHardCoded == false)
				UniteProviderFunctionsUC::printCustomScript($js);
			else{
				$output .= "\n\n			<script type=\"text/javascript\">";
				$output .= "\n			".$js;
				$output .= "\n			</script>";
			}
			
		}
			
		$output .= "\n			<!-- end {$title} -->";
		
		return($output);
	}
	
	
	/**
	 * get addon preview html
	 */
	public function getPreviewHtml(){
		
		$this->validateInited();
		
		$title = $this->addon->getTitle();
		$title .= " ". __("Preview",ADDONLIBRARY_TEXTDOMAIN);
		$title = htmlspecialchars($title);

		//get libraries, but not process provider
		$arrIncludes = $this->getProcessedIncludes(true, false);
		
		$htmlInlcudes = $this->getHtmlIncludes($arrIncludes);
		
		$htmlBody = $this->getHtmlBody();
		
		//set options
		
		$options = $this->addon->getOptions();
				
		$bgCol = $this->addon->getOption("preview_bgcol");
		$previewSize = $this->addon->getOption("preview_size");
		
		$previewWidth = "100%";
		
		switch($previewSize){
			case "column":
				$previewWidth = "300px";
			break;
			case "custom":
				$previewWidth = $this->addon->getOption("preview_custom_width");
				if(!empty($previewWidth)){
					$previewWidth = (int)$previewWidth;
					$previewWidth .= "px";
				}
			break;
		}
		
		
		$style = "";
		$style .= "max-width:{$previewWidth};";
		$style .= "background-color:{$bgCol};";
		
		$urlPreviewCss = GlobalsUC::$urlPlugin."css/unitecreator_preview.css";
		
		$html = "<!DOCTYPE html>".self::BR;
		$html .= "<html>".self::BR;
		
		//output head
		$html .= self::TAB."<head>".self::BR;
		$html .= self::TAB2."<title>{$title}</title>".self::BR;
		$html .= self::TAB2."<link rel='stylesheet' href='{$urlPreviewCss}' type='text/css'>".self::BR;
		$html .= $htmlInlcudes;
		$html .= self::TAB."</head>".self::BR;

		//output body
		$html .= self::TAB."<body>".self::BR;
		$html .= self::BR.self::TAB2."<div class='uc-preview-wrapper' style='{$style}'>";
		$html .= self::BR.$htmlBody;
		$html .= self::BR.self::TAB2."</div>";
		$html .= self::BR.self::TAB."</body>".self::BR;
		
		$html .= "</html>";
		
		return($html);
	}
	
	
	/**
	 * get addon contstant data that will be used in the template
	 */
	public function getConstantData(){
		
		$this->validateInited();
		
		if(!empty(self::$arrConstantData))
			return(self::$arrConstantData);
		
		$data = array();
		
		$prefix = "ucid";
		if($this->isInited)
			$prefix = "uc_".$this->addon->getName();
		
		//add serial number:
		self::$serial++;
		$data["uc_serial"] = self::$serial;
		$data["uc_id"] = $prefix.self::$serial;
		
		//add assets url
		$urlAssets = $this->addon->getUrlAssets();
		if(!empty($urlAssets))
			$data["uc_assets_url"] = $urlAssets;
		
		$data = UniteProviderFunctionsUC::addSystemConstantData($data);
		
		return($data);
	}
	
	
	/**
	 * get item extra variables
	 */
	public function getItemConstantDataKeys(){
		
		$arrKeys = array(
				"item_id",
				"item_index"
		);
		
		return($arrKeys);
	}
	
	
	
	/**
	 * get constant data keys
	 */
	public function getConstantDataKeys(){
		$constantData = $this->getConstantData();
		$arrKeys = array_keys($constantData);
		return($arrKeys);
	}
	
	
	/**
	 * get addon params
	 */
	private function getAddonParams(){
		if(!empty($this->paramsCache))
			return($this->paramsCache);
		
		$this->paramsCache = $this->addon->getProcessedMainParamsValues();
		return($this->paramsCache);
	}
	
	
	/**
	 * init the template
	 */
	private function initTemplate(){
		
		$this->validateInited();
		
		
		//set params
		$arrData = $this->getConstantData();
		$arrParams = $this->getAddonParams();
				
		$arrData = array_merge($arrData, $arrParams);
		
		$this->objTemplate->setParams($arrData);
				
		//set templates
		$html = $this->addon->getHtml();
		$css = $this->addon->getCss();
		$js = $this->addon->getJs();
		
		$this->objTemplate->addTemplate(self::TEMPLATE_HTML, $html);
		$this->objTemplate->addTemplate(self::TEMPLATE_CSS, $css);
		$this->objTemplate->addTemplate(self::TEMPLATE_JS, $js);
		
		//set items template
		if($this->isItemsExists == true){
			
			$arrItemData = $this->addon->getProcessedItemsData();
						
			$this->objTemplate->setArrItems($arrItemData);
			
			$htmlItem = $this->addon->getHtmlItem();
			$this->objTemplate->addTemplate(self::TEMPLATE_HTML_ITEM, $htmlItem);
			
			$htmlItem2 = $this->addon->getHtmlItem2();
			$this->objTemplate->addTemplate(self::TEMPLATE_HTML_ITEM2, $htmlItem2);
			
		}
		
	}
		
	
	/**
	 * init by addon
	 */
	public function initByAddon(UniteCreatorAddon $addon){
		
		if(empty($addon))
			UniteFunctionsUC::throwError("Wrong addon given");
		
		$this->isInited = true;
		$this->addon = $addon;
		$this->isItemsExists = $this->addon->isHasItems();
		
		$this->initTemplate();
	}
	
	
}

?>