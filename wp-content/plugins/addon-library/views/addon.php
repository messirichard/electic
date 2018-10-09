<?php

defined('ADDON_LIBRARY_INC') or die;

class UniteCreatorAddonView{

	protected $objAddon;
	protected $settingsItemOutput;
	protected $showToolbar = true;
	protected $showHeader = true;
	
	
	/**
	 * constructor
	 */
	public function __construct(){
		$this->init();
		$this->putHtml();
		
	}
	
	
	/**
	 * get settings item output
	 */
	private function initSettingsItem(){
		$options = $this->objAddon->getOptions();
		$paramsItems = $this->objAddon->getParamsItems();
		
		//items editor - settings
		$settingsItem = new UniteCreatorSettings();
		$settingsItem->addRadioBoolean("enable_items", __("Enable Items", ADDONLIBRARY_TEXTDOMAIN), false);
	
		$settingsItem->setStoredValues($options);
	
		$this->settingsItemOutput = new UniteSettingsOutputInlineUC();
		$this->settingsItemOutput->init($settingsItem);
		$this->settingsItemOutput->setAddCss("[wrapperid] .unite_table_settings_wide th{width:100px;}");
	
	}
	
	
	/**
	 * init the view
	 */
	private function init(){
		$addonID = UniteFunctionsUC::getGetVar("id","",UniteFunctionsUC::SANITIZE_ID);
		
		if(empty($addonID))
			UniteFunctionsUC::throwError("Addon ID not given");
		
		$this->objAddon = new UniteCreatorAddon();
		$this->objAddon->initByID($addonID);
		
		
		$this->initSettingsItem();
		
	}
	
	
	/**
	 * get header title
	 */
	protected function getHeaderTitle(){
		$title = $this->objAddon->getTitle(true);
		$addonID = $this->objAddon->getID();
		
		$headerTitle = __("Edit Addon",ADDONLIBRARY_TEXTDOMAIN);
		$headerTitle .= " - " . $title;
		
		return($headerTitle);
	}
	
	
	/**
	 * put top html
	 */
	private function putHtml_top(){
		
		$headerTitle = $this->getHeaderTitle();
		
		require HelperUC::getPathTemplate("header");
	}
	
	/**
	 * init general settings from file
	 */
	private function initGeneralSettings(){

		$filepathAddonSettings = GlobalsUC::$pathSettings."addon_fields.php";
		
		require $filepathAddonSettings;
		
		return($generalSettings);
	}
	
	
	/**
	 * put general settings tab html
	 */
	private function putHtml_generalSettings(){
		
		$addonID = $this->objAddon->getID();
		$title = $this->objAddon->getTitle(true);
		
		$name = $this->objAddon->getNameByType();
		
		$generalSettings = $this->initGeneralSettings();
		
		//set options from addon
		$arrOptions = $this->objAddon->getOptions();
		$generalSettings->setStoredValues($arrOptions);
		
		$settingsOutput = new UniteCreatorSettingsOutput();
		$settingsOutput->init($generalSettings);
		
		?>
		
		<div class="uc-edit-addon-col uc-col-first">
		
			<span id="addon_id" data-addonid="<?php echo $addonID?>" style="display:none"></span>
			
			<?php _e("Addon Title", ADDONLIBRARY_TEXTDOMAIN); ?>:
			
			<div class="vert_sap5"></div>
			
			<input type="text" id="text_addon_title" value="<?php echo $title?>" class="unite-input-regular">
			
			<!-- NAME -->
			
			<div class="vert_sap15"></div>
			
			<?php _e("Addon Name", ADDONLIBRARY_TEXTDOMAIN); ?>:
			
			<div class="vert_sap5"></div>
			
			<input type="text" id="text_addon_name" value="<?php echo $name?>" class="unite-input-regular">
			
		</div>
		
		<div class="uc-edit-addon-col uc-col-second">
				<?php 
					$settingsOutput->draw("uc_general_settings", true); 
				?>
		</div>
		
		
		<div class="unite-clear"></div>
		
		<div class="vert_sap15"></div>
		
		
		<?php
		
	}
	
	
	/**
	 * put tabs html
	 */
	private function putHtml_tabs(){
		?>
		
		<div id="uc_tabs" class="uc-tabs" data-inittab="uc_tablink_general">
			
			<a id="uc_tablink_general" href="javascript:void(0)" data-contentid="uc_tab_general">
				<?php _e("General", ADDONLIBRARY_TEXTDOMAIN)?> 
			</a>
		
			<a id="uc_tablink_attr" href="javascript:void(0)" data-contentid="uc_tab_attr">
				<?php _e("Attributes", ADDONLIBRARY_TEXTDOMAIN)?> 
			</a>
			
			<a id="uc_tablink_itemattr" href="javascript:void(0)" data-contentid="uc_tab_itemattr">
				<?php _e("Item Attributes", ADDONLIBRARY_TEXTDOMAIN)?> 
			</a>
			
			<a id="uc_tablink_html" href="javascript:void(0)" data-contentid="uc_tab_html">
				<?php _e("HTML", ADDONLIBRARY_TEXTDOMAIN)?>
			</a>
			<a id="uc_tablink_css" href="javascript:void(0)" data-contentid="uc_tab_css">
				<?php _e("CSS", ADDONLIBRARY_TEXTDOMAIN)?>
			</a>
			<a id="uc_tablink_js" href="javascript:void(0)" data-contentid="uc_tab_js">
				<?php _e("Javascript", ADDONLIBRARY_TEXTDOMAIN)?>
			</a>
			<a id="uc_tablink_includes" href="javascript:void(0)" data-contentid="uc_tab_includes">
				<?php _e("js/css Includes", ADDONLIBRARY_TEXTDOMAIN)?>
			</a>
			<a id="uc_tablink_assets" href="javascript:void(0)" data-contentid="uc_tab_assets">
				<?php _e("Assets", ADDONLIBRARY_TEXTDOMAIN)?>
			</a>
			
		</div>
		
		<div class="unite-clear"></div>
		
		<?php 
	}
	
	
	/**
	 * put item for library include
	 */
	private function putIncludeLibraryItem($title, $name, $arrIncludes){
	
		$htmlChecked = "";
		if(in_array($name, $arrIncludes) == true)
			$htmlChecked = "checked='checked'";
	
		?>
		
			<li>
				<label for="check_include_<?php echo $name?>">
					<?php echo $title?>
				</label>
				
				<input type="checkbox" id="check_include_<?php echo $name?>" data-include="<?php echo $name?>" <?php echo $htmlChecked?>>
				
			</li>
		
		<?php 
	}

	
	/**
	 * put library includes
	 */
	private function putHtml_LibraryIncludes($arrJsLibIncludes){
		
		$objLibrary = new UniteCreatorLibrary();
		$arrLibrary = $objLibrary->getArrLibrary();
				
		foreach($arrLibrary as $item){
			$name = $item["name"];
			$title = $item["title"];
			
			$this->putIncludeLibraryItem($title, $name, $arrJsLibIncludes);
		}
		
			
	}
	
	/**
	 * put includes assets browser
	 */
	private function putHtml_Includes_assetsBrowser(){
		
		$objAssets = new UniteCreatorAssetsWork();
		$objAssets->initByKey("includes", $this->objAddon);
		$pathAssets = $this->objAddon->getPathAssetsFull();
		$objAssets->putHTML($pathAssets);
		
	}
	
	
	/**
	 * put includes html
	 */
	private function putHtml_Includes(){
		
		$arrJsLibIncludes = $this->objAddon->getJSLibIncludes();
		$arrJsIncludes = $this->objAddon->getJSIncludes();
		$arrCssIncludes = $this->objAddon->getCSSIncludes();
		
		$dataJs = UniteFunctionsUC::jsonEncodeForHtmlData($arrJsIncludes, "init");
		$dataCss = UniteFunctionsUC::jsonEncodeForHtmlData($arrCssIncludes, "init");
		
		
		?>
			<table id="uc_table_includes" class="unite_table_items">
				<thead>
					<tr>
						<th class="uc-table-includes-left">
							<b>
							<?php _e("Choose From Browser", ADDONLIBRARY_TEXTDOMAIN)?>
							</b>
						</th>
						<th class="uc-table-includes-right">
							<b>
							<?php _e("JS / Css Includes", ADDONLIBRARY_TEXTDOMAIN)?>
							</b>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td valign="top">
							<?php $this->putHtml_Includes_assetsBrowser(); ?>
						</td>
						<td valign="top">
							
							<ul id="uc-js-libraries" class="unite-list-hor">
								<li class="pright_10">
									<span class="unite-title2"><?php _e("Libraries", ADDONLIBRARY_TEXTDOMAIN)?>:</span> </b>
								</li>
								<?php $this->putHtml_LibraryIncludes($arrJsLibIncludes)?>
							</ul>
							
							<div class="unite-clear"></div>
							
							<div id="uc_includes_wrapper">
								
								<div class="unite-title2">Js Includes:</div>
								
								<ul id="uc-js-includes" class="uc-js-includes" data-type="js" <?php echo $dataJs?>></ul>
								
								<div class="unite-title2">Css Includes:</div>
								
								<ul id="uc-css-includes" class="uc-css-includes" data-type="css" <?php echo $dataCss?>></ul>
							
							</div>
							
						</td>
					</tr>
				</tbody>
			</table>
			
			<div id="uc_dialog_unclude_settings" title="<?php _e("Include Settings")?>" class="unite-inputs" style="display:none">
				<div class="unite-dialog-inside">
				
					<?php _e("Include When:", ADDONLIBRARY_TEXTDOMAIN)?>
					
					<span class="hor_sap"></span>
					
					<select id="uc_dialog_include_attr"></select>
					
					<span id="uc_dialog_include_value_container" style="display:none">
					
						<span class="hor_sap5"></span>
						
						<?php _e("equals", ADDONLIBRARY_TEXTDOMAIN)?>
						
						<span class="hor_sap5"></span>
						
						<select id="uc_dialog_include_values"></select>
						
					</span>
					
					<?php HelperHtmlUC::putDialogControlFieldsNotice() ?>
				</div>
			</div>
			
						
			<?php 
			
	}
	
	
	/**
	 * put assets tab html
	 */
	private function putHtml_assetsTab(){
		
		$path = $this->objAddon->getPathAssets();
		$pathAbsolute = $this->objAddon->getPathAssetsFull();
		
		$textNotSet = __("[not set]", ADDONLIBRARY_TEXTDOMAIN);
		
		$unsetAddHtml = "style='display:none'";
		$htmlPath = $textNotSet;
		$dataPath = "";
		if(!empty($path)){
			$unsetAddHtml = "";
			$htmlPath = htmlspecialchars($path);
			$dataPath = $htmlPath;
		}
		
		?>
			<div class="uc-assets-folder-wrapper">
				<span class="uc-assets-folder-label"><?php _e("Addon Assets Path: ", ADDONLIBRARY_TEXTDOMAIN)?></span>
				<span id="uc_assets_path" class="uc-assets-folder-folder" data-path="<?php echo $dataPath?>" data-textnotset="<?php echo $textNotSet?>"><?php echo $htmlPath?></span>
				<a id="uc_button_set_assets_folder" href="javascript:void(0)" class="unite-button-secondary"><?php _e("Set", ADDONLIBRARY_TEXTDOMAIN)?></a>
				<a id="uc_button_set_assets_unset" href="javascript:void(0)" class="unite-button-secondary" <?php echo $unsetAddHtml?>><?php _e("Unset", ADDONLIBRARY_TEXTDOMAIN)?></a>
			</div>
		<?php 
		
		$objAssets = new UniteCreatorAssetsWork();
		$objAssets->initByKey("assets_manager");
		
		$objAssets->putHTML($pathAbsolute);
	}
	
	
	/**
	 * put html tab content
	 */
	private function putHtml_tabTableRow($textareaID, $title, $areaHtml, $paramsPanelID, $addVariableID = null, $isItemsRelated = false){
		
		$rowClass = "";
		$rowAddHtml = "";
		
		$paramsPanelClassAdd = " uc-params-panel-main";
		
		if($isItemsRelated == true){
			$rowClass = "class='uc-items-related'";
			$hasItems = $this->objAddon->isHasItems();
			
			if($hasItems == false)
				$rowAddHtml = "style='display:none'";
			
			$paramsPanelClassAdd = "";
			
		}
		
		
		
		?>
					<tr <?php echo $rowClass?> <?php echo $rowAddHtml?>>
						<td class="uc-tabcontent-cell-left">
							<div class="uc-editor-title"><?php echo $title?></div>
							<textarea id="<?php echo $textareaID?>" class="area_addon <?php echo $textareaID?>"><?php echo $areaHtml?></textarea>
						</td>
						<td class="uc-tabcontent-cell-right">

							<?php if($isItemsRelated == true):?>
								<div class="uc-params-panel-filters">
									<a href="javascript:void(0)" class="uc-filter-active" data-filter="item" onfocus="this.blur()"><?php _e("Item", ADDONLIBRARY_TEXTDOMAIN)?></a>
									<a href="javascript:void(0)" data-filter="main" onfocus="this.blur()"><?php _e("Main", ADDONLIBRARY_TEXTDOMAIN)?></a>
								</div>
							<?php endif?>
						
							<div id="<?php echo $paramsPanelID?>" class="uc-params-panel<?php echo $paramsPanelClassAdd?>"></div>
							
							<?php if(!empty($addVariableID)):?>
						    <a id="<?php echo $addVariableID?>" type="button" href="javascript:void(0)" class="unite-button-secondary mleft_20"><?php _e("Add Variable", ADDONLIBRARY_TEXTDOMAIN)?></a>
							<?php endif?>
							
						</td>
					</tr>
		
		<?php 
	}
	
	
	/**
	 * put tab table sap
	 */
	private function putHtml_tabTableSap($isItemsRelated = false){
		
		$rowClass = "";
		if($isItemsRelated == true)
			$rowClass = "class='uc-items-related'";
		
		?>
			<tr <?php echo $rowClass?>>
				<td colspan="2"><div class="vert_sap10"></div></td>
			</tr>
		<?php 
	}
	
	
	/**
	 * put overwiew tab html
	 */
	private function putHtml_overviewTab(){
		
		$title = $this->objAddon->getTitle();
		$name = $this->objAddon->getName();
		$description = $this->objAddon->getDescription();
		$link = $this->objAddon->getOption("link_resource");
		if(!empty($link))
			$link = HelperHtmlUC::getHtmlLink($link, $link, "uc_overview_link","",true);
		
		$addonIcon = $this->objAddon->getUrlIcon();
		
		
		?>
		<div class="uc-tab-overview">
			<div class="uc-section-inline"><?php _e("Addon Title", ADDONLIBRARY_TEXTDOMAIN)?>: <span id="uc_overview_title" class="unite-bold"><?php echo $title?></span></div>
			<div class="uc-section-inline"><?php _e("Addon Name", ADDONLIBRARY_TEXTDOMAIN)?>: <span id="uc_overview_name" class="unite-bold"><?php echo $name?></span></div>
			<div class="uc-section">
				<div class="uc-section-title"><?php _e("Addon Description", ADDONLIBRARY_TEXTDOMAIN)?>:</div>
				<div id="uc_overview_description" class="uc-section-content uc-desc-wrapper">
					<?php echo $description?>
				</div>
				<div class="unite-clear"></div>
			</div>
			<div class="uc-section-inline"><?php _e("Link to resource", ADDONLIBRARY_TEXTDOMAIN)?>: <?php echo $link?></div>
			<div class="uc-section">
				<div class="uc-section-title uc-title-icon"><?php _e("Addon Icon", ADDONLIBRARY_TEXTDOMAIN)?>:</div>
				<div id="uc_overview_icon" class="uc-section-content uc-addon-icon-small" style="background-image:url('<?php echo $addonIcon?>')"></div> 
			</div>
			
		</div>
		
		
		<?php
	}
	
	
	/**
	 * put tabs content
	 */
	private function putHtml_content(){
		
		$css = $this->objAddon->getCss(true);
		$html = $this->objAddon->getHtml(true);
		$htmlItem = $this->objAddon->getHtmlItem(true);
		$htmlItem2 = $this->objAddon->getHtmlItem2(true);
		
		$js = $this->objAddon->getJs(true);
		$hasItems = $this->objAddon->isHasItems();
		
		$params = $this->objAddon->getParams();
		$paramsItems = $this->objAddon->getParamsItems();
		
		$paramsEditorItems = new UniteCreatorParamsEditor();
		
		if($hasItems == false)
			$paramsEditorItems->setHiddenAtStart();
		
		$paramsEditorItems->init("items");
		
		?>
		
		<div id="uc_tab_contents" class="uc-tabs-content-wrapper uc-addon-props">
			
			<!-- General -->
			
			<div id="uc_tab_general" class="uc-tab-content" style="display:none">
				
				<?php 
				try{
					
					$this->putHtml_generalSettings();
					
				}catch(Exception $e){
					HelperHtmlUC::outputException($e);
				}
				?>
					
			</div>
			
			<!-- Attributes -->
			
			<div id="uc_tab_attr" class="uc-tab-content" style="display:none">
					
				<?php 
					$paramsEditorMain = new UniteCreatorParamsEditor();
					$paramsEditorMain->init("main");
					$paramsEditorMain->outputHtmlTable();
				?>
				
			</div>
			
			<!-- Item Attributes -->
			
			<div id="uc_tab_itemattr" class="uc-tab-content uc-tab-itemattr" style="display:none">
			
				<?php 
					$this->settingsItemOutput->draw("uc_form_edit_addon");
					$paramsEditorItems->outputHtmlTable();
				?>
			
			</div>
			
			
			<!-- HTML -->
		
			<div id="uc_tab_html" class="uc-tab-content" style="display:none">
						
				<table class="uc-tabcontent-table">
					
					<?php 
						
						//------------- put html row
					
						$textareaID = "area_addon_html";
						$rowTitle = __("Addon HTML",ADDONLIBRARY_TEXTDOMAIN);
						$areaHtml = $html;
						$paramsPanelID = "uc_params_panel_main";
						$addVariableID = "uc_params_panel_main_addvar";
						
						$this->putHtml_tabTableRow($textareaID, $rowTitle, $areaHtml, $paramsPanelID, $addVariableID);
						
						//------------- put html item row
						
						$this->putHtml_tabTableSap(true);
						
						$textareaID = "area_addon_html_item";
						$rowTitle = __("Addon Item HTML",ADDONLIBRARY_TEXTDOMAIN);
						$areaHtml = $htmlItem;
						$paramsPanelID = "uc_params_panel_item";
						$addVariableID = "uc_params_panel_item_addvar";
						$isItemsRelated = true;
												
						$this->putHtml_tabTableRow($textareaID, $rowTitle, $areaHtml, $paramsPanelID, $addVariableID, $isItemsRelated);

						$this->putHtml_tabTableSap(true);
						
						//------------- put html item row 2
						
						$textareaID = "area_addon_html_item2";
						$rowTitle = __("Addon Item HTML 2",ADDONLIBRARY_TEXTDOMAIN);
						$areaHtml = $htmlItem2;
						$paramsPanelID = "uc_params_panel_item2";
						$addVariableID = "uc_params_panel_item_addvar2";
						$isItemsRelated = true;
						
						$this->putHtml_tabTableRow($textareaID, $rowTitle, $areaHtml, $paramsPanelID, $addVariableID, $isItemsRelated);
						
					?>				
					
				</table>
				
			</div>
			
			<!-- CSS -->
			
			<div id="uc_tab_css" class="uc-tab-content" style="display:none">
			
				<table class="uc-tabcontent-table">
					<tr>
						<td class="uc-tabcontent-cell-left">
							<div class="uc-editor-title"><?php _e("Addon CSS", ADDONLIBRARY_TEXTDOMAIN)?></div>
						
							<textarea id="area_addon_css" class="area_addon area_addon_css"><?php echo $css?></textarea>
						</td>
						<td class="uc-tabcontent-cell-right">
							<div id="uc_params_panel_css" class="uc-params-panel"></div>
						</td>
					</tr>
				</table>
			
			</div>
			
			<!-- JS -->
			
			<div id="uc_tab_js" class="uc-tab-content" style="display:none">
				
				<table class="uc-tabcontent-table">
					<tr>
						<td class="uc-tabcontent-cell-left">
							<div class="uc-editor-title"><?php _e("Addon Javascript", ADDONLIBRARY_TEXTDOMAIN)?></div>
						
							<textarea id="area_addon_js" class="area_addon area_addon_js"><?php echo $js?></textarea>
						</td>
						<td class="uc-tabcontent-cell-right">
							<div id="uc_params_panel_js" class="uc-params-panel"></div>
						</td>
					</tr>
				</table>
				
			</div>
			
			<!-- INCLUDES -->
			<div id="uc_tab_includes" class="uc-tab-content" style="display:none">
				
				<?php $this->putHtml_Includes()?>
				
			</div>
	
			<div id="uc_tab_assets" class="uc-tab-content" style="display:none">
				
				<?php $this->putHtml_assetsTab() ?>
				
			</div>
			
		</div>
		
		<!-- END TABS -->
		
		
		<?php 
	}

	
	/**
	 * put action buttons html
	 */
	private function putHtml_actionButtons(){
		
		$addonID = $this->objAddon->getID();
		$addonType = $this->objAddon->getType();
		
		$urlTestAddon = HelperUC::getViewUrl_TestAddon($addonID);
		
		$urlPreviewAddon = HelperUC::getViewUrl_TestAddon($addonID,"preview=1");
		
		?>
		
		<div class="uc-edit-addon-buttons-panel-wrapper">
		
			<div id="uc_buttons_panel" class="uc-edit-addon-buttons-panel">
			
				<div class="unite-float-left">
				
					<div class="uc-button-action-wrapper">
						<a id="button_update_addon" class="button_update_addon unite-button-primary" href="javascript:void(0)"><?php _e("Update", ADDONLIBRARY_TEXTDOMAIN);?></a>
						
						<div style="padding-top:6px;">
							
							<span id="uc_loader_update" class="loader_text" style="display:none"><?php _e("Updating...", ADDONLIBRARY_TEXTDOMAIN)?></span>
							<span id="uc_message_addon_updated" class="unite-color-green" style="display:none"></span>
							
						</div>
					</div>
					
					<a class="unite-button-secondary" href="<?php echo HelperUC::getViewUrl_Addons($addonType)?>"><?php _e("Back to Addons List", ADDONLIBRARY_TEXTDOMAIN);?></a>
					
					<a href="<?php echo $urlTestAddon?>" class="unite-button-secondary " ><?php _e("Test Addon", ADDONLIBRARY_TEXTDOMAIN)?></a>
					
					<a href="<?php echo $urlPreviewAddon?>" class="unite-button-secondary " ><?php _e("Preview Addon", ADDONLIBRARY_TEXTDOMAIN)?></a>
					
				</div>
				
				<?php 
					
					UniteProviderFunctionsUC::doAction(UniteCreatorFilters::ACTION_EDIT_ADDON_EXTRA_BUTTONS);
					
				?>
				
				<div class="unite-clear"></div>
				
				<div id="uc_update_addon_error" class="unite_error_message" style="display:none"></div>
			
			</div>
		</div>
		<?php 
	}
	
	
	
	/**
	 * put config
	 */
	private function putConfig(){
		
		$options = array();
		$options["url_preview"] = $this->objAddon->getUrlPreview();
		$dataOptions = UniteFunctionsUC::jsonEncodeForHtmlData($options, "options");
		
		$params = $this->objAddon->getParams();
		$dataParams = UniteFunctionsUC::jsonEncodeForHtmlData($params, "params");
		
		$paramsItems = $this->objAddon->getParamsItems();
		$dataParamsItems = UniteFunctionsUC::jsonEncodeForHtmlData($paramsItems, "params-items");
		
		$variablesItems = $this->objAddon->getVariablesItem();
		$variablesMain = $this->objAddon->getVariablesMain();
		
		$dataVarItems = UniteFunctionsUC::jsonEncodeForHtmlData($variablesItems, "variables-items");
		$dataVarMain = UniteFunctionsUC::jsonEncodeForHtmlData($variablesMain, "variables-main");
		
		$objOutput = new UniteCreatorOutput();
		$objOutput->initByAddon($this->objAddon);
		
		$arrConstantData = $objOutput->getConstantDataKeys();
		$dataPanelKeys = UniteFunctionsUC::jsonEncodeForHtmlData($arrConstantData, "panel-keys");
		
		$arrItemConstantData = $objOutput->getItemConstantDataKeys();
		$dataItemPanelKeys = UniteFunctionsUC::jsonEncodeForHtmlData($arrItemConstantData, "panel-item-keys");
		
		
		?>
		
		<div id="uc_edit_item_config" style="display:none"
			<?php echo $dataParams?>	
			<?php echo $dataParamsItems?>
			<?php echo $dataPanelKeys?>
			<?php echo $dataItemPanelKeys?>
			<?php echo $dataVarItems?>
			<?php echo $dataVarMain?>
			<?php echo $dataOptions?>
		></div>
		
		<?php 
	}
	
	
	/**
	 * put js
	 */
	private function putJs(){
		?>
		
		<script type="text/javascript">
		
		jQuery(document).ready(function(){
			var objAdmin = new UniteCreatorAdmin();
			objAdmin.initEditAddonView();
		});
		
		</script>
		
		<?php 
	}
	
	
	/**
	 * put params and variables dialog
	 */
	private function putDialogs(){
		
		//dialog param		
		$objDialogParam = new UniteCreatorDialogParam();
		$objDialogParam->init(UniteCreatorDialogParam::TYPE_MAIN, $this->objAddon);
		$objDialogParam->outputHtml();
	
		//dialog variable item
		
		$objDialogVariableItem = new UniteCreatorDialogParam();
		$objDialogVariableItem->init(UniteCreatorDialogParam::TYPE_ITEM_VARIABLE, $this->objAddon);
		$objDialogVariableItem->outputHtml();
		
		//dialog variable main
		$objDialogVariableMain = new UniteCreatorDialogParam();
		$objDialogVariableMain->init(UniteCreatorDialogParam::TYPE_MAIN_VARIABLE, $this->objAddon);
		$objDialogVariableMain->outputHtml();
		
		
	}
	
	
	/**
	 * put html
	 */
	private function putHtml(){
		
		if($this->showHeader == true)
			$this->putHtml_top();
		
		if($this->showToolbar == true)
			$this->putHtml_actionButtons();
		
		$this->putHtml_tabs();
		$this->putHtml_content();
		$this->putConfig();
		$this->putJs();
		
		$this->putDialogs();
		
	}
	
	
}


$pathProviderAddon = GlobalsUC::$pathProvider."views/addon.php";

if(file_exists($pathProviderAddon) == true){
	require_once $pathProviderAddon;
	new UniteCreatorAddonViewProvider();
}
else{
	new UniteCreatorAddonView();
}

		
	
?>