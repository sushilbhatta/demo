<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kris & Li Cleaning Services</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <?php wp_head(); ?>
</head>

<body>
    <!-- Backdrop -->
    <div class="backdrop"></div>

    <!-- Desktop Nav -->
    <header class="main-header">
        <!-- Light header (small) -->
        <a href="#" class="main-header__brand light sm">
            <?php $light_header_image = get_light_header_image(); ?>
            <img src="<?php echo esc_url($light_header_image); ?>" alt="Site Header">
        </a>

        <!-- Light header (large) -->
        <a href="#" class="main-header__brand light lg">
            <?php $light_header_image_lg = get_light_header_image_xl(); ?>
            <img src="<?php echo esc_url($light_header_image_lg); ?>" alt="Site Header">
        </a>

        <!-- Dark header -->
        <a href="#" class="main-header__brand dark">
            <?php $dark_header_image = get_dark_header_image(); ?>
            <img src="<?php echo esc_url($dark_header_image); ?>" alt="Site Header">
        </a>

        <?php
        wp_nav_menu([
            'theme_location' => 'top-menu',
            'menu_class' => 'top-menu',
            'container' => '',
        ]);
        ?>
    </header>

    <!-- Mobile Nav -->
    <header class="mobile-nav">
        <!-- Small device logo -->
        <a href="#" class="main-header__brand light sm">
            <?php $light_header_image = get_light_header_image(); ?>
            <img src="<?php echo esc_url($light_header_image); ?>" alt="Site Header">
        </a>

        <!-- Large device logo -->
        <a href="#" class="main-header__brand light lg">
            <?php $light_header_image_lg = get_light_header_image_xl(); ?>
            <img src="<?php echo esc_url($light_header_image_lg); ?>" alt="Site Header">
        </a>

        <!-- Dark header -->
        <a href="#" class="main-header__brand dark">
            <?php $dark_header_image = get_dark_header_image(); ?>
            <img src="<?php echo esc_url($dark_header_image); ?>" alt="Site Header">
        </a>

        <button class="toggle-button"></button>

        <nav class="mobile-nav__container">
            <?php
            wp_nav_menu([
                'theme_location' => 'mobile-menu',
                'menu_class' => 'mobile-menu',
                'container' => '',
            ]);
            ?>
        </nav>
    </header>

    <?php wp_body_open(); ?>
</body>

</html>