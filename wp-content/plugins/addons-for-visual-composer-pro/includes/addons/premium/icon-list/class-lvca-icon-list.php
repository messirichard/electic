<?php

/*
Widget Name: Icon List
Description: Use images or icon fonts to create social icons list, show payment options etc.
Author: LiveMesh
Author URI: https://www.livemeshthemes.com
*/

class LVCA_Icon_List {

    protected $settings;

    /**
     * Get things started
     */
    public function __construct() {

        add_action('wp_enqueue_scripts', array($this, 'load_scripts'));

        add_shortcode('lvca_icon_list', array($this, 'shortcode_func'));

        add_shortcode('lvca_icon_list_item', array($this, 'child_shortcode_func'));

        add_action('init', array($this, 'map_vc_element'));

        add_action('init', array($this, 'map_child_vc_element'));

    }

    function load_scripts() {

        wp_enqueue_script('lvca-tooltips', LVCA_PLUGIN_URL . 'assets/js/premium/jquery.powertip' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_script('lvca-icon-list', plugin_dir_url(__FILE__) . 'js/icon-list' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_style('lvca-premium-frontend-styles', LVCA_PLUGIN_URL . 'assets/css/premium/lvca-frontend.css', array(), LVCA_VERSION);

        wp_enqueue_style('lvca-icon-list', plugin_dir_url(__FILE__) . 'css/style.css', array(), LVCA_VERSION);



    }

    public function shortcode_func($atts, $content = null, $tag) {

        $defaults = array(
            'icon_size' => 32,
            'icon_color' => '#333333',
            'hover_color' => '#666666',
            'target' => '',
            'align' => 'left',
            'animation' => 'none',
        );

        $this->settings = shortcode_atts($defaults, $atts);

        ob_start();

        ?>

        <div id="lvca-icon-list<?php echo uniqid(); ?>" data-settings='<?php echo htmlspecialchars(json_encode($this->settings)); ?>' class="lvca-icon-list lvca-align<?php echo $this->settings['align']; ?>">

            <?php

            do_shortcode($content);

            ?>

        </div>

        <?php

        $output = ob_get_clean();

        return $output;
    }

    public function child_shortcode_func($atts, $content = null, $tag) {

        $title = $icon_image = $icon_type = $icon_family = $href = '';
        extract(shortcode_atts(array(
            'icon_type' => 'icon',
            'icon_image' => '',
            'icon_family' => 'fontawesome',
            "icon_fontawesome" => '',
            "icon_openiconic" => '',
            "icon_typicons" => '',
            "icon_entypo" => '',
            "icon_linecons" => '',
            'title' => '',
            'href' => ''

        ), $atts));

        if (!empty($this->settings['target']))
            $target = 'target="_blank"';
        else
            $target = '';

        list($animate_class, $animation_attr) = lvca_get_animation_atts($this->settings['animation']);

        if ($icon_type == 'icon' && !empty(${'icon_' . $icon_family}) && function_exists('vc_icon_element_fonts_enqueue'))
            vc_icon_element_fonts_enqueue($icon_family); ?>

        <div class="lvca-icon-list-item<?php echo $animate_class; ?>" <?php echo $animation_attr; ?>
             title="<?php echo $title; ?>">

            <?php if ($icon_type == 'icon_image') : ?>

                <?php if (empty($href)) : ?>

                    <div class="lvca-image-wrapper">

                        <?php echo wp_get_attachment_image($icon_image, 'full', false, array('class' => 'lvca-image full', 'alt' => $title)); ?>

                    </div>

                <?php else : ?>

                    <a class="lvca-image-wrapper" href="<?php echo esc_url($href); ?>" <?php echo $target; ?>>

                        <?php echo wp_get_attachment_image($icon_image, 'full', false, array('class' => 'lvca-image full', 'alt' => $title)); ?>

                    </a>

                <?php endif; ?>

            <?php elseif (!empty(${'icon_' . $icon_family})) : ?>

                <?php if (empty($href)) : ?>

                    <div class="lvca-icon-wrapper">

                        <?php echo lvca_get_icon(${'icon_' . $icon_family}); ?>

                    </div>

                <?php else : ?>

                    <a class="lvca-icon-wrapper" href="<?php echo esc_url($href); ?>" <?php echo $target; ?>>

                        <?php echo lvca_get_icon(${'icon_' . $icon_family}); ?>

                    </a>

                <?php endif; ?>

            <?php endif; ?>

        </div>

        <?php
    }

    function map_vc_element() {
        if (function_exists("vc_map")) {

            //Register "container" content element. It will hold all your inner (child) content elements
            vc_map(array(
                "name" => __("Icon List", "livemesh-vc-addons"),
                "base" => "lvca_icon_list",
                "as_parent" => array('only' => 'lvca_icon_list_item'), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
                "content_element" => true,
                "show_settings_on_create" => true,
                "category" => __("Livemesh Addons", "livemesh-vc-addons"),
                "is_container" => true,
                'description' => __('Use images or icon fonts to create social icons list, show payment options etc.', 'livemesh-vc-addons'),
                "js_view" => 'VcColumnView',
                "icon" => 'icon-lvca-icons',
                "params" => array(
                    // add params same as with any other content element

                    array(
                        "type" => "lvca_number",
                        "param_name" => "icon_size",
                        "value" => 32,
                        "min" => 1,
                        "max" => 128,
                        "suffix" => 'px',
                        "heading" => __("Icon/Image size in pixels", "livemesh-vc-addons")
                    ),

                    array(
                        'type' => 'colorpicker',
                        'heading' => __('Icon color', 'js_composer'),
                        'param_name' => 'icon_color',
                        'description' => __('Custom color for the font icon.', 'js_composer'),
                    ),

                    array(
                        'type' => 'colorpicker',
                        'heading' => __('Icon Hover color', 'js_composer'),
                        'param_name' => 'hover_color',
                        'description' => __('Custom hover color for the font icon.', 'js_composer'),
                    ),
                    array(
                        "param_name" => "target",
                        "type" => "checkbox",
                        "heading" => __("Open the link in new window?", 'livemesh-vc-addons'),
                    ),
                    array(
                        "param_name" => "align",
                        "type" => "dropdown",
                        "heading" => __("Alignment", 'livemesh-vc-addons'),
                        "value" => array(
                            __("Left", 'livemesh-vc-addons') => "left",
                            __("Center", 'livemesh-vc-addons') => "center",
                            __("Right", 'livemesh-vc-addons') => "right"
                        ),
                        "description" => __("Alignment of the icon list.", 'livemesh-vc-addons')
                    ),
                    array(
                        "type" => "dropdown",
                        "param_name" => "animation",
                        "heading" => __("Choose Animation Type", "livemesh-vc-addons"),
                        'value' => lvca_get_animation_options(),
                        'std' => 'none',
                    ),
                ),
            ));


        }
    }


    function map_child_vc_element() {
        if (function_exists("vc_map")) {
            vc_map(array(
                    "name" => __("Icon Item", "my-text-domain"),
                    "base" => "lvca_icon_list_item",
                    "content_element" => true,
                    "as_child" => array('only' => 'lvca_icon_list'), // Use only|except attributes to limit parent (separate multiple values with comma)
                    "icon" => 'icon-lvca-icon-add',
                    "params" => array(
                        // add params same as with any other content element
                        array(
                            'type' => 'textfield',
                            'param_name' => 'title',
                            "admin_label" => true,
                            'heading' => __('Title', 'livemesh-vc-addons'),
                            'description' => __('Title for the icon.', 'livemesh-vc-addons'),
                        ),

                        array(
                            'type' => 'dropdown',
                            'param_name' => 'icon_type',
                            'heading' => __('Choose Icon Type', 'livemesh-vc-addons'),
                            'std' => 'icon',
                            'value' => array(
                                __('Icon', 'livemesh-vc-addons') => 'icon',
                                __('Icon Image', 'livemesh-vc-addons') => 'icon_image',
                            )
                        ),

                        array(
                            'type' => 'attach_image',
                            'param_name' => 'icon_image',
                            'heading' => __('Service Image.', 'livemesh-vc-addons'),
                            "dependency" => array('element' => "icon_type", 'value' => 'icon_image'),
                        ),

                        array(
                            'type' => 'dropdown',
                            'heading' => __('Icon library', 'livemesh-vc-addons'),
                            'value' => array(
                                __('Font Awesome', 'livemesh-vc-addons') => 'fontawesome',
                                __('Open Iconic', 'livemesh-vc-addons') => 'openiconic',
                                __('Typicons', 'livemesh-vc-addons') => 'typicons',
                                __('Entypo', 'livemesh-vc-addons') => 'entypo',
                                __('Linecons', 'livemesh-vc-addons') => 'linecons',
                            ),
                            'std' => 'fontawesome',
                            'param_name' => 'icon_family',
                            'description' => __('Select icon library.', 'livemesh-vc-addons'),
                            "dependency" => array('element' => "icon_type", 'value' => 'icon'),
                        ),
                        array(
                            'type' => 'iconpicker',
                            'heading' => __('Icon', 'livemesh-vc-addons'),
                            'param_name' => 'icon_fontawesome',
                            'value' => 'fa fa-info-circle',
                            'settings' => array(
                                'emptyIcon' => false,
                                // default true, display an "EMPTY" icon?
                                'iconsPerPage' => 4000,
                                // default 100, how many icons per/page to display
                            ),
                            'dependency' => array(
                                'element' => 'icon_family',
                                'value' => 'fontawesome',
                            ),
                            'description' => __('Select icon from library.', 'livemesh-vc-addons'),
                        ),
                        array(
                            'type' => 'iconpicker',
                            'heading' => __('Icon', 'livemesh-vc-addons'),
                            'param_name' => 'icon_openiconic',
                            'settings' => array(
                                'emptyIcon' => false,
                                // default true, display an "EMPTY" icon?
                                'type' => 'openiconic',
                                'iconsPerPage' => 4000,
                                // default 100, how many icons per/page to display
                            ),
                            'dependency' => array(
                                'element' => 'icon_family',
                                'value' => 'openiconic',
                            ),
                            'description' => __('Select icon from library.', 'livemesh-vc-addons'),
                        ),
                        array(
                            'type' => 'iconpicker',
                            'heading' => __('Icon', 'livemesh-vc-addons'),
                            'param_name' => 'icon_typicons',
                            'settings' => array(
                                'emptyIcon' => false,
                                // default true, display an "EMPTY" icon?
                                'type' => 'typicons',
                                'iconsPerPage' => 4000,
                                // default 100, how many icons per/page to display
                            ),
                            'dependency' => array(
                                'element' => 'icon_family',
                                'value' => 'typicons',
                            ),
                            'description' => __('Select icon from library.', 'livemesh-vc-addons'),
                        ),
                        array(
                            'type' => 'iconpicker',
                            'heading' => __('Icon', 'livemesh-vc-addons'),
                            'param_name' => 'icon_entypo',
                            'settings' => array(
                                'emptyIcon' => false,
                                // default true, display an "EMPTY" icon?
                                'type' => 'entypo',
                                'iconsPerPage' => 4000,
                                // default 100, how many icons per/page to display
                            ),
                            'dependency' => array(
                                'element' => 'icon_family',
                                'value' => 'entypo',
                            ),
                        ),
                        array(
                            'type' => 'iconpicker',
                            'heading' => __('Icon', 'livemesh-vc-addons'),
                            'param_name' => 'icon_linecons',
                            'settings' => array(
                                'emptyIcon' => false,
                                // default true, display an "EMPTY" icon?
                                'type' => 'linecons',
                                'iconsPerPage' => 4000,
                                // default 100, how many icons per/page to display
                            ),
                            'dependency' => array(
                                'element' => 'icon_family',
                                'value' => 'linecons',
                            ),
                            'description' => __('Select icon from library.', 'livemesh-vc-addons'),
                        ),

                        array(
                            "param_name" => "href",
                            "type" => "textfield",
                            "heading" => __("Target URL", 'livemesh-vc-addons'),
                            "description" => __("The URL to which icon/image should point to. (optional).eg.http://targeturl.com", 'livemesh-vc-addons')
                        ),

                    )
                )

            );

        }
    }

}

//Your "container" content element should extend WPBakeryShortCodesContainer class to inherit all required functionality
if (class_exists('WPBakeryShortCodesContainer')) {
    class WPBakeryShortCode_lvca_icon_list extends WPBakeryShortCodesContainer {
    }
}
if (class_exists('WPBakeryShortCode')) {
    class WPBakeryShortCode_lvca_icon_list_item extends WPBakeryShortCode {
    }
}

// Initialize Element Class
if (class_exists('LVCA_Icon_List')) {
    new LVCA_Icon_List();
}