<?php
/**
 * @package Addon Library
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('ADDON_LIBRARY_INC') or die('Restricted access');


class UniteCreatorGridBuilder{

	const ID_PREFIX = "uc_grid_builder";
	private static $serial = 0;
	
	private $gridID;
	private $initData;
	private $putJs = false;
	
	protected $showGridSettingButton = true;
	protected $browserAddonType = null;
	
	
	/**
	 * constructor
	 */
	public function __construct(){
		
		$this->browserAddonType = GlobalsUC::$layoutsAddonType;
		
	}
	
	
	/**
	 * set grid ID
	 */
	public function setGridID($gridID){
		$this->gridID = $gridID;
	}
	
		
	
	/**
	 * set to put js init
	 */
	public function putJsInit(){
		$this->putJs = true;
	}
	
	
	/**
	 * set the layout object
	 */
	public function initByLayout(UniteCreatorLayout $objLayout){
		
		$this->initData = $objLayout->getGridDataForEditor();
	}
	
	
	/**
	 * put top panel
	 */
	private function putTopPanel(){
		?>
				
		<div class="uc-grid-builder-top-panel">
			
			<a id="uc_button_grid_settings" href="javascript:void(0)" class="unite-button-secondary unite-float-right"><?php _e("Grid Settings",ADDONLIBRARY_TEXTDOMAIN)?></a>
			
		<div class="unite-clear"></div>
		</div>
		
		
		<?php 
	}
	
	
	/**
	 * put bottom panel
	 */
	private function putBottomPanel(){
		?>
		
		<div id="uc_grid_builder_bottom_panel" class="uc-grid-builder-bottom-panel">
			
			<a href="javascript:void(0)" data-action="add_row" data-actiontype="grid" title="Add Row" class="uc-grid-action-icon uc-button-addrow-wrapper"><i class="fa fa-plus" aria-hidden="true"></i><span><?php _e("Add Row",ADDONLIBRARY_TEXTDOMAIN)?></span></a>
			
		<div class="unite-clear"></div>
		</div>
		
		<?php 
	}
	
	
	/**
	 * put js init
	 */
	private function putJs(){
		?>
			<script type="text/javascript">

				jQuery(document).ready(function(){

					var objBuilder = new UniteCreatorGridBuilder();
					objBuilder.init("#<?php echo $this->gridID?>");
					
				});
							
			</script>
		
		<?php 
	}
	
		
	/**
	 * put global settings dialog. stand alone function
	 */
	public function putLayoutsGlobalSettingsDialog(){
		
		$settingsGeneral = UniteCreatorLayout::getGlobalSettingsObject();
		
		$outputGeneralSettings = new UniteCreatorSettingsOutput();
		$outputGeneralSettings->setShowSaps(true);
		$outputGeneralSettings->init($settingsGeneral);
		
		?>
		
		<div id="uc_dialog_layout_global_settings" title="<?php _e("Layouts Global Settings", ADDONLIBRARY_TEXTDOMAIN)?>" class="unite-inputs" style="display:none">
				
				<div class="unite-dialog-inner-constant">
		
				<?php 		
					$outputGeneralSettings->draw("uc_layout_general_settings", true);
					
				?>
				</div>
				
				<?php 
					$prefix = "uc_dialog_layout_global_settings";
					$buttonTitle = __("Update Global Settings", ADDONLIBRARY_TEXTDOMAIN);
					$loaderTitle = __("Updating...", ADDONLIBRARY_TEXTDOMAIN);
					$successTitle = __("Settings Updated", ADDONLIBRARY_TEXTDOMAIN);
					HelperHtmlUC::putDialogActions($prefix, $buttonTitle, $loaderTitle, $successTitle);
				?>
				
		</div>		
		
		
		<?php
	}
	
	
	
	
	/**
	 * get grid options - from global object and grid settings
	 * they can be not overriden because they will be overriden in the front
	 * only keys will be overriden
	 */
	private function getGridCombinedOptions(){
		
		$optionsGlobal = UniteCreatorLayout::getGridGlobalOptions();
		$optionsGrid = UniteCreatorLayout::getGridSettingsOptions();
		
		//merge only missing keys
		
		foreach($optionsGrid as $key=>$value){
			
			if(array_key_exists($key, $optionsGlobal) == false)
				$optionsGlobal[$key] = $value;
		}
		
		return($optionsGlobal);
	}
	
	
	/**
	 * modify grid settings for dialog
	 */
	private function modifyGridDialogSettings($objGridSettings){
		
		$arrSettings = $objGridSettings->getArrSettings();
		
		$descPrefix = __(". If %s, it will be set to global value: ", ADDONLIBRARY_TEXTDOMAIN);
		
		$optionsGlobal = UniteCreatorLayout::getGridGlobalOptions();
		
		$arrExceptToEmpty = array("show_row_titles");
		
		foreach($arrSettings as $setting){
		
			$name = UniteFunctionsUC::getVal($setting, "name");
		
			//set replace sign
			switch($name){
				case "show_row_titles":
					$replaceSign = "default";
					break;
				default:
					$replaceSign = "empty";
				break;
			}
		
			$descActualPrefix = sprintf($descPrefix, $replaceSign);
		
			//handle excepts
			$globalOptionExists = array_key_exists($name, $optionsGlobal);
			if($globalOptionExists == false)
				continue;
		
			$globalValue = UniteFunctionsUC::getVal($optionsGlobal, $name);
			$setting["description"] .=  $descActualPrefix.$globalValue;
		
			//handle to empty excerpts
			$isExceptEmpty = array_search($name, $arrExceptToEmpty);
			if($isExceptEmpty === false){
				$setting["value"] = "";
				$setting["default_value"] = "";
			}
		
			$objGridSettings->updateArrSettingByName($name, $setting);
		
		}
		
		return($objGridSettings);
	}	
	
	
	/**
	 * put grid settings dialog
	 * the values will be set in js
	 */
	private function putGridSettingsDialog(){
				
		//$settings = new UniteCreatorSettings();
		$objGridSettings = UniteCreatorLayout::getGridSettingsObject();
		$objGridSettings = $this->modifyGridDialogSettings($objGridSettings);
		
		$output = new UniteCreatorSettingsOutput();
		
		$output->setShowSaps(true);
		$output->init($objGridSettings);
		
		
		?>
			
			<div id="uc_dialog_grid_settings" title="<?php _e("Grid Settings", ADDONLIBRARY_TEXTDOMAIN)?>" style="display:none">
				
				<div class="unite-dialog-inner-constant">
			
				<?php $output->draw("uc_settings_grid", true)?>
				
				</div>
				
				<?php 
					$prefix = "uc_dialog_grid_settings";
					$buttonTitle = __("Update Grid Settings", ADDONLIBRARY_TEXTDOMAIN);
					$loaderTitle = __("Updating...", ADDONLIBRARY_TEXTDOMAIN);
					$successTitle = __("Settings Updated", ADDONLIBRARY_TEXTDOMAIN);
					HelperHtmlUC::putDialogActions($prefix, $buttonTitle, $loaderTitle, $successTitle);
				?>
				
		</div>
		
		<?php 
	}
	
	
	/**
	 * get row settings object
	 */
	private function getRowSettingsObject(){
		
		$filepathSettings = GlobalsUC::$pathSettings."layout_row_settings.xml";
		
		$objSettings = new UniteCreatorSettings();
		$objSettings->loadXMLFile($filepathSettings);
		
		return($objSettings);
	}
	
	
	/**
	 * row settings
	 */
	private function putRowSettingsDialog(){
		
		$settings = $this->getRowSettingsObject();
		
		$output = new UniteCreatorSettingsOutput();
		$output->init($settings);
		$output->setShowSaps();
		
		?>
		<div id="uc_dialog_row_settings" title="<?php _e("Row Settings", ADDONLIBRARY_TEXTDOMAIN)?>" style="display:none">
				
				<div class="unite-dialog-inner-constant">
				
				<?php $output->draw("uc_settings_grid_row", true)?>
				
				</div>
				
				<?php 
					$prefix = "uc_dialog_row_settings";
					$buttonTitle = __("Update Row Settings", ADDONLIBRARY_TEXTDOMAIN);
					$loaderTitle = __("Updating...", ADDONLIBRARY_TEXTDOMAIN);
					$successTitle = __("Settings Updated", ADDONLIBRARY_TEXTDOMAIN);
					HelperHtmlUC::putDialogActions($prefix, $buttonTitle, $loaderTitle, $successTitle);
				?>
			
		</div>
		<?php 
	}
	
	
	/**
	 * put browser dialog
	 */
	private function putBrowserDialog(){
		
		$objBrowser = new UniteCreatorBrowser();
		$objBrowser->initAddonType($this->browserAddonType);
		
		?>
				<div class="uc-grid-builder-dialog-browser" title="<?php _e("Choose Addon", ADDONLIBRARY_TEXTDOMAIN)?>" style="display:none">
					<div class="uc-grid-builder-dialog-browser-inner">
						
						<?php $objBrowser->putBrowser() ?>
					
					</div>
				</div>
		<?php
	}
	
	
	
	/**
	 * put grid
	 */
	public function putGrid(){
		
		if(empty($this->gridID)){
			self::$serial++;
			$this->gridID = self::ID_PREFIX.self::$serial;
		}
		
		$gridID = $this->gridID;
		
		//get data-init='...'
		
		$initData = "";
		if(!empty($this->initData)){
			$initData = UniteFunctionsUC::jsonEncodeForHtmlData($this->initData, "init");
		}
		
		$options = $this->getGridCombinedOptions();
		
		$dataOptions = UniteFunctionsUC::jsonEncodeForHtmlData($options, "options");
		
		?>
			<div class="uc-grid-builder-wrapper">
				
				<style type="text/css"></style>
				
				<?php 
				
				if($this->showGridSettingButton == true)
					$this->putTopPanel()
				
				?>
				
				<div id="<?php echo $gridID?>" class="uc-grid-builder" <?php echo $initData.$dataOptions?> >
				</div>
				
				
				<?php 
				
				$this->putBottomPanel();
				$this->putBrowserDialog();
				$this->putGridSettingsDialog();
				$this->putRowSettingsDialog();
				
				?>
				
			</div>
			
			
			<?php 
				if($this->putJs == true)
					$this->putJs();
		
	}
	
	
}