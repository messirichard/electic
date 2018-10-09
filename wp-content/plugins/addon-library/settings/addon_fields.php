<?php 
		$filepathAddonSettings = GlobalsUC::$pathSettings."addon_fields.xml";
		
		UniteFunctionsUC::validateFilepath($filepathAddonSettings);
		
		$generalSettings = new UniteCreatorSettings();
		
		if(isset($this->objAddon)){
			$generalSettings->setCurrentAddon($this->objAddon);
		}
		
		$generalSettings->loadXMLFile($filepathAddonSettings);
