<?php


/**
 * return if addon creator plugin exists and active
 */
function UCIsAddonCreatorPluginExists(){
	$creatorPlugin = "addon_creator/addon_creator.php";
	
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	
	$arrPlugins = get_plugins();
	if(isset($arrPlugins[$creatorPlugin]) == false)
		return(false);
	
	return(true);

}

if(UCIsAddonCreatorPluginExists()){
	require_once dirname(__FILE__)."/ac_importer/provider_ac_importer.php";
}else{
	
	require_once $currentFolder.'/includes.php';
	require_once  GlobalsUC::$pathProvider."provider_main_file.php";
}

