<?php

defined('ADDON_LIBRARY_INC') or die;


class AddonLibraryViewLayout{
	
	protected $showButtons = true;
	protected $isEditMode = false;
	protected $showHeader = true;
	protected $layoutID;
	protected $objLayout;
	protected $shortcodeWrappers = "{}";
	
	/**
	 * the constructor
	 */
	public function __construct(){
		
		$layoutID = UniteFunctionsUC::getGetVar("id", null, UniteFunctionsUC::SANITIZE_ID);
		$this->layoutID = $layoutID;
		
		$this->objLayout = null;
		
		if(!empty($layoutID)){
			$this->objLayout = new UniteCreatorLayout();
			$this->objLayout->initByID($layoutID);
		}
			
	}
	
	
	/**
	 * get header title
	 */
	protected function getHeaderTitle(){
		
		if(empty($this->objLayout)){
			
			$title = __("New Layout", ADDONLIBRARY_TEXTDOMAIN);
		
		}else{
			$title = __("Edit Layout - ", ADDONLIBRARY_TEXTDOMAIN);
			$title .= $this->objLayout->getTitle();
		}
		
		return($title);
	}
	
	
	//protected function 
	
	/**
	 * display
	 */
	protected function display(){
		
		$layoutID = $this->layoutID;
		
		$objGridEditor = new UniteCreatorGridBuilderProvider();
		$objGridEditor->setGridID("uc_grid_builder");
		
		$title = null;
		
		$objLayout = $this->objLayout;
		
		//init the layout object if in edit mode
		if(!empty($layoutID)){
			$this->isEditMode = true;
			
			$objGridEditor->initByLayout($objLayout);
		
			$title = $objLayout->getTitle();
		}
		
		require HelperUC::getPathViewObject("layouts_view.class");
		require HelperUC::getPathViewProvider("provider_layouts_view.class");
		
		$objLayouts = new UniteCreatorLayoutsViewProvider();
		
		require HelperUC::getPathTemplate("layout_edit");
		
	}
	
	
}

$pathProviderLayout = GlobalsUC::$pathProvider."views/layout.php";

require_once $pathProviderLayout;

new AddonLibraryViewLayoutProvider();
