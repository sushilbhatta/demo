<ul class="offer-items">
    <?php
    $offer_icon_url = get_post_meta(get_the_ID(), '_offer_icon', true) ?: [];
    $offer_title = get_post_meta(get_the_ID(), '_offer_title', true) ?: [];
    $offer_description = get_post_meta(get_the_ID(), '_offer_description', true) ?: [];
    $offer_btn = get_post_meta(get_the_ID(), '_offer_button', true) ?: [];
    $offer_btn_link = get_post_meta(get_the_ID(), '_offer_button_links', true) ?: [];

    for ($i = 0; $i < 6; $i++) {
    ?>
        <li class="offer-item">

            <?php if (!empty($offer_icon_url[$i])) : ?>
                <div class="offer-item_icon">
                    <?php
                    $svg_content = file_get_contents($offer_icon_url[$i]);
                    echo $svg_content;
                    ?> </div>
            <?php endif; ?>
            <div class="offer-item__content">
                <?php if (!empty($offer_title[$i])) : ?>
                    <h4 class="offer-item_title">
                        <?php echo esc_html($offer_title[$i]); ?>
                    </h4>
                <?php endif; ?>

                <?php if (!empty($offer_description[$i])) : ?>
                    <p class="offer-item_description">
                        <?php echo esc_html($offer_description[$i]); ?>
                    </p>
                <?php endif; ?>

            </div>

            <!-- btn -->
            <?php if (!empty($offer_btn[$i]) && !empty($offer_btn_link[$i])): ?>
                <a class="content_btn" href="<?php echo esc_url($offer_btn_link[$i]); ?>" class="button">
                    <span content_btn--text>
                        <?php echo esc_html($offer_btn[$i]); ?>
                    </span>
                    <span class="content_btn--icon"></span>
                </a>
            <?php endif; ?>

        </li>
    <?php
    }
    ?>
</ul>