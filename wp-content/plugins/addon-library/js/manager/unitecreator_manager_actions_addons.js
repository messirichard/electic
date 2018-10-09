function UCManagerActionsAddons(){
	
	var g_objCats = new UCManagerAdminCats(), g_objItems;
	var g_manager = new UCManagerAdmin();
	var g_options, g_addonsType = "", g_emptyAddonsWrapper;
	var g_objTooltip, g_objListWrapper,g_objWrapper;
	
	if(!g_ucAdmin){
		var g_ucAdmin = new UniteAdminUC();
	}
	
	
	/**
	 * run action function, return if found true/false
	 */
	function runActionFunctions(action,data){
		
		var arrActionFunctions = g_manager.getActionFunctions();
		if(!arrActionFunctions)
			return(false);
		
		if(arrActionFunctions.length == 0)
			return(false);
		
		jQuery.each(arrActionFunctions, function(index, func){
			if(typeof func != "function")
				throw new Error(func+" is not a function");
			
			var isFound = func(action, data);
			if(isFound == true)
				return(true);
		});
		
		return(false);
	}
	
	
	/**
	 * on item button click
	 */
	this.runItemAction = function(action, data){
		
		switch(action){
			case "add_addon":
				openAddAddonDialog();
			break;
			case "update_order":
				updateItemsOrder();
			break;
			case "select_all_items":
				g_objItems.selectUnselectAllItems();
			break;
			case "duplicate_item":
				duplicateItems();
			break;
			case "item_default_action":
			case "edit_addon":
				editAddon();
			break;
            case "edit_addon_blank":
            	editAddon(true);
            break;
			case "quick_edit":
				quickEdit();
			break;
			case "remove_item":
				removeSelectedAddons();				
			break;
			case "test_addon":
				testAddon();
			break;
			case "test_addon_blank":
				testAddon(true);
			break;
			case "move_items":
				moveAddons(data);
			break;
			case "copymove_move":
				onMoveOperationClick();
			break;
			case "get_cat_items":
		    	getSelectedCatAddons(data);	//data - catID
			break;
			case "import_addon":
				openImportAddonDialog();
			break;
			case "export_addon":
				exportAddon();
			break;
			case "activate_addons":
				activateAddons(true);
			break;
			case "deactivate_addons":
				activateAddons(false);
			break;
			case "export_cat_addons":
				var catID = data;
				exportCatAddons(catID);
			break;
			default:
				
				var isFound = runActionFunctions(action, data);
				if(isFound == false)
					trace("wrong addon action: " + action);
				
			break;
		}
		
	}
	
	/**
	 * copy / move items
	 */
	function moveAddons(data){
		
		//set status text
		var text = g_uctext.moving_addons;
		
		g_manager.ajaxRequestManager("move_addons",data , g_uctext.moving_addons, function(response){
			setHtmlListCombo(response);
		});
		
	}
	
	
	
	/**
	 * init items
	 */
	function initItems(){
		
		initAddAddonDialog();
			
		initQuickEditDialog();
		g_manager.initBottomOperations();
		
		initImportAddonDialog();
	
	}

	/**
	 * set combo lists from response
	 */
	function setHtmlListCombo(response){
		var htmlItems = response.htmlItems;
		var htmlCats = response.htmlCats;
		
		g_objItems.setHtmlListItems(htmlItems);
		
		if(g_objCats)
			g_objCats.setHtmlListCats(htmlCats);
	}

	
	/**
	 * make some copy/move operation and close the dialog
	 */
	function onMoveOperationClick(){
		
		var objDrag = g_objItems.getObjDrag();
		
		var data = {};
		data.targetCatID = objDrag.targetItemID;
		data.selectedCatID = g_objCats.getSelectedCatID();
		data.arrAddonIDs = objDrag.arrItemIDs;
		
		moveAddons(data);

		g_objItems.resetDragData();
	}
	
	
	function ___________ADDONS_DIALOGS________________(){}

	
	/**
	 * init add addon dialog actions
	 */
	function initAddAddonDialog(){

		jQuery("#dialog_add_addon_action").click(addAddon);
		
		jQuery("#dialog_add_addon_name").add("#dialog_add_addon_title").keyup(function(event){
			if(event.keyCode == 13)
				addAddon();
		});
		
	}

	
	/**
	 * init quick edit dialog
	 */
	function initQuickEditDialog(){
		
		// set update title onenter function
		jQuery("#dialog_quick_edit_title").add("#dialog_quick_edit_name").keyup(function(event){
			if(event.keyCode == 13)
				updateItemTitle();
		});
		
	}
	
	
	/**
	 * init import addon dialog
	 */
	function initImportAddonDialog(){
		
		jQuery("#dialog_import_addons_action").click(function(){
						
	        var data = {};
	        data.catid = jQuery("#dialog_import_addons").data("catid");
	        data.addontype = g_addonsType;
	        data.isoverwrite = jQuery("#dialog_import_check_overwrite").is(":checked");
	        
	        var objData = new FormData();
	        var jsonData = JSON.stringify(data);
	    	objData.append("data", jsonData);
	    	
	    	g_ucAdmin.addFormFilesToData("dialog_import_addons_form", objData);
	    	
	    	var params = {"noclose":true};

	    	jQuery("#dialog_import_addons_log").hide();
	    	
			g_ucAdmin.dialogAjaxRequest("dialog_import_addons", "import_addons", objData, function(response){
				
				setHtmlListCombo(response);
				
				//show log
				jQuery("#dialog_import_addons_log").show();
				jQuery("#dialog_import_addons_log_text").html(response.import_log);
				jQuery("#dialog_import_addons_file").val("");
				
			}, params);
	    	
			
		});
				
		
	}
	
	
	/**
	 * on add addon click - open add addon dialog
	 */
	function openAddAddonDialog(){
		
		jQuery(".dialog_addon_input").val("");
		
		g_ucAdmin.openCommonDialog("#dialog_add_addon", function(){
			jQuery("#dialog_add_addon_title").select();			
		});
		
	}
	
	
	/**
	 * open import addon dialog
	 */
	function openImportAddonDialog(){
		
		var catData = g_objCats.getSelectedCatData();
		
		var catID = catData.id;
		
		var isAutodetect = false;
		
		if(!catID || catID == 0 || catID == "all"){
			catID = "";
			var catName = jQuery("#dialog_import_catname").data("text-autodetect");
		}else{
			catName = catData.title;
		}
		
		jQuery("#dialog_import_addons").data("catid", catID);
		jQuery("#dialog_import_catname").html(catName);

		jQuery("#dialog_import_addons_log").hide();
		
		jQuery("#dialog_import_addons_file").val("");
		
		jQuery("#dialog_import_addons_action").show();
		
		var options = {minWidth:700};
		
		g_ucAdmin.openCommonDialog("#dialog_import_addons", null, options);
		
	}
	
	
	function ___________ADDONS_RELATED_OPERATIONS________________(){}	//sap for outline	

	
	
	/**
	 * on dialog add addon click
	 */
	function addAddon(){
		
		var selectedCatID = 0;
		
		if(g_objCats)
			selectedCatID = g_objCats.getSelectedCatID();
		
		var data = {
				title: jQuery("#dialog_add_addon_title").val(),
				name: jQuery("#dialog_add_addon_name").val(),
				description: jQuery("#dialog_add_addon_description").val(),
				catid: selectedCatID,
				addontype: g_addonsType
		};
		
		g_ucAdmin.dialogAjaxRequest("dialog_add_addon", "add_addon", data, function(response){

			var objItem = g_objItems.appendItem(response.htmlItem);
			
			//update categories list
			if(g_objCats)
				g_objCats.setHtmlListCats(response.htmlCats);
			
			g_objItems.selectSingleItem(objItem);
			
			//var urlAddon = response["url_addon"];
			//location.href = urlAddon;
			
		});
		
	}
	
	
	/**
	 * get item data from server
	 */
	function getItemData(itemID, callbackFunction){
		
		var data = {itemid:itemID};
		g_manager.ajaxRequestManager("get_item_data",data,g_uctext.loading_item_data,callbackFunction);
	}
	
	
	/**
	 * get category items
	 */
	function getSelectedCatAddons(){
		
		var catID = 0;
		
		if(g_objCats){
			catID = g_objCats.getSelectedCatID();
			if(catID == -1)
				return(false);
		}
		
		jQuery("#items_loader").show();
		jQuery("#uc_list_items").hide();
		g_objItems.hideNoAddonsText();
		
		var activeFilter = getFitlerActive();
		
		var data = {};
		data["catID"] = catID;
		data["filter_active"] = getFitlerActive();
		data["response_combo"] = true;
		data["addontype"] = g_addonsType;
				
		
		g_ucAdmin.ajaxRequest("get_cat_addons", data, function(response){
			
			setHtmlListCombo(response);
			
			g_objItems.checkSelectRelatedItems();
			
			//patch for showing no addons html if not items on all
			if(catID == "all" && activeFilter == "all" && g_emptyAddonsWrapper){
				var numItems = jQuery("#uc_list_items li").length;
				if(numItems == 0){
					jQuery("#no_items_text").hide();
					g_emptyAddonsWrapper.show();
					g_manager.updateGlobalHeight(null, 390);
				}
				
			}
			
		});
	}
	
	
	/**
	 * refresh categories list
	 */
	function refreshCatList(){
		
		if(!g_objCats)
			throw new Error("The categories don't exists");
		
		var selectedCatID = g_objCats.getSelectedCatID();
		var data = {};
		data["selected_catid"] = selectedCatID;
		data["filter_active"] = getFitlerActive();
		
		g_manager.ajaxRequestManager("get_catlist",data , "", function(response){
						
			var htmlCats = response.htmlCats;
			
			g_objCats.setHtmlListCats(htmlCats);
			
		});
		
	}
	
	/**
	 * remove items
	 */
	function removeAddons(arrIDs){
		
		var data = {};
		data.arrAddonsIDs = arrIDs;
		
		data.catid = 0;
		
		if(g_objCats)
			data.catid = g_objCats.getSelectedCatID();
		
		g_manager.ajaxRequestManager("remove_addons",data, g_uctext.removing_addons, function(response){
			setHtmlListCombo(response);
		});
		
	}
	
	
    /**
     * remove selected items
     */
    function removeSelectedAddons(){
		if(g_ucAdmin.isButtonEnabled(this) == false)
			return(false);
		
		if(confirm(g_uctext.confirm_remove_addons) == false)
			return(false);
		
		var arrIDs = g_objItems.getSelectedItemIDs();
		
		removeAddons(arrIDs);
    }


    /**
     * run addons view url
     */
    function runAddonsViewUrl(view, isNewWindow){

    	var itemID = g_objItems.getSelectedItemID();
		if(itemID == null)
			return(false);
		
		var urlViewEdit = g_ucAdmin.getUrlView(view, "id="+itemID);
		
		if(isNewWindow === true){
			window.open(urlViewEdit);
		}else{
			location.href = urlViewEdit;
		}
    	
    }
	
    
	/**
	 * edit item operation. open quick edit dialog
	 */
	function editAddon(isNewWindow){
		
		runAddonsViewUrl("addon", isNewWindow);
	}
	
	
	/**
	 * test addon
	 */
	function testAddon(isNewWindow){
		runAddonsViewUrl("testaddon", isNewWindow);
	}
	
	/**
	 * export selected addon
	 */
	function exportAddon(){
		var arrIDs = g_objItems.getSelectedItemIDs();
		
		if(arrIDs.length == 0)
			return(false);
		
		var addonID = arrIDs[0];
		
		var params = "id="+addonID;
		var urlExport = g_ucAdmin.getUrlAjax("export_addon", params);
		
		location.href=urlExport;
	}
	
	
	/**
	 * export category addons
	 */
	function exportCatAddons(catID){
		
		var params = "catid=" + catID;
		var urlExport = g_ucAdmin.getUrlAjax("export_cat_addons", params);
		
		location.href = urlExport;
	}
	
	
	/**
	 * edit item title function
	 */
	function quickEdit(){
		
		var arrIDs = g_objItems.getSelectedItemIDs();
		
		if(arrIDs.length == 0)
			return(false);
		
		var itemID = arrIDs[0];
		
		var objItem = g_objItems.getItemByID(itemID);
		if(objItem.length == 0)
			throw new Error("item not found: "+itemID);
		
		var title = objItem.data("title");
		var name = objItem.data("name");
		var description = objItem.data("description");
		
		var objDialog = jQuery("#dialog_edit_item_title");
		
		jQuery("#dialog_quick_edit_title").val(title).focus();
		jQuery("#dialog_quick_edit_name").val(name);
		jQuery("#dialog_quick_edit_description").val(description);
		
		var buttonOpts = {};
		
		buttonOpts[g_uctext.cancel] = function(){
			jQuery("#dialog_edit_item_title").dialog("close");
		};
		
		buttonOpts[g_uctext.update] = function(){
			updateItemTitle();
		}
		
		objDialog.data("itemid",itemID);
		
		objDialog.dialog({
			dialogClass:"unite-ui",			
			buttons:buttonOpts,
			minWidth:500,
			modal:true,
			open:function(){
				jQuery("#dialog_quick_edit_title").select();
			}
		});
		
	}
	
	
	/**
	 * update item title - on dialog update press
	 */
	function updateItemTitle(){
		
		var objDialog = jQuery("#dialog_edit_item_title");
		var itemID = objDialog.data("itemid");
		
		var objItem = g_objItems.getItemByID(itemID);
		if(objItem.length == 0)
			throw new Error("item not found: "+itemID);
		
		var titleHolder = objItem.find(".uc-item-title");
		var descHolder = objItem.find(".uc-item-description");
		
		var newTitle = jQuery("#dialog_quick_edit_title").val();
		var newName = jQuery("#dialog_quick_edit_name").val();
		var newDesc = jQuery("#dialog_quick_edit_description").val();
		
		var data = {
			itemID: itemID,
			title: newTitle,
			name: newName,
			description: newDesc
		};
		
		objDialog.dialog("close");
		
		//update the items
		objItem.data("title", newTitle);
		objItem.data("name", newName);
		objItem.data("description", newDesc);
		
		titleHolder.html(newTitle);
		
		var showDesc = "";
		if(newDesc)
			showDesc = newDesc;
		
		descHolder.html(showDesc);
		
		g_manager.ajaxRequestManager("update_addon_title",data,g_uctext.updating_addon_title);
	}
	
	
	/**
	 * duplicate items
	 */
	function duplicateItems(){
		
		var arrIDs = g_objItems.getSelectedItemIDs();
		if(arrIDs.length == 0)
			return(false);
		
		var selectedCatID = 0;
		
		if(g_objCats)
			selectedCatID = g_objCats.getSelectedCatID();
		
		if(selectedCatID == -1)
			return(false);
		
		var data = {
				arrIDs: arrIDs,
				catID: selectedCatID
		};
		
		g_manager.ajaxRequestManager("duplicate_addons",data,g_uctext.duplicating_addons,function(response){
			setHtmlListCombo(response);
		});	
	}
	
	
	/**
	 * update items order in server
	 */
	function updateItemsOrder(){
		
		var arrIDs = g_objItems.getArrItemIDs();
		
		var data = {addons_order:arrIDs};
		g_manager.ajaxRequestManager("update_addons_order",data,g_uctext.updating_addons_order);
	}
	
	
	/**
	 * activate selected addons
	 */
	function activateAddons(isActive){
		var arrIDs = g_objItems.getSelectedItemIDs();
		
		g_objItems.acivateSelectedItems(isActive, true);
		
		var data = {addons_ids:arrIDs,is_active:isActive};
		g_manager.ajaxRequestManager("update_addons_activation",data,g_uctext.updating_addons);
	}
	
	
	function ___________FILTERS________________(){}	
	
	
	/**
	 * get active / not active filter
	 */
	function getFitlerActive(){
		var objFilter = jQuery("#uc_filters_active a.uc-active");
		
		if(objFilter.length == 0)
			throw new Error("there must be active filter");
		
		var filter = objFilter.data("filter");
		
		return(filter);
	}
	
	
	/**
	 * init filters
	 */
	function initFilters(){
		
		//init active / not active filters
		
		jQuery("#uc_filters_active a").click(function(){
			var classActive = "uc-active";
			var objFilter = jQuery(this);
			if(objFilter.hasClass(classActive))
				return(true);
			
			var filter = objFilter.data("filter");
			
			jQuery("#uc_filters_active a").not(objFilter).removeClass(classActive);
			objFilter.addClass(classActive);
			
			
			//if no cats selected - refresh cats list
			
			var selectedCatID = g_objCats.getSelectedCatID();
			if(selectedCatID == -1)
				refreshCatList();
			else
				getSelectedCatAddons();
			
		});
		
	}
	
	function ___________TOOLTIP________________(){}	
	
	
	/**
	 * init tooltip
	 */
	function initTooltip(){
		g_objTooltip = jQuery("#uc_manager_addon_preview");
		if(g_objTooltip.length == 0)
			g_objTooltip = null;
		
	}
	
	
	/**
	 * check if the item has tooltip
	 */
	function checkShowTooltip(objItem){
		
		if(!g_objTooltip)
			return(false);
		
		//check if item has preview
		var urlPreview = objItem.data("preview");
		if(!urlPreview)
			return(false);
		
		//show tooltip
		g_objTooltip.show();
		
		var gapTop = 10;
		var gapLeft = 10;
		
		var itemWidth = objItem.width();
		var tooltipHeight = g_objTooltip.height();
		var tooltipWidth = g_objTooltip.width();
		
		var maxLeft = g_objWrapper.width() - tooltipWidth;
		
		var pos = g_objItems.getItemWrapperPos(objItem);
		pos.top = pos.top - tooltipHeight + gapTop;
		pos.left = pos.left + itemWidth - gapLeft;
		
		if(pos.left > maxLeft)
			pos.left = maxLeft;
		
		//set position and image
		
		var objCss = {top:pos.top+"px",left:pos.left+"px"};
		objCss["background-image"] = "url('"+urlPreview+"')";
		
		g_objTooltip.css(objCss);
								
	}
	
	
	/**
	 * hide tooltip if avilable
	 */
	function hideTooltip(){
		
		if(!g_objTooltip)
			return(false);
		
		var objCss = {};
		objCss["background-image"] = "";
		
		g_objTooltip.hide();
		g_objTooltip.css(objCss);
	}
	
	
	function ___________EVENTS________________(){}	
	
	
	/**
	 * on hide empty text, hide no addons message as well
	 */
	function onItemHideEmptyText(){
		if(g_emptyAddonsWrapper)
			g_emptyAddonsWrapper.hide();
	}
	
	
	
	/**
	 * on item mouseover
	 */
	function onItemMouseover(event, item){
		var objItem = jQuery(item);
		
		if(g_objTooltip)
			checkShowTooltip(objItem);
		
	}
	
	
	/**
	 * on item mouseover
	 */
	function onItemMouseout(objItem){
		
		if(g_objTooltip)
			hideTooltip();
		
	}
	
	
	/**
	 * init events
	 */
	function initEvents(){
		g_manager.onEvent(g_manager.events.ITEM_HIDE_EMPTY_TEXT, onItemHideEmptyText);
		g_manager.onEvent(g_manager.events.ITEM_MOUSEOVER, onItemMouseover);
		g_manager.onEvent(g_manager.events.ITEM_MOUSEOUT, onItemMouseout);
		
	}
	
	
	/**
	 * init the actions
	 */
	this.init = function(objManager){
		g_manager = objManager;
		
		//set addons type
		g_objWrapper = g_manager.getObjWrapper();
		g_addonsType = g_objWrapper.data("addonstype");
		
		g_manager.setObjAjaxAddData({addontype: g_addonsType});
		
		//init empty addons wrapper
		g_emptyAddonsWrapper = jQuery("#uc_empty_addons_wrapper");
		if(g_emptyAddonsWrapper.length == 0)
			g_emptyAddonsWrapper = null;
		
		//init cats
		g_objCats = g_manager.getObjCats();
		g_objCats.setObjAjaxAddData({type: g_addonsType});
		
		//init items
		g_objItems = g_manager.getObjItems();
		g_objItems.setSpacesBetween(15,15);
		
		g_manager.initItems();
		
		initItems();
		
		initFilters();
		
		initTooltip();
		initEvents();
	}
	
	
}