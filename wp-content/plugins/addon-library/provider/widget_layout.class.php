<?php

// no direct access
defined('ADDON_LIBRARY_INC') or die;


class AddonLibrary_WidgetLayout extends WP_Widget {
	
    public function __construct(){
    	
        // widget actual processes
     	$widget_ops = array('classname' => 'widget_addonlibrary_layout', 'description' => __('Show Addon Library Layout') );
        parent::__construct('addonlibrary-widget', __('Addon Library Layout', ADDONLIBRARY_TEXTDOMAIN), $widget_ops);
    }

    
    /**
     * 
     * the form
     */
    public function form($instance) {
		
    	$objLayouts = new UniteCreatorLayouts();
    	$arrLayouts = $objLayouts->getArrLayoutsShort(true);
    	$fieldID = "addonlibrarylayoutid";
    	$layoutID = UniteFunctionsUG::getVal($instance, $fieldID);
    	
    	if(empty($arrLayouts)){
    		?>
    		<div style="padding-top:10px;padding-bottom:10px;">
    		<?php _e("No layouts found, Please create a layout", ADDONLIBRARY_TEXTDOMAIN); ?>
    		</div>
    		<?php }
    	else{
    		$fieldOutputID = $this->get_field_id( $fieldID );
    		$fieldOutputName = $this->get_field_name( $fieldID );
    		
    		$selectLayouts = HelperHtmlUC::getHTMLSelect($arrLayouts, $layoutID,'name="'.$fieldOutputName.'" id="'.$fieldOutputID.'"',true);
    		?>
				<div style="padding-top:10px;padding-bottom:10px;">
				
				<?php _e("Title", ADDONLIBRARY_TEXTDOMAIN)?>: 
				&nbsp; <input type="text" id="<?php echo $this->get_field_id( "title" );?>" name="<?php echo $this->get_field_name( "title" )?>" value="<?php echo UniteFunctionsUC::getVal($instance, 'title')?>" />
				
				<br><br>
				
				<?php _e("Choose a Layout", ADDONLIBRARY_TEXTDOMAIN)?>: 
				<?php echo $selectLayouts?>
				
				</div>
				
				<br>
    		
    		<?php 
    	}

    }
 
    
    /**
     * 
     * update
     */
    public function update($new_instance, $old_instance) {
    	
        return($new_instance);
    }

    
    /**
     * 
     * widget output
     */
    public function widget($args, $instance) {
    	
    	$title = UniteFunctionsUC::getVal($instance, "title");
		    	
    	$layoutID =  UniteFunctionsUC::getVal($instance, "addonlibrarylayoutid");
    	
    	if(empty($layoutID))
    		return(false);
    	    	
    	//widget output
    	$beforeWidget = UniteFunctionsUC::getVal($args, "before_widget");
    	$afterWidget = UniteFunctionsUC::getVal($args, "after_widget");
    	$beforeTitle = UniteFunctionsUC::getVal($args, "before_title");
    	$afterTitle = UniteFunctionsUC::getVal($args, "after_title");
    	
    	echo $beforeWidget;
    	
    	if(!empty($title))
    		echo $beforeTitle.$title.$afterTitle;
    	
    	if(is_numeric($layoutID) == false)
    		_e("no layout selected", ADDONLIBRARY_TEXTDOMAIN);
    	else
    		HelperUC::outputLayout($layoutID);
 		
    	echo $afterWidget;
    }
 
    
}


?>