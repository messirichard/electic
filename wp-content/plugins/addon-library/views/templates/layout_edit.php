<?php

defined('ADDON_LIBRARY_INC') or die;

if($this->showHeader == true){
	$headerTitle = __("Edit Layout", ADDONLIBRARY_TEXTDOMAIN);
	require HelperUC::getPathTemplate("header");
}

$addHtml = "";
if(!empty($layoutID))
	$addHtml = "data-layoutid=\"{$layoutID}\"";
	
$urlLayoutsList = HelperUC::getViewUrl_LayoutsList();

if($this->isEditMode){
	$urlPreview = HelperUC::getViewUrl_LayoutPreview($layoutID);
}


?>

	<div class="unite-content-wrapper unite-inputs uc-content-layout">
		
		<?php if($this->showButtons == true):?>
		
				<a href="<?php echo $urlLayoutsList?>" class="unite-button-secondary"><?php _e("Back to layouts list",ADDONLIBRARY_TEXTDOMAIN)?></a>
				
				<?php if($this->isEditMode): ?>
				
				<a href="<?php echo $urlPreview?>" class="mleft_10 unite-button-secondary"><?php _e("Preview Layout",ADDONLIBRARY_TEXTDOMAIN)?></a>
				
				<a id="uc_button_import_layout" href="javascript:void(0)" class="unite-button-secondary unite-float-right mright_10"><?php _e("Import",ADDONLIBRARY_TEXTDOMAIN)?></a>
				
				<?php endif?>
				
				<div class="vert_sap30"></div>
		
		<?php endif?>
		
		<?php UniteProviderFunctionsUC::putInitHelperHtmlEditor()?>
		
		<div id="uc_edit_layout_wrapper" <?php echo $addHtml?> class='uc-edit-layout-panel'>
			
			<div class="unite-float-left">
		
			<?php _e("Layout Title: ", ADDONLIBRARY_TEXTDOMAIN)?>
				
				
			<input type="text" id="uc_layout_title" class="unite-input-regular" value="<?php echo UniteFunctionsUC::sanitizeAttr($title)?>">
			
			
			<?php if($this->isEditMode): ?>
				
				<input type="text" id="uc_layout_shortcode" class="unite-input-shortcode" data-shortcode="<?php echo GlobalsUC::$layoutShortcodeName?>" data-wrappers="<?php echo $this->shortcodeWrappers?>" readonly onfocus="this.select()" value="" title="<?php _e("Put the shortcode into article text. The layout output will replace it.", ADDONLIBRARY_TEXTDOMAIN)?>">
					
			<?php endif?>
			
			</div>
			
			<?php if($this->showButtons == true):?>
			
			<div class="uc-button-action-wrapper mleft_20">
				
				<a id="uc_button_update_layout" class="button_update_addon unite-button-primary" href="javascript:void(0)"><?php _e("Update Layout", ADDONLIBRARY_TEXTDOMAIN);?></a>
				
				<div style="padding-top:6px;">
					
					<span id="uc_loader_update" class="loader_text" style="display:none"><?php _e("Updating...", ADDONLIBRARY_TEXTDOMAIN)?></span>
					<span id="uc_message_addon_updated" class="unite-color-green" style="display:none"></span>
					
				</div>
								
			</div>
			
			<?php endif?>
			
			<div class="unite-clear"></div>
						
		</div>
		
		<div id="uc_update_addon_error" class="unite_error_message" style="display:none"></div>
				
		<div class="vert_sap20"></div>
		
			<?php 
				
				$objGridEditor->putGrid();
				
			?>
		
	</div>
	
	<?php 
		if($this->isEditMode){
			
			$objLayouts->putDialogImportLayout();
			
			UniteProviderFunctionsUC::doAction(UniteCreatorFilters::ACTION_LAYOUT_EDIT_HTML);
		}
?>
	
<script type="text/javascript">

	jQuery(document).ready(function(){

		var objAdmin = new UniteCreatorAdmin_Layout();
		objAdmin.initLayoutView();
		
	});

</script>

	