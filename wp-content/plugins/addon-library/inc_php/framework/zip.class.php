<?php
/**
 * @package Addon Library
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('ADDON_LIBRARY_INC') or die('Restricted access');


	class UniteZipUC{
		
		//custom extract vars
		
		private $_methods = array(0x0 => 'None', 0x1 => 'Shrunk', 0x2 => 'Super Fast', 0x3 => 'Fast', 0x4 => 'Normal', 0x5 => 'Maximum', 0x6 => 'Imploded',
				0x8 => 'Deflated');
		private $_ctrlDirHeader = "\x50\x4b\x01\x02";
		private $_ctrlDirEnd = "\x50\x4b\x05\x06\x00\x00\x00\x00";
		private $_fileHeader = "\x50\x4b\x03\x04";
		private $_data = null;
		private $_metadata = null;
		
		
		private $zip;
		
		
		/**
		 * check if php has native zip functions
		 */
		protected function isNativeSupportExists(){
			
			return (function_exists('zip_open') && function_exists('zip_read'));
		}
		
		
		/**
		 * 
		 * get true / false if the zip archive exists.
		 */
		protected function isZipArchiveExists(){
			
			$exists = class_exists("ZipArchive");
			return $exists;
		}
		
		
	    /**
	     * 
	     * add zip file
	     */
	    private function addItem($basePath,$path){
	    	
	    	$rel_path = str_replace($basePath."/", "", $path);
	    	
	    	if(is_dir($path)){		//directory
	    		
		    	//add dir to zip
		    	if($basePath != $path)
		    		$this->zip->addEmptyDir($rel_path);
	    		
	    		$files = scandir($path);
	    		foreach($files as $file){
	    			if($file == "." || $file == ".." || $file == ".svn")
	    				continue;
	    			$filepath = $path."/".$file;
	    			$this->addItem($basePath, $filepath);
	    		}
	    	}
	    	else{	//file
	    		if(!file_exists($path))
	    			UniteFunctionsUC::throwError("filepath: '$path' don't exists, can't zip");
	    		
	    		$this->zip->addFile($path,$rel_path);
	    	}
	    }	   
		
	    /**
	     * 
	     * make zip archive
	     * if exists additional paths, add additional items to the zip
	     */
	    public function makeZip($srcPath, $zipFilepath,$additionPaths = array()){
	    	
	    	if(!is_dir($srcPath))
	    		UniteFunctionsUC::throwError("The path: '$srcPath' don't exists, can't zip");
	        
	    	$this->zip = new ZipArchive;
	        $success = $this->zip->open($zipFilepath, ZipArchive::CREATE);
	        
	        if($success == false)
	        	UniteFunctionsUC::throwError("Can't create zip file: $zipFilepath");
	        
	        $this->addItem($srcPath,$srcPath);
	       	
	        if(gettype($additionPaths) != "array")
	        	UniteFunctionsUC::throwError("Wrong additional paths variable.");
	       	
	        	
	        //add additional paths
	        if(!empty($additionPaths))
	        	foreach($additionPaths as $path){
	        		if(!is_dir($path))
	        			UniteFunctionsUC::throwError("Path: $path not found, can't zip");
	        		$this->addItem($path, $path);
	        	}
	        
           	$this->zip->close();
	    }
	    
	    
	    /**
	     * check if dir exists, if not, create it recursivelly
	     */
	    protected function checkCreateDir($filepath){
	    	
	    	$dir = dirname($filepath);
	    	
	    	if(is_dir($dir) == false)
	    		$success = $this->checkCreateDir($dir);
	    	else
	    		return(true);
	    	
	    	//this dir is not exists, and all parent dirs exists
	    	
	    	@mkdir($dir);
	    	if(is_dir($dir) == false)
	    		UniteFunctionsUC::throwError("Can't create directory: $dir");
	    	
	    }
	    
	    
	    /**
	     * write some file
	     */
	    protected function writeFile($str, $filepath){
	    	
	    	//create folder if not exists
	    	$this->checkCreateDir($filepath);
	    	
	    	$fp = fopen($filepath,"w+");
	    	fwrite($fp,$str);
	    	fclose($fp);
	    	
	    	if(file_exists($filepath) == false)
	    		UniteFunctionsUC::throwError("can't write file: $filepath");
	    	
	    }
	    
	    
	    /**
	     * extract using native library
	     */
	    protected function extract_native($src, $dest){

	    	if(function_exists("zip_open") == false)
	    		UniteFunctionsUC::throwError("Please enable zip_open php function in php.ini");
	    	
	    	$zip = zip_open($src);
	    	
	    	if(is_resource($zip) == false)
	    		UniteFunctionsUC::throwError("Unable to open zip file: $src");
	    	
	    	if(!is_dir($dest))
	    		@mkdir($dest);
	    	
	    	if(!is_dir($dest))
	    		UniteFunctionsUC::throwError("Could not create folder: $dest");
	    	
	    	$dest = UniteFunctionsUC::addPathEndingSlash($dest);
	    	
    		// Read files in the archive
    		while ($file = @zip_read($zip)){

    			$entryOpened = zip_entry_open($zip, $file, "r");
    			
    			if($entryOpened == false)
    				UniteFunctionsUC::throwError("unable to read entry");
    			
    			$filenameCorrent = substr(zip_entry_name($file), strlen(zip_entry_name($file)) - 1) != "/";
    			if($filenameCorrent == false){
    				zip_entry_close($file);
    				continue;
    			}	
    			
    			//write file
    			
    			$buffer = zip_entry_read($file, zip_entry_filesize($file));
    			$destFilepath = $dest . zip_entry_name($file);
    			
    			$this->writeFile($buffer, $destFilepath);
    			
    		}
    		
    		@zip_close($zip);
	    	    		
	    	return true;
	    }
	    
	    
	    /**
	     * extract using zip acchive
	     */
	    protected function extract_zipArchive($src, $dest){
	    	
	    	$zip = new ZipArchive;
	    	if ($zip->open($src)===true){
	    		$zip->extractTo($dest);
	    		$zip->close();
	    		return true;
	    	}
	    	return false;
	    }
	    
	    
	    private function ____________EXTRACT_CUSTOM__________(){}
	    
	    
	    /**
	     * read zip info
	     */
	    private function extract_custom_readZipInfo(&$data){
	    	
	    	$entries = array();
	    
	    	// Find the last central directory header entry
	    	$fhLast = strpos($data, $this->_ctrlDirEnd);
	    
	    	do
	    	{
	    		$last = $fhLast;
	    	}
	    	while (($fhLast = strpos($data, $this->_ctrlDirEnd, $fhLast + 1)) !== false);
	    
	    	// Find the central directory offset
	    	$offset = 0;
	    
	    	if ($last)
	    	{
	    		$endOfCentralDirectory = unpack(
	    				'vNumberOfDisk/vNoOfDiskWithStartOfCentralDirectory/vNoOfCentralDirectoryEntriesOnDisk/' .
	    				'vTotalCentralDirectoryEntries/VSizeOfCentralDirectory/VCentralDirectoryOffset/vCommentLength',
	    				substr($data, $last + 4)
	    		);
	    		$offset = $endOfCentralDirectory['CentralDirectoryOffset'];
	    	}
	    
	    	// Get details from central directory structure.
	    	$fhStart = strpos($data, $this->_ctrlDirHeader, $offset);
	    	$dataLength = strlen($data);
	    
	    	do
	    	{
	    		if ($dataLength < $fhStart + 31)
	    		{
	    			UniteFunctionsUC::throwError('Invalid Zip Data');
	    		}
	    
	    		$info = unpack('vMethod/VTime/VCRC32/VCompressed/VUncompressed/vLength', substr($data, $fhStart + 10, 20));
	    		$name = substr($data, $fhStart + 46, $info['Length']);
	    
	    		$entries[$name] = array(
	    				'attr' => null,
	    				'crc' => sprintf("%08s", dechex($info['CRC32'])),
	    				'csize' => $info['Compressed'],
	    				'date' => null,
	    				'_dataStart' => null,
	    				'name' => $name,
	    				'method' => $this->_methods[$info['Method']],
	    				'_method' => $info['Method'],
	    				'size' => $info['Uncompressed'],
	    				'type' => null
	    		);
	    
	    		$entries[$name]['date'] = mktime(
	    				(($info['Time'] >> 11) & 0x1f),
	    				(($info['Time'] >> 5) & 0x3f),
	    				(($info['Time'] << 1) & 0x3e),
	    				(($info['Time'] >> 21) & 0x07),
	    				(($info['Time'] >> 16) & 0x1f),
	    				((($info['Time'] >> 25) & 0x7f) + 1980)
	    		);
	    
	    		if ($dataLength < $fhStart + 43)
	    		{
	    			UniteFunctionsUC::throwError('Invalid Zip Data');
	    			
	    		}
	    
	    		$info = unpack('vInternal/VExternal/VOffset', substr($data, $fhStart + 36, 10));
	    
	    		$entries[$name]['type'] = ($info['Internal'] & 0x01) ? 'text' : 'binary';
	    		$entries[$name]['attr'] = (($info['External'] & 0x10) ? 'D' : '-') . (($info['External'] & 0x20) ? 'A' : '-')
	    		. (($info['External'] & 0x03) ? 'S' : '-') . (($info['External'] & 0x02) ? 'H' : '-') . (($info['External'] & 0x01) ? 'R' : '-');
	    		$entries[$name]['offset'] = $info['Offset'];
	    
	    		// Get details from local file header since we have the offset
	    		$lfhStart = strpos($data, $this->_fileHeader, $entries[$name]['offset']);
	    
	    		if ($dataLength < $lfhStart + 34)
	    		{
	    			UniteFunctionsUC::throwError('Invalid Zip Data');
	    			
	    		}
	    
	    		$info = unpack('vMethod/VTime/VCRC32/VCompressed/VUncompressed/vLength/vExtraLength', substr($data, $lfhStart + 8, 25));
	    		$name = substr($data, $lfhStart + 30, $info['Length']);
	    		$entries[$name]['_dataStart'] = $lfhStart + 30 + $info['Length'] + $info['ExtraLength'];
	    
	    		// Bump the max execution time because not using the built in php zip libs makes this process slow.
	    		@set_time_limit(ini_get('max_execution_time'));
	    	}
	    	while ((($fhStart = strpos($data, $this->_ctrlDirHeader, $fhStart + 46)) !== false));
	    
	    	$this->_metadata = array_values($entries);
	    
	    	return true;
	    }
	    
	    
	    /**
	     * 
	     * get file data for extract
	     */
	    private function extract_custom_getFileData($key)
	    {
	    	if ($this->_metadata[$key]['_method'] == 0x8)
	    	{
	    		return gzinflate(substr($this->_data, $this->_metadata[$key]['_dataStart'], $this->_metadata[$key]['csize']));
	    	}
	    	elseif ($this->_metadata[$key]['_method'] == 0x0)
	    	{
	    		/* Files that aren't compressed. */
	    		return substr($this->_data, $this->_metadata[$key]['_dataStart'], $this->_metadata[$key]['csize']);
	    	}
	    	elseif ($this->_metadata[$key]['_method'] == 0x12)
	    	{
	    		// If bz2 extension is loaded use it
	    		if (extension_loaded('bz2'))
	    		{
	    			return bzdecompress(substr($this->_data, $this->_metadata[$key]['_dataStart'], $this->_metadata[$key]['csize']));
	    		}
	    	}
	    
	    	return '';
	    }
	    
	    
	    /**
	     * extract zip customely
	     */
	    protected function extract_custom($src, $dest){
	    	
	    	
	    	$this->_data = null;
	    	$this->_metadata = null;
	    	
	    	if (!extension_loaded('zlib'))
	    		UniteFunctionsUC::throwError('Zlib not supported, please enable in php.ini');
	    	
	    	$this->_data = file_get_contents($src);
	    	if(!$this->_data)
	    		UniteFunctionsUC::throwError('Get ZIP Data failed');
	    	
	    	$success = $this->extract_custom_readZipInfo($this->_data);
	    	if(!$success)
	    		UniteFunctionsUC::throwError('Get ZIP Information failed');
	    		
	    		
	    	for ($i = 0, $n = count($this->_metadata); $i < $n; $i++)
	    	{
	    		$lastPathCharacter = substr($this->_metadata[$i]['name'], -1, 1);
	    	
	    		if ($lastPathCharacter !== '/' && $lastPathCharacter !== '\\'){
	    			
	    			//write file
	    			
	    			$buffer = $this->extract_custom_getFileData($i);
	    			$destFilepath = UniteFunctionsUC::cleanPath($dest . '/' . $this->_metadata[$i]['name']);
	    			
	    			$this->writeFile($buffer, $destFilepath);
	    			
	    		}
	    	}
	    	
	    	
	    	return true;
	    		 
	    }
	    
	    
	    /**
	     * 
	     * Extract zip archive
	     */
	    public function extract($src, $dest){
			
	    	if($this->isZipArchiveExists() == true){				//zipArchive
				$success = $this->extract_zipArchive($src, $dest);
	    	}
			else if($this->isNativeSupportExists() == true){		//native
				
				$success = $this->extract_native($src, $dest);
				
			}else{													//custom
				
				$success = $this->extract_custom($src, $dest);
				
			}
			
			return($success);
	    }
	    
	    
	}

