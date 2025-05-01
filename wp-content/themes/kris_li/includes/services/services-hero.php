<section class="aboutus-main">
    <?php
    $headings   = get_post_meta(get_the_ID(), '_service_krisli_headings', true) ?: '';
    $paragraphs = get_post_meta(get_the_ID(), '_service_krisli_paragraphs', true) ?: '';
    $images     = get_post_meta(get_the_ID(), '_service_krisli_images', true) ?: '';
    $buttons    = get_post_meta(get_the_ID(), '_service_krisli_buttons', true) ?: '';
    $button_links = get_post_meta(get_the_ID(), '_service_krisli_button_links', true) ?: '';
    $button_about    = get_post_meta(get_the_ID(), '_service_krisli_button_about', true) ?: '';
    $button_link_about = get_post_meta(get_the_ID(), '_service_krisli_button_link_about', true) ?: '';
    $image_orintation = get_post_meta(get_the_ID(), '_service_krisli_img_orintation', true) ?: '';
    ?>
    <article class="feature layout-<?php echo esc_attr($image_orintation ?? 'top'); ?> ">
        <div class="feature_content">
            <?php if (!empty($headings)): ?>
                <h2 class="content_heading"><?php echo esc_html($headings); ?></h2>
            <?php endif; ?>

            <?php if (!empty($paragraphs)): ?>
                <p class="content_description"><?php echo esc_html($paragraphs); ?></p>
            <?php endif; ?>

            <?php if (!empty($buttons) && !empty($button_links) || (!empty($button_about) && !empty($button_link_about))): ?>
                <div class="service_btn_container">

                    <a class="contact_btn" href="<?php echo esc_url($button_links); ?>">
                        <span contact_btn--text>

                            <?php echo esc_html($buttons); ?>
                        </span> </a>
                    <a class="content_btn" href="<?php echo esc_url($button_link_about); ?>">
                        <span contact_btn--text>

                            <?php echo esc_html($button_about); ?>
                        </span> </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="image-container service-image">
            <?php if (!empty($images)): ?>
                <img class=" fe" src="<?php echo esc_url($images); ?>" alt="Contact_Image">
            <?php endif; ?>
        </div>
    </article>
</section>