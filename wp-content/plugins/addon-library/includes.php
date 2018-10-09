<?php

// no direct access
if(!defined("ADDON_LIBRARY_INC"))
	define("ADDON_LIBRARY_INC", true);


if(!defined("ADDON_LIBRARY_VERSION"))
	define("ADDON_LIBRARY_VERSION", "1.3.33");


$currentFile = __FILE__;
$currentFolder = dirname($currentFile);
$folderIncludesMain = $currentFolder."/inc_php/";

//include frameword files
require_once $folderIncludesMain . 'framework/include_framework.php';

require_once $folderIncludesMain . 'unitecreator_globals.class.php';
require_once $folderIncludesMain . 'unitecreator_operations.class.php';
require_once $folderIncludesMain . 'unitecreator_categories.class.php';
require_once $folderIncludesMain . 'unitecreator_addon.class.php';
require_once $folderIncludesMain . 'unitecreator_addons.class.php';
require_once $folderIncludesMain . 'unitecreator_helper.class.php';
require_once $folderIncludesMain . 'unitecreator_helperhtml.class.php';
require_once $folderIncludesMain . 'unitecreator_output.class.php';
require_once $folderIncludesMain . 'unitecreator_variables_output.class.php';
require_once $folderIncludesMain . 'unitecreator_actions.class.php';
require_once $folderIncludesMain . 'unitecreator_template_engine.class.php';
require_once $folderIncludesMain . 'unitecreator_settings.class.php';
require_once $folderIncludesMain . 'unitecreator_settings_output.class.php';
require_once $folderIncludesMain . 'unitecreator_library.class.php';

require_once $folderIncludesMain . 'plugins/unitecreator_plugin_filters.class.php';
require_once $folderIncludesMain . 'plugins/unitecreator_plugin_base.class.php';
require_once $folderIncludesMain . 'plugins/unitecreator_plugins.class.php';

require_once $folderIncludesMain . 'layouts/unitecreator_layout.class.php';
require_once GlobalsUC::$pathProvider . 'provider_layout.class.php';
require_once $folderIncludesMain . 'layouts/unitecreator_layout_output.class.php';
require_once GlobalsUC::$pathProvider . 'provider_layout_output.class.php';
require_once GlobalsUC::$pathProvider . 'provider_library.class.php';
require_once GlobalsUC::$pathProvider . 'provider_library.class.php';


//admin only, maybe split later
if(GlobalsUC::$is_admin){

	require_once $folderIncludesMain . 'unitecreator_client_text.php';
	require_once $folderIncludesMain . 'unitecreator_assets.class.php';
	require_once $folderIncludesMain . 'unitecreator_assets_work.class.php';
	require_once $folderIncludesMain . 'manager/unitecreator_manager.class.php';
	require_once $folderIncludesMain . 'manager/unitecreator_manager_addons.class.php';
	require_once $folderIncludesMain . 'manager/unitecreator_manager_inline.class.php';
	require_once $folderIncludesMain . 'unitecreator_browser.class.php';
	require_once $folderIncludesMain . 'unitecreator_addon_config.class.php';
	require_once $folderIncludesMain . 'unitecreator_dialog_param.class.php';
	require_once $folderIncludesMain . 'unitecreator_params_editor.class.php';
	require_once $folderIncludesMain . 'unitecreator_exporter_base.class.php';
	require_once $folderIncludesMain . 'unitecreator_exporter.class.php';
	require_once $folderIncludesMain . 'unitecreator_settingsview.class.php';
	require_once $folderIncludesMain . 'layouts/unitecreator_grid_builder.class.php';
	require_once $folderIncludesMain . 'layouts/unitecreator_layouts.class.php';
	require_once GlobalsUC::$pathProvider . 'provider_layouts.class.php';
	require_once $folderIncludesMain . 'layouts/unitecreator_layouts_exporter.class.php';
	require_once GlobalsUC::$pathProvider . 'provider_laytouts_exporter.class.php';
}

 $filepathIncludeProviderAfter = GlobalsUC::$pathProvider."include_provider_after.php";
 if(file_exists($filepathIncludeProviderAfter))
 	require_once $filepathIncludeProviderAfter;
 
 
 
?>