<?php

class LVCA_Module_10 extends LVCA_Module {

    function render() {
        ob_start();
        ?>

        <div class="lvca-module-10 lvca-small-thumb <?php echo $this->get_module_classes(); ?>">

            <div class="lvca-entry-details">

                <?php echo $this->get_taxonomies_info(); ?>

                <?php echo $this->get_title(); ?>

                <div class="lvca-module-meta">
                    <?php echo $this->get_author(); ?>
                    <?php echo $this->get_date(); ?>
                    <?php echo $this->get_comments(); ?>
                    <?php echo $this->get_taxonomies_info(); ?>
                </div>

            </div>

            <?php echo $this->get_thumbnail(); ?>

            <div class="lvca-excerpt">
                <?php echo $this->get_excerpt(); ?>
            </div>

            <div class="lvca-read-more">
                <a href="<?php the_permalink($this->post_ID); ?>"><?php echo esc_html__('Read more', 'livemesh-vc-addons'); ?></a>
            </div>

        </div>

        <?php return ob_get_clean();
    }
}