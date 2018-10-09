<?php

/*
Widget Name: Image Slider
Description: Create a responsive image slider.
Author: LiveMesh
Author URI: https://www.livemeshthemes.com
*/

class LVCA_Image_Slider {

    protected $slides = array();

    /**
     * Get things started
     */
    public function __construct() {

        add_action('wp_enqueue_scripts', array($this, 'load_scripts'), 15); // load a little later than common styles for the plugin

        add_shortcode('lvca_image_slider', array($this, 'shortcode_func'));

        add_shortcode('lvca_image_slide', array($this, 'child_shortcode_func'));

        add_action('init', array($this, 'map_vc_element'));

        add_action('init', array($this, 'map_child_vc_element'));

    }

    function load_scripts() {

        wp_enqueue_script('lvca-flexslider', LVCA_PLUGIN_URL . 'assets/js/jquery.flexslider' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_style('lvca-flexslider', LVCA_PLUGIN_URL . 'assets/css/flexslider.css', array(), LVCA_VERSION);

        wp_enqueue_script('lvca-sliders', LVCA_PLUGIN_URL . 'assets/js/premium/jquery.sliders' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_style('lvca-sliders', LVCA_PLUGIN_URL . 'assets/css/premium/sliders.css', array(), LVCA_VERSION);

        wp_enqueue_script('lvca-slick-carousel', LVCA_PLUGIN_URL . 'assets/js/slick' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_style('lvca-slick', LVCA_PLUGIN_URL . 'assets/css/slick.css', array(), LVCA_VERSION);

        wp_enqueue_script('lvca-image-slider', plugin_dir_url(__FILE__) . 'js/image-slider' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_style('lvca-image-slider', plugin_dir_url(__FILE__) . 'css/style.css', array(), LVCA_VERSION);

    }

    public function shortcode_func($atts, $content = null, $tag) {

        $defaults = array_merge(
            array(
                'class' => '',
                'slider_type' => 'flex',
                'image_size' => 'full',
                'caption_style' => 'style1',
                'animation' => 'slide',
                'direction' => 'horizontal',
                'control_nav' => '',
                'direction_nav' => '',
                'thumbnail_nav' => '',
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

        $this->slides = array(); // always reset it prior to use - wipes out data from earlier sliders

        // Get the items array ready by processing shortcodes in content
        do_shortcode($content);

        $slides = $this->slides;

        if (empty($slides))
            return '';

        ob_start();

        ?>

        <?php $caption_style = $settings['caption_style']; ?>

        <?php $slider_type = $settings['slider_type']; ?>

        <div class="lvca-image-slider lvca-container lvca-<?php echo $caption_style; ?>"
             data-slider-type="<?php echo $slider_type; ?>"
             data-settings='<?php echo wp_json_encode($settings); ?>'>

            <?php if ($slider_type == 'flex'): ?>

                <?php if ($settings['thumbnail_nav']):

                    $carousel_id = uniqid('lvca-carousel-');
                    $slider_id = uniqid('lvca-slider-');

                endif; ?>

                <div <?php echo(isset($slider_id) ? 'id="' . $slider_id . '"' : ''); ?>
                    <?php echo(isset($carousel_id) ? 'data-carousel="' . $carousel_id . '"' : ''); ?>
                        class="lvca-flexslider">

                    <div class="lvca-slides">

                        <?php foreach ($slides as $slide): ?>

                            <?php if (!empty($slide['image']) && wp_attachment_is_image($slide['image'])) : ?>

                                <?php

                                if ($settings['thumbnail_nav']):

                                    $thumbnail_src = wp_get_attachment_image_src($slide['image'], 'medium');

                                    if ($thumbnail_src)
                                        $thumbnail_attr = 'data-thumb="' . $thumbnail_src[0] . '"';

                                endif;

                                ?>

                                <div <?php echo(isset($thumbnail_attr) ? $thumbnail_attr : ''); ?> class="lvca-slide">

                                    <?php if (!empty($slide['slide_url'])) : ?>

                                        <a href="<?php echo esc_url($slide['slide_url']); ?>"
                                           title="<?php echo $slide['title']; ?>" <?php echo (empty($slide['new_window'])) ? '' : 'target="_blank"'; ?>>

                                            <?php echo wp_get_attachment_image($slide['image'], $settings['image_size'], false, array('class' => 'lvca-image full', 'alt' => $slide['title'])); ?>

                                        </a>

                                    <?php else : ?>

                                        <?php echo wp_get_attachment_image($slide['image'], $settings['image_size'], false, array('class' => 'lvca-image full', 'alt' => $slide['title'])); ?>

                                    <?php endif; ?>

                                    <?php if (!empty($slide['heading'])): ?>

                                        <div class="lvca-caption">

                                            <?php echo empty($slide['subheading']) ? '' : '<div class="lvca-subheading">' . htmlspecialchars_decode($slide['subheading']) . '</div>'; ?>

                                            <?php if (!empty($slide['heading'])): ?>

                                                <?php if (!empty($slide['slide_url'])) : ?>

                                                    <h3 class="lvca-heading">
                                                        <a href="<?php echo esc_url($slide['slide_url']); ?>"
                                                           title="<?php echo $slide['title']; ?>" <?php echo (empty($slide['new_window'])) ? '' : 'target="_blank"'; ?>><?php echo $slide['heading']; ?></a>
                                                    </h3>

                                                <?php else : ?>

                                                    <h3 class="lvca-heading"><?php echo $slide['heading']; ?></h3>

                                                <?php endif; ?>

                                            <?php endif; ?>

                                            <?php if ($caption_style == 'style1' && (!empty($slide['button_url']))) : ?>

                                                <?php
                                                $color_class = ' lvca-' . esc_attr($slide['button_color']);
                                                if (!empty($slide['button_type']))
                                                    $type = ' lvca-' . esc_attr($slide['button_type']);

                                                $rounded = (!empty($slide['rounded'])) ? ' lvca-rounded' : '';

                                                ?>

                                                <a class="lvca-button <?php echo $color_class . $type . $rounded; ?>"
                                                   href="<?php echo esc_url($slide['button_url']); ?>"
                                                    <?php echo (empty($slide['new_window'])) ? '' : 'target="_blank"'; ?>><?php echo $slide['button_text']; ?></a>

                                            <?php endif; ?>

                                        </div>

                                    <?php endif; ?>


                                </div>

                            <?php endif; ?>

                        <?php endforeach; ?>

                    </div>

                </div>

                <?php if (isset($carousel_id)): ?>

                    <div id="<?php echo $carousel_id; ?>" class="lvca-thumbnailslider lvca-flexslider">

                        <div class="lvca-slides">

                            <?php foreach ($slides as $slide): ?>

                                <?php if (!empty($slide['image']) && wp_attachment_is_image($slide['image'])) : ?>

                                    <div class="lvca-slide">

                                        <?php echo wp_get_attachment_image($slide['image'], 'medium', false, array('class' => 'lvca-image medium', 'alt' => $slide['title'])); ?>

                                    </div>

                                <?php endif; ?>

                            <?php endforeach; ?>

                        </div>

                    </div>

                <?php endif; ?>

            <?php elseif ($slider_type == 'nivo') : ?>

                <?php $nivo_captions = array(); ?>

                <div class="nivoSlider">

                    <?php foreach ($slides as $slide): ?>

                        <?php $caption_index = uniqid('lvca-nivo-caption-'); ?>

                        <?php if (!empty($slide['image']) && wp_attachment_is_image($slide['image'])) : ?>

                            <?php

                            $thumbnail_src = wp_get_attachment_image_src($slide['image'], 'medium');

                            if ($thumbnail_src)
                                $thumbnail_src = $thumbnail_src[0];

                            ?>

                            <?php if (!empty($slide['slide_url'])) : ?>

                                <a href="<?php echo esc_url($slide['slide_url']); ?>"
                                   title="<?php echo $slide['title']; ?>" <?php echo (empty($slide['new_window'])) ? '' : 'target="_blank"'; ?>>

                                    <?php echo wp_get_attachment_image($slide['image'], $settings['image_size'], false, array('class' => 'lvca-image full', 'data-thumb' => $thumbnail_src, 'alt' => $slide['title'], 'title' => ('#' . $caption_index))); ?>

                                </a>

                            <?php else : ?>

                                <?php echo wp_get_attachment_image($slide['image'], $settings['image_size'], false, array('class' => 'lvca-image full', 'data-thumb' => $thumbnail_src, 'alt' => $slide['title'], 'title' => ('#' . $caption_index))); ?>

                            <?php endif; ?>

                            <?php if (!empty($slide['heading'])): ?>

                                <?php if (!empty($slide['slide_url'])) : ?>

                                    <?php $nivo_captions[] = '<div id="' . $caption_index . '" class="nivo-html-caption">' . '<div class="lvca-subheading">' . htmlspecialchars_decode($slide['subheading']) . '</div>' . '<h3 class="lvca-heading">' . '<a href="' . esc_url($slide['slide_url']) . '" title="' . $slide['title'] . '">' . $slide['heading'] . '</a></h3>' . '</div>'; ?>

                                <?php else : ?>

                                    <?php $nivo_captions[] = '<div id="' . $caption_index . '" class="nivo-html-caption">' . '<div class="lvca-subheading">' . htmlspecialchars_decode($slide['subheading']) . '</div>' . '<h3 class="lvca-heading">' . $slide['heading'] . '</h3>' . '</div>'; ?>

                                <?php endif; ?>

                            <?php endif; ?>

                            <?php $nivo_captions[] = '<div id="' . $caption_index . '" class="nivo-html-caption">' . '<div class="lvca-subheading">' . htmlspecialchars_decode($slide['subheading']) . '</div>' . '<h3 class="lvca-heading">' . $slide['heading'] . '</h3>' . '</div>'; ?>

                        <?php endif; ?>

                    <?php endforeach; ?>

                </div>

                <div class="lvca-caption nivo-html-caption">

                    <?php foreach ($nivo_captions as $nivo_caption): ?>

                        <?php echo $nivo_caption . "\n"; ?>

                    <?php endforeach; ?>

                </div>


            <?php elseif ($slider_type == 'slick') : ?>

                <div class="lvca-slickslider">

                    <?php foreach ($slides as $slide): ?>

                        <div class="lvca-slide">

                            <?php if (!empty($slide['image']) && wp_attachment_is_image($slide['image'])) : ?>

                                <?php if (!empty($slide['slide_url'])) : ?>

                                    <a href="<?php echo esc_url($slide['slide_url']); ?>"
                                       title="<?php echo $slide['title']; ?>" <?php echo (empty($slide['new_window'])) ? '' : 'target="_blank"'; ?>>

                                        <?php echo wp_get_attachment_image($slide['image'], $settings['image_size'], false, array('class' => 'lvca-image full', 'alt' => $slide['title'])); ?>

                                    </a>

                                <?php else : ?>

                                    <?php echo wp_get_attachment_image($slide['image'], $settings['image_size'], false, array('class' => 'lvca-image full', 'alt' => $slide['title'])); ?>

                                <?php endif; ?>

                                <div class="lvca-caption">

                                    <?php echo empty($slide['subheading']) ? '' : '<div class="lvca-subheading">' . htmlspecialchars_decode($slide['subheading']) . '</div>'; ?>

                                    <?php if (!empty($slide['heading'])): ?>

                                        <?php if (!empty($slide['slide_url'])) : ?>

                                            <h3 class="lvca-heading">
                                                <a href="<?php echo esc_url($slide['slide_url']); ?>"
                                                   title="<?php echo $slide['title']; ?>" <?php echo (empty($slide['new_window'])) ? '' : 'target="_blank"'; ?>><?php echo $slide['heading']; ?></a>
                                            </h3>

                                        <?php else : ?>

                                            <h3 class="lvca-heading"><?php echo $slide['heading']; ?></h3>

                                        <?php endif; ?>

                                    <?php endif; ?>

                                    <?php if ($caption_style == 'style1' && (!empty($slide['button_url']))) : ?>

                                        <?php
                                        $color_class = ' lvca-' . esc_attr($slide['button_color']);
                                        if (!empty($slide['button_type']))
                                            $type = ' lvca-' . esc_attr($slide['button_type']);

                                        $rounded = (!empty($slide['rounded'])) ? ' lvca-rounded' : '';

                                        ?>

                                        <a class="lvca-button <?php echo $color_class . $type . $rounded; ?>"
                                           href="<?php echo esc_url($slide['button_url']); ?>"
                                            <?php echo (empty($slide['new_window'])) ? '' : 'target="_blank"'; ?>><?php echo $slide['button_text']; ?></a>

                                    <?php endif; ?>

                                </div>

                            <?php endif; ?>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php elseif ($slider_type == 'responsive') : ?>

                <div class="rslides_container">

                    <ul class="rslides lvca-slide">

                        <?php foreach ($slides as $slide): ?>

                            <li>

                                <?php if (!empty($slide['image']) && wp_attachment_is_image($slide['image'])) : ?>

                                    <?php if (!empty($slide['slide_url'])) : ?>

                                        <a href="<?php echo esc_url($slide['slide_url']); ?>"
                                           title="<?php echo $slide['title']; ?>" <?php echo (empty($slide['new_window'])) ? '' : 'target="_blank"'; ?>>

                                            <?php echo wp_get_attachment_image($slide['image'], $settings['image_size'], false, array('class' => 'lvca-image full', 'alt' => $slide['title'])); ?>

                                        </a>

                                    <?php else : ?>

                                        <?php echo wp_get_attachment_image($slide['image'], $settings['image_size'], false, array('class' => 'lvca-image full', 'alt' => $slide['title'])); ?>

                                    <?php endif; ?>

                                    <div class="lvca-caption">

                                        <?php echo empty($slide['subheading']) ? '' : '<div class="lvca-subheading">' . htmlspecialchars_decode($slide['subheading']) . '</div>'; ?>

                                        <?php if (!empty($slide['heading'])): ?>

                                            <?php if (!empty($slide['slide_url'])) : ?>

                                                <h3 class="lvca-heading">
                                                    <a href="<?php echo esc_url($slide['slide_url']); ?>"
                                                       title="<?php echo $slide['title']; ?>" <?php echo (empty($slide['new_window'])) ? '' : 'target="_blank"'; ?>><?php echo $slide['heading']; ?></a>
                                                </h3>

                                            <?php else : ?>

                                                <h3 class="lvca-heading"><?php echo $slide['heading']; ?></h3>

                                            <?php endif; ?>

                                        <?php endif; ?>

                                        <?php if ($caption_style == 'style1' && (!empty($slide['button_url']))) : ?>

                                            <?php
                                            $color_class = ' lvca-' . esc_attr($slide['button_color']);
                                            if (!empty($slide['button_type']))
                                                $type = ' lvca-' . esc_attr($slide['button_type']);

                                            $rounded = (!empty($slide['rounded'])) ? ' lvca-rounded' : '';

                                            ?>

                                            <a class="lvca-button <?php echo $color_class . $type . $rounded; ?>"
                                               href="<?php echo esc_url($slide['button_url']); ?>"
                                                <?php echo (empty($slide['new_window'])) ? '' : 'target="_blank"'; ?>><?php echo $slide['button_text']; ?></a>

                                        <?php endif; ?>

                                    </div>

                                <?php endif; ?>

                            </li>

                        <?php endforeach; ?>

                    </ul>

                </div>

            <?php endif; ?>

        </div>

        <?php

        $output = ob_get_clean();

        return $output;
    }

    public function child_shortcode_func($atts, $content = null, $tag) {

        $defaults = array(
            'title' => '',
            'image' => '',
            "slide_url" => '',
            'heading' => '',
            'subheading' => '',
            'button_text' => '',
            'button_url' => '',
            'new_window' => '',
            'button_color' => 'trans',
            'button_type' => 'large',
            'rounded' => ''
        );

        $slide = shortcode_atts($defaults, $atts);

        $this->slides[] = $slide;
    }

    function map_vc_element() {

        if (function_exists("vc_map")) {

            $general_params = array(

                array(
                    "param_name" => "class",
                    "type" => "textfield",
                    "heading" => __("Slider Class", 'livemesh-vc-addons'),
                    "description" => __("Custom CSS class name to be set for the slider (optional)", 'livemesh-vc-addons')
                ),

                array(
                    "param_name" => "slider_type",
                    "type" => "dropdown",
                    "heading" => __("Slider Type", 'livemesh-vc-addons'),
                    "value" => array(
                        __("Flex Slider", 'livemesh-vc-addons') => "flex",
                        __("Nivo Slider", 'livemesh-vc-addons') => "nivo",
                        __("Slick Slider", 'livemesh-vc-addons') => "slick",
                        __("Responsive Slider", 'livemesh-vc-addons') => "responsive"
                    ),
                    "description" => __("Choose the slider type.", 'livemesh-vc-addons'),
                    "std" => "flex",
                    'admin_label' => true,
                ),

                array(
                    'type' => 'dropdown',
                    'param_name' => 'image_size',
                    'heading' => __('Image Size', 'livemesh-vc-addons'),
                    'std' => 'full',
                    'value' => lvca_get_image_sizes()
                ),

                array(
                    "param_name" => "caption_style",
                    "type" => "dropdown",
                    "heading" => __("Choose Caption Style", 'livemesh-vc-addons'),
                    "value" => array(
                        __("Style 1", 'livemesh-vc-addons') => "style1",
                        __("Style 2", 'livemesh-vc-addons') => "style2"
                    ),
                    "std" => "style1"
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
                    'dependency' => array(
                        'element' => 'slider_type',
                        'value' => array('flex', 'slick'),
                    ),
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
                    'dependency' => array(
                        'element' => 'slider_type',
                        'value' => array('flex', 'slick'),
                    ),
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
                    "param_name" => "thumbnail_nav",
                    'heading' => __('Thumbnails Navigation?', 'livemesh-vc-addons'),
                    'description' => __('Use thumbnails for Control Nav?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'dependency' => array(
                        'element' => 'slider_type',
                        'value' => array('flex', 'nivo'),
                    ),
                    'group' => __('Settings', 'livemesh-vc-addons')
                ),

                array(
                    'type' => 'checkbox',
                    "param_name" => "randomize",
                    'heading' => __('Randomize slides?', 'livemesh-vc-addons'),
                    'description' => __('Randomize slide order?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'dependency' => array(
                        'element' => 'slider_type',
                        'value' => array('flex', 'responsive'),
                    ),
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
                    'dependency' => array(
                        'element' => 'slider_type',
                        'value' => array('flex'),
                    ),
                    'group' => __('Settings', 'livemesh-vc-addons')
                ),

                array(
                    'type' => 'checkbox',
                    "param_name" => "loop",
                    'heading' => __('Loop?', 'livemesh-vc-addons'),
                    'description' => __('Should the animation loop?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'dependency' => array(
                        'element' => 'slider_type',
                        'value' => array('flex', 'slick'),
                    ),
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
                "name" => __("Image Slider", "livemesh-vc-addons"),
                "base" => "lvca_image_slider",
                "as_parent" => array('only' => 'lvca_image_slide'), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
                "content_element" => true,
                "show_settings_on_create" => true,
                "category" => __("Livemesh Addons", "livemesh-vc-addons"),
                "is_container" => true,
                'description' => __('Create a responsive image slider.', 'livemesh-vc-addons'),
                "js_view" => 'VcColumnView',
                "icon" => 'icon-lvca-image-slider',
                "params" => $params
            ));

        }
    }


    function map_child_vc_element() {
        if (function_exists("vc_map")) {

            $slide_params = array(

                array(
                    'type' => 'textfield',
                    'param_name' => 'title',
                    'heading' => __('Title', 'livemesh-vc-addons'),
                    'admin_label' => true,
                    'description' => __('The title to identify the slide', 'livemesh-vc-addons'),
                ),
                array(
                    'type' => 'attach_image',
                    'param_name' => 'image',
                    'heading' => __('Slide Image.', 'livemesh-vc-addons'),
                    'description' => __('The image for the slide.', 'livemesh-vc-addons'),
                ),
                array(
                    'type' => 'textfield',
                    'param_name' => 'slide_url',
                    'heading' => __('URL to link to by image and caption heading. (optional)', 'livemesh-vc-addons'),
                    'description' => __('Specify the URL to which the slide image and caption heading should link to. (optional)', 'livemesh-vc-addons')
                ),
            );

            $caption_params = array(

                array(
                    'type' => 'textfield',
                    'param_name' => 'heading',
                    'heading' => __('Caption Heading', 'livemesh-vc-addons'),
                    'group' => __('Caption', 'livemesh-vc-addons'),
                ),
                array(
                    'type' => 'textfield',
                    'param_name' => 'subheading',
                    'heading' => __('Caption Sub-heading', 'livemesh-vc-addons'),
                    'group' => __('Caption', 'livemesh-vc-addons'),
                ),

                array(
                    'type' => 'textfield',
                    'param_name' => 'button_text',
                    'heading' => __('Button Title', 'livemesh-vc-addons'),
                    'description' => __('The text or title for the button.', 'livemesh-vc-addons'),
                    'group' => __('Caption', 'livemesh-vc-addons'),
                ),
                array(
                    "param_name" => "button_url",
                    "type" => "textfield",
                    "heading" => __("Button URL", 'livemesh-vc-addons'),
                    "description" => __("The URL to which button should point to. The user is taken to this destination when the button is clicked.eg.http://targeturl.com", 'livemesh-vc-addons'),
                    'group' => __('Caption', 'livemesh-vc-addons'),
                ),
                array(
                    "param_name" => "new_window",
                    "type" => "checkbox",
                    "heading" => __("Open the link in new window?", 'livemesh-vc-addons'),
                    'group' => __('Caption', 'livemesh-vc-addons'),
                ),
                array(
                    "param_name" => "button_color",
                    "type" => "dropdown",
                    "heading" => __("Button Color", 'livemesh-vc-addons'),
                    "value" => array(
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
                    'std' => 'trans',
                    'group' => __('Caption', 'livemesh-vc-addons'),
                ),
                array(
                    "param_name" => "button_type",
                    "type" => "dropdown",
                    "heading" => __("Button Size", 'livemesh-vc-addons'),
                    "value" => array(
                        __("Medium", 'livemesh-vc-addons') => "medium",
                        __("Small", 'livemesh-vc-addons') => "small",
                        __("large", 'livemesh-vc-addons') => "large"
                    ),
                    "description" => __("Can be large, small or medium.", 'livemesh-vc-addons'),
                    'group' => __('Caption', 'livemesh-vc-addons'),
                ),
                array(
                    "param_name" => "rounded",
                    "type" => "checkbox",
                    "heading" => __("Display Rounded Button?", 'livemesh-vc-addons'),
                    'group' => __('Caption', 'livemesh-vc-addons'),
                ),
            );

            $params = array_merge($slide_params, $caption_params);

            vc_map(array(
                    "name" => __("Image Slide", "livemesh-vc-addons"),
                    "base" => "lvca_image_slide",
                    "as_child" => array('only' => 'lvca_image_slider'), // Use only|except attributes to limit parent (separate multiple values with comma)
                    "icon" => 'icon-lvca-image-slider-add',
                    "category" => __("Livemesh Addons", "livemesh-vc-addons"),
                    "params" => $params

                )
            );

        }
    }

}

//Your "container" content element should extend WPBakeryShortCodesContainer class to inherit all required functionality
if (class_exists('WPBakeryShortCodesContainer')) {
    class WPBakeryShortCode_lvca_image_slider extends WPBakeryShortCodesContainer {
    }
}
if (class_exists('WPBakeryShortCode')) {
    class WPBakeryShortCode_lvca_image_slide extends WPBakeryShortCode {
    }
}

// Initialize Element Class
if (class_exists('LVCA_Image_Slider')) {
    new LVCA_Image_Slider();
}