<?php

defined('ADDON_LIBRARY_INC') or die;

class UniteCreatorAddonsView{
	
	protected $showButtons = true;
	protected $showHeader = true;
	
	
	/**
	 * constructor
	 */
	public function __construct(){
		
		$this->putHtml();
	}
	
	/**
	 * get header text
	 * @return unknown
	 */
	protected function getHeaderText(){
		$headerTitle = __("Manage Addons", ADDONLIBRARY_TEXTDOMAIN);
		return($headerTitle);
	}
	
	
	/**
	 * init the view
	 */
	protected function init(){
		
	}
	
	/**
	 * constructor
	 */
	protected function putHtml(){
		
		$view = UniteCreatorAdmin::getView();
		
		if($view == GlobalsUC::VIEW_ADDONS_LIST)
			UniteProviderAdminUC::validateSingleView($view);
		
		$objManager = new UniteCreatorManagerAddons();
		
		require HelperUC::getPathTemplate("addons");		
	}

}

$pathProviderAddons = GlobalsUC::$pathProvider."views/addons.php";

if(file_exists($pathProviderAddons) == true){
	require_once $pathProviderAddons;
	new UniteCreatorAddonsViewProvider();
}
else{
	new UniteCreatorAddonsView();
}

