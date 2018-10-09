
/**
 * browser object
 */
function UniteCreatorBrowser(){
	
	var g_objWrapper, g_objTabsWrapper, g_objContentWrapper, g_objBackButton;
	var g_objConfig = new UniteCreatorAddonConfig(), g_objLoader, g_objDropdown;
	var g_objConfigWrapper;
	var g_objCache = {};
	
	
	if(!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();
	
	
	//temp vars
	var g_temp = {
			isBrowserMode: true,
			loadedAddonName: null,
			isCustomBackButton: false,
			isDropdownTabType: false,
			addonType: ""
	}
	
	var g_options = {
		startWidthAddon: false,
		startAddonName:null
	};
	
	
	var t = this;
	
	/**
	 * return if tab selected or not
	 */
	function isTabSelected(objTab){
		if(objTab.hasClass("uc-tab-selected"))
			return(true);
		
		return(false);
	}
	
	
	/**
	 * select some tab
	 */
	function selectTab(objTab){
		
		var objOtherTabs = getObjTabs(objTab);
		
		objOtherTabs.removeClass("uc-tab-selected");
		objTab.addClass("uc-tab-selected");
		
		//show content, hide others
		var catID = objTab.data("catid");
			
		showContentCategory(catID);
	}
	
	
	/**
	 * show content category
	 */
	function showContentCategory(catID){
		
		var objContent = jQuery("#uc_browser_content_"+catID);
		g_objWrapper.find(".uc-browser-content").not(objContent).hide();
		objContent.show();
	}
	
	
	/**
	 * on tab click function
	 */
	function onTabClick(){
		var objTab = jQuery(this);
		if(isTabSelected(objTab))
			return(true);
		
		selectTab(objTab);
		
	}
	
	/**
	 * get obj all tabs without some tab
	 */
	function getObjTabs(objWithout){
		var objTabs = g_objWrapper.find(".uc-browser-tabs-wrapper .uc-browser-tab");
		
		if(objWithout)
			objTabs = objTabs.not(objWithout);
		
		return(objTabs);
	}
	
	
	/**
	 * hide browser
	 */
	function hideBrowser(){
		g_objTabsWrapper.hide();
		g_objContentWrapper.hide();
		
		if(g_objBackButton)
			g_objBackButton.show();
		
		g_temp.isBrowserMode = false;
	}
	
	
	/**
	 * show browser
	 */
	function showBrowser(){
		g_objTabsWrapper.show();
		g_objContentWrapper.show();
		
		if(g_objBackButton)
			g_objBackButton.hide();
		
		g_objConfigWrapper.hide();
		
		g_temp.isBrowserMode = true;
	}
	
	
	/**
	 * cache addon
	 */
	function cacheAddonData(name, addonData){
		g_objCache[name] = addonData;
	}
	
	
	/**
	 * get cached addon settings
	 */
	function getCachedAddonData(name){
		
		if(g_objCache.hasOwnProperty(name) == false)
			return(null);
		
		return(g_objCache[name]);
	}
	
	
	/**
	 * load addon config
	 */
	function loadAddonConfig(addonData){
		
		g_objConfig.destroy();
		g_objConfig = new UniteCreatorAddonConfig();
		
		g_objConfigWrapper.html("");
		
		g_objLoader.show();
		
		//get from ajax
		g_objLoader.show();
		
		if(typeof addonData != "object")
			throw new Error("The addon data should be an object");
		
		g_ucAdmin.setAjaxLoaderID("uc_browser_loader");
		g_ucAdmin.setErrorMessageID("uc_browser_error");
				
		addonData.addontype = g_temp.addonType;
		
		g_ucAdmin.ajaxRequest("get_addon_config_html", addonData, function(response){
			
			g_objLoader.hide();
			g_objConfigWrapper.html(response.html);
			
			var objConfigDiv = g_objConfigWrapper.find(".uc-addon-config");
			
			g_objConfig.init(objConfigDiv);
			
			g_temp.loadedAddonName = addonData.name;
		});
		
	}
	
	
	/**
	 * on addon click
	 */
	function onAddonClick(){
		
		var objAddon = jQuery(this);
		var addonName = objAddon.data("name");
		
		t.setAddonMode(addonName);
	}
	
	
	/**
	 * init tabs
	 */
	function initTabs(){
		
		var objTabs = getObjTabs();
		
		objTabs.click(onTabClick);
		
	}
	
	
	/**
	 * on dropdown change
	 */
	function onDropdownChange(){
		
		var catID = g_objDropdown.val();
		
		showContentCategory(catID);
	}
	
	
	/**
	 * init dropdown
	 */
	function initDropdown(){
		
		g_objDropdown.change(onDropdownChange);
				
	}
	
	/**
	 * on back button click
	 */
	function onBackButtonClick(){
				
		showBrowser();
	}
	
	
	/**
	 * init events
	 */
	function initEvents(){
		
		g_objWrapper.find(".uc-browser-addon").click(onAddonClick);
		
		if(g_objBackButton)
			g_objBackButton.click(onBackButtonClick);
	}
	
	
	/**
	 * init config object
	 */
	function initConfig(){
		
		//input for update settings ID's
		var inputIDForUpdate = g_objWrapper.data("inputupdate");
		if(inputIDForUpdate){
			var objInput = jQuery("#"+inputIDForUpdate);
			if(objInput.length == 0)
				trace("error - input "+inputIDForUpdate+"not found");
			else
				g_objConfig.setInputUpdate(objInput);
		}
		
		//set start addon name in case that avilable
		if(g_options.startWidthAddon == true){
			var objConfigWrapper = g_objWrapper.find(".uc-addon-config");
			
			g_ucAdmin.validateDomElement(objConfigWrapper," config wrapper");
			
			if(objConfigWrapper.length == 0){
				g_objConfig = null;
				return(false);
			}
			
			g_objConfig.setStartAddon(g_options.startAddonName);
			g_objConfig.init(objConfigWrapper);
			
		}
	}
	
	
	/**
	 * get current addon data
	 */
	this.getCurrentAddonData = function(){
		
		if(g_temp.isBrowserMode == true)
			return(false);
		
		var data = g_objConfig.getObjData();
		
		return(data);
	}
	
	
	/**
	 * set start addon from data. 
	 * the data can be a name too
	 */
	this.setAddonMode = function(addonData, forceLoad){
		
		g_ucAdmin.validateNotEmpty(addonData, "addon data in browser.setAddonMode");
		
		if(forceLoad === true)
			g_temp.loadedAddonName = null;
		
		if(typeof addonData == "string")
			var addonData = {name: addonData}
		
		hideBrowser();
		g_objConfigWrapper.show();
		
		var addonName = addonData.name;
		
		if(addonName != g_temp.loadedAddonName)
			loadAddonConfig(addonData);
		
	}
	
	
	/**
	 * open browser without data
	 */
	this.setBrowserMode = function(){
		g_temp.loadedAddonName = null;
		showBrowser();
	}
	
	
	/**
	 * set back button object
	 */
	this.setObjBackButton = function(objButton){
		
		//remove old button
		if(g_objBackButton){
			var isVisible = g_objBackButton.is(":visible");
			g_objBackButton.remove();
			
			g_objBackButton = objButton;
			g_objBackButton.click(onBackButtonClick);
			
			if(isVisible == true)
				g_objBackButton.show();
			else
				g_objBackButton.hide();
			
		}else{
			g_objBackButton = objButton;
			g_objBackButton.click(onBackButtonClick);
		}
		
		g_temp.isCustomBackButton = true;
			
	}
	
	/**
	 * on browser show
	 */
	this.onShowBrowser = function(){
		
		//set custom button event
		if(g_temp.isCustomBackButton == true)
			g_objBackButton.click(onBackButtonClick);
		
	}
	
	
	/**
	 * init browser object
	 */
	this.init = function(objWrapper){
		
		g_objWrapper = objWrapper;
		
		g_objTabsWrapper = objWrapper.find(".uc-browser-tabs-wrapper");
		g_objContentWrapper = objWrapper.find(".uc-browser-content-wrapper");
		
		g_objBackButton = objWrapper.find(".uc-browser-button-back");
		if(g_objBackButton.length == 0)
			g_objBackButton = null;
		
		g_objConfigWrapper = objWrapper.find(".uc-browser-addon-config-wrapper");
		g_objLoader = g_objWrapper.find(".uc-browser-loader");
		
		g_ucAdmin.validateDomElement(g_objConfigWrapper,"config wrapper");
		g_ucAdmin.validateDomElement(g_objLoader,"loader");
		
		//set mode
		var objTabsWrapper = g_objWrapper.find(".uc-browser-tabs-wrapper");
		
		var tabsType = objTabsWrapper.data("tabstype");
		g_temp.addonType = g_objWrapper.data("addontype");
		
		
		if(tabsType == "dropdown"){
			g_temp.isDropdownTabType = true;
			g_objDropdown = objWrapper.find(".uc-browser-select-category");
			g_ucAdmin.validateDomElement(g_objDropdown, "loader");
		}else
			g_objDropdown = null;
		
		//set start addon name
		var startAddonName = g_objWrapper.data("startaddon");
		g_temp.isBrowserMode = true;
		
		if(startAddonName){
			g_options.startWidthAddon = true;
			g_options.startAddonName = startAddonName;
			g_temp.isBrowserMode = false;
			initConfig();
		}
		
		
		//init config 
		if(g_temp.isDropdownTabType == false)
			initTabs();
		else
			initDropdown();
		
		initEvents();
	}
	
	
}



