<?php
$heading = get_post_meta(get_the_ID(), '_offer_main_heading', true) ?: "What we offer";
$paragraph = get_post_meta(get_the_ID(), '_offer_main_paragraph', true) ?: "We are an Australian web design agency that offers full-service solutions for clients worldwide.";

?>
<header class="offer-heading">
    <h4><?php echo $heading ?></h4>
    <p><?php echo $paragraph ?></p>
</header>