<?php
/**
 * @package Addon Library
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('ADDON_LIBRARY_INC') or die('Restricted access');


/**
 * 
 * creator helper functions class
 *
 */
	class HelperHtmlUC extends UniteHelperBaseUC{
		
		private static $isGlobalJSPut = false;

		
		/**
		 *
		 * get link html
		 */
		public static function getHtmlLink($link,$text,$id="",$class="", $isNewWindow = false){
		
			if(!empty($class))
				$class = " class='$class'";
		
			if(!empty($id))
				$id = " id='$id'";
		
			$htmlAdd = "";
			if($isNewWindow == true)
				$htmlAdd = ' target="_blank"';
		
			$html = "<a href=\"$link\"".$id.$class.$htmlAdd.">$text</a>";
			return($html);
		}

		
		/**
		 *
		 * get select from array
		 */
		public static function getHTMLSelect($arr,$default="",$htmlParams="",$assoc = false){
		
			$html = "<select $htmlParams>";
			foreach($arr as $key=>$item){
				$selected = "";
		
				if($assoc == false){
					if($item == $default) $selected = " selected ";
				}
				else{
					if(trim($key) == trim($default))
						$selected = " selected ";
				}
		
		
				if($assoc == true)
					$html .= "<option $selected value='$key'>$item</option>";
				else
					$html .= "<option $selected value='$item'>$item</option>";
			}
			$html.= "</select>";
			return($html);
		}
		
		
		/**
		 * get row of addons table
		 */
		public static function getTableAddonsRow($addonID, $title){
			
			$editLink = HelperUC::getViewUrl_EditAddon($addonID);
			
			$htmlTitle = htmlspecialchars($title);
			
			$html = "<tr>\n";
			$html.= "<td><a href='{$editLink}'>{$title}</a></td>\n";
			$html.= "	<td>\n";
			$html.= " 	  <a href='{$editLink}' class='unite-button-secondary float_left mleft_15'>". __("Edit",ADDONLIBRARY_TEXTDOMAIN) . "</a>\n";
			$html.= "		<a href='javascript:void(0)' data-addonid='{$addonID}' class='uc-button-delete unite-button-secondary float_left mleft_15'>".__("Delete",ADDONLIBRARY_TEXTDOMAIN)."</a>";
			$html.= "		<span class='loader_text uc-loader-delete mleft_10' style='display:none'>" . __("Deleting", ADDONLIBRARY_TEXTDOMAIN) . "</span>";
			$html.= "		<a href='javascript:void(0)' data-addonid='{$addonID}' class='uc-button-duplicate unite-button-secondary float_left mleft_15'>" . __("Duplicate",ADDONLIBRARY_TEXTDOMAIN)."</a>\n";
			$html.= "		<span class='loader_text uc-loader-duplicate mleft_10' style='display:none'>" . __("Duplicating", ADDONLIBRARY_TEXTDOMAIN) . "</span>";
			$html.= "		<a href='javascript:void(0)' data-addonid='{$addonID}' data-title='{$htmlTitle}' class='uc-button-savelibrary unite-button-secondary float_left mleft_15'>" . __("Save To Library",ADDONLIBRARY_TEXTDOMAIN)."</a>\n";
			$html.= "		<span class='loader_text uc-loader-save mleft_10' style='display:none'>" . __("Saving to library", ADDONLIBRARY_TEXTDOMAIN) . "</span>";
			$html.= "	</td>\n";
			$html.= "	</tr>\n";
			
			return($html);
		}

		
		
		/**
		 * put dialog actions
		 */
		public static function putDialogActions($prefix, $buttonTitle, $loaderTitle, $successTitle){
			?>
				<div id="<?php echo $prefix?>_actions_wrapper" class="unite-dialog-actions">
					
					<a id="<?php echo $prefix?>_action" href="javascript:void(0)" class="unite-button-primary"><?php echo $buttonTitle?></a>
					<div id="<?php echo $prefix?>_loader" class="loader_text" style="display:none"><?php echo $loaderTitle?></div>
					<div id="<?php echo $prefix?>_error" class="unite-dialog-error"  style="display:none"></div>
					<div id="<?php echo $prefix?>_success" class="unite-dialog-success" style="display:none"><?php echo $successTitle?></div>
					
				</div>
			<?php 
		}
		
		
		/**
		 * get global js output for plugin pages
		 */
		public static function getGlobalJsOutput(){
			
			//insure that this function run only once
			if(self::$isGlobalJSPut == true)
				return("");
			
			self::$isGlobalJSPut = true;
			
			$jsArrayText = UniteFunctionsUC::phpArrayToJsArrayText(GlobalsUC::$arrClientSideText,"				");
			
			//prepare assets path
			$pathAssets = HelperUC::pathToRelative(GlobalsUC::$pathAssets, false);
			$pathAssets = urlencode($pathAssets);
			
			$js = "";
			$js .= self::TAB2.'var g_pluginNameUC = "'.GlobalsUC::PLUGIN_NAME.'";'.self::BR;
			$js .= self::TAB2.'var g_pathAssetsUC = decodeURIComponent("'.$pathAssets.'");'.self::BR;
			$js .= self::TAB2.'var g_urlAjaxActionsUC = "'.GlobalsUC::$url_ajax.'";'.self::BR;
			$js .= self::TAB2.'var g_urlViewBaseUC = "'.GlobalsUC::$url_component_admin.'";'.self::BR;
			$js .= self::TAB2.'var g_urlBaseUC = "'.GlobalsUC::$url_base.'";'.self::BR;
			$js .= self::TAB2.'var g_urlAssetsUC = "'.GlobalsUC::$url_assets.'";'.self::BR;
			$js .= self::TAB2.'var g_settingsObjUC = {};'.self::BR;
			$js .= self::TAB2.'var g_ucAdmin;'.self::BR;
			
			//get nonce
			if(method_exists("UniteProviderFunctionsUC", "getNonce"))
				$js .= self::TAB2 . "var g_ucNonce='".UniteProviderFunctionsUC::getNonce()."';";
			
			$js .= self::TAB2.'var g_uctext = {'.self::BR;
			$js .= self::TAB3.$jsArrayText.self::BR;
			$js .= self::TAB2.'};'.self::BR;
						
			return($js);
		}
		
		
		/**
		 * get flobal debug divs
		 */
		public static function getGlobalDebugDivs(){
			$html = "";
			
			$html .= self::TAB2.'<div id="div_debug" class="unite-div-debug"></div>'.self::BR;
			$html .= self::TAB2.'<div id="debug_line" style="display:none"></div>'.self::BR;
			$html .= self::TAB2.'<div id="debug_side" style="display:none"></div>'.self::BR;
			$html .= self::TAB2.'<div class="unite_error_message" id="error_message" style="display:none;"></div>'.self::BR;
			$html .= self::TAB2.'<div class="unite_success_message" id="success_message" style="display:none;"></div>'.self::BR;
			
			return($html);
		}
		
		
		/**
		 * put global framework
		 */
		public static function putGlobalsHtmlOutput(){
			
			if(self::$isGlobalJSPut == true)
				return(false);
			
			$jsOutput = self::getGlobalJsOutput();
			
			?>
			<script type="text/javascript">
				
				<?php echo $jsOutput?>
				
				
			</script>
			
			<?php 
				
				$debugDivs = self::getGlobalDebugDivs();
				echo $debugDivs;
				
				if(method_exists("UniteProviderFunctionsUC", "putMasterHTML"))
					UniteProviderFunctionsUC::putMasterHTML() 
			?>			
			
			<?php 			
		}

		
		/**
		 * put control fields notice to dialogs that use it
		 */
		public static function putDialogControlFieldsNotice(){
			?>
				<div class="unite-inputs-sap"></div> 
			
				<div class="unite-inputs-label unite-italic">
					* <?php _e("only dropdown and radio boolean field types are used for conditional inputs", ADDONLIBRARY_TEXTDOMAIN)?>.
				</div>
			
			<?php 
		}
		
		
		/**
		 * get version text
		 */
		public static function getVersionText(){
			$filepath = GlobalsUC::$pathPlugin."release_log.txt";
			$content = file_get_contents($filepath);
			
			return($content);
			
		}
		
		
		/**
		 * put plugin version html
		 */
		public static function putPluginVersionHtml(){
			
			$objPlugins = new UniteCreatorPlugins();
			
			$arrPlugins = $objPlugins->getArrPlugins();
			
			if(empty($arrPlugins))
				return(false);
			
			foreach($arrPlugins as $plugin){
				
				$name = UniteFunctionsUC::getVal($plugin, "name");
				$title = UniteFunctionsUC::getVal($plugin, "title");
				$version = UniteFunctionsUC::getVal($plugin, "version");
				
				switch($name){
					case "create_addons":
						$title = "Create addons plugin {$version}";
					break;
					default:
						$title = "$title {$version}";
					break;
				}
				
				echo ", ";
				
				echo $title;
			}
			
		}
		
		
		/**
		 * output exception
		 */
		public static function outputException(Exception $e, $prefix=""){
			
			if(empty($prefix))
				$prefix = "Addon Library Error: ";
			
			$message = $prefix.$e->getMessage();
			$trace = $e->getTraceAsString();
			
			dmp($message);
			if(GlobalsUC::SHOW_TRACE == true)
				dmp($trace);
		}
		
		
		/**
		 * get error message html
		 */
		public static function getErrorMessageHtml($message, $trace = ""){
		
			$html = '<div style="width:90%;min-width:400px;height:300px;margin-bottom:10px;border:1px solid black;margin:0px auto;overflow:auto;">';
			$html .= '<div style="padding-left:20px;padding-right:20px;line-height:1.5;padding-top:40px;color:red;font-size:16px;text-align:left;">';
			$html .= $message;
		
			if(!empty($trace)){
				$html .= '<div style="text-align:left;padding-left:20px;padding-top:20px;">';
				$html .= "<pre>{$trace}</pre>";
				$html .= "</div>";
			}
		
			$html .= '</div></div>';
		
			return($html);
		}
		
		
		/**
		 * output exception in a box
		 */
		public static function outputExceptionBox($e, $prefix=""){
			
			$message = $e->getMessage();
			
			if(!empty($prefix))
				$message = $prefix.":  ".$message;
			
			$trace = "";
			
			$showTrace = GlobalsUC::SHOW_TRACE_FRONT;
			if(UniteProviderFunctionsUC::isAdmin() == true)
				$showTrace = GlobalsUC::SHOW_TRACE;
				
			if($showTrace)
				$trace = $e->getTraceAsString();
			
			$html = self::getErrorMessageHtml($message, $trace);
			
			echo $html;
		}
		
		
		
		
		
	} //end class

	