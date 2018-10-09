<?php 

$urlLayoutsList = HelperUC::getViewUrl_LayoutsList();
$urlEdit = HelperUC::getViewUrl_Layout($layoutID);

if($this->showHeader == true){
	$headerTitle = $this->getHeaderTitle();
	require HelperUC::getPathTemplate("header");
}

?>


<div class="unite-content-wrapper unite-inputs">
	
		<?php if($this->showToolbar == true):?>
		
			<a href="<?php echo $urlLayoutsList?>" class="unite-button-secondary"><?php _e("Back to layouts list",ADDONLIBRARY_TEXTDOMAIN)?></a>
			<a href="<?php echo $urlEdit?>" class="mleft_10 unite-button-secondary"><?php _e("Edit Layout",ADDONLIBRARY_TEXTDOMAIN)?></a>
		
		<?php endif?>
		
		
		<div class="uc-layout-preview-wrapper">
		
			<?php HelperUC::outputLayout($layoutID); ?>
			
			<div class="unite-clear"></div>
		</div>
		
</div>
