<?php

class LVCA_Module_7 extends LVCA_Module {

    function render() {
        ob_start();
        ?>

        <div class="lvca-module-7 lvca-small-thumb <?php echo $this->get_module_classes(); ?>">

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

                <div class="lvca-read-more">
                    <a class="lvca-button" href="<?php the_permalink($this->post_ID);?>"><?php echo esc_html__('Read more', 'livemesh-vc-addons');?></a>
                </div>

            </div>

        </div>

        <?php return ob_get_clean();
    }
}