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
                const tabList = document.querySelector('.tab-titles');

                if ('scrollRestoration' in history) {
                    history.scrollRestoration = 'manual'; //scroll manually to location
                }

                function activateTab(target, updateHash = true, scroll = true) {
                    contents.forEach(content => {
                        content.style.display = 'none'; //hide the content of tabs
                    });

                    const targetContent = document.getElementById(target);
                    if (targetContent) {
                        targetContent.style.display = 'flex'; //show the content of clicked tab only
                    }

                    tabs.forEach(t => t.classList.remove('active')); //remove while switching

                    const targetTab = document.querySelector(`.tab-title_item[data-tab="${target}"]`);

                    if (targetTab) {
                        targetTab.classList.add('active');
                        targetTab.setAttribute('aria-selected', 'true');
                    }

                    tabs.forEach(t => {
                        if (t !== targetTab) {
                            t.setAttribute('aria-selected', 'false');
                        }
                    });

                    if (scroll && tabList) {
                        const header = document.querySelector('header');
                        const defaultOffset = 20;
                        const headerHeight = header ? header.offsetHeight : 0;
                        const offset = headerHeight > 0 ? headerHeight + defaultOffset : 250;

                        const tabListRect = tabList.getBoundingClientRect();
                        const scrollTop = window.pageYOffset + tabListRect.top - offset;
                        window.scrollTo({
                            top: scrollTop,
                            behavior: 'smooth'
                        });
                    }


                    if (updateHash) {
                        history.pushState(null, null, `#${target}`);
                    }
                }


                tabs.forEach(tab => {
                    tab.addEventListener('click', function(e) {
                        e.preventDefault();
                        const target = this.getAttribute('data-tab');
                        activateTab(target, true, true);
                    });
                });


                window.addEventListener('load', function() {
                    const hash = window.location.hash.replace('#', ''); //remove # form hashstring
                    if (hash && document.getElementById(hash)) {

                        activateTab(hash, false, true);
                    } else if (tabs.length > 0) {

                        activateTab('tab-0', false, false);
                    }
                });

                window.addEventListener('hashchange', function() {
                    const newHash = window.location.hash.replace('#', '');
                    if (newHash && document.getElementById(newHash)) {
                        activateTab(newHash, false, true);
                    }
                });
            });
        </script>

    <?php
        wp_reset_postdata();
    endif;
    ?>
</section>