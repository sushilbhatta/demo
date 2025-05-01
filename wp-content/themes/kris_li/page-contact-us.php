<?php
get_header('about');

$main_bg_img = get_post_meta(get_the_ID(), '_contactus_bg_image', true) ?: '';
$contact_form_bg_img = get_post_meta(get_the_ID(), '_contact_form_images', true) ?: '';
$contact_form_captcha_logo = get_post_meta(get_the_ID(), '_contact_form_recaptcha_images', true) ?: '';
?>
<main class="contactus-main">
    <section class="contactus_bg-map">
        <img src="<?php echo esc_url($main_bg_img); ?>" alt="map image">
    </section>

    <section>
        <div class="contact-page-container">
            <div class="contact-form-section">
                <h1>Contact Us</h1>
                <div id="form-messages" class="toast-container"></div>
                <form id="contact-form" method="post" novalidate>

                    <!-- first name -->
                    <div class="form-group">
                        <div class="form-group__item">
                            <label for="first-name">First name *</label>
                            <input type="text" id="first-name" name="first-name" placeholder="Text" required>
                        </div>
                        <span class="error-message"></span>
                    </div>

                    <!-- last name -->
                    <div class="form-group">
                        <div class="form-group__item">
                            <label for="last-name">Last name *</label>
                            <input type="text" id="last-name" name="last-name" placeholder="Text" required>
                        </div>
                        <span class="error-message"></span>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <div class="form-group__item">

                            <label for="email">Email address *</label>
                            <input type="email" id="email" name="email" placeholder="Text" required>
                        </div>
                        <span class="error-message"></span>
                    </div>

                    <!-- Contact Number -->
                    <div class="form-group">
                        <div class="form-group__item">

                            <label for="contact-number">Contact number *</label>
                            <input type="text" id="contact-number" name="contact-number" placeholder="Text" required>
                        </div>
                        <span class="error-message"></span>
                    </div>

                    <!-- Enquery Relation -->
                    <div class="form-group">
                        <div class="form-group__item">
                            <label for="enquiry-type">What is your enquiry related to? *</label>
                            <input type="text" id="enquiry-type" name="enquiry-type" placeholder="Text" required>
                        </div>
                        <span class="error-message"></span>
                    </div>

                    <!-- Enquery -->
                    <div class="form-group">
                        <div class="form-group__item">
                            <label for="enquiry">Your enquiry *</label>
                            <textarea id="enquiry" name="enquiry" placeholder="Text" required></textarea>
                        </div>
                        <span class="error-message"></span>
                    </div>


                    <!-- Checkboxes -->
                    <div class="form-footer">
                        <div class="form-footer__content">
                            <div class="checkbox-container">
                                <div class="form-group__item">

                                    <input type="checkbox" id="terms" name="terms">
                                    <label for="terms">I agree to the Terms of Use and Privacy Policy *</label>
                                </div>
                                <span class="error-message"></span>
                            </div>
                            <div class="captcha-container">
                                <div class="form-group__item">

                                    <span class="captcha-checkbox_container">
                                        <input type="checkbox" id="captcha" name="captcha">
                                        <label for="captcha">I'm not a robot *</label>
                                    </span>
                                    <span class="captcha-branding">
                                        <img src="<?php echo esc_url($contact_form_captcha_logo); ?>" alt="Recaptcha Branding Image">
                                    </span>
                                </div>
                                <span class="error-message"></span>
                            </div>
                        </div>


                        <button type="submit" class="submit-button">Submit</button>
                    </div>
                    <input type="hidden" name="action" value="submit_contact_form">
                    <?php wp_nonce_field('contact_form_nonce', 'contact_form_nonce'); ?>
                </form>
            </div>
            <div class="contact-image-section">
                <img src="<?php echo esc_url($contact_form_bg_img); ?>" alt="Sydney Harbour Bridge">
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>