<?php

defined('ADDON_LIBRARY_INC') or die;

class UniteCreatorLayoutPreviewProvider extends UniteCreatorLayoutPreview{

			
	/**
	 * constructor
	 */
	public function __construct(){

		$this->showHeader = true;
		
		parent::__construct();
				
		$this->display();
	}
	
}