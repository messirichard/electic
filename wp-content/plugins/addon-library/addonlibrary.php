<?php
/*
Plugin Name: Addon Library
Plugin URI: http://addon-library.com
Description: Addon Library - Addons for Visual Editors
Author: Unite CMS
Version: 1.3.33
Author URI: http://addon-library.com
*/

//ini_set("display_errors", "on");
//ini_set("error_reporting", E_ALL);

if(!defined("ADDON_LIBRARY_INC"))
	define("ADDON_LIBRARY_INC", true);

$mainFilepath = __FILE__;
$currentFolder = dirname($mainFilepath);
$pathProvider = $currentFolder."/provider/";


//phpinfo();
try{
	$pathAltLoader = $pathProvider."provider_alt_loader.php";
	if(file_exists($pathAltLoader)){
		require $pathAltLoader;
	}else{
	require_once $currentFolder.'/includes.php';
	
	require_once  GlobalsUC::$pathProvider."provider_main_file.php";
	}
	
}catch(Exception $e){
	$message = $e->getMessage();
	$trace = $e->getTraceAsString();
	echo "<br>";
	echo $message;
	echo "<pre>";
	print_r($trace);
}


