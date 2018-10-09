<?php

/*
Widget Name: Posts Grid
Description: Display posts or custom post types in a multi-column grid.
Author: LiveMesh
Author URI: https://www.livemeshthemes.com
*/

class LVCA_Portfolio {

    static public $grid_counter = 0;

    /**
     * Get things started
     */
    public function __construct() {

        add_action('wp_enqueue_scripts', array($this, 'load_scripts'));

        add_action('wp_enqueue_scripts', array($this, 'localize_scripts'), 999999);

        add_shortcode('lvca_portfolio', array($this, 'shortcode_func'));

        // Do it as late as possible so that all taxonomies are registered
        add_action('init', array($this, 'map_vc_element'), 9999);

    }

    function load_scripts() {

        wp_enqueue_script('lvca-isotope', LVCA_PLUGIN_URL . 'assets/js/isotope.pkgd' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_script('lvca-imagesloaded', LVCA_PLUGIN_URL . 'assets/js/imagesloaded.pkgd' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_script('lvca-blocks', LVCA_PLUGIN_URL . 'assets/js/premium/lvca-blocks' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_style('lvca-blocks', LVCA_PLUGIN_URL . 'assets/css/premium/lvca-blocks.css', array(), LVCA_VERSION);

        wp_enqueue_script('lvca-fancybox', LVCA_PLUGIN_URL . 'assets/js/premium/jquery.fancybox' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_style('lvca-fancybox', LVCA_PLUGIN_URL . 'assets/css/premium/jquery.fancybox.css', array(), LVCA_VERSION);

        wp_enqueue_style('lvca-premium-frontend-styles', LVCA_PLUGIN_URL . 'assets/css/premium/lvca-frontend.css', array(), LVCA_VERSION);

        wp_enqueue_script('lvca-portfolio', plugin_dir_url(__FILE__) . 'js/portfolio' . LVCA_JS_SUFFIX . '.js', array('jquery'), LVCA_VERSION);

        wp_enqueue_style('lvca-portfolio', plugin_dir_url(__FILE__) . 'css/style.css', array(), LVCA_VERSION);

    }

    public function localize_scripts() {

        wp_localize_script('lvca-frontend-scripts', 'lvca_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

    }

    public function shortcode_func($atts, $content = null, $tag) {

        $defaults = array_merge(
            array(
                'posts_query' => '',
                'taxonomy_filter' => 'category',
                'block_type' => 'block_grid_1',
                'per_line' => 3,
                'per_line_tablet' => 2,
                'per_line_mobile' => 1,
                'filterable' => '',
                'enable_lightbox' => '',
                'image_size' => 'large',
                'header_template' => 'block_header_6',
                'heading' => '',
                'heading_url' => '',
                'image_linkable' => '',
                'post_link_new_window' => '',
                'display_title_on_thumbnail' => '',
                'display_taxonomy_on_thumbnail' => '',
                'display_title' => '',
                'display_summary' => '',
                'rich_text_excerpt' => '',
                'excerpt_length' => 25,
                'display_read_more' => '',
                'display_excerpt_lightbox' => '',
                'display_author' => '',
                'display_post_date' => '',
                'display_taxonomy' => '',
                'display_comments' => '',
                'pagination' => 'none',
                'show_remaining' => '',
                'post_type' => 'post',
                'layout_mode' => 'fitRows',
                'gutter' => 20,
                'tablet_gutter' => 10,
                'tablet_width' => 800,
                'mobile_gutter' => 10,
                'mobile_width' => 480
            )

        );

        $settings = shortcode_atts($defaults, $atts);

        self::$grid_counter++;

        $settings['block_class'] = !empty($settings['grid_class']) ? sanitize_title($settings['grid_class']) : 'grid-' . self::$grid_counter;

        $settings = lvca_parse_block_settings($settings);

        $block = LVCA_Blocks_Manager::get_instance($settings['block_type']);

        $output = $block->render($settings);

        return $output;
    }


    function map_vc_element() {
        if (function_exists("vc_map")) {

            $general_params = array(
                array(
                    'type' => 'loop',
                    'param_name' => 'posts_query',
                    'heading' => __('Posts query', 'livemesh-vc-addons'),
                    'value' => 'size:6|order_by:date',
                    'settings' => array(
                        'size' => array(
                            'hidden' => false,
                            'value' => 6,
                        ),
                        'order_by' => array('value' => 'date'),
                        'post_type' => array(
                            'hidden' => false,
                            'value' => 'post',
                        ),
                    ),
                    'description' => __('Create WordPress loop, to populate content from your site. After you build the query, make sure you choose the right taxonomy below to display for your posts and filter on, based on the post type selected during build query.', 'livemesh-vc-addons'),
                    'admin_label' => true
                ),


                array(
                    'type' => 'textfield',
                    'param_name' => 'grid_class',
                    'description' => __('Specify an unique identifier used as a custom CSS class name and lightbox group name/slug for the grid element.(optional)', 'livemesh-vc-addons'),
                    'heading' => __('Grid Class/Identifier', 'livemesh-vc-addons'),
                ),

                array(
                    'type' => 'dropdown',
                    'param_name' => 'taxonomy_filter',
                    'heading' => __('Choose the taxonomy to display and filter on.', 'livemesh-vc-addons'),
                    'description' => __('Choose the taxonomy information to display for posts/portfolio and the taxonomy that is used to filter the posts/portfolio items. Takes effect only if no query category/tag/taxonomy filters are specified when building query.', 'livemesh-vc-addons'),
                    'value' => lvca_get_taxonomies_map(),
                    'std' => 'category',
                ),
                array(
                    'type' => 'checkbox',
                    "param_name" => "enable_lightbox",
                    'heading' => __('Enable Lightbox Gallery?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                ),

                array(
                    'type' => 'dropdown',
                    'param_name' => 'image_size',
                    'heading' => __('Image Size', 'livemesh-vc-addons'),
                    'std' => 'large',
                    'value' => lvca_get_image_sizes()
                ),
            );

            $layout_params = array(


                array(
                    'type' => 'dropdown',
                    'param_name' => 'layout_mode',
                    'heading' => __('Choose a layout for the portfolio/blog', 'livemesh-vc-addons'),
                    'value' => array(
                        __('Fit Rows', 'livemesh-vc-addons') => 'fitRows',
                        __('Masonry', 'livemesh-vc-addons') => 'masonry',
                    ),
                    'std' => 'fitRows',
                    'group' => __('Layout', 'livemesh-vc-addons')
                ),
                array(
                    'type' => 'dropdown',
                    'param_name' => 'block_type',
                    'heading' => __('Choose Block Style', 'livemesh-vc-addons'),
                    'value' => array(
                        __('Grid Style 1', 'livemesh-vc-addons') => 'block_grid_1',
                        __('Grid Style 2', 'livemesh-vc-addons') => 'block_grid_2',
                        __('Grid Style 3', 'livemesh-vc-addons') => 'block_grid_3',
                        __('Grid Style 4', 'livemesh-vc-addons') => 'block_grid_4',
                        __('Grid Style 5', 'livemesh-vc-addons') => 'block_grid_5',
                        __('Grid Style 6', 'livemesh-vc-addons') => 'block_grid_6',
                    ),
                    'std' => 'block_grid_1',
                    'group' => __('Layout', 'livemesh-vc-addons')
                ),

                array(
                    "type" => "lvca_number",
                    "param_name" => "per_line",
                    "value" => 3,
                    "min" => 1,
                    "max" => 6,
                    "suffix" => '',
                    "heading" => __("Columns per row", "livemesh-vc-addons"),
                    "description" => __("The number of columns to display per row of the posts grid", "livemesh-vc-addons"),
                    'group' => __('Layout', 'livemesh-vc-addons')
                ),

                array(
                    "type" => "lvca_number",
                    "param_name" => "per_line_tablet",
                    "value" => 2,
                    "min" => 1,
                    "max" => 6,
                    "suffix" => '',
                    "heading" => __("Columns per row in Tablet Resolution", "livemesh-vc-addons"),
                    "description" => __("The number of columns to display per row of the posts grid in tablet resolution", "livemesh-vc-addons"),
                    'group' => __('Layout', 'livemesh-vc-addons')
                ),

                array(
                    "type" => "lvca_number",
                    "param_name" => "per_line_mobile",
                    "value" => 1,
                    "min" => 1,
                    "max" => 4,
                    "suffix" => '',
                    "heading" => __("Columns per row in Mobile Resolution", "livemesh-vc-addons"),
                    "description" => __("The number of columns to display per row of the posts grid in mobile resolution", "livemesh-vc-addons"),
                    'group' => __('Layout', 'livemesh-vc-addons')
                ),
            );

            $heading_params = array(

                array(
                    'type' => 'checkbox',
                    'param_name' => 'filterable',
                    'heading' => __('Display Category/Taxonomy Filters?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Block Header', 'livemesh-vc-addons'),
                ),


                array(
                    'type' => 'textfield',
                    'param_name' => 'heading',
                    'heading' => __('Heading for the posts block', 'livemesh-vc-addons'),
                    'group' => __('Block Header', 'livemesh-vc-addons'),
                ),

                array(
                    'type' => 'textfield',
                    'param_name' => 'heading_url',
                    'heading' => __('URL for the heading', 'livemesh-vc-addons'),
                    'group' => __('Block Header', 'livemesh-vc-addons'),
                ),

                array(
                    'type' => 'dropdown',
                    'param_name' => 'header_template',
                    'heading' => __('Choose Header Style', 'livemesh-vc-addons'),
                    'value' => array(
                        __('Header Style 1', 'livemesh-vc-addons') => 'block_header_1',
                        __('Header Style 2', 'livemesh-vc-addons') => 'block_header_2',
                        __('Header Style 3', 'livemesh-vc-addons') => 'block_header_3',
                        __('Header Style 4', 'livemesh-vc-addons') => 'block_header_4',
                        __('Header Style 5', 'livemesh-vc-addons') => 'block_header_5',
                        __('Header Style 6', 'livemesh-vc-addons') => 'block_header_6',
                        __('Header Style 7', 'livemesh-vc-addons') => 'block_header_7',
                    ),
                    'std' => 'block_header_6',
                    'group' => __('Block Header', 'livemesh-vc-addons'),
                ),

            );

            $display_params = array(
                array(
                    'type' => 'checkbox',
                    'param_name' => 'image_linkable',
                    'heading' => __('Link Images to Posts?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Post Info', 'livemesh-vc-addons'),
                ),

                array(
                    'type' => 'checkbox',
                    'param_name' => 'post_link_new_window',
                    'heading' => __('Open post links in new window?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Post Info', 'livemesh-vc-addons'),
                ),

                array(
                    'type' => 'checkbox',
                    'param_name' => 'display_title_on_thumbnail',
                    'heading' => __('Display project title on post/project thumbnail?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Post Info', 'livemesh-vc-addons'),
                    'dependency' => array(
                        'element' => 'block_type',
                        'value' => array('block_grid_1', 'block_grid_6'),
                    ),
                ),

                array(
                    'type' => 'checkbox',
                    'param_name' => 'display_taxonomy_on_thumbnail',
                    'heading' => __('Display taxonomy info on post/project thumbnail?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Post Info', 'livemesh-vc-addons'),
                    'dependency' => array(
                        'element' => 'block_type',
                        'value' => array('block_grid_1', 'block_grid_6'),
                    ),
                ),

                array(
                    'type' => 'checkbox',
                    'param_name' => 'display_title',
                    'heading' => __('Display posts title for the post/portfolio item?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Post Info', 'livemesh-vc-addons'),
                ),

                array(
                    'type' => 'checkbox',
                    'param_name' => 'display_summary',
                    'heading' => __('Display post excerpt/summary for the post/portfolio item?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Post Info', 'livemesh-vc-addons'),
                    'dependency' => array(
                        'element' => 'block_type',
                        'value' => array('block_grid_1', 'block_grid_3', 'block_grid_4', 'block_grid_6'),
                    ),
                ),

                array(
                    'type' => 'checkbox',
                    'param_name' => 'rich_text_excerpt',
                    'heading' => __('Preserve shortcodes/HTML tags in excerpt?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Post Info', 'livemesh-vc-addons'),
                    'dependency' => array(
                        'element' => 'block_type',
                        'value' => array('block_grid_1', 'block_grid_3', 'block_grid_4', 'block_grid_6'),
                    ),
                ),

                array(
                    "type" => "lvca_number",
                    "param_name" => "excerpt_length",
                    "value" => '',
                    "min" => 10,
                    "max" => 50,
                    'std' => 25,
                    "suffix" => 'words',
                    "heading" => __("Excerpt Length?", "livemesh-vc-addons"),
                    'group' => __('Post Info', 'livemesh-vc-addons'),
                    'dependency' => array(
                        'element' => 'block_type',
                        'value' => array('block_grid_1', 'block_grid_3', 'block_grid_4', 'block_grid_6'),
                    ),
                ),

                array(
                    'type' => 'checkbox',
                    'param_name' => 'display_read_more',
                    'heading' => __('Display read more link the post/portfolio item?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Post Info', 'livemesh-vc-addons'),
                    'dependency' => array(
                        'element' => 'block_type',
                        'value' => array('block_grid_1', 'block_grid_3', 'block_grid_4', 'block_grid_6'),
                    ),
                ),

                array(
                    'type' => 'checkbox',
                    'param_name' => 'display_excerpt_lightbox',
                    'heading' => __('Display post excerpt/summary in the lightbox?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Post Info', 'livemesh-vc-addons'),
                ),

                array(
                    'type' => 'checkbox',
                    'param_name' => 'display_author',
                    'heading' => __('Display post author info for the post item?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Post Info', 'livemesh-vc-addons'),
                    'dependency' => array(
                        'element' => 'block_type',
                        'value' => array('block_grid_1', 'block_grid_2', 'block_grid_3', 'block_grid_4', 'block_grid_6'),
                    ),
                ),

                array(
                    'type' => 'checkbox',
                    'param_name' => 'display_post_date',
                    'heading' => __('Display post date info for the post item?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Post Info', 'livemesh-vc-addons'),
                    'dependency' => array(
                        'element' => 'block_type',
                        'value' => array('block_grid_1', 'block_grid_2', 'block_grid_3', 'block_grid_4', 'block_grid_6'),
                    ),
                ),

                array(
                    'type' => 'checkbox',
                    'param_name' => 'display_comments',
                    'heading' => __('Display comments number for the post item?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Post Info', 'livemesh-vc-addons'),
                    'dependency' => array(
                        'element' => 'block_type',
                        'value' => array('block_grid_1', 'block_grid_2', 'block_grid_3', 'block_grid_4', 'block_grid_6'),
                    ),
                ),

                array(
                    'type' => 'checkbox',
                    'param_name' => 'display_taxonomy',
                    'heading' => __('Display taxonomy info for the post/portfolio item?', 'livemesh-vc-addons'),
                    "value" => array(__("Yes", "livemesh-vc-addons") => 'true'),
                    'group' => __('Post Info', 'livemesh-vc-addons'),
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
                        __('Next Prev', 'livemesh-vc-addons') => 'next_prev',
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
            );

            $responsive_params = array(

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
                    'std' => 800,
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

            $params = array_merge($general_params, $layout_params, $heading_params, $display_params, $pagination_params, $responsive_params);

            //Register "container" content element. It will hold all your inner (child) content elements
            vc_map(array(
                "name" => __("Posts Grid", "livemesh-vc-addons"),
                "base" => "lvca_portfolio",
                "content_element" => true,
                "show_settings_on_create" => true,
                "category" => __("Livemesh Addons", "livemesh-vc-addons"),
                'description' => __('Display posts or custom post types in a multi-column grid.', 'livemesh-vc-addons'),
                "icon" => 'icon-lvca-portfolio',
                "params" => $params
            ));


        }
    }

}

if (class_exists('WPBakeryShortCode')) {
    class WPBakeryShortCode_lvca_portfolio extends WPBakeryShortCode {
    }
}

// Initialize Element Class
if (class_exists('LVCA_Portfolio')) {
    new LVCA_Portfolio();
}