<?php

class HelperProviderUC{
	
	
	/**
	 * set general settings
	 */
	public static function setGeneralSettings(UniteCreatorSettings $objSettings){
		
		//add general settings
		$filepathGeneral = GlobalsUC::$pathProvider."settings/general_settings.xml";
		UniteFunctionsUC::validateFilepath($filepathGeneral, "Provider general settings");
		$objSettings->addFromXmlFile($filepathGeneral);
		
		
		//add vc
		$filepathVC = GlobalsUC::$pathProvider."settings/general_settings_vc.xml";
		UniteFunctionsUC::validateFilepath($filepathVC, "Visual composer settings xml file");
		
		$objSettings->addFromXmlFile($filepathVC);
		
		
		return($objSettings);
	}
	
	
	/**
	 * check if layout editor plugin exists, or exists addons for it
	 */
	public static function isLayoutEditorExists(){
		
		$classExists = class_exists("LayoutEditorGlobals");
		if($classExists == true)
			return(true);
	
		return(false);
	}
	
	
	/**
	 * register widgets 
	 */
	public static function registerWidgets(){
		
		$isLayouEditorExists = self::isLayoutEditorExists();
		
		if($isLayouEditorExists == true){
			
			register_widget("AddonLibrary_WidgetLayout");
		}
		
	}
	
	/**
	 * global init function that common to the admin and front
	 */
	public static function globalInit(){
		
		add_filter(UniteCreatorFilters::FILTER_MODIFY_GENERAL_SETTINGS, array("HelperProviderUC", "setGeneralSettings") );
		
		//create_function('', 'return register_widget("AddonLibrary_WidgetLayout");'));
		
		//register the addon library widget
		add_action('widgets_init', array("HelperProviderUC","registerWidgets"));
		
		//dmp("init");exit();
	}
	
	/**
	 * on plugins loaded call plugin
	 */
	public static function onPluginsLoadedCallPlugins(){
		
		do_action("addon_library_register_plugins");
		
		UniteProviderFunctionsUC::doAction(UniteCreatorFilters::ACTION_EDIT_GLOBALS);
		
	}
	
	
	/**
	 * register plugins
	 */
	public static function registerPlugins(){
		
		add_action("plugins_loaded",array("HelperProviderUC","onPluginsLoadedCallPlugins"));
		
	}
	
	
	/**
	 * print custom scripts
	 */
	public static function onPrintFooterScripts($isFront = false){
		
		if($isFront == false){
			
			//print inline html
			$arrHtml = UniteProviderFunctionsUC::getInlineHtml();
			if(!empty($arrHtml)){
				foreach($arrHtml as $html){
					echo $html;
				}
			}
			
		}
			
		//print custom script
		$arrScrips = UniteProviderFunctionsUC::getCustomScripts();
		if(!empty($arrScrips)){
			echo "\n<!--   Addon Library Scripts  --> \n";
			
			echo "<script type='text/javascript'>\n";
			foreach ($arrScrips as $script){
				echo $script."\n";
			}
			echo "</script>";
		}
	
		$arrStyles = UniteProviderFunctionsUC::getCustomStyles();
		if(!empty($arrStyles)){
			echo "\n<!--   Addon Library Styles  --> \n";
			
			echo "<style type='text/css'>";
	
			foreach ($arrStyles as $style){
				echo $style."\n";
			}
	
			echo "</style>";
		}
	
	}
	
	
}