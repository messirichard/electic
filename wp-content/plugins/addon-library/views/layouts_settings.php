<?php

defined('ADDON_LIBRARY_INC') or die;


class UniteCreatorViewLayoutsSettings extends UniteCreatorSettingsView{
	
	
	/**
	 * constructor
	 */
	public function __construct(){
		
		$this->headerTitle = __("Layouts Global Settings", ADDONLIBRARY_TEXTDOMAIN);
		$this->saveAction = "update_global_layout_settings";
		$this->textButton = __("Save Layout Settings", ADDONLIBRARY_TEXTDOMAIN);
		
		
		//set settings object
		$this->objSettings = UniteCreatorLayout::getGlobalSettingsObject();
		
		$this->display();
	}
	
}


new UniteCreatorViewLayoutsSettings();
