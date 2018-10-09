<?php

class UniteCreatorTemplateEngine{
	
	private $twig;
	private $arrTemplates = array();
	private $arrParams = null;
	private $arrItems = array();
	
	
	/**
	 * init twig
	 */
	public function __construct(){
	
	}
	
	/**
	 * return if some template exists
	 * @param $name
	 */
	private function isTemplateExists($name){
		
		$isExists = array_key_exists($name, $this->arrTemplates);
		
		return($isExists);
	}
	
	
	/**
	 * put items
	 */
	public function putItems($templateName = "item"){
				
		if(empty($this->arrItems))
		 	return(false);
		
		if($this->isTemplateExists($templateName) == false)
			return(false);
		
		foreach($this->arrItems as $itemParams){
			$params = array_merge($this->arrParams, $itemParams);
			
			$htmlItem = $this->twig->render($templateName, $params);
			echo $htmlItem."\n";
		}
		
	}
	
	
	/**
	 * put items 2
	 */
	public function putItems2(){
		$this->putItems("item2");
	}
	
	
	/**
	 * add extra functions to twig
	 */
	private function initTwig_addExtraFunctions(){
		
		//add extra functions
				
		$putItemsFunction = new Twig_SimpleFunction('put_items', array($this,"putItems"));
		$putItemsFunction2 = new Twig_SimpleFunction('put_items2', array($this,"putItems2"));
		
		$this->twig->addFunction($putItemsFunction);
		$this->twig->addFunction($putItemsFunction2);
		
	}
	
	
	/**
	 * init twig
	 */
	private function initTwig(){
		
		if(empty($this->arrTemplates))
			UniteFunctionsUC::throwError("No templates found");
		
		$loader = new Twig_Loader_Array($this->arrTemplates);
		
		$arrOptions = array();
		$arrOptions["debug"] = true;
		
		$this->twig = new Twig_Environment($loader, $arrOptions);
		$this->twig->addExtension(new Twig_Extension_Debug());
		
		$this->initTwig_addExtraFunctions();
		
	}
	
	
	/**
	 * validate that not inited
	 */
	private function validateNotInited(){
		if(!empty($this->twig))
			UniteFunctionsUC::throwError("Can't add template or params when after rendered");
	}

	
	/**
	 * validate that all is inited
	 */
	private function validateInited(){
		if($this->arrParams === null)
			UniteFunctionsUC::throwError("Please set the params");
	}
	
	
	/**
	 * add template
	 */
	public function addTemplate($name, $html){
		$this->validateNotInited();
		if(isset($this->arrTemplates[$name]))
			UniteFunctionsUC::throwError("template with name: $name already exists");
		
		$this->arrTemplates[$name] = $html;
	}
	
	
	/**
	 * add params
	 */
	public function setParams($params){
		
		$this->arrParams = $params;
	}
	
	
	/**
	 * set items
	 * @param $arrItems
	 */
	public function setArrItems($arrItems){
		
		$this->arrItems = $arrItems;
	}
	
	
	/**
	 * get rendered html
	 * @param $name
	 */
	public function getRenderedHtml($name){
		
		UniteFunctionsUC::validateNotEmpty($name);
		$this->validateInited();
		if(array_key_exists($name, $this->arrTemplates) == false)
			UniteFunctionsUC::throwError("Template with name: $name not exists");
		
		if(empty($this->twig))
			$this->initTwig();
		
		$output = $this->twig->render($name, $this->arrParams);
		
		return($output);
	}
	
	
}