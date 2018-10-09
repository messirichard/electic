<?php

/*
Widget Name: FAQ
Description: Capture frequently asked questions in a multi-column grid.
Author: LiveMesh
Author URI: https://www.livemeshthemes.com
*/

class LVCA_FAQ {

    /**
     * Get things started
     */
    public function __construct() {

        add_action('wp_enqueue_scripts', array($this, 'load_scripts'));

        add_shortcode('lvca_faq', array($this, 'shortcode_func'));

        add_shortcode('lvca_faq_item', array($this, 'child_shortcode_func'));

        add_action('init', array($this, 'map_vc_element'));

        add_action('init', array($this, 'map_child_vc_element'));

    }

    function load_scripts() {

        wp_enqueue_style('lvca-faq', plugin_dir_url(__FILE__) . 'css/style.css', array(), LVCA_VERSION);
    }

    public function shortcode_func($atts, $content = null, $tag) {

        $per_line = $per_line_tablet = $per_line_mobile = $style = '';

        extract(shortcode_atts(array(
            'per_line' => '2',
            'per_line_tablet' => '1',
            'per_line_mobile' => '1',
            'style' => 'style1',

        ), $atts));

        $settings = array();

        $settings['per_line'] = $per_line;

        $settings['per_line_tablet'] = $per_line_tablet;

        $settings['per_line_mobile'] = $per_line_mobile;

        ob_start();

        ?>

        <div class="lvca-faq-list lvca-<?php echo $style; ?> lvca-grid-container <?php echo lvca_get_grid_classes($settings); ?>">

            <?php

            do_shortcode($content);

            ?>

        </div>

        <?php

        $output = ob_get_clean();

        return $output;
    }

    public function child_shortcode_func($atts, $content = null, $tag) {

        $question = $animation = '';

        extract(shortcode_atts(array(
            'question' => '',
            'animation' => 'none'
        ), $atts));

        list($animate_class, $animation_attr) = lvca_get_animation_atts($animation); ?>

        <div class="lvca-grid-item lvca-faq-item <?php echo $animate_class; ?>" <?php echo $animation_attr; ?>>

            <h3 class="lvca-faq-question"><?php echo wp_kses_post($question) ?></h3>

            <div class="lvca-faq-answer"><?php echo wp_kses_post($content) ?></div>

        </div>

        <?php
    }

    function map_vc_element() {
        if (function_exists("vc_map")) {

            //Register "container" content element. It will hold all your inner (child) content elements
            vc_map(array(
                "name" => __("FAQ", "livemesh-vc-addons"),
                "base" => "lvca_faq",
                "as_parent" => array('only' => 'lvca_faq_item'), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
                "content_element" => true,
                "show_settings_on_create" => false,
                "category" => __("Livemesh Addons", "livemesh-vc-addons"),
                "is_container" => true,
                'description' => __('Create FAQ to display in a multi-column grid.', 'livemesh-vc-addons'),
                "js_view" => 'VcColumnView',
                "icon" => 'icon-lvca-faq',
                "params" => array(

                    array(
                        "type" => "dropdown",
                        "param_name" => "style",
                        "heading" => __("Choose FAQ Style", "livemesh-vc-addons"),
                        "description" => __("Choose the particular style of faq you need", "livemesh-vc-addons"),
                        'value' => array(
                            __('Style 1', 'livemesh-vc-addons') => 'style1',
                            __('Style 2', 'livemesh-vc-addons') => 'style2',
                        ),
                        'std' => 'style1',
                        "admin_label" => true,
                    ),

                    array(
                        "type" => "lvca_number",
                        "param_name" => "per_line",
                        "value" => 2,
                        "min" => 1,
                        "max" => 4,
                        "suffix" => '',
                        "heading" => __("FAQ Items per row", "livemesh-vc-addons"),
                        "description" => __("The number of columns to display per row of the FAQ", "livemesh-vc-addons")
                    ),

                    array(
                        "type" => "lvca_number",
                        "param_name" => "per_line_tablet",
                        "value" => 1,
                        "min" => 1,
                        "max" => 4,
                        "suffix" => '',
                        "heading" => __("FAQ items per row in Tablet Resolution", "livemesh-vc-addons"),
                        "description" => __("The number of columns to display per row of the FAQ in tablet resolution", "livemesh-vc-addons")
                    ),

                    array(
                        "type" => "lvca_number",
                        "param_name" => "per_line_mobile",
                        "value" => 1,
                        "min" => 1,
                        "max" => 3,
                        "suffix" => '',
                        "heading" => __("FAQ items per row in Mobile Resolution", "livemesh-vc-addons"),
                        "description" => __("The number of columns to display per row of the FAQ in mobile resolution", "livemesh-vc-addons")
                    ),

                ),
            ));


        }
    }

    function map_child_vc_element() {
        if (function_exists("vc_map")) {
            vc_map(array(
                    "name" => __("FAQ Item", "my-text-domain"),
                    "base" => "lvca_faq_item",
                    "content_element" => true,
                    "as_child" => array('only' => 'lvca_faq'), // Use only|except attributes to limit parent (separate multiple values with comma)
                    "icon" => 'icon-lvca-faq-add',
                    "params" => array(
                        // add params same as with any other content element
                        array(
                            'type' => 'textfield',
                            'param_name' => 'question',
                            "admin_label" => true,
                            'heading' => __('Question', 'livemesh-vc-addons'),
                            'description' => __('The question for the FAQ item.', 'livemesh-vc-addons'),
                        ),
                        array(
                            'type' => 'textarea_html',
                            'param_name' => 'content',
                            'heading' => __('Answer', 'livemesh-vc-addons'),
                            'description' => __('The HTML content as answer for the FAQ item.', 'livemesh-vc-addons'),
                        ),
                        array(
                            "type" => "dropdown",
                            "param_name" => "animation",
                            "heading" => __("Choose Animation Type", "livemesh-vc-addons"),
                            "description" => __("Animation for the FAQ item", "livemesh-vc-addons"),
                            'value' => lvca_get_animation_options(),
                            'std' => 'none',
                        ),

                    )
                )

            );

        }
    }

}

//Your "container" content element should extend WPBakeryShortCodesContainer class to inherit all required functionality
if (class_exists('WPBakeryShortCodesContainer')) {
    class WPBakeryShortCode_lvca_faq extends WPBakeryShortCodesContainer {
    }
}
if (class_exists('WPBakeryShortCode')) {
    class WPBakeryShortCode_lvca_faq_item extends WPBakeryShortCode {
    }
}

// Initialize Element Class
if (class_exists('LVCA_FAQ')) {
    $LVCA_FAQ = new LVCA_FAQ();
}