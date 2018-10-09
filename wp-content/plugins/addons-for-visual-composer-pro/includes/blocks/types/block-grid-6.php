<?php

class LVCA_Block_Grid_6 extends LVCA_Block_Grid {

    function inner($posts, $settings) {

        $output = '';

        $post_count = 1;

        $num_of_columns = $settings['per_line'];

        $block_layout = new LVCA_Block_Layout();

        $column_class = $this->get_column_class(intval($num_of_columns));

        if (!empty($posts)) {

            foreach ($posts as $post) {

                if ($num_of_columns == 1) {

                    $output .= $block_layout->open_column($column_class);

                    $module2 = new LVCA_Module_13($post, $settings);

                    $output .= $module2->render();

                    $post_count++;

                }
                else {

                    $output .= $block_layout->open_column($column_class);

                    $module2 = new LVCA_Module_13($post, $settings);

                    $output .= $module2->render();

                    $output .= $block_layout->close_column($column_class);

                    $post_count++;
                }

            };

            $output .= $block_layout->close_all_tags();

        };

        return $output;

    }

    function get_block_class() {

        return 'lvca-block-grid lvca-block-grid-6 lvca-gapless-grid';

    }
}