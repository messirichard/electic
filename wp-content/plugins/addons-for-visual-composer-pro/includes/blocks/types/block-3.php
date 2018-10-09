<?php

class LVCA_Block_3 extends LVCA_Block {

    function inner($posts, $settings) {

        $output = '';

        $post_count = 1;

        $num_of_columns = $settings['per_line1'];

        $block_layout = new LVCA_Block_Layout();

        $column_class = $this->get_column_class(intval($num_of_columns));

        if (!empty($posts)) {

            foreach ($posts as $post) {

                switch ($num_of_columns) {
                    case 1:
                        $output .= $block_layout->open_column($column_class);

                        // big posts for posts
                        if ($post_count <= 1) {

                            $module2 = new LVCA_Module_1($post, $settings);

                            $output .= $module2->render();

                        }
                        else {

                            $module6 = new LVCA_Module_3($post, $settings);

                            $output .= $module6->render();
                        }

                        $post_count++;

                        break;

                    case 2:
                    case 3:

                        // big posts for posts
                        if ($post_count <= 1) {

                            $output .= $block_layout->open_row();

                            $output .= $block_layout->open_column($column_class);

                            $module2 = new LVCA_Module_1($post, $settings);

                            $output .= $module2->render();

                            $output .= $block_layout->close_column($column_class);

                        }
                        else {

                            $output .= $block_layout->open_column($column_class);

                            $module6 = new LVCA_Module_3($post, $settings);

                            $output .= $module6->render();
                        }

                        // Help start a 3rd column for 5th post onwards when in 3 column mode
                        if ($num_of_columns == 3 && $post_count == 5)
                            $output .= $block_layout->close_column($column_class);

                        $post_count++;

                        break;
                }


            };

            $output .= $block_layout->close_all_tags();

        };

        return $output;

    }

    function get_block_class() {

        return 'lvca-block-posts lvca-block-3';

    }

    function get_grid_classes($settings) {

        $grid_classes = ' lvca-grid-desktop-' . $settings['per_line1'];

        $grid_classes .= ' lvca-grid-tablet-2';

        $grid_classes .= ' lvca-grid-mobile-1';

        return $grid_classes;

    }
}