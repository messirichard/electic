<?php

/**
 * @package Addon Library
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('ADDON_LIBRARY_INC') or die('Restricted access');

class UniteCreatorManagerAddons extends UniteCreatorManager{
	
	const VIEW_TYPE_INFO = "info";		//addons view type
	const VIEW_TYPE_THUMB = "thumb";
	
	private $filterAddonType = null;
	private $addonTypeTitle = "";
	private $filterActive = "";
	private $showAddonTooltip = false;
	
	
	/**
	 * construct the manager
	 */
	public function __construct(){
		$this->type = self::TYPE_ADDONS;
		$this->viewType = self::VIEW_TYPE_THUMB;		
		
		$this->init();
		
		UniteProviderFunctionsUC::doAction(UniteCreatorFilters::ACTION_MODIFY_ADDONS_MANAGER, $this);
		
	}
	
	
	/**
	 * set filter addon type to use only it
	 */
	public function setAddonType($addonType, $typeTitle){
		
		$this->filterAddonType = $addonType;
		$this->addonTypeTitle = $typeTitle;
		
	}
	
	
	/**
	 * get html addon
	 */
	public function getAddonAdminHtml(UniteCreatorAddon $objAddon){
		
		$objAddon->validateInited();
		
		$title = $objAddon->getTitle(true);
				
		$name = $objAddon->getNameByType();
		
		$name = htmlspecialchars($name);
		
		$description = $objAddon->getDescription(true);
		
		//set html icon
		$urlIcon = $objAddon->getUrlIcon();
		
		
		//get preview html
		$urlPreview = $objAddon->getUrlPreview();
		
		$htmlPreview = "";
		
		if($this->showAddonTooltip === true && !empty($urlPreview)){
			$urlPreviewHtml = htmlspecialchars($urlPreview);
			$htmlPreview = "data-preview='$urlPreviewHtml'";
		}
		
		$itemID = $objAddon->getID();
				
		$descOutput = $description;
		
		$class = "";
		$isActive = $objAddon->getIsActive();
		
		if($isActive == false)
			$class = " class=\"uc-item-notactive\"";
		
		//set html output
				
		$htmlItem  = "<li id=\"uc_item_{$itemID}\" data-id=\"{$itemID}\" data-title=\"{$title}\" data-name=\"{$name}\" data-description=\"{$description}\" {$htmlPreview} {$class} >";
		
		IF($this->viewType == self::VIEW_TYPE_INFO){
			
			$htmlItem .= "	<div class=\"uc-item-title unselectable\" unselectable=\"on\">{$title}</div>";
			$htmlItem .= "	<div class=\"uc-item-description unselectable\" unselectable=\"on\">{$descOutput}</div>";
			$htmlItem .= "	<div class=\"uc-item-icon unselectable\" unselectable=\"on\"></div>";
			
			//add icon
			$htmlIcon = "";
			if(!empty($urlIcon))
				$htmlIcon = "<div class='uc-item-icon' style=\"background-image:url('{$urlIcon}')\"></div>";
			
			$htmlItem .= $htmlIcon;
			
		}elseif($this->viewType == self::VIEW_TYPE_THUMB){
			
			$classThumb = "";
			$style = "";
			if(empty($urlPreview))
				$classThumb = " uc-no-thumb";
			else{
				$style = "style=\"background-image:url('{$urlPreview}')\"";
			}
			
			$htmlItem .= "	<div class=\"uc-item-thumb{$classThumb} unselectable\" unselectable=\"on\" {$style}></div>";
			
			$htmlItem .= "	<div class=\"uc-item-title unselectable\" unselectable=\"on\">{$title}</div>";
			
			
		}else{
			UniteFunctionsUC::throwError("Wrong addons view type");
		}
		
		$htmlItem .= "</li>";
		
		
		return($htmlItem);
		
	}
	
	
	/**
	 * get single item menu
	 */
	protected function getMenuSingleItem(){
		
		$arrMenuItem = array();
		$arrMenuItem["edit_addon"] = __("Edit Addon",ADDONLIBRARY_TEXTDOMAIN);
		$arrMenuItem["edit_addon_blank"] = __("Edit In New Tab",ADDONLIBRARY_TEXTDOMAIN);
		$arrMenuItem["quick_edit"] = __("Quick Edit",ADDONLIBRARY_TEXTDOMAIN);
		$arrMenuItem["remove_item"] = __("Delete",ADDONLIBRARY_TEXTDOMAIN);
		$arrMenuItem["test_addon"] = __("Test Addon",ADDONLIBRARY_TEXTDOMAIN);
		$arrMenuItem["test_addon_blank"] = __("Test In New Tab",ADDONLIBRARY_TEXTDOMAIN);
		$arrMenuItem["export_addon"] = __("Export Addon",ADDONLIBRARY_TEXTDOMAIN);
		
		$arrMenuItem = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_MANAGER_MENU_SINGLE, $arrMenuItem);
		
		return($arrMenuItem);
	}

	
	/**
	 * get item field menu
	 */
	protected function getMenuField(){
		$arrMenuField = array();
				
		$arrMenuField["select_all"] = __("Select All",ADDONLIBRARY_TEXTDOMAIN);
		
		$arrMenuField = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_MANAGER_MENU_FIELD, $arrMenuField);
		
		return($arrMenuField);
	}

	
	/**
	 * get multiple items menu
	 */
	protected function getMenuMulitipleItems(){
		$arrMenuItemMultiple = array();
		$arrMenuItemMultiple["remove_item"] = __("Delete",ADDONLIBRARY_TEXTDOMAIN);
				
		$arrMenuItemMultiple = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_MANAGER_MENU_MULTIPLE, $arrMenuItemMultiple);
		
		return($arrMenuItemMultiple);
	}
	
	
	/**
	 * get category menu
	 */
	protected function getMenuCategory(){
	
		$arrMenuCat = array();
		$arrMenuCat["edit_category"] = __("Edit Category",ADDONLIBRARY_TEXTDOMAIN);
		$arrMenuCat["delete_category"] = __("Delete Category",ADDONLIBRARY_TEXTDOMAIN);
		
		
		$arrMenuCat = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_MANAGER_MENU_CATEGORY, $arrMenuCat);
		
		return($arrMenuCat);
	}
	
	
	/**
	 * get category list
	 */
	protected function getCatList(){
		
		$htmlCatList = $this->objCats->getHtmlCatList(null, $this->filterAddonType);
		
		return($htmlCatList);
	}
	
	
	/**
	 * get no items text
	 */
	protected function getNoItemsText(){
		
		$text = __("No Addons Found", ADDONLIBRARY_TEXTDOMAIN);
		
		return($text);
	}
	
	
	/**
	 * get html categories select
	 */
	protected function getHtmlSelectCats(){
		
		if($this->hasCats == false)
			UniteFunctionsUC::throwError("the function ");
		
		$htmlSelectCats = $this->objCats->getHtmlSelectCats($this->filterAddonType);
		
		return($htmlSelectCats);
	}
	
	
	/**
	 * put content to items wrapper div
	 */
	protected function putListWrapperContent(){
		$addonType = $this->filterAddonType;
		if(empty($addonType))
			$addonType = "default";
		
		$filepathEmptyAddons = GlobalsUC::$pathProviderViews."empty_addons_text_{$addonType}.php";
		if(file_exists($filepathEmptyAddons) == false)
			return(false);
		
		?>
		<div id="uc_empty_addons_wrapper" class="uc-empty-addons-wrapper" style="display:none">
			
			<?php include $filepathEmptyAddons?>
			
		</div>
		<?php 
	}
	
	
	/**
	 * put items buttons
	 */
	protected function putItemsButtons(){
		?>
			
			<?php 
			 UniteProviderFunctionsUC::doAction(UniteCreatorFilters::ACTION_MANAGER_ITEM_BUTTONS1);
			?>
				 			
 			<a data-action="import_addon" type="button" class="unite-button-secondary unite-button-blue button-disabled uc-button-item uc-button-add"><?php _e("Import Addons",ADDONLIBRARY_TEXTDOMAIN)?></a>
 			<a data-action="select_all_items" type="button" class="unite-button-secondary button-disabled uc-button-item uc-button-select" data-textselect="<?php _e("Select All",ADDONLIBRARY_TEXTDOMAIN)?>" data-textunselect="<?php _e("Unselect All",ADDONLIBRARY_TEXTDOMAIN)?>"><?php _e("Select All",ADDONLIBRARY_TEXTDOMAIN)?></a>

			<?php 
			 UniteProviderFunctionsUC::doAction(UniteCreatorFilters::ACTION_MANAGER_ITEM_BUTTONS2);
			?>
 			
	 		<a data-action="remove_item" type="button" class="unite-button-secondary button-disabled uc-button-item"><?php _e("Delete",ADDONLIBRARY_TEXTDOMAIN)?></a>
	 		<a data-action="edit_addon" type="button" class="unite-button-primary button-disabled uc-button-item uc-single-item"><?php _e("Edit Addon",ADDONLIBRARY_TEXTDOMAIN)?> </a>
	 		<a data-action="quick_edit" type="button" class="unite-button-secondary button-disabled uc-button-item uc-single-item"><?php _e("Quick Edit",ADDONLIBRARY_TEXTDOMAIN)?></a>
	 		<a data-action="test_addon" type="button" class="unite-button-secondary button-disabled uc-button-item uc-single-item"><?php _e("Test Addon",ADDONLIBRARY_TEXTDOMAIN)?></a>

			<?php 
			 UniteProviderFunctionsUC::doAction(UniteCreatorFilters::ACTION_MANAGER_ITEM_BUTTONS3);
			?>
	 			 			
	 		<a data-action="activate_addons" type="button" class="unite-button-secondary button-disabled uc-button-item uc-notactive-item"><?php _e("Activate",ADDONLIBRARY_TEXTDOMAIN)?></a>
	 		<a data-action="deactivate_addons" type="button" class="unite-button-secondary button-disabled uc-button-item uc-active-item"><?php _e("Deactivate",ADDONLIBRARY_TEXTDOMAIN)?></a>
		<?php
	}
	
	
	/**
	 * put filters - function for override
	 */
	protected function putItemsFilters(){
		
		$classActive = "class='uc-active'";
		$filter = $this->filterActive;
		if(empty($filter))
			$filter = "all";
		
		?>
		
		<div class="uc-items-filters">
			
			<div class="uc-filters-set-title"><?php _e("Show Addons", ADDONLIBRARY_TEXTDOMAIN)?>:</div>
			
			<div id="uc_filters_active" class="uc-filters-set">
				<a href="javascript:void(0)" onfocus="this.blur()" data-filter="all" <?php echo ($filter == "all")?$classActive:""?> ><?php _e("All", ADDONLIBRARY_TEXTDOMAIN)?></a>
				<a href="javascript:void(0)" onfocus="this.blur()" data-filter="active" <?php echo ($filter == "active")?$classActive:""?> ><?php _e("Active", ADDONLIBRARY_TEXTDOMAIN)?></a>
				<a href="javascript:void(0)" onfocus="this.blur()" data-filter="not_active" <?php echo ($filter == "not_active")?$classActive:""?> ><?php _e("Not Active", ADDONLIBRARY_TEXTDOMAIN)?></a>
			</div>
			
			<div class="unite-clear"></div>
		</div>
		
		<?php 
	}
	
	
	/**
	 * put quick edit dialog
	 */
	private function putDialogQuickEdit(){
		?>
			<!-- dialog quick edit -->
		
			<div id="dialog_edit_item_title"  title="<?php _e("Quick Edit",ADDONLIBRARY_TEXTDOMAIN)?>" style="display:none;">
			
				<div class="dialog_edit_title_inner unite-inputs mtop_20 mbottom_20" >
			
					<div class="unite-inputs-label-inline">
						<?php _e("Title", ADDONLIBRARY_TEXTDOMAIN)?>:
					</div>
					<input type="text" id="dialog_quick_edit_title" class="unite-input-wide">
					
					<div class="unite-inputs-sap"></div>
							
					<div class="unite-inputs-label-inline">
						<?php _e("Name", ADDONLIBRARY_TEXTDOMAIN)?>:
					</div>
					<input type="text" id="dialog_quick_edit_name" class="unite-input-wide">
					
					<div class="unite-inputs-sap"></div>
					
					<div class="unite-inputs-label-inline">
						<?php _e("Description", ADDONLIBRARY_TEXTDOMAIN)?>:
					</div>
					
					<textarea class="unite-input-wide" id="dialog_quick_edit_description"></textarea>
					
				</div>
				
			</div>
		
		<?php 
	}


	/**
	 * put import addons dialog
	 */
	private function putDialogImportAddons(){
		
		$dialogTitle = __("Import Addons",ADDONLIBRARY_TEXTDOMAIN);
		
		if(!empty($this->filterAddonType)){
			$dialogTitle .= __(" for ",ADDONLIBRARY_TEXTDOMAIN);
			$dialogTitle .= $this->addonTypeTitle;
		}
		
		?>
		
			<div id="dialog_import_addons" class="unite-inputs" title="<?php echo $dialogTitle?>" style="display:none;">
				
				<div class="unite-dialog-top"></div>
				
				<div class='dialog-import-addons-left'>
				
					<div class="unite-inputs-label">
						<?php _e("Select addons export file", ADDONLIBRARY_TEXTDOMAIN)?>:
					</div>
					
					<div class="unite-inputs-sap-small"></div>
				
					<form id="dialog_import_addons_form" name="form_import_addon">
						<input id="dialog_import_addons_file" type="file" name="import_addon">
						
						<div class="unite-inputs-sap-double"></div>
						
						<div class="unite-inputs-label">
							<?php _e("Import to Category", ADDONLIBRARY_TEXTDOMAIN)?>:
							
							<span id="dialog_import_catname" data-text-autodetect="[Autodetect]"></span>
							
						</div>
						
						<div class="unite-inputs-sap-double"></div>
						
						<div class="unite-inputs-label">
							<label for="dialog_import_check_overwrite">							
								<?php _e("Overwrite Existing Addons", ADDONLIBRARY_TEXTDOMAIN)?>:
							</label>
							<input type="checkbox" checked="checked" id="dialog_import_check_overwrite"></input>
						</div>
						
					</form>	
				
				</div>
				
				<div id="dialog_import_addons_log" class='dialog-import-addons-right' style="display:none">
					
					<div class="unite-bold"> <?php _e("Import Addons Log",ADDONLIBRARY_TEXTDOMAIN)?> </div>
					
					<br>
					
					<div id="dialog_import_addons_log_text" class="dialog-import-addons-log"></div>
				</div>
				
				<div class="unite-clear"></div>
				
				<?php 
					$prefix = "dialog_import_addons";
					$buttonTitle = __("Import Addons", ADDONLIBRARY_TEXTDOMAIN);
					$loaderTitle = __("Uploading addon file...", ADDONLIBRARY_TEXTDOMAIN);
					$successTitle = __("Addon Added Successfully", ADDONLIBRARY_TEXTDOMAIN);
					HelperHtmlUC::putDialogActions($prefix, $buttonTitle, $loaderTitle, $successTitle);
				?>
				
					
			</div>		
		<?php 
	}
	
	
	/**
	 * put add addon dialog
	 */
	private function putDialogAddAddon(){
		?>
			<!-- add addon dialog -->
			
			<div id="dialog_add_addon" class="unite-inputs" title="<?php _e("Add Addon",ADDONLIBRARY_TEXTDOMAIN)?>" style="display:none;">
			
				<div class="unite-dialog-top"></div>
			
				<div class="unite-inputs-label">
					<?php _e("Addon Title", ADDONLIBRARY_TEXTDOMAIN)?>:
				</div>
				
				<input type="text" id="dialog_add_addon_title" class="dialog_addon_input unite-input-regular" />
				
				<div class="unite-inputs-sap"></div>
				
				<div class="unite-inputs-label">
					<?php _e("Addon Name")?>:
				</div>
				
				<input type="text" id="dialog_add_addon_name" class="dialog_addon_input unite-input-alias" />
				
				<div class="unite-inputs-sap"></div>
				
				<div class="unite-inputs-label">
					<?php _e("Addon Description")?>:
				</div>
				
				<textarea id="dialog_add_addon_description" class="dialog_addon_input unite-input-regular"></textarea>
				
				<?php 
					$prefix = "dialog_add_addon";
					$buttonTitle = __("Add Addon", ADDONLIBRARY_TEXTDOMAIN);
					$loaderTitle = __("Adding Addon...", ADDONLIBRARY_TEXTDOMAIN);
					$successTitle = __("Addon Added Successfully", ADDONLIBRARY_TEXTDOMAIN);
					HelperHtmlUC::putDialogActions($prefix, $buttonTitle, $loaderTitle, $successTitle);
				?>			
				
			</div>
		
		<?php 
	}	
	

	/**
	 * put scripts
	 */
	private function putScripts(){
		
		$arrPlugins = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_MANAGER_ADDONS_PLUGINS, array());
		
		//$arrPlugins[] = "UCManagerMaster";
		
		$script = "
			jQuery(document).ready(function(){
				var selectedCatID = \"{$this->selectedCategory}\";
				var managerAdmin = new UCManagerAdmin();";
		
		if(!empty($arrPlugins)){
			foreach($arrPlugins as $plugin)
				$script .= "\n				managerAdmin.addPlugin('{$plugin}');";
		}
		
		$script .= "
				managerAdmin.initManager(selectedCatID);
			});
		";
	
		UniteProviderFunctionsUC::printCustomScript($script);
	}
	
	
	/**
	 * put preview tooltips
	 */
	protected function putPreviewTooltips(){
		?>
		<div id="uc_manager_addon_preview" class="uc-addon-preview-wrapper" style="display:none"></div>
		<?php 
	}
	
	
	/**
	 * put additional html here
	 */
	protected function putAddHtml(){
		$this->putDialogQuickEdit();
		$this->putDialogAddAddon();
		$this->putDialogImportAddons();
		
		if($this->showAddonTooltip)
			$this->putPreviewTooltips();
		
		$this->putScripts();
	}
	
	
	/**
	 * put init items
	 */
	protected function putInitItems(){
		
		if($this->hasCats == true)
			return(false);
		
		$objAddons = new UniteCreatorAddons();
		$htmlAddons = $objAddons->getCatAddonsHtml(null, $this->filterActive);
		
		echo $htmlAddons;
	}
	
	/**
	 * 
	 * set the custom data to manager wrapper div
	 */
	protected function onBeforePutHtml(){
		
		$addonsType = $this->filterAddonType;
		
		$addHTML = "data-addonstype=\"{$addonsType}\"";
		
		$this->setManagerAddHtml($addHTML); 
	}
	
	
	/**
	 * init the addons manager
	 */
	protected function init(){
		
		$this->hasCats = true;
		
		parent::init();
		
		$this->itemsLoaderText = __("Getting Addons",ADDONLIBRARY_TEXTDOMAIN);
		$this->textItemsSelected = __("addons selected",ADDONLIBRARY_TEXTDOMAIN);
		
		$this->filterActive = UniteCreatorAddons::getStateFilterActive();
		
		//set selected category
		$lastCatID = HelperUC::getState(UniteCreatorAddons::STATE_LAST_ADDONS_CATEGORY);
		if(!empty($lastCatID))
			$this->selectedCategory = $lastCatID;
		
	}
	
	
}