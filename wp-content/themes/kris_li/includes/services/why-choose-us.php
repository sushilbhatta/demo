<section id="why-us">
    <h4> Why choose us</h4>
    <ul class="offer-items why-choose-items">
        <?php
        $why_choose_us_number     = get_post_meta(get_the_ID(), '_why_choose_us_number', true) ?: [];
        $why_choose_us_title   = get_post_meta(get_the_ID(), '_why_choose_us_title', true) ?: [];
        $why_choose_us_description = get_post_meta(get_the_ID(), '_why_choose_us_description', true) ?: [];

        for ($i = 0; $i < 3; $i++) {
        ?>
            <li class="offer-item why-choose-item">
                <?php if (!empty($why_choose_us_number[$i])) : ?>
                    <div class="offer-item_icon  why-us-icon">
                        <span><?php echo esc_html($why_choose_us_number[$i]); ?> </span>
                    </div>
                <?php endif; ?>
                <div class="offer-item__content why-choose-content">
                    <?php if (!empty($why_choose_us_title[$i])) : ?>
                        <h4 class="offer-item_title">
                            <?php echo esc_html($why_choose_us_title[$i]); ?>
                        </h4>
                    <?php endif; ?>

                    <?php if (!empty($why_choose_us_description[$i])) : ?>
                        <p class="offer-item_description">
                            <?php echo esc_html($why_choose_us_description[$i]); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </li>
        <?php
        }
        ?>
    </ul>
</section>