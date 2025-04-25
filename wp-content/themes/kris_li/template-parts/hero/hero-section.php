<section id="hero">
    <?php
    $hero_label     = get_post_meta(get_the_ID(), '_hero_labels', true) ?: [];
    $hero_title   = get_post_meta(get_the_ID(), '_hero_title', true) ?: [];
    $hero_description = get_post_meta(get_the_ID(), '_hero_description', true) ?: [];
    $hero_bg_img_url     = get_post_meta(get_the_ID(), '_hero_bg_images', true) ?: [];
    $hero_btn    = get_post_meta(get_the_ID(), '_hero_buttons', true) ?: [];
    $hero_btn_link = get_post_meta(get_the_ID(), '_hero_button_links', true) ?: [];

    for ($i = 0; $i < 4; $i++) {
    ?>
        <article class="hero_item slide-<?php echo $i; ?>">
            <?php if (!empty($hero_bg_img_url[$i])): ?>
                <div class="hero-background slide slide-<?php echo $i; ?>"
                    style="background:linear-gradient(to left,hsla(221, 42%, 15%, 0.5),hsla(221, 42%, 15%, 0.8),hsla(221, 42%, 15%, 1)),
                     url('<?php echo esc_url_raw($hero_bg_img_url[$i]); ?>')  top 30% right 0%/cover no-repeat border-box;,#16203680""></div>
            <?php endif; ?>

            <div class=" hero_content">
                    <div class="hero_content__text">

                        <?php if (!empty($hero_label[$i])): ?>
                            <span class="hero_label"><?php echo esc_html($hero_label[$i]); ?></span>
                        <?php endif; ?>

                        <?php if (!empty($hero_title[$i])): ?>

                            <h1 class="hero_heading"><?php echo esc_html($hero_title[$i]); ?></h1>

                        <?php endif; ?>

                        <?php if (!empty($hero_description[$i])): ?>

                            <p class="hero_description"><?php echo esc_html($hero_description[$i]); ?></p>

                        <?php endif; ?>

                    </div>

                    <?php if (!empty($hero_btn[$i]) && !empty($hero_btn_link[$i])): ?>
                        <a class="contact_btn" href="<?php echo esc_url($hero_btn_link[$i]); ?> ">

                            <?php echo esc_html($hero_btn[$i]); ?>

                        </a>
                    <?php endif; ?>
                </div>
        </article>


    <?php
    }
    ?>
    <div class="slider">
        <span class="slider-item item-1" data-slide="0"></span>
        <span class="slider-item item-2" data-slide="1"></span>
        <span class="slider-item item-3" data-slide="2"></span>
        <span class="slider-item item-4" data-slide="3"></span>
    </div>

</section>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const slides = document.querySelectorAll(".hero_item");
        const dots = document.querySelectorAll(".slider-item");
        let currentSlide = 0;
        let slideInterval;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.style.display = i === index ? "block" : "none";
            });

            dots.forEach((dot, i) => {
                dot.classList.toggle("active", i === index);
            });

            currentSlide = index;
        }

        function nextSlide() {
            let nextIndex = (currentSlide + 1) % slides.length; //find the index of next slide.
            showSlide(nextIndex);
        }

        function startAutoplay() {
            slideInterval = setInterval(nextSlide, 5000); // 5 seconds
        }

        function resetAutoplay() {
            clearInterval(slideInterval);
            startAutoplay();
        }
        showSlide(currentSlide);
        startAutoplay();

        // Dot click handling
        dots.forEach(dot => {
            dot.addEventListener("click", () => {
                const index = parseInt(dot.getAttribute("data-slide"));
                showSlide(index);
                resetAutoplay();
            });
        });
    });
</script>