<?php
/**
 * @package Addon Library
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('ADDON_LIBRARY_INC') or die('Restricted access');


	class UniteCreatorSettingsOutput extends UniteSettingsOutputWideUC{
		
		
		/**
		 * draw addon setting output
		 */
		private function drawImageAddonInput($setting){
			
			$previewStyle = "display:none";
			
			$urlBase = UniteFunctionsUC::getVal($setting, "url_base");
						
			$isError = false;
			
			$value = UniteFunctionsUC::getVal($setting, "value");
			
			if(empty($urlBase)){
				$isError = true;
				$value = "";
				$setting["value"] = "";
			}
						
			if(!empty($value)){
				
				$urlFull = $urlBase.$value;
								
				$previewStyle = "";
			
				$operations = new UCOperations();
				try{
					
					$urlThumb = $operations->createThumbs($urlFull);
					
				}catch(Exception $e){
					$urlThumb = $value;
				}
			
				$urlThumbFull = HelperUC::URLtoFull($urlThumb);
				if(!empty($previewStyle))
					$previewStyle .= ";";
				
				$previewStyle .= "background-image:url('{$urlThumbFull}');";
			}
			
			if(!empty($previewStyle))
				$previewStyle = "style=\"{$previewStyle}\"";
			
			
			$class = $this->getInputClassAttr($setting, "", "unite-setting-image-input unite-input-image");
			
			$addHtml = $this->getDefaultAddHtml($setting);
			
			//add source param
			$source = UniteFunctionsUC::getVal($setting, "source");
			if(!empty($source))
				$addHtml .= " data-source='{$source}'";
			
			//set error related
			
			$buttonAddClass = "";
			$errorStyle = "style='display:none'";
			if($isError == true){
				$buttonAddClass = " button-disabled";
				$errorStyle = "'";
			}
			
			?>
				<div class="unite-setting-image"> 
					<input type="text" id="<?php echo $setting["id"]?>" name="<?php echo $setting["name"]?>" readonly data-baseurl="<?php echo $urlBase?>" <?php echo $class?> value="<?php echo $value?>" <?php echo $addHtml?> />
					<a href="javascript:void(0)" class="unite-button-secondary unite-button-choose <?php echo $buttonAddClass?>"><?php _e("Choose", ADDONLIBRARY_TEXTDOMAIN)?></a>
					<a href="javascript:void(0)" class="unite-button-secondary unite-button-clear <?php echo $buttonAddClass?>"><?php _e("Clear", ADDONLIBRARY_TEXTDOMAIN)?></a>
					<div class='unite-setting-image-preview' <?php echo $previewStyle?>></div>
					<div class='unite-setting-image-error' <?php echo $errorStyle?>><?php _e("Please select assets path", ADDONLIBRARY_TEXTDOMAIN)?></div>
				</div>
			<?php
		}
		
		
		/**
		 *
		 * draw imaeg input:
		 * @param $setting
		 */
		protected function drawMp3AddonInput($setting){
		
			$previewStyle = "display:none";
		
			$setting = $this->modifyImageSetting($setting);
		
			$value = UniteFunctionsUC::getVal($setting, "value");
			
			$urlBase = UniteFunctionsUC::getVal($setting, "url_base");
			
			$isError = false;
						
			if(empty($urlBase)){
				$isError = true;
				$value = "";
				$setting["value"] = "";
			}
			
			$class = $this->getInputClassAttr($setting, "", "unite-setting-mp3-input unite-input-image");
		
			$addHtml = $this->getDefaultAddHtml($setting);
		
			//add source param
			$source = UniteFunctionsUC::getVal($setting, "source");
			if(!empty($source))
				$addHtml .= " data-source='{$source}'";
			
			$buttonAddClass = "";
			$errorStyle = "style='display:none'";
			if($isError == true){
				$buttonAddClass = " button-disabled";
				$errorStyle = "'";
			}
			
			?>
				<div class="unite-setting-mp3">
					<input type="text" id="<?php echo $setting["id"]?>" name="<?php echo $setting["name"]?>" <?php echo $class?> value="<?php echo $value?>" <?php echo $addHtml?> />
					<a href="javascript:void(0)" class="unite-button-secondary unite-button-choose <?php echo $buttonAddClass?>"><?php _e("Choose", ADDONLIBRARY_TEXTDOMAIN)?></a>
					<div class='unite-setting-mp3-error' <?php echo $errorStyle?>><?php _e("Please select assets path", ADDONLIBRARY_TEXTDOMAIN)?></div>
				</div>
			<?php
		}
		
		
		/**
		 * override setting
		 */
		protected function drawImageInput($setting){
			
			//add source param
			$source = UniteFunctionsUC::getVal($setting, "source");
			if($source == "addon")
				$this->drawImageAddonInput($setting);
			else
				parent::drawImageInput($setting);
			
		}
		
		
		/**
		 * draw mp3 input
		 */
		protected function drawMp3Input($setting){
			
			//add source param
			$source = UniteFunctionsUC::getVal($setting, "source");
			if($source == "addon")
				$this->drawMp3AddonInput($setting);
			else
				parent::drawMp3Input($setting);
		}
		
		
	}
		
		