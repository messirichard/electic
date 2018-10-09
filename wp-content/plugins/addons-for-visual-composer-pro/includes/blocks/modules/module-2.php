<?php

class LVCA_Module_2 extends LVCA_Module {

    function render() {
        ob_start();
        ?>

        <div class="lvca-module-2 lvca-small-thumb <?php echo $this->get_module_classes(); ?>">

            <div class="lvca-entry-details">

                <?php echo $this->get_title(); ?>

                <div class="lvca-module-meta">
                    <?php echo $this->get_author();?>
                    <?php echo $this->get_date();?>
                    <?php echo $this->get_comments();?>
                </div>

            </div>

        </div>

        <?php return ob_get_clean();
    }
}