<section class="aboutus-need">
    <?php
    $headings        = get_post_meta(get_the_ID(), '_service_pricing_headings', true) ?: [];
    $paragraphs      = get_post_meta(get_the_ID(), '_service_pricing_paragraphs', true) ?: [];
    $images          = get_post_meta(get_the_ID(), '_service_pricing_images', true) ?: [];
    $buttons         = get_post_meta(get_the_ID(), '_service_pricing_buttons', true) ?: [];
    $button_links    = get_post_meta(get_the_ID(), '_service_pricing_button_links', true) ?: [];
    $image_orintation = get_post_meta(get_the_ID(), '_service_pricing_img_orintation', true) ?: [];
    $lists           = get_post_meta(get_the_ID(), '_service_pricing_lists', true) ?: [];


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
                <?php if (!empty($buttons[$i]) && !empty($button_links[$i])): ?>
                    <a class="content_btn" href="<?php echo esc_url($button_links[$i]); ?>" class="button">
                        <span content_btn--text>

                            <?php echo esc_html($buttons[$i]); ?>
                        </span>
                        <span class="content_btn--icon"></span>
                    </a>
                <?php endif; ?>
            </div>
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