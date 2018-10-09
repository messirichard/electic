<?php

if($this->showHeader == true){
	if(!isset($headerTitle))
		$headerTitle = __("Manage Addons", ADDONLIBRARY_TEXTDOMAIN);
	require HelperUC::getPathTemplate("header");
}

?>
	
	<?php 
		if($this->showButtons == true)
			UniteProviderFunctionsUC::putAddonViewAddHtml()
	?>
	
	<div class="content_wrapper">
		<?php $objManager->outputHtml() ?>
	</div>

	<?php 
		
		if(method_exists("UniteProviderFunctionsUC", "putUpdatePluginHtml"))
			UniteProviderFunctionsUC::putUpdatePluginHtml();
	
	?>