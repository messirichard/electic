<?php

class LVCA_Module_3 extends LVCA_Module {

    function render() {
        ob_start();
        ?>

        <div class="lvca-module-3 lvca-small-thumb <?php echo $this->get_module_classes(); ?>">

            <?php echo $this->get_thumbnail('medium'); ?>

            <div class="lvca-entry-details">

                <?php echo $this->get_title(); ?>

                <div class="lvca-module-meta">
                    <?php echo $this->get_date(); ?>
                </div>

            </div>

        </div>

        <?php return ob_get_clean();
    }
}