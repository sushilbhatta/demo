    <?php

    // Get Meta Data from Database
    $labels = get_post_meta(get_the_ID(), '_feature_labels', true) ?: [];
    $headings = get_post_meta(get_the_ID(), '_feature_headings', true) ?: [];
    $paragraphs = get_post_meta(get_the_ID(), '_feature_paragraphs', true) ?: [];
    $images = get_post_meta(get_the_ID(), '_feature_images', true) ?: [];
    $buttons = get_post_meta(get_the_ID(), '_feature_buttons', true) ?: [];
    $button_links = get_post_meta(get_the_ID(), '_feature_button_links', true) ?: [];
    $image_orintation = get_post_meta(get_the_ID(), '_feature_img_orintation', true) ?: [];
    $image_container_color = get_post_meta(get_the_ID(), '_feature_container_color', true) ?: [];

    for ($i = 0; $i < 3; $i++) {
    ?>
        <article class="feature layout-<?php echo esc_attr($image_orintation[$i] ?? 'top'); ?> ">
            <div class="feature_content">
                <?php if (!empty($labels[$i])): ?>
                    <span class="content_label"><?php echo esc_html($labels[$i]); ?></span>
                <?php endif; ?>

                <?php if (!empty($headings[$i])): ?>
                    <h2 class="content_heading"><?php echo esc_html($headings[$i]); ?></h2>
                <?php endif; ?>

                <?php if (!empty($paragraphs[$i])): ?>
                    <p class="content_description"><?php echo esc_html($paragraphs[$i]); ?></p>
                <?php endif; ?>

                <?php if (!empty($buttons[$i]) && !empty($button_links[$i])): ?>
                    <a class="content_btn" href="<?php echo esc_url($button_links[$i]); ?>" class="button">
                        <span content_btn--text>
                            <?php echo esc_html($buttons[$i]); ?>
                        </span>
                        <span class="content_btn--icon"></span>
                    </a>
                <?php endif; ?>
            </div>
            <!-- <?php echo esc_attr($image_container_color[$i] ?? 'blue'); ?> -->
            <div class="feature_image">
                <?php if (!empty($images[$i])): ?>
                    <img class=" fe" src="<?php echo esc_url($images[$i]); ?>" alt="Feature_images">
                <?php endif; ?>
            </div>
        </article>
    <?php
    }
    ?>