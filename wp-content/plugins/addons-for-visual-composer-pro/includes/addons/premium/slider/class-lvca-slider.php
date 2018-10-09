<?php

/*
Widget Name: Slider
Description: Create a responsive slider of custom HTML content.
Author: LiveMesh
Author URI: https://www.livemeshthemes.com
*/

class LVCA_Slider {

    /**
     * Get things started
     */
    public function __construct() {

        add_action('wp_enqueue_scripts', array($this, 'load_scripts'), 15); // load a little later than common styles for the plugin

        add_shortcode('lvca_slider', array($this, 'shortcode_func'));

        add_shortcode('lvca_slide', array($this, 'child_shortcode_func'));

        add_action('init', array($this, 'map_vc_element'));

        add_action('init', array($this, 'map_child_vc_element'));

    }

    function load_scripts() {

        wp_enqueue_script('lvca-flexslider', LVCA_PLUGIN_URL . 'assets/js/jquery.flexslider' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_style('lvca-flexslider', LVCA_PLUGIN_URL . 'assets/css/flexslider.css', array(), LVCA_VERSION);

        wp_enqueue_script('lvca-slider', plugin_dir_url(__FILE__) . 'js/slider' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_style('lvca-slider', plugin_dir_url(__FILE__) . 'css/style.css', array(), LVCA_VERSION);

    }

    public function shortcode_func($atts, $content = null, $tag) {

        $id = '';

        $defaults = array_merge(
            array(
                'id' => '',
                'class' => '',
                'animation' => 'slide',
                'direction' => 'horizontal',
                'control_nav' => '',
                'direction_nav' => '',
                'randomize' => '',
                'pause_on_hover' => '',
                'pause_on_action' => '',
                'loop' => '',
                'slideshow' => '',
                'slideshow_speed' => 5000,
                'animation_speed' => 600
            )

        );

        $settings = shortcode_atts($defaults, $atts);

        if (!empty($settings['id']))
            $id = ' id="' . esc_attr($settings['id']) . '"';

        ob_start();

        ?>

        <div <?php echo esc_html($id); ?>
                class="lvca-slider lvca-container <?php echo esc_attr($settings['class']); ?>"
                data-settings='<?php echo wp_json_encode($settings); ?>'>

            <div class="lvca-flexslider">

                <div class="lvca-slides">

                    <?php

                    do_shortcode($content);

                    ?>

                </div>

            </div>

        </div>

        <?php

        $output = ob_get_clean();

        return $output;
    }

    public function child_shortcode_func($atts, $content = null, $tag) {

        extract(shortcode_atts(array(
            'title' => '',

        ), $atts));

        ?>

        <?php if (!empty($content)): ?>

            <div class="lvca-slide">

                <?php echo do_shortcode(wp_kses_post($content)); ?>

            </div>

        <?php endif; ?>

        <?php
    }

    function map_vc_element() {

        if (function_exists("vc_map")) {

            $general_params = array(

                array(
                    "param_name" => "id",
                    "type" => "textfield",
                    "heading" => __("Slider ID", 'livemesh-vc-addons'),
                    "description" => __("Set a ID for the slider. (optional).", 'livemesh-vc-addons')
                ),

                array(
                    "param_name" => "class",
                    "type" => "textfield",
                    "heading" => __("Slider Class", 'livemesh-vc-addons'),
                    "description" => __("Custom CSS class name to be set for the slider (optional)", 'livemesh-vc-addons')
                ),
            );

            $settings_params = array(

                array(
                    "param_name" => "animation",
                    "type" => "dropdown",
                    "heading" => __("Slider Animation", 'livemesh-vc-addons'),
                    "value" => array(
                        __("Slide", 'livemesh-vc-addons') => "slide",
                        __("Fade", 'livemesh-vc-addons') => "fade"
                    ),
                    "std" => "slide",
                    'group' => __('Settings', 'livemesh-vc-addons')
                ),

                array(
                    "param_name" => "direction",
                    "type" => "dropdown",
                    "heading" => __("Sliding Direction", 'livemesh-vc-addons'),
                    "description" => __("Select the sliding direction.", 'livemesh-vc-addons'),
                    "value" => array(
                        __("Horizontal", 'livemesh-vc-addons') => "horizontal",
                        __("Vertical", 'livemesh-vc-addons') => "vertical"
                    ),
                    "std" => "horizontal",
                    'group' => __('Settings', 'livemesh-vc-addons')
                ),

                array(
                    'type' => 'checkbox',
                    "param_name" => "direction_nav",
                    'heading' => __('Direction Navigation', 'livemesh-vc-addons'),
                    'description' => __('Should the slider have direction navigation?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Settings', 'livemesh-vc-addons')
                ),

                array(
                    'type' => 'checkbox',
                    "param_name" => "control_nav",
                    'heading' => __('Navigation Controls', 'livemesh-vc-addons'),
                    'description' => __('Should the slider have navigation controls?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Settings', 'livemesh-vc-addons')
                ),

                array(
                    'type' => 'checkbox',
                    "param_name" => "randomize",
                    'heading' => __('Randomize slides?', 'livemesh-vc-addons'),
                    'description' => __('Randomize slide order?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Settings', 'livemesh-vc-addons')
                ),

                array(
                    'type' => 'checkbox',
                    "param_name" => "pause_on_hover",
                    'heading' => __('Pause on Hover', 'livemesh-vc-addons'),
                    'description' => __('Should the slider pause on mouse hover over the slider.', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Settings', 'livemesh-vc-addons')
                ),

                array(
                    'type' => 'checkbox',
                    "param_name" => "pause_on_action",
                    'heading' => __('Pause slider on action.', 'livemesh-vc-addons'),
                    'description' => __('Should the slideshow pause once user initiates an action using navigation/direction controls.', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Settings', 'livemesh-vc-addons')
                ),

                array(
                    'type' => 'checkbox',
                    "param_name" => "loop",
                    'heading' => __('Loop?', 'livemesh-vc-addons'),
                    'description' => __('Should the animation loop?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Settings', 'livemesh-vc-addons')
                ),

                array(
                    'type' => 'checkbox',
                    "param_name" => "slideshow",
                    'heading' => __('Slideshow?', 'livemesh-vc-addons'),
                    'description' => __('Animate slider automatically without user intervention.', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Settings', 'livemesh-vc-addons')
                ),

                array(
                    'type' => 'lvca_number',
                    "param_name" => "slideshow_speed",
                    'heading' => __('Slideshow speed', 'livemesh-vc-addons'),
                    'value' => 5000,
                    "min" => 500,
                    "max" => 10000,
                    "suffix" => 'milliseconds',
                    'group' => __('Settings', 'livemesh-vc-addons')
                ),

                array(
                    'type' => 'lvca_number',
                    "param_name" => "animation_speed",
                    'heading' => __('Animation Speed', 'livemesh-vc-addons'),
                    'value' => 600,
                    "min" => 100,
                    "max" => 1200,
                    "suffix" => 'milliseconds',
                    'group' => __('Settings', 'livemesh-vc-addons')
                ),
            );

            $params = array_merge($general_params, $settings_params);

            //Register "container" content element. It will hold all your inner (child) content elements
            vc_map(array(
                "name" => __("Slider", "livemesh-vc-addons"),
                "base" => "lvca_slider",
                "as_parent" => array('only' => 'lvca_slide'), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
                "content_element" => true,
                "show_settings_on_create" => true,
                "category" => __("Livemesh Addons", "livemesh-vc-addons"),
                "is_container" => true,
                'description' => __('Create a responsive slider of custom HTML content.', 'livemesh-vc-addons'),
                "js_view" => 'VcColumnView',
                "icon" => 'icon-lvca-slider',
                "params" => $params
            ));

        }
    }


    function map_child_vc_element() {
        if (function_exists("vc_map")) {

            vc_map(array(
                    "name" => __("Slide", "livemesh-vc-addons"),
                    "base" => "lvca_slide",
                    "as_child" => array('only' => 'lvca_slider'), // Use only|except attributes to limit parent (separate multiple values with comma)
                    "icon" => 'icon-lvca-slider-add',
                    "category" => __("Livemesh Addons", "livemesh-vc-addons"),
                    "params" => array(

                        array(
                            'type' => 'textfield',
                            'param_name' => 'title',
                            'heading' => __('Title', 'livemesh-vc-addons'),
                            'admin_label' => true,
                            'description' => __('The title to identify the slide', 'livemesh-vc-addons'),
                        ),

                        array(
                            'type' => 'textarea_html',
                            'param_name' => 'content',
                            'heading' => __('HTML Content', 'livemesh-vc-addons'),
                            'description' => __('The HTML content for the slide', 'livemesh-vc-addons'),
                        ),

                    )
                )
            );

        }
    }

}

//Your "container" content element should extend WPBakeryShortCodesContainer class to inherit all required functionality
if (class_exists('WPBakeryShortCodesContainer')) {
    class WPBakeryShortCode_lvca_slider extends WPBakeryShortCodesContainer {
    }
}
if (class_exists('WPBakeryShortCode')) {
    class WPBakeryShortCode_lvca_slide extends WPBakeryShortCode {
    }
}

// Initialize Element Class
if (class_exists('LVCA_Slider')) {
    new LVCA_Slider();
}