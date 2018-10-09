<?php
/**
 * @package Addon Library
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('ADDON_LIBRARY_INC') or die('Restricted access');


	class GlobalsUC{
		
		const SHOW_TRACE = false;
		const SHOW_TRACE_FRONT = false;
		
		const ENABLE_TRANSLATIONS = false;
		
		const PLUGIN_TITLE = "Addon Library";
		const PLUGIN_NAME = "unitecreator";
		
		const TABLE_ADDONS_NAME = "addonlibrary_addons";
		const TABLE_LAYOUTS_NAME = "addonlibrary_layouts";
		const TABLE_CATEGORIES_NAME = "addonlibrary_categories";
		
		const VIEW_ADDONS_LIST = "addons";
		const VIEW_EDIT_ADDON = "addon";
		const VIEW_ASSETS = "assets";
		const VIEW_SETTINGS = "settings";
		const VIEW_TEST_ADDON = "testaddon";
		const VIEW_MEDIA_SELECT = "mediaselect";
		const VIEW_LAYOUTS_LIST = "layouts";
		const VIEW_LAYOUT = "layout";
		const VIEW_LAYOUT_PREVIEW = "layout_preview";
		const VIEW_LAYOUTS_SETTINGS = "layouts_settings";
		
		const DEFAULT_JPG_QUALITY = 81;
		const THUMB_WIDTH = 300;
		const THUMB_WIDTH_LARGE = 700;
		
		const THUMB_SIZE_NORMAL = "size_normal";
		const THUMB_SIZE_LARGE = "size_large";
		
		const DIR_THUMBS = "unitecreator_thumbs";
		const DIR_THUMBS_ELFINDER = "elfinder_tmb";
		
		const DIR_THEME_ADDONS = "al_addons";
		
		public static $permisison_add = false;
		public static $blankWindowMode = false;
		
		public static $view_default;
		
		public static $table_addons;
		public static $table_categories;
		public static $table_layouts;
		
		public static $pathSettings;
		public static $filepathItemSettings;
		public static $pathPlugin;
		public static $pathTemplates;
		public static $pathViews;
		public static $pathViewsObjects;
		public static $pathLibrary;
		public static $pathAssets;
		public static $pathProvider;
		public static $pathProviderViews;
		public static $pathProviderTemplates;
		
		public static $url_base;
		public static $url_images;
		public static $url_component_client;
		public static $url_component_admin;
		public static $url_ajax;
		public static $url_ajax_front;
		public static $url_default_addon_icon;
		
		public static $urlPlugin;
		public static $url_provider;
		public static $url_assets;
		public static $url_assets_libraries;
		public static $url_assets_internal;
		
		public static $is_admin;
		public static $path_base;
		public static $path_cache;
		public static $path_images;
		
		public static $layoutShortcodeName = "unite_addon_layout";
		public static $layoutsAddonType = null;
		
		public static $arrClientSideText = array();
		
		
		/**
		 * init globals
		 */
		public static function initGlobals(){

			UniteProviderFunctionsUC::initGlobalsBase();
			
			UniteFunctionsUC::validateNotEmpty(self::$view_default, "default view");
			
			self::$pathProvider = self::$pathPlugin."provider/";
			self::$pathTemplates = self::$pathPlugin."views/templates/";
			self::$pathViews = self::$pathPlugin."views/";
			self::$pathViewsObjects = self::$pathPlugin."views/objects/";
			self::$pathLibrary = self::$pathPlugin."library/";
			self::$pathSettings = self::$pathPlugin."settings/";
			
			self::$pathProviderViews = self::$pathProvider."views/";
			self::$pathProviderTemplates = self::$pathProvider."views/templates/";
			
			self::$filepathItemSettings = self::$pathSettings."item_settings.php";
									
			//check for wp version
			UniteFunctionsUC::validateNotEmpty(GlobalsUC::$url_assets_internal, "assets internal");
			
			
			//GlobalsUC::printVars();
		}

		
		/**
		 * print all globals variables
		 */
		public static function printVars(){
			$methods = get_class_vars( "GlobalsUC" );
			dmp($methods);
			exit();
		}
		
	}

	//init the globals
	GlobalsUC::initGlobals();
	
?>
