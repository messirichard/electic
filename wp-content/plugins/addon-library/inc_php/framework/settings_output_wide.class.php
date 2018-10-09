<?php
/**
 * @package Addon Library
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('ADDON_LIBRARY_INC') or die('Restricted access');


	class UniteSettingsOutputWideUC extends UniteSettingsOutputUC{
		
		
		/**
		 * constuct function
		 */
		public function __construct(){
			$this->isParent = true;
			self::$serial++;
			$this->wrapperID = "unite_settings_wide_output_".self::$serial;
			$this->settingsMainClass = "unite_settings_wide";
		}
		
		
		/**
		 * draw settings row
		 * @param $setting
		 */
		protected function drawSettingRow($setting){
		
			//set cellstyle:
			$cellStyle = "";
			if(isset($setting[UniteSettingsUC::PARAM_CELLSTYLE])){
				$cellStyle .= $setting[UniteSettingsUC::PARAM_CELLSTYLE];
			}
			
			if($cellStyle != "")
				 $cellStyle = "style='".$cellStyle."'";
			
			$textStyle = $this->drawSettingRow_getTextStyle($setting);
			
			$rowStyle = $this->drawSettingRow_getRowStyle($setting);
			
			$rowClass = $this->drawSettingRow_getRowClass($setting);
			
			$text = $this->drawSettingRow_getText($setting);
			
			$description = UniteFunctionsUC::getVal($setting,"description");

			
			//set settings text width:
			$textWidth = "";
			if(isset($setting["textWidth"])) 
				$textWidth = 'width="'.$setting["textWidth"].'"';
			
			
			$addField = UniteFunctionsUC::getVal($setting, UniteSettingsUC::PARAM_ADDFIELD);
			
			?>
						
			<?php
			if(!empty($addField)):
				
				$addSetting = $this->settings->getSettingByName($addField);
				UniteFunctionsUC::validateNotEmpty($addSetting,"AddSetting {$addField}");
			
				//set hidden
				$rowStyleAdd = "";
				if(isset($addSetting["hidden"]))
					$rowStyleAdd = "display:none;";
				
				if(!empty($rowStyleAdd))
					$rowStyleAdd = "style='$rowStyleAdd'";
							
				$addSettingText = UniteFunctionsUC::getVal($addSetting,"text","");
				$addSettingText = str_replace(" ","&nbsp;", $addSettingText);
				
				?>
				<tr <?php echo $rowClass?> valign="top">
				
				<td <?php echo $cellStyle?> class="unite-settings-onecell" colspan="2">
					<span id="<?php echo $setting["id_row"]?>" <?php echo $rowStyle ?>>
						<span class='setting_onecell_text'><?php echo $text?></span>				
							<?php 
								$this->drawInputs($setting);
								$this->drawInputAdditions($setting);
							?>
						<span class="setting_onecell_horsap"></span>
					</span>
					
					<span id="<?php echo $addSetting["id_row"]?>" <?php echo $rowStyleAdd ?>>
						<span class='setting_onecell_text'><?php echo $addSettingText?></span>				
						<?php
							$this->drawInputs($addSetting);
							$this->drawInputAdditions($addSetting);
						?>
					</span>
				</td>
				</tr>
				<?php
			?>
			<?php else:	?>
				<tr id="<?php echo $setting["id_row"]?>" <?php echo $rowStyle ?> <?php echo $rowClass?> valign="top">
			
					<th <?php echo $textStyle?> scope="row" <?php echo $textWidth ?>>
						<?php if($this->showDescAsTips == true): ?>
					    	<span class='setting_text' title="<?php echo $description?>"><?php echo $text?></span>
					    <?php else:?>
					    	<?php echo $text?>
					    <?php endif?>
					</th>
					<td <?php echo $cellStyle?>>
						<?php 
							$this->drawInputs($setting);
							$this->drawInputAdditions($setting);
						?>
					</td>
				</tr>
			<?php
			endif;
		}

		/**
		 * draw hr row
		 * @param unknown_type $setting
		 */
		protected function drawHrRow($setting){

			//set hidden
			$rowStyle = "";
			if(isset($setting["hidden"])) $rowStyle = "style='display:none;'";
		
			$class = UniteFunctionsUC::getVal($setting, "class");
			if(!empty($class))
				$class = "class='$class'";
		
			?>
			<tr id="<?php echo $setting["id_row"]?>" <?php echo $rowStyle ?>>
				<td colspan="4" align="left" style="text-align:left;">
					 <hr <?php echo $class; ?> /> 
				</td>
			</tr>
			<?php 
		}
		
		
		
		/**
		 * draw text row
		 * @param unknown_type $setting
		 */
		protected function drawTextRow($setting){
		
			//set cell style
			$cellStyle = "";
			if(isset($setting["padding"]))
				$cellStyle .= "padding-left:".$setting["padding"].";";
		
			if(!empty($cellStyle))
				$cellStyle="style='$cellStyle'";
		
			//set style
			$rowStyle = "";
			if(isset($setting["hidden"]))
				$rowStyle .= "display:none;";
		
			if(!empty($rowStyle))
				$rowStyle = "style='$rowStyle'";
		
			?>
				<tr id="<?php echo $setting["id_row"]?>" <?php echo $rowStyle ?> valign="top">
					<td colspan="4" <?php echo $cellStyle?>>
						<span class="spanSettingsStaticText"><?php echo $setting["text"]?></span>
					</td>
				</tr>
			<?php 
		}
		
		
		/**
		 * draw wrapper before settings
		 */
		protected function drawSettings_before(){
			?><table class='unite_table_settings_wide'><?php
		}
		
		
		/**
		 * draw wrapper end after settings
		 */
		protected function drawSettingsAfter(){
			
			?></table><?php
		}
		
		
	
	}
?>