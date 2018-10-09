function UniteCreatorAdmin_Layout(){
	
	var t = this;
	var g_providerAdmin = new UniteProviderAdminUC();
	var g_gridBuilder, g_layoutID;
	
	
	if(!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();
	
	
	/**
	 * validate update data
	 */
	function validateUpdateData(data){
		
		try{
			
			//validate title
			var title = data.title;
			
			if(!title || jQuery.trim(title) == ""){
				jQuery("#uc_object_title").focus();
				throw new Error("Please fill the <b>Layout Title </b>");
			}
			
		}catch(error){
			
			var prefix = "Update Error: ";
			var message = prefix + error.message;
			jQuery("#uc_update_addon_error").show().html(message);
			
			return(false);
		}
		
		return(true);
	}
	
	
	/**
	 * on update layout button click
	 */
	function onUpdateClick(){
		
		var dataGrid = g_gridBuilder.getGridData();
		
		var jsonData = JSON.stringify(dataGrid);
		
		var strEncodedData = g_ucAdmin.encodeContent(jsonData);
				
		var title = jQuery("#uc_layout_title").val();
		
		var data = {
				layoutid: g_layoutID,
				title: title,
				grid_data: strEncodedData
		};
		
		var validateSuccess = validateUpdateData(data);
		if(validateSuccess == false)
			return(false);
		
		//var data
		g_ucAdmin.setAjaxLoaderID("uc_loader_update");
		g_ucAdmin.setSuccessMessageID("uc_message_addon_updated");
		g_ucAdmin.setAjaxHideButtonID("uc_button_update_layout");
		g_ucAdmin.setErrorMessageID("uc_update_addon_error");
		
		g_ucAdmin.ajaxRequest("update_layout", data);
	}
	
	
	/**
	 * export layout
	 */
	function exportLayoutClick(){
		
		var params = "id="+g_layoutID;
		var urlExport = g_ucAdmin.getUrlAjax("export_layout", params);
		location.href=urlExport;
	}
	
	
	/**
	 * init events
	 */
	function initEvents(){
		
		jQuery("#uc_button_update_layout").click(onUpdateClick);
		
		var objButtonExport = jQuery("#uc_button_export_layout");
		if(objButtonExport.length)
			objButtonExport.click(exportLayoutClick);
	}
	
	/**
	 * set shortcode
	 */
	function updateShortcode(){
		
		var objShortcode = jQuery("#uc_layout_shortcode");
		
		var titleText = jQuery("#uc_layout_title").val();
		titleText = g_ucAdmin.stripslashes(titleText);
		
		titleText = g_ucAdmin.escapeDoubleQuote(titleText);
		
		var wrappersType = objShortcode.data("wrappers");
		var shortcodeName = objShortcode.data("shortcode");
		
		var wrapperLeft = "{", wrapperRight = "}";
		
		if(wrappersType == "wp"){
			wrapperLeft = "[";
			wrapperRight = "]";
		}
		
		var shortcode = wrapperLeft+shortcodeName+" id="+g_layoutID+" title=\""+titleText+"\" " + wrapperRight;
		
		objShortcode.val(shortcode);
	}
	
	
	/**
	 * init shortcode
	 */
	function initShortcode(){
		
		var objShortcode = jQuery("#uc_layout_shortcode");
		if(objShortcode.length == 0)
			return(false);
				
		updateShortcode();
		
		jQuery("#uc_layout_title").change(updateShortcode);
	}
	
	
	/**
	 * open import layout dialog
	 */
	function openImportLayoutDialog(){
						
		jQuery("#dialog_import_layouts_file").val("");
		
		var options = {minWidth:700};
		
		g_ucAdmin.openCommonDialog("#uc_dialog_import_layouts", null, options);
		
	}
	
	
	/**
	 * init import layout dialog
	 */
	function initImportLayoutDialog(){
		
		var objButtonImport = jQuery("#uc_button_import_layout");
		
		if(objButtonImport.length == 0)
			return(false);
		
		if(!g_layoutID)
			return(false);
		
		
		objButtonImport.click(openImportLayoutDialog);
		
		jQuery("#uc_dialog_import_layouts_action").click(function(){
			
			var isOverwrite = jQuery("#dialog_import_layouts_file_overwrite").is(":checked");
	        var data = {overwrite_addons:isOverwrite};
	        
	        data.layoutID = g_layoutID;
	        
	        var objData = new FormData();
	        var jsonData = JSON.stringify(data);
	    	objData.append("data", jsonData);
	    	
	    	g_ucAdmin.addFormFilesToData("dialog_import_layouts_form", objData);
	    	
			g_ucAdmin.dialogAjaxRequest("uc_dialog_import_layouts", "import_layouts", objData);
	    	
			
		});
		
	}
	
	
	
	/**
	 * objects list view
	 */
	this.initLayoutView = function(){
		
		//get layout ID - if exists
		var objWrapper = jQuery("#uc_edit_layout_wrapper");
		if(objWrapper.length == 0)
			throw new Error("No edit layout wrapper found");
		
		g_layoutID = objWrapper.data("layoutid");
		if(!g_layoutID)
			g_layoutID = null;
		
		g_gridBuilder = new UniteCreatorGridBuilder();
		g_gridBuilder.init("#uc_grid_builder");
		
		initEvents();
		initShortcode();
		initImportLayoutDialog();
	}
	
	
	
	
}