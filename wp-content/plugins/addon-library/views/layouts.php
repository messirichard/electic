<?php

defined('ADDON_LIBRARY_INC') or die;

require HelperUC::getPathViewObject("layouts_view.class");
require HelperUC::getPathViewProvider("provider_layouts_view.class");

$objLayouts = new UniteCreatorLayoutsViewProvider();
$objLayouts->display();
