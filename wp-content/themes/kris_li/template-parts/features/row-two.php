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
        <section id="content-hchi-with-bg-block_203f2731544f712256ea66e09239f0b4"
            class="section section-half-image-half-content hchi-with-bg img-right p-large bg-white">
            <div class="container">
                <div class="row justify-content-between align-items-center layout-<?php echo esc_attr($image_orintation[$i] ?? 'top'); ?> ">
                    <div class="col-lg-6 section-col-img mb-md-0 order-2 order-lg-2">
                        <div class="image-wrapper deep-peach <?php echo esc_attr($image_container_color[$i] ?? 'blue'); ?>">
                            <div class="img-wrap img-cover">
                                <img decoding="async" width="580" height="387"
                                    src="<?php echo esc_url($images[$i]); ?>"
                                    class="attachment-580x561 size-580x561" alt="9c980c24-6cf7-4f5c-9116-b894ef5cf3c9"
                                    srcset="<?php echo esc_url($images[$i]); ?> 2048w, <?php echo esc_url($images[$i]); ?> 300w, <?php echo esc_url($images[$i]); ?> 1024w, <?php echo esc_url($images[$i]); ?> 768w, <?php echo esc_url($images[$i]); ?> 1536w, <?php echo esc_url($images[$i]); ?> 1620w"
                                    sizes="(max-width: 580px) 100vw, 580px">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 section-col-content  order-1 order-lg-1">
                        <div class="section-col-content-wrap feature_content">
                            <p class="section-col-content-wrapsubtitle content_label">
                                <?php echo esc_html($labels[$i]); ?> </p>
                            <h2 class="section-col-content-wraptitle
                            text-body-text content_heading">
                                <?php echo esc_html($headings[$i]); ?></h2>
                            <div class="section-col-content-wrap-desc mg-4 large text-body-text-secondary">
                                <p class="content_description"><?php echo esc_html($paragraphs[$i]); ?></p>
                            </div>
                            <div class="section-col-content-wrap-btn">
                                <?php if (!empty($buttons[$i]) && !empty($button_links[$i])): ?>
                                    <a class="content_btn" href="<?php echo esc_url($button_links[$i]); ?>" class="button">
                                        <span content_btn--text>
                                            <?php echo esc_html($buttons[$i]); ?>
                                        </span>
                                        <span class="content_btn--icon"></span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php
    }
    ?>