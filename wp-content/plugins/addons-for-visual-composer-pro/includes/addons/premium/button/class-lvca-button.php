<?php

/*
Widget Name: Button
Description: Flat style buttons with rich set of customization options.
Author: LiveMesh
Author URI: https://www.livemeshthemes.com
*/

class LVCA_Button {

    /**
     * Get things started
     */
    public function __construct() {

        add_action('wp_enqueue_scripts', array($this, 'load_scripts'));

        add_shortcode('lvca_button', array($this, 'shortcode_func'));

        add_action('init', array($this, 'map_vc_element'));

    }

    function load_scripts() {

        wp_enqueue_script('lvca-button', plugin_dir_url(__FILE__) . 'js/button' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_style('lvca-button', plugin_dir_url(__FILE__) . 'css/style.css', array(), LVCA_VERSION);

    }

    public function shortcode_func($atts, $content = null, $tag) {

        $class = $style = $color = $custom_color = $hover_color = $type = $rounded = $href = $align = $target = $text = $icon_type = $icon_image = $icon_family = $icon = $animation = '';

        extract(shortcode_atts(array(
            'class' => false,
            'style' => null,
            'color' => 'default',
            'custom_color' => '',
            'hover_color' => '',
            'align' => false,
            'type' => '',
            'rounded' => false,
            'icon' => false,
            'icon_type' => 'icon',
            'icon_image' => '',
            'icon_family' => 'fontawesome',
            "icon_fontawesome" => '',
            "icon_openiconic" => '',
            "icon_typicons" => '',
            "icon_entypo" => '',
            "icon_linecons" => '',
            'text' => 'Buy Now',
            'href' => '',
            'target' => '',
            'animation' => 'none'
        ), $atts));


        $output = '';

        list($animate_class, $animation_attr) = lvca_get_animation_atts($animation);

        $icon_html = '';

        $class = (!empty($class)) ? ' ' . $class : '';

        $color_class = ' lvca-' . esc_attr($color);
        if (!empty($type))
            $type = ' lvca-' . esc_attr($type);

        $rounded = (!empty($rounded)) ? ' lvca-rounded' : '';

        $style = ($style) ? ' style="' . esc_attr($style) . '"' : '';

        if (!empty($target))
            $target = ' target="_blank"';
        else
            $target = '';

        if ($color == 'default' || ($color == 'custom' && empty($custom_color))) {
            $options = get_option('lvca_settings');

            if ($options && isset($options['lvca_theme_color']))
                $custom_color = $options['lvca_theme_color'];
            else
                $custom_color = '#f94213'; // default button color if none set in theme options
        }

        // Automatically set a hover color for custom color if none specified by user
        if (!empty($custom_color) && empty($hover_color))
            $hover_color = lvca_color_luminance($custom_color, 0.05);

        /* Use the custom color only if user wants to use the custom color set */
        $color_attr = (!empty($custom_color)) ? ' data-color=' . esc_attr($custom_color) : '';

        $hover_color_attr = (!empty($hover_color)) ? ' data-hover_color=' . esc_attr($hover_color) : '';

        if ($icon_type == 'icon_image') {
            $icon_html = wp_get_attachment_image($icon_image, 'thumbnail', false, array('class' => 'lvca-image lvca-thumbnail'));
        }
        elseif ($icon_type == 'icon' && !empty(${'icon_' . $icon_family}) && function_exists('vc_icon_element_fonts_enqueue')) {

            vc_icon_element_fonts_enqueue($icon_family);
            $icon_html = lvca_get_icon(${'icon_' . $icon_family});
        }

        $button_content = '<a id="lvca-button' . uniqid() . '" class= "lvca-button ' . ((!empty($icon_html)) ? ' lvca-with-icon' : '') . esc_attr($class) . $color_class . $type . $rounded . $animate_class . '"' . $style . $color_attr . $hover_color_attr . $animation_attr . ' href="' . esc_url($href) . '"' . esc_html($target) . '>' . $icon_html . esc_html($text) . '</a>';

        if ($align != 'none')
            $output = '<div class="lvca-button-wrap" style="clear: both; text-align:' . esc_attr($align) . ';">' . $button_content . '</div>';

        return $output;
    }

    function map_vc_element() {

        if (function_exists("vc_map")) {

            //Register "container" content element. It will hold all your inner (child) content elements
            vc_map(array(
                "name" => __("Button", "livemesh-vc-addons"),
                "base" => "lvca_button",
                "show_settings_on_create" => false,
                "category" => __("Livemesh Addons", "livemesh-vc-addons"),
                'description' => __('Flat style buttons with rich set of customization options.', 'livemesh-vc-addons'),
                "icon" => 'icon-lvca-button',
                "params" => array(
                    array(
                        'type' => 'textfield',
                        'param_name' => 'text',
                        "admin_label" => true,
                        'heading' => __('Button Title', 'livemesh-vc-addons'),
                        'description' => __('The text or title for the button.', 'livemesh-vc-addons'),
                    ),
                    array(
                        "param_name" => "href",
                        "type" => "textfield",
                        "heading" => __("URL", 'livemesh-vc-addons'),
                        "description" => __("The URL to which button should point to. The user is taken to this destination when the button is clicked.eg.http://targeturl.com", 'livemesh-vc-addons')
                    ),
                    array(
                        "param_name" => "target",
                        "type" => "checkbox",
                        "heading" => __("Open the link in new window?", 'livemesh-vc-addons'),
                    ),
                    array(
                        "param_name" => "color",
                        "type" => "dropdown",
                        'group' => __('Customize', 'livemesh-vc-addons'),
                        "heading" => __("Color", 'livemesh-vc-addons'),
                        "value" => array(
                            __("Default", 'livemesh-vc-addons') => "default",
                            __("Custom", 'livemesh-vc-addons') => "custom",
                            __("Black", 'livemesh-vc-addons') => "black",
                            __("Blue", 'livemesh-vc-addons') => "blue",
                            __("Cyan", 'livemesh-vc-addons') => "cyan",
                            __("Green", 'livemesh-vc-addons') => "green",
                            __("Orange", 'livemesh-vc-addons') => "orange",
                            __("Pink", 'livemesh-vc-addons') => "pink",
                            __("Red", 'livemesh-vc-addons') => "red",
                            __("Teal", 'livemesh-vc-addons') => "teal",
                            __("Transparent", 'livemesh-vc-addons') => "trans",
                            __("Semi Transparent", 'livemesh-vc-addons') => "semitrans"
                        ),
                        "description" => __("The color of the button.", 'livemesh-vc-addons'),
                        'std' => 'default'
                    ),
                    array(
                        'type' => 'colorpicker',
                        'group' => __('Customize', 'livemesh-vc-addons'),
                        'heading' => __('Custom Button color', 'js_composer'),
                        'param_name' => 'custom_color',
                        'description' => __('Custom color for the button.', 'js_composer'),
                        'dependency' => array(
                            'element' => 'color',
                            'value' => array('custom'),
                        ),
                    ),
                    array(
                        'type' => 'colorpicker',
                        'group' => __('Customize', 'livemesh-vc-addons'),
                        'heading' => __('Custom Button Hover Color', 'js_composer'),
                        'param_name' => 'hover_color',
                        'description' => __('The custom hover color for the button.', 'js_composer'),
                        'dependency' => array(
                            'element' => 'color',
                            'value' => array('custom'),
                        ),
                    ),

                    array(
                        "param_name" => "type",
                        "type" => "dropdown",
                        'group' => __('Customize', 'livemesh-vc-addons'),
                        "heading" => __("Type", 'livemesh-vc-addons'),
                        "value" => array(
                            __("Medium", 'livemesh-vc-addons') => "medium",
                            __("Small", 'livemesh-vc-addons') => "small",
                            __("large", 'livemesh-vc-addons') => "large"
                        ),
                        "description" => __("Can be large, small or medium.", 'livemesh-vc-addons')
                    ),
                    array(
                        "param_name" => "rounded",
                        "type" => "checkbox",
                        'group' => __('Customize', 'livemesh-vc-addons'),
                        "heading" => __("Display Rounded Button?", 'livemesh-vc-addons'),
                    ),
                    array(
                        "param_name" => "align",
                        "type" => "dropdown",
                        'group' => __('Customize', 'livemesh-vc-addons'),
                        "heading" => __("Alignment", 'livemesh-vc-addons'),
                        "value" => array(
                            __("None", 'livemesh-vc-addons') => "none",
                            __("Left", 'livemesh-vc-addons') => "left",
                            __("Center", 'livemesh-vc-addons') => "center",
                            __("Right", 'livemesh-vc-addons') => "right"
                        ),
                        "description" => __(" Alignment of the button and text alignment of the button title displayed.", 'livemesh-vc-addons')
                    ),
                    array(
                        'type' => 'dropdown',
                        'group' => __('Button Icon', 'livemesh-vc-addons'),
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
                        'group' => __('Button Icon', 'livemesh-vc-addons'),
                        'param_name' => 'icon_image',
                        'heading' => __('Button Image.', 'livemesh-vc-addons'),
                        "dependency" => array('element' => "icon_type", 'value' => 'icon_image'),
                    ),

                    array(
                        'type' => 'dropdown',
                        'group' => __('Button Icon', 'livemesh-vc-addons'),
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
                        'group' => __('Button Icon', 'livemesh-vc-addons'),
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
                        'group' => __('Button Icon', 'livemesh-vc-addons'),
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
                        'group' => __('Button Icon', 'livemesh-vc-addons'),
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
                        'group' => __('Button Icon', 'livemesh-vc-addons'),
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
                        'group' => __('Button Icon', 'livemesh-vc-addons'),
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
                        "param_name" => "class",
                        "type" => "textfield",
                        'group' => __('Settings', 'livemesh-vc-addons'),
                        "heading" => __("Button Class", 'livemesh-vc-addons'),
                        "description" => __("Custom CSS class name to be set for the button element created (optional)", 'livemesh-vc-addons')
                    ),
                    array(
                        "param_name" => "style",
                        "type" => "textfield",
                        'group' => __('Settings', 'livemesh-vc-addons'),
                        "heading" => __("Button Style", 'livemesh-vc-addons'),
                        "description" => __("Inline CSS styling applied for the button element created eg.padding: 10px 20px; (optional)", 'livemesh-vc-addons')
                    ),
                    array(
                        "type" => "dropdown",
                        'group' => __('Settings', 'livemesh-vc-addons'),
                        "param_name" => "animation",
                        "heading" => __("Choose Animation for Button", "livemesh-vc-addons"),
                        'value' => lvca_get_animation_options(),
                        'std' => 'none',
                    ),
                ),
            ));
        }
    }
}

//Your "container" content element should extend WPBakeryShortCodesContainer class to inherit all required functionality
if (class_exists('WPBakeryShortCode')) {
    class WPBakeryShortCode_lvca_button extends WPBakeryShortCode {
    }
}

// Initialize Element Class
if (class_exists('LVCA_Button')) {
    $LVCA_Button = new LVCA_Button();
}