<?php

/*
Widget Name: Gallery Carousel
Description: Display images or videos in a responsive carousel.
Author: LiveMesh
Author URI: https://www.livemeshthemes.com
*/

class LVCA_Gallery_Carousel {

    static public $gallery_counter = 0;

    protected $settings;

    /**
     * Get things started
     */
    public function __construct() {

        add_action('wp_enqueue_scripts', array($this, 'load_scripts'));

        add_shortcode('lvca_gallery_carousel', array($this, 'shortcode_func'));

        add_shortcode('lvca_gallery_carousel_item', array($this, 'child_shortcode_func'));

        add_action('init', array($this, 'map_vc_element'));

        add_action('init', array($this, 'map_child_vc_element'));

    }

    function load_scripts() {

        wp_enqueue_script('lvca-slick-carousel', LVCA_PLUGIN_URL . 'assets/js/slick' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_style('lvca-slick', LVCA_PLUGIN_URL . 'assets/css/slick.css', array(), LVCA_VERSION);

        wp_enqueue_script('lvca-fancybox', LVCA_PLUGIN_URL . 'assets/js/premium/jquery.fancybox' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_style('lvca-fancybox', LVCA_PLUGIN_URL . 'assets/css/premium/jquery.fancybox.css', array(), LVCA_VERSION);

        wp_enqueue_style('lvca-premium-frontend-styles', LVCA_PLUGIN_URL . 'assets/css/premium/lvca-frontend.css', array(), LVCA_VERSION);

        wp_enqueue_script('lvca-gallery-carousel', plugin_dir_url(__FILE__) . 'js/gallery-carousel' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_style('lvca-gallery-carousel', plugin_dir_url(__FILE__) . 'css/style.css', array(), LVCA_VERSION);

    }

    public function shortcode_func($atts, $content = null, $tag) {

        $defaults = lvca_get_default_atts_carousel();

        $settings = shortcode_atts($defaults, $atts);

        self::$gallery_counter++;

        $settings['gallery_class'] = !empty($settings['gallery_class']) ? sanitize_title($settings['gallery_class']) : 'gallery-carousel-' . self::$gallery_counter;

        $this->settings = $settings;

        $uniqueid = uniqid();

        ob_start();

        ?>

        <div id="lvca-gallery-carousel<?php echo $uniqueid; ?>"
             class="lvca-gallery-carousel lvca-container"
             data-settings='<?php echo wp_json_encode($settings); ?>'>

            <?php

            do_shortcode($content);

            ?>
        </div><!-- .lvca-gallery-carousel -->
        <?php

        $output = ob_get_clean();

        return $output;
    }

    public function child_shortcode_func($atts, $content = null, $tag) {

        $item_type = $name = $image = $tags = $link = $video_link = $mp4_video_link = $webm_video_link = $description = '';
        extract(shortcode_atts(array(
            'item_type' => 'image',
            'name' => '',
            'image' => '',
            "tags" => '',
            'link' => false,
            'video_link' => '',
            'mp4_video_link' => '',
            'webm_video_link' => '',
            'description' => '',

        ), $atts));

        ?>
        <?php

        $style = '';
        if (!empty($tags)) {
            $terms = explode(',', $tags);

            foreach ($terms as $term) {
                $style .= ' term-' . $term;
            }
        }
        ?>

        <?php

        $item_type = $item_type;
        $item_class = 'lvca-' . $item_type . '-type';

        ?>

        <div class="lvca-gallery-carousel-item <?php echo $style; ?> <?php echo $item_class; ?>">

            <div class="lvca-project-image">

                <?php

                if (!empty($link)) {

                    if (function_exists('vc_build_link'))
                        $link = vc_build_link($link);
                    else
                        $link = explode('|', $link);
                }
                ?>

                <?php if ($item_type == 'image' && !empty($link)): ?>

                    <a href="<?php echo $link['url']; ?>"
                       title="<?php echo esc_html($name); ?>"
                       target="<?php echo esc_attr($link['target']); ?>"><?php echo wp_get_attachment_image($image, $this->settings['image_size'], false, array('class' => 'lvca-image large', 'alt' => $name)); ?> </a>

                <?php else: ?>

                    <?php echo wp_get_attachment_image($image, $this->settings['image_size'], false, array('class' => 'lvca-image large', 'alt' => $name)); ?>

                <?php endif; ?>

                <div class="lvca-image-info">

                    <div class="lvca-entry-info">

                        <h3 class="lvca-entry-title">

                            <?php if ($item_type == 'image' && !empty($link)): ?>

                                <a href="<?php echo $link['url']; ?>"
                                   title="<?php echo esc_html($name); ?>"
                                   target="<?php echo esc_attr($link['target']); ?>"><?php echo esc_html($name); ?></a>

                            <?php else: ?>

                                <?php echo esc_html($name); ?>

                            <?php endif; ?>

                        </h3>

                        <?php if ($item_type == 'youtube' || $item_type == 'vimeo') : ?>

                            <?php
                            $video_url = $video_link;
                            ?>
                            <?php if (!empty($video_url)) : ?>

                                <a class="lvca-video-lightbox"
                                   data-fancybox="<?php echo $this->settings['gallery_class']; ?>"
                                   href="<?php echo $video_url; ?>"
                                   title="<?php echo esc_html($name); ?>"
                                   data-description="<?php echo wp_kses_post($description); ?>"><i
                                            class="lvca-icon-video-play"></i></a>

                            <?php endif; ?>

                            <?php elseif ($item_type == 'html5video' && !empty($mp4_video_link)) : ?>

                                <?php $video_id = 'lvca-video-' . $image; // will use thumbnail id as id for video for now ?>

                                <a class="lvca-video-lightbox"
                                   data-fancybox="<?php echo $this->settings['gallery_class']; ?>"
                                   href="#<?php echo $video_id; ?>"
                                   title="<?php echo esc_html($name); ?>"
                                   data-description="<?php echo wp_kses_post($description); ?>"><i
                                            class="lvca-icon-video-play"></i></a>

                                <div id="<?php echo $video_id; ?>" class="lvca-fancybox-video">

                                    <?php

                                    $image_data = wp_get_attachment_image_src($image, 'full');

                                    $image_src = ($image_data) ? $image_data[0]: '';

                                    ?>

                                    <video poster="<?php echo $image_src; ?>"
                                           src="<?php echo $mp4_video_link; ?>"
                                           autoplay="1"
                                           preload="metadata"
                                           controls
                                           controlsList="nodownload">
                                        <source type="video/mp4"
                                                src="<?php echo $mp4_video_link; ?>">
                                        <source type="video/webm"
                                                src="<?php echo $webm_video_link; ?>">
                                    </video>

                                </div>

                            <?php endif; ?>

                        <span class="lvca-terms"><?php echo esc_html($tags); ?></span>

                    </div>

                    <?php if ($item_type == 'image' && $this->settings['enable_lightbox']) : ?>

                        <?php
                        $image_data = wp_get_attachment_image_src($image, 'full');
                        ?>
                        <?php if ($image_data) : ?>

                            <?php $image_src = $image_data[0]; ?>

                            <a class="lvca-lightbox-item"
                               data-fancybox="<?php echo $this->settings['gallery_class']; ?>"
                               href="<?php echo $image_src; ?>"
                               title="<?php echo esc_html($name); ?>"
                               data-description="<?php echo wp_kses_post($description); ?>"><i
                                        class="lvca-icon-full-screen"></i></a>

                        <?php endif; ?>

                    <?php endif; ?>


                </div>

            </div>

        </div><!--.lvca-gallery-carousel-item -->

        <?php
    }

    function map_vc_element() {
        if (function_exists("vc_map")) {

            $carousel_params = array(
                array(
                    'type' => 'checkbox',
                    "param_name" => "enable_lightbox",
                    'heading' => __('Enable Lightbox Gallery?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true')
                ),

                array(
                    'type' => 'dropdown',
                    'param_name' => 'image_size',
                    'heading' => __('Image Size', 'livemesh-vc-addons'),
                    'std' => 'large',
                    'value' => lvca_get_image_sizes()
                ),
            );

            $carousel_params = array_merge($carousel_params, lvca_get_vc_map_carousel_options());

            $carousel_params = array_merge($carousel_params, lvca_get_vc_map_carousel_display_options());

            //Register "container" content element. It will hold all your inner (child) content elements
            vc_map(array(
                "name" => __("Gallery Carousel", "livemesh-vc-addons"),
                "base" => "lvca_gallery_carousel",
                "as_parent" => array('only' => 'lvca_gallery_carousel_item'), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
                "content_element" => true,
                "show_settings_on_create" => true,
                "category" => __("Livemesh Addons", "livemesh-vc-addons"),
                "is_container" => true,
                'description' => __('Display images or videos in a responsive carousel.', 'livemesh-vc-addons'),
                "js_view" => 'VcColumnView',
                "icon" => 'icon-lvca-gallery-carousel',
                "params" => $carousel_params
            ));


        }
    }


    function map_child_vc_element() {
        if (function_exists("vc_map")) {

            vc_map(array(
                    "name" => __("Gallery Carousel Item", "livemesh-vc-addons"),
                    "base" => "lvca_gallery_carousel_item",
                    "as_child" => array('only' => 'lvca_gallery_carousel'), // Use only|except attributes to limit parent (separate multiple values with comma)
                    "icon" => 'icon-lvca-carousel-item',
                    "category" => __("Livemesh Addons", "livemesh-vc-addons"),
                    "params" => array(

                        array(
                            'type' => 'dropdown',
                            'param_name' => 'item_type',
                            'heading' => __('Item Type', 'livemesh-vc-addons'),
                            'description' => __('Specify the item type - if this is an image or represents a YouTube/Vimeo/HTML5 video.', 'livemesh-vc-addons'),
                            'std' => 'image',
                            'value' => array(
                                __('Image', 'livemesh-vc-addons') => 'image',
                                __('YouTube Video', 'livemesh-vc-addons') => 'youtube',
                                __('Vimeo Video', 'livemesh-vc-addons') => 'vimeo',
                                __('HTML5 Video', 'livemesh-vc-addons') => 'html5video',
                            )
                        ),
                        array(
                            'type' => 'textfield',
                            'param_name' => 'name',
                            'heading' => __('Item Label', 'livemesh-vc-addons'),
                            'description' => __('The label or name for the gallery item.', 'livemesh-vc-addons'),
                            'admin_label' => true
                        ),
                        array(
                            'type' => 'attach_image',
                            'param_name' => 'image',
                            'heading' => __('Gallery Image.', 'livemesh-vc-addons'),
                            'description' => __('The image for the gallery item. If item type chosen is YouTube or Vimeo or MP4/WebM video, the image will be used as a placeholder image for video.', 'livemesh-vc-addons'),
                        ),
                        array(
                            'type' => 'textfield',
                            'param_name' => 'tags',
                            'heading' => __('Item Tag(s)', 'livemesh-vc-addons'),
                            'description' => __('One or more comma separated tags for the gallery item.', 'livemesh-vc-addons'),
                        ),
                        array(
                            'type' => 'vc_link',
                            'param_name' => 'link',
                            'heading' => __('Page URL', 'livemesh-vc-addons'),
                            'description' => __('The URL of the page to which the image gallery item points to (optional).', 'livemesh-vc-addons'),
                            'dependency' => array(
                                'element' => 'item_type',
                                'value' => array('image'),
                            ),
                        ),
                        array(
                            'type' => 'textfield',
                            'param_name' => 'video_link',
                            'heading' => __('Video URL', 'livemesh-vc-addons'),
                            'description' => __('The URL of the YouTube or Vimeo video.', 'livemesh-vc-addons'),
                            'dependency' => array(
                                'element' => 'item_type',
                                'value' => array('youtube', 'vimeo'),
                            ),
                        ),
                        array(
                            'type' => 'textfield',
                            'param_name' => 'mp4_video_link',
                            'heading' => __('MP4 Video URL', 'livemesh-vc-addons'),
                            'description' => __('The URL of the MP4 video.', 'livemesh-vc-addons'),
                            'dependency' => array(
                                'element' => 'item_type',
                                'value' => array('html5video'),
                            ),
                        ),
                        array(
                            'type' => 'textfield',
                            'param_name' => 'webm_video_link',
                            'heading' => __('WebM Video URL', 'livemesh-vc-addons'),
                            'description' => __('The URL of the WebM video.', 'livemesh-vc-addons'),
                            'dependency' => array(
                                'element' => 'item_type',
                                'value' => array('html5video'),
                            ),
                        ),
                        array(
                            'type' => 'textarea',
                            'param_name' => 'description',
                            'heading' => __('Item description', 'livemesh-vc-addons'),
                            'description' => __('Short description for the gallery item displayed in the lightbox gallery.(optional)', 'livemesh-vc-addons'),
                        ),
                    )

                )
            );

        }
    }

}

//Your "container" content element should extend WPBakeryShortCodesContainer class to inherit all required functionality
if (class_exists('WPBakeryShortCodesContainer')) {
    class WPBakeryShortCode_lvca_gallery_carousel extends WPBakeryShortCodesContainer {
    }
}
if (class_exists('WPBakeryShortCode')) {
    class WPBakeryShortCode_lvca_gallery_carousel_item extends WPBakeryShortCode {
    }
}

// Initialize Element Class
if (class_exists('LVCA_Gallery_Carousel')) {
    new LVCA_Gallery_Carousel();
}