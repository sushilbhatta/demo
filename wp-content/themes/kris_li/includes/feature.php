<!-- <section id="features"> -->

<?php
$feature_blocks = get_blocks_by_name_with_class('core/group', 'features');
if (!empty($feature_blocks)) {

    foreach ($feature_blocks as $block) {
        echo apply_filters('the_content', render_block($block));
    }

    get_template_part('template-parts/features/row', 'two');
}
?>


<!-- </section> -->