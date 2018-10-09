<?php

class LVCA_Module_1 extends LVCA_Module {

    function render() {
        ob_start();
        ?>

        <article
                class="lvca-module-1 <?php echo $this->get_module_classes(); ?> <?php echo join(' ', get_post_class('', $this->post_ID)); ?>">

            <div class="lvca-module-image">
                <?php echo $this->get_thumbnail();?>
                <?php echo $this->get_taxonomies_info(); ?>
            </div>

            <?php echo $this->get_title();?>

            <div class="lvca-module-meta">
                <?php echo $this->get_author();?>
                <?php echo $this->get_date();?>
                <?php echo $this->get_comments();?>
            </div>

            <div class="lvca-excerpt">
                <?php echo $this->get_excerpt();?>
            </div>

        </article>

        <?php return ob_get_clean();
    }
}