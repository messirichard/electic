<?php
/**
 * @package Addon Library
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('ADDON_LIBRARY_INC') or die('Restricted access');


class UniteCreatorExporter extends UniteCreatorExporterBase{
	
	const MULTIPLE_FOLDERS_FILENAME = "multiple";
	
	private $addon;
	
	protected $objAddons;
	protected $pathExportAddons;
	protected $filepathAddonZip;
	protected $pathExportAddon;
	protected $pathExportAddonAssets;

	//export categories
	
	protected $pathExportCategories;
	protected $pathExportCategory;
	protected $pathExportCategoryAddons;
	
	protected $filepathCategoryZip;
	protected $filenameCategoryZip;
	
	//import	
	private $isPathSingle = false;
	
	private $pathImportAddon;
	private $pathImportAddonAssets;
	private $isImportedOnce = false;
	private $arrLog = array();
	private $textLogShort = "";
	private $importedAddonType = "";
	private $mustAddonType = null;
	
	
	/**
	 * constructor
	 */
	public function __construct(){
		parent::__construct();
		$this->objAddons = new UniteCreatorAddons();
	}
	
	
	/**
	 * validate that the addon exists
	 */
	private function validateInited(){
		if(empty($this->addon))
			UniteFunctionsUC::throwError("export error: addon not inited");
	}
	
	
	/**
	 * print all globals variables
	 */
	private function printVars(){
		
		$vars = get_object_vars($this);
		
		dmp($vars);
		//exit();
	}
		
	
	/**
	 * init by addon
	 * @param $addon
	 */
	public function initByAddon(UniteCreatorAddon $addon){
		
		$this->addon = $addon;
		
	}
	
	
	/**
	 * add log text
	 */
	private function addLog($text){
		$this->arrLog[] = $text;
	}
	
	
	/**
	 * get log text
	 */
	public function getLogText(){
		
		$text = implode("<br>", $this->arrLog);
		
		return($text);
	}
	
	
	private function ______________EXPORT_ADDON___________(){}
	
	
	
	/**
	 * create export folder if not exists
	 */
	private function prepareExportFolders_addons(){
	
		$this->prepareExportFolders_globalExport();
		
		$this->pathExportAddons = $this->pathExport."addons/";
		UniteFunctionsUC::mkdirValidate($this->pathExportAddons, "Export Addons");
		
		//clear folder
		UniteFunctionsUC::deleteDir($this->pathExportAddons, false);
		
		$this->pathExportAddon = $this->pathExportAddons."addon_".UniteFunctionsUC::getRandomString(10)."/";
		$this->pathExportAddonAssets = $this->pathExportAddon."assets/";
	
		//create index.html
		UniteFunctionsUC::writeFile("", $this->pathExportAddons."index.html");

		UniteFunctionsUC::mkdirValidate($this->pathExportAddon, "Export Addon");
		
		$addonName = $this->addon->getName();
	
		$tempFilenameZip = $addonName."_".UniteFunctionsUC::getRandomString(10).".zip";
		
		$this->filepathAddonZip = $this->pathExportAddons.$tempFilenameZip;
		
	}
	
		
	
	/**
	 * modify includes in config that it will be saved in new way
	 */
	private function modifyConfig($config){
		
		$includes = UniteFunctionsUC::getVal($config, "includes");
		
		$arrJs = UniteFunctionsUC::getVal($includes, "js");
		$arrCss = UniteFunctionsUC::getVal($includes, "css");
		
		$arrJs = HelperUC::arrUrlsToRelative($arrJs, true);
		$arrCss = HelperUC::arrUrlsToRelative($arrCss, true);
		
		if(!empty($arrJs))
			$includes["js"] = $arrJs;
		
		if(!empty($arrCss))
			$includes["css"] = $arrCss;
		
		$config["includes"] = $includes;
		
		return($config);
	}
	
	
	
	/**
	 * get xml from addon
	 */
	private function getJsonFromAddon(){
		
		$arrAddon = array();
		
		$addonType = $this->addon->getType();
		
		$addonName = $this->addon->getName();
		$addonAlias = $this->addon->getAlias();
		
		//set alias and name by type
		if(!empty($addonType) && empty($addonAlias)){
			$addonAlias = $addonName;
			$addonName = $addonName."_".$addonType;
		}
		
		$arrAddon["addontype"] = $addonType;
		$arrAddon["name"] = $addonName;
		$arrAddon["alias"] = $addonAlias;
		
		$arrAddon["title"] = $this->addon->getTitle();
		$arrAddon["description"] = $this->addon->getDescription();
		
		$arrConfig = $this->addon->getConfig();
		$arrAddon["config"] = $this->modifyConfig($arrConfig);
		
		$arrTemplates = $this->addon->getTemplates();
		
		$arrTemplateNames = array();
		foreach($arrTemplates as $name=>$content){
			if(!empty($content))
				$arrTemplateNames[] = $name;
		}
		
		$arrAddon["templates"] = $arrTemplateNames;
		
		$json = json_encode($arrAddon, JSON_PRETTY_PRINT);
		
		return($json);
	}
	
	
	/**
	 * write file if not empty content
	 */
	private function writeFile($filename, $content){
		
		$filepath = $this->pathExportAddon.$filename;
		if(!empty($content))
			UniteFunctionsUC::writeFile($content, $filepath);
	}
	
	
	/**
	 * write export data
	 */
	private function createExportFiles(){
	
		//write addon main file
		$strJson = $this->getJsonFromAddon();
		$this->writeFile("addon.json", $strJson);
		
		//write template files
		$arrTemplates = $this->addon->getTemplates();		
		
		//write templates
		foreach($arrTemplates as $name=>$content){
			$filename = $name.".tpl";
			$this->writeFile($filename, $content);
		}
		
		/*
		//write data
		$testData = $this->addon->getAllTestData(true);
		if(!empty($testData))
			$this->writeFile("data.json", $testData);
		*/
	}
	
	
	/**
	 * check if assets path is ready for export
	 */
	private function isPathAssetsReadyForExport($pathAssets){
		$isUnderAssets = HelperUC::isPathUnderAssetsPath($pathAssets);
		if(!$isUnderAssets)
			return(false);
		
		if(is_dir($pathAssets) == false)
			return(false);
		
		$isPathAssets = HelperUC::isPathAssets($pathAssets);
		if($isPathAssets == true)
			return(false);
		
		return(true);
	}
	
	
	/**
	 * copy addon assets
	 */
	private function copyAssets(){
		
		$options = $this->addon->getOptions();
		$dirAssets = $this->addon->getOption("path_assets");
		
		if(empty($dirAssets))
			return(false);
		
		$pathAssets = GlobalsUC::$pathAssets.$dirAssets.'/';
		
		$isReady = $this->isPathAssetsReadyForExport($pathAssets);
		if($isReady == false)
			return(false);
		
		//make assets folder
		UniteFunctionsUC::mkdirValidate($this->pathExportAddonAssets, "assets folder");
		
		$pathAssetsDest = $this->pathExportAddonAssets.$dirAssets."/";
		
		UniteFunctionsUC::mkdirValidate($pathAssetsDest, "assets dest folder: $dirAssets");
		
		UniteFunctionsUC::copyDir($pathAssets, $pathAssetsDest);
		
		//delete thumbs folders
		
		$arrDirs = UniteFunctionsUC::getDirList($pathAssets);
		
		foreach($arrDirs as $dir){
			
			switch($dir){
				case GlobalsUC::DIR_THUMBS_ELFINDER:
				case ".quarantine":
				case GlobalsUC::DIR_THUMBS:
					
					$pathDirDelete = $pathAssetsDest.$dir."/";
					
					UniteFunctionsUC::deleteDir($pathDirDelete);
					
				break;
			}
		}
						
	}
	
	
	/**
	 * make export zip file
	 */
	private function makeExportZipFile(){
		
		$zip = new UniteZipUC();
		$zip->makeZip($this->pathExportAddon, $this->filepathAddonZip);
		
		if(file_exists($this->filepathAddonZip) == false)
			UniteFunctionsUC::throwError("zip file {$this->filepathAddonZip} could not be created");
	}

	/**
	 * delete export addon folder
	 */
	private function deleteExportAddonFolder(){
		
		if(!empty($this->pathExportAddon) && is_dir($this->pathExportAddon))
			UniteFunctionsUC::deleteDir($this->pathExportAddon);
		
	}
	
	/**
	 * write to folder
	 */
	private function moveExportZipToFolder($moveFolder, $createCategoryFolder = false){
		
		UniteFunctionsUC::validateDir($moveFolder);
		
		/**
		 * create category folder if needed
		 */
		if($createCategoryFolder == true){
			$catTitle = $this->addon->getCatTitle();
			if(!empty($catTitle)){
				$moveFolder = $moveFolder.$catTitle."/";
				UniteFunctionsUC::mkdirValidate($moveFolder, $catTitle);
			}
		}
		
		$addonName = $this->addon->getName();
		$filename = $addonName.".zip";
		
		$newFilepath = $moveFolder.$filename;
		
		$success = rename($this->filepathAddonZip, $newFilepath);
		
		if($success == false)
			UniteFunctionsUC::throwError("Can't move export zip:$filename to the export folder");
		
	}
	
	
	/**
	 * download the zip file
	 */
	private function downloadFile(){
		
		$addonName = $this->addon->getName();
		$filename = $addonName.".zip";
		
		UniteFunctionsUC::downloadFile($this->filepathAddonZip, $filename);
	}
	
	
	/**
	 * export addon - create export file and send it to download.
	 */
	public function export($moveFolder = null, $createCategoryFolders = false){
		
		$this->validateInited();
		
		try{
			$this->prepareExportFolders_addons();
			
			$this->createExportFiles();
			$this->copyAssets();
			$this->makeExportZipFile();
			$this->deleteExportAddonFolder();
			
			if(!empty($moveFolder)){
				$this->moveExportZipToFolder($moveFolder, $createCategoryFolders);
				$this->deleteExportAddonFolder();
			}
			else{
				$this->downloadFile();
				exit();
			}
			
		}catch(Exception $e){
			
			$this->deleteExportAddonFolder();
			throw $e;
		}
		
	}
	
	
	private function ______________EXPORT_CATEGORY___________(){}
	
	
	/**
	 * prepare export categories folders
	 */
	private function prepareExportFolders_categories($catTitle, $exportType=""){
		
		$this->prepareExportFolders_globalExport();
		
		//make folders
		$this->pathExportCategories = $this->pathExport."categories/";
		
		UniteFunctionsUC::mkdirValidate($this->pathExportCategories, "Categories");
		
		//clear folder
		UniteFunctionsUC::deleteDir($this->pathExportCategories, false);
		
		$this->pathExportCategory = $this->pathExportCategories."category_".UniteFunctionsUC::getRandomString(10)."/";
		
		UniteFunctionsUC::mkdirValidate($this->pathExportCategory, "Export Category");
		
		//create index.html
		UniteFunctionsUC::writeFile("", $this->pathExportCategories."index.html");
		
		//create actual category folder
		if(!empty($catTitle))
			$this->prepareExportFolders_category($catTitle, $exportType);
		
	}
	
	
	/**
	 * prepare category export folder and names
	 */
	private function prepareExportFolders_category($catTitle, $exportType=""){
		
		UniteFunctionsUC::validateNotEmpty($catTitle);
		
		UniteFunctionsUC::validateNotEmpty($this->pathExportCategory);
		
		$this->pathExportCategoryAddons = $this->pathExportCategory.$catTitle."/";
		UniteFunctionsUC::mkdirValidate($this->pathExportCategoryAddons, "$catTitle category");
		
		$this->prepareExportFolders_exportFilename($catTitle, $exportType);
	}
	
	
	/**
	 * prepare export folders file path and filename
	 */
	private function prepareExportFolders_exportFilename($catTitle, $exportType="", $addSuffix = true){
		
		$suffix = "";
		
		if($addSuffix == true){
			if(!empty($exportType))
				$suffix .= "_".$exportType;
			$suffix .= "_addons";
		}
		
		$nameZip = $catTitle.$suffix;
		$nameZip = HelperUC::convertTitleToHandle($nameZip);
		
		$this->filenameCategoryZip = $nameZip.".zip";
		
		$tempFilenameZip = $nameZip."_".UniteFunctionsUC::getRandomString(10).".zip";
		
		$this->filepathCategoryZip = $this->pathExportCategories.$tempFilenameZip;
		
	}
	
	
	/**
	 * create category addons zips
	 * put them in the category export folder
	 */
	private function createCategoryAddonsZips($arrAddons){
		
		foreach($arrAddons as $addon){
			$objExport = new UniteCreatorExporter();
			$objExport->initByAddon($addon);
			
			$objExport->export($this->pathExportCategoryAddons);
		}
		
	}

	
	/**
	 * make export zip file
	 */
	private function makeExportCategoryZipFile(){
	
		$zip = new UniteZipUC();
		$zip->makeZip($this->pathExportCategory, $this->filepathCategoryZip);
		
		if(file_exists($this->filepathCategoryZip) == false)
			UniteFunctionsUC::throwError("zip file {$this->filepathCategoryZip} could not be created");
	}
	
	
	/**
	 * download the category zip file
	 */
	private function downloadCategoryFile(){
	
		UniteFunctionsUC::downloadFile($this->filepathCategoryZip, $this->filenameCategoryZip);
	}
	
	
	/**
	 * get addons array and cat title from catID
	 */
	private function exportCatAddons_getArrAddons($catID, $exportType){
		
		$objCat = new UniteCreatorCategories();
		$arrCat = $objCat->getCat($catID);
		
		if(empty($exportType))
			$exportType = UniteFunctionsUC::getVal($arrCat, "type");
		
		$catTitle = UniteFunctionsUC::getVal($arrCat, "title");
		
		UniteFunctionsUC::validateNotEmpty($catTitle);
		
		$objAddons = new UniteCreatorAddons();
		
		$arrAddons = $objAddons->getCatAddons($catID);
		
		//set addon types
		foreach($arrAddons as $addon){
			$addon->setType($exportType);
		}
		
		if(empty($arrAddons))
			UniteFunctionsUC::throwError("No addons found");
		
		$output = array();
		$output["addons"] = $arrAddons;
		$output["title"] = $catTitle;
		
		return($output);
	}
	
	
	/**
	 * export category addons
	 */
	public function exportCatAddons($catID, $exportType="", $runFunc = null){
		
		$output = $this->exportCatAddons_getArrAddons($catID, $exportType);
		$arrAddons = $output["addons"];
		$catTitle = $output["title"];
		
		try{
			
			$this->prepareExportFolders_categories($catTitle, $exportType);
			$this->createCategoryAddonsZips($arrAddons);
			
			if($runFunc === null){
				$this->makeExportCategoryZipFile();
				$this->downloadCategoryFile();	
				exit();
			}else{
				call_user_func(array($this, $runFunc));
			}
	
		}catch(Exception $e){
	
			throw $e;
		}
		
	}
	
	private function ______________EXPORT_CATS___________(){}
	
	
	/**
	 * export multiple categories and their addons
	 */
	public function exportCatsAndAddons($arrCats, $exportType="", $runFunc = null, $filename = ""){
		
		UniteFunctionsUC::validateNotEmpty($arrCats);
		if(is_array($arrCats) == false)
			UniteFunctionsUC::throwError("export cats error: The categories should be array");
		
		try{
		
			//create general export folder
			$this->prepareExportFolders_categories("");
			
			foreach($arrCats as $catID){
				
				$output = $this->exportCatAddons_getArrAddons($catID, $exportType);
				$arrAddons = $output["addons"];
				$catTitle = $output["title"];
				
				$this->prepareExportFolders_category($catTitle, $exportType);
				$this->createCategoryAddonsZips($arrAddons);
			}
			
			//create zip file
			if(empty($filename)){
				if(count($arrCats) == 1)
					$filename = $catTitle;
				else
					$filename = self::MULTIPLE_FOLDERS_FILENAME;
			}
			
			UniteFunctionsUC::validateNotEmpty($filename,"export filename");
			
			
			if($runFunc === null){
				$this->prepareExportFolders_exportFilename($filename, $exportType);
				
				$this->makeExportCategoryZipFile();
				$this->downloadCategoryFile();
				exit();
			}else{
				call_user_func(array($this, $runFunc));
			}
			
			
		}catch(Exception $e){
		
			throw $e;
		}
		
	}
	
	private function ______________IMPORT_ADDON___________(){}
	
	/**
	 * validate that the temp file array
	 */
	private function validateArrTempFile($arrTempFile){
		
		$filename = UniteFunctionsUC::getVal($arrTempFile, "name");
		UniteFunctionsUC::validateNotEmpty($filename, "addon file name");
		
		$info = pathinfo($filename);
		$ext = UniteFunctionsUC::getVal($info, "extension");
		
		if($ext != "zip")
			UniteFunctionsUC::throwError("Wrong import addon file type: {$filename}, should be zip type only.");
		
	}
	
	
	/**
	 * prepare import addon folders
	 */
	private function prepareImportFolders(){
		
		//prepare import folder
		$this->prepareImportFolders_globalImport();
		
		if($this->isPathSingle == false){		//first path
			
			$pathImportBase = $this->pathImport."first/";
					
		}else{
			$pathImportBase = $this->pathImport."single/";
		}
		
		UniteFunctionsUC::mkdirValidate($pathImportBase, "import first");
		UniteFunctionsUC::deleteDir($pathImportBase, false);
		
		//create index.html
		UniteFunctionsUC::writeFile("", $pathImportBase."index.html");
		
		self::$serial++;
		
		$this->pathImportAddon = $pathImportBase."addon_".self::$serial."_".UniteFunctionsUC::getRandomString(10)."/";
		$this->pathImportAddonAssets = $this->pathImportAddon."assets/";
				
		UniteFunctionsUC::mkdirValidate($this->pathImportAddon, "import addon");
				
		
	}
	
	
	/**
	 * delete import addon folder
	 */
	private function deleteImportAddonFolder(){
	
		if(!empty($this->pathImportAddon) && is_dir($this->pathImportAddon))
			UniteFunctionsUC::deleteDir($this->pathImportAddon);
	}
	
	
	/**
	 * unpack import addon from temp file
	 */
	private function extractImportAddonFile($arrTempFile){
		
		$filepath = UniteFunctionsUC::getVal($arrTempFile, "tmp_name");
		
		$zip = new UniteZipUC();
		$extracted = $zip->extract($filepath, $this->pathImportAddon);
		
		if($extracted == false)
			UniteFunctionsUC::throwError("The import addon zip didn't extracted");
		
	}
	
	
	
	
	/**
	 * import templates
	 */
	private function importAddonData_addTemplates($arrImport, $addonData){
		
		//prepare templates data
		$templateNames = UniteFunctionsUC::getVal($arrImport, "templates");
		
		$arrTemplates = array();
		foreach($templateNames as $templateName){
		
			$filenameTemplate = $templateName.".tpl";
			$filepathTemplate = $this->pathImportAddon.$filenameTemplate;
			if(is_file($filepathTemplate) == false)
				UniteFunctionsUC::throwError("Template {$filenameTemplate} not found!");
		
			$templateContent = file_get_contents($filepathTemplate);
			$arrTemplates[$templateName] = $templateContent;
		}
		
		$addonData["templates"] = json_encode($arrTemplates);
		
		return($addonData);
	}

	
	/**
	 * add test data
	 */
	private function importAddonData_addTestData($addonData){
		
		$filenameTestData = "data.json";
		$filepathTestData = $this->pathImportAddon.$filenameTestData;
		
		if(file_exists($filepathTestData) == false)
			return($addonData);
		
		$testContent = file_get_contents($filepathTestData);
		if(empty($testContent))
			return($addonData);
		
		$testContent = @json_decode($testContent);
		
		if(empty($testContent))
			return(false);
		
		$testContent = UniteFunctionsUC::convertStdClassToArray($testContent);
		
		foreach($testContent as $key=>$arrContent){
			if(empty($arrContent))
				continue;
			
			$jsonContent = $arrContent;
			if(is_array($jsonContent))
				$jsonContent = json_encode($jsonContent);
			
			$addonData[$key] = $jsonContent;
		}
		
		return($addonData);
	}
	
	
	/**
	 * import addon data
	 */
	private function importAddonData($catID, $overwrite = true){
		
		$filenameAddon = "addon.json";
		$filepathData = $this->pathImportAddon.$filenameAddon;
		
		if(is_file($filepathData) == false)
			UniteFunctionsUC::throwError("Addon import file: $filenameAddon don't found");
		
		$contents = file_get_contents($filepathData);
		
		if(empty($contents))
			UniteFunctionsUC::throwError("Empty import file {$filenameAddon} contents");
		
		$arrImport = @json_decode($contents);
		
		if(empty($arrImport))
			UniteFunctionsUC::throwError("Wrong import file {$filenameAddon} content");
			
		$arrImport = UniteFunctionsUC::convertStdClassToArray($arrImport);
		
		if(is_array($arrImport) == false)
			UniteFunctionsUC::throwError("Wrong addon import data, should be array");
		
		$addonName = UniteFunctionsUC::getVal($arrImport, "name");
		$addonType = UniteFunctionsUC::getVal($arrImport, "addontype");
		$addonAlias = UniteFunctionsUC::getVal($arrImport, "alias");
		
		$textLog = "addon ".$addonName;
		//if empty addon type - set by the given type
		if(empty($addonType) && !empty($this->mustAddonType)){
			$addonType = $this->mustAddonType;
			if(empty($addonAlias))
				$addonAlias = $addonName;
			$addonName = $addonName."_".$addonType;
		}
		
		//check if the addon exists, if needed
		if($overwrite == false){
			
			$isExists = $this->objAddons->isAddonExistsByName($addonName);
			
			if($isExists == true){
				$textLog .= " skipped";
				$this->addLog($textLog);
				return(false);
			}
			
		}
		
		//check if addon type match
		if(!empty($this->mustAddonType) && $this->mustAddonType != $addonType){
			
			$textLog .= " wrong addon type, skipped";
			$this->addLog($textLog);
			return(false);
		}
		
			
		//prepare data
		$addonData = array();
		$addonData["name"] = $addonName;
		$addonData["alias"] = $addonAlias;
		$addonData["addontype"] = $addonType;
		$addonData["title"] = UniteFunctionsUC::getVal($arrImport, "title");
		$addonData["description"] = UniteFunctionsUC::getVal($arrImport, "description");
		$addonData["catid"] = $catID;
		
		$config = UniteFunctionsUC::getVal($arrImport, "config");
		if(is_array($config) == false)
			UniteFunctionsUC::throwError("Wrong addon config data");
			
		$addonData["config"] = json_encode($config);
		
		//---- import templates ---- 
		
		$addonData = $this->importAddonData_addTemplates($arrImport, $addonData);
		
		
		// ------ import test data ----------
		
		$addonData = $this->importAddonData_addTestData($addonData);
		
		$objAddon = new UniteCreatorAddon();
		$isNewAdded = $objAddon->importAddonData($addonData);
		
		if($isNewAdded)
			$textLog .= " added";
		else
			$textLog .= " overwrited";
		
		$this->addLog($textLog);
		
		//save added type
		$this->importedAddonType = $addonType;
		
		return(true);
	}
	
	
	/**
	 * copy import assets folder
	 */
	private function copyImportAssetsFolder(){
		
		if(is_dir($this->pathImportAddonAssets) == false)
			return(false);
		
		UniteFunctionsUC::copyDir($this->pathImportAddonAssets, GlobalsUC::$pathAssets);
		
	}
	
	
	/**
	 * check if extracted addon single
	 */
	private function isExtractedAddonSingle(){
		
		if($this->isPathSingle == true)
			return(true);
		
		$filenameAddon = "addon.json";
		$filepathData = $this->pathImportAddon.$filenameAddon;
		
		if(file_exists($filepathData))
			return(true);
		
		return(false);
	}
	
	
	/**
	 * set import single type
	 */
	public function setImportSingleAddon(){
		
		$this->isPathSingle = true;
		
	}
	
	
	/**
	 * set must import addon type
	 */
	public function setMustImportAddonType($addonType){
		
		$this->mustAddonType = $addonType;
	}
	
	
	/**
	 * import addon
	 * tempFile can be array or filepath
	 */
	public function import($catID, $arrTempFile, $overwrite = true){
		
		if($this->isImportedOnce == true)
			UniteFunctionsUC::throwError("The import script can't run twice");
		
		$this->isImportedOnce = true;
		
		if(empty($catID) || is_numeric($catID) == false)
			$catID = 0;
		
		//crate array from filepath
		if(getType($arrTempFile) == "string"){
			$filepath = $arrTempFile;
			$arrInfo = pathinfo($filepath);
			$filename = UniteFunctionsUC::getVal($arrInfo, "basename");
			
			$arrTempFile = array();
			$arrTempFile["tmp_name"] = $filepath;
			$arrTempFile["name"] = $filename;
		}
		
		$this->validateArrTempFile($arrTempFile);
		
		try{
			
			$this->prepareImportFolders();
			
			//$this->printVars();
			
			$this->extractImportAddonFile($arrTempFile);
			
			$isSingle = $this->isExtractedAddonSingle();
			
			if($isSingle == true){
				
				$isImported = $this->importAddonData($catID, $overwrite);
				
				if($isImported == true)
					$this->copyImportAssetsFolder();
			}else{
				
				$this->importAddonsFromFolder($this->pathImportAddon, $catID, $overwrite);
			}
			
			$this->deleteImportAddonFolder();
			
			
		}catch(Exception $e){
			
			$this->deleteImportAddonFolder();
			
			throw $e;
		}
		
		$logText = $this->getLogText();
		return($logText);
	}
	
	/**
	 * get imported addon type
	 */
	public function getImportedAddonType(){
		
		return($this->importedAddonType);
	}
	
	
	private function ______________BULK_IMPORT___________(){}
	
	/**
	 * get short log text (only from import addons from folder)
	 */
	public function getTextLogShort(){
		
		return($this->textLogShort);
	}
	
	/**
	 * import all addons from folder
	 */
	public function importAddonsFromFolder($path, $catIDForce = 0, $overwrite = false){
		
		if(is_dir($path) == false)
			return(false);
		
		if($catIDForce == "all" || empty($catIDForce))
			$catIDForce = null;
		
		$arrFiles = UniteFunctionsUC::getFileListTree($path, "zip");
		
		//make files with paths list
		$arrFilesFull = array();
		$arrCats = array();
		
		foreach($arrFiles as $file){
			
			$pathRelative = str_replace($path, "", $file);
			
			$parentDir = dirname($pathRelative);
			if($parentDir == ".")
				$parentDir = "";
			
			$item = array();
			$item["catname"] = $parentDir;
			$item["filepath"] = $file;
			
			$arrCats[$parentDir] = null;
			
			$arrFilesFull[] = $item;
		}
				
		//create categories
		if(empty($catIDForce)){
			
			$objCategories = new UniteCreatorCategories();
			
			$addonType = $this->mustAddonType;
			if(empty($addonType))
				$addonType = "";
			
			foreach($arrCats as $catname => $value){
				$catID = $objCategories->getCreateCatByTitle($catname, $addonType);
				$arrCats[$catname] = $catID;
			}
			
		}
		
		try{
			
			//update category type on the way
			$isAddonTypeUpdated = false;
			$addonType = "";
			
			foreach($arrFilesFull as $item){
				
				$catName = $item["catname"];
				$filepath = $item["filepath"];
				
				if(file_exists($filepath) == false)
					continue;
				
				$catID = 0;
				if(!empty($catIDForce))
					$catID = $catIDForce;
				else{
					$catID = UniteFunctionsUC::getVal($arrCats, $catName);
					if(empty($catID))
						$catID = 0;
				}
				
				$objImporter = new UniteCreatorExporter();
				$objImporter->setImportSingleAddon();
				
				$logText = $objImporter->import($catID, $filepath, $overwrite);
				
				//update category type if not updated
				if($isAddonTypeUpdated == false){
					
					$addonType = $objImporter->getImportedAddonType();
					
					if(!empty($addonType) && !empty($catID)){
						$objCats = new UniteCreatorCategories();
						$objCats->updateType($catID, $addonType);
						
						$isAddonTypeUpdated = true;
					}
				}
				
				$this->addLog($logText);
				
			}
			
			$logText = $this->getLogText();
			
			//create categories short text
			$textCats = "";
			if(!empty($arrCats)){
				$arrCatNames = array_keys($arrCats);
				$this->textLogShort = "Installed ".implode(",", $arrCatNames);
				$numCats = count($arrCatNames);
				
				if($numCats == 1)
					$this->textLogShort .= " bundle";
				else 
					$this->textLogShort .= " bundles";
					
			}else{
				$numAddons = count($arrFilesFull);
				$this->textLogShort = "Installed ".$numAddons." addons";
			}
			
			
			return($logText);
			
		}catch(Exception $e){
			
			//HelperHtmlUC::outputException($e);
			//exit();
			
			$message = $e->getMessage();
			
			$this->addLog("error: $message");
			
			throw $e;
						
		}
		
	}
	
	
	
}
