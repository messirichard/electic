<?php

/*
Widget Name: Countdown
Description: Display a countdown timer for an end date.
Author: LiveMesh
Author URI: https://www.livemeshthemes.com
*/

class LVCA_Countdown {

    /**
     * Get things started
     */
    public function __construct() {

        add_action('wp_enqueue_scripts', array($this, 'load_scripts'));

        add_shortcode('lvca_countdown', array($this, 'shortcode_func'));

        add_action('init', array($this, 'map_vc_element'));

    }

    function load_scripts() {

        wp_enqueue_script('lvca-jquery-countdown', LVCA_PLUGIN_URL . 'assets/js/premium/jquery.countdown' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_script('lvca-countdown', plugin_dir_url(__FILE__) . 'js/countdown' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_style('lvca-countdown', plugin_dir_url(__FILE__) . 'css/style.css', array(), LVCA_VERSION);
    }

    public function shortcode_func($atts, $content = null, $tag) {

        $align = $label = $end_date = '';

        extract(shortcode_atts(array(
            'align' => 'left',
            'label' => '',
            'end_date' => false

        ), $atts));


        $output = '';

        if (!empty($end_date)) :

            ob_start();

            ?>

            <div class="lvca-countdown-wrap lvca-align<?php echo $align; ?>">

                <div class="lvca-countdown-label"><?php echo $label; ?></div>

                <div class="lvca-countdown" data-end-date="<?php echo $end_date; ?>"></div>

            </div>

            <?php

            $output = ob_get_clean();

        endif;

        return $output;
    }

    function map_vc_element() {
        if (function_exists("vc_map")) {

            //Register "container" content element. It will hold all your inner (child) content elements
            vc_map(array(
                "name" => __("Countdown", "livemesh-vc-addons"),
                "base" => "lvca_countdown",
                "content_element" => true,
                "show_settings_on_create" => false,
                "category" => __("Livemesh Addons", "livemesh-vc-addons"),
                'description' => __('Display a countdown timer for an end date.', 'livemesh-vc-addons'),
                "icon" => 'icon-lvca-countdown',
                "params" => array(

                    array(
                        "type" => "dropdown",
                        "param_name" => "align",
                        "heading" => __("Alignment", "livemesh-vc-addons"),
                        "description" => __("Choose the alignment of the countdown addon", "livemesh-vc-addons"),
                        'value' => array(
                            __('Left', 'livemesh-vc-addons') => 'left',
                            __('Right', 'livemesh-vc-addons') => 'right',
                            __('Center', 'livemesh-vc-addons') => 'center',
                        ),
                        'std' => 'left',
                        "admin_label" => true,
                    ),
                    array(
                        'type' => 'textfield',
                        'param_name' => 'label',
                        "admin_label" => true,
                        'heading' => __('Countdown Label', 'livemesh-vc-addons'),
                        'description' => __('The label for the countdown.', 'livemesh-vc-addons'),
                    ),
                    array(
                        "type" => "lvca_datetime_picker",
                        "param_name" => "end_date",
                        "heading" => __("End date", "livemesh-vc-addons"),
                        "description" => __("The end date for the countdown", "livemesh-vc-addons")
                    ),

                ),
            ));


        }
    }
}

//Your "container" content element should extend WPBakeryShortCodesContainer class to inherit all required functionality
if (class_exists('WPBakeryShortCode')) {
    class WPBakeryShortCode_lvca_countdown extends WPBakeryShortCode {
    }
}

// Initialize Element Class
if (class_exists('LVCA_Countdown')) {
    $LVCA_Countdown = new LVCA_Countdown();
}