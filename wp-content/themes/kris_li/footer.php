<footer>
    <section class="get-in-touch">
        <?php
        $headings   = get_post_meta(get_the_ID(), '_get_in_touch_headings', true) ?: '';
        $paragraphs = get_post_meta(get_the_ID(), '_get_in_touch_paragraphs', true) ?: '';
        $images     = get_post_meta(get_the_ID(), '_get_in_touch_images', true) ?: '';
        $buttons    = get_post_meta(get_the_ID(), '_get_in_touch_buttons', true) ?: '';
        $button_links = get_post_meta(get_the_ID(), '_get_in_touch_button_links', true) ?: '';
        ?>
        <div class="get-in-touch_container">
            <?php if (!empty($images)): ?>
                <div class="get-in-touch-background"
                    style="background:linear-gradient(to bottom,hsla(221, 42%, 15%, 0.5),hsla(221, 42%, 15%, 0.8),hsla(221, 42%, 15%, 1)), url('<?php echo esc_url_raw($images); ?>') top 30% right 0%/cover no-repeat border-box;"></div>
            <?php endif; ?>
            <div class=" get-in-touch_content">
                <?php if (!empty($headings)): ?>
                    <h2> <?php echo $headings; ?> </h2>
                <?php endif; ?>
                <?php if (!empty($paragraphs)): ?>
                    <p> <?php echo $paragraphs; ?> </p>
                <?php endif; ?>
                <?php if (!empty($buttons[$i]) && !empty($button_links)): ?>
                    <a class="contact_btn get_in_touch_btn" href="<?php echo esc_url($button_links); ?>" class="button">
                        <?php echo esc_html($buttons); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <section class="footer">

        <section class="footer-main">
            <!-- Footer Header -->
            <div class="footer_header">
                <a href="#" class="footer_header-logo">
                    <?php $logoimg = get_header_image(); ?>
                    <img src="<?php echo $logoimg; ?>" alt="">
                </a>
                <p class="footer_header-description">Specialised cleaning solutions for healthcare facilities and other commercial premises.</p>
            </div>

            <section class="footer-links">

                <!-- Quick Links -->
                <div class="footer_quick_links">
                    <h4 class="footer_link-title">Quick Links</h4>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'footer-menu-quick-link'
                    ]);
                    ?>
                </div>

                <!-- Services -->
                <div class="footer_services_links">
                    <h4 class="footer_link-title">Services</h4>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'footer-menu-services'
                    ]);
                    ?>
                </div>

                <!-- Contact Us Link -->
                <div class="footer_contact_us">
                    <h4 class="footer_link-title">Contact Us</h4>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'footer-menu-custom-link'
                    ]);
                    ?>
                </div>
            </section>
        </section>

        <!-- copyright and privacy policy -->
        <section class="footer-end">

            <!-- privacy and terms -->
            <div footer-end_links>
                <?php
                wp_nav_menu([
                    'theme_location' => 'footer-menu-end-link'
                ]);
                ?>
            </div>

            <p class="copyright">Copyright @ebPearls</p>

        </section>
    </section>
</footer>

<?php wp_footer(); ?>
</body>

</html>