<?php

class LVCA_Module_9 extends LVCA_Module {

    function render() {
        ob_start();
        ?>

        <article
                class="lvca-module-9 lvca-module-trans1 <?php echo $this->get_module_classes(); ?> <?php echo join(' ', get_post_class('', $this->post_ID)); ?>">

            <?php echo $this->get_thumbnail(); ?>

            <div class="lvca-entry-details">

                <?php echo $this->get_title();?>

                <div class="lvca-module-meta">
                    <?php echo $this->get_author();?>
                    <?php echo $this->get_date();?>
                    <?php echo $this->get_comments();?>
                    <?php echo $this->get_taxonomies_info(); ?>
                </div>

            </div>

        </article>

        <?php return ob_get_clean();
    }
}