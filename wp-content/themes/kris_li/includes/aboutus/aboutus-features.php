<?php
$feature_title   = get_post_meta(get_the_ID(), '_about_feature_headings', true) ?: '';
$feature_description = get_post_meta(get_the_ID(), '_about_feature_paragraphs', true) ?: '';
$feature_image     = get_post_meta(get_the_ID(), '_about_feature_images', true) ?: '';
$satisfied_client     = get_post_meta(get_the_ID(), '_about_feature_satisfied_client', true) ?: '';


?>
<section class="aboutus-feature">
    <header>
        <?php if (!empty($feature_title)) : ?>

            <h2><?php echo $feature_title ?></h2>

        <?php endif; ?>
        <?php if (!empty($feature_description)) : ?>

            <p><?php echo $feature_description ?></p>

        <?php endif; ?>
    </header>
    <section class="aboutus-content">
        <div class="aboutus-illustrator_container">

            <div class="satisfied-client__illustrator">
                <!-- svg1 -->
                <img class="full-small-gray" src="<?php echo get_template_directory_uri() . '/images/Component.png' ?>" alt="ddd">
                <img class="full-small-white" src="<?php echo get_template_directory_uri() . '/images/full-star.png' ?>" alt="ddd">
                <img class="half-large-gray" src="<?php echo get_template_directory_uri() . '/images/half-star.png' ?>" alt="ddd">
                <div class="satisfied-client__content">
                    <?php if (!empty($satisfied_client)) : ?>

                        <h1><?php echo $satisfied_client ?></h1>
                        <h5>Satisfied Clients</h5>

                    <?php endif; ?>
                </div>
            </div>
            <figure class="illustrator-img">
                <?php if (!empty($feature_image)) : ?>
                    <img src=" <?php echo esc_url($feature_image); ?> " alt="title" />
                <?php endif; ?>
            </figure>
        </div>
        <section class="aboutus-feature_items">
            <?php
            $about_feature_item_title   = get_post_meta($post->ID, '_about_feature_item_title', true) ?: [];
            $about_feature_item_description = get_post_meta($post->ID, '_about_feature_item_description', true) ?: [];
            for ($i = 0; $i < 3; $i++) {

            ?>

                <article class="aboutus-feature_item">
                    <?php if (!empty($about_feature_item_title[$i])) : ?>
                        <h1><?php echo ($about_feature_item_title[$i]); ?> </h1>
                    <?php endif; ?>
                    <?php if (!empty($feature_image[$i])) : ?>
                        <p><?php echo ($about_feature_item_description[$i]); ?> </p>
                    <?php endif; ?>
                </article>
            <?php } ?>
        </section>
    </section>
</section>