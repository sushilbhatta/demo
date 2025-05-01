<?php
$heading = get_post_meta(get_the_ID(), '_brand_carousel_main_heading', true) ?: "What we offer";
$paragraph = get_post_meta(get_the_ID(), '_brand_carousel_main_paragraph', true) ?: "We are an Australian web design agency that offers full-service solutions for clients worldwide.";

?>
<section id="carousel">
    <header class="carousel_heading">
        <h3><?php echo $heading ?></h3>
        <h3 class="heading--special"><?php echo $paragraph ?></h3>
    </header>

    <ul class="scroller" data-direction="right" data-speed="fast">

        <div class="scroller__inner">
            <?php
            $offer_icon_url = get_post_meta(get_the_ID(), '_carousel_icon', true) ?: [];
            for ($i = 0; $i < 6; $i++) {
            ?>
                <?php if (!empty($offer_icon_url[$i])) : ?>
                    <img src=" <?php echo esc_url($offer_icon_url[$i]); ?> " alt="title" />
                <?php endif; ?>
            <?php
            }
            ?>
        </div>

    </ul>

</section>