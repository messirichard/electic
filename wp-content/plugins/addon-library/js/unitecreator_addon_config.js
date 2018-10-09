
function UniteCreatorAddonConfig(){
	
	var g_objWrapper, g_addonName, g_addonType; 
	var g_objSettingsContainer, g_objItemsWrapper;
	var g_objConfigTable;
	var g_objTitle, g_objSettings = new UniteSettingsUC();
	var g_objPreviewWrapper, g_objIframePreview, g_objManager = new UCManagerAdmin();
	var g_objInputUpdate = null;	//field for put settings values
	
	
	var t = this;
	
	if(!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();
	
	var g_temp = {
		isTabsView: false	
	};
	
	var g_options = {
			addon_id:"",
			title: "",
			url_icon: "",
			enable_items: false
	};
	
	this.events = {
			SHOW_PREVIEW: "show_preview",
			HIDE_PREVIEW: "hide_preview"
	}
	
	
	/**
	 * validate that addon exists
	 */
	function validateInited(){
		if(!g_addonName)
			throw new Error("Addon name not given");
	}
	
	
	/**
	 * show the addon config
	 */
	this.show = function(){
		g_objWrapper.show();
	}
	
	
	/**
	 * hide the addon config
	 */
	this.hide = function(){
		g_objWrapper.hide();
		
		triggerEvent(t.events.HIDE_PREVIEW);
	}
	
		
	/**
	 * get data object
	 */
	this.getObjData = function(){
		
		validateInited();
		
		var objValues = g_objSettings.getSettingsValues();
		
		var objData = {};
		objData["name"] = g_addonName;
		objData["addontype"] = g_addonType;
		objData["title"] = g_options.title;
		objData["url_icon"] = g_options.url_icon;
		objData["config"] = objValues;
		objData["items"] = "";
		
		if(g_options.enable_items == true)
			objData["items"] = g_objManager.getItemsData();
		
		return(objData);
	}
	
	
	/**
	 * get addon ID
	 */
	this.getAddonID = function(){
		
		
		return(g_options.addon_id);
	}
	
	
	/**
	 * get json data from the settings
	 */
	function getJsonData(){
		
		var objData = t.getObjData();
		
		var strData = JSON.stringify(objData);
		
		return(strData);
	}

	
	/**
	 * update values field if exists
	 */
	function updateValuesInput(){

		if(!g_objInputUpdate)
			return(false);
		
		if(!g_addonName)
			throw new Error("Addon name should be exists");
		
		var strData = getJsonData();
		
		g_objInputUpdate.val(strData);
	}
	
		
	
	/**
	 * set update input ID, this function should be run before init
	 */
	this.setInputUpdate = function(objInput){
		g_objInputUpdate = objInput;
	}
	
	
	/**
	 * parse options from input
	 */
	function parseInputOptions(optionsInput){
		
		jQuery.each(optionsInput, function(key, value){
			
			if(g_options.hasOwnProperty(key)){
				if(value === "true")
					value = true;
				else
				if(value === "false")
					value = false;
				
				g_options[key] = value;
			}
						
		});

	}
	
	/**
	 * clear addon configuration to default
	 */
	this.clearData = function(){
		validateInited();
		g_objSettings.clearSettings();
		
		if(g_options.enable_items == true){
			g_objManager.clearItemsPanel();
		}
		
	}
	
	
	/**
	 * set addon config
	 */
	this.setData = function(settingsData, itemsData){
		validateInited();
		g_objSettings.setValues(settingsData);
		
		if(g_options.enable_items == true){
			g_objManager.setItemsFromData(itemsData);
		}
		
	}

	
	/**
	 * get ajax preview url
	 */
	function getPreviewUrl(){
		
		var jsonData = getJsonData();
		jsonData = encodeURIComponent(jsonData);
		var params = "data="+jsonData+"";
		var urlPreview = g_ucAdmin.getUrlAjax("show_preview", params);
		
		return(urlPreview);
	}
	
	/**
	 * validate that preview exists
	 */
	function validatePreviewExists(){
	
		if(!g_objPreviewWrapper)
			throw new Error("The preview container not exists");
		
	}
	
	
	/**
	 * show preview
	 */
	this.showPreview = function(){
		
		validatePreviewExists();
		
		g_objConfigTable.hide();
		g_objPreviewWrapper.show();
		
		var urlPreview = getPreviewUrl();
		g_objIframePreview.attr("src", urlPreview);
		
		triggerEvent(t.events.SHOW_PREVIEW);
	}

	
	/**
	 * hide the preview
	 */
	this.hidePreview = function(){
		g_objIframePreview.attr("src", "");
		g_objPreviewWrapper.hide();

		g_objConfigTable.show();
		
		triggerEvent(t.events.HIDE_PREVIEW);
	}
	
	
	/**
	 * show preview in new tab
	 */
	this.showPreviewNewTab = function(){
		
		var urlPreview = getPreviewUrl();
		window.open(urlPreview);
		
	}
	
	
	/**
	 * init preview button
	 */
	function initPreview(){
		
		g_objPreviewWrapper = g_objWrapper.find(".uc-addon-config-preview");
		if(g_objPreviewWrapper.length == 0){
			g_objPreviewWrapper = null;
			return(false);
		}
		
		g_objIframePreview = g_objPreviewWrapper.find(".uc-preview-iframe");
		
		
	}
	
	
	function ______________EVENTS____________(){};
	
	/**
	 * on settings change event. 
	 * Update field if exists
	 */
	function onSettingsChange(){
		
		if(g_objInputUpdate)
			updateValuesInput();
	}
	
	
	/**
	 * grigger event
	 */
	function triggerEvent(eventName, options){
		
		g_objWrapper.trigger(eventName, options);
	
	}
	
	
	/**
	 * on some event
	 */
	function onEvent(eventName, func){
		
		g_objWrapper.on(eventName, func);
		
	}
	
	
	/**
	 * set on show preview function
	 */
	this.onShowPreview = function(func){
		
		onEvent(t.events.SHOW_PREVIEW, func);
	
	}
	
	
	/**
	 * set on hide preview function
	 */
	this.onHidePreview = function(func){
		
		onEvent(t.events.HIDE_PREVIEW, func);
		
	}
	
	
	/**
	 * init events
	 */
	function initEvens(){
		
		g_objSettings.setEventOnChange(onSettingsChange);
		
	}
	
		
	/**
	 * destroy object
	 */
	this.destroy = function(){
		
		if(!g_objWrapper || g_objWrapper.length == 0)
			return(false);
		
		g_objSettings.destroy();
		
		if(g_objManager)
			g_objManager.destroy();
		
		if(g_temp.isTabsView == true)
			jQuery("#uc_addon_config_tabs .uc-addon-config-tab").off("click");
		
		g_objWrapper.html("");
		g_objWrapper = null;
	}
	
	
	/**
	 * init tabs view
	 */
	function initTabsView(){
		
		var tabsWrapper = jQuery("#uc_addon_config_tabs");
		
		if(tabsWrapper.length == false){
			g_temp.isTabsView = false;
			return(false);
		}
		
		tabsWrapper.find(".uc-addon-config-tab").click(function(){
			
			var classTabSelected = "uc-tab-selected";
			
			var objTab = jQuery(this);
			if(objTab.hasClass(classTabSelected))
				return(false);
			
			var tabName = objTab.data("name");
			var idContent = "uc_addon_config_tab_"+tabName;
			var currentContent = jQuery("#"+idContent);
			
			g_objWrapper.find(".uc-addon-config-tab-content").not(currentContent).hide();
			currentContent.show();
			
			tabsWrapper.find(".uc-addon-config-tab").not(objTab).removeClass(classTabSelected);
			
			objTab.addClass(classTabSelected);
			
		});
		
	}
	
	
	/**
	 * 
	 * @param objWrapper
	 */
	this.init = function(objWrapper, isPreviewMode){
		
		if(g_objWrapper)
			throw new Error("the config is alrady inited, can't init it twice");
		
		g_objWrapper = objWrapper;
		
		if(g_objWrapper.length == 0)
			throw new Error("wrong config object");
				
		g_objSettingsContainer = g_objWrapper.find(".uc-addon-config-settings");
		g_objItemsWrapper = g_objWrapper.find(".uc-addon-config-items");
		g_objTitle = g_objWrapper.find(".uc-addon-config-title");
		g_objConfigTable = g_objWrapper.find(".uc-addon-config-table");
		
		//get name
		g_addonName = g_objWrapper.data("name");
		g_addonType = g_objWrapper.data("addontype");
		
		g_ucAdmin.validateNotEmpty(g_addonName, "addon admin");
		
		//get options
		var objOptions = g_objWrapper.data("options");
		parseInputOptions(objOptions);
		
				//set settings events
		g_objSettings.init(g_objSettingsContainer);
		
		initEvens();
		
		//init manager
		if(g_options.enable_items == true)
			g_objManager.initManager();
		else
			g_objManager = null;
		
		initPreview();
		
		var view = g_objWrapper.data("view");
		if(view == "tabs"){
			g_temp.isTabsView = true;
			initTabsView();
		}
		
	}
	
	
	
}