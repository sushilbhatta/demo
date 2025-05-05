<!-- DISPLAY THE TITLE OF PAGE
<h1><?php the_title(); ?></h1> -->

<!-- GET FIELD DATA -->
<?php if (get_field('sub_heading')): ?>
    <h2><?php echo esc_html(get_field('sub_heading')); ?></h2>
<?php endif; ?>

<div class="hero">
    <?php $image = get_field('hero_image'); ?>
    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
</div>

<?php if (have_rows('event')): ?>
    <ul>
        <?php while (have_rows('event')): the_row(); ?>
            <li>
                <a href="<?php echo esc_url(get_sub_field('url')); ?>"><?php echo esc_html(get_sub_field('title')); ?></a>
            </li>
        <?php endwhile; ?>
    </ul>
<?php endif; ?>