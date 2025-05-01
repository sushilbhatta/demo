<?php
$faqs = get_post_meta(get_the_ID(), '_custom_faqs', true);
if (!empty($faqs) && is_array($faqs)) :
?>
    <section>
        <header>
            <h2 class="terms_heading">
                Frequently Asked Questions
            </h2>
        </header>

        <div class="faq-container">
            <ul class="faq-list__items">
                <?php foreach ($faqs as $faq) : ?>
                    <li class="faq-list_item">
                        <div class="list-item_content">
                            <h1 class="list-item_content--heading"><?php echo esc_html($faq['question']); ?></h1>
                        </div>
                        <p class="list-item_answer"><?php echo esc_html($faq['answer']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>

            <button class="content_btn faq" style="margin:0 auto;margin-top: 48px;background:transparent;">
                Load More
            </button>

        </div>
    </section>

<?php endif; ?>