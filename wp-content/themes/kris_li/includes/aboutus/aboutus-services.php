<section class="aboutus-services">

    <?php

    // Get Meta Data from Database

    $headings   = get_post_meta($post->ID, '_aboutus_service_headings', true) ?: [];
    $paragraphs = get_post_meta($post->ID, '_aboutus_service_paragraphs', true) ?: [];
    $images     = get_post_meta($post->ID, '_aboutus_service_images', true) ?: [];
    $buttons    = get_post_meta($post->ID, '_aboutus_service_buttons', true) ?: [];
    $button_links = get_post_meta($post->ID, '_aboutus_service_button_links', true) ?: [];
    $image_orintation = get_post_meta($post->ID, '_aboutus_service_img_orintation', true) ?: [];



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

                <?php if (!empty($buttons[$i]) && !empty($button_links[$i])): ?>
                    <a class="contact_btn" href="<?php echo esc_url($button_links[$i]); ?>" class="button">
                        <span content_btn--text>
                            <?php echo esc_html($buttons[$i]); ?>
                        </span>
                    </a>
                <?php endif; ?>
            </div>

            <div class="aboutus_service_image">
                <?php if (!empty($images[0])): ?>
                    <img src="<?php echo esc_url($images[0]); ?>" alt="Service Image">
                <?php endif; ?>
            </div>
        </article>
    <?php
    }
    ?>
</section>