<?php
/**
 * @package Addon Library
 * @author UniteCMS.net / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */

defined('ADDON_LIBRARY_INC') or die('Restricted access');

/**
 * visual composer integration class
 *
 */
	class UniteVcIntegrateUC extends UniteCreatorPluginBase{
		
		const ADDONTYPE_VC = "vc";
		private static $arrCats = null;
		private $lastPostID = null;
		
		private $isMetaboxEnabled = false;
		
		const ACTION_ADD_METABOXES = "add_meta_boxes";
		const ACTION_ADMIN_FOOTER = "admin_footer";
		
		private $vcFolder;
		
		
		
		/**
		 * return if visual composer functions exists
		 */
		public static function isVCExists(){
			
			if(function_exists('vc_map'))
				return(true);
			
			return(false);
		}
		
		/**
		 * get array of vc post types. get all post types available, 
		 * because some plugins could change it
		 */
		private static function getVcOriginalPostTypes(){
			
			if(function_exists("vc_user_access") == false)
				return(array());
			
			$arrOriginalTypes = array_keys( vc_user_access()->part( 'post_types' )->getAllCaps() );
			if(empty($arrOriginalTypes))
				$arrOriginalTypes = array();
			
			return($arrOriginalTypes);
		}
		
		
		/**
		 * check if visual composer in on current page
		 */
		public static function isVcOnAdminPage(){
			
			$screen = @get_current_screen();
			if(empty($screen))
				return(false);
			
			$baseType = $screen->base;
			if($baseType != "post")
				return(false);
			
			
			//check if can be included in all pages
			$includeInPostTypes = HelperUC::getGeneralSetting("vc_post_types");
			if($includeInPostTypes == "all")
				return(true);
			
			
			if(function_exists("vc_editor_post_types") == false)
				return(false);
			
			$arrVcPostTypes = vc_editor_post_types();
			$arrVcPostTypes[] = "templatera";
			
			$found = in_array( get_post_type(), $arrVcPostTypes);
			
			//if not found, check in original post types
			if($found == false){
				$arrOriginalTypes = self::getVcOriginalPostTypes();
				$found = in_array( get_post_type(), $arrOriginalTypes);
			}
			
			
			return($found);	
		}
		
		
		/**
		 * return if vc addons enabled by general setting
		 */
		public static function isVCAddonsEnabled(){
			
			$isEnabled = HelperUC::getGeneralSetting("vc_enable");
			$isEnabled = UniteFunctionsUC::strToBool($isEnabled);
			
			return($isEnabled);
		}
		
		
		/**
		 * on add outside scripts
		 */
		public static function onAddOutsideScripts(){
			
			$isVCPage = self::isVcOnAdminPage();
			
			if($isVCPage == false)
				return(false);
			
			$isVCAddonsEnabled = self::isVCAddonsEnabled();
			
			if($isVCAddonsEnabled == false)
				return(false);
			
			
			self::onVCPagesScripts();
		}
		
		
		/**
		 * put scripts on visual composer pages
		 */
		public static function onVCPagesScripts(){
		
			UniteCreatorAdmin::addScripts_settingsBase("nojqueryui");
			
			$globalJsOutput = HelperHtmlUC::getGlobalJsOutput();
			UniteProviderFunctionsUC::printCustomScript($globalJsOutput);
			
			HelperUC::addScriptAbsoluteUrl(GlobalsUC::$url_provider."assets/uc_general_settings.js", "uc_general_settings");
			HelperUC::addStyleAbsoluteUrl( GlobalsUC::$url_provider."assets/jquery-ui-custom-vc.css", "jquery-ui-custom-vc");
			HelperUC::addStyleAbsoluteUrl( GlobalsUC::$url_provider."assets/provider_admin.css", "uc_provider_admin");
			
		}
		
		
		/**
		 * admin title
		 */
		private function getAdminTitleParam(){
			
			$param = array();
			$param["type"] = "textfield";
			$param["name"] = "uc_admin_title";
			$param["param_name"] = "uc_admin_title";
			$param["heading"] = "Add-on Title";
			$param["value"] = "";
			$param["admin_label"] = true;
			
			return($param);
		}
		
		
		/**
		 * convert params object to vc params object
		 */
		private function mapParams($arrParams){
			
			$vcParams = array();
			
			//$vcParams[] = $this->getAdminTitleParam();
			
			//dmp($arrParams);exit();
			//dmp($arrParams);
			
			foreach($arrParams as $param){
				
				$vcParam = $param;
				$vcParam["param_name"] = $vcParam["name"];
				$vcParam["heading"] = $vcParam["title"];
				
				$vcParam["value"] = UniteFunctionsUC::getVal($vcParam, "default_value");
								
				//validation
				if(empty($vcParam["type"]))
					continue;
				
				if(empty($vcParam["param_name"]))
					continue;
				
				if(isset($vcParam["admin_label"]))
					$vcParam["admin_label"] = UniteFunctionsUC::strToBool($vcParam["admin_label"]);
				
				$vcParams[] = $vcParam;
			}
			
			//dmp($vcParams);exit();
			
			return($vcParams);
		}
		
		
		/**
		 * map addon
		 */
		private function mapAddon($data){
			
			$name = UniteFunctionsUC::getVal($data, "name");
			$addonName = UniteFunctionsUC::getVal($data, "addon_name");
			$shortcode = "ucaddon_".$addonName;
			
			$category = UniteFunctionsUC::getVal($data, "category");
			$description = UniteFunctionsUC::getVal($data, "description");
			$class = "uc-addon";
			$params = UniteFunctionsUC::getVal($data, "params", array());
			$params = $this->mapParams($params);
			
			$settings = array();
			$settings["name"] = __($name, ADDONLIBRARY_TEXTDOMAIN);
			$settings["base"] = $shortcode;
			$settings["addon_name"] = $addonName;
			
			$settings["class"] = $class;
			$settings["icon"] = UniteFunctionsUC::getVal($data, "icon");
			$settings["category"] = $category;
			$settings["description"] = __($description, ADDONLIBRARY_TEXTDOMAIN);
			$settings["params"] = $params;
			 
			$settings["content_element"] = true;
			$settings["controls"] = "full";
			$settings["show_settings_on_create"] = true;
			
			//$settings["as_parent"] = array('except' => $shortcode);
			//$settings["is_container"] = true;
			//$settings["js_view"] = "VcColumnView";
			
			//create new class (validate alpha numeric shortcode
			$isAlphaNumeric = UniteFunctionsUC::isAlphaNumeric($shortcode);
			if($isAlphaNumeric == false)
				return(false);
			
			$code = "class WPBakeryShortCode_{$shortcode} extends UCVCAddonBase{};";
			eval($code);
			
			//dmp($settings);
			//exit();
			//dmp($settings);
			//exit();
			
			vc_map($settings);
		}
		
		
		/**
		 * get addon params
		 * 
		 */
		private function getAddonParams(UniteCreatorAddon $addon){
			
			$params = $addon->getProcessedMainParams();
			
			if(empty($params))
				$params = array();
			
			$hasSettings = !empty($params);
			$hasItems = $addon->isHasItems();
			
			$addonName = $addon->getName();

			//add init setting param
			if($hasSettings == true){
				$paramInit = array();
				$paramInit["type"] = "uc_init_settings";
				$paramInit["title"] = "";
				$paramInit["name"] = "uc_init_settings";
				$paramInit["addon_name"] = $addonName;
				$params[] = $paramInit;
			}else{		//no settings - add static text
				
				$paramText = array();
				$paramText["type"] = "uc_statictext";
				$paramText["title"] = __("No Settings Available", ADDONLIBRARY_TEXTDOMAIN);
				$paramText["name"] = "uc_text_nosettings";
				$paramText["addon_name"] = $addonName;
				$params[] = $paramText;
			}
			
			
			//add items param
			if($hasItems == true){
				$paramItems = array();
				$paramItems["type"] = "uc_items";
				$paramItems["title"] = "";
				$paramItems["name"] = "uc_items_data";
				$paramItems["group"] = __("Items", ADDONLIBRARY_TEXTDOMAIN);
				$paramItems["addon_name"] = $addonName;
				
				$params[] = $paramItems;
			}
			
			//add assets url to all params
			$urlAssets = $addon->getUrlAssets();
			foreach($params as $key=>$param){
				$param["url_assets"] = $urlAssets;
				$params[$key] = $param;
			}
			
			return($params);
		}
		
			
		
		/**
		 * map addon from onject
		 */
		private function mapAddonFromObject(UniteCreatorAddon $addon, $forceTitle = null){
			
			if(!empty($forceTitle))
				$catTitle = $forceTitle;
			else{
				$catTitle = $addon->getCatTitle();				
			}
			
			if(empty($catTitle))
				$catTitle = $this->vcFolder;
						
			$addonAlias = $addon->getAlias();
			
			//skip addon without alias
			if(empty($addonAlias))
				return(false);
			
			$data = array();
			$data["name"] = $addon->getTitle();
			$data["addon_name"] = $addonAlias;
			$data["category"] = $catTitle;
			$data["description"] = $addon->getDescription();
			$data["icon"] = $addon->getUrlIcon();
			
			$params = $this->getAddonParams($addon);
			
			$data["params"] = $params;
			
			$this->mapAddon($data);
		}
		
		
		/**
		 * create custom params
		 */
		private function createCustomParams(){
			UniteVCCustomParams::createCustomParams();
		}
		
		
		/**
		 * map all addons
		 */
		private function mapAllAddons(){
			
			$objAddons = new UniteCreatorAddons();
			
			$arrAddons = $objAddons->getCatAddons(null, false, "active", self::ADDONTYPE_VC);
			
			$forceTitle = null;
			$showAddonCats = HelperUC::getGeneralSetting("vc_group_cats");
			$showAddonCats = UniteFunctionsUC::strToBool($showAddonCats);
			
			if($showAddonCats == false)
				$forceTitle = $this->vcFolder;
			
			foreach($arrAddons as $addon)
				$this->mapAddonFromObject($addon, $forceTitle);
			
		}
		
		
		/**
		 * put import vc layout html
		 */
		private function putDialogImportVCLayoutHtml(){
			
			$dialogTitle = __("Import Layout to Visual Composer",ADDONLIBRARY_TEXTDOMAIN);
			
			
			?>
			<div id="uc_dialog_import_layouts" class="unite-inputs" title="<?php echo $dialogTitle?>" style="display:none;">
				
				<div class="unite-dialog-top"></div>
				
				<div class="unite-inputs-label">
					<?php _e("Select vc layout export file (zip)", ADDONLIBRARY_TEXTDOMAIN)?>:
				</div>
				
				<div class="unite-inputs-sap-small"></div>
				
				<form id="dialog_import_layouts_form" name="form_import_layouts">
					<input id="dialog_import_layouts_file" type="file" name="import_layout">
							
				</form>	
				
				<div class="unite-inputs-sap-double"></div>
				
				<div class="unite-inputs-label" >
					<label for="dialog_import_layouts_file_overwrite">
						<?php _e("Overwrite Addons", ADDONLIBRARY_TEXTDOMAIN)?>:
					</label>
					<input type="checkbox" id="dialog_import_layouts_file_overwrite"></input>
				</div>
				
				
				<div class="unite-clear"></div>
				
				<?php 
					$prefix = "uc_dialog_import_layouts";
					$buttonTitle = __("Import VC Layout", ADDONLIBRARY_TEXTDOMAIN);
					$loaderTitle = __("Uploading layout file...", ADDONLIBRARY_TEXTDOMAIN);
					$successTitle = __("Layout Imported Successfully", ADDONLIBRARY_TEXTDOMAIN);
					HelperHtmlUC::putDialogActions($prefix, $buttonTitle, $loaderTitle, $successTitle);
				?>
				
				<div id="div_debug"></div>
				 
			</div>		
			
			<?php 
		}
		
		/**
		 * put metabox html
		 */
		public function putMegaboxImportHtml(){
			
			//init post title
			$initTitle = UniteFunctionsUC::getGetVar("uc_init_title", "", UniteFunctionsUC::SANITIZE_TEXT_FIELD);	
			
			$addHtml = "";
			if(!empty($initTitle)){
				$initTitle = esc_attr($initTitle);
				$addHtml = " data-init_post_title=\"$initTitle\"";
			}
			
			?>
				<div class="uc-metabox-import-layout">
					<a id="uc_button_import_layout" <?php echo $addHtml?> href="javascript:void(0)" class="unite-button-secondary button-disabled unite-float-left mleft_20"><?php _e("Import Layout to Visual Composer", ADDONLIBRARY_TEXTDOMAIN)?></a>
				</div>
			<?php
			
		}
		
		
		/**
		 * add meta boxes
		 */
		public function addPostImportMetabox(){
			
			$isVcOnPosts = $this->isVcOnAdminPage();
			if($isVcOnPosts == false){
				return(false);
			}
			
			add_meta_box("addon_library_import_metabox", __("Addon Library", ADDONLIBRARY_TEXTDOMAIN), array($this,"putMegaboxImportHtml"), null, "side");
			
		}
		
		/**
		 * add dialog import if needed
		 */
		public function onAdminFooter(){
			
			//put the footer content only on vc pages			
			
			$isVcOnPosts = $this->isVcOnAdminPage();
			if($isVcOnPosts == false)
				return(false);
			
			if($this->isMetaboxEnabled)
				$this->putDialogImportVCLayoutHtml();
			
			//remove me
			//$htmlDebug = HelperHtmlUC::getGlobalDebugDivs();
			//echo $htmlDebug;
		}
		
		
		/**
		 * update post
		 */
		private function updatePostWithLayoutContent($postID, $content){
			
			$arrPost = array();
			$arrPost["ID"] = $postID;
			$arrPost["post_content"] = $content;
			//$arrPost["post_title"] = $title;
			
			wp_update_post($arrPost);
		}
		
		
		/**
		 * import vc layout zip
		 */
		private function importVCLayout($data){
			
			$postID = UniteFunctionsUC::getVal($data, "postid");
			$postID = (int)$postID;
			
			$arrTempFile = UniteFunctionsUC::getVal($_FILES, "import_layout");
			
			$exporter = new UniteCreatorLayoutsExporterVC();
			
			$isOverwriteAddons = UniteFunctionsUC::getVal($data, "overwrite_addons");
			$isOverwriteAddons = UniteFunctionsUC::strToBool($isOverwriteAddons);
						
			$content = $exporter->importVCLayout($arrTempFile, $isOverwriteAddons);
			$importedTitle = $exporter->getLayoutTitle();
			
			$urlEditPost = "";
			
			if(!empty($postID)){
				
				//get title
				$title = UniteFunctionsUC::getVal($data, "title");
				
				$this->updatePostWithLayoutContent($postID, $content);
				$urlEditPost = get_edit_post_link($postID);
				if(empty($title))
					$urlEditPost .= "&uc_init_title=".$importedTitle;
			}
			
			
			$response = array();
			$response["url_reload"] = $urlEditPost;
			if(empty($urlEditPost))
				$response["content"] = $content;
			
			return($response);
		}
		
		
		/**
		 * on ajax action
		 */
		public function onAjaxAction($found, $action, $data){
			
			if($found == true)
				return(true);
			
			switch($action){
				case "import_vc_layout":
					$response = $this->importVCLayout($data);
					HelperUC::ajaxResponseData($response);
				break;
				default:
					return(false);
				break;
			}
		
			return(true);
		}
		
		
		/**
		 * init vc integration
		 */
		public function initVCIntegration(){
						
			if(self::isVCExists() == false)
				return(false);
			
			if(self::isVCAddonsEnabled() == false)
				return(false);
			
			//init vc folder
			$this->vcFolder = HelperUC::getGeneralSetting("vc_folder");
			if(empty($this->vcFolder))
				$this->vcFolder = "Addon Library";
			
			//include vc related files
			require_once GlobalsUC::$pathProvider . 'unitevc_addon_shortcode.class.php';
			require_once GlobalsUC::$pathProvider . 'unitevc_custom_params.class.php';
			
			//add_action( 'wp_ajax_wpb_show_edit_form', array( &$this, 'build' ) );
			
			$this->createCustomParams();
			$this->mapAllAddons();
			
			//add metabox
			$addMetabox = HelperUC::getGeneralSetting("vc_put_import_metabox");
			$addMetabox = UniteFunctionsUC::strToBool($addMetabox);
			
			if($addMetabox == true){
				$this->isMetaboxEnabled = true;
				add_action( self::ACTION_ADD_METABOXES, array($this, "addPostImportMetabox") );
				add_action(self::ACTION_ADMIN_FOOTER, array($this,"onAdminFooter"));
			}
			
			$this->addFilter(self::FILTER_ADMIN_AJAX_ACTION, "onAjaxAction",10,3);
			
		}
		
	}

?>