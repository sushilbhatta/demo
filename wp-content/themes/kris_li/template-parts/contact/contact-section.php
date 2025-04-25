<?php
$contact_label            = get_post_meta(get_the_ID(), '_contact_labels', true) ?: '';
$contact_title            = get_post_meta(get_the_ID(), '_contact_headings', true) ?: '';
$contact_description      = get_post_meta(get_the_ID(), '_contact_paragraphs', true) ?: '';
$contact_image            = get_post_meta(get_the_ID(), '_contact_images', true) ?: '';
$contact_button           = get_post_meta(get_the_ID(), '_contact_buttons', true) ?: '';
$contact_button_link      = get_post_meta(get_the_ID(), '_contact_button_links', true) ?: '';
$contact_image_orintation = get_post_meta(get_the_ID(), '_contact_img_orintation', true) ?: '';

// var_dump($contact_button);
// die()

?>
<article class="feature layout-<?php echo esc_attr($contact_image_orintation ?? 'top'); ?> ">
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
</article>