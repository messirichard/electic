<?php

defined('ADDON_LIBRARY_INC') or die;

class UniteCreatorLayoutsView{
	
	protected $showButtonsPanel = true;
	protected $showHeaderTitle = true;
	
	/**
	 * constructor
	 */
	public function __construct(){
		
	}
	
	/**
	 * put import addons dialog
	 */
	public function putDialogImportLayout(){
	
		$dialogTitle = __("Import Layouts",ADDONLIBRARY_TEXTDOMAIN);
		
		?>
		
			<div id="uc_dialog_import_layouts" class="unite-inputs" title="<?php echo $dialogTitle?>" style="display:none;">
				
				<div class="unite-dialog-top"></div>
				
				<div class="unite-inputs-label">
					<?php _e("Select layouts export file", ADDONLIBRARY_TEXTDOMAIN)?>:
				</div>
				
				<form id="dialog_import_layouts_form" name="form_import_layouts">
					<input id="dialog_import_layouts_file" type="file" name="import_layout">
							
				</form>	
				
				<div class="unite-inputs-sap-double"></div>
				
				<div class="unite-inputs-label" >
					<label for="dialog_import_layouts_file_overwrite">
						<?php _e("Overwrite Addons", ADDONLIBRARY_TEXTDOMAIN)?>:
					</label>
					<input type="checkbox" id="dialog_import_layouts_file_overwrite"></input>
				</div>
				
				
				<div class="unite-clear"></div>
				
				<?php 
					$prefix = "uc_dialog_import_layouts";
					$buttonTitle = __("Import Layouts", ADDONLIBRARY_TEXTDOMAIN);
					$loaderTitle = __("Uploading layouts file...", ADDONLIBRARY_TEXTDOMAIN);
					$successTitle = __("Layouts Added Successfully", ADDONLIBRARY_TEXTDOMAIN);
					HelperHtmlUC::putDialogActions($prefix, $buttonTitle, $loaderTitle, $successTitle);
				?>
					
			</div>		
		
	<?php
	}
	
	
	
	/**
	* display layouts view
	 */
	public function display(){
		
		//table object
		$objTable = new UniteTableUC();
		
		$objLayouts = new UniteCreatorLayouts();
		$gridBuilder = new UniteCreatorGridBuilderProvider();
		
		$pagingOptions = $objTable->getPagingOptions();
		
		$response = $objLayouts->getArrLayoutsPaging($pagingOptions);
		
		$arrLayouts = $response["layouts"];
		$pagingData = $response["paging"];
		
		
		require HelperUC::getPathTemplate("layouts_list");		
	}
	
}

