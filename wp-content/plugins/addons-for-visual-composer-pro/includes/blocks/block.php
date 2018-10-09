<?php

abstract class LVCA_Block {

    protected $block_uid;

    protected $wp_query;

    protected $query_args;

    protected $settings = array();

    protected $block_header_obj;

    /* Force override  */
    abstract function get_block_class();

    /* Force override  */
    abstract function inner($posts, $settings);

    function init($settings) {

        $output = '';

        $this->add_related_posts_params($settings);

        $defaults = array(
            'class' => '',
            'heading' => '',
            'heading_url' => '',
            'block_type' => 'block_1',
            'filterable' => true,
            'taxonomy_chosen' => 'category',
            'current_filter_term' => '',
            'pagination' => 'none',
            'show_remaining' => true,
            'show_related_posts' => false,
            'current_post_id' => '',
        );

        $this->settings = wp_parse_args($settings, $defaults);

        $posts_query = $settings['posts_query'];

        if (is_array($posts_query)) {
            $posts_query['post_status'] = 'publish';
        }
        else {
            $posts_query .= '|post_status:publish';
        }
        if (function_exists('vc_build_loop_query')) {

            list($query_args, $loop) = vc_build_loop_query($posts_query);

        }
        else {

            // just display first 6 posts if the user came directly to this shortcode
            $query_args = array('posts_per_page' => 6, 'ignore_sticky_posts' => 1);

            $loop = new WP_Query($query_args);
        }

        $this->query_args = $query_args;

        $this->wp_query = $loop;

        $this->block_uid = 'lvca-block-uid-' . uniqid();

        $this->add_class($this->block_uid);

        $block_filter_terms = $this->get_block_filter_terms();

        $block_header_args = array(
            'settings' => $this->settings,
            'block_uid' => $this->block_uid,
            'block_filter_terms' => $block_filter_terms
        );

        $block_header_class = LVCA_Blocks_Manager::get_class_name($this->settings['header_template']);

        $this->block_header_obj = new $block_header_class($block_header_args);

        return $output;

    }

    function render($settings) {

        $output = $this->init($settings);

        $output .= '<div id="' . $this->block_uid . '" class="' . $this->get_block_classes() . '" ' . $this->get_block_data_atts() . '>';

        $output .= $this->get_block_header();

        // add container class to enable column styling
        $output .= '<div class="lvca-block-inner lvca-grid-container '. $this->get_grid_classes($settings) . '">';

        $output .= $this->inner($this->wp_query->posts, $this->settings);

        $output .= '</div><!-- .block-inner -->';

        $output .= $this->get_block_pagination();

        $output .= '</div><!-- .block -->';

        return $output;

    }

    // get atts
    protected function get_block_data_atts() {

        $output = '';

        $output .= " data-block-uid='" . $this->block_uid . "'";

        $output .= " data-query='" . wp_json_encode($this->query_args) . "'";

        $output .= " data-settings='" . wp_json_encode($this->get_settings_data_atts()) . "'";

        $output .= " data-prefs='" . wp_json_encode($this->get_prefs_data_atts()) . "'";

        $output .= " data-taxonomies='" . wp_json_encode($this->settings['taxonomies']) . "'";

        $output .= " data-current='1'";

        $output .= " data-maxpages='" . $this->wp_query->max_num_pages . "'";

        // will be populated later when filter links are clicked by user
        $output .= " data-filter-term=''";
        $output .= " data-filter-taxonomy=''";

        return $output;

    }

    protected function get_settings_data_atts() {

        $data_atts = array();

        /* Block Content */

        $data_atts['block_class'] = $this->settings['block_class'];

        $data_atts['heading'] = $this->settings['heading'];

        $data_atts['heading_url'] = $this->settings['heading_url'];

        $data_atts['taxonomy_chosen'] = $this->settings['taxonomy_chosen'];

        $data_atts['header_template'] = $this->settings['header_template'];

        $data_atts['block_type'] = $this->settings['block_type'];

        /* Post Content */
        $data_atts['display_title_on_thumbnail'] = $this->settings['display_title_on_thumbnail'];

        $data_atts['display_taxonomy_on_thumbnail'] = $this->settings['display_taxonomy_on_thumbnail'];

        $data_atts['display_title'] = $this->settings['display_title'];

        $data_atts['display_summary'] = $this->settings['display_summary'];

        $data_atts['rich_text_excerpt'] = $this->settings['rich_text_excerpt'];

        $data_atts['display_excerpt_lightbox'] = $this->settings['display_excerpt_lightbox'];

        $data_atts['display_read_more'] = $this->settings['display_read_more'];

        $data_atts['display_author'] = $this->settings['display_author'];

        $data_atts['display_post_date'] = $this->settings['display_post_date'];

        $data_atts['display_comments'] = $this->settings['display_comments'];

        $data_atts['display_taxonomy'] = $this->settings['display_taxonomy'];

        /* Block Settings */

        $data_atts['filterable'] = $this->settings['filterable'];

        $data_atts['layout_mode'] = $this->settings['layout_mode'];

        $data_atts['per_line'] = $this->settings['per_line'];

        $data_atts['per_line_tablet'] = $this->settings['per_line_tablet'];

        $data_atts['per_line_mobile'] = $this->settings['per_line_mobile'];

        $data_atts['per_line1'] = $this->settings['per_line1'];

        $data_atts['per_line2'] = $this->settings['per_line2'];

        $data_atts['per_line2_tablet'] = $this->settings['per_line2_tablet'];

        $data_atts['per_line2_mobile'] = $this->settings['per_line2_mobile'];

        $data_atts['image_linkable'] = $this->settings['image_linkable'];

        $data_atts['post_link_new_window'] = $this->settings['post_link_new_window'];

        $data_atts['excerpt_length'] = $this->settings['excerpt_length'];

        $data_atts['enable_lightbox'] = $this->settings['enable_lightbox'];

        $data_atts['image_size'] = $this->settings['image_size'];

        /* Pagination */

        $data_atts['pagination'] = $this->settings['pagination'];

        $data_atts['show_remaining'] = $this->settings['show_remaining'];

        /* Derived Attributes */

        $data_atts['taxonomies'] = $this->settings['taxonomies'];

        return $data_atts;

    }

    protected function get_prefs_data_atts() {

        return array();

    }

    function get_block_header() {

        return $this->block_header_obj->get_block_header();

    }

    private function add_class($class_name) {

        if (!empty($this->settings['block_class'])) {

            $this->settings['class'] = $this->settings['block_class'] . ' ' . $class_name;
        }
        else {
            $this->settings['class'] = $class_name;
        }
    }

    private function add_related_posts_params($settings) {

        if (!empty($settings['show_related_posts'])) {

            $settings['current_post_id'] = get_queried_object_id();

        }
    }

    private function get_block_filter_terms() {

        $block_filter_terms = array();

        // Check if any taxonomy filter has been applied
        list($chosen_terms, $taxonomies) = lvca_get_chosen_terms($this->query_args);

        if (empty($chosen_terms))
            $taxonomies[] = $this->settings['taxonomy_chosen'];

        $this->settings['taxonomies'] = $taxonomies;

        if ($this->settings['filterable']) {

            if (empty($chosen_terms)) {

                global $wp_version;

                if (version_compare($wp_version, '4.5', '>=')) {
                    $terms = get_terms($taxonomies);
                }
                else {
                    $terms = get_terms($taxonomies[0]);
                }
            }
            else {
                $terms = $chosen_terms;
            }

            if (!empty($terms) && !is_wp_error($terms)) {
                $block_filter_terms = $terms;
            }
        }

        return $block_filter_terms;

    }


    function get_block_pagination() {

        $loop = $this->wp_query;

        $pagination_type = $this->settings['pagination'];

        // no pagination required if option is not chosen by user or if all posts are already displayed
        if ($pagination_type == 'none' || $loop->max_num_pages == 1)
            return;


        $output = '<div class="lvca-pagination ' . 'lvca-' . preg_replace('/_/', '-', $pagination_type) . '-nav">';

        switch ($pagination_type) {

            case 'next_prev':

                $output .= '<a class="lvca-page-nav lvca-disabled" href="#" data-page="prev"><i class="lvca-icon-arrow-left3"></i></a>';

                $output .= '<a class="lvca-page-nav" href="#" data-page="next"><i class="lvca-icon-arrow-right3"></i></a>';

                break;

            case 'load_more':

                $output .= '<a href="#" class="lvca-load-more lvca-button">';

                $output .= __('Load More', 'livemesh-vc-addons');

                if ($this->settings['show_remaining'])
                    $output .= ' - ' . '<span>' . (intval($loop->found_posts) - $loop->post_count) . '</span>';

                $output .= '</a>';

                break;

            case 'paged':

                $page_links = array();

                for ($n = 1; $n <= $loop->max_num_pages; $n++) :
                    $page_links[] = '<a class="lvca-page-nav lvca-numbered' . ($n == 1 ? ' lvca-current-page' : '') . '" href="#" data-page="' . $n . '">' . number_format_i18n($n) . '</a>';
                endfor;

                $r = join("\n", $page_links);

                if (!empty($page_links)) {
                    $prev_link = '<a class="lvca-page-nav lvca-disabled" href="#" data-page="prev"><i class="lvca-icon-arrow-left3"></i></a>';
                    $next_link = '<a class="lvca-page-nav" href="#" data-page="next"><i class="lvca-icon-arrow-right3"></i></a>';

                    $output .= $prev_link . "\n" . $r . "\n" . $next_link;
                }

                break;
        }

        $output .= '<span class="lvca-loading"></span>';

        $output .= '</div><!-- .lvca-pagination -->';

        return $output;
    }

    function get_column_class($column_size = 3) {

        // Ignore column size since grid class will determine the column size
        $style_class = 'lvca-grid-item';

        return $style_class;
    }

    function get_grid_classes_from_field($settings, $columns_field) {

        return lvca_get_grid_classes($settings, $columns_field);

    }

    protected function get_block_classes($classes_array = array()) {

        $block_classes = array();

        // add container class to enable column styling
        $block_classes[] = 'lvca-container';

        // add block wrap
        $block_classes[] = 'lvca-block';

        // add block id for styling
        $block_classes[] = $this->get_block_class();

        // add block header type for styling
        $block_classes[] = $this->block_header_obj->get_block_header_class();

        //add the classes that we receive via settings and those which are set based on block id
        $class = $this->settings['class'];
        if (!empty($class)) {
            $class_array = explode(' ', $class);
            $block_classes = array_merge(
                $block_classes,
                $class_array
            );
        }

        //marge the additional classes received from blocks code
        if (!empty($classes_array)) {
            $block_classes = array_merge(
                $block_classes,
                $classes_array
            );
        }

        //remove duplicates
        $block_classes = array_unique($block_classes);

        return implode(' ', $block_classes);
    }

    protected function get_grid_classes($settings) {

        return $this->get_grid_classes_from_field($settings, 'per_line');

    }


}