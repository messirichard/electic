<?php

class LVCA_Block_6 extends LVCA_Block {

    function inner($posts, $settings) {

        $output = '';

        $post_count = 1;

        $num_of_columns = $settings['per_line2'];

        $block_layout = new LVCA_Block_Layout();

        $column_class = $this->get_column_class(intval($num_of_columns));

        if (!empty($posts)) {

            foreach ($posts as $post) {

                if ($num_of_columns == 1) {

                    $output .= $block_layout->open_column($column_class);

                    $module6 = new LVCA_Module_5($post, $settings);

                    $output .= $module6->render();

                    $post_count++;

                }
                else {

                    $output .= $block_layout->open_column($column_class);

                    $module6 = new LVCA_Module_5($post, $settings);

                    $output .= $module6->render();

                    $output .= $block_layout->close_column($column_class);

                    $post_count++;
                }


            };

            $output .= $block_layout->close_all_tags();

        };

        return $output;

    }

    function get_block_class() {

        return 'lvca-block-posts lvca-block-6';

    }

    function get_grid_classes($settings) {

        return $this->get_grid_classes_from_field($settings, 'per_line2');

    }
}