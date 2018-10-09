<?php

class LVCA_Module_6 extends LVCA_Module {

    function render() {
        ob_start();
        ?>

        <div class="lvca-module-6 lvca-small-thumb <?php echo $this->get_module_classes(); ?>">

            <?php echo $this->get_thumbnail(); ?>

            <div class="lvca-entry-details">

                <?php echo $this->get_title();?>

                <div class="lvca-module-meta">
                    <?php echo $this->get_author();?>
                    <?php echo $this->get_date();?>
                    <?php echo $this->get_comments();?>
                </div>

                <div class="lvca-excerpt">
                    <?php echo $this->get_excerpt();?>
                </div>

            </div>

        </div>

        <?php return ob_get_clean();
    }
}