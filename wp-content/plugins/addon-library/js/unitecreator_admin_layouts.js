function UniteCreatorAdmin_LayoutsList(){
	
	var t = this;
	var g_providerAdmin = new UniteProviderAdminUC();
	var g_settingsGlobal, g_tableLayouts;
	
	
	if(!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();

		
	
	/**
	 * init global settings dialog
	 */
	function initGlobalSettingsDialog(){
		
		//init settings
		var settingsWrapper = jQuery("#uc_layout_general_settings");
		g_settingsGlobal = new UniteSettingsUC();
		g_settingsGlobal.init(settingsWrapper);
		
		
		//on open dialog click
		jQuery("#uc_layouts_global_settings").click(function(){
			
			var dialogOptions = {
					minWidth: 750
			};
			
			g_ucAdmin.openCommonDialog("#uc_dialog_layout_global_settings", null, dialogOptions);
			
		});
		
		jQuery("#uc_dialog_layout_global_settings_action").click(function(){
			
			var settingsData = g_settingsGlobal.getSettingsValues();
			var data = {
				settings_values: settingsData
			};
			
			g_ucAdmin.dialogAjaxRequest("uc_dialog_layout_global_settings", "update_global_layout_settings", data);
			
		});
		
	}
	
	
	/**
	 * on delete layout click
	 */
	function onDeleteClick(){
		var objButton = jQuery(this);
		var objLoader = objButton.siblings(".uc-loader-delete");
		
		var textDelete = g_tableLayouts.data("text-delete");
				
		if(confirm(textDelete) == false)
			return(false);
			
		objButton.hide();
		objLoader.show();
		
		var layoutID = objButton.data("layoutid");
		
		var data = {
				layout_id: layoutID
		};
		
		g_ucAdmin.ajaxRequest("delete_layout", data);
		
	}
	
	
	/**
	 * duplicating addon
	 */
	function onDuplicateClick(){
		var objButton = jQuery(this);
		var objLoader = objButton.siblings(".uc-loader-duplicate");
		
		objButton.hide();
		objLoader.show();
		
		var layoutID = objButton.data("layoutid");
		
		var data = {
				layout_id: layoutID
		};
		
		g_ucAdmin.ajaxRequest("duplicate_layout", data);
		
	}
	
	
	/**
	 * on export click
	 */
	this.onExportClick = function(){
		var objButton = jQuery(this);
		var layoutID = objButton.data("layoutid");
		
		var params = "id="+layoutID;
		var urlExport = g_ucAdmin.getUrlAjax("export_layout", params);
		
		location.href=urlExport;
		
	}
	
	
	function ___________IMPORT_DIALOG_____________(){}
	
	
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
	this.initImportLayoutDialog = function(){
		jQuery("#uc_button_import_layout").click(openImportLayoutDialog);
		
		jQuery("#uc_dialog_import_layouts_action").click(function(){
			
			var isOverwrite = jQuery("#dialog_import_layouts_file_overwrite").is(":checked");
	        var data = {overwrite_addons:isOverwrite};
	        
	        var objData = new FormData();
	        var jsonData = JSON.stringify(data);
	    	objData.append("data", jsonData);
	    	
	    	g_ucAdmin.addFormFilesToData("dialog_import_layouts_form", objData);
	    	
			g_ucAdmin.dialogAjaxRequest("uc_dialog_import_layouts", "import_layouts", objData);
	    	
			
		});
		
	}
	
	/**
	 * init view events
	 */
	function initEvents(){
		
		
		if(g_tableLayouts){
			
			g_tableLayouts.delegate(".button_delete", "click", onDeleteClick);
			g_tableLayouts.delegate(".button_duplicate", "click", onDuplicateClick);
			g_tableLayouts.delegate(".button_export", "click", t.onExportClick);
		
		}
		
	}
	
	/**
	 * objects list view
	 */
	this.initObjectsListView = function(){
		
		g_tableLayouts = jQuery("#uc_table_layouts");
		if(g_tableLayouts.length == 0)
			g_tableLayouts = null;
		
		//g_ucAdmin.validateDomElement(g_tableLayouts, "table layouts");
		
		initGlobalSettingsDialog();
		t.initImportLayoutDialog();
		
		initEvents();
		
	}
	
	
	
}