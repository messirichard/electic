<?php

$pathProvider = dirname(__FILE__)."/";

if(GlobalsUC::$is_admin){
	require_once $pathProvider . 'provider_gridbuilder.class.php';
	require_once $pathProvider . 'unitevc_exporter.class.php';
}

require_once $pathProvider . 'settings_output_vc.class.php';
require_once $pathProvider . 'unitevc_integrate.class.php';
require_once $pathProvider . 'widget_layout.class.php';


HelperProviderUC::registerPlugins();
