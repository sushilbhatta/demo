<section class="aboutus-need">

    <?php

    // Get Meta Data from Database


    $headings        = get_post_meta(get_the_ID(), '_aboutus_mission_headings', true) ?: [];
    $paragraphs      = get_post_meta(get_the_ID(), '_aboutus_mission_paragraphs', true) ?: [];
    $images          = get_post_meta(get_the_ID(), '_aboutus_mission_images', true) ?: [];
    $buttons         = get_post_meta(get_the_ID(), '_aboutus_mission_buttons', true) ?: [];
    $button_links    = get_post_meta(get_the_ID(), '_aboutus_mission_button_links', true) ?: [];
    $image_orintation = get_post_meta(get_the_ID(), '_aboutus_mission_img_orintation', true) ?: [];
    $lists           = get_post_meta(get_the_ID(), '_aboutus_mission_lists', true) ?: [];
    // var_dump($lists);
    // die();

    for ($i = 0; $i < 1; $i++) {
    ?>
        <article class="feature layout-<?php echo esc_attr($image_orintation[$i] ?? 'top'); ?> ">
            <div class="feature_content">

                <?php if (!empty($headings[$i])): ?>
                    <h2 class="content_heading"><?php echo esc_html($headings[$i]); ?></h2>
                <?php endif; ?>

                <?php if (!empty($paragraphs[$i])): ?>
                    <p class="content_description"><?php echo esc_html($paragraphs[$i]); ?></p>
                <?php endif; ?>

                <?php if (!empty($lists)): ?>
                    <ul class="need_list">
                        <?php foreach ($lists as $list): ?>

                            <li>
                                <?php if (!empty($list['title'])): ?>
                                    <strong><?php echo esc_html($list['title']); ?> : </strong>
                                <?php endif; ?>
                                <?php if (!empty($list['description'])): ?>
                                    <span> <?php echo esc_html($list['description']); ?></span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

            </div>
            <!-- <?php echo esc_attr($image_container_color[$i] ?? 'blue'); ?> -->
            <div class="aboutus_service_image">
                <?php if (!empty($images[$i])): ?>
                    <img src="<?php echo esc_url($images[$i]); ?>" alt="Service Image">
                <?php endif; ?>
            </div>
        </article>
    <?php
    }
    ?>
</section>