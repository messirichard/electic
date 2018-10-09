<?php

/**
 * Gallery class.
 *
 */
class LVCA_Gallery_Common {

    /**
     * Holds the class object.
     */
    public static $instance;

    /**
     * Primary class constructor.
     * 
     */
    public function __construct() {

        add_filter('attachment_fields_to_edit', array($this, 'attachment_field_grid_width'), 10, 2);
        add_filter('attachment_fields_to_save', array($this, 'attachment_field_grid_width_save'), 10, 2);

        // Ajax calls
        add_action('wp_ajax_lvca_load_gallery_items', array( $this, 'load_gallery_items_callback'));
        add_action('wp_ajax_nopriv_lvca_load_gallery_items', array( $this, 'load_gallery_items_callback'));

    }

    public function attachment_field_grid_width( $form_fields, $post ) {
        $form_fields['lvca_grid_width'] = array(
            'label' => esc_html__( 'Masonry Width', 'livemesh-vc-addons' ),
            'input' => 'html',
            'html' => '
<select name="attachments[' . $post->ID . '][lvca_grid_width]" id="attachments-' . $post->ID . '-lvca_grid_width">
  <option ' . selected(get_post_meta( $post->ID, 'lvca_grid_width', true ), "lvca-default", false) .' value="lvca-default">' . esc_html__('Default', 'livemesh-vc-addons') .'</option>
  <option ' . selected(get_post_meta( $post->ID, 'lvca_grid_width', true ), "lvca-wide", false) .' value="lvca-wide">' . esc_html__('Wide', 'livemesh-vc-addons') .'</option>
</select>',
            'value' => get_post_meta( $post->ID, 'lvca_grid_width', true ),
            'helps' => esc_html__('Width of the image in masonry gallery grid', 'livemesh-vc-addons')
        );

        return $form_fields;
    }

    public function attachment_field_grid_width_save( $post, $attachment ) {
        if( isset( $attachment['lvca_grid_width'] ) )
            update_post_meta( $post['ID'], 'lvca_grid_width', $attachment['lvca_grid_width'] );

        return $post;
    }


    function load_gallery_items_callback() {
        $items = $this->parse_items($_POST['items']);
        $settings = $this->parse_gallery_settings($_POST['settings']);
        $paged = intval($_POST['paged']);

        $this->display_gallery($items, $settings, $paged);

        wp_die();

    }

    function parse_items($items) {

        $parsed_items = array();

        foreach ($items as $item):

            // Remove encoded quotes or other characters
            $item['name'] = stripslashes($item['name']);

            $item['description'] = stripslashes($item['description']);

            $item['link'] = isset($item['link']) ? filter_var($item['link'], FILTER_DEFAULT) : '';

            $item['video_link'] = isset($item['video_link']) ? filter_var($item['video_link'], FILTER_DEFAULT) : '';

            $item['mp4_video_link'] = isset($item['mp4_video_link']) ? filter_var($item['mp4_video_link'], FILTER_DEFAULT) : '';

            $item['webm_video_link'] = isset($item['webm_video_link']) ? filter_var($item['webm_video_link'], FILTER_DEFAULT) : '';

            $item['display_video_inline'] = isset($item['display_video_inline']) ? filter_var($item['display_video_inline'], FILTER_VALIDATE_BOOLEAN) : false;

            $parsed_items[] = $item;

        endforeach;

        return $parsed_items;
    }

    function parse_gallery_settings($settings) {

        $s = $settings;

        $s['gallery_class'] = filter_var($s['gallery_class'], FILTER_DEFAULT);

        $s['filterable'] = filter_var($s['filterable'], FILTER_VALIDATE_BOOLEAN);

        $s['per_line'] = filter_var($s['per_line'], FILTER_VALIDATE_INT);

        $s['per_line_tablet'] = filter_var($s['per_line_tablet'], FILTER_VALIDATE_INT);

        $s['per_line_mobile'] = filter_var($s['per_line_mobile'], FILTER_VALIDATE_INT);

        $s['items_per_page'] = filter_var($s['items_per_page'], FILTER_VALIDATE_INT);

        $s['enable_lightbox'] = filter_var($s['enable_lightbox'], FILTER_VALIDATE_BOOLEAN);

        $s['display_item_tags'] = filter_var($s['display_item_tags'], FILTER_VALIDATE_BOOLEAN);

        $s['display_item_title'] = filter_var($s['display_item_title'], FILTER_VALIDATE_BOOLEAN);

        return $s;
    }

    function display_gallery($items, $settings, $paged = 1) {

        $gallery_video = LVCA_Gallery_Video::get_instance();

        $items_per_page = intval($settings['items_per_page']); ?>

        <?php
        // If pagination option is chosen, filter the items for the current page
        if ($settings['pagination'] != 'none')
            $items = $this->get_items_to_display($items, $paged, $items_per_page);
        ?>

        <?php foreach ($items as $item): ?>

            <?php

            $item_type = $item['item_type'];

            // No need to populate anything if no image is provided for the image
            if ($item_type == 'image' && empty($item['image']))
                continue;

            $style = '';
            if (!empty($item['tags'])) {
                $terms = array_map('trim', explode(',', $item['tags']));

                foreach ($terms as $term) {
                    // Get rid of spaces before adding the term
                    $style .= ' term-' . preg_replace('/\s+/', '-', $term);
                }
            }
            ?>

            <?php

            $item_class = 'lvca-' . $item_type . '-type';

            $custom_class = get_post_meta($item['image'], 'lvca_grid_width', true);

            if ($custom_class !== '')
                $item_class .= ' ' . $custom_class;

            ?>

            <div class="lvca-grid-item lvca-gallery-item <?php echo $style; ?> <?php echo $item_class; ?>">

                <?php if ($gallery_video->is_inline_video($item, $settings)): ?>

                    <?php $gallery_video->display_inline_video($item, $settings); ?>

                <?php else: ?>

                    <div class="lvca-project-image">

                        <?php

                        $link = NULL;

                        if (!empty($item['link'])) {

                            if (function_exists('vc_build_link'))
                                $link = vc_build_link($item['link']);
                            else
                                $link = explode('|', $item['link']);
                        }
                        ?>

                        <?php if ($gallery_video->is_gallery_video($item, $settings)): ?>

                            <?php $image_html = ''; ?>

                            <?php if (isset($item['image']) && !empty($item['image'])): ?>

                                <?php $image_html = wp_get_attachment_image($item['image'], $settings['image_size'], false, array('class' => 'lvca-image large', 'alt' => $item['name'])); ?>

                            <?php elseif ($item_type == 'youtube' || $item_type == 'vimeo') : ?>

                                <?php $thumbnail_url = $gallery_video->get_video_thumbnail_url($item['video_link'], $settings); ?>

                                <?php if (!empty($thumbnail_url)): ?>

                                    <?php $image_html = sprintf('<img src="%s" title="%s" alt="%s" class="lvca-image"/>', esc_attr($thumbnail_url), esc_html($item['name']), esc_html($item['name'])); ?>

                                <?php endif; ?>

                            <?php endif; ?>

                            <?php echo $image_html; ?>

                        <?php else: ?>

                            <?php $image_html = wp_get_attachment_image($item['image'], $settings['image_size'], false, array('class' => 'lvca-image large', 'alt' => $item['name'])); ?>

                            <?php if ($item_type == 'image' && !empty($link['url'])): ?>

                                <a href="<?php echo esc_url($link['url']); ?>"
                                   title="<?php echo esc_html($item['name']); ?>"
                                   target="<?php echo esc_html($link['target']); ?>"><?php echo $image_html; ?> </a>

                            <?php else: ?>

                                <?php echo $image_html; ?>

                            <?php endif; ?>

                        <?php endif; ?>

                        <div class="lvca-image-info">

                            <div class="lvca-entry-info">

                                <?php if ($settings['display_item_title']): ?>

                                    <h3 class="lvca-entry-title">

                                        <?php if ($item_type == 'image' && !empty($link)): ?>

                                            <a href="<?php echo esc_url($link['url']); ?>"
                                               title="<?php echo esc_html($link['title']); ?>"
                                               target="<?php echo esc_html($link['target']); ?>"><?php echo esc_html($item['name']); ?></a>

                                        <?php else: ?>

                                            <?php echo esc_html($item['name']); ?>

                                        <?php endif; ?>

                                    </h3>

                                <?php endif; ?>

                                <?php if ($gallery_video->is_gallery_video($item, $settings)): ?>

                                    <?php $gallery_video->display_video_lightbox_link($item, $settings); ?>

                                <?php endif; ?>

                                <?php if ($settings['display_item_tags']): ?>

                                    <span class="lvca-terms"><?php echo esc_html($item['tags']); ?></span>

                                <?php endif; ?>

                            </div>

                            <?php if ($item_type == 'image' && !empty($item['image']) && $settings['enable_lightbox']) : ?>

                                <?php $anchor_type = (empty($link['url']) ? 'lvca-click-anywhere' : 'lvca-click-icon'); ?>

                                <?php $this->display_image_lightbox_link($item, $settings, $anchor_type); ?>

                            <?php endif; ?>

                        </div>

                    </div>

                <?php endif; ?>

            </div>

            <?php

        endforeach;

    }

    function display_image_lightbox_link($item, $settings, $anchor_type) {

        $image_data = wp_get_attachment_image_src($item['image'], 'full');

        ?>
        <?php if ($image_data) : ?>

            <?php $image_src = $image_data[0]; ?>

            <a class="lvca-lightbox-item <?php echo $anchor_type; ?>"
               data-fancybox="<?php echo $settings['gallery_class']; ?>"
               href="<?php echo $image_src; ?>"
               title="<?php echo esc_html($item['name']); ?>"
               data-description="<?php echo wp_kses_post($item['description']); ?>"><i
                        class="lvca-icon-full-screen"></i></a>

        <?php endif; ?>

        <?php
    }

    function get_gallery_terms($items) {

        $tags = $terms = array();

        foreach ($items as $item) {
            $tags = array_merge($tags, explode(',', $item['tags']));
        }

        // trim whitespaces before applying array_unique
        $tags = array_map('trim', $tags);

        $terms = array_values(array_unique($tags));

        return $terms;

    }

    function get_items_to_display($items, $paged, $items_per_page) {

        $offset = $items_per_page * ($paged - 1);

        $items = array_slice($items, $offset, $items_per_page);

        return $items;
    }

    function paginate_gallery($items, $settings) {

        $pagination_type = $settings['pagination'];

        // no pagination required if option is not chosen by user or if all posts are already displayed
        if ($pagination_type == 'none' || count($items) <= $settings['items_per_page'])
            return;

        $max_num_pages = ceil(count($items) / $settings['items_per_page']);

        $output = '<div class="lvca-pagination">';

        if ($pagination_type == 'load_more') {
            $output .= '<a href="#" class="lvca-load-more lvca-button">';
            $output .= esc_html__('Load More', 'livemesh-vc-addons');
            if ($settings['show_remaining'])
                $output .= ' - ' . '<span>' . (count($items) - $settings['items_per_page']) . '</span>';
            $output .= '</a>';
        }
        else {
            $page_links = array();

            for ($n = 1; $n <= $max_num_pages; $n++) :
                $page_links[] = '<a class="lvca-page-nav' . ($n == 1 ? ' lvca-current-page' : '') . '" href="#" data-page="' . $n . '">' . number_format_i18n($n) . '</a>';
            endfor;

            $r = join("\n", $page_links);

            if (!empty($page_links)) {
                $prev_link = '<a class="lvca-page-nav lvca-disabled" href="#" data-page="prev"><i class="lvca-icon-arrow-left3"></i></a>';
                $next_link = '<a class="lvca-page-nav" href="#" data-page="next"><i class="lvca-icon-arrow-right3"></i></a>';

                $output .= $prev_link . "\n" . $r . "\n" . $next_link;
            }
        }

        $output .= '<span class="lvca-loading"></span>';

        $output .= '</div>';

        return $output;

    }

    /** Isotope filtering support for Gallery * */

    function get_gallery_terms_filter($terms) {

        $output = '';

        if (!empty($terms)) {

            $output .= '<div class="lvca-taxonomy-filter">';

            $output .= '<div class="lvca-filter-item segment-0 lvca-active"><a data-value="*" href="#">' . esc_html__('All', 'livemesh-vc-addons') . '</a></div>';

            $segment_count = 1;
            foreach ($terms as $term) {

                $output .= '<div class="lvca-filter-item segment-' . intval($segment_count) . '"><a href="#" data-value=".term-' . preg_replace('/\s+/', '-', $term) . '" title="' . esc_html__('View all items filed under ', 'livemesh-vc-addons') . esc_attr($term) . '">' . esc_html($term) . '</a></div>';

                $segment_count++;
            }

            $output .= '</div>';

        }

        return $output;
    }

    /**
     * Returns the singleton instance of the class.
     * 
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof LVCA_Gallery_Common ) ) {
            self::$instance = new LVCA_Gallery_Common();
        }

        return self::$instance;

    }

}

// Load the metabox class.
$lvca_gallery_common = LVCA_Gallery_Common::get_instance();


