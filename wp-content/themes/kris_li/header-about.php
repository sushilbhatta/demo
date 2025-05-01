<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kris & Li Cleaning Services</title>

    <!--  google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <?php wp_head(); ?>
</head>

<body>
    <!-- backdrop -->
    <div class="backdrop"></div>
    <!-- desktop nav -->
    <header class="main-header main-nav--style">
        <a href="#" class="main-header__brand">
            <?php $header_img = get_dynamic_header_image(); ?>
            <img src="<?php echo esc_url($header_img); ?>" alt="Site Header">
        </a>
        <?php
        wp_nav_menu([
            'theme_location' => 'top-menu',
            'menu_class' => 'top-menu',
            'container' => '',
        ]);
        ?>

    </header>

    <!-- mobile nav -->
    <header class="mobile-nav">
        <a href="#" class="mobile-nav__logo">
            <?php $header_img = get_dynamic_header_image(); ?>
            <img src="<?php echo esc_url($header_img); ?>" alt="Site Header">
        </a>

        <button class="toggle-btn--white"></button>

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