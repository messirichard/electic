function UniteCreatorParamsPanel(){
	var g_objWrapper, g_prefix = "", g_type, g_arrConstants = {};
	var g_objFiltersWrapper, g_activeFilter = null;
	
	var t = this;
	var g_temp = {
			funcOnClick: function(){}
	}
	
	var events = {
			DELETE_VARIABLE: "delete_variable",
			EDIT_VARIABLE: "edit_variable"
	}
	
	if(!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();
	
	
	/**
	 * validate that the panel is inited
	 */
	function validateInited(){
		if(!g_objWrapper)
			throw new Error("The panel is not inited");
	}

	

	/**
	 * add image base params
	 */
	function addImageBaseParams(objParam, filter){
		
		var arrParams = ["image","thumb","description","enable_link","link"];
		
		jQuery.each(arrParams, function(index, name){
			addParam(name, null, filter);
		});
		
	}
	
	
	/**
	 * add textare param fields
	 */
	function addTextareaParam(objParam, filter){
		
		var name = objParam.name;
		
		addParam(name, null, filter);
		addParam(name+"|raw", null, filter);
		
	}
	
	
	/**
	 * get prefix by fitler
	 */
	function getPrefix(filter){
		
		if(typeof g_prefix == "string")
			return(g_prefix);
		
		if(!filter || typeof g_prefix != "object")
			return("");
		
		if(g_prefix.hasOwnProperty(filter) == false)
			return("");
		
		var prefix = g_prefix[filter];
		
		return(prefix);
	}
	
	
	/**
	 * get fitler class
	 */
	function getFilterClass(filter, addDot){
		var classFilter = "";
		
		var prefix = "";
		if(addDot === true)
			prefix = ".";
		
		if(filter)
			classFilter = prefix+"uc-filter-"+filter;
		
		return(classFilter);
	}
	
	
	/**
	 * add image param
	 */
	function addImageParam(objParam, filter){
		
		var isAddThumb = g_ucAdmin.getVal(objParam, "add_thumb", false, g_ucAdmin.getvalopt.FORCE_BOOLEAN);
		var isAddThumbLarge = g_ucAdmin.getVal(objParam, "add_thumb_large", false, g_ucAdmin.getvalopt.FORCE_BOOLEAN);
		
		var name = objParam.name;
		
		addParam(name, null, filter);
		
		if(isAddThumb)
			addParam(name+"_thumb", null, filter);
		
		if(isAddThumbLarge)
			addParam(name+"_thumb_large", null, filter);
		
		
	}
	
	
	/**
	 * add param to panel
	 * can accept name:string, type:string or object
	 */
	function addParam(objParam, type, filter){
		
		if(typeof objParam == "string"){
			objParam = {
				name: objParam,
				type: "uc_textfield"
			};
			if(type)
				objParam.type = type;
		}
		
		
		switch(objParam.type){
			case "uc_imagebase":
				addImageBaseParams(objParam, filter);
				return(false);
			break;
			case "uc_textarea":
				addTextareaParam(objParam, filter);
				return(false);
			break;
			case "uc_image":
				addImageParam(objParam, filter);
				return(false);
			break;
		}
		
		//get param type
		var paramType = "uc-type-param";
		switch(objParam.type){
			case "uc_function":
				paramType = "uc-type-function";
			break;
			case "uc_constant":
				paramType = "uc-type-constant";
			break;
		}
		
		//set filter class
		var classFilter = getFilterClass(filter);
		
		var name = objParam.name;
		var prefix = getPrefix(filter);
		
		var text = "{{"+prefix+name+"}}";
		
		var htmlClass = "uc-link-paramkey " + paramType +" " + classFilter;
		
		//check if hidden by filter
		var style = "";
		if(g_activeFilter && filter && g_activeFilter !== filter)
			style = "style='display:none'";
		
		var html = "<a data-name='"+name+"' data-text='"+text+"' href='javascript:void(0)' class='"+htmlClass+"' "+style+">"+text+"</a>";
		
		g_objWrapper.append(html);
	}
	
	
	/**
	 * add param to panel
	 */
	function addVariable(index, objVar, filter){
		
		if(typeof objVar != "object")
			throw new Error("The variable should be object");
		
		var name = objVar.name;
		var prefix = getPrefix(filter);
		var text = "{{"+prefix+name+"}}";
		
		//set class
		var classFilter = getFilterClass(filter);
		var htmlClass = "uc-link-paramkey uc-type-variable "+classFilter;

		var style = "";
		if(g_activeFilter && filter && g_activeFilter !== filter)
			style = "style='display:none'";

		
		var html = "<div class='uc-variable-wrapper' data-name='"+name+"' data-index='"+index+"'>";
		html += "<a data-name='"+name+"' data-text='"+text+"' href='javascript:void(0)' class='"+htmlClass+"' "+style+">"+text+"</a>";
		html += "<div class='uc-icons-wrapper'>";
		html += "<div class='uc-icon-edit'></div>";
		html += "<div class='uc-icon-delete'></div>";
		html += "</div>";
		html += "</div>";
		
		g_objWrapper.append(html);
	}
	
	
	/**
	 * on param click
	 */
	function onParamClick(){
		var objParam = jQuery(this);
		var text = objParam.data("text");
		
		g_temp.funcOnClick(text);
	}
	
	
	
	/**
	 * trigger event
	 */
	function triggerEvent(eventName, params){
		if(!params)
			var params = null;
		
		g_objWrapper.trigger(eventName, params);
	}
	
	
	/**
	 * on event name
	 */
	function onEvent(eventName, func){
		g_objWrapper.on(eventName,func);
	}
	
	
	/**
	 * init events
	 */
	function initEvents(){
		
		g_objWrapper.delegate("a.uc-link-paramkey", "click", onParamClick);

		g_objWrapper.delegate("a.uc-link-paramkey", "focus", function(){
			this.blur();
		});
		
		//show, hide icons panel
		
		g_objWrapper.delegate(".uc-variable-wrapper", "mouseenter", function(){
			jQuery(this).addClass("uc-hover");
		});
		
		g_objWrapper.delegate(".uc-variable-wrapper", "mouseleave", function(){
			jQuery(this).removeClass("uc-hover");
		});
		
		
		g_objWrapper.delegate(".uc-variable-wrapper .uc-icon-edit","click",function(){
			
			var objLink = jQuery(this);
			var objVarWrapper = objLink.parents(".uc-variable-wrapper");
			
			var varIndex = objVarWrapper.data("index");
			
			triggerEvent(events.EDIT_VARIABLE, varIndex);
		
		});
		
		
		g_objWrapper.delegate(".uc-variable-wrapper .uc-icon-delete","click",function(){
			
			var objLink = jQuery(this);
			var objVarWrapper = objLink.parents(".uc-variable-wrapper");
			var varIndex = objVarWrapper.data("index");
			
			triggerEvent(events.DELETE_VARIABLE, varIndex);
			
		});
		
		
		
	}
	
	
	/**
	 * remove all params
	 */
	this.removeAllParams = function(){
		g_objWrapper.html("");
	}
	
	
	/**
	 * add constant params as prefix
	 */
	function addConstants(argFilter){
		
		if(!g_arrConstants)
			return(false);
		
		if(typeof g_arrConstants != "object")
			return(false);
		
		if(g_arrConstants.length == 0)
			return(false);
		
		jQuery.each(g_arrConstants, function(filter, name){
			
			if(argFilter && filter != argFilter)
				return(true);
				
			var arrConstants = g_arrConstants[filter];
			
			jQuery.map(arrConstants,function(name){
				addParam(name, "uc_constant", filter);
			});
			
		});
		
	}
	
	function ___________FILTERS___________(){}
	
	/**
	 * activate all filter tabs
	 */
	function onFilterTabClick(){
		var activeClass = "uc-filter-active";
		
		var objFilter = jQuery(this);
		if(objFilter.hasClass(activeClass))
			return(false);
		
		var otherFitlers = g_objFiltersWrapper.find("a").not(objFilter);
		otherFitlers.removeClass(activeClass);
		
		objFilter.addClass(activeClass);
		
		g_activeFilter = objFilter.data("filter");
		
		//hide, show filters
		var classFilter = getFilterClass(g_activeFilter, true);
		
		var objFilterKeys = g_objWrapper.find(classFilter);
		var objOtherKeys = g_objWrapper.find("a").not(objFilterKeys);
		
		objOtherKeys.hide();
		objFilterKeys.show();
		
	}
	
	
	/**
	 * init filter tabs
	 */
	function initFilterTabs(){
		
		var objFilterWrapper = g_objWrapper.siblings(".uc-params-panel-filters");
		
		if(objFilterWrapper.length == 0)
			return(false);
		
		g_objFiltersWrapper = objFilterWrapper;
		
		
		//set active filter
		
		var objActiveFilter = g_objFiltersWrapper.find("a.uc-filter-active");
		if(objActiveFilter.length == 0)
			throw new Error("Must have at least one active filter!!!");
		
		g_activeFilter = objActiveFilter.data("filter");
		
		//set events
		g_objFiltersWrapper.delegate("a", "click", onFilterTabClick);
	}
	
	
	/**
	 * replace all params
	 */
	this.setParams = function(arrParams, arrVariables, filter){
		
		if(!filter)
			t.removeAllParams();
		
		//add constants
		addConstants(filter);
		
		//add params
		jQuery.each(arrParams, function(index, param){
			addParam(param, null, filter);
		});
		
		//add variables
		if(arrVariables && typeof arrVariables == "object"){
			
			jQuery.each(arrVariables, function(index, objVar){
				addVariable(index, objVar, filter);
			});
			
		}
			
	}
	
	
	/**
	 * on param click
	 */
	this.onParamClick = function(func){
		g_temp.funcOnClick = func;
	}
	
	
	/**
	 * on edit variable
	 */
	this.onEditVariable = function(func){
		onEvent(events.EDIT_VARIABLE, func);
	}
	
	
	/**
	 * on delete variable function
	 */
	this.onDeleteVariable = function(func){
		onEvent(events.DELETE_VARIABLE, func);
	}
	
	
	/**
	 * init the panel
	 */
	this.init = function(objWrapper, type, prefix, arrConstants){
		g_objWrapper = objWrapper;
		
		g_type = type;
		
		if(prefix)
			g_prefix = prefix;
		
		initFilterTabs();
		
		if(arrConstants && typeof arrConstants == "object")
			t.initConstants(arrConstants, "all");
		
		initEvents();
	}
	
	
	/**
	 * init consants
	 */
	this.initConstants = function(arrConstants, filter){
		
		if(!arrConstants || typeof arrConstants != "object")
			return(false);
		
		if(!g_arrConstants)
			g_arrConstants = {};
		
		if(!filter)
			filter = "all";
		
		g_arrConstants[filter] = arrConstants;
		
	}
	
	
	
}