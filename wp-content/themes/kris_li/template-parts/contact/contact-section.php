<?php
$contact_label            = get_post_meta(get_the_ID(), '_contact_labels', true) ?: '';
$contact_title            = get_post_meta(get_the_ID(), '_contact_headings', true) ?: '';
$contact_description      = get_post_meta(get_the_ID(), '_contact_paragraphs', true) ?: '';
$contact_image            = get_post_meta(get_the_ID(), '_contact_images', true) ?: '';
$contact_button           = get_post_meta(get_the_ID(), '_contact_buttons', true) ?: '';
$contact_button_link      = get_post_meta(get_the_ID(), '_contact_button_links', true) ?: '';
$contact_image_orintation = get_post_meta(get_the_ID(), '_contact_img_orintation', true) ?: '';
?>


<section id="content-hchi-with-bg-block_203f2731544f712256ea66e09239f0b4"
    class="section section-half-image-half-content hchi-with-bg img-right p-large bg-white">
    <div class="container">
        <div class="row justify-content-between align-items-center layout-<?php echo esc_attr($contact_image_orintation ?? 'top'); ?> ">
            <div class="col-lg-6 section-col-img mb-md-0 order-2 order-lg-2">
                <div class="image-wrapper deep-peach <?php echo esc_attr($image_container_color[$i] ?? 'blue'); ?>">
                    <div class="img-wrap img-cover">
                        <img decoding="async" width="580" height="387"
                            src="<?php echo esc_url($contact_image); ?>"
                            class="attachment-580x561 size-580x561" alt="9c980c24-6cf7-4f5c-9116-b894ef5cf3c9"
                            srcset="<?php echo esc_url($contact_image); ?> 2048w, <?php echo esc_url($contact_image); ?> 300w, <?php echo esc_url($contact_image); ?> 1024w, <?php echo esc_url($contact_image); ?> 768w, <?php echo esc_url($contact_image); ?> 1536w, <?php echo esc_url($contact_image); ?> 1620w"
                            sizes="(max-width: 580px) 100vw, 580px">
                    </div>
                </div>
            </div>
            <div class="col-lg-5 section-col-content  order-1 order-lg-1">
                <div class="section-col-content-wrap feature_content">
                    <p class="section-col-content-wrapsubtitle content_label">
                        <?php echo esc_html($contact_label); ?>
                    <h2 class="section-col-content-wraptitle
                            text-body-text content_heading">
                        <?php echo esc_html($contact_title); ?>
                    </h2>
                    <div class="section-col-content-wrap-desc mg-4 large text-body-text-secondary">
                        <p class="content_description"><?php echo esc_html($contact_description); ?></p>
                    </div>
                    <div class="section-col-content-wrap-btn">
                        <?php if (!empty($contact_button) && !empty($contact_button_link)): ?>
                            <a class="content_btn" href="<?php echo esc_url($contact_button_link); ?>" class="button">
                                <span content_btn--text>
                                    <?php echo esc_html($contact_button); ?>
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


<!-- <article class="feature layout-<?php echo esc_attr($contact_image_orintation ?? 'top'); ?> ">
    <div class="feature_content">
        <?php if (!empty($contact_label)): ?>
            <span class="content_label"><?php echo esc_html($contact_label); ?></span>
        <?php endif; ?>

        <?php if (!empty($contact_title)): ?>
            <h2 class="content_heading"><?php echo esc_html($contact_title); ?></h2>
        <?php endif; ?>

        <?php if (!empty($contact_description)): ?>
            <p class="content_description"><?php echo esc_html($contact_description); ?></p>
        <?php endif; ?>

        <?php if (!empty($contact_button) && !empty($contact_button_link)): ?>
            <a class="contact_btn" href="<?php echo esc_url($contact_button_link); ?>">
                <span contact_btn--text>

                    <?php echo esc_html($contact_button); ?>
                </span>
                <span class="contact_btn--icon"></span>
            </a>
        <?php endif; ?>
    </div>

    <div class="feature_image">
        <div class="feature_image--inlay"></div>
        <div class="image-container">
            <?php if (!empty($contact_image)): ?>
                <img class=" fe" src="<?php echo esc_url($contact_image); ?>" alt="Contact_Image">
            <?php endif; ?>
        </div>
    </div>
    </div>
    <div class="contact_image">
    </div>
</article> -->