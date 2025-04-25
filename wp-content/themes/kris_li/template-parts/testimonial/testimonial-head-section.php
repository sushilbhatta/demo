<?php
$testimonial_heading = get_post_meta(get_the_ID(), '_testimonial_main_heading', true) ?: "What we offer";
$testimonial_paragraph = get_post_meta(get_the_ID(), '_testimonial_main_paragraph', true) ?: "We are an Australian web design agency that offers full-service solutions for clients worldwide.";

?>
<header class="offer-heading">
    <h4><?php echo $testimonial_heading ?></h4>
    <p><?php echo $testimonial_paragraph ?></p>
</header>