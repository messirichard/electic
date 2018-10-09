<?php

class LVCA_Block_13 extends LVCA_Block {

    function inner($posts, $settings) {

        $output = '';

        $post_count = 1;

        $num_of_columns = 1;

        $block_layout = new LVCA_Block_Layout();

        $column_class = $this->get_column_class(intval($num_of_columns));

        if (!empty($posts)) {

            foreach ($posts as $post) {

                    $output .= $block_layout->open_column($column_class);

                    $module6 = new LVCA_Module_10($post, $settings);

                    $output .= $module6->render();

                    $post_count++;

            };

            $output .= $block_layout->close_all_tags();

        };

        return $output;

    }

    function get_block_class() {

        return 'lvca-block-posts lvca-block-13';

    }

    function get_grid_classes($settings) {

        $grid_classes = ' lvca-grid-desktop-1 lvca-grid-tablet-1 lvca-grid-mobile-1';

        return $grid_classes;

    }
}