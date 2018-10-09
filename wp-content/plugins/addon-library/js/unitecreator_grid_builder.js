
function UniteCreatorGridBuilder(){

	var g_objGrid, g_dialogBrowser, g_objStyle, g_options;
	var g_optionsCustom = {};
	var g_gridID, g_objSettingsGrid = new UniteSettingsUC();
	var g_objBrowser = new UniteCreatorBrowser();
	var g_objSettingsRow = new UniteSettingsUC();
	var g_objDialogRowSettings;
	
	var t = this;
	
	var g_vars = {
			class_col: "uc-grid-col",
			class_first_col: "uc-col-first",
			class_last_col: "uc-col-last",
			class_empty: "uc-col-empty",
			class_first_row: "uc-row-first",
			class_last_row: "uc-row-last",
			class_size_prefix:"uc-colsize-",
			max_cols: 6
	};
	
	this.events = {
			ROW_COLUMNS_UPDATED: "ROW_COLUMNS_UPDATED",
			ROWS_UPDATED: "ROWS_UPDATED",	//add, remove, reorder row
			ROW_ADDED: "ROW_ADDED",
			COL_ADDED: "COL_ADDED",
			COL_ADDONS_UPDATED: "ADDONS_UPDATED"
	}
	
	if(!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();
	
	
	function ____________GENERAL______________(){}
	
	
	/**
	 * get element type - column, addon, row, undefined
	 */
	function getElementType(element){
		
		if(element.hasClass("uc-grid-col"))
			return("column");
		
		if(element.hasClass("uc-grid-col-addon"))
			return("addon");

		if(element.hasClass("uc-grid-row"))
			return("row");
				
		return("undefined");
	}
	
	
	/**
	 * do grid action
	 */
	function doGridAction(action){
		
		switch(action){
			case "add_row":
				addRow();
			break;
			default:
				throw new Error("Wrong grid action: "+action);
			break;
		}
		
	}
	
	
	function ____________ROW______________(){}
	
	
	/**
	 * validate row
	 */
	function validateRow(objRow){
		
		if(!objRow)
			throw new Error("Empty Row Found: "+objRow);
		
	}
	
		
	
	/**
	 * get row html
	 */
	function getHtmlRow(){
		
		var html = "";
		html += "<div class='uc-grid-row'>";
		
		//add row panel
		html += "	<div class='uc-grid-row-panel'>";
		html += "			<a href='javascript:void(0)' title='"+g_uctext.move_row+"' class=\"uc-row-icon uc-grid-icon-move uc-row-icon-move uc-tip\" ><i class=\"fa fa-arrows\" aria-hidden=\"true\"></i></a> ";
		html += "			<a href='javascript:void(0)' data-action='delete_row' data-actiontype='row' title='"+g_uctext.delete_row+"' class=\"uc-row-icon uc-grid-action-icon uc-tip\" ><i class=\"fa fa-trash\" aria-hidden=\"true\"></i></a> ";
		html += "			<a href='javascript:void(0)' data-action='duplicate_row' data-actiontype='row' title='"+g_uctext.duplicate_row+"' class=\"uc-row-icon uc-grid-action-icon uc-tip\" ><i class=\"fa fa-clone\" aria-hidden=\"true\"></i></a> ";
		html += "			<a href='javascript:void(0)' data-action='row_settings' data-actiontype='row' title='"+g_uctext.settings+"' class=\"uc-row-icon uc-grid-action-icon uc-tip\" ><i class=\"fa fa-cog\" aria-hidden=\"true\"></i></a> ";
		html += "	</div>";
		
		//add title
		html += "<div class='uc-grid-row-title' style='display:none'></div>";
		
		//add container
		html += "	<div class='uc-grid-row-container'>";
		html += "	</div>";
		
		html += "</div>";
		
		return(html);
	}
	
	
	/**
	 * add empty row
	 */
	function addEmptyRow(){
		var html = getHtmlRow();
		
		var objRow = jQuery(html);
		
		g_objGrid.append(objRow);
		
		triggerEvent(t.events.ROW_ADDED, objRow);
		triggerEvent(t.events.ROWS_UPDATED);
		
		return(objRow);
	}
	
	
	/**
	 * add row to the end
	 */
	function addRow(){
		
		var objRow = addEmptyRow();
				
		addColumn(objRow);
		
	}
	
	
	/**
	 * get all rows
	 */
	function getRows(){
		
		var objRows = g_objGrid.children(".uc-grid-row");
		
		return(objRows);
	}
	
	
	/**
	 * get number of rows
	 */
	function getNumRows(){
		
		var objRows = getRows();
		
		var numRows = objRows.length;
		
		return(numRows);
	}
	
	
	
	/**
	 * get row bu number
	 */
	function getRow(num){
		
		if(!num)
			var num = 0;
		
		var objRows = getRows();
		
		if(num >= objRows.length)
			throw new Error("getRow error: Row "+num+" don't exists");
		
		var objRow = jQuery(objRows[num]);
		
		return(objRow);
	}
	
	
	/**
	 * get row container
	 * @param objRow
	 */
	function getRowContainer(objRow){
		var objContainer = objRow.children(".uc-grid-row-container");
		g_ucAdmin.validateDomElement(objContainer, "Row Container");
		return(objContainer);
	}
	
	
	/**
	 * get parent row
	 */
	function getParentRow(objChild){
		
		var objRow = objChild.parents(".uc-grid-row");
		
		return(objRow);
	}
	
	
	/**
	 * get row settings
	 */
	function getRowSettings(objRow){
		var objSettings = objRow.data("settings");
		if(!objSettings)
			objSettings = {};
		
		return(objSettings);
	}
		
	
	
	
	/**
	 * delete row
	 */
	function deleteRow(objRow){
		
		objRow.remove();
		
		var numRows = getNumRows();
		if(numRows == 0)
			addRow();	//triggers the updated event
		else
			triggerEvent(t.events.ROWS_UPDATED);

	}
	
	
	/**
	 * update row visual css
	 */
	function updateRowVisual_css(objRow, objSettings){
		
		
		//back color
		var cssRow = {};
		
		cssRow = g_ucAdmin.addCssSetting(objSettings, cssRow, "row_background_color", "background-color");
		cssRow = g_ucAdmin.addCssSetting(objSettings, cssRow, "row_padding_top", "padding-top","px");
		cssRow = g_ucAdmin.addCssSetting(objSettings, cssRow, "row_padding_bottom", "padding-bottom","px");
		
		//remove style
		objRow.removeAttr("style");
		
		//add additional css
		var rowAddCss = g_ucAdmin.getVal(objSettings, "row_css", null);
		if(rowAddCss){
			rowAddCss = g_ucAdmin.removeLineBreaks(rowAddCss);
			objRow.prop("style", rowAddCss);
		}
		
		objRow.css(cssRow);
		
		//----------- Container
		
		var strStyleContainer = "";
		var containerWidth = g_ucAdmin.getVal(objSettings, "row_container_width", null);
		if(containerWidth){
			containerWidth = g_ucAdmin.normalizeSizeValue(containerWidth);
			strStyleContainer += "width:" + containerWidth+";";
		}
		
		
		//add container css
		var containerAddCss = g_ucAdmin.getVal(objSettings, "row_container_css", null);
		if(containerAddCss){
			containerAddCss = g_ucAdmin.removeLineBreaks(containerAddCss);
			strStyleContainer += containerAddCss;
		}
		
		var objContainer = getRowContainer(objRow);
		
		//remove style
		objContainer.removeAttr("style");
		
		if(strStyleContainer){
			
			objContainer.prop("style", strStyleContainer);
		
		}
		
		// row - col - addons 
		var addonBoxStyle = "";
		var objAddons = getRowAddons(objRow);
		var spaceBetweenAddons = g_ucAdmin.getVal(objSettings, "space_between_addons", null);
		
		if(spaceBetweenAddons){
			spaceBetweenAddons = g_ucAdmin.normalizeSizeValue(spaceBetweenAddons);
			addonBoxStyle += "margin-top:" + spaceBetweenAddons+";";
		}
		
		jQuery.each(objAddons,function(index, addon){
			var objAddon = jQuery(addon);
			var addonIndex = objAddon.index();
			if(addonIndex != 0)
				objAddon.prop("style", addonBoxStyle);
			else
				objAddon.prop("style", "");
		
		});
		
	}
	
	
	/**
	 * update row title object
	 */
	function updateRowVisual_title(objRow, objSettings){
		
		//update row title:
		var rowTitle = g_ucAdmin.getVal(objSettings, "row_title","",g_ucAdmin.getvalopt.TRIM);
		
		var showTitle = false;
		if(rowTitle)
			showTitle = true;
		
		//turn of by local or global setting
		if(showTitle == true){
			
			var showTitleText = g_ucAdmin.getVal(objSettings, "row_show_title");
			if(showTitleText == "default")
				showTitleText = getGridOption("show_row_titles");
			
			if(showTitleText == "default")
				showTitleText = getGridOption("show_row_titles_global");
			
			if(showTitleText == "no")
				showTitle = false;
			
		}
		
		//show or hide title
		var objTitle = objRow.children(".uc-grid-row-title");
		if(showTitle == true){
			objTitle.show();
			objTitle.html(rowTitle);
		}else
			objTitle.hide();
		
	}
	
	/**
	 * update row css
	 */
	function updateRowVisual(objRow){
		
		var objSettings = objRow.data("settings");
		
		updateRowVisual_css(objRow, objSettings);
		updateRowVisual_title(objRow, objSettings);
		
	}
	
		
	
	/**
	 * set row settings, update css
	 */
	function updateRowSettings(objRow, objSettings){
		
		objRow.data("settings", objSettings);
		
		updateRowVisual(objRow);
	}
	
	
	/**
	 * duplicate row
	 */
	function duplicateRow(objRow){
				
		var objRowCopy = objRow.clone(true, true);
		
		objRowCopy.insertAfter(objRow);
		
		triggerEvent(t.events.ROWS_UPDATED);
		
	}
	
	/**
	 * redraw row
	 */
	function redrawRow(objRow){
		
		trace("redraw");
		
	}
	
	
	/**
	 * do action
	 */
	function doRowAction(action, objRow){
		
		switch(action){
			case "add_row":
				addRow();
			break;
			case "delete_row":
				deleteRow(objRow);
			break;
			case "duplicate_row":
				duplicateRow(objRow);
			break;
			case "row_settings":
				openRowSettingsDialog(objRow);
			break;
			default:
				trace("wrong row action: " + action);
			break;
		}
		
	}
	
	function ____________MULTIPLE_ROWS______________(){}
	
	/**
	 * update all rows visual
	 */
	function updateAllRowsVisual(){
		var rows = getRows();
		
		jQuery.each(rows,function(index, row){
			var objRow = jQuery(row);
			updateRowVisual(objRow);
		});
	}
	
	/**
	 * update classes of all rows
	 */
	function updateRowsClasses(){
		
		var objRows = getRows();
		
		var numRows = getNumRows();
		jQuery.each(objRows, function(key, row){
			var rowClass = "uc-grid-row";
			
			if(key == 0)
				rowClass += " uc-row-first";
			
			if(key == (numRows-1))
				rowClass += " uc-row-last";
			
			row.className = rowClass;
		})
		
		
	}
	
	
	function ____________COLUMN______________(){}
	
	/**
	 * validate that the object is column
	 */
	function validateCol(objCol){
		
		g_ucAdmin.validateDomElement(objCol, "column");
		
		if(objCol.hasClass("uc-grid-col") == false)
			throw new Error("The object is not column type");
	}
	
	
	/**
	 * get html columns
	 */
	function getHtmlColumn(){
		var html = "";
		html += "<div class=\"uc-grid-col\">";
		
		//add columns buttons
		html += "			<a href='javascript:void(0)' data-action='addcol_before' data-actiontype='col' title='"+g_uctext.add_column+"' class=\"uc-icon-addcol uc-addcol-before uc-grid-action-icon\" ><i class=\"fa fa-plus\" aria-hidden=\"true\"></i></a> ";
		html += "			<a href='javascript:void(0)' data-action='addcol_after' data-actiontype='col' title='"+g_uctext.add_column+"' class=\"uc-icon-addcol uc-addcol-after uc-grid-action-icon\" ><i class=\"fa fa-plus\" aria-hidden=\"true\"></i></a> ";
		
		html += "	<div class=\"uc-grid-box-wrapper\">";
		
		//top panel
		html += "		<div class=\"uc-grid-col-top\">";
		html += "			<div class = \"uc-grid-col-panel\">";
		html += "				<a href='javascript:void(0)' title='"+g_uctext.move_column+"' class=\"uc-col-icon uc-grid-icon-move uc-col-icon-move uc-tip\"><i class=\"fa fa-arrows\" aria-hidden=\"true\"></i></a> ";
		html += "				<a href='javascript:void(0)' data-action='duplicate' data-actiontype='col' title='"+g_uctext.duplicate_column+"' class=\"uc-col-icon uc-grid-action-icon uc-tip\"><i class=\"fa fa-clone\" aria-hidden=\"true\"></i></a> ";
		html += "				<a href='javascript:void(0)' data-action='delete' data-actiontype='col' title='"+g_uctext.delete_column+"' class=\"uc-col-icon uc-grid-action-icon uc-tip\"><i class=\"fa fa-trash\" aria-hidden=\"true\"></i></a> ";
		//html += "				<a href='javascript:void(0)' data-action='stretch'  class=\"uc-col-icon uc-col-icon-stretch uc-action-icon uc-tip\"></a> ";
		html += "			</div>";
		html += "		</div>";
		
		html += "		<div class=\"uc-grid-col-middle\">";
		
		//middle content
		html += "			<div data-actiontype='col' data-action='add_col_addon' class=\"uc-grid-overlay-empty uc-grid-action-icon\" >";
		
		html += "				<div class='uc-overlay-empty-content unite-centered-vert'>Set Addon</div>";
		
		html += "			</div>";
		
		html += "			<div class=\"uc-grid-col-addons\" style='display:none'>";
		
		html += "			</div>";
		
		html += "		</div>";
		
		//bottom panel
		html += "		<div class=\"uc-grid-col-bottom\">";
		html += "				<a href='javascript:void(0)' onfocus='this.blur()' data-action='add_col_addon' data-actiontype='col' title='"+g_uctext.add_addon_to_column+"' class=\"uc-col-icon uc-grid-action-icon uc-tip uc-icon-add-more-addon\" style='display:none'><i class=\"fa fa-plus\" aria-hidden=\"true\"></i></a> ";
		html += "		</div>";


		html += "	</div>";	//box wrapper end
		
		html += "</div>"	//col;
		
		return(html);
	}
	
	
	/**
	 * get num columns in row
	 */
	function getCols(objRow, novalidate){
		
		var objContainer = getRowContainer(objRow);
		
		var objCols = objContainer.children(".uc-grid-col");
		
		if(objCols.length == 0 && !novalidate)
			throw new Error("getCols error - row should have at least 1 column");
		
		return(objCols);
	}
	
	
	/**
	 * get column by number
	 */
	function getCol(objRow, numCol){
		
		var objCols = getCols(objRow);
		if(numCol >= objCols.length)
			throw new Error("There is no col number: "+numCol+" in the row");
		
		var objCol = jQuery(objCols[numCol]);
		
		return(objCol);
	}
	
	
	
	/**
	 * get parent column
	 */
	function getParentCol(objChild){
		
		var objCol = objChild.parents(".uc-grid-col");
		
		return(objCol);
	}
	
	
	/**
	 * get number of columns in row
	 */
	function getNumCols(objRow){
		
		validateRow(objRow);
				
		var objCols = getCols(objRow, true);
		
		var numCols = objCols.length;
		
		return(numCols);
	}
	
	/**
	 * get addons wrapper
	 */
	function getColAddonsWrapper(objCol){
		
		var objAddonsWrapper = objCol.find(".uc-grid-col-addons");
		g_ucAdmin.validateDomElement(objAddonsWrapper, "col addons wrapper");
		
		return(objAddonsWrapper);
	}
	
		
	
	/**
	 * check if it's first column
	 */
	function isFirstCol(objCol){
		var isFirst = objCol.hasClass("uc-col-first");
		
		return isFirst;
	}
	
	
	/**
	 * check if it's last column
	 */
	function isLastCol(objCol){
		var isLast = objCol.hasClass("uc-col-last");
		
		return isLast;
	}
	
	
	/**
	 * add empty column
	 * the mode can be: empty, before, after
	 */
	function addColumn(objRow, objCol, mode){
		
		if(!objRow){
			if(objCol)
				var objRow = getParentRow(objCol);
			else
				var objRow = getRow();
		}
				
		//check the limits
		var numCols = getNumCols(objRow);
				
		if(numCols == g_vars.max_cols)
			return(false);
				
		//add the column
		var htmlCol = getHtmlColumn();
		
		var objNewCol = jQuery(htmlCol);
		
		//insert before or after column
		objNewCol.hide();
		
		if(objCol){
			
			switch(mode){
				case "before":
					objNewCol.insertBefore(objCol);
				break;
				case "after":
					objNewCol.insertAfter(objCol);
				break;
				default:
				break;
			}
			
			
		}else{	//insert last column
			
			var objContainer = getRowContainer(objRow);
			
			objContainer.append(objNewCol);
			
		}

		triggerEvent(t.events.ROW_COLUMNS_UPDATED, objRow);
		triggerEvent(t.events.COL_ADDED, objNewCol);
		
		//show the column after transition
		setTimeout(function(){
			objNewCol.show();
		},350);
		
		return(objNewCol);
	}
	
	
	/**
	 * duplicate column
	 */
	function duplicateCol(objCol){
		
		var objRow = getParentRow(objCol);
		
		var objColCopy = objCol.clone(true, true);
		
		objColCopy.insertAfter(objCol);
		
		triggerEvent(t.events.ROW_COLUMNS_UPDATED, objRow);
		
	}
	
	
	/**
	 * 
	 * @param objCol
	 */
	function deleteCol(objCol){
		
		var objRow = getParentRow(objCol);
		var numCols = getNumCols(objRow);
		
		if(numCols <= 1){
			alert("Can't delete last column");
			return(false);
		}
		
		objCol.remove();
		
		triggerEvent(t.events.ROW_COLUMNS_UPDATED, objRow);
	}
	
	
	
	/**
	 * do column action
	 */
	function doColAction(objCol, action){
		
		switch(action){
			case "delete":
				
				deleteCol(objCol);
				
			break;
			case "duplicate":
				
				duplicateCol(objCol);
			
			break;
			case "stretch":
				trace("stretch");
			break;
			case "addcol_before":
				
				addColumn(null, objCol, "before");
			
			break;
			case "addcol_after":
			
				addColumn(null, objCol, "after");
				
			break;
			case "add_col_addon":
				openAddonsBrowser(objCol, true);				
			break;
			default:
				trace("wrong col action: "+action);
			break;
		}
		
	}
	
	
	/**
	 * update row columns classes
	 */
	function updateColsClasses(objRow){
				
		var objCols = getCols(objRow);
		
		var numCols = objCols.length;
		
		var colWidth = 1;	//temp value, num cells that it take
		
		var classColSize = g_vars.class_size_prefix + colWidth+ "_" + numCols;
		
		objCols.each(function(num, col){
			
			var isFirst = (num == 0);
			var isLast = (num == numCols-1);
			
			var objCol = jQuery(col);
			var isEmpty = objCol.hasClass(g_vars.class_empty);
			
			//set class
			var classCol = g_vars.class_col;

			if(isFirst)
				classCol += " "+g_vars.class_first_col;
			
			if(isLast)
				classCol += " "+g_vars.class_last_col;
			
			if(isEmpty)
				classCol += " "+g_vars.class_empty;
			
			classCol += " " + classColSize;
			
			classCol += " uc-col-trans";
			
			col.className = classCol;
			
		});
		
	}
	
	
	/**
	 * set the add col icon active / not active
	 */
	function activateAddColIcon(objIcon, isActivate){
		
		if(isActivate){
			objIcon.addClass("uc-icon-active");
		}
		else{
			objIcon.removeClass("uc-icon-active");
		}
		
	}
	
	
	/**
	 * check row related buttons after the row is changed
	 */
	function updateColOperationButtons(objRow){
	
		var numCols = getNumCols(objRow);
		
		var objCols = getCols(objRow);

		var isHideAll = false;
		if(numCols >= g_vars.max_cols)
			isHideAll = true;
			
		jQuery.each(objCols, function(key, col){
			
			var objCol = jQuery(col);
			
			var showLeftIcon = false;
			var showRightIcon = true;
			
			if(isHideAll == true){
				showRightIcon = false
			}else
			  if(isFirstCol(objCol) == true)
				showLeftIcon = true;
			
			//show the buttons
			var objLeftIcon = objCol.find(".uc-icon-addcol.uc-addcol-before");
			var objRightIcon = objCol.find(".uc-icon-addcol.uc-addcol-after");
			
			
			activateAddColIcon(objLeftIcon, showLeftIcon);
			activateAddColIcon(objRightIcon, showRightIcon);
			
		});
		
	}
	
	/**
	 * set col empty state visual
	 */
	function setColEmptyStateVisual(objCol, isEmpty){
		
		var objOverlayEmpty = objCol.find(".uc-grid-overlay-empty");
		var objIconAddMore =  objCol.find(".uc-icon-add-more-addon");
		var objAddonsWrapper = getColAddonsWrapper(objCol);
		
		if(isEmpty == true){
			objOverlayEmpty.show();
			objIconAddMore.hide();			//empty column
			objAddonsWrapper.hide();
		}else{
			objOverlayEmpty.hide();			//has addons
			objIconAddMore.show();
			objAddonsWrapper.show();
		}
		
	}
	
	
	
	function ____________COL_ADDON_ELEMENT______________(){}
	
	/**
	 * validate that the column element is col addon type
	 */
	function validateColAddonElement(objElement){
		
		//validate type
		
		var type = getElementType(objElement);
		if(type != "addon")
			throw new Error("The element must be addons type");
		
		//validate single
		if(objElement.length > 1){
			trace(objElement);
			throw new Error("The addon element should be sinlge");
		}
		
	}
	
	
	/**
	 * get col addons data
	 */
	function getColAddons(objCol){
		
		validateCol(objCol);
		
		var objAddonsWrapper = getColAddonsWrapper(objCol);
		
		var objAddons = objAddonsWrapper.children(".uc-grid-col-addon");
		
		return(objAddons);
	}
	
	
	/**
	 * get row addons
	 */
	function getRowAddons(objRow){
		validateRow(objRow);
		
		var objAddons = objRow.find(".uc-grid-col-addon");
		
		return(objAddons);
	}
	
	
	/**
	 * get number of col addons
	 */
	function getNumColAddons(objCol){
		var objAddons = getColAddons(objCol);
		var numAddons = objAddons.length;
		
		return(numAddons);
	}
	
	
	/**
	 * generate addon html
	 */
	function generateAddonHtml(addonData){
		
		var title = addonData.title;
		var url_icon = addonData.url_icon;
		var addon_name = addonData.name;
		
		url_icon = 	g_ucAdmin.escapeDoubleQuote(url_icon);
		title = g_ucAdmin.htmlspecialchars(title);
		
		var html = "";
		
		html += "		<div class=\"uc-grid-col-addon\" data-name='"+addon_name+"'>";
		
		html += "			<div class=\"uc-grid-col-addon-html unite-centered-vert\" >";
		
		html += "				<img src=\""+url_icon+"\">";
		html += "				<span class='uc-grid-addon-title'>"+title+"</span>";
		
		html += "			</div>";
		
		html += "			<div class=\"uc-grid-overlay-edit\" style='display:none'>";
		html += "				<div class='uc-grid-overlay-buttons unite-centered-vert'>";
		html += "					<a href=\"javascript:void(0)\" data-actiontype='addon' data-action='edit_addon' title=\""+g_uctext.edit_addon+"\" class=\"uc-grid-action-icon \"><i class=\"fa fa-edit\" aria-hidden=\"true\"></i></a>";
		html += "					<a href=\"javascript:void(0)\" data-actiontype='addon' data-action='delete_addon' title=\""+g_uctext.delete_addon+"\" class=\"uc-grid-action-icon \"><i class=\"fa fa-trash\" aria-hidden=\"true\"></i></a>";
		html += "					<a href=\"javascript:void(0)\" data-actiontype='addon' data-action='duplicate_addon' title=\""+g_uctext.duplicate_addon+"\" class=\"uc-grid-action-icon \"><i class=\"fa fa-clone\" aria-hidden=\"true\"></i></a>";
		html += "					<a href='javascript:void(0)' title='"+g_uctext.move_addon+"' class=\"uc-addon-icon-move uc-grid-icon-move uc-tip\" style='display:none' ><i class=\"fa fa-arrows\" aria-hidden=\"true\"></i></a> ";
		html += "				</div>";
				
		html += "			</div>";
	
		html += "		</div>";
				
		return(html);
		
	}
	
	
	/**
	 * get parent row
	 */
	function getParentAddonElement(objChild){
		
		var objAddon = objChild.parents(".uc-grid-col-addon");
		
		g_ucAdmin.validateDomElement(objAddon, "addon holder");
		
		return(objAddon);
	}
	
	
	/**
	 * hide addon overlay
	 */
	function showAddonOverlay(objAddon, toShow){
		
		var objOverlay = objAddon.find(".uc-grid-overlay-edit");
		var objMoveIcon = objAddon.find(".uc-addon-icon-move");
		
		if(toShow == true){
			
			var objCol = getParentCol(objAddon);
			var numAddons = getNumColAddons(objCol);
			
			objOverlay.show();
			
			if(numAddons > 1)
				objMoveIcon.show();
			
		}else{
			
			objOverlay.hide();
			objMoveIcon.hide();
		
		}
		
	}
	
	
	/**
	 * delete column addon
	 */
	function deleteColAddon(objAddon){
		
		validateColAddonElement(objAddon);
		
		var objCol = getParentCol(objAddon);
		
		objAddon.remove();
		
		var numAddons = getNumColAddons(objCol);
		
		if(numAddons == 0)
			setColEmptyStateVisual(objCol, true);
		
		triggerEvent(t.events.COL_ADDONS_UPDATED, objCol);
	}
	
	
	/**
	 * duplicate col addon
	 */
	function duplicateColAddon(objAddon){
		
		validateColAddonElement(objAddon);
		
		var objAddonCopy = objAddon.clone(true, true);
		
		showAddonOverlay(objAddonCopy);
		
		objAddonCopy.insertAfter(objAddon);
		
		var objCol = getParentCol(objAddon);
				
		triggerEvent(t.events.COL_ADDONS_UPDATED, objCol);
	}
	
	
	/**
	 * do addon element related action
	 */
	function doAddonAction(objAddon, action){
		
		validateColAddonElement(objAddon);
		
		switch(action){
			case "edit_addon":
				openAddonsBrowser(objAddon);
			break;
			case "delete_addon":
				deleteColAddon(objAddon);
			break;
			case "duplicate_addon":
				duplicateColAddon(objAddon);
			break;
			default:
				throw new Error("Wrong addon action: "+action);
			break;
		}
		
	}
	
	
	function ____________ADDONS______________(){}
	
	/**
	 * save addon element data
	 */
	function saveAddonElementData(objAddon, addonData){
		
		validateColAddonElement(objAddon);
		
		var addonName = addonData.name;
		objAddon.data("addon_name", addonName);
		objAddon.data("addon_data", addonData);
		
	}
	
	
	/**
	 * update column with addon data
	 */
	function addColAddon(objCol, addonData){
		
		if(jQuery.isArray(addonData))
			return(false);
		
		var objAddonsWrapper = getColAddonsWrapper(objCol);
		
		//save data
		var htmlAddon = generateAddonHtml(addonData);
		var objHtml = jQuery(htmlAddon);
		
		saveAddonElementData(objHtml, addonData);
		
		//generate html
		setColEmptyStateVisual(objCol, false);
		
		objAddonsWrapper.append(objHtml);
	}
	
	
	/**
	 * update obj addon
	 */
	function updateColAddon(objAddon, addonData){
		
		g_ucAdmin.validateNotEmpty(addonData, "addon data");
		
		validateColAddonElement(objAddon);
		
		var htmlAddon = generateAddonHtml(addonData);
		var objAddonNew = jQuery(htmlAddon);
			
		objAddon.replaceWith(objAddonNew);
		
		saveAddonElementData(objAddonNew, addonData);
	}
	
	
	/**
	 * get col addon data
	 */
	function getColAddonData(objAddon){
		
		validateColAddonElement(objAddon);
		
		var objData = objAddon.data("addon_data");
		
		if(!objData)
			objData = null;
		
		return(objData);
	}
	
	/**
	 * get col addons data
	 */
	function getColAddonsData(objCol){
		
		var objAddons = getColAddons(objCol);
		
		var arrData = [];
		
		jQuery.each(objAddons, function(index, addon){
			var objAddon = jQuery(addon);
			var objData = getColAddonData(objAddon);
			
			arrData.push(objData);
		});
		
		return(arrData);
	}
	
	
	/**
	 * open addons browser, for column - add new, for addon - update
	 */
	function openAddonsBrowser(objElement){
		
		var isNew = true;
		
		var type = getElementType(objElement);
		if(type == "addon")
			isNew = false;
		
		var buttonOpts = {};
				
		//cancel button
		buttonOpts[g_uctext.cancel] = function(){
			g_dialogBrowser.dialog("close");
		};
		
		var buttonText = g_uctext.update;
		if(isNew == true)
			buttonText = g_uctext.set_addon;
		
		//update button
		buttonOpts[buttonText] = function(){
			
			var addonData = g_objBrowser.getCurrentAddonData();
			if(addonData){
				
				if(isNew == true)
					addColAddon(objElement, addonData);
				else{
					updateColAddon(objElement, addonData);
				}
				
			}
			
			g_dialogBrowser.dialog("close");
		}
		
		
		g_dialogBrowser.dialog({
			
			dialogClass:"unite-ui",			
			buttons:buttonOpts,
			minWidth:1000,
			modal:true,
			create:function () {
				
				var objButton = g_ucAdmin.dialogAddLeftButton(jQuery(this),"Choose Another Addon");
				g_objBrowser.setObjBackButton(objButton);
				
		    },			
			open:function(){
				
				g_objBrowser.onShowBrowser();
				
				if(isNew == false){
					var objData = getColAddonData(objElement);
					g_objBrowser.setAddonMode(objData, true);
				}else{
					g_objBrowser.setBrowserMode();
				}
					
			}
		});
		
	}
	
		
	
	function ____________________GET_DATA________________(){}
	
		
	
	/**
	 * get columns data
	 */
	function getGridData_cols(objRow){
		
		var objCols = getCols(objRow);
		var dataCols = [];
		
		//create col data
		jQuery.each(objCols,function(colIndex, col){
			var objCol = jQuery(col);
			
			var dataCol = {};
			dataCol.addon_data = getColAddonsData(objCol);
			dataCols.push(dataCol);
		});
		
		return(dataCols);
	}
	
	
	/**
	 * get row data
	 */
	function getGridData_row(objRow){
		var dataRow = {};
		dataRow.cols = getGridData_cols(objRow);
		
		var rowSettings = objRow.data("settings");
		if(rowSettings)
			dataRow.settings = rowSettings;
		
		return(dataRow);
	}
	
	
	/**
	 * get grid rows
	 */
	function getGridData_rows(){
		var dataRows = [];
		var objRows = getRows();
				
		jQuery.each(objRows, function(index, row){
			var objRow = jQuery(row);
			var dataRow = getGridData_row(objRow);
			
			dataRows.push(dataRow);
			
		});
		
		return(dataRows);
	}
	
	
	/**
	 * get grid data
	 */
	function getGridData(){
		var data = {};
		data.rows = getGridData_rows();
		
		if(g_optionsCustom)
			data.options = g_optionsCustom;
		
		
		return(data);
	}
	
	
	/**
	 * get grid data
	 */
	this.getGridData = function(){
		
		var objData = getGridData();
		
		return(objData);
	}
	
	function ____________GRID_SETTINGS______________(){}
	
	
	/**
	 * get grid option
	 */
	function getGridOption(name){
		
		var gridOptions = getCombinedOptions();
				
		var value = g_ucAdmin.getVal(gridOptions, name);
		
		return(value);
	}
	
	
	/**
	 * get combined options
	 */
	function getCombinedOptions(){
		
		if(!g_optionsCustom)
			g_optionsCustom = {};
				
		var objOptions = {};
		jQuery.extend(objOptions, g_options, g_optionsCustom);
		
		return(objOptions);
	}
	
	
	/**
	 * put css based on the options
	 */
	function putGeneratedCss(){
		var br = "\n";
		var tab = "	    ";
		var objOptions = getCombinedOptions();
				
		var css = "";
		
		g_ucAdmin.validateObjProperty(objOptions, ["col_gutter",
		                                           "col_border_gutter",
		                                           "row_gutter",
		                                           "row_container_width",
		                                           "row_title_global_css",
		                                           "row_title_css",
		                                           "row_titles_css_type"
		              ],"grid options");
				
		//row css
		css += g_gridID+" .uc-grid-row{"+br;
			css += tab+"padding-top:"+objOptions.row_gutter+"px;"+br;
			css += tab+"padding-bottom:"+objOptions.row_gutter+"px;"+br;
		css += "}"+br+br;
		
		//row title css
		
		var rowTitleCss = jQuery.trim(objOptions.row_title_global_css);
		var rowTitleLocalCss = jQuery.trim(objOptions.row_title_css)
		
		if(objOptions.row_titles_css_type == "override")
			rowTitleCss = rowTitleLocalCss;
		else{		//add
			rowTitleCss += rowTitleLocalCss;
		}
		
		css += g_gridID+" .uc-grid-row .uc-grid-row-title{"+br;
		css += rowTitleCss
		css += "}"+br+br;
		
		//row container css
		css += g_gridID+" .uc-grid-row .uc-grid-row-container{"+br;
		
		css += tab+"width:"+g_ucAdmin.normalizeSizeValue(objOptions.row_container_width)+";"+br;
		
		css += "}"+br+br;
		
		
		//column css
		css += g_gridID+" .uc-grid-row .uc-grid-col{"+br;
			
		//add gutter
		css += tab+"padding-left:"+objOptions.col_gutter+"px;"+br;
		css += tab+"padding-right:"+objOptions.col_gutter+"px;"+br;
		css += "}"+br;
		
		//first and last column
		
		var borderGutter = objOptions.col_border_gutter;
		borderGutter = jQuery.trim(borderGutter);
		
		if(borderGutter !== ""){
			
			//first column
			css += g_gridID+" .uc-grid-row .uc-grid-col.uc-col-first{"+br;
			css += tab+"padding-left:"+borderGutter+"px;"+br;
			css += "}"+br;
			
			//last column
			css += g_gridID+" .uc-grid-row .uc-grid-col.uc-col-last{"+br;
			css += tab+"padding-right:"+borderGutter+"px;"+br;
			css += "}"+br;
		}
		
		g_objStyle.html(css);
	}
	
	
	/**
	 * on grid settings click
	 */
	function openGridSettingsDialog(){
		
		var dialogOptions = {
				minWidth:750
		};
		
		jQuery("#uc_dialog_grid_settings_action").show();
		jQuery("#uc_dialog_grid_settings_success").hide();
		
		g_ucAdmin.openCommonDialog("#uc_dialog_grid_settings" , null , dialogOptions);
		
	}
	
	
	/**
	 * update options from settings dialog
	 */
	function updateOptionsFromSettingsDialog(){
		
		var objValues = g_objSettingsGrid.getSettingsValues();
		
		//update custom options, skip empty values
		g_optionsCustom = {};
		
		jQuery.each(objValues, function(option, val){
			if(!val || jQuery.trim(val) == "")
				return(true);
			
			//convert to int
			if(typeof val == "string" && jQuery.isNumeric(val))
				val = parseInt(val);
			
			g_optionsCustom[option] = val;
		});
		
		
		putGeneratedCss();
		
	}
	
	
	/**
	 * on update grid settings click
	 */
	function onUpdateGridSettingsClick(){
		
		//visualize close
		jQuery("#uc_dialog_grid_settings_action").hide();
		jQuery("#uc_dialog_grid_settings_success").show();
		
		setTimeout(function(){
			
			jQuery("#uc_dialog_grid_settings").dialog("close");
			
			updateOptionsFromSettingsDialog();
			updateAllRowsVisual();
			
		}, 300);
		
		
	}
	
	
	/**
	 * init grid settings related, style, options, dialogs
	 */
	function initGridSettings(){
		
		//init style object
		g_objStyle = objWrapper.children("style");
		g_ucAdmin.validateDomElement(g_objStyle, "style tag");
		
		//init options
		g_options = g_objGrid.data("options");
		if(!g_options)
			throw new Error("Should be passed some options!");
		
		g_objGrid.removeAttr("data-options");	//remove attribute for not interfere
		
		//init settings object
		var objSettingsWrapper = jQuery("#uc_settings_grid");
		g_ucAdmin.validateDomElement(objSettingsWrapper, "settings wrapper");
		
		g_objSettingsGrid.init(objSettingsWrapper);
		
		//init settings related events:
		jQuery("#uc_button_grid_settings").click(openGridSettingsDialog);
		
		jQuery("#uc_dialog_grid_settings_action").click(onUpdateGridSettingsClick);
		
	}
	
	function ____________ROW_SETTINGS______________(){}
	
	
	/**
	 * open row settings dialog
	 */
	function openRowSettingsDialog(objRow){
		
		var dialogOptions = {
				minWidth:700
		};
		
		jQuery("#uc_dialog_row_settings_action").show();
		jQuery("#uc_dialog_row_settings_success").hide();
		
		g_objDialogRowSettings.data("active_row", objRow);
		
		g_ucAdmin.openCommonDialog("#uc_dialog_row_settings" , function(){
			
			//on open - set setting values
			var objSettingsData = getRowSettings(objRow);
			g_objSettingsRow.setValues(objSettingsData);
			
		} , dialogOptions);
		
	}
		
	
	/**
	 * on update row settings click
	 */
	function onUpdateRowSettingsClick(){
		
		//visualize close
		jQuery("#uc_dialog_row_settings_action").hide();
		jQuery("#uc_dialog_row_settings_success").show();
		
		setTimeout(function(){
			
			jQuery("#uc_dialog_row_settings").dialog("close");
			
			var objRow = g_objDialogRowSettings.data("active_row");
			if(!objRow)
				throw new Error("Active row not found");
			
			var objSettingsData = g_objSettingsRow.getSettingsValues();
			
			updateRowSettings(objRow, objSettingsData);
						
		}, 300);
		
	}
	
	
	/**
	 * init grid settings related, style, options, dialogs
	 */
	function initRowSettings(){
		
		g_objDialogRowSettings = jQuery("#uc_dialog_row_settings");
		
		g_ucAdmin.validateDomElement(g_objDialogRowSettings, "row settings dialog");
		
		var objSettingsWrapper = jQuery("#uc_settings_grid_row");
		g_ucAdmin.validateDomElement(objSettingsWrapper, "row settings wrapper");
		g_objSettingsRow.init(objSettingsWrapper);
		
		//init settings related events:
		
		jQuery("#uc_dialog_row_settings_action").click(onUpdateRowSettingsClick);
		
	}
	
	
	function ____________EVENTS______________(){}
	
	
	/**
	 * grigger event
	 */
	function triggerEvent(eventName, options){
		
		g_objGrid.trigger(eventName, options);
	
	}
	
	
	/**
	 * on some event
	 */
	function onEvent(eventName, func){
		
		g_objGrid.on(eventName, func);
		
	}
	
	
	/**
	 * on rows updated
	 * happends on add / update / delete / reorder row
	 */
	function onRowsUpdated(event){
		
		updateRowsClasses();
		
	}
	
	
	/**
	 * on add column
	 */
	function onRowColumnsUpdated(event, objRow){
		
		objRow = jQuery(objRow);
		
		updateColsClasses(objRow);
		
		updateColOperationButtons(objRow);
	}
	
	
	/**
	 * on col or row action icon click
	 */
	function onActionIconClick(){
		
		var objIcon = jQuery(this);
		
		var action = objIcon.data("action");
		var actionType = objIcon.data("actiontype");
		
		if(!action || action == "")
			throw new Error("wrong icon action");
		
		switch(actionType){
			case "grid":
				doGridAction(action);				
			break;
			case "col":
				var objAddon = getParentCol(objIcon);
				doColAction(objAddon, action);
			break;
			case "row":
				var objRow = getParentRow(objIcon);
				doRowAction(action, objRow);
			break;
			case "addon":
				var objAddon = getParentAddonElement(objIcon);
				
				doAddonAction(objAddon, action);
			break;
			default:
				throw new Error("Wrong action type: " + actionType);
			break;
		}
		
				
	}
	
	
	/**
	 * on row mouse over
	 */
	function onRowMouseOver(){
		
		var objRow = jQuery(this);
		objRow.addClass("uc-row-over");
		
	}

	
	/**
	 * on row mouse out
	 */
	function onRowMouseOut(){
		
		var objRow = jQuery(this);
		objRow.removeClass("uc-row-over");
		
	}
	
	
	/**
	 * on column mouse enter
	 */
	function onColAddonMouseOver(){
		
		var objAddon = jQuery(this);
		
		showAddonOverlay(objAddon, true);
	}
	
		
	
	/**
	 * on column mouse out
	 */
	function onColAddonMouseOut(){
		
		var objAddon = jQuery(this);
		
		showAddonOverlay(objAddon, false);
	}
	
	
	/**
	 * init new row events
	 */
	function initNewRowEvents(event, objRow){
		
		jQuery(objRow).sortable({
			items: ".uc-grid-col",
			handle: ".uc-col-icon-move",
			cursor: "move",
			axis: "x",
			update: function(event, ui){
				var objCol = ui.item;
				var objRow = getParentRow(objCol);
				
				triggerEvent(t.events.ROW_COLUMNS_UPDATED, objRow);
				
			}
		});
			
	}
	
	
	/**
	 * init new column events - set the addons sortable
	 */
	function initNewColEvents(event, col){
		
		var objCol = jQuery(col);
		var objAddonsWrapper = getColAddonsWrapper(objCol);
		
		jQuery(objAddonsWrapper).sortable({
			items: ".uc-grid-col-addon",
			handle: ".uc-addon-icon-move",
			cursor: "move",
			axis: "y",
			update: function(event, ui){
				var objAddon = ui.item;
				var objCol = getParentCol(objAddon);
				
				triggerEvent(t.events.COL_ADDONS_UPDATED, objCol);
			}
		});
			
	}
	
	
	/**
	 * init the events
	 */
	function initEvents(){
		
		onEvent(t.events.ROW_COLUMNS_UPDATED, onRowColumnsUpdated);
		onEvent(t.events.ROWS_UPDATED, onRowsUpdated);
		onEvent(t.events.ROW_ADDED, initNewRowEvents);
		onEvent(t.events.COL_ADDED, initNewColEvents);
		
		g_objGrid.delegate(".uc-grid-col .uc-grid-col-addon", "mouseenter", onColAddonMouseOver);
		g_objGrid.delegate(".uc-grid-col .uc-grid-col-addon", "mouseleave", onColAddonMouseOut);
		
		g_objGrid.delegate(".uc-grid-row","mouseenter",onRowMouseOver);
		g_objGrid.delegate(".uc-grid-row","mouseleave",onRowMouseOut);
		
		jQuery("#uc_grid_builder_bottom_panel .uc-grid-action-icon").click(onActionIconClick);
		g_objGrid.delegate(".uc-grid-action-icon", "click", onActionIconClick);
				
		
		//init sortable
		g_objGrid.sortable({
			handle: ".uc-row-icon-move",
			axis: "y",
			update: function(){
				triggerEvent(t.events.ROWS_UPDATED);
			}
		});	
		
	}
	
	
	/**
	 * init tipsy
	 */
	function initTipsy(){
		
		if(typeof jQuery("body").tipsy != "function")
			return(false);
		
		var tipsyOptions = {
				html:true,
				gravity:"s",
		        delayIn: 1000,
		        selector: ".uc-tip"
		};
		
		g_objGrid.tipsy(tipsyOptions);
		
	}
	
	
	function ____________INIT______________(){}
	
	
	
	/**
	 * init rows
	 */
	function initByData_rows(rows){
		
		jQuery.map(rows, function(row){
			
			var objRow = addEmptyRow();
			
			if(row.hasOwnProperty("settings") && typeof row.settings == "object"){
				updateRowSettings(objRow, row.settings);
			}
			
			g_ucAdmin.validateObjProperty(row, "cols");
			
			var cols = row.cols;
			
			jQuery.map(cols,function(col){
				
				var objCol = addColumn(objRow);
				var addonsData = col.addon_data;
				
				if(jQuery.isArray(addonsData)){
					
					//add addons
					jQuery.map(addonsData, function(addonData){
						addColAddon(objCol, addonData);
					});
					
				}else{
							//single - old way
					if(addonsData)
						addColAddon(objCol, addonsData);
				}
				
			});
			
		});
		
	}
	
	
	/**
	 * init options by data
	 */
	function initByData_options(options){
		
		g_optionsCustom = options;
		
		g_objSettingsGrid.setValues(g_optionsCustom);
		
	}
	
	
	/**
	 * init the builder by data
	 */
	function initByData(initData){
		
		g_ucAdmin.validateObjProperty(initData, "rows");
		
		//init options
		if(initData.hasOwnProperty("options"))
			initByData_options(initData.options);
		
		//init rows
		if(initData.hasOwnProperty("rows"))
			initByData_rows(initData.rows);
				
	}
	
	
	/**
	 * init grid
	 */
	this.init = function(gridID){
		
		g_objGrid = jQuery(gridID);
		if(g_objGrid.length == 0)
			throw new Error("grid object: " + gridID + " not found");
		
		g_gridID = gridID;
		
		objWrapper = g_objGrid.parents(".uc-grid-builder-wrapper");
		
		//init browser
		g_dialogBrowser = objWrapper.find(".uc-grid-builder-dialog-browser");
		g_ucAdmin.validateDomElement(g_dialogBrowser, "dialog browser");
		
		var browserWrapper = objWrapper.find(".uc-browser-wrapper");
		
		g_objBrowser.init(browserWrapper);
		
		initEvents();
				
		initGridSettings();
		initRowSettings();
		
		//add the data
		var initData = g_objGrid.data("init");
		if(initData){
			initByData(initData);
			g_objGrid.removeAttr("data-init");   //remove attribute for not interfere
		}
		else
			addRow();

		//put the css by the options
		putGeneratedCss();
		
		//initTipsy();
		
	}
	
	
}