<?php

class LVCA_Module_5 extends LVCA_Module {

    function render() {
        ob_start();
        ?>

        <article
                class="lvca-module-5 <?php echo $this->get_module_classes(); ?> <?php echo join(' ', get_post_class('', $this->post_ID)); ?>">

            <div class="lvca-entry-details">

                <?php echo $this->get_title(); ?>

                <div class="lvca-module-meta">
                    <?php echo $this->get_author(); ?>
                    <?php echo $this->get_date(); ?>
                    <?php echo $this->get_comments(); ?>
                </div>

                <div class="lvca-excerpt">
                    <?php echo $this->get_excerpt(); ?>
                </div>

            </div>

        </article>

        <?php return ob_get_clean();
    }
}