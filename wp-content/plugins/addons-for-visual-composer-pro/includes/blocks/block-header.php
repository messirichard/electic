<?php

abstract class LVCA_Block_Header {

    protected $block_header_args;

    function __construct($block_header_args) {

        $this->block_header_args = $block_header_args;

    }

    /* Force override  */
    abstract function get_block_header_class();

    protected function get_setting($setting_name) {

        if (isset($this->block_header_args['settings'][$setting_name])) {

            return $this->block_header_args['settings'][$setting_name];

        }

        return '';
    }

    protected function get_block_uid() {

        return $this->block_header_args['block_uid'];

    }

    protected function get_block_filter_terms() {

        if (empty($this->block_header_args['block_filter_terms'])) {

            return false;

        }

        return $this->block_header_args['block_filter_terms'];

    }

    function get_block_header() {

        // No header required if there are no filters to be shown and if heading for the block is not specified
        if (!$this->get_setting('filterable') && trim($this->get_setting('heading')) === '')
            return '';

        $header_class = (trim($this->get_setting('heading')) === '') ? ' lvca-no-heading' : '';

        $output = '<div class="lvca-block-header' . $header_class . '">';

        $output .= $this->get_block_title();

        $output .= $this->get_block_taxonomy_filter();

        $output .= '</div>';

        return $output;
    }

    function get_block_title() {

        $output = '';

        if (trim($this->get_setting('heading')) !== '') {

            $output .= '<h3 class="lvca-heading">';

            if (trim($this->get_setting('heading_url')) !== '') {

                $output .= '<a href="' . $this->get_setting('heading_url') . '" title="' . $this->get_setting('heading') . '">';

                $output .= wp_kses_post($this->get_setting('heading'));

                $output .= '</a>';

            }
            else {

                $output .= '<span>';

                $output .= wp_kses_post($this->get_setting('heading'));

                $output .= '</span>';

            }

            $output .= '</h3>';
        }
        else {
            $output .= '<div class="lvca-heading"></div>';
        }

        return $output;

    }

    function get_block_taxonomy_filter() {

        $output = '';

        $block_filter_terms = $this->get_block_filter_terms();

        if (empty($block_filter_terms))
            return '';

        $output .= '<div class="lvca-block-filter">';

        $output .= '<ul class="lvca-block-filter-list">';

        $output .= '<li class="lvca-block-filter-item lvca-active"><a class="lvca-block-filter-link" data-term-id="" data-taxonomy="" href="#">' . esc_html__('All', 'livemesh-vc-addons') . '</a>';

        foreach ($block_filter_terms as $block_filter_term) {

            $output .= '<li class="lvca-block-filter-item"><a class="lvca-block-filter-link" data-term-id="' . $block_filter_term->term_id . '" data-taxonomy="' . $block_filter_term->taxonomy . '" href="#">' . $block_filter_term->name . '</a>';

        }

        $output .= '</ul>';

        $output .= '<div class="lvca-block-filter-dropdown">';

        $output .= '<div class="lvca-block-filter-more"><span>' . __('More', 'livemesh-vc-addons') . '</span><i class="lvca-icon-arrow-right3"></i></div>';

        $output .= '<ul class="lvca-block-filter-dropdown-list">';

        $output .= '</ul>';

        $output .= '</div><!-- .lvca-block-filter-dropdown -->';

        $output .= '</div><!-- .lvca-block-filter -->';

        return $output;

    }

}