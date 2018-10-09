function UCManagerAdminCats(){

	var g_selectedCatID = -1;
	var g_catClickReady = false;
	var g_catFieldRightClickReady = true;		//avoid double menu on cat field
	var g_maxCatHeight = 450;
	var g_manager, g_objAjaxDataAdd = null;
	
	
	//event functions
	this.events = {
		onRemoveSelectedCategory: function(){},
		onHeightChange: function(){}
	};
	
	
	var g_temp = {
			isInited: false
	};
	
	var t = this;
	

	function _______________INIT______________(){}
	
	/**
	 * validate that the object is inited
	 */
	function validateInited(){
		if(g_temp.isInited == false)
			throw new Error("The categories is not inited");
		
	}
	
	
	/**
	 * init the categories
	 */
	function initCats(objManager){
				
		if(g_temp.isInited == true)
			throw new Error("Can't init cat object twice");

		g_manager = objManager;
		
		g_temp.isInited = true;

		if(!g_ucAdmin)
			g_ucAdmin = new UniteAdminUC();		
		
		initEvents();
		
		//update sortable categories		
		jQuery( "#list_cats" ).sortable({
			axis:'y',
			start: function( event, ui ) {
				g_catClickReady = false;
			},
			update: function(){
				updateCatOrder();
				//save sorting order
			}
		});		
		
		initAddCategoryDialog();
		
		initEditCategoryDialog();
		
		initDeleteCategoryDialog();
	}
	
	
	function _______________GETTERS______________(){}
	
	/**
	 * 
	 * get category by id
	 */
	function getCatByID(catID){
		var objCat = jQuery("#category_" + catID);
		return(objCat);
	}
	
	
	/**
	 * get category data
	 */
	function getCatData(catID){
		
		var objCat = getCatByID(catID);
		if(objCat.length == 0)
			return(null);
		
		var data = {};
		data.id = catID;
		data.title = objCat.data("title");
		
		return(data);
	}
	
	
	/**
	 * check if some category selected
	 * 
	 */
	this.isCatSelected = function(catID){
		if(catID == g_selectedCatID)
			return(true);
		
		return(false);
	}
	
	
	function _______________SETTERS______________(){}
	
	
	/**
	 * remove category from html
	 */
	function removeCategoryFromHtml(catID){
		
		jQuery("#category_"+catID).remove();
		
		if(catID == g_selectedCatID)
			g_selectedCatID = -1;
		
		//disableCatButtons();
	}
	
	
	/**
	 * set first category selected
	 */
	function selectFirstCategory(){
		var arrCats = getArrCats();
		if(arrCats.length == 0)
			return(false);
		
		var firstCat = arrCats[0];
		var catID = jQuery(firstCat).data("id");
		t.selectCategory(catID);
	}
	
	
	/**
	 * run category action
	 */
	this.runCategoryAction = function(action, catID){
		
		if(!catID)
			var catID = g_selectedCatID;
		
		switch(action){
			case "add_category":
				openAddCategoryDialog();
			break;
			case "edit_category":
				openEditCategoryDialog(catID);
			break;
			case "delete_category":
				openDeleteCategoryDialog(catID);
			break;
			default:
				return(false);
			break;
		}
	
		return(true);
	}

	
	/**
	 * enable category buttons
	 */
	function enableCatButtons(){
		
		//cat butons:
		//g_ucAdmin.enableButton("#button_remove_category, #button_edit_category");
		
	}
	
	/**
	 * enable category buttons
	 */
	function disableCatButtons(){
		
		//g_ucAdmin.disableButton("#button_remove_category, #button_edit_category");
		
	}

	
	/**
	 * update categories order
	 */
	function updateCatOrder(){
		
		//get sortIDs
		var arrSortCats = jQuery( "#list_cats" ).sortable("toArray");
		var arrSortIDs = [];
		for(var i=0;i < arrSortCats.length; i++){
			var catHtmlID = arrSortCats[i];
			var catID = catHtmlID.replace("category_","");
			arrSortIDs.push(catID);
		}
		
		var data = {cat_order:arrSortIDs};
		g_manager.ajaxRequestManager("update_cat_order",data,g_uctext.updating_categories_order);
	}
	
	function _______________ADD_CATEGORY______________(){}
		
	/**
	 * add category
	 */
	function addCategory(){
		
		var data = {};
		data.catname = jQuery("#uc_dialog_add_category_catname").val();
		
		if(g_objAjaxDataAdd && typeof(data) == "object"){
			jQuery.extend(data, g_objAjaxDataAdd);
		}
		
		g_ucAdmin.dialogAjaxRequest("uc_dialog_add_category", "add_category", data, function(response){
			
			var html = response.htmlCat;
			
			jQuery("#list_cats").append(html);
			
			//update html cats select
			var htmlSelectCats = response.htmlSelectCats;
			jQuery("#select_item_category").html(htmlSelectCats);
			
			t.events.onHeightChange();
			
		});


	}
	
	
	/**
	 * open add category dialog
	 */
	function openAddCategoryDialog(){
		
		g_ucAdmin.openCommonDialog("#uc_dialog_add_category", function(){
			
			jQuery("#uc_dialog_add_category_catname").val("").focus();
			
		});
		
	}
	
	
	/**
	 * init add category dialog
	 */
	function initAddCategoryDialog(){
		
		jQuery("#uc_dialog_add_category_action").click(addCategory);
		
		// set update title onenter function
		jQuery("#uc_dialog_add_category_catname").keyup(function(event){
			if(event.keyCode == 13)
				addCategory();
		});
		
	}
	
	function _______________EDIT_CATEGORY______________(){}
	
	/**
	 * 
	 * open the edit category dialog by category id
	 */
	function openEditCategoryDialog(catID){
		
		if(catID == -1)
			return(false);
		
		var cat = getCatByID(catID);
		
		if(cat.length == 0){
			trace("category with id: " + catID + " don't exists");
			return(false);
		}
		
		//set data
		var dialogEdit = jQuery("#uc_dialog_edit_category");
		dialogEdit.data("catid", catID);
		
		//update catid field		
		jQuery("#span_catdialog_id").html(catID);
		
		var title = cat.data("title");
		
		jQuery("#uc_dialog_edit_category_title").val(title).focus();
		
		g_ucAdmin.openCommonDialog("#uc_dialog_edit_category", function(){
			
			jQuery("#uc_dialog_edit_category_title").select();
			
		});
		
	}
	
	
	/**
	 * function invoke from the dialog update button
	 */
	function updateCategoryTitle(){
		
		var dialogEdit = jQuery("#uc_dialog_edit_category");
		
		var catID = dialogEdit.data("catid");		
		
		var cat = getCatByID(catID);
		
		var numItems = cat.data("numaddons");
		
		var newTitle = jQuery("#uc_dialog_edit_category_title").val();
		var data = {
			catID: catID,
			title: newTitle
		};
		
		if(g_objAjaxDataAdd && typeof(data) == "object"){
			jQuery.extend(data, g_objAjaxDataAdd);
		}
		
		g_ucAdmin.dialogAjaxRequest("uc_dialog_edit_category", "update_category", data, function(response){
			
			var newTitleShow = newTitle;
			if(numItems && numItems != undefined && numItems > 0)
				newTitleShow += " ("+numItems+")";
				
			cat.html("<span>" + newTitleShow + "</span>");
			
			cat.data("title",newTitle);
			
		});
		
	}
	
	
	/**
	 * init edit category dialog
	 */
	function initEditCategoryDialog(){
		
		// set update title onenter function
		jQuery("#uc_dialog_edit_category_action").click(updateCategoryTitle);
		
		jQuery("#uc_dialog_edit_category_title").doOnEnter(updateCategoryTitle);
		
	}
	
	
	function _______________DELETE_CATEGORY______________(){}
		
	
	/**
	 * remove some category by id
	 */
	function deleteCategory(){
		 
		var dialogDelete = jQuery("#uc_dialog_delete_category");
		var catID = dialogDelete.data("catid");
				
		var data = {};
		data.catID = catID;
		
		//get if selected category will be removed
		var isSelectedRemoved = (catID == g_selectedCatID);
		
		if(g_objAjaxDataAdd && typeof(data) == "object"){
			jQuery.extend(data, g_objAjaxDataAdd);
		}
		
		g_ucAdmin.dialogAjaxRequest("uc_dialog_delete_category", "remove_category", data, function(response){
			
			removeCategoryFromHtml(catID);
			
			//update html cats select
			var htmlSelectCats = response.htmlSelectCats;
			jQuery("#select_item_category").html(htmlSelectCats);
			
			//clear the items panel
			if(isSelectedRemoved == true){
				
				//run event
				t.events.onRemoveSelectedCategory();
								
				g_selectedCatID = -1;
				t.checkSelectFirstCategory();
			}
			
			//fire height change event
			t.events.onHeightChange();
			
		});
		
				
	}
	
	/**
	 * 
	 * open the edit category dialog by category id
	 */
	function openDeleteCategoryDialog(catID){
		
		if(catID == -1)
			return(false);
		
		var cat = getCatByID(catID);
		
		if(cat.length == 0){
			trace("category with id: " + catID + " don't exists");
			return(false);
		}
		
		//set data
		var dialogDelete = jQuery("#uc_dialog_delete_category");
		dialogDelete.data("catid", catID);
		
		var title = cat.data("title");
		
		jQuery("#uc_dialog_delete_category_catname").html(title);
		
		g_ucAdmin.openCommonDialog("#uc_dialog_delete_category");
		
	}
	
	
	/**
	 * init edit category dialog
	 */
	function initDeleteCategoryDialog(){
		
		// set update title onenter function
		jQuery("#uc_dialog_delete_category_action").click(deleteCategory);
		
	}
	
	
	function _______________EVENTS______________(){}
	
	
	/**
	 * on category list item click
	 */
	function onCatListItemClick(event){

		if(g_ucAdmin.isRightButtonPressed(event))
    		return(true);
		
		if(g_catClickReady == false)
			return(false);
		
		if(jQuery(this).hasClass("selected-item"))
			return(false);
		
		var catID = jQuery(this).data("id");
		t.selectCategory(catID);
		
	}
	
	/**
	 * on cat list item mousedown
	 */
	function onCatListItemMousedown(event){
	
		if(g_ucAdmin.isRightButtonPressed(event))
			return(true);
		
		g_catClickReady = true;
		
	}

	
	/**
	 * on category context menu click
	 */
	function onCategoryContextMenu(event){
		
		g_catFieldRightClickReady = false;
		
		var objCat = jQuery(this);
		var catID = objCat.data("id");
		
		if(catID == 0 || catID == "all")
			return(false);
		
		var objMenu = jQuery("#rightmenu_cat");
		
		objMenu.data("catid",catID);
		g_manager.showMenuOnMousePos(event, objMenu);
	}

	
	/**
	 * on categories context menu
	 */
	function onCatsFieldContextMenu(event){
		
		event.preventDefault();
		
		if(g_catFieldRightClickReady == false){
			g_catFieldRightClickReady = true;
			return(true);
		}
		
		var objMenu = jQuery("#rightmenu_catfield");
		g_manager.showMenuOnMousePos(event, objMenu);
	}
	
	
	/**
	 * on action button click
	 */
	function onActionByttonClick(){
		
		var objButton = jQuery(this);
		
		if(!g_ucAdmin.isButtonEnabled(objButton))
			return(false);
		
		var action = objButton.data("action");
		
		t.runCategoryAction(action);
		
	}
	
	
	/**
	 * init events
	 */
	function initEvents(){
		
		jQuery(".uc-cat-action-button").click(onActionByttonClick);
		
		//list categories actions
		jQuery("#list_cats").delegate("li", "mouseover", function() {
			jQuery(this).addClass("item-hover");
			
		});
		
		jQuery("#list_cats").delegate("li", "mouseout", function() {
			jQuery(this).removeClass("item-hover");
		});
		
		jQuery("#list_cats").delegate("li", "click", onCatListItemClick);
		
		jQuery("#list_cats").delegate("li", "mousedown", onCatListItemMousedown );
		
		//init context menus
		jQuery("#list_cats").delegate("li","contextmenu", onCategoryContextMenu);
		jQuery("#cats_section").bind("contextmenu", onCatsFieldContextMenu);
		
	}
	
	this._______________EXTERNAL_GETTERS______________ = function(){}
	
	
	/**
	 * get selected category ID
	 */
	this.getSelectedCatID = function(){
		
		return(g_selectedCatID);
	}
	
	
	/**
	 * get selected category data
	 */
	this.getSelectedCatData = function(){
		
		if(g_selectedCatID == -1)
			return(null);
		
		var data = getCatData(g_selectedCatID);
		
		return(data);
	}
	
	
	/**
	 * return if some category selected
	 */
	this.isSomeCatSelected = function(){
		
		if(g_selectedCatID == -1)
			return(false);
		
		return(true);
	}
	
	
	/**
	 * get height of the categories list
	 */
	this.getCatsHeight = function(){
		
		var catsWrapper = jQuery("#cats_section .cat_list_wrapper");
		var catHeight = catsWrapper.height();

		if(catHeight > g_maxCatHeight)
			catHeight = g_maxCatHeight;
		
		return(catHeight);
	}
	
	/**
	 * get arr categories
	 */
	function getArrCats(){
		var arrCats = jQuery("#list_cats li").get();
		return(arrCats);
	}
	
	
	/**
	 * get num categories
	 */
	this.getNumCats = function(){
		var numCats = jQuery("#list_cats li").length;
		return(numCats);
	}
	
	
	/**
	 * get mouseover category
	 */
	this.getMouseOverCat = function(){

		var arrCats = getArrCats();
		
		for(var index in arrCats){
			var objCat = arrCats[index];
			objCat = jQuery(objCat);
			
			var isMouseOver = objCat.ismouseover();
			if(isMouseOver == true)
				return(objCat);
		}
		
		return(null);
	}
	
	
	this._______________EXTERNAL_SETTERS______________ = function(){}
	
	
	/**
	 * set object add data to every ajax request
	 */
	this.setObjAjaxAddData = function(objData){
		
		g_objAjaxDataAdd = objData;
		
	}
	
	/**
	 * set cat section height
	 */
	this.setHeight = function(height){
		
		jQuery("#cats_section").css("height", height+"px");
		
	}
	
	/**
	 * set html cats list
	 */
	this.setHtmlListCats = function(htmlCats){
		
		jQuery("#list_cats").html(htmlCats);
		
	}
	
	/**
	 * select some category by id
	 */
	this.selectCategory = function(catID){
		
		var cat = jQuery("#category_"+catID);
		if(cat.length == 0){
			//g_ucAdmin.showErrorMessage("category with id: "+catID+" not found");
			return(false);
		}
		
		cat.removeClass("item-hover");
		
		if(cat.hasClass("selected-item"))
			return(false);
		
		g_selectedCatID = catID;
		
		jQuery("#list_cats li").removeClass("selected-item");
		cat.addClass("selected-item");
		
		/*
		if(catID == 0 || catID == "all")
			disableCatButtons();
		else
			enableCatButtons();
		*/
		
		g_manager.onCatSelect(catID);
		
		return(true);
	}
	
	/**
	 * check if number of cats = 1, if do, select it
	 */
	this.checkSelectFirstCategory = function(){
		
		var arrCats = getArrCats();
		
		if(arrCats.length == 0)
			return(false);
		
		//if only one category exists - select it
		
		if(arrCats.length == 1){
			selectFirstCategory();
			return(false);
		}
		
		//if first cat - all - select it
		
		var firstCat = jQuery(arrCats[0]);
		var catID = firstCat.data("id");
		if(catID == "all")
			selectFirstCategory();
		
	}
	
	
	/**
	 * get context menu category ID
	 */
	this.getContextMenuCatID = function(){
		var catID = jQuery("#rightmenu_cat").data("catid");
		return(catID);
	}
	
	
	/**
	 * destroy the categories
	 */
	this.destroy = function(){
		
		//add category
		jQuery("#button_add_category").off("click");
		
		//remove category:
		jQuery("#button_remove_category").off("click");
		
		//edit category
		jQuery("#button_edit_category").off("click");
		
		var objListItems = jQuery("#list_cats").find("li");
		objListItems.off("mouseover");
		objListItems.off("mouseout");
		objListItems.off("click");
		objListItems.off("mousedown");
							
		//init context menus
		jQuery("#list_cats").off("contextmenu");
		jQuery("#cats_section").off("contextmenu");
		
		
	}
	
	
	/**
	 * init categories
	 */
	this.init = function(objManager){
		
		initCats(objManager);
		
	}
	
	
}