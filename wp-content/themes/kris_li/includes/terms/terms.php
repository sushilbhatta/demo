<?php
$hero_blocks = get_blocks_by_name_with_class('core/group', 'terms-conditions');

if (!empty($hero_blocks)) {
    foreach ($hero_blocks as $block) {
        echo apply_filters('the_content', render_block($block));
    }
}
