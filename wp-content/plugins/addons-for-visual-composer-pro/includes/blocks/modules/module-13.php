<?php

class LVCA_Module_13 extends LVCA_Module {

    function render() {
        ob_start();
        ?>

        <article
                class="lvca-module-13 <?php echo $this->get_module_classes(); ?> <?php echo join(' ', get_post_class('', $this->post_ID)); ?>">

            <?php if ($thumbnail_exists = has_post_thumbnail($this->post_ID)): ?>

                <div class="lvca-module-image">

                    <div class="lvca-module-thumb">

                        <?php echo $this->get_media(); ?>

                        <?php echo $this->get_lightbox(); ?>

                    </div>

                    <div class="lvca-module-image-info">

                        <div class="lvca-module-entry-info">

                            <?php echo $this->get_media_title(); ?>

                            <?php echo $this->get_media_taxonomy(); ?>

                        </div>

                    </div>

                </div>

            <?php endif; ?>

            <div class="lvca-module-entry-text">

                <?php echo $this->get_title(); ?>

                <div class="lvca-module-meta">
                    <?php echo $this->get_author(); ?>
                    <?php echo $this->get_date(); ?>
                    <?php echo $this->get_comments(); ?>
                    <?php echo $this->get_taxonomies_info(); ?>
                </div>

                <div class="lvca-excerpt">
                    <?php echo $this->get_excerpt(); ?>
                </div>

                <?php echo $this->get_read_more_link(); ?>

            </div>

        </article><!-- .hentry -->

        <?php return ob_get_clean();
    }
}