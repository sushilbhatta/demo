<section class="cleaning-services">
    <header class="offer-heading">
        <h4>Our Cleaning Services</h4>
        <p>We are an Australian web design agency that offers full-service solutions for clients worldwide.</p>
    </header>
    <?php
    $args = array(
        'post_type' => 'tab_content',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC'
    );

    $tab_query = new WP_Query($args);

    if ($tab_query->have_posts()) :
    ?>
        <div class="tabs">
            <ul class="tab-titles">
                <?php
                $i = 0;
                // get svg based on index of the current tab
                $svgs = get_tab_svgs();
                while ($tab_query->have_posts()) :
                    $tab_query->the_post();
                    $svg_code = isset($svgs[$i]) ? $svgs[$i] : $svgs[0];
                ?>
                    <li class="tab-title_item" data-tab="tab-<?php echo $i; ?>">
                        <span class="tab_icon tab_icon_<?php echo $i; ?>">
                            <?php echo $svg_code; ?>
                        </span>
                        <span class="tab_title">
                            <?php the_title(); ?>
                        </span>
                    </li>
                <?php
                    $i++;
                endwhile;
                ?>
            </ul>

            <div class="tab-contents">
                <?php
                $i = 0;
                $tab_query->rewind_posts();
                while ($tab_query->have_posts()) :
                    $tab_query->the_post();
                    $headings   = get_post_meta(get_the_ID(), '_service_tab_posts_headings', true) ?: '';
                    $paragraphs = get_post_meta(get_the_ID(), '_service_tab_posts_paragraphs', true) ?: '';
                    $images     = get_post_meta(get_the_ID(), '_service_tab_posts_images', true) ?: '';
                    $buttons    = get_post_meta(get_the_ID(), '_service_tab_posts_buttons', true) ?: '';
                    $button_links = get_post_meta(get_the_ID(), '_service_tab_posts_button_links', true) ?: '';

                ?>
                    <article id="tab-<?php echo $i; ?>" class="tab-content" style="display: none;" ?>
                        <div class="feature_content service_content">
                            <?php if (!empty($headings)): ?>
                                <h2 class="content_heading"><?php echo esc_html($headings); ?></h2>
                            <?php endif; ?>

                            <?php if (!empty($paragraphs)): ?>
                                <p class="content_description"><?php echo esc_html($paragraphs); ?></p>
                            <?php endif; ?>

                            <?php if (!empty($buttons) && !empty($button_links)): ?>
                                <a class="content_btn" href="<?php echo esc_url($button_links); ?>">
                                    <span contact_btn--text>

                                        <?php echo esc_html($buttons); ?>
                                    </span>
                                    <span class="content_btn--icon"></span></a>
                            <?php endif; ?>
                        </div>

                        <div class="image-container service-image-container">
                            <?php if (!empty($images)): ?>
                                <img class=" fe" src="<?php echo esc_url($images); ?>" alt="Contact_Image">
                            <?php endif; ?>
                        </div>
                    </article>
                <?php
                    $i++;
                endwhile;
                ?>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tabs = document.querySelectorAll('.tab-titles li');
                const contents = document.querySelectorAll('.tab-content');
                // get active tab only
                function activateTab(target) {
                    contents.forEach(content => {
                        content.style.display = 'none';
                    });

                    const targetContent = document.getElementById(target);
                    if (targetContent) {
                        targetContent.style.display = 'flex';
                    }

                    tabs.forEach(t => t.classList.remove('active'));
                    const targetTab = document.querySelector(`.tab-title_item[data-tab="${target}"]`);
                    if (targetTab) {
                        targetTab.classList.add('active');
                    }
                }

                // Handle tab Toggling/changing
                tabs.forEach(tab => {
                    tab.addEventListener('click', function() {
                        const target = this.getAttribute('data-tab');
                        activateTab(target);
                        window.location.hash = target;
                    });
                });

                // Check for hash in URL on page load
                const hash = window.location.hash.replace('#', '');
                if (hash && document.getElementById(hash)) {
                    activateTab(hash);
                } else if (tabs.length > 0) {
                    tabs[0].click();
                }

                // Handle hash changes (e.g., browser back/forward)
                window.addEventListener('hashchange', function() {
                    const newHash = window.location.hash.replace('#', '');
                    if (newHash && document.getElementById(newHash)) {
                        activateTab(newHash);
                    }
                });
            });
        </script>
        </script>

    <?php
        wp_reset_postdata();
    endif;
    ?>
</section>