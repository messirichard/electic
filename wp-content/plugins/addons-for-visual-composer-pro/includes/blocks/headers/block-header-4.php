<?php

class LVCA_Block_Header_4 extends LVCA_Block_Header {

    function get_block_taxonomy_filter() {

        $output = '';

        $block_filter_terms = $this->get_block_filter_terms();

        if (empty($block_filter_terms))
            return '';

        $output .= '<div class="lvca-block-filter">';

        $output .= '<div class="lvca-block-filter-dropdown">';

        $output .= '<div class="lvca-block-filter-more"><span>' . __('All' , 'livemesh-vc-addons') . '</span><i class="lvca-icon-arrow-right3"></i></div>';

        $output .= '<ul class="lvca-block-filter-dropdown-list">';

        $output .= '<li class="lvca-block-filter-item lvca-active"><a class="lvca-block-filter-link" data-term-id="" data-taxonomy="" href="#">' . esc_html__('All', 'livemesh-vc-addons') . '</a>';

        foreach ($block_filter_terms as $block_filter_term) {

            $output .= '<li class="lvca-block-filter-item"><a class="lvca-block-filter-link" data-term-id="' . $block_filter_term->term_id . '" data-taxonomy="' . $block_filter_term->taxonomy . '" href="#">' . $block_filter_term->name . '</a>';

        }

        $output .= '</ul>';

        $output .= '</div><!-- .lvca-block-filter-dropdown -->';

        $output .= '</div><!-- .lvca-block-filter -->';

        return $output;

    }

    function get_block_header_class() {

        return 'lvca-block-header-4';

    }
}