<?php

abstract class LVCA_Block_Grid extends LVCA_Block {

    protected function get_prefs_data_atts() {

        $data_atts = array();

        /* Block Preferences */

        $data_atts['gutter'] = $this->settings['gutter'];

        $data_atts['tablet_gutter'] = $this->settings['tablet_gutter'];

        $data_atts['tablet_width'] = $this->settings['tablet_width'];

        $data_atts['mobile_gutter'] = $this->settings['mobile_gutter'];

        $data_atts['mobile_width'] = $this->settings['mobile_width'];

        return $data_atts;

    }

}