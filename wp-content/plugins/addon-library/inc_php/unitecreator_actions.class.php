<?php
/**
 * @package Addon Library
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('ADDON_LIBRARY_INC') or die('Restricted access');

class UniteCreatorActions{

	
	/**
	 * on ajax action
	 */
	public function onAjaxAction(){
		
		$actionType = UniteFunctionsUC::getPostGetVariable("action","",UniteFunctionsUC::SANITIZE_KEY);
		
		if($actionType != "unitecreator_ajax_action")
			return(false);
		
		$operations = new UCOperations();
		$addons = new UniteCreatorAddons();
		$assets = new UniteCreatorAssetsWork();
		$categories = new UniteCreatorCategories();
		$layouts = new UniteCreatorLayouts();
		
		$action = UniteFunctionsUC::getPostGetVariable("client_action","",UniteFunctionsUC::SANITIZE_KEY);
		
		$data = UniteFunctionsUC::getPostGetVariable("data","",UniteFunctionsUC::SANITIZE_NOTHING);
		if(empty($data))
			$data = $_REQUEST;
		
		if(is_string($data)){
						
			$arrData = (array)json_decode($data);
		
			if(empty($arrData)){
				$arrData = stripslashes(trim($data));
				$arrData = (array)json_decode($arrData);
			}
						
			$data = $arrData;
		}
		
		$data = UniteFunctionsUC::convertStdClassToArray($data);
		
		$data = UniteProviderFunctionsUC::normalizeAjaxInputData($data);
		
		try{
		
			if(method_exists("UniteProviderFunctionsUC", "verifyNonce")){
				$nonce = UniteFunctionsUC::getPostGetVariable("nonce","",UniteFunctionsUC::SANITIZE_NOTHING);
				UniteProviderFunctionsUC::verifyNonce($nonce);
			}
			switch($action){
				
				case "remove_category":
					$response = $categories->removeFromData($data);
				
					HelperUC::ajaxResponseSuccess(__("The category deleted successfully",ADDONLIBRARY_TEXTDOMAIN),$response);
					break;
				case "update_category":
				
					$categories->updateFromData($data);
					HelperUC::ajaxResponseSuccess(__("Category updated",ADDONLIBRARY_TEXTDOMAIN));
					break;
				case "update_cat_order":
					$categories->updateOrderFromData($data);
					HelperUC::ajaxResponseSuccess(__("Order updated",ADDONLIBRARY_TEXTDOMAIN));
					break;
				case "get_cat_addons":
					$responeData = $addons->getCatAddonsHtmlFromData($data);
					
					HelperUC::ajaxResponseData($responeData);
				break;
				case "get_catlist":
					$responeData = $categories->getCatListFromData($data);
					
					HelperUC::ajaxResponseData($responeData);
				break;
				case "update_addon":
					$response = $addons->updateAddonFromData($data);
					HelperUC::ajaxResponseSuccess(__("Updated.",ADDONLIBRARY_TEXTDOMAIN),$response);
					break;
				case "delete_addon":
					$addons->deleteAddonFromData($data);
					HelperUC::ajaxResponseSuccess(__("The addon deleted successfully",ADDONLIBRARY_TEXTDOMAIN));
				break;
				case "add_category":
					$catData = $categories->addFromData($data);
					HelperUC::ajaxResponseData($catData);
				break;
				case "add_addon":
					
					if(GlobalsUC::$permisison_add == false)
						UniteFunctionsUC::throwError("Operation not permitted");
					
					$response = $addons->createFromManager($data);
					
					HelperUC::ajaxResponseSuccess(__("Addon added successfully",ADDONLIBRARY_TEXTDOMAIN), $response);
				break;
				case "update_addon_title":
					$addons->updateAddonTitleFromData($data);
					
					HelperUC::ajaxResponseSuccess(__("Addon updated successfully",ADDONLIBRARY_TEXTDOMAIN));
				break;
				case "update_addons_activation":
					$addons->activateAddonsFromData($data);
					
					HelperUC::ajaxResponseSuccess(__("Addons updated successfully",ADDONLIBRARY_TEXTDOMAIN));
				break;
				case "remove_addons":
					$response = $addons->removeAddonsFromData($data);
					
					HelperUC::ajaxResponseSuccess(__("Addons Removed",ADDONLIBRARY_TEXTDOMAIN), $response);
				break;
				case "update_addons_order":
					$addons->saveOrderFromData($data);

					HelperUC::ajaxResponseSuccess(__("Order Saved",ADDONLIBRARY_TEXTDOMAIN));
				break;
				case "move_addons":
					$response = $addons->moveAddonsFromData($data);
					HelperUC::ajaxResponseSuccess(__("Done Operation",ADDONLIBRARY_TEXTDOMAIN),$response);
				break;
				case "duplicate_addons":
					$response = $addons->duplicateAddonsFromData($data);
					HelperUC::ajaxResponseSuccess(__("Addons Duplicated",ADDONLIBRARY_TEXTDOMAIN),$response);
				break;
				case "get_addon_config_html":
					
					$html = $addons->getAddonConfigHTML($data);
					
					HelperUC::ajaxResponseData(array("html"=>$html));
				break;
				case "show_preview":
					$addons->showAddonPreviewFromData($data);
					exit();
				break;
				case "save_test_addon":
					$addons->saveTestAddonData($data);
					HelperUC::ajaxResponseSuccess(__("Saved",ADDONLIBRARY_TEXTDOMAIN));
				break;
				case "get_test_addon_data":
					$response = $addons->getTestAddonData($data);
					HelperUC::ajaxResponseData($response);
				break;
				case "delete_test_addon_data":
					$addons->deleteTestAddonData($data);
					HelperUC::ajaxResponseSuccess(__("Test data deleted",ADDONLIBRARY_TEXTDOMAIN));
				break;
				case "export_addon":
					$addons->exportAddon($data);
					exit();
				break;
				case "export_cat_addons":
					$addons->exportCatAddons($data);
				break;
				case "import_addons":
					$response = $addons->importAddons($data);
					
					HelperUC::ajaxResponseSuccess(__("Addons Imported",ADDONLIBRARY_TEXTDOMAIN),$response);
				break;
				case "get_addon_item_settings_html":
					
					$html = $addons->getAddonItemsSettingsHTMLFromData($data);
					HelperUC::ajaxResponseData(array("html"=>$html));
					
				break;
				case "import_layouts":
					$urlRedirect = $layouts->importLayouts($data);
					
					HelperUC::ajaxResponseSuccessRedirect(__("Layout Imported",ADDONLIBRARY_TEXTDOMAIN), $urlRedirect);
				break;
				case "get_version_text":
					$content = HelperHtmlUC::getVersionText();
					HelperUC::ajaxResponseData(array("text"=>$content));
				break;
				case "update_plugin":
				
					if(method_exists("UniteProviderFunctionsUC", "updatePlugin"))
						UniteProviderFunctionsUC::updatePlugin();
					else{
						echo "Functionality Don't Exists";
						exit();
					}
				
				break;
				case "update_general_settings":
					$operations->updateGeneralSettingsFromData($data);
					
					HelperUC::ajaxResponseSuccess(__("Settings Saved",ADDONLIBRARY_TEXTDOMAIN));
				break;
				case "update_global_layout_settings":
					
					UniteCreatorLayout::updateLayoutGlobalSettingsFromData($data);
					
					HelperUC::ajaxResponseSuccess(__("Settings Saved",ADDONLIBRARY_TEXTDOMAIN));
				break;
				case "update_layout":
					
					$response = $layouts->updateLayoutFromData($data);
					
					$isUpdate = $response["is_update"];
					
					//create
					if($isUpdate == false){
						$layoutID = $response["layout_id"];
						
						$urlRedirect = HelperUC::getViewUrl_Layout($layoutID);
						
						HelperUC::ajaxResponseSuccessRedirect(__("Layout Created",ADDONLIBRARY_TEXTDOMAIN), $urlRedirect);
						
					}else{
						//update
						HelperUC::ajaxResponseSuccess(__("Layout Updated",ADDONLIBRARY_TEXTDOMAIN));
					}
					
				break;
				case "delete_layout":
					
					$layouts->deleteLayoutFromData($data);
					$urlLayouts = HelperUC::getViewUrl_LayoutsList();
					
					HelperUC::ajaxResponseSuccessRedirect(__("Layout Deleted",ADDONLIBRARY_TEXTDOMAIN), $urlLayouts);
					
				break;
				case "duplicate_layout":
					
					$layouts->duplicateLayoutFromData($data);
					
					$urlLayouts = HelperUC::getViewUrl_LayoutsList();
					
					HelperUC::ajaxResponseSuccessRedirect(__("Layout Duplicated",ADDONLIBRARY_TEXTDOMAIN), $urlLayouts);
					
				break;
				case "export_layout":
					$layouts->exportLayout();
					exit();
				break;
				default:
					
					//check assets
					$found = $assets->checkAjaxActions($action, $data);

					if(!$found)
						$found = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_ADMIN_AJAX_ACTION, $found, $action, $data);
					
					if(!$found)
						HelperUC::ajaxResponseError("wrong ajax action: <b>$action</b> ");
				break;
			}
		
		}
		catch(Exception $e){
			$message = $e->getMessage();
		
			$errorMessage = $message;
			if(GlobalsUC::SHOW_TRACE == true){
				$trace = $e->getTraceAsString();
				$errorMessage = $message."<pre>".$trace."</pre>";
			}
		
			HelperUC::ajaxResponseError($errorMessage);
		}
		
		//it's an ajax action, so exit
		HelperUC::ajaxResponseError("No response output on <b> $action </b> action. please check with the developer.");
		exit();
		
	}
	
}