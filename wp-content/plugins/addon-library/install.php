<?php
/**
 * @package Addon Library for Joomla 1.7-3.5
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('_JEXEC') or die('Restricted access');


class com_addonlibraryInstallerScript
{
	
	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	public function __constructor(JAdapterInstance $adapter){
		
	}
 
			
	
	/**
	 * Called before any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($route, JAdapterInstance $adapter){
	}
 

	/**
	 * publish the plugin
	 */
	private function publishPlugin(){
		
		$sql = "UPDATE `#__extensions` SET `enabled`=1 where (`type`=\"plugin\" and `element`=\"addonlibrary\")";
		
		$db = JFactory::getDbo();
		$db->setQuery($sql);
		$db->query();		
	}
	
	
	/**
	 * print message about addons install
	 */
	private function printInstallAddonsMessage($message, $fullLog){
		
		if(empty($message))
			return(false);
		 
		$html = "<div style='padding-bottom:10px;'>";
		$html .= "<span style='color:green;'>$message</span>";
		
		$html .= " &nbsp; <a style='color:green;text-decoration:underline;' href='javascript:void(0);' onclick='document.getElementById(\"addons_install_details\").style.display=\"\"'>show details</a>";
		
		$html .= "<div id='addons_install_details' style='padding-top:10px;padding-bottom:10px;display:none'>";
		$html .= $fullLog;
		$html .= "</div>";
		
		$html .= "</div>";
		
		echo $html;
	}
	
	
	/**
	 * install addons
	 */
	private function installAddons(JAdapterInstance $adapter){
		
		//get addons path
		$installer = new JInstaller();
		$p_installer = $adapter->getParent();
		$pathInstaller = $p_installer->getPath("source");
		
		$pathInstallAddons = $pathInstaller."/install_addons/";
		
		//require component files
		
		$pathIncludes = JPATH_ADMINISTRATOR."/components/com_addonlibrary/includes.php";
		require $pathIncludes;
		
		//import addons
		
		$objImporter = new UniteCreatorExporter();
		$fullLog = $objImporter->importAddonsFromFolder($pathInstallAddons);
		$textLog = $objImporter->getTextLogShort();
		
		$this->printInstallAddonsMessage($textLog, $fullLog);
		
	}
	
	
	/**
	 * update content id
	 */
	private function updateTables(){
		$db = JFactory::getDbo();
		
		//add addontype to addons table
		
		try{
			
			//alter table change
			$sql = "ALTER TABLE `#__addonlibrary_addons`
			add `addontype` varchar(255);";
		
			$db->setQuery($sql);
			$db->query();
						
		
		}catch(Exception $e){
			//throw $e;
			//skip errors
		}
		
		//add alias to addons table
		
		try{
			
			//alter table change
			$sql = "ALTER TABLE `#__addonlibrary_addons`
			add `alias` varchar(128);";
			
			$db->setQuery($sql);
			$db->query();
			
		}catch(Exception $e){
			//throw $e;
			//skip errors
		}
			
	}
	
	
	/**
	 * Called after any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($route, JAdapterInstance $adapter){
				
		
		try{
			
			if($route == "update"){
				$this->updateTables();
			}
			
			$this->installAddons($adapter);
			
		}catch(Exception $e){
			//skip errors
		}
		
	}	
	
	
	
	/**
	 * 
	 * install the modules from "modules" folder
	 */
	public function installModules(JAdapterInstance &$adapter,$type="install"){
		
		$ds = "";
		if(defined("DIRECTORY_SEPARATOR"))
			$ds = DIRECTORY_SEPARATOR;
		else
			$ds = DS;
		
		$manifest = $adapter->get("manifest");
		
		$installer = new JInstaller();
		$p_installer = $adapter->getParent();
		
		// Install modules
		if (is_object($manifest->modules->module)){	
			foreach($manifest->modules->module as $module){
				$attributes = $module->attributes();
				$modulePath = $p_installer->getPath("source") . $ds . $attributes['folder'] . $ds . $attributes['module'];
				
				if($type == "install")
					$installer->install($modulePath);
				else 
					$installer->update($modulePath);
			}
		}
		
	}

	/**
	 * 
	 * install the plugins from "plugins" folder
	 */
	public function installPlugins(JAdapterInstance &$adapter,$type="install"){
		
		$ds = "";
		if(defined("DIRECTORY_SEPARATOR"))
			$ds = DIRECTORY_SEPARATOR;
		else
			$ds = DS;
		
		$manifest = $adapter->get("manifest");
		
		$installer = new JInstaller();
		$p_installer = $adapter->getParent();
		
		//Install plugins
		if (is_object($manifest->plugins->plugins)){	
			foreach($manifest->plugins->plugin as $plugin){
				$attributes = $plugin->attributes();
				$pluginPath = $p_installer->getPath("source") . $ds . $attributes['folder'] . $ds . $attributes['plugin'];
				
				if($type == "install")
					$installer->install($pluginPath);
				else 
					$installer->update($pluginPath);
			}
		}
		
	}
	
	
	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(JAdapterInstance $adapter){
		
		$this->installModules($adapter,"install");
		$this->installPlugins($adapter,"install");
		
		$this->publishPlugin();		
	}
 
	
	/**
	 * Called on update
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function update(JAdapterInstance $adapter){
		
		$this->installModules($adapter,"update");
		$this->installPlugins($adapter,"update");
		
		$this->publishPlugin();
	}

	
	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	public function uninstall(JAdapterInstance $adapter){
		
	}
}

?>