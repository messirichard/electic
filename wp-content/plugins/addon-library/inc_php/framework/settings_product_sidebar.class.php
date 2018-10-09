<?php
/**
 * @package Addon Library
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('ADDON_LIBRARY_INC') or die('Restricted access');

	class UniteSettingsProductSidebarUC extends UniteSettingsOutputUC{
		
		private $addClass = "";		//add class to the main div
		private $arrButtons = array();
		private $isAccordion = true;
		private $defaultTextClass;
		
		const INPUT_CLASS_SHORT = "text-sidebar";
		const INPUT_CLASS_NORMAL = "text-sidebar-normal";
		const INPUT_CLASS_LONG = "text-sidebar-long";
		const INPUT_CLASS_LINK = "text-sidebar-link";
		

		/**
		 * 
		 * construction
		 */
		public function __construct(){
			$this->defaultTextClass = self::INPUT_CLASS_SHORT;
		}
		
		
		
		
		/**
		 * 
		 * add buggon
		 */
		public function addButton($title,$id,$class = "unite-button-secondary"){
			
			$button = array(
				"title"=>$title,
				"id"=>$id,
				"class"=>$class
			);
			
			$this->arrButtons[] = $button;			
		}
		
		
		
		/**
		 * 
		 * set add class for the main div
		 */
		public function setAddClass($addClass){
			$this->addClass = $addClass;
		}
		
		
		//-----------------------------------------------------------------------------------------------
		//draw hr row
		protected function drawTextRow($setting){
			
			//set cell style
			$cellStyle = "";
			if(isset($setting["padding"])) 
				$cellStyle .= "padding-left:".$setting["padding"].";";
				
			if(!empty($cellStyle))
				$cellStyle="style='$cellStyle'";
				
			//set style
			$rowStyle = "";					
			if(isset($setting["hidden"]) && $setting["hidden"] == true) 
				$rowStyle .= "display:none;";
				
			if(!empty($rowStyle))
				$rowStyle = "style='$rowStyle'";
			
			?>
				<span class="spanSettingsStaticText"><?php echo __($setting["text"],ADDONLIBRARY_TEXTDOMAIN)?></span>
			<?php 
		}
		
		//-----------------------------------------------------------------------------------------------
		//draw hr row
		protected function drawHrRow($setting){
			//set hidden
			$rowStyle = "";
			if(isset($setting["hidden"]) && $setting["hidden"] == true) 
				$rowStyle = "style='display:none;'";
			
			?>
				<li id="<?php echo $setting["id"]?>_row" <?php echo $rowStyle?> class="hrrow">
					<hr />
				</li>
			<?php 
		}
		
		
		//-----------------------------------------------------------------------------------------------
		//draw settings row
		protected function drawSettingRow($setting){
		
			//set cellstyle:
			$cellStyle = "";
			if(isset($setting[UniteSettingsUC::PARAM_CELLSTYLE])){
				$cellStyle .= $setting[UniteSettingsUC::PARAM_CELLSTYLE];
			}
			
			//set text style:
			$textStyle = $cellStyle;
			if(isset($setting[UniteSettingsUC::PARAM_TEXTSTYLE])){
				$textStyle .= $setting[UniteSettingsUC::PARAM_TEXTSTYLE];
			}
			
			if($textStyle != "") 
				$textStyle = "style='".$textStyle."'";
			
			if($cellStyle != "") 
				$cellStyle = "style='".$cellStyle."'";
			
			//set hidden
			$rowStyle = "";
			if(isset($setting["hidden"]) && $setting["hidden"] == true) $rowStyle = "display:none;";
			if(!empty($rowStyle)) $rowStyle = "style='$rowStyle'";
			
			//set row class:
			$rowClass = "";
			if(isset($setting["disabled"])) 
				$rowClass = "setting-disabled";
			
			if($setting["type"] == UniteSettingsUC::TYPE_TEXTAREA){
				if(!empty($rowClass))
					$rowClass .= " ";
				$rowClass .= "setting_row_textarea";
			}
			
			if(!empty($rowClass))
				$rowClass = "class='{$rowClass}'";
			
			
			//modify text:
			$text = UniteFunctionsUC::getVal($setting,"text","");
			$text = __($text,ADDONLIBRARY_TEXTDOMAIN);
			
			// prevent line break (convert spaces to nbsp)
			$text = str_replace(" ","&nbsp;",$text);
			
			if($setting["type"] == UniteSettingsUC::TYPE_CHECKBOX)
				$text = "<label for='{$setting["id"]}'>{$text}</label>";
			
			//set settings text width:
			$textWidth = "";
			if(isset($setting["textWidth"])) 
				$textWidth = 'width="'.$setting["textWidth"].'"';
			
			$description = UniteFunctionsUC::getVal($setting, "description");
			$description = __($description,ADDONLIBRARY_TEXTDOMAIN);
			
			$unit = UniteFunctionsUC::getVal($setting, "unit");
			$unit = __($unit,ADDONLIBRARY_TEXTDOMAIN);
			
			$required = UniteFunctionsUC::getVal($setting, "required");
			
			$addHtml = UniteFunctionsUC::getVal($setting, UniteSettingsUC::PARAM_ADDTEXT);			
			$addHtmlBefore = UniteFunctionsUC::getVal($setting, UniteSettingsUC::PARAM_ADDTEXT_BEFORE_ELEMENT);			
			
			
			//set if draw text or not.
			$toDrawText = true;
			//if($setting["type"] == UniteSettingsUC::TYPE_BUTTON || $setting["type"] == UniteSettingsUC::TYPE_MULTIPLE_TEXT)
				//$toDrawText = false;
				
			$settingID = $setting["id"];
			$attribsText = UniteFunctionsUC::getVal($setting, "attrib_text");
			

			?>
				<li id="<?php echo $settingID?>_row" <?php echo $rowStyle." ".$rowClass?>>
					
					<?php if($toDrawText == true):?>
						<div id="<?php echo $settingID?>_text" class='setting_text' title="<?php echo $description?>" <?php echo $attribsText?>><?php echo $text ?></div>
					<?php endif?>
					
					<?php if(!empty($addHtmlBefore)):?>
						<div class="settings_addhtmlbefore"><?php echo $addHtmlBefore?></div>
					<?php endif?>
					
					<div class='setting_input'>
						<?php $this->drawInputs($setting);?>
					<?php if(!empty($unit)):?>
						<div class='setting_unit'><?php echo $unit?></div>
					<?php endif?>
					<?php if(!empty($required)):?>
						<div class='setting_required'>*</div>
					<?php endif?>
					<?php if(!empty($addHtml)):?>
						<div class="settings_addhtml"><?php echo $addHtml?></div>
					<?php endif?>					
					</div>
					<div class="unite-clear"></div>
				</li>
				<?php
				if($setting['name'] == 'shadow_type'){ //For shadow types, add box with shadow types
					$this->drawShadowTypes($setting['value']);
				}
		}
		
		/**
		 * 
		 * insert settings into saps array
		 */
		private function groupSettingsIntoSaps(){
			$arrSections = $this->settings->getArrSections();
			$arrSaps = $arrSections[0]["arrSaps"];
			$arrSettings = $this->settings->getArrSettings(); 
			
			//group settings by saps
			foreach($arrSettings as $key=>$setting){
				
				$sapID = $setting["sap"];
				
				if(isset($arrSaps[$sapID]["settings"]))
					$arrSaps[$sapID]["settings"][] = $setting;
				else 
					$arrSaps[$sapID]["settings"] = array($setting);
			}
			return($arrSaps);
		}
		
		/**
		 * 
		 * draw buttons that defined earlier
		 */
		private function drawButtons(){
			foreach($this->arrButtons as $key=>$button){
				if($key>0)
				echo "<span class='hor_sap'></span>";
				echo UniteFunctionsUC::getHtmlLink("#", $button["title"],$button["id"],$button["class"]);
			}
		}
		
		/**
		 * 
		 * draw some setting, can be setting array or name
		 */
		public function drawSetting($setting,$state = null){
			if(gettype($setting) == "string")
				$setting = $this->settings->getSettingByName($setting);
			
			switch($state){
				case "hidden":
					$setting["hidden"] = true;
				break;
			}
				
			switch($setting["type"]){
				case UniteSettingsUC::TYPE_HR:
					$this->drawHrRow($setting);
				break;
				case UniteSettingsUC::TYPE_STATIC_TEXT:
					$this->drawTextRow($setting);
				break;
				default:
					$this->drawSettingRow($setting);
				break;
			}
		}
		
		/**
		 * 
		 * draw setting by bulk names
		 */
		public function drawSettingsByNames($arrSettingNames,$state=null){
			if(gettype($arrSettingNames) == "string")
				$arrSettingNames = explode(",",$arrSettingNames);
				
			foreach($arrSettingNames as $name)
				$this->drawSetting($name,$state);
		}
		
		
		/**
		 * 
		 * draw all settings
		 */
		public function drawSettings(){
			$this->prepareToDraw();
			$this->drawHeaderIncludes();
			
			$arrSaps = $this->groupSettingsIntoSaps();			
			
			$class = "unite-postbox";
			if(!empty($this->addClass))
				$class .= " ".$this->addClass;
			
			//draw wrapper
			echo "<div class='settings_wrapper'>";
				
			//draw settings - advanced - with sections
			foreach($arrSaps as $key=>$sap):

				//set accordion closed
				$style = "";
				if($this->isAccordion == false){
					$h3Class = " no-accordion";
				}else{
					$h3Class = "";
					if($key>0){
						$style = "style='display:none;'";
						$h3Class = " box_closed";
					}
				}
					
				$text = $sap["text"];
				$classIcon = UniteFunctionsUC::getVal($sap, "icon");
				$text = __($text,ADDONLIBRARY_TEXTDOMAIN);
				
				?>
					<div class="<?php echo $class?>">
						<div class="unite-postbox-title<?php echo $h3Class?>">
						
						<?php if(!empty($classIcon)):?>
						<i style="float:left;margin-top:4px;font-size:14px;" class="<?php echo $classIcon?>"></i>
						<?php endif?>
						
						<?php if($this->isAccordion == true):?>
							<div class="unite-postbox-arrow"></div>
						<?php endif?>
						
							<span><?php echo $text ?></span>
						</div>			
												
						<div class="inside" <?php echo $style?> >
							<ul class="list_settings">
						<?php
							
							$settings = UniteFunctionsUC::getVal($sap, "settings", array());
								
							foreach($settings as $setting)
								$this->drawSetting($setting);
							
							?>
							</ul>
							
							<?php 
							if(!empty($this->arrButtons)){
								?>
								<div class="unite-clear"></div>
								<div class="settings_buttons">
								<?php 
									$this->drawButtons();
								?>
								</div>	
								<div class="unite-clear"></div>
								<?php 								
							}								
						?>
						
							<div class="unite-clear"></div>
						</div>
					</div>
				<?php 			
														
			endforeach;
			
			echo "</div>";	//wrapper close
		}
		
		
		/**
		 * 
		 * init accordion
		 */
		private function putAccordionInit(){
			?>
			<script type="text/javascript">
				jQuery(document).ready(function(){
					var settings = new UniteSettingsUC();					
					settings.initAccordion("<?php echo $this->formID?>");
				});				
			</script>
			<?php 
		}
		
		/**
		 * 
		 * activate the accordion
		 */
		public function isAccordion($activate){
			$this->isAccordion = $activate;
		}
		
		
		/**
		 * 
		 * draw settings function
		 */
		public function draw($formID=null){
			if(empty($formID))
				UniteFunctionsUC::throwError("You must provide formID to side settings.");
			
			$this->formID = $formID;
			
			if(!empty($formID)){
				?>
				<form name="<?php echo $formID?>" id="<?php echo $formID?>">
					<?php $this->drawSettings() ?>
				</form>
				<?php 
			}else
				$this->drawSettings();
			
			if($this->isAccordion == true)
				$this->putAccordionInit();
			
		}
		
	}
		
?>