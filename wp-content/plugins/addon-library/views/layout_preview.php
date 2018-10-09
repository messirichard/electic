<?php

defined('ADDON_LIBRARY_INC') or die;

class UniteCreatorLayoutPreview{
	
	protected $showHeader = false;
	protected $showToolbar = true;
	protected $layoutID;
	protected $layout;
	
	
	/**
	 * constructor
	 */
	public function __construct(){
		
		$layoutID = UniteFunctionsUC::getGetVar("id", null, UniteFunctionsUC::SANITIZE_ID);
		UniteFunctionsUC::validateNotEmpty($layoutID, "Layout ID var");
		
		$this->layoutID = $layoutID;
		
		$this->layout = new UniteCreatorLayout();
		$this->layout->initByID($layoutID);
		
		
	}
	
	
	/**
	 * get header title
	 */
	protected function getHeaderTitle(){
		
		$titleText = $this->layout->getTitle();
		
		$title = __("Preview Layout - ", ADDONLIBRARY_TEXTDOMAIN).$titleText;
		
		return($title);
	}
	
	
	/**
	 * display
	 */
	protected function display(){
		
		$layoutID = $this->layoutID;
		
		require HelperUC::getPathTemplate("layout_preview");
		
	}
	
	
}

$pathProviderLayout = GlobalsUC::$pathProvider."views/layout_preview.php";
require_once $pathProviderLayout;

new UniteCreatorLayoutPreviewProvider();

