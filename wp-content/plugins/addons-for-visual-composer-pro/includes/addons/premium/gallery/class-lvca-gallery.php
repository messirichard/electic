<?php

/*
Widget Name: Gallery
Description: Display images or videos in a multi-column grid.
Author: LiveMesh
Author URI: https://www.livemeshthemes.com
*/

class LVCA_Gallery {

    static public $gallery_counter = 0;

    protected $gallery_items = array();

    /**
     * Get things started
     */
    public function __construct() {

        add_action('wp_enqueue_scripts', array($this, 'load_scripts'));

        add_action('wp_enqueue_scripts', array($this, 'localize_scripts'), 999999);

        add_shortcode('lvca_gallery', array($this, 'shortcode_func'));

        add_shortcode('lvca_gallery_item', array($this, 'child_shortcode_func'));

        add_action('init', array($this, 'map_vc_element'));

        add_action('init', array($this, 'map_child_vc_element'));

    }

    function load_scripts() {

        wp_enqueue_script('lvca-isotope', LVCA_PLUGIN_URL . 'assets/js/isotope.pkgd' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_script('lvca-imagesloaded', LVCA_PLUGIN_URL . 'assets/js/imagesloaded.pkgd' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_script('lvca-fancybox', LVCA_PLUGIN_URL . 'assets/js/premium/jquery.fancybox' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_style('lvca-fancybox', LVCA_PLUGIN_URL . 'assets/css/premium/jquery.fancybox.css', array(), LVCA_VERSION);

        wp_enqueue_style('lvca-premium-frontend-styles', LVCA_PLUGIN_URL . 'assets/css/premium/lvca-frontend.css', array(), LVCA_VERSION);

        wp_enqueue_script('lvca-gallery', plugin_dir_url(__FILE__) . 'js/gallery' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_style('lvca-gallery', plugin_dir_url(__FILE__) . 'css/style.css', array(), LVCA_VERSION);

    }

    public function localize_scripts() {

        wp_localize_script('lvca-frontend-scripts', 'lvca_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

    }

    public function shortcode_func($atts, $content = null, $tag) {

        $defaults = array_merge(
            array(
                'gallery_class' => '',
                'heading' => '',
                'filterable' => '',
                'layout_mode' => 'fitRows',
                'pagination' => 'none',
                'show_remaining' => '',
                'items_per_page' => 8,
                'per_line' => 3,
                'per_line_tablet' => 2,
                'per_line_mobile' => 1,
                'enable_lightbox' => '',
                'display_item_tags' => '',
                'display_item_title' => '',
                'image_size' => 'large',
                'gutter' => 20,
                'tablet_gutter' => 10,
                'tablet_width' => 960,
                'mobile_gutter' => 10,
                'mobile_width' => 480
            )

        );

        $settings = shortcode_atts($defaults, $atts);

        $common = LVCA_Gallery_Common::get_instance();

        $settings = $common->parse_gallery_settings($settings);

        self::$gallery_counter++;

        $settings['gallery_class'] = !empty($settings['gallery_class']) ? sanitize_title($settings['gallery_class']) : 'gallery-' . self::$gallery_counter;

        $this->gallery_items = array(); // always reset it prior to use - wipes out data from earlier grids

        // Get the items array ready by processing shortcodes in content
        do_shortcode($content);

        $items = $this->gallery_items;

        $items = $common->parse_items($items);

        ob_start();

        if (!empty($items)) :

            $terms = $common->get_gallery_terms($items);
            $max_num_pages = ceil(count($items) / $settings['items_per_page']);

            ?>

            <div class="lvca-gallery-wrap lvca-gapless-grid"
                 data-settings='<?php echo wp_json_encode($settings); ?>'
                 data-items='<?php echo ($settings['pagination'] !== 'none') ? json_encode($items, JSON_HEX_APOS) : ''; ?>'
                 data-maxpages='<?php echo $max_num_pages; ?>'
                 data-total='<?php echo count($items); ?>'
                 data-current='1'>

                <?php if (!empty($settings['heading']) || $settings['filterable']): ?>

                    <?php $header_class = (trim($settings['heading']) === '') ? ' lvca-no-heading' : ''; ?>

                    <div class="lvca-gallery-header <?php echo $header_class; ?>">

                        <?php if (!empty($settings['heading'])): ?>

                            <h3 class="lvca-heading"><?php echo wp_kses_post($settings['heading']); ?></h3>

                        <?php endif; ?>

                        <?php

                        if ($settings['filterable'])
                            echo $common->get_gallery_terms_filter($terms);

                        ?>

                    </div>

                <?php endif; ?>

                <div id="<?php echo uniqid('lvca-gallery'); ?>"
                     class="lvca-gallery js-isotope lvca-<?php echo esc_attr($settings['layout_mode']); ?> lvca-grid-container <?php echo lvca_get_grid_classes($settings); ?> <?php echo $settings['gallery_class']; ?>"
                     data-isotope-options='{ "itemSelector": ".lvca-gallery-item", "layoutMode": "<?php echo esc_attr($settings['layout_mode']); ?>", "masonry": { "columnWidth": ".lvca-grid-sizer" } }'>

                    <?php if ($settings['layout_mode'] == 'masonry'): ?>

                        <div class="lvca-grid-sizer"></div>

                    <?php endif; ?>

                    <?php $common->display_gallery($items, $settings, 1); ?>

                </div><!-- Isotope items -->

                <?php echo $common->paginate_gallery($items, $settings); ?>

            </div><!-- .lvca-gallery-wrap -->

            <?php

        endif;

        $output = ob_get_clean();

        return $output;
    }

    public function child_shortcode_func($atts, $content = null, $tag) {

        $defaults = array(
            'item_type' => 'image',
            'name' => '',
            'image' => '',
            'tags' => '',
            'link' => false,
            'video_link' => false,
            'mp4_video_link' => false,
            'webm_video_link' => false,
            'display_video_inline' => false,
            "description" => '',

        );

        $item = shortcode_atts($defaults, $atts);

        $this->gallery_items[] = $item;
    }

    function map_vc_element() {

        if (function_exists("vc_map")) {

            $general_params = array(

                array(
                    'type' => 'textfield',
                    'param_name' => 'gallery_class',
                    'description' => __('Specify an unique identifier used as a custom CSS class name and lightbox group name/slug for the gallery element.', 'livemesh-vc-addons'),
                    'heading' => __('Gallery Class/Identifier', 'livemesh-vc-addons'),
                    'std' => '',
                ),

                array(
                    'type' => 'textfield',
                    'param_name' => 'heading',
                    'heading' => __('Heading for the grid', 'livemesh-vc-addons'),
                ),

                array(
                    'type' => 'checkbox',
                    'param_name' => 'filterable',
                    'heading' => __('Filterable?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true')
                ),

                array(
                    'type' => 'dropdown',
                    'param_name' => 'layout_mode',
                    'heading' => __('Choose a layout for the portfolio/blog', 'livemesh-vc-addons'),
                    'value' => array(
                        __('Fit Rows', 'livemesh-vc-addons') => 'fitRows',
                        __('Masonry', 'livemesh-vc-addons') => 'masonry',
                    ),
                    'std' => 'fitRows'
                ),

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

                array(
                    'type' => 'checkbox',
                    "param_name" => "display_item_title",
                    'heading' => __('Display Item Title?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true')
                ),

                array(
                    'type' => 'checkbox',
                    "param_name" => "display_item_tags",
                    'heading' => __('Display Item Tags?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true')
                ),
            );

            $pagination_params = array(

                array(
                    'type' => 'dropdown',
                    'param_name' => 'pagination',
                    'heading' => __('Pagination', 'livemesh-vc-addons'),
                    'description' => __('Choose pagination type or choose None if no pagination is desired. Make sure you enter the items per page value in the option \'Number of items to be displayed per page and on each load more invocation\' field below to control number of items to display per page.', 'livemesh-vc-addons'),
                    'value' => array(
                        __('None', 'livemesh-vc-addons') => 'none',
                        __('Paged', 'livemesh-vc-addons') => 'paged',
                        __('Load More', 'livemesh-vc-addons') => 'load_more',
                    ),
                    'std' => 'none',
                    'group' => __('Pagination', 'livemesh-vc-addons')
                ),


                array(
                    'type' => 'checkbox',
                    "param_name" => "show_remaining",
                    'heading' => __('Display count of items yet to be loaded with the load more button?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Pagination', 'livemesh-vc-addons'),
                    'dependency' => array(
                        'element' => 'pagination',
                        'value' => array('load_more'),
                    ),
                ),

                array(
                    'type' => 'lvca_number',
                    'param_name' => 'items_per_page',
                    'heading' => __('Number of items to be displayed per page and on each load more invocation.', 'livemesh-vc-addons'),
                    'value' => 8,
                    'group' => __('Pagination', 'livemesh-vc-addons'),
                    'dependency' => array(
                        'element' => 'pagination',
                        'value_not_equal_to' => array('none'),
                    ),
                ),
            );

            $responsive_params = array(

                array(
                    "type" => "lvca_number",
                    "param_name" => "per_line",
                    "value" => 3,
                    "min" => 1,
                    "max" => 6,
                    'integer' => true,
                    "suffix" => '',
                    "heading" => __("Columns per row", "livemesh-vc-addons"),
                    "description" => __("The number of gallery items to display per row of the gallery", "livemesh-vc-addons"),
                    'group' => __('Responsiveness', 'livemesh-vc-addons')
                ),

                array(
                    "type" => "lvca_number",
                    "param_name" => "per_line_tablet",
                    "value" => 2,
                    "min" => 1,
                    "max" => 6,
                    'integer' => true,
                    "suffix" => '',
                    "heading" => __("Columns per row in Tablet Resolution", "livemesh-vc-addons"),
                    "description" => __("The number of gallery items to display per row of the gallery in tablet resolution", "livemesh-vc-addons"),
                    'group' => __('Responsiveness', 'livemesh-vc-addons')
                ),

                array(
                    "type" => "lvca_number",
                    "param_name" => "per_line_mobile",
                    "value" => 1,
                    "min" => 1,
                    "max" => 4,
                    'integer' => true,
                    "suffix" => '',
                    "heading" => __("Columns per row in Mobile Resolution", "livemesh-vc-addons"),
                    "description" => __("The number of gallery items to display per row of the gallery in mobile resolution", "livemesh-vc-addons"),
                    'group' => __('Responsiveness', 'livemesh-vc-addons')
                ),

                array(
                    'type' => 'lvca_number',
                    'param_name' => 'gutter',
                    'heading' => __('Gutter', 'livemesh-vc-addons'),
                    'description' => __('Space between columns.', 'livemesh-vc-addons'),
                    'value' => 20,
                    'group' => __('Responsiveness', 'livemesh-vc-addons')
                ),

                array(
                    'type' => 'lvca_number',
                    'param_name' => 'tablet_gutter',
                    'heading' => __('Gutter in Tablets', 'livemesh-vc-addons'),
                    'description' => __('Space between columns in tablets.', 'livemesh-vc-addons'),
                    'value' => 10,
                    'group' => __('Responsiveness', 'livemesh-vc-addons')
                ),

                array(
                    'type' => 'textfield',
                    'param_name' => 'tablet_width',
                    'heading' => __('Tablet Resolution', 'livemesh-vc-addons'),
                    'description' => __('The resolution to treat as a tablet resolution.', 'livemesh-vc-addons'),
                    'std' => 960,
                    'sanitize' => 'intval',
                    'group' => __('Responsiveness', 'livemesh-vc-addons')
                ),

                array(
                    'type' => 'lvca_number',
                    'param_name' => 'mobile_gutter',
                    'heading' => __('Gutter in Mobiles', 'livemesh-vc-addons'),
                    'description' => __('Space between columns in mobiles.', 'livemesh-vc-addons'),
                    'value' => 10,
                    'group' => __('Responsiveness', 'livemesh-vc-addons')
                ),

                array(
                    'type' => 'textfield',
                    'param_name' => 'mobile_width',
                    'heading' => __('Mobile Resolution', 'livemesh-vc-addons'),
                    'description' => __('The resolution to treat as a mobile resolution.', 'livemesh-vc-addons'),
                    'std' => 480,
                    'sanitize' => 'intval',
                    'group' => __('Responsiveness', 'livemesh-vc-addons')
                )
            );

            $params = array_merge($general_params, $pagination_params, $responsive_params);

            //Register "container" content element. It will hold all your inner (child) content elements
            vc_map(array(
                "name" => __("Gallery", "livemesh-vc-addons"),
                "base" => "lvca_gallery",
                "as_parent" => array('only' => 'lvca_gallery_item'), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
                "content_element" => true,
                "show_settings_on_create" => true,
                "category" => __("Livemesh Addons", "livemesh-vc-addons"),
                "is_container" => true,
                'description' => __('Display images or videos in a multi-column grid.', 'livemesh-vc-addons'),
                "js_view" => 'VcColumnView',
                "icon" => 'icon-lvca-gallery',
                "params" => $params
            ));

        }
    }


    function map_child_vc_element() {
        if (function_exists("vc_map")) {

            vc_map(array(
                    "name" => __("Gallery Item", "livemesh-vc-addons"),
                    "base" => "lvca_gallery_item",
                    "as_child" => array('only' => 'lvca_gallery'), // Use only|except attributes to limit parent (separate multiple values with comma)
                    "icon" => 'icon-lvca-gallery-add',
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
                            'type' => 'checkbox',
                            "param_name" => "display_video_inline",
                            'heading' => __('Display Video Inline?', 'livemesh-vc-addons'),
                            "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                            'dependency' => array(
                                'element' => 'item_type',
                                'value_not_equal_to' => array('image'),
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
    class WPBakeryShortCode_lvca_gallery extends WPBakeryShortCodesContainer {
    }
}
if (class_exists('WPBakeryShortCode')) {
    class WPBakeryShortCode_lvca_gallery_item extends WPBakeryShortCode {
    }
}

// Initialize Element Class
if (class_exists('LVCA_Gallery')) {
    new LVCA_Gallery();
}