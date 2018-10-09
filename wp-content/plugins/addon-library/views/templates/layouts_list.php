
<?php

if($this->showHeaderTitle == true){
	$headerTitle = __("Manage Layouts", ADDONLIBRARY_TEXTDOMAIN);
	require HelperUC::getPathTemplate("header");
}

$urlViewCreateObject = HelperUC::getViewUrl_Layout();

$urlLayouts = HelperUC::getViewUrl_LayoutsList();

$objTable->setPagingData($urlLayouts, $pagingData);

$urlManageAddons = HelperUC::getViewUrl_Addons();

$sizeActions = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_LAYOUTS_ACTIONS_COL_WIDTH, 380);


?>

	<div class="unite-content-wrapper">
		
		<?php if($this->showButtonsPanel == true):?>
		
			<a href="<?php echo $urlViewCreateObject?>" class="unite-button-primary unite-float-left"><?php _e("New Layout", ADDONLIBRARY_TEXTDOMAIN)?></a>
			
			<a id="uc_button_import_layout" href="javascript:void(0)" class="unite-button-secondary unite-float-left mleft_20"><?php _e("Import Layout", ADDONLIBRARY_TEXTDOMAIN)?></a>
			
			<a href="javascript:void(0)" id="uc_layouts_global_settings" class="unite-float-right mright_20 unite-button-secondary"><?php _e("Layouts Global Settings", ADDONLIBRARY_TEXTDOMAIN)?></a>
			<a href="<?php echo $urlManageAddons?>" class="unite-float-right mright_20 unite-button-secondary"><?php _e("My Addons", ADDONLIBRARY_TEXTDOMAIN)?></a>
		
			<div class="vert_sap20"></div>
			
		<?php endif?>
		
		<?php if(empty($arrLayouts)): ?>
		<div>
			<?php _e("No Layouts Found", ADDONLIBRARY_TEXTDOMAIN)?>
		</div>			
		<?php else:?>
		
			<table id="uc_table_layouts" class='unite_table_items' data-text-delete="<?php _e("Are you sure to delete this layout?", ADDONLIBRARY_TEXTDOMAIN)?>">
				<thead>
					<tr>
						<th width=''><?php _e("Layout Title",ADDONLIBRARY_TEXTDOMAIN); ?></th>
						<th width='200'><?php _e("Shortcode",ADDONLIBRARY_TEXTDOMAIN); ?></th>
						<th width='<?php echo $sizeActions?>'><?php _e("Actions",ADDONLIBRARY_TEXTDOMAIN); ?></th>
						<th width='60'><?php _e("Preview",ADDONLIBRARY_TEXTDOMAIN); ?></th>						
					</tr>
				</thead>
				<tbody>
					<?php foreach($arrLayouts as $key=>$layout):
							
						$id = $layout->getID();
																
						$title = $layout->getTitle();

						$shortcode = $layout->getShortcode();
						$shortcode = UniteFunctionsUC::sanitizeAttr($shortcode);
						
						$editLink = HelperUC::getViewUrl_Layout($id);
						
						$previewLink = HelperUC::getViewUrl_LayoutPreview($id, true);
						
						$showTitle = HelperHtmlUC::getHtmlLink($editLink, $title);
						
						$rowClass = ($key%2==0)?"unite-row1":"unite-row2";
												
					?>
						<tr class="<?php echo $rowClass?>">
							<td><?php echo $showTitle?></td>
							<td>
								<input type="text" readonly onfocus="this.select()" class="unite-input-medium unite-cursor-text" value="<?php echo $shortcode?>" />
							</td>
							<td>
								<a href='<?php echo $editLink?>' class="unite-button-primary float_left mleft_15"><?php _e("Edit Layout",ADDONLIBRARY_TEXTDOMAIN); ?></a>
								
								<a href='javascript:void(0)' data-layoutid="<?php echo $id?>" data-id="<?php echo $id?>" class="button_delete unite-button-secondary float_left mleft_15"><?php _e("Delete",ADDONLIBRARY_TEXTDOMAIN); ?></a>
								<span class="loader_text uc-loader-delete" style="display:none"><?php _e("Deleting", ADDONLIBRARY_TEXTDOMAIN)?></span>
								<a href='javascript:void(0)' data-layoutid="<?php echo $id?>" data-id="<?php echo $id?>" class="button_duplicate unite-button-secondary float_left mleft_15"><?php _e("Duplicate",ADDONLIBRARY_TEXTDOMAIN); ?></a>
								<span class="loader_text uc-loader-duplicate" style="display:none"><?php _e("Duplicating", ADDONLIBRARY_TEXTDOMAIN)?></span>
								<a href='javascript:void(0)' data-layoutid="<?php echo $id?>" data-id="<?php echo $id?>" class="button_export unite-button-secondary float_left mleft_15"><?php _e("Export",ADDONLIBRARY_TEXTDOMAIN); ?></a>
								<?php UniteProviderFunctionsUC::doAction(UniteCreatorFilters::ACTION_LAYOUTS_LIST_ACTIONS, $id); ?>
							</td>
							<td>
								<a href='<?php echo $previewLink?>' target="_blank" class="unite-button-secondary float_left"><?php _e("Preview",ADDONLIBRARY_TEXTDOMAIN); ?></a>					
							</td>
						</tr>							
					<?php endforeach;?>
					
				</tbody>		 
			</table>
			
			<?php 
				$objTable->putPaginationHtml();
			?>
			
		<?php endif?>
		
		<?php 
			$gridBuilder->putLayoutsGlobalSettingsDialog();
			
			$this->putDialogImportLayout();
		?>
		
		
	</div>
	
<script type="text/javascript">

	jQuery(document).ready(function(){

		var objAdmin = new UniteCreatorAdmin_LayoutsList();
		objAdmin.initObjectsListView();
		
	});

</script>

