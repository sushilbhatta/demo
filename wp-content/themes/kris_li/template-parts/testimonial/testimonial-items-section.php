<ul class="testimonial-items">

    <?php
    $testimonial_card_title   = get_post_meta(get_the_ID(), '_testimonial_card_title', true) ?: [];
    $testimonial_card_description = get_post_meta(get_the_ID(), '_testimonial_card_description', true) ?: [];
    $testimonial_author_name = get_post_meta(get_the_ID(), '_testimonial_card_author_name', true) ?: [];
    $testimonial_author_position = get_post_meta(get_the_ID(), '_testimonial_card_author_position', true) ?: [];


    for ($i = 0; $i < 4; $i++) {
    ?>
        <article class="testimonial-item">

            <?php if (!empty($testimonial_card_title[$i])): ?>
                <h4 class="testimonial-item_title"> <?php echo $testimonial_card_title[$i]; ?> </h4>
            <?php endif; ?>

            <?php if (!empty($testimonial_card_description[$i])): ?>
                <p class="testimonial-item_description"> <?php echo $testimonial_card_description[$i]; ?></p>
            <?php endif; ?>

            <div class="author_info">

                <?php if (!empty($testimonial_author_name[$i])): ?>
                    <p class="author_info--name"><?php echo $testimonial_author_name[$i]; ?></p>
                <?php endif; ?>

                <?php if (!empty($testimonial_author_position[$i])): ?>
                    <p class="author_info--position"> <?php echo $testimonial_author_position[$i]; ?></p>
                <?php endif; ?>
            </div>
        </article>
    <?php
    }
    ?>
</ul>
<div class="slider-indicator">
    <span class="indicator-circle selected" data-slide="0"></span>
    <span class="indicator-circle" data-slide="1"></span>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const testimonials = document.querySelectorAll(".testimonial-item");
        const indicators = document.querySelectorAll(".indicator-circle");
        let totalSlides = Math.ceil(testimonials.length / 2);
        const viewportWidth = window.innerWidth;
        if (viewportWidth < 1024) {
            totalSlides = Math.ceil(testimonials.length / 4);
        }

        let currentSlide = 0;
        let slideInterval;

        function showSlide(slideIndex) {
            // Hide all testimonials
            testimonials.forEach(item => {
                item.classList.remove("active");
            });

            // Calculate which testimonials to show (2 per slide)
            let startIndex = slideIndex * 2;
            if (viewportWidth < 1024) {
                startIndex = slideIndex * 1;
            } else {
                startIndex = slideIndex * 2;
            }
            let endIndex = Math.min(startIndex + 2, testimonials.length);
            if (viewportWidth < 1024) {
                endIndex = Math.min(startIndex + 1, testimonials.length);

            } else {
                endIndex = Math.min(startIndex + 2, testimonials.length);
            }

            // Show the current pair
            for (let i = startIndex; i < endIndex; i++) {
                testimonials[i].classList.add("active");
            }

            // Update indicators
            indicators.forEach((indicator, index) => {
                indicator.classList.toggle("selected", index === slideIndex);
            });

            currentSlide = slideIndex;
        }

        // Auto slide every 5 seconds
        function startAutoSlide() {
            slideInterval = setInterval(() => {
                const nextSlide = (currentSlide + 1) % totalSlides;
                showSlide(nextSlide);
            }, 5000);
        }

        // Reset timer when manually changing slides
        function resetAutoSlide() {
            clearInterval(slideInterval);
            startAutoSlide();
        }

        // Initialize with first pair showing
        showSlide(0);
        startAutoSlide();

        // Handle indicator clicks
        indicators.forEach((dot, index) => {
            dot.addEventListener("click", () => {
                showSlide(index);
                resetAutoSlide();
            });
        });
    });
</script>