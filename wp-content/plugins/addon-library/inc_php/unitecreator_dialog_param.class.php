<?php
/**
 * @package Addon Library
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('ADDON_LIBRARY_INC') or die('Restricted access');

class UniteCreatorDialogParam{
	
	const TYPE_MAIN = "main";
	const TYPE_ITEM_VARIABLE = "variable_item";
	const TYPE_MAIN_VARIABLE = "variable_main";
	const PARAM_EDITOR = "uc_editor";
	
	
	private $addon;
	private $type;
	private $arrContentIDs = array();
	private $arrParamsTypes = array();
	private $arrParams = array();
	
	private $option_putTitle = true;
	private $option_putAdminLabel = true;
	private $option_arrTexts = array();
	
	
	/**
	 * init all params
	 */
	public function __construct(){
		$this->initParamTypes();
	}
	
	
	/**
	 * add param to the list
	 */
	private function addParam($paramType, $paramText){
		$this->arrParamsTypes[$paramType] = $paramText;
	}
	
	
	/**
	 * set the param types
	 */
	private function initParamTypes(){
		
		$this->addParam("uc_textfield", __("Text Field", ADDONLIBRARY_TEXTDOMAIN));
		$this->addParam("uc_number", __("Number", ADDONLIBRARY_TEXTDOMAIN));
		$this->addParam("uc_radioboolean", __("Radio Boolean", ADDONLIBRARY_TEXTDOMAIN));
		$this->addParam("uc_textarea", __("Text Area", ADDONLIBRARY_TEXTDOMAIN));
		$this->addParam(self::PARAM_EDITOR, __("Editor", ADDONLIBRARY_TEXTDOMAIN));
		$this->addParam("uc_checkbox", __("Checkbox", ADDONLIBRARY_TEXTDOMAIN));
		$this->addParam("uc_dropdown", __("Dropdown", ADDONLIBRARY_TEXTDOMAIN));
		$this->addParam("uc_colorpicker", __("Color Picker", ADDONLIBRARY_TEXTDOMAIN));
		$this->addParam("uc_image", __("Image", ADDONLIBRARY_TEXTDOMAIN));
		$this->addParam("uc_mp3", __("Audio", ADDONLIBRARY_TEXTDOMAIN));
		
		//variables
		$this->addParam("uc_varitem_simple", __("Simple Variable", ADDONLIBRARY_TEXTDOMAIN));
		$this->addParam("uc_var_paramrelated", __("Attribute Related", ADDONLIBRARY_TEXTDOMAIN));
		$this->addParam("uc_var_paramitemrelated", __("Item Attribute Related", ADDONLIBRARY_TEXTDOMAIN));
		
	}
	
	
	/**
	 * validate that the dialog inited
	 */
	private function validateInited(){
		if(empty($this->type))
			UniteFunctionsUC::throwError("Empty params dialog");
	}

	
	private function ___________________MAIN_PARAMS________________(){}
	
	
	/**
	 * put default value param in params dialog
	 */
	private function putDefaultValueParam($isTextarea = false, $class=""){
		$strClass = "";
		if(!empty($class))
			$strClass = "class='{$class}'";
		
		?>
				<div class="unite-inputs-label">
					<?php _e("Default Value", ADDONLIBRARY_TEXTDOMAIN)?>:
				</div>
				
				<?php if($isTextarea == false):?>
				
				<input type="text" name="default_value" <?php echo $strClass?> value="">
				
				<?php else: ?>
				
				<textarea name="default_value" <?php echo $strClass?>> </textarea>
				
				<br><br>
				
				* <?php _e("To allow html tags, use",ADDONLIBRARY_TEXTDOMAIN)?> <b>|raw</b> <?php _e("filter", ADDONLIBRARY_TEXTDOMAIN) ?> <br><br>
				&nbsp;&nbsp;&nbsp; <?php _e("example",ADDONLIBRARY_TEXTDOMAIN)?> : {{myfield|raw}}
				
				<?php endif?>
		
		<?php 
	}
	
	/**
	 * put color picker default value
	 */
	private function putColorPickerDefault(){
		?>
			<?php _e("Default Value", ADDONLIBRARY_TEXTDOMAIN)?>:
			
			<div class="vert_sap5"></div>
 		    <input type="text" name="default_value" class="uc-text-colorpicker" value="#ffffff" data-initval="#ffffff">
			<div class='unite-color-picker-element'></div>
		<?php 
	}
	
	
	/**
	 * put number unit select
	 */
	private function putNumberUnitSelect(){
		?>
				<div class="unite-inputs-label-inline-suffix">
					<?php _e("Suffix")?>:
				</div>
				
				<select name="unit" class='uc-select-unit' data-initval="px">
					<option value="px">px</option>
					<option value="ms">ms</option>
					<option value="%">ms</option>
					<option value="">[none]</option>
					<option value="other">[custom]</option>
				</select>
				
				<input type="text" class='uc-text-unit-custom input-small' name="unit_custom" style="display:none">
		<?php
	}

	
	/**
	 * put radio yes no option
	 */
	private function putRadioYesNo($name, $text = null, $defaultTrue = false, $yesText = "Yes", $noText="No", $isTextNear = false){
	
		if($defaultTrue == true){
			$trueChecked = " checked ";
			$falseChecked = "";
			$defaultValue = "true";
		}else{
			$defaultValue = "false";
			$trueChecked = "";
			$falseChecked = " checked ";
		}
		
		//make not repeated id's
		$idPrefix = "uc_param_radio_".$this->type."_".$name;
		
		$idYes = $idPrefix."_yes";
		$idNo = $idPrefix."_no";
		
		?>
			<div class='uc-radioset-wrapper' data-defaultchecked="<?php echo $defaultValue?>">
			
			<?php if(!empty($text)): ?>
				<span class="uc-radioset-title">
				<?php _e($text, ADDONLIBRARY_TEXTDOMAIN)?>:
				</span>
			<?php endif?>
			
				<input id="<?php echo $idYes?>" type="radio" name="<?php echo $name?>" value="true" <?php echo $trueChecked?>>
				<label for="<?php echo $idYes?>"><?php _e($yesText, ADDONLIBRARY_TEXTDOMAIN)?></label>
				
				<input id="<?php echo $idNo?>" type="radio" name="<?php echo $name?>" value="false" <?php echo $falseChecked?>>
				<label for="<?php echo $idNo?>"><?php _e($noText, ADDONLIBRARY_TEXTDOMAIN)?></label>
				
				<?php if($isTextNear == true):?>
					<input type="text" name="text_near" class="unite-input-medium">
					<?php _e("(text near)", ADDONLIBRARY_TEXTDOMAIN)?>
					
				<?php endif?>
			</div>
			
		
		<?php 
	}
	
	
	/**
	 * put radio boolean param
	 */
	private function putRadioBooleanParam(){
		?>
			<table data-inputtype="radio_boolean"  class='uc-table-dropdown-items uc-table-dropdown-full'>
				<thead>
					<tr>
						<th width="100px"><?php _e("Item Text", ADDONLIBRARY_TEXTDOMAIN)?></th>
						<th width="100px"><?php _e("Item Value", ADDONLIBRARY_TEXTDOMAIN)?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><input type="text" name="true_name" value="Yes" data-initval="Yes" class='uc-dropdown-item-name'></td>
						<td><input type="text" name="true_value" value="true" data-initval="true" class='uc-dropdown-item-value'></td>
						<td>
							<div class='uc-dropdown-icon uc-dropdown-item-default uc-selected' title="<?php _e("Default Item", ADDONLIBRARY_TEXTDOMAIN)?>"></div>
						</td>
					</tr>
					<tr>
						<td><input type="text" name="false_name" value="No" data-initval="No" class='uc-dropdown-item-name'></td>
						<td><input type="text" name="false_value" value="false" data-initval="false" class='uc-dropdown-item-value'></td>
						<td>
							<div class='uc-dropdown-icon uc-dropdown-item-default' title="<?php _e("Default Item", ADDONLIBRARY_TEXTDOMAIN)?>"></div>
						</td>
					</tr>
					
				</tbody>
			</table>
		<?php 
	}
	
	
	/**
	 * add checkbox section param to image param type
	 */
	private function putImageParam_addThumbSection($thumbName, $text, $addSuffix){
		$IDprefix = "uc_param_image_".$this->type."_";
		
		$checkID = $IDprefix.$thumbName;
		$inputID = $IDprefix.$thumbName."_input";
		
		?>
			<label for="<?php echo $checkID?>">
				<input id="<?php echo $checkID?>" type="checkbox" class="uc-param-image-checkbox uc-control" data-controlled-selector="#<?php echo $inputID?>" name="<?php echo $thumbName?>">
				<?php _e($text, ADDONLIBRARY_TEXTDOMAIN)?>
			</label>
			<input id="<?php echo $inputID?>" type="text" data-addsuffix="<?php echo $addSuffix?>" style="display:none" disabled class="mleft_5 unite-input-alias uc-param-image-thumbname">
			
		<?php 
	}
	
	
	/**
	 * put single setting input
	 */
	private function putSingleSettingInput($name, $text, $type){
		
		?>			
			<div class="unite-inputs-label"><?php echo $text?>:</div>
		<?php 
		
		$objSettings = new UniteCreatorSettings();
		$objSettings->setCurrentAddon($this->addon);
		
		switch($type){
			case "image":
				$objSettings->addImage($name, "", $text, array("source"=>"addon"));
			break;
			case "mp3":
				$objSettings->addMp3($name, "", $text, array("source"=>"addon"));
			break;
			default:
				UniteFunctionsUC::throwError("Wrong seting type: $type");
			break;
		}
		
		$objOutput = new UniteCreatorSettingsOutput();
		$objOutput->init($objSettings);
		$objOutput->drawSingleSetting($name);
		
	}
	
	
	/**
	 * put image select input
	 */
	private function putImageSelectInput($name, $text){
		
		$this->putSingleSettingInput($name, $text, "image");
	}
	
	
	/**
	 * put mp3 select input
	 */
	private function putMp3SelectInput($name, $text){
		
		$this->putSingleSettingInput($name, $text, "mp3");
		
	}
	
	/**
	 * put image param settings
	 */
	private function putImageParam(){
		
		?>
			<div class="uc-tab-content-desc">
				<?php _e("* You can add thumbnails creation to the image. Turn them on if you will use them in addon", ADDONLIBRARY_TEXTDOMAIN)?>
			</div>
			
			<div class="unite-inputs-sap"></div>
			
			<?php $this->putImageParam_addThumbSection("add_thumb", "Add Thumbnail", "thumb") ?>
			
			<div class="unite-inputs-sap"></div>
			
			<?php $this->putImageParam_addThumbSection("add_thumb_large", "Add Thumbnail - Large","thumb_large") ?>
			
			<div class="unite-inputs-sap"></div>
						
			<?php $this->putImageSelectInput("default_value",__("Default Image",ADDONLIBRARY_TEXTDOMAIN)); ?>
			
		<?php 
	}

	
	/**
	 * put mp3 param
	 */
	private function putMp3Param(){
	
		$this->putMp3SelectInput("default_value",__("Default Audio File Url",ADDONLIBRARY_TEXTDOMAIN));
	}
	
	
	private function ___________________DROPDOWN_PARAM________________(){}
	
	
	/**
	 * put dropdown items table
	 */
	private function putDropdownItems(){
		?>
				<table data-inputtype="table_dropdown" class='uc-table-dropdown-items uc-table-dropdown-full'>
					<thead>
						<tr>
							<th></th>
							<th width="100px"><?php _e("Item Text", ADDONLIBRARY_TEXTDOMAIN)?></th>
							<th width="100px"><?php _e("Item Value", ADDONLIBRARY_TEXTDOMAIN)?></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><div class='uc-dropdown-item-handle'></div></td>
							<td><input type="text" value="" class='uc-dropdown-item-name'></td>
							<td><input type="text" value="" class='uc-dropdown-item-value'></td>
							<td>
								<div class='uc-dropdown-icon uc-dropdown-item-delete' title="<?php _e("Delete Item", ADDONLIBRARY_TEXTDOMAIN)?>"></div>
								<div class='uc-dropdown-icon uc-dropdown-item-add' title="<?php _e("Add Item", ADDONLIBRARY_TEXTDOMAIN)?>"></div>
								<div class='uc-dropdown-icon uc-dropdown-item-default uc-selected' title="<?php _e("Default Item", ADDONLIBRARY_TEXTDOMAIN)?>"></div>
							</td>
						</tr>
					</tbody>
				</table>
		
		<?php 
	}
	
	
	/**
	 * put select related dropdown
	 */
	private function putDropdownSelectRelated($selectSelector, $valueText = null, $putText = null){
		
		$valueTextOutput = __("Attribute Value", ADDONLIBRARY_TEXTDOMAIN);
		$putTextOutput = __("Html Output", ADDONLIBRARY_TEXTDOMAIN);
		
		if(!empty($valueText))
			$valueTextOutput = $valueText;
		
		if(!empty($putText))
			$putTextOutput = $putText;
		
		?>
				<table data-inputtype="table_select_related" class='uc-table-dropdown-items uc-table-dropdown-simple uc-table-select-related' data-relateto="<?php echo $selectSelector?>">
					<thead>
						<tr>
							<th><?php echo $valueTextOutput?></th>
							<th><?php echo $putTextOutput?></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
		<?php 
	}
	
	
	private function ___________________VARIABLE_PARAMS________________(){}
	
	
	/**
	 * put item variable fields
	 */
	private function putVarItemSimpleFields(){
		
		$checkboxFirstID = "uc_check_first_varitem_".$this->type;
		$checkboxLastID = "uc_check_last_varitem_".$this->type;
		
		?>
			
			<div class="unite-inputs-label">
				<?php _e("Default Value", ADDONLIBRARY_TEXTDOMAIN)?>:
			</div>
			
			<input type="text" name="default_value" value="" class="uc_default_value">
			
			<a class="uc-link-add" data-addto-selector=".uc_default_value" data-addtext="%numitem%" href="javascript:void(0)"><?php _e("Add Numitem", ADDONLIBRARY_TEXTDOMAIN)?></a>
			
			<div class="unite-inputs-label mtop_5 mbottom_5">
				
				<input id="<?php echo $checkboxFirstID?>" type="checkbox" name="enable_first_item" class="uc-control" data-controlled-selector=".uc_section_first">
				
				<label for="<?php echo $checkboxFirstID?>">
				<?php _e("Value for First Item", ADDONLIBRARY_TEXTDOMAIN)?>:
				</label>
			</div>
			
			<div class="uc_section_first" style="display:none">
				
				<input type="text" name="first_item_value" value="" class="uc_first_item_value">
				
				<a class="uc-link-add" data-addto-selector=".uc_first_item_value" data-addtext="%numitem%" href="javascript:void(0)"><?php _e("Add Numitem", ADDONLIBRARY_TEXTDOMAIN)?></a>
				
			</div>
			
			<div class="unite-inputs-label mtop_5 mbottom_5">
				
				<input id="<?php echo $checkboxLastID?>" type="checkbox" name="enable_last_item" class="uc-control" data-controlled-selector=".uc_section_last">
				
				<label for="<?php echo $checkboxLastID?>">
				<?php _e("Value for Last Item", ADDONLIBRARY_TEXTDOMAIN)?>:
				</label>
			</div>
			
			<div class="uc_section_last" style="display:none">
				
				<input type="text" name="last_item_value" value="" class="uc_last_item_value" >
				
				<a class="uc-link-add" data-addto-selector=".uc_last_item_value" data-addtext="%numitem%" href="javascript:void(0)"><?php _e("Add Numitem", ADDONLIBRARY_TEXTDOMAIN)?></a>
							
			</div>
			
			<div class="unite-dialog-description-right">
				* <?php _e("The %numitem% is 1,2,3,4... numbers serials", ADDONLIBRARY_TEXTDOMAIN)?>
			</div>
			
		<?php
	}
	
	
	/**
	 * put fields of item params related variable
	 * type: item / main
	 */
	private function putParamsRelatedFields($type = "main"){
		
		$title = __("Select Main Attribute", ADDONLIBRARY_TEXTDOMAIN);
		$source = "main";
		
		if($type == "item"){
			$title = __("Select Item Attribute", ADDONLIBRARY_TEXTDOMAIN);
			$source = "item";
		}
		
		?>
		
		<div class="unite-inputs-label-inline-free ptop_5" >
			<?php echo $title?>:
		</div>
		
		<select class="uc-select-param uc_select_param_name" data-source="<?php echo $source?>" name="param_name"></select>
		
		<div class="unite-inputs-sap"></div>
		
		<div class="uc-dialog-param-min-height">
		
		<?php $this->putDropdownSelectRelated(".uc_select_param_name");?>
		
		</div>
		
		<?php HelperHtmlUC::putDialogControlFieldsNotice() ?>
		
		<?php
		
	}
	
	
	private function ___________________OUTPUT________________(){}
	
	
	/**
	 * put tab html
	 */
	private function putTab($paramType, $isSelected = false){
		
		$tabPrefix = "uc_tabparam_".$this->type."_";
		$contentID = $tabPrefix.$paramType;
		
		//check for duplicates
		if(isset($this->arrContentIDs[$paramType]))
			UniteFunctionsUC::throwError("dialog param error: duplicate tab type: $paramType");
		
		//save content id
		$this->arrContentIDs[$paramType] = $contentID;
		
		$title = UniteFunctionsUC::getVal($this->arrParamsTypes, $paramType);
		if(empty($title))
			UniteFunctionsUC::throwError("Attribute: {$paramType} is not found in param list.");
		
		
		//put tab content
		$class = "uc-tab";
		if($isSelected == true)
			$class = "uc-tab uc-tab-selected";
		
		?>
			<a href="javascript:void(0)" data-type="<?php echo $paramType?>" data-contentid="<?php echo $contentID?>" class="<?php echo $class?>">
				<?php _e($title, ADDONLIBRARY_TEXTDOMAIN)?> 
			</a>
		<?php 
		
	}
	
	
	/**
	 * put param content
	 */
	private function putParamFields($paramType){
		
		
		switch($paramType){
			case "uc_textfield":
				$this->putDefaultValueParam();
			break;
			case "uc_number":
				$this->putDefaultValueParam(false, "input-small");
				$this->putNumberUnitSelect();
			break;
			case "uc_radioboolean":
				$this->putRadioBooleanParam();
			break;
			case "uc_textarea":
				$this->putDefaultValueParam(true);
			break;
			case self::PARAM_EDITOR:
				$this->putDefaultValueParam(true);
			break;
			case "uc_checkbox":
				$this->putRadioYesNo("is_checked", __("Checked By Default", ADDONLIBRARY_TEXTDOMAIN), false, "Yes", "No", true);
			break;
			case "uc_dropdown":
				$this->putDropDownItems();
			break;
			case "uc_colorpicker":
				$this->putColorPickerDefault();
			break;
			case "uc_image":
				$this->putImageParam();
			break;
			case "uc_mp3":
				$this->putMp3Param();
			break;
			
			//variable params
			
			case "uc_varitem_simple":
				$this->putVarItemSimpleFields();
			break;
			case "uc_var_paramrelated":
				$this->putParamsRelatedFields("main");
			break;
			case "uc_var_paramitemrelated":
				$this->putParamsRelatedFields("item");
			break;
			default:
				UniteFunctionsUC::throwError("Wrong param type, fields not found: $paramType");
			break;
		}
		
	}
	
	
	/**
	 * get texts array
	 */
	private function getArrTexts(){
		
		$arrTexts = array();
		
		$arrTexts["add_title"] = __("Add Attribute",ADDONLIBRARY_TEXTDOMAIN);
		$arrTexts["add_button"] = __("Add Attribute",ADDONLIBRARY_TEXTDOMAIN);
		$arrTexts["edit_title"] = __("Edit Attribute",ADDONLIBRARY_TEXTDOMAIN);
		$arrTexts["update_button"] = __("Update Attribute",ADDONLIBRARY_TEXTDOMAIN);
		
		$arrTexts = array_merge($arrTexts, $this->option_arrTexts);
		
		return($arrTexts);
	}
	
	
	/**
	 * output html
	 */
	public function outputHtml(){
		
		$this->validateInited();
		$type = $this->type;
		$dialogID = "uc_dialog_param_".$type;
		
		//fill texts
		$arrTexts = $this->getArrTexts();
		$dataTexts = UniteFunctionsUC::jsonEncodeForHtmlData($arrTexts);
		
		?>
			
			<!-- Dialog Param: <?php echo $type?> -->
			
			<div id="<?php echo $dialogID?>" class="uc-dialog-param" data-texts="<?php echo $dataTexts?>" style="display:none">
				
				<div class="dialog-param-wrapper unite-inputs">
					
					<div class="uc-tabs uc-tabs-paramdialog">
						
						<?php 
							$firstParam = true;
							foreach($this->arrParams as $paramType){
								
								$this->putTab($paramType, $firstParam);
								$firstParam = false;
							}
						?>
					</div>
					
					<div class="unite-clear"></div>
					
					<div class="uc-tabsparams-content-wrapper">
					
						<div class="dialog-param-left">
							
							<?php if($this->option_putTitle == true): ?>
							
								<div class="unite-inputs-label">
								<?php _e("Title")?>:
								</div>
								
								<input type="text" class="uc-param-title" name="title" value="">
								
								<div class="unite-inputs-sap"></div>
							
							<?php endif?>
							
							
							<div class="unite-inputs-label">
							<?php _e("Name", ADDONLIBRARY_TEXTDOMAIN)?>:
							</div>
							<input type="text" class="uc-param-name" name="name" value="">
							
							<div class="unite-inputs-sap"></div>
							
							<div class="unite-inputs-label">
							<?php _e("Description", ADDONLIBRARY_TEXTDOMAIN)?>:
							</div>
							
							<textarea name="description"></textarea>
							
							<?php if($this->option_putAdminLabel == true):?>
							<div class='uc-dialog-param-admin-label-wrapper'>
								<div class="unite-inputs-sap"></div>
								
								<div class="unite-inputs-label-inline-free">
										<?php _e("Admin Label", ADDONLIBRARY_TEXTDOMAIN)?>:
								</div>
								<input type="checkbox" name="admin_label">
								<div class="unite-dialog-description-left"><?php _e("Show attribute content on admin side", ADDONLIBRARY_TEXTDOMAIN)?></div>
							</div>
							<?php endif?>
						</div>
						
						
						<div class="dialog-param-right">
							
							<?php 
							
							$firstParam = true;
							foreach($this->arrParams as $paramType):
								
								$tabContentID = UniteFunctionsUC::getVal($this->arrContentIDs, $paramType);
								if(empty($tabContentID))
									UniteFunctionsUC::throwError("No content ID found for param: {$paramType} ");
								
								$addHTML = "";
								$addClass = "uc-content-selected";
								if($firstParam == false){
									$addHTML = " style='display:none'";
									$addClass = "";
								}
								
								$firstParam = false;
								
								?>
								
								<!-- <?php echo $paramType?> fields -->
								
								<div id="<?php echo $tabContentID?>" class="uc-tab-content <?php echo $addClass?>" <?php echo $addHTML?> >
									
									<?php 
									
										$this->putParamFields($paramType);
									
									?>
									
								</div>
								
								<?php 								
								
							endforeach;
							?>
							
							
						</div>
						
						<div class="unite-clear"></div>
					
					</div>	<!-- end uc-tabs-content-wrapper -->
					
					<div class="uc-dialog-param-error unite-color-red" style="display:none"></div>
					
				</div>
				
					
			</div>		
		
		
		<?php 
	}
	
	
	private function ___________________INIT________________(){}
	
	
	/**
	 * init main dialog params
	 */
	public function initMainParams(){
		
		$this->arrParams = array(
			"uc_textfield",
			"uc_number",
			"uc_radioboolean",
			"uc_textarea",
			"uc_checkbox",
			"uc_dropdown",
			"uc_colorpicker",
			self::PARAM_EDITOR,
			"uc_image",
			"uc_mp3"
		);
		
	}
	
	/**
	 * init common variable dialogs
	 */
	private function initVariableCommon(){
		
		$this->option_putAdminLabel = false;
		$this->option_putTitle = false;
		$this->option_arrTexts["add_title"] = __("Add Item Variable",ADDONLIBRARY_TEXTDOMAIN);
		$this->option_arrTexts["add_button"] = __("Add Variable",ADDONLIBRARY_TEXTDOMAIN);
		$this->option_arrTexts["update_button"] = __("Update Variable",ADDONLIBRARY_TEXTDOMAIN);
		$this->option_arrTexts["edit_title"] = __("Edit Variable",ADDONLIBRARY_TEXTDOMAIN);
		
	}
		
	
	/**
	 * init variable params
	 */
	private function initVariableMainParams(){
	
		$this->initVariableCommon();
		
		$this->arrParams = array(
				"uc_var_paramrelated"
		);
	
	}
	
	
	/**
	 * init variable item params
	 */
	private function initVariableItemParams(){
	
		$this->initVariableCommon();
		
		$this->arrParams = array(
				"uc_varitem_simple",
				"uc_var_paramrelated",
				"uc_var_paramitemrelated"
		);
		
	}
	
	
	/**
	 * init the params dialog
	 */
	public function init($type, $addon){
		$this->type = $type;
		
		if(empty($addon))
			UniteFunctionsUC::throwError("you must pass addon");
		
		$this->addon = $addon;
		
		switch($this->type){
			case self::TYPE_MAIN:
				$this->initMainParams();
			break;
			case self::TYPE_ITEM_VARIABLE:
				$this->initVariableItemParams();
			break;
			case self::TYPE_MAIN_VARIABLE:
				$this->initVariableMainParams();
			break;
			default:
				UniteFunctionsUC::throwError("Wrong param dialog type: $type");
			break;
		}
		
	}
	
	
	
}
