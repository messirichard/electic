<?php

defined('ADDON_LIBRARY_INC') or die;


class UniteCreatorViewGeneralSettings extends UniteCreatorSettingsView{
	
	
	/**
	 * draw additional tabs
	 */
	protected function drawAdditionalTabs(){
		?>
		<a data-contentid="uc_tab_developers" href="javascript:void(0)" onfocus="this.blur()"> <?php _e("Theme Developers", ADDONLIBRARY_TEXTDOMAIN)?></a>
		<?php 
	}
	
	
	/**
	 * function for override
	 */
	protected function drawAdditionalTabsContent(){
		?>
		<div id="uc_tab_developers" class="uc-tab-content" style="display:none">
			Dear Theme Developer. <br><br>
			
			If you put the addon library as part of your theme and want
			the addons to auto install on plugin activation or theme switch, <br>
			please create folder <b>"al_addons"</b> inside your theme and put the addons import zips there. <br>
			example: <b>wp-content/themes/yourtheme/al_addons</b>
			
			<br><br>
			If you want to put them to another path please copy this code to your theme <b>functions.php</b> file:
			
			<br><br>
			
<textarea cols="80" rows="6" readonly onfocus="this.select()">				
/**
* set Addon Library addons install folder. 
* example: 'installs/addons' (will be wp-content/themes/installs/addons)
**/
function set_addons_install_path_<?php echo $randomString?>($value){
	
	//change the 'yourfolder' to the folder you want
	
	return(&quot;yourfolder&quot;);
}

add_filter(&quot;uc_path_theme_addons&quot;, &quot;set_addons_install_path_<?php echo $randomString?>&quot;);
</textarea>
			
		</div>
		
		<?php 
	}
	
	
	/**
	 * constructor
	 */
	public function __construct(){
		
		$this->headerTitle = __("General Settings", ADDONLIBRARY_TEXTDOMAIN);
		$this->saveAction = "update_general_settings";
		
		//set settings
		$operations = new UCOperations();
		$this->objSettings = $operations->getGeneralSettingsObject();
		
		$this->display();
	}
	
	
	
}


new UniteCreatorViewGeneralSettings();
