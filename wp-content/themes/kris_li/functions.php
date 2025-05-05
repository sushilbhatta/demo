<?php
// theme options
add_theme_support('title-tag');
add_theme_support('post-thumbnails');
add_theme_support('menus');
add_theme_support('html5');
add_theme_support('custom-background');
add_theme_support('post-formats', array('gallary'));

// svg Support 
function enable_svg_upload($mimes)
{
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter('upload_mimes', 'enable_svg_upload');

// Style
function kris_li_enqueue_styles()
{
	wp_register_style('main-css', get_template_directory_uri() . '/style.css', [], time(), 'all');
	wp_register_style('shared-css', get_template_directory_uri() . '/css/shared.css', [], time(), 'all');
	wp_register_style('home-css', get_template_directory_uri() . '/css/home.css', [], time(), 'all');
	wp_register_style('about-css', get_template_directory_uri() . '/css/aboutus.css', [], time(), 'all');
	wp_register_style('services-css', get_template_directory_uri() . '/css/services.css', [], time(), 'all');
	wp_register_style('contactus-css', get_template_directory_uri() . '/css/contactus.css', [], time(), 'all');
	wp_register_style('terms_privacy-css', get_template_directory_uri() . '/css/terms_privacy.css', [], time(), 'all');
	wp_register_style('faq-css', get_template_directory_uri() . '/css/faq.css', [], time(), 'all');
	wp_enqueue_style('main-css');
	wp_enqueue_style('shared-css');
	wp_enqueue_style('home-css');
	wp_enqueue_style('about-css');
	wp_enqueue_style('services-css');
	wp_enqueue_style('contactus-css');
	wp_enqueue_style('terms_privacy-css');
	wp_enqueue_style('faq-css');
}

add_action('wp_enqueue_scripts', 'kris_li_enqueue_styles');

//  Javascript
function kris_li_enqueue_scripts()
{
	wp_register_script('main-js', get_template_directory_uri() . '/js/scripts.js', [], time(), true);
	wp_enqueue_script('main-js');
}

add_action('wp_enqueue_scripts', 'kris_li_enqueue_scripts');

// Images
wp_enqueue_script('my-theme-js', get_template_directory_uri() . '/js/main.js', [], null, true);
wp_localize_script('my-theme-js', 'myTheme', [
	'themeUrl' => get_template_directory_uri(),
]);


// AjAX
add_action('wp_enqueue_scripts', 'contact_form_enqueue_scripts');
function contact_form_enqueue_scripts()
{
	wp_enqueue_script('contact-form-script', get_template_directory_uri() . '/js/contact-form.js', [], null, true);

	wp_localize_script('contact-form-script', 'contactFormAjax', [
		'ajaxurl' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('contact_form_nonce')
	]);
}

// Handle form submission
add_action('wp_ajax_submit_contact_form', 'handle_contact_form_submission');
add_action('wp_ajax_nopriv_submit_contact_form', 'handle_contact_form_submission');
function handle_contact_form_submission()
{
	check_ajax_referer('contact_form_nonce', 'contact_form_nonce');

	$response = ['success' => false, 'errors' => []];

	// Backend validation
	$first_name = sanitize_text_field($_POST['first-name'] ?? '');
	$last_name = sanitize_text_field($_POST['last-name'] ?? '');
	$email = sanitize_email($_POST['email'] ?? '');
	$contact_number = sanitize_text_field($_POST['contact-number'] ?? '');
	$enquiry_type = sanitize_text_field($_POST['enquiry-type'] ?? '');
	$enquiry = sanitize_textarea_field($_POST['enquiry'] ?? '');
	$terms = isset($_POST['terms']) && $_POST['terms'] === 'on';
	$captcha = isset($_POST['captcha']) && $_POST['captcha'] === 'on';

	if (empty($first_name)) {
		$response['errors']['first-name'] = 'First name is required';
	} elseif (strlen($first_name) > 50) {
		$response['errors']['first-name'] = 'First name must be less than 50 characters';
	}

	if (empty($last_name)) {
		$response['errors']['last-name'] = 'Last name is required';
	} elseif (strlen($last_name) > 50) {
		$response['errors']['last-name'] = 'Last name must be less than 50 characters';
	}

	if (empty($email)) {
		$response['errors']['email'] = 'Email is required';
	} elseif (!is_email($email)) {
		$response['errors']['email'] = 'Invalid email format';
	}

	if (empty($contact_number)) {
		$response['errors']['contact-number'] = 'Contact number is required';
	} elseif (!preg_match('/^[\d\s+()-]{7,20}$/', $contact_number)) {
		$response['errors']['contact-number'] = 'Invalid phone number format';
	}

	if (empty($enquiry_type)) {
		$response['errors']['enquiry-type'] = 'Enquiry type is required';
	} elseif (strlen($enquiry_type) > 100) {
		$response['errors']['enquiry-type'] = 'Enquiry type must be less than 100 characters';
	}

	if (empty($enquiry)) {
		$response['errors']['enquiry'] = 'Enquiry is required';
	} elseif (strlen($enquiry) > 1000) {
		$response['errors']['enquiry'] = 'Enquiry must be less than 1000 characters';
	}

	if (!$terms) {
		$response['errors']['terms'] = 'You must agree to the Terms of Use and Privacy Policy';
	}

	if (!$captcha) {
		$response['errors']['captcha'] = 'Please verify you are not a robot';
	}

	// Saving to db
	if (empty($response['errors'])) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'contact_form_submissions';

		$result = $wpdb->insert(
			$table_name,
			[
				'first_name' => $first_name,
				'last_name' => $last_name,
				'email' => $email,
				'contact_number' => $contact_number,
				'enquiry_type' => $enquiry_type,
				'enquiry' => $enquiry,
				'created_at' => current_time('mysql')
			],
			['%s', '%s', '%s', '%s', '%s', '%s', '%s']
		);

		if ($result !== false) {
			$response['success'] = true;
			$response['message'] = 'Form submitted successfully!';
		} else {
			$response['errors']['general'] = 'Failed to save submission';
		}
	}

	wp_send_json($response);
}



// Dynamic header logos According to page

function get_dynamic_header_image()
{
	$header_image = get_template_directory_uri() . '/images/main_header_logo.svg';
	if (is_page('about') || is_page('contact-us') || is_page('privacy') || is_page('our-services')) {
		$header_image = get_template_directory_uri() . '/images/dark_nav_header.png';
	}
	return $header_image;
}

// dark dynamic logo
function get_dark_header_image()
{
	$dark_header_image = get_template_directory_uri() . '/images/dark_nav_header.png';
	return $dark_header_image;
}

//light dynamic logo
function get_light_header_image()
{
	$light_header_image = get_template_directory_uri() . '/images/mobile_header_light.svg';
	return $light_header_image;
}
function get_light_header_image_xl()
{
	$light_header_image = get_template_directory_uri() . '/images/main_header_light.svg';
	return $light_header_image;
}

// Register Navigation Menus
register_nav_menus([
	'top-menu' => esc_html__('Top Menu Location', 'kris_li'),
	'mobile-menu' => esc_html__('Mobile Menu Location', 'kris_li'),
	'footer-menu-quick-link' => esc_html__('Footer Menu Quick Link Location', 'kris_li'),
	'footer-menu-services' => esc_html__('Footer Menu Services Location', 'kris_li'),
	'footer-menu-custom-link' => esc_html__('Footer Menu Custom Link Location', 'kris_li'),
	'footer-menu-end-link' => esc_html__('Footer Menu End Location', 'kris_li'),

]);


// Dropdown menu 
function add_dropdown_toggles_to_menu($item_output, $item, $depth, $args)
{
	if (in_array('menu-item-has-children', $item->classes)) {

		$item_output .= '<button class="dropdown-toggle" aria-expanded="false"><span class="screen-reader-text">Toggle submenu</span></button>';
	};
	return $item_output;
}
add_filter('walker_nav_menu_start_el', 'add_dropdown_toggles_to_menu', 10, 4);


//  get block from backend via classname
function get_blocks_by_name_with_class($block_name, $class_name, $blocks = null)
{
	global $post;

	if (is_null($blocks)) {
		if (!isset($post)) return [];
		$blocks = parse_blocks($post->post_content);
	}

	$matched_blocks = [];

	foreach ($blocks as $block) {
		if (
			isset($block['blockName']) &&
			$block['blockName'] === $block_name &&
			isset($block['attrs']['className']) &&
			in_array($class_name, explode(' ', $block['attrs']['className']))
		) {
			$matched_blocks[] = $block;
		}

		if (!empty($block['innerBlocks'])) {
			$inner_matches = get_blocks_by_name_with_class($block_name, $class_name, $block['innerBlocks']);
			$matched_blocks = array_merge($matched_blocks, $inner_matches);
		}
	}

	return $matched_blocks;
}


// %%%%%%%%%%%%%%%%%%%%%%%%%%%% HOME PAGE %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

// hero section metaboxes
function hero_section_meta_box()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'home') {

		add_meta_box(
			'hero_section_meta',
			'Hero Section',
			'hero_section_meta_box_callback',
			['post', 'page'],
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'hero_section_meta_box');

function hero_section_meta_box_callback($post)
{
	wp_nonce_field('hero_section_nonce_action', 'hero_section_nonce');

	$labels     = get_post_meta($post->ID, '_hero_labels', true) ?: [];
	$headings   = get_post_meta($post->ID, '_hero_title', true) ?: [];
	$paragraphs = get_post_meta($post->ID, '_hero_description', true) ?: [];
	$images     = get_post_meta($post->ID, '_hero_bg_images', true) ?: [];
	$buttons    = get_post_meta($post->ID, '_hero_buttons', true) ?: [];
	$button_links = get_post_meta($post->ID, '_hero_button_links', true) ?: [];

	for ($i = 0; $i < 4; $i++) {
?>
		<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
			<h4>Hero <?php echo $i + 1; ?></h4>
			<p><label>Hero Label:</label><br>
				<input type="text" name="hero_labels[]" value="<?php echo esc_attr($labels[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p><label>Hero Title :</label><br>
				<input type="text" name="hero_title[]" value="<?php echo esc_attr($headings[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p><label>Hero Description:</label><br>
				<textarea name="hero_description[]" rows="4" style="width:100%;"><?php echo esc_textarea($paragraphs[$i] ?? ''); ?></textarea>
			</p>

			<p><label>Image URL:</label><br>
				<input type="text" name="hero_images[]" id="hero_image_<?php echo $i; ?>" value="<?php echo esc_attr($images[$i] ?? ''); ?>" style="width:80%;">
				<button class="button upload-image" data-target="hero_image_<?php echo $i; ?>">Upload</button>
			</p>
			<p><label>Button Label:</label><br>
				<input type="text" name="hero_buttons[]" value="<?php echo esc_attr($buttons[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p><label>Button Link:</label><br>
				<input type="text" name="hero_button_links[]" value="<?php echo esc_attr($button_links[$i] ?? ''); ?>" style="width:100%;">
			</p>
		</div>
	<?php
	}

	?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.upload-image').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const inputId = this.dataset.target;
					const input = document.getElementById(inputId);
					const uploader = wp.media({
						title: 'Select Image',
						button: {
							text: 'Use this image'
						},
						multiple: false
					}).on('select', function() {
						const attachment = uploader.state().get('selection').first().toJSON();
						input.value = attachment.url;
					}).open();
				});
			});
		});
	</script>
<?php
}

function save_hero_section_meta_box($post_id)
{
	if (
		!isset($_POST['hero_section_nonce']) ||
		!wp_verify_nonce($_POST['hero_section_nonce'], 'hero_section_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_hero_labels', array_map('sanitize_text_field', $_POST['hero_labels'] ?? []));
	update_post_meta($post_id, '_hero_title', array_map('sanitize_text_field', $_POST['hero_title'] ?? []));
	update_post_meta($post_id, '_hero_description', array_map('sanitize_textarea_field', $_POST['hero_description'] ?? []));
	update_post_meta($post_id, '_hero_bg_images', array_map('esc_url_raw', $_POST['hero_images'] ?? []));
	update_post_meta($post_id, '_hero_buttons', array_map('sanitize_text_field', $_POST['hero_buttons'] ?? []));
	update_post_meta($post_id, '_hero_button_links', array_map('esc_url_raw', $_POST['hero_button_links'] ?? []));
}

add_action('save_post', 'save_hero_section_meta_box');

//  Carousel heading metaboxes
function brand_carousel_section_meta_box()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'home') {

		add_meta_box(
			'brand_carousel_section_meta',
			'Brand_carousel Section',
			'brand_carousel_section_meta_box_callback',
			['post', 'page'],
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'brand_carousel_section_meta_box');

function brand_carousel_section_meta_box_callback($post)
{
	$brand_carousel_main_heading = get_post_meta($post->ID, '_brand_carousel_main_heading', true);
	$brand_carousel_main_paragraph = get_post_meta($post->ID, '_brand_carousel_main_paragraph', true);

	wp_nonce_field('feature_section_nonce_action', 'feature_section_nonce');
?>
	<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
		<h4>Brand_carousel Heading</h4>
		<p><label>Heading:</label><br>
			<input type="text" name="brand_carousel_main_heading" value="<?php echo esc_attr($brand_carousel_main_heading); ?>" style="width:100%;">
		</p>
		<p><label>Paragraph:</label><br>
			<textarea name="brand_carousel_main_paragraph" rows="4" style="width:100%;"><?php echo esc_textarea($brand_carousel_main_paragraph); ?></textarea>
		</p>
	</div>
	<?php
}

function save_brand_carousel_section_meta_box($post_id)
{
	if (
		!isset($_POST['feature_section_nonce']) ||
		!wp_verify_nonce($_POST['feature_section_nonce'], 'feature_section_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_brand_carousel_main_heading', sanitize_text_field($_POST['brand_carousel_main_heading'] ?? ''));
	update_post_meta($post_id, '_brand_carousel_main_paragraph', sanitize_textarea_field($_POST['brand_carousel_main_paragraph'] ?? ''));
}
add_action('save_post', 'save_brand_carousel_section_meta_box');


//Carousel content meta_box
function carousel_meta_box()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'home') {

		add_meta_box(
			'carousel_meta',
			'Carousel Section',
			'carousel_meta_box_callback',
			['post', 'page'],
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'carousel_meta_box');

function carousel_meta_box_callback($post)
{
	wp_nonce_field('carousel_nonce_action', 'carousel_nonce');

	$carousel_icon     = get_post_meta($post->ID, '_carousel_icon', true) ?: [];



	for ($i = 0; $i < 6; $i++) {
	?>
		<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
			<h4>Carousels <?php echo $i + 1; ?></h4>
			<p>
				<label>Add Logo URL:</label><br>
				<input type="text" name="carousel_image[]" id="carousel_image_<?php echo $i; ?>" value="<?php echo esc_attr($carousel_icon[$i] ?? ''); ?>" style="width:80%;">
				<button class="button upload-icon" data-target="carousel_image_<?php echo $i; ?>">Upload</button>
			</p>
		</div>
	<?php
	}

	?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.upload-icon').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const inputId = this.dataset.target;
					const input = document.getElementById(inputId);
					const uploader = wp.media({
						title: 'Select Image',
						button: {
							text: 'Use this image'
						},
						multiple: false
					}).on('select', function() {
						const attachment = uploader.state().get('selection').first().toJSON();
						input.value = attachment.url;
					}).open();
				});
			});
		});
	</script>
	<?php
}

function save_carousel_meta_box($post_id)
{
	if (
		!isset($_POST['carousel_nonce']) ||
		!wp_verify_nonce($_POST['carousel_nonce'], 'carousel_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_carousel_icon', array_map('esc_url_raw', $_POST['carousel_image'] ?? []));
}
add_action('save_post', 'save_carousel_meta_box');


// feature section metaboxes
function feature_section_meta_box()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'home') {

		add_meta_box(
			'feature_section_meta',
			'Feature Section',
			'feature_section_meta_box_callback',
			['post', 'page'],
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'feature_section_meta_box');

function feature_section_meta_box_callback($post)
{
	wp_nonce_field('feature_section_nonce_action', 'feature_section_nonce');

	$labels     = get_post_meta($post->ID, '_feature_labels', true) ?: [];
	$headings   = get_post_meta($post->ID, '_feature_headings', true) ?: [];
	$paragraphs = get_post_meta($post->ID, '_feature_paragraphs', true) ?: [];
	$images     = get_post_meta($post->ID, '_feature_images', true) ?: [];
	$buttons    = get_post_meta($post->ID, '_feature_buttons', true) ?: [];
	$button_links = get_post_meta($post->ID, '_feature_button_links', true) ?: [];
	$image_orintation = get_post_meta($post->ID, '_feature_img_orintation', true) ?: [];
	$image_container_color = get_post_meta($post->ID, '_feature_container_color', true) ?: [];

	for ($i = 0; $i < 3; $i++) {
	?>
		<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
			<h4>Feature <?php echo $i + 1; ?></h4>
			<p><label>Label:</label><br>
				<input type="text" name="feature_labels[]" value="<?php echo esc_attr($labels[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p><label>Heading:</label><br>
				<input type="text" name="feature_headings[]" value="<?php echo esc_attr($headings[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p><label>Paragraph:</label><br>
				<textarea name="feature_paragraphs[]" rows="4" style="width:100%;"><?php echo esc_textarea($paragraphs[$i] ?? ''); ?></textarea>
			</p>

			<p><label>Image URL:</label><br>
				<input type="text" name="feature_images[]" id="feature_image_<?php echo $i; ?>" value="<?php echo esc_attr($images[$i] ?? ''); ?>" style="width:80%;">
				<button class="button upload-image" data-target="feature_image_<?php echo $i; ?>">Upload</button>
			</p>
			<p><label>Button Label:</label><br>
				<input type="text" name="feature_buttons[]" value="<?php echo esc_attr($buttons[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p><label>Button Link:</label><br>
				<input type="text" name="feature_button_links[]" value="<?php echo esc_attr($button_links[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p>
				<label>Image Orintation</label><br>
				<select name="feature_img_orintation[]" id="feature_img_orintation<?php echo $i; ?>">
					<option value="normal" <?php selected($image_orintation[$i], 'top'); ?>>Top</option>
					<option value="right" <?php selected($image_orintation[$i], 'right'); ?>>Right</option>
					<option value="bottom" <?php selected($image_orintation[$i], 'bottom'); ?>>Bottom</option>
					<option value="left" <?php selected($image_orintation[$i], 'left'); ?>>Left</option>
				</select>

			</p>

			<p>
				<label>Image Orintation</label><br>
				<select name="feature_img_container_color[]" id="feature_img_container_color<?php echo $i; ?>">
					<option value="orange" <?php selected($image_container_color[$i], 'orange'); ?>>Light Orange</option>
					<option value="blue" <?php selected($image_container_color[$i], 'blue'); ?>> Light Blue</option>
					<option value="violet" <?php selected($image_container_color[$i], 'violet'); ?>>Violet</option>

				</select>

			</p>
		</div>
	<?php
	}

	?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.upload-image').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const inputId = this.dataset.target;
					const input = document.getElementById(inputId);
					const uploader = wp.media({
						title: 'Select Image',
						button: {
							text: 'Use this image'
						},
						multiple: false
					}).on('select', function() {
						const attachment = uploader.state().get('selection').first().toJSON();
						input.value = attachment.url;
					}).open();
				});
			});
		});
	</script>
<?php
}


function save_feature_section_meta_box($post_id)
{
	if (
		!isset($_POST['feature_section_nonce']) ||
		!wp_verify_nonce($_POST['feature_section_nonce'], 'feature_section_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_feature_labels', array_map('sanitize_text_field', $_POST['feature_labels'] ?? []));
	update_post_meta($post_id, '_feature_headings', array_map('sanitize_text_field', $_POST['feature_headings'] ?? []));
	update_post_meta($post_id, '_feature_paragraphs', array_map('sanitize_textarea_field', $_POST['feature_paragraphs'] ?? []));
	update_post_meta($post_id, '_feature_images', array_map('esc_url_raw', $_POST['feature_images'] ?? []));
	update_post_meta($post_id, '_feature_buttons', array_map('sanitize_text_field', $_POST['feature_buttons'] ?? []));
	update_post_meta($post_id, '_feature_button_links', array_map('esc_url_raw', $_POST['feature_button_links'] ?? []));
	update_post_meta($post_id, '_feature_img_orintation', array_map('sanitize_text_field', $_POST['feature_img_orintation'] ?? []));
	update_post_meta($post_id, '_feature_container_color', array_map('sanitize_text_field', $_POST['feature_img_container_color'] ?? []));
}
add_action('save_post', 'save_feature_section_meta_box');



// Offer Section  Heading Meta box
function offer_section_meta_box()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'home') {

		add_meta_box(
			'offer_section_meta',
			'Offer Section Heading',
			'offer_section_meta_box_callback',
			['post', 'page'],
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'offer_section_meta_box');

function offer_section_meta_box_callback($post)
{
	$offer_main_heading = get_post_meta($post->ID, '_offer_main_heading', true);
	$offer_main_paragraph = get_post_meta($post->ID, '_offer_main_paragraph', true);

	wp_nonce_field('feature_section_nonce_action', 'feature_section_nonce');
?>
	<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
		<h4>Offer Heading</h4>
		<p><label>Heading:</label><br>
			<input type="text" name="offer_main_heading" value="<?php echo esc_attr($offer_main_heading); ?>" style="width:100%;">
		</p>
		<p><label>Paragraph:</label><br>
			<textarea name="offer_main_paragraph" rows="4" style="width:100%;"><?php echo esc_textarea($offer_main_paragraph); ?></textarea>
		</p>
	</div>
	<?php
}

function save_offer_section_meta_box($post_id)
{
	if (
		!isset($_POST['feature_section_nonce']) ||
		!wp_verify_nonce($_POST['feature_section_nonce'], 'feature_section_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_offer_main_heading', sanitize_text_field($_POST['offer_main_heading'] ?? ''));
	update_post_meta($post_id, '_offer_main_paragraph', sanitize_textarea_field($_POST['offer_main_paragraph'] ?? ''));
}
add_action('save_post', 'save_offer_section_meta_box');


// Offer Content  meta_box
function offer_meta_box()

{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'home') {

		add_meta_box(
			'offer_meta',
			'Offer Section Content',
			'offer_meta_box_callback',
			['post', 'page'],
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'offer_meta_box');

function offer_meta_box_callback($post)
{
	wp_nonce_field('offer_nonce_action', 'offer_nonce');

	$offer_icon     = get_post_meta($post->ID, '_offer_icon', true) ?: [];
	$offer_title   = get_post_meta($post->ID, '_offer_title', true) ?: [];
	$offer_description = get_post_meta($post->ID, '_offer_description', true) ?: [];
	$offer_button    = get_post_meta($post->ID, '_offer_button', true) ?: [];
	$offer_button_links = get_post_meta($post->ID, '_offer_button_links', true) ?: [];


	for ($i = 0; $i < 6; $i++) {
	?>
		<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
			<h4>Offers <?php echo $i + 1; ?></h4>


			<p>
				<label>Add Icon URL:</label><br>
				<input type="text" name="offer_image[]" id="offer_image_<?php echo $i; ?>" value="<?php echo esc_attr($offer_icon[$i] ?? ''); ?>" style="width:80%;">
				<button class="button upload-icon" data-target="offer_image_<?php echo $i; ?>">Upload</button>

			</p>
			<p><label>Title:</label><br>
				<input type="text" name="offer_title[]" value="<?php echo esc_attr($offer_title[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p><label>Description:</label><br>
				<textarea name="offer_description[]" rows="4" style="width:100%;"><?php echo esc_textarea($offer_description[$i] ?? ''); ?></textarea>
			</p>

			<p><label>Button Label:</label><br>
				<input type="text" name="offer_button[]" value="<?php echo esc_attr($offer_button[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p>
				<label>Button Link:</label><br>
				<input type="text" name="offer_button_links[]" value="<?php echo esc_attr($offer_button_links[$i] ?? ''); ?>" style="width:100%;">
			</p>
		</div>
	<?php
	}

	?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.upload-icon').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const inputId = this.dataset.target;
					const input = document.getElementById(inputId);
					const uploader = wp.media({
						title: 'Select Image',
						button: {
							text: 'Use this image'
						},
						multiple: false
					}).on('select', function() {
						const attachment = uploader.state().get('selection').first().toJSON();
						input.value = attachment.url;
					}).open();
				});
			});
		});
	</script>
<?php
}

function save_offer_meta_box($post_id)
{
	if (
		!isset($_POST['offer_nonce']) ||
		!wp_verify_nonce($_POST['offer_nonce'], 'offer_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_offer_icon', array_map('esc_url_raw', $_POST['offer_image'] ?? []));
	update_post_meta($post_id, '_offer_title', array_map('sanitize_text_field', $_POST['offer_title'] ?? []));
	update_post_meta($post_id, '_offer_description', array_map('sanitize_textarea_field', $_POST['offer_description'] ?? []));
	update_post_meta($post_id, '_offer_button', array_map('sanitize_text_field', $_POST['offer_button'] ?? []));
	update_post_meta($post_id, '_offer_button_links', array_map('esc_url_raw', $_POST['offer_button_links'] ?? []));
}
add_action('save_post', 'save_offer_meta_box');


// Contact Us Section metabox

function contact_section_meta_box()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'home') {

		add_meta_box(
			'contact_section_meta',
			'Contact Section',
			'contact_section_meta_box_callback',
			['post', 'page'],
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'contact_section_meta_box');

function contact_section_meta_box_callback($post)
{
	wp_nonce_field('contact_section_nonce_action', 'contact_section_nonce');

	$labels     = get_post_meta($post->ID, '_contact_labels', true) ?: '';
	$headings   = get_post_meta($post->ID, '_contact_headings', true) ?: '';
	$paragraphs = get_post_meta($post->ID, '_contact_paragraphs', true) ?: '';
	$images     = get_post_meta($post->ID, '_contact_images', true) ?: '';
	$buttons    = get_post_meta($post->ID, '_contact_buttons', true) ?: '';
	$button_links = get_post_meta($post->ID, '_contact_button_links', true) ?: '';
	$image_orintation = get_post_meta($post->ID, '_contact_img_orintation', true) ?: '';

?>
	<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
		<h4>Contact Information </h4>
		<p><label>Label:</label><br>
			<input type="text" name="contact_labels" value="<?php echo esc_attr($labels ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Heading:</label><br>
			<input type="text" name="contact_headings" value="<?php echo esc_attr($headings ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Paragraph:</label><br>
			<textarea name="contact_paragraphs" rows="4" style="width:100%;"><?php echo esc_textarea($paragraphs ?? ''); ?></textarea>
		</p>

		<p><label>Image URL:</label><br>
			<input type="text" name="contact_images" id="contact_image" value="<?php echo esc_attr($images ?? ''); ?>" style="width:80%;">
			<button class="button upload-image" data-target="contact_image">Upload</button>
		</p>
		<p><label>Button Label:</label><br>
			<input type="text" name="contact_buttons" value="<?php echo esc_attr($buttons ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Button Link:</label><br>
			<input type="text" name="contact_button_links" value="<?php echo esc_attr($button_links ?? ''); ?>" style="width:100%;">
		</p>

		<p>
			<label>Image Orintation</label><br>
			<select name="contact_img_orintation" id="contact_img_orintation">
				<option value="normal" <?php selected($image_orintation, 'top'); ?>>Top</option>
				<option value="right" <?php selected($image_orintation, 'right'); ?>>Right</option>
				<option value="bottom" <?php selected($image_orintation, 'bottom'); ?>>Bottom</option>
				<option value="left" <?php selected($image_orintation, 'left'); ?>>Left</option>
			</select>

		</p>
	</div>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.upload-image').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const inputId = this.dataset.target;
					const input = document.getElementById(inputId);
					const uploader = wp.media({
						title: 'Select Image',
						button: {
							text: 'Use this image'
						},
						multiple: false
					}).on('select', function() {
						const attachment = uploader.state().get('selection').first().toJSON();
						input.value = attachment.url;
					}).open();
				});
			});
		});
	</script>
<?php
}

function save_contact_section_meta_box($post_id)
{
	if (
		!isset($_POST['contact_section_nonce']) ||
		!wp_verify_nonce($_POST['contact_section_nonce'], 'contact_section_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_contact_labels', sanitize_text_field($_POST['contact_labels']));
	update_post_meta($post_id, '_contact_headings', sanitize_text_field($_POST['contact_headings']));
	update_post_meta($post_id, '_contact_paragraphs', sanitize_textarea_field($_POST['contact_paragraphs']));
	update_post_meta($post_id, '_contact_images', esc_url_raw($_POST['contact_images']));
	update_post_meta($post_id, '_contact_buttons', sanitize_text_field($_POST['contact_buttons']));
	update_post_meta($post_id, '_contact_button_links', esc_url_raw($_POST['contact_button_links']));
	update_post_meta($post_id, '_contact_img_orintation', sanitize_text_field($_POST['contact_img_orintation']));
}
add_action('save_post', 'save_contact_section_meta_box');


// Testimonial Header metabox
function testimonial_section_meta_box()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'home') {

		add_meta_box(
			'Testimonial_section_meta',
			'Testimonial Section',
			'testimonial_section_meta_box_callback',
			['post', 'page'],
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'testimonial_section_meta_box');

function testimonial_section_meta_box_callback($post)
{
	$testimonial_main_heading = get_post_meta($post->ID, '_testimonial_main_heading', true);
	$testimonial_main_paragraph = get_post_meta($post->ID, '_testimonial_main_paragraph', true);

	wp_nonce_field('testimonial_section_nonce_action', 'testimonial_section_nonce');
?>
	<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
		<h4>Testimonial Heading</h4>
		<p><label>Heading:</label><br>
			<input type="text" name="testimonial_main_heading" value="<?php echo esc_attr($testimonial_main_heading); ?>" style="width:100%;">
		</p>
		<p><label>Paragraph:</label><br>
			<textarea name="testimonial_main_paragraph" rows="4" style="width:100%;"><?php echo esc_textarea($testimonial_main_paragraph); ?></textarea>
		</p>
	</div>
	<?php
}

function save_testimonial_section_meta_box($post_id)
{
	if (
		!isset($_POST['testimonial_section_nonce']) ||
		!wp_verify_nonce($_POST['testimonial_section_nonce'], 'testimonial_section_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_testimonial_main_heading', sanitize_text_field($_POST['testimonial_main_heading'] ?? ''));
	update_post_meta($post_id, '_testimonial_main_paragraph', sanitize_textarea_field($_POST['testimonial_main_paragraph'] ?? ''));
}
add_action('save_post', 'save_testimonial_section_meta_box');



// Testimonial Content meta_box
function testimonial_card_meta_box()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'home') {

		add_meta_box(
			'testimonial_card_meta',
			'Testimonial Card Section',
			'testimonial_card_meta_box_callback',
			['post', 'page'],
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'testimonial_card_meta_box');

function testimonial_card_meta_box_callback($post)
{
	wp_nonce_field('testimonial_card_nonce_action', 'testimonial_card_nonce');
	$testimonial_card_title   = get_post_meta($post->ID, '_testimonial_card_title', true) ?: [];
	$testimonial_card_description = get_post_meta($post->ID, '_testimonial_card_description', true) ?: [];
	$testimonial_author_name = get_post_meta($post->ID, '_testimonial_card_author_name', true) ?: [];
	$testimonial_author_position = get_post_meta($post->ID, '_testimonial_card_author_position', true) ?: [];


	for ($i = 0; $i < 4; $i++) {
	?>
		<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
			<h4>Testimonial Card <?php echo $i + 1; ?></h4>


			<p><label>Title:</label><br>
				<input type="text" name="testimonial_card_title[]" value="<?php echo esc_attr($testimonial_card_title[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p><label>Description:</label><br>
				<textarea name="testimonial_card_description[]" rows="4" style="width:100%;"><?php echo esc_textarea($testimonial_card_description[$i] ?? ''); ?></textarea>
			</p>

			<p><label>Author Name:</label><br>
				<textarea name="testimonial_author_name[]" rows="4" style="width:100%;"><?php echo esc_textarea($testimonial_author_name[$i] ?? ''); ?></textarea>
			</p>
			<p><label>Author Position:</label><br>
				<textarea name="testimonial_author_position[]" rows="4" style="width:100%;"><?php echo esc_textarea($testimonial_author_position[$i] ?? ''); ?></textarea>
			</p>
		</div>
	<?php
	}
}

function save_testimonial_card_meta_box($post_id)
{
	if (
		!isset($_POST['testimonial_card_nonce']) ||
		!wp_verify_nonce($_POST['testimonial_card_nonce'], 'testimonial_card_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_testimonial_card_title', array_map('sanitize_text_field', $_POST['testimonial_card_title'] ?? []));
	update_post_meta($post_id, '_testimonial_card_description', array_map('sanitize_textarea_field', $_POST['testimonial_card_description'] ?? []));
	update_post_meta($post_id, '_testimonial_card_author_name', array_map('sanitize_textarea_field', $_POST['testimonial_author_name'] ?? []));
	update_post_meta($post_id, '_testimonial_card_author_position', array_map('sanitize_textarea_field', $_POST['testimonial_author_position'] ?? []));
}


add_action('save_post', 'save_testimonial_card_meta_box');


// Get In Touch Section Metabox
function get_in_touch_section_meta_box()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'home') {

		add_meta_box(
			'get_in_touch_section_meta',
			'Get_in_touch Section',
			'get_in_touch_section_meta_box_callback',
			['post', 'page'],
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'get_in_touch_section_meta_box');

function get_in_touch_section_meta_box_callback($post)
{
	wp_nonce_field('get_in_touch_section_nonce_action', 'get_in_touch_section_nonce');

	$headings   = get_post_meta($post->ID, '_get_in_touch_headings', true) ?: '';
	$paragraphs = get_post_meta($post->ID, '_get_in_touch_paragraphs', true) ?: '';
	$images     = get_post_meta($post->ID, '_get_in_touch_images', true) ?: '';
	$buttons    = get_post_meta($post->ID, '_get_in_touch_buttons', true) ?: '';
	$button_links = get_post_meta($post->ID, '_get_in_touch_button_links', true) ?: '';
	?>
	<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
		<h4>Get_in_touch Section Information </h4>


		<p><label>Heading:</label><br>
			<input type="text" name="get_in_touch_headings" value="<?php echo esc_attr($headings ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Paragraph:</label><br>
			<textarea name="get_in_touch_paragraphs" rows="4" style="width:100%;"><?php echo esc_textarea($paragraphs ?? ''); ?></textarea>
		</p>

		<p><label>Image URL:</label><br>
			<input type="text" name="get_in_touch_images" id="get_in_touch_image" value="<?php echo esc_attr($images ?? ''); ?>" style="width:80%;">
			<button class="button upload-image" data-target="get_in_touch_image">Upload</button>
		</p>
		<p><label>Button Label:</label><br>
			<input type="text" name="get_in_touch_buttons" value="<?php echo esc_attr($buttons ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Button Link:</label><br>
			<input type="text" name="get_in_touch_button_links" value="<?php echo esc_attr($button_links ?? ''); ?>" style="width:100%;">
		</p>
	</div>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.upload-image').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const inputId = this.dataset.target;
					const input = document.getElementById(inputId);
					const uploader = wp.media({
						title: 'Select Image',
						button: {
							text: 'Use this image'
						},
						multiple: false
					}).on('select', function() {
						const attachment = uploader.state().get('selection').first().toJSON();
						input.value = attachment.url;
					}).open();
				});
			});
		});
	</script>
<?php
}




function save_get_in_touch_section_meta_box($post_id)
{
	if (
		!isset($_POST['get_in_touch_section_nonce']) ||
		!wp_verify_nonce($_POST['get_in_touch_section_nonce'], 'get_in_touch_section_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_get_in_touch_headings', sanitize_text_field($_POST['get_in_touch_headings']));
	update_post_meta($post_id, '_get_in_touch_paragraphs', sanitize_textarea_field($_POST['get_in_touch_paragraphs']));
	update_post_meta($post_id, '_get_in_touch_images', esc_url_raw($_POST['get_in_touch_images']));
	update_post_meta($post_id, '_get_in_touch_buttons', sanitize_text_field($_POST['get_in_touch_buttons']));
	update_post_meta($post_id, '_get_in_touch_button_links', esc_url_raw($_POST['get_in_touch_button_links']));
}


add_action('save_post', 'save_get_in_touch_section_meta_box');


// %%%%%%%%%%%%%%%%%%%%%%%%%%%% Aboutus Section %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

// Aboutus hero_section metaboxes
function about_krisli_meta_box()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'about') {

		add_meta_box(
			'about_krisli_meta',
			'About Us Hero Section',
			'about_krisli_meta_box_callback',
			['post', 'page'],
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'about_krisli_meta_box');

function about_krisli_meta_box_callback($post)
{
	wp_nonce_field('about_krisli_nonce_action', 'about_krisli_nonce');

	$headings   = get_post_meta($post->ID, '_about_krisli_headings', true) ?: '';
	$paragraphs = get_post_meta($post->ID, '_about_krisli_paragraphs', true) ?: '';
	$images     = get_post_meta($post->ID, '_about_krisli_images', true) ?: '';
	$buttons    = get_post_meta($post->ID, '_about_krisli_buttons', true) ?: '';
	$button_links = get_post_meta($post->ID, '_about_krisli_button_links', true) ?: '';
	$image_orintation = get_post_meta($post->ID, '_about_krisli_img_orintation', true) ?: '';

?>
	<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
		<h4>About Us Hero Information </h4>

		<p><label>Heading:</label><br>
			<input type="text" name="about_krisli_headings" value="<?php echo esc_attr($headings ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Paragraph:</label><br>
			<textarea name="about_krisli_paragraphs" rows="4" style="width:100%;"><?php echo esc_textarea($paragraphs ?? ''); ?></textarea>
		</p>

		<p><label>Image URL:</label><br>
			<input type="text" name="about_krisli_images" id="about_krisli_image" value="<?php echo esc_attr($images ?? ''); ?>" style="width:80%;">
			<button class="button upload-image" data-target="about_krisli_image">Upload</button>
		</p>
		<p><label>Button Label:</label><br>
			<input type="text" name="about_krisli_buttons" value="<?php echo esc_attr($buttons ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Button Link:</label><br>
			<input type="text" name="about_krisli_button_links" value="<?php echo esc_attr($button_links ?? ''); ?>" style="width:100%;">
		</p>

		<p>
			<label>Image Orintation</label><br>
			<select name="about_krisli_img_orintation" id="about_krisli_img_orintation">
				<option value="normal" <?php selected($image_orintation, 'top'); ?>>Top</option>
				<option value="right" <?php selected($image_orintation, 'right'); ?>>Right</option>
				<option value="bottom" <?php selected($image_orintation, 'bottom'); ?>>Bottom</option>
				<option value="left" <?php selected($image_orintation, 'left'); ?>>Left</option>
			</select>

		</p>
	</div>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.upload-image').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const inputId = this.dataset.target;
					const input = document.getElementById(inputId);
					const uploader = wp.media({
						title: 'Select Image',
						button: {
							text: 'Use this image'
						},
						multiple: false
					}).on('select', function() {
						const attachment = uploader.state().get('selection').first().toJSON();
						input.value = attachment.url;
					}).open();
				});
			});
		});
	</script>
<?php
}

function save_about_krisli_meta_box($post_id)
{
	if (
		!isset($_POST['about_krisli_nonce']) ||
		!wp_verify_nonce($_POST['about_krisli_nonce'], 'about_krisli_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_about_krisli_headings', sanitize_text_field($_POST['about_krisli_headings']));
	update_post_meta($post_id, '_about_krisli_paragraphs', sanitize_textarea_field($_POST['about_krisli_paragraphs']));
	update_post_meta($post_id, '_about_krisli_images', esc_url_raw($_POST['about_krisli_images']));
	update_post_meta($post_id, '_about_krisli_buttons', sanitize_text_field($_POST['about_krisli_buttons']));
	update_post_meta($post_id, '_about_krisli_button_links', esc_url_raw($_POST['about_krisli_button_links']));
	update_post_meta($post_id, '_about_krisli_img_orintation', sanitize_text_field($_POST['about_krisli_img_orintation']));
}

add_action('save_post', 'save_about_krisli_meta_box');


// aboutus  feature heading metabox
function about_feature_meta_box()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'about') {

		add_meta_box(
			'about_feature_meta',              // ID
			'About Feature Meta Data Section',
			'about_feature_meta_box_callback',
			['post', 'page'],                    // Screen(s)
			'advanced',                            // Context
			'high'                               // Priority
		);
	}
}
add_action('add_meta_boxes', 'about_feature_meta_box');

function about_feature_meta_box_callback($post)
{
	wp_nonce_field('about_feature_nonce_action', 'about_feature_nonce');

	$feature_title   = get_post_meta($post->ID, '_about_feature_headings', true) ?: '';
	$feature_description = get_post_meta($post->ID, '_about_feature_paragraphs', true) ?: '';
	$feature_image     = get_post_meta($post->ID, '_about_feature_images', true) ?: '';
	$satisfied_client   = get_post_meta($post->ID, '_about_feature_satisfied_client', true) ?: '';

?>
	<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
		<h4>Aboutus Feature metaData </h4>

		<p><label>Heading:</label><br>
			<input type="text" name="about_feature_headings" value="<?php echo esc_attr($feature_title ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Paragraph:</label><br>
			<textarea name="about_feature_paragraphs" rows="4" style="width:100%;"><?php echo esc_textarea($feature_description ?? ''); ?></textarea>
		</p>

		<p><label>Total Clients Satisfied:</label><br>
			<input type="text" name="about_feature_satisfied_client" value="<?php echo esc_attr($satisfied_client ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Image URL:</label><br>
			<input type="text" name="about_feature_images" id="about_feature_image" value="<?php echo esc_attr($feature_image ?? ''); ?>" style="width:80%;">
			<button class="button upload-image" data-target="about_feature_image">Upload</button>
		</p>


	</div>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.upload-image').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const inputId = this.dataset.target;
					const input = document.getElementById(inputId);
					const uploader = wp.media({
						title: 'Select Image',
						button: {
							text: 'Use this image'
						},
						multiple: false
					}).on('select', function() {
						const attachment = uploader.state().get('selection').first().toJSON();
						input.value = attachment.url;
					}).open();
				});
			});
		});
	</script>
	<?php
}

function save_about_feature_meta_box($post_id)
{
	if (
		!isset($_POST['about_feature_nonce']) ||
		!wp_verify_nonce($_POST['about_feature_nonce'], 'about_feature_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_about_feature_headings', sanitize_text_field($_POST['about_feature_headings']));
	update_post_meta($post_id, '_about_feature_paragraphs', sanitize_textarea_field($_POST['about_feature_paragraphs']));
	update_post_meta($post_id, '_about_feature_images', esc_url_raw($_POST['about_feature_images']));
	update_post_meta($post_id, '_about_feature_satisfied_client', sanitize_text_field($_POST['about_feature_satisfied_client']));
}
add_action('save_post', 'save_about_feature_meta_box');

// Aboutus Feature Content  meta_box
function about_feature_item_meta_box()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'about') {

		add_meta_box(
			'about_feature_item_meta',
			'aboutus Feature Item Content Section',
			'about_feature_item_meta_box_callback',
			['post', 'page'],
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'about_feature_item_meta_box');

function about_feature_item_meta_box_callback($post)
{
	wp_nonce_field('about_feature_item_nonce_action', 'about_feature_item_nonce');

	$about_feature_item_title   = get_post_meta($post->ID, '_about_feature_item_title', true) ?: [];
	$about_feature_item_description = get_post_meta($post->ID, '_about_feature_item_description', true) ?: [];

	for ($i = 0; $i < 3; $i++) {
	?>
		<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
			<h4>About_feature_items <?php echo $i + 1; ?></h4>

			<p><label>Title:</label><br>
				<input type="text" name="about_feature_item_title[]" value="<?php echo esc_attr($about_feature_item_title[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p><label>Description:</label><br>
				<textarea name="about_feature_item_description[]" rows="4" style="width:100%;"><?php echo esc_textarea($about_feature_item_description[$i] ?? ''); ?></textarea>
			</p>

		</div>
	<?php
	}
}

function save_about_feature_item_meta_box($post_id)
{
	if (
		!isset($_POST['about_feature_item_nonce']) ||
		!wp_verify_nonce($_POST['about_feature_item_nonce'], 'about_feature_item_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_about_feature_item_title', array_map('sanitize_text_field', $_POST['about_feature_item_title'] ?? []));
	update_post_meta($post_id, '_about_feature_item_description', array_map('sanitize_textarea_field', $_POST['about_feature_item_description'] ?? []));
}
add_action('save_post', 'save_about_feature_item_meta_box');

// Aboutus Services Section Metabox
function aboutus_service_meta_box()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'about') {

		add_meta_box(
			'aboutus_service_meta',
			'About us Service Section',
			'aboutus_service_meta_box_callback',
			['post', 'page'],
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'aboutus_service_meta_box');

function aboutus_service_meta_box_callback($post)
{
	wp_nonce_field('aboutus_service_nonce_action', 'aboutus_service_nonce');
	$headings   = get_post_meta($post->ID, '_aboutus_service_headings', true) ?: [];
	$paragraphs = get_post_meta($post->ID, '_aboutus_service_paragraphs', true) ?: [];
	$images     = get_post_meta($post->ID, '_aboutus_service_images', true) ?: [];
	$buttons    = get_post_meta($post->ID, '_aboutus_service_buttons', true) ?: [];
	$button_links = get_post_meta($post->ID, '_aboutus_service_button_links', true) ?: [];
	$image_orintation = get_post_meta($post->ID, '_aboutus_service_img_orintation', true) ?: [];


	for ($i = 0; $i < 1; $i++) {
	?>
		<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
			<h4>About Us Services <?php echo $i + 1; ?></h4>


			<p><label>Heading:</label><br>
				<input type="text" name="aboutus_service_headings[]" value="<?php echo esc_attr($headings[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p><label>Paragraph:</label><br>
				<textarea name="aboutus_service_paragraphs[]" rows="4" style="width:100%;"><?php echo esc_textarea($paragraphs[$i] ?? ''); ?></textarea>
			</p>

			<p><label>Image URL:</label><br>
				<input type="text" name="aboutus_service_images[]" id="aboutus_service_image_<?php echo $i; ?>" value="<?php echo esc_attr($images[$i] ?? ''); ?>" style="width:80%;">
				<button class="button upload-image" data-target="aboutus_service_image_<?php echo $i; ?>">Upload</button>
			</p>
			<p><label>Button Label:</label><br>
				<input type="text" name="aboutus_service_buttons[]" value="<?php echo esc_attr($buttons[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p><label>Button Link:</label><br>
				<input type="text" name="aboutus_service_button_links[]" value="<?php echo esc_attr($button_links[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p>
				<label>Image Orintation</label><br>
				<select name="aboutus_service_img_orintation[]" id="aboutus_service_img_orintation<?php echo $i; ?>">
					<option value="normal" <?php selected($image_orintation[$i], 'top'); ?>>Top</option>
					<option value="right" <?php selected($image_orintation[$i], 'right'); ?>>Right</option>
					<option value="bottom" <?php selected($image_orintation[$i], 'bottom'); ?>>Bottom</option>
					<option value="left" <?php selected($image_orintation[$i], 'left'); ?>>Left</option>
				</select>

			</p>

		</div>
	<?php
	}

	?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.upload-image').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const inputId = this.dataset.target;
					const input = document.getElementById(inputId);
					const uploader = wp.media({
						title: 'Select Image',
						button: {
							text: 'Use this image'
						},
						multiple: false
					}).on('select', function() {
						const attachment = uploader.state().get('selection').first().toJSON();
						input.value = attachment.url;
					}).open();
				});
			});
		});
	</script>
<?php
}

function save_aboutus_service_meta_box($post_id)
{
	if (
		!isset($_POST['aboutus_service_nonce']) ||
		!wp_verify_nonce($_POST['aboutus_service_nonce'], 'aboutus_service_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_aboutus_service_headings', array_map('sanitize_text_field', $_POST['aboutus_service_headings'] ?? []));
	update_post_meta($post_id, '_aboutus_service_paragraphs', array_map('sanitize_textarea_field', $_POST['aboutus_service_paragraphs'] ?? []));
	update_post_meta($post_id, '_aboutus_service_images', array_map('esc_url_raw', $_POST['aboutus_service_images'] ?? []));
	update_post_meta($post_id, '_aboutus_service_buttons', array_map('sanitize_text_field', $_POST['aboutus_service_buttons'] ?? []));
	update_post_meta($post_id, '_aboutus_service_button_links', array_map('esc_url_raw', $_POST['aboutus_service_button_links'] ?? []));
	update_post_meta($post_id, '_aboutus_service_img_orintation', array_map('sanitize_text_field', $_POST['aboutus_service_img_orintation'] ?? []));
}
add_action('save_post', 'save_aboutus_service_meta_box');


// Aboutus Need Section metabox
function aboutus_mission_meta_box()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'about') {

		add_meta_box(
			'aboutus_mission_meta',
			'About Us need Section',
			'aboutus_mission_meta_box_callback',
			'page',
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'aboutus_mission_meta_box');

function aboutus_mission_meta_box_callback($post)
{
	wp_nonce_field('aboutus_mission_nonce_action', 'aboutus_mission_nonce');

	$headings        = get_post_meta($post->ID, '_aboutus_mission_headings', true) ?: [];
	$paragraphs      = get_post_meta($post->ID, '_aboutus_mission_paragraphs', true) ?: [];
	$images          = get_post_meta($post->ID, '_aboutus_mission_images', true) ?: [];
	$buttons         = get_post_meta($post->ID, '_aboutus_mission_buttons', true) ?: [];
	$button_links    = get_post_meta($post->ID, '_aboutus_mission_button_links', true) ?: [];
	$image_orintation = get_post_meta($post->ID, '_aboutus_mission_img_orintation', true) ?: [];
	$lists           = get_post_meta($post->ID, '_aboutus_mission_lists', true) ?: [];

?>
	<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
		<h4>Mission/Need Section</h4>

		<p><label>Heading:</label><br>
			<input type="text" name="aboutus_mission_headings[]" value="<?php echo esc_attr($headings[0] ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Paragraph:</label><br>
			<textarea name="aboutus_mission_paragraphs[]" rows="4" style="width:100%;"><?php echo esc_textarea($paragraphs[0] ?? ''); ?></textarea>
		</p>

		<p><label>Image URL:</label><br>
			<input type="text" name="aboutus_mission_images[]" id="aboutus_mission_image_0" value="<?php echo esc_attr($images[0] ?? ''); ?>" style="width:80%;">
			<button class="button upload-image" data-target="aboutus_mission_image_0">Upload</button>
		</p>

		<p><label>Button Label:</label><br>
			<input type="text" name="aboutus_mission_buttons[]" value="<?php echo esc_attr($buttons[0] ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Button Link:</label><br>
			<input type="text" name="aboutus_mission_button_links[]" value="<?php echo esc_attr($button_links[0] ?? ''); ?>" style="width:100%;">
		</p>

		<p>
			<label>Image Orientation:</label><br>
			<select name="aboutus_mission_img_orintation[]" id="aboutus_mission_img_orintation_0">
				<option value="top" <?php selected($image_orintation[0] ?? '', 'top'); ?>>Top</option>
				<option value="right" <?php selected($image_orintation[0] ?? '', 'right'); ?>>Right</option>
				<option value="bottom" <?php selected($image_orintation[0] ?? '', 'bottom'); ?>>Bottom</option>
				<option value="left" <?php selected($image_orintation[0] ?? '', 'left'); ?>>Left</option>
			</select>
		</p>

		<div id="aboutus-list-container">
			<label>List Items:</label>
			<?php
			if (!empty($lists) && is_array($lists)) {
				foreach ($lists as $list) {
					$title = $list['title'] ?? '';
					$desc  = $list['description'] ?? '';
			?>
					<div class="list-item-group">
						<p><input type="text" name="aboutus_mission_lists_title[]" value="<?php echo esc_attr($title); ?>" placeholder="Title" style="width:100%;"></p>
						<p><textarea name="aboutus_mission_lists_description[]" rows="2" style="width:100%;" placeholder="Description"><?php echo esc_textarea($desc); ?></textarea></p>
					</div>
			<?php }
			}
			?>

			<div class="list-item-group">
				<p><input type="text" name="aboutus_mission_lists_title[]" placeholder="Title" style="width:100%;"></p>
				<p><textarea name="aboutus_mission_lists_description[]" rows="2" style="width:100%;" placeholder="Description"></textarea></p>
			</div>
		</div>
		<p><button type="button" class="button add-list-item">+ Add List Item</button></p>


	</div>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.upload-image').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const inputId = this.dataset.target;
					const input = document.getElementById(inputId);
					const uploader = wp.media({
						title: 'Select Image',
						button: {
							text: 'Use this image'
						},
						multiple: false
					}).on('select', function() {
						const attachment = uploader.state().get('selection').first().toJSON();
						input.value = attachment.url;
					}).open();
				});
			});

			const listContainer = document.getElementById('aboutus-list-container');
			document.querySelector('.add-list-item').addEventListener('click', function() {
				const group = document.createElement('div');
				group.className = 'list-item-group';

				const title = document.createElement('input');
				title.type = 'text';
				title.name = 'aboutus_mission_lists_title[]';
				title.placeholder = 'Title';
				title.style.width = '100%';

				const desc = document.createElement('textarea');
				desc.name = 'aboutus_mission_lists_description[]';
				desc.rows = 2;
				desc.placeholder = 'Description';
				desc.style.width = '100%';

				group.appendChild(document.createElement('p')).appendChild(title);
				group.appendChild(document.createElement('p')).appendChild(desc);
				listContainer.appendChild(group);
			});
		});
	</script>
<?php
}

function save_aboutus_mission_meta_box($post_id)
{
	if (
		!isset($_POST['aboutus_mission_nonce']) ||
		!wp_verify_nonce($_POST['aboutus_mission_nonce'], 'aboutus_mission_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;
	$titles = $_POST['aboutus_mission_lists_title'] ?? [];
	$descs  = $_POST['aboutus_mission_lists_description'] ?? [];

	$structured_lists = [];

	for ($i = 0; $i < count($titles); $i++) {
		$title = sanitize_text_field($titles[$i]);
		$desc  = sanitize_textarea_field($descs[$i]);

		if ($title || $desc) {
			$structured_lists[] = [
				'title' => $title,
				'description' => $desc
			];
		}
	}

	update_post_meta($post_id, '_aboutus_mission_lists', $structured_lists);

	update_post_meta($post_id, '_aboutus_mission_headings', array_map('sanitize_text_field', $_POST['aboutus_mission_headings'] ?? []));
	update_post_meta($post_id, '_aboutus_mission_paragraphs', array_map('sanitize_textarea_field', $_POST['aboutus_mission_paragraphs'] ?? []));
	update_post_meta($post_id, '_aboutus_mission_images', array_map('esc_url_raw', $_POST['aboutus_mission_images'] ?? []));
	update_post_meta($post_id, '_aboutus_mission_buttons', array_map('sanitize_text_field', $_POST['aboutus_mission_buttons'] ?? []));
	update_post_meta($post_id, '_aboutus_mission_button_links', array_map('esc_url_raw', $_POST['aboutus_mission_button_links'] ?? []));
	update_post_meta($post_id, '_aboutus_mission_img_orintation', array_map('sanitize_text_field', $_POST['aboutus_mission_img_orintation'] ?? []));
	update_post_meta($post_id, '_aboutus_mission_lists', $structured_lists);
}
add_action('save_post', 'save_aboutus_mission_meta_box');

//our team section metabox
function team_header_meta_box()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'about') {

		add_meta_box(
			'team_header_meta',
			'Team Section Heading',
			'team_header_meta_box_callback',
			['post', 'page'],
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'team_header_meta_box');

function team_header_meta_box_callback($post)
{
	$team_heading = get_post_meta($post->ID, '_team_heading', true);
	$team_paragraph = get_post_meta($post->ID, '_team_paragraph', true);

	wp_nonce_field('team_header_section_nonce_action', 'team_header_section_nonce');
?>
	<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
		<h4>Offer Heading</h4>
		<p><label>Heading:</label><br>
			<input type="text" name="team_heading" value="<?php echo esc_attr($team_heading); ?>" style="width:100%;">
		</p>
		<p><label>Paragraph:</label><br>
			<textarea name="team_paragraph" rows="4" style="width:100%;"><?php echo esc_textarea($team_paragraph); ?></textarea>
		</p>
	</div>
	<?php
}

function save_team_header_meta_box($post_id)
{
	if (
		!isset($_POST['team_header_section_nonce']) ||
		!wp_verify_nonce($_POST['team_header_section_nonce'], 'team_header_section_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_team_heading', sanitize_text_field($_POST['team_heading'] ?? ''));
	update_post_meta($post_id, '_team_paragraph', sanitize_textarea_field($_POST['team_paragraph'] ?? ''));
}
add_action('save_post', 'save_team_header_meta_box');


// Team  Content meta_box

function team_meta_box()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'about') {

		add_meta_box(
			'team_meta',
			'Team Section',
			'team_meta_box_callback',
			['post', 'page'],
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'team_meta_box');

function team_meta_box_callback($post)
{
	wp_nonce_field('team_nonce_action', 'team_nonce');

	$team_icon     = get_post_meta($post->ID, '_team_icon', true) ?: [];
	$team_title   = get_post_meta($post->ID, '_team_title', true) ?: [];
	$team_description = get_post_meta($post->ID, '_team_description', true) ?: [];


	for ($i = 0; $i < 2; $i++) {
	?>
		<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
			<h4>Teams <?php echo $i + 1; ?></h4>


			<p>
				<label>Add Icon URL:</label><br>
				<input type="text" name="team_image[]" id="team_image_<?php echo $i; ?>" value="<?php echo esc_attr($team_icon[$i] ?? ''); ?>" style="width:80%;">
				<button class="button upload-icon" data-target="team_image_<?php echo $i; ?>">Upload</button>

			</p>
			<p><label>Title:</label><br>
				<input type="text" name="team_title[]" value="<?php echo esc_attr($team_title[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p><label>Description:</label><br>
				<textarea name="team_description[]" rows="4" style="width:100%;"><?php echo esc_textarea($team_description[$i] ?? ''); ?></textarea>
			</p>

			<p><label>Button Label:</label><br>
				<input type="text" name="team_button[]" value="<?php echo esc_attr($team_button[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p>
				<label>Button Link:</label><br>
				<input type="text" name="team_button_links[]" value="<?php echo esc_attr($team_button_links[$i] ?? ''); ?>" style="width:100%;">
			</p>
		</div>
	<?php
	}

	?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.upload-icon').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const inputId = this.dataset.target;
					const input = document.getElementById(inputId);
					const uploader = wp.media({
						title: 'Select Image',
						button: {
							text: 'Use this image'
						},
						multiple: false
					}).on('select', function() {
						const attachment = uploader.state().get('selection').first().toJSON();
						input.value = attachment.url;
					}).open();
				});
			});
		});
	</script>
<?php
}

function save_team_meta_box($post_id)
{
	if (
		!isset($_POST['team_nonce']) ||
		!wp_verify_nonce($_POST['team_nonce'], 'team_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_team_icon', array_map('esc_url_raw', $_POST['team_image'] ?? []));
	update_post_meta($post_id, '_team_title', array_map('sanitize_text_field', $_POST['team_title'] ?? []));
	update_post_meta($post_id, '_team_description', array_map('sanitize_textarea_field', $_POST['team_description'] ?? []));
}
add_action('save_post', 'save_team_meta_box');




// %%%%%%%%%%%%%%%%%%%%%%%%%%%% Service PAGE %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

// Dynamic svg icon for services page
function get_tab_svgs()
{
	return [
		'<svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M3.34806 20.0022C3.00398 19.8687 2.88747 19.6115 2.88868 19.2518C2.89657 17.2611 2.89414 15.2703 2.88929 13.2796C2.88929 13.1195 2.92206 13.0333 3.08166 12.9551C3.27524 12.8599 3.45243 12.7137 3.60657 12.5596C5.84216 10.3323 8.0729 8.10013 10.3042 5.86797C10.3589 5.81277 10.4019 5.74605 10.4383 5.69995C12.592 7.85326 14.7208 9.9817 16.8563 12.1162C16.3641 12.5044 16.227 13.1346 15.9236 13.666C15.8592 13.7788 15.7676 13.8874 15.6644 13.9656C15.4314 14.1421 15.1826 14.2974 14.9417 14.4636C13.9192 15.1697 13.9137 16.5866 14.9308 17.3005C14.9465 17.3114 14.9623 17.3242 14.9793 17.3327C15.6329 17.659 16.0492 18.1794 16.2968 18.8636C16.4843 19.3823 16.8472 19.7541 17.3836 19.9367C17.4103 19.9458 17.4285 19.9791 17.4504 20.0016H12.7625C12.7595 19.9367 12.7534 19.8724 12.7534 19.8075C12.7534 18.2971 12.7583 16.7874 12.7516 15.277C12.7449 13.7545 11.4415 12.6651 9.95713 12.9314C8.85754 13.1286 8.0644 14.1015 8.06015 15.2776C8.05408 16.788 8.05833 18.2977 8.05954 19.8081C8.05954 19.8724 8.06986 19.9373 8.07532 20.0016H3.34806V20.0022Z" fill="#0066FF"/>
			<path d="M20.4994 10.0408C20.429 10.1324 20.3677 10.2325 20.2876 10.3137C19.8027 10.8045 19.3136 11.2909 18.8251 11.7786C18.5897 12.0133 18.4713 12.0139 18.2377 11.7804C15.7102 9.25407 13.1834 6.72773 10.6571 4.20017C10.6025 4.14557 10.5636 4.074 10.486 3.96724C10.4034 4.07703 10.3634 4.14618 10.3088 4.20077C7.80132 6.71013 5.29204 9.21768 2.78338 11.7258C2.50302 12.0061 2.40593 12.0055 2.12557 11.7258C1.63767 11.2381 1.14916 10.7505 0.661874 10.2622C0.446447 10.0462 0.44584 9.91887 0.661267 9.70354C1.31908 9.04481 1.9775 8.38668 2.63592 7.72917C2.67657 7.68853 2.72027 7.65092 2.76275 7.61149C2.89322 7.73159 3.0073 7.85654 3.14081 7.95663C4.01769 8.61293 5.25503 8.32906 5.75992 7.3537C5.95714 6.97278 6.12645 6.5779 6.32367 6.19698C6.38314 6.08234 6.47902 5.97558 6.5834 5.89794C6.86011 5.69232 7.15139 5.50732 7.43418 5.31018C8.3323 4.6836 8.49433 3.55781 7.81163 2.6977C7.79586 2.67769 7.78069 2.65706 7.7552 2.62309C7.79828 2.57639 7.83894 2.52847 7.88324 2.48358C8.6515 1.71446 9.42036 0.94594 10.1892 0.177422C10.4253 -0.0585324 10.5406 -0.059139 10.776 0.175602C13.9528 3.35037 17.129 6.52513 20.304 9.70172C20.3798 9.77814 20.4351 9.8752 20.5 9.96254V10.0408H20.4994Z" fill="#0066FF"/>
			<path d="M20.5006 16.0158C20.3641 16.1572 20.2312 16.3021 20.0892 16.438C20.0243 16.4999 19.9448 16.5514 19.8647 16.5921C19.2693 16.8947 18.8773 17.366 18.6613 17.9963C18.5964 18.1855 18.4902 18.3602 18.4022 18.541C18.3045 18.7411 18.1406 18.8497 17.921 18.8491C17.7013 18.8491 17.5386 18.7387 17.4415 18.5379C17.2443 18.1285 17.0544 17.7154 16.8462 17.3115C16.7874 17.1968 16.6915 17.0888 16.5871 17.0124C16.2782 16.7856 15.9554 16.5775 15.6399 16.3598C15.249 16.0904 15.2509 15.6786 15.6417 15.4062C15.7376 15.3395 15.8286 15.2625 15.9323 15.2103C16.5683 14.8919 16.9779 14.3854 17.2091 13.7194C17.2686 13.5489 17.3614 13.3894 17.4428 13.2268C17.5411 13.0297 17.6982 12.9199 17.9234 12.9199C18.1485 12.9199 18.3063 13.0303 18.4028 13.2287C18.6049 13.6429 18.7984 14.0615 19.0066 14.4727C19.0563 14.5716 19.1371 14.6662 19.2263 14.7311C19.5254 14.9495 19.8446 15.1412 20.1371 15.3674C20.2797 15.4778 20.3805 15.6416 20.5 15.7811V16.0152L20.5006 16.0158Z" fill="#0066FF"/>
			<path d="M9.20898 20.0006C9.21748 19.9175 9.23265 19.8344 9.23265 19.7513C9.23386 18.2682 9.23204 16.7852 9.23447 15.3027C9.23569 14.7156 9.55124 14.2655 10.0592 14.1114C10.7516 13.9009 11.4852 14.3498 11.5532 15.0716C11.5999 15.5666 11.5623 16.07 11.5623 16.602C11.396 16.602 11.2243 16.6008 11.0519 16.602C10.6696 16.605 10.4111 16.8397 10.4093 17.1843C10.4075 17.53 10.6666 17.7708 11.0453 17.7745C11.214 17.7763 11.3827 17.7745 11.592 17.7745V20.0006H9.20898Z" fill="#0066FF"/>
			<path d="M7.05717 3.83129C7.04807 4.04298 6.95583 4.21161 6.7756 4.33413C6.38844 4.59738 6.00006 4.85881 5.61897 5.13055C5.53401 5.19121 5.45937 5.28219 5.41264 5.375C5.18872 5.82204 4.97086 6.27272 4.76029 6.72643C4.6456 6.9733 4.47144 7.13283 4.19472 7.13404C3.918 7.13586 3.7408 6.97633 3.62611 6.73067C3.41129 6.27211 3.19222 5.81476 2.96648 5.36105C2.92279 5.27309 2.85118 5.18696 2.77108 5.13055C2.39969 4.8673 2.02042 4.61558 1.64539 4.35718C1.21515 4.06057 1.21757 3.60383 1.65025 3.306C1.98219 3.07733 2.31109 2.84319 2.64971 2.62483C2.82569 2.5114 2.94888 2.3725 3.03505 2.18022C3.22317 1.75926 3.43132 1.3474 3.62793 0.930085C3.74323 0.686246 3.918 0.530359 4.19957 0.533998C4.47144 0.537637 4.64135 0.688066 4.75362 0.926446C4.96965 1.38501 5.1875 1.84236 5.41446 2.29546C5.46119 2.38888 5.53826 2.47804 5.62321 2.53809C5.99945 2.80619 6.38237 3.06338 6.76468 3.32299C6.94369 3.4443 7.04443 3.60807 7.05717 3.83008V3.83129Z" fill="#0066FF"/>
			<path d="M13.8365 1.51893C13.9069 1.51468 13.9773 1.5068 14.0477 1.5068C14.7504 1.50619 15.4532 1.50498 16.1559 1.5068C16.6377 1.50801 16.8604 1.73244 16.861 2.21163C16.8616 2.98561 16.861 3.75898 16.861 4.482C15.8828 3.50301 14.8821 2.50217 13.8808 1.50073C13.8657 1.5068 13.8511 1.51286 13.8359 1.51893H13.8365Z" fill="#0066FF"/>
		</svg>',

		'<svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M15.7734 0C15.7674 0.0770865 15.7552 0.154173 15.7552 0.231259C15.754 1.31108 15.7546 2.3915 15.7546 3.47132V3.71593H5.57812V0C7.0365 0 8.49488 0 9.95326 0C9.8932 0.0807284 9.83375 0.161457 9.79371 0.214871C10.6952 1.11624 11.5924 2.01457 12.483 2.90501C12.6189 2.77329 12.7717 2.6258 12.9337 2.4698C12.8803 2.41396 12.833 2.36176 12.7833 2.31199C12.0668 1.59514 11.3504 0.8783 10.6345 0.16085C10.5854 0.111684 10.5447 0.0540212 10.4998 0C12.2579 0 14.0154 0 15.7734 0V0ZM10.182 2.49226C9.74457 2.05341 9.29747 1.60486 8.86433 1.17026C8.72601 1.30197 8.57253 1.44826 8.43482 1.57936C8.88253 2.02671 9.32659 2.47041 9.77248 2.91593C9.90715 2.77633 10.0552 2.62276 10.1814 2.49226H10.182Z" fill="white"/>
			<path d="M20.5012 8.63577C20.3732 8.9253 20.1887 9.14381 19.8405 9.15048C19.4862 9.15716 19.206 8.91801 19.1514 8.56293C19.1041 8.25398 19.3067 7.93288 19.613 7.83334C19.9358 7.72772 20.2803 7.87643 20.4332 8.1866C20.4557 8.23212 20.4787 8.27765 20.5012 8.32378V8.63637V8.63577Z" fill="white"/>
			<path d="M16.4425 3.72827V1.70581H19.8093V7.09519C18.8703 7.34223 18.4644 7.76226 18.4662 8.48335C18.468 9.19231 18.8757 9.61112 19.8118 9.85816V19.3173H11.6973C11.6973 17.0655 11.6973 14.8154 11.6973 12.5659C12.2238 12.4227 12.3658 12.2333 12.3658 11.6761C12.3658 10.9143 12.3658 10.1519 12.3658 9.39018V9.15346C12.4598 9.15346 12.5357 9.15346 12.6115 9.15346C13.6143 9.15346 14.6164 9.15346 15.6192 9.15346C16.1816 9.15346 16.4315 8.90338 16.4321 8.34557C16.4321 8.17137 16.4321 7.99716 16.4321 7.80232C16.8416 7.80232 17.2305 7.80232 17.6193 7.80232C18.2187 7.80232 18.465 7.55832 18.465 6.96469C18.465 6.14405 18.4656 5.32341 18.465 4.50278C18.465 3.99898 18.1993 3.73191 17.6946 3.72948C17.2845 3.72706 16.875 3.72948 16.4418 3.72948L16.4425 3.72827ZM12.7085 13.236C12.7043 13.304 12.6994 13.3489 12.6994 13.3944C12.6994 14.5604 12.6988 15.7258 12.7001 16.8919C12.7001 16.9823 12.7092 17.0746 12.7255 17.1638C12.8256 17.6979 13.3795 18.0645 13.9115 17.951C14.4211 17.8424 14.7299 17.4509 14.7323 16.8906C14.7366 16.0506 14.7335 15.2105 14.7335 14.3705C14.7335 13.2305 14.7329 12.0912 14.7341 10.9513C14.7341 10.6703 14.8555 10.5161 15.0684 10.5131C15.2862 10.5101 15.4136 10.6739 15.4148 10.961C15.4154 11.2409 15.4124 11.5213 15.416 11.8011C15.4184 12.0093 15.4014 12.2224 15.4415 12.4233C15.5458 12.9453 16.0172 13.2676 16.5553 13.2123C17.0503 13.1614 17.4398 12.7346 17.4452 12.212C17.4531 11.5085 17.4477 10.8051 17.4477 10.1022C17.4477 10.0196 17.4477 9.93707 17.4477 9.84906H16.7694C16.7694 9.931 16.7694 10.0008 16.7694 10.0712C16.7694 10.7353 16.7707 11.3999 16.7682 12.0639C16.7682 12.1544 16.7682 12.2509 16.7367 12.3328C16.6766 12.4906 16.5468 12.5598 16.3782 12.5392C16.2083 12.5186 16.0912 12.3771 16.0888 12.1774C16.0833 11.7216 16.0936 11.2658 16.0833 10.8099C16.0718 10.2746 15.6386 9.85149 15.1054 9.83814C14.5054 9.82296 14.0553 10.2733 14.0547 10.9004C14.0523 12.8609 14.0541 14.8208 14.0535 16.7814C14.0535 16.853 14.0577 16.9252 14.0492 16.9963C14.028 17.1692 13.8848 17.2925 13.7156 17.2919C13.5457 17.2912 13.4056 17.1662 13.3856 16.9932C13.3783 16.9289 13.3813 16.8633 13.3813 16.7978C13.3813 15.684 13.3813 14.5708 13.3813 13.457C13.3813 13.3871 13.3813 13.318 13.3813 13.2366H12.7073L12.7085 13.236ZM17.1079 13.9152C17.1079 14.5125 17.0904 15.0861 17.1128 15.6573C17.1334 16.1853 17.5654 16.592 18.0725 16.6096C18.6658 16.6302 19.1329 16.2011 19.1354 15.6051C19.142 13.9444 19.1378 12.2837 19.1378 10.623C19.1378 10.592 19.1323 10.561 19.1287 10.5252H18.465V10.7747C18.465 12.3444 18.465 13.914 18.465 15.4831C18.465 15.7714 18.3394 15.9365 18.1235 15.9359C17.9075 15.9346 17.7898 15.7756 17.7886 15.4794C17.7874 15.2057 17.7886 14.9325 17.7886 14.6588C17.7886 14.4129 17.7886 14.1671 17.7886 13.9146H17.1086L17.1079 13.9152Z" fill="white"/>
			<path d="M9.6406 19.324H1.5443C1.4715 19.0011 1.48181 18.9908 1.75602 18.8518C2.26681 18.592 2.54162 18.1726 2.52706 17.5972C2.5125 17.0303 2.21949 16.6315 1.7087 16.3923C1.64985 16.365 1.58797 16.3444 1.52246 16.3189V6.3292C1.97138 6.4773 2.41605 6.48155 2.87589 6.32495C2.87589 6.58777 2.87407 6.82753 2.87589 7.06729C2.87953 7.52313 3.14584 7.79384 3.60447 7.80113C3.96239 7.80659 4.32031 7.80234 4.67884 7.80234C4.7486 7.80234 4.81776 7.80234 4.90876 7.80234C4.90876 8.00629 4.90876 8.18717 4.90876 8.36805C4.90997 8.9034 5.1593 9.15287 5.69982 9.15348C6.71535 9.15469 7.73088 9.15348 8.7464 9.15348C8.81617 9.15348 8.88593 9.15348 8.97511 9.15348C8.97511 9.29187 8.97511 9.4072 8.97511 9.52313C8.97511 10.246 8.97511 10.969 8.97511 11.6919C8.97511 12.2363 9.11706 12.4251 9.64121 12.5696V19.3247L9.6406 19.324ZM8.29809 13.2372C8.29809 13.3386 8.29809 13.4157 8.29809 13.4934C8.29809 14.5938 8.29809 15.6943 8.29749 16.7948C8.29749 16.8725 8.29749 16.952 8.28111 17.0272C8.24531 17.196 8.13005 17.2858 7.96201 17.2888C7.79397 17.2919 7.67507 17.2039 7.63442 17.0375C7.6144 16.9568 7.61561 16.87 7.61561 16.7863C7.6144 14.8524 7.61561 12.918 7.61379 10.9841C7.61379 10.8609 7.61379 10.7341 7.58589 10.6157C7.46395 10.1028 7.01018 9.79263 6.47512 9.84422C5.98495 9.89157 5.59427 10.3237 5.58335 10.8445C5.57486 11.2743 5.58214 11.704 5.58032 12.1338C5.5791 12.3948 5.45292 12.5453 5.2412 12.5435C5.03009 12.5416 4.90936 12.3929 4.90876 12.1271C4.90754 11.4369 4.90876 10.7468 4.90876 10.0561C4.90876 9.98747 4.90876 9.91828 4.90876 9.85029H4.22689C4.22689 10.0518 4.22628 10.24 4.22689 10.4275C4.23053 11.0588 4.20687 11.6925 4.2463 12.3213C4.28634 12.9629 4.94576 13.369 5.55666 13.1717C5.99344 13.0303 6.25491 12.66 6.26158 12.1574C6.26765 11.721 6.25915 11.2846 6.26825 10.8488C6.2725 10.6442 6.41627 10.5125 6.6007 10.5119C6.78208 10.5119 6.92282 10.6497 6.93981 10.847C6.94466 10.9052 6.94163 10.9641 6.94163 11.023C6.94163 12.9768 6.94284 14.9301 6.93981 16.884C6.93981 17.1553 7.00351 17.3987 7.18004 17.6075C7.45971 17.9371 7.89164 18.0542 8.29324 17.9116C8.69969 17.7671 8.97086 17.3987 8.97208 16.9568C8.97693 15.752 8.9739 14.5471 8.97329 13.3429C8.97329 13.3113 8.96722 13.2797 8.96298 13.236H8.29749L8.29809 13.2372ZM2.53131 10.5186C2.53131 11.1007 2.53131 11.6658 2.53131 12.2309C2.53131 13.3507 2.52767 14.4712 2.53313 15.5911C2.53616 16.1684 2.98387 16.6108 3.54684 16.6114C4.11344 16.6121 4.55387 16.1732 4.56358 15.596C4.56722 15.3744 4.56418 15.1529 4.56418 14.9319C4.56418 14.5957 4.56418 14.2588 4.56418 13.9177H3.89141C3.89141 14.4294 3.89141 14.9295 3.89141 15.4297C3.89141 15.7708 3.77494 15.9407 3.54441 15.9371C3.31995 15.9335 3.20833 15.7696 3.20833 15.4388C3.20833 13.8758 3.20833 12.3128 3.20833 10.7504V10.518H2.53131V10.5186Z" fill="white"/>
			<path d="M4.91047 7.11203H3.5625V4.42554H17.7799V7.109H16.4355C16.4355 6.92083 16.4361 6.72903 16.4355 6.53661C16.4337 6.04071 16.1716 5.76878 15.6809 5.76878C12.3419 5.76696 9.0023 5.76757 5.66332 5.76878C5.16951 5.76878 4.91229 6.03464 4.91047 6.53358C4.90986 6.72114 4.91047 6.90809 4.91047 7.11203Z" fill="white"/>
			<path d="M15.7417 6.46167V8.47078H12.367C12.367 8.27593 12.3676 8.08352 12.367 7.89172C12.3639 7.39217 12.0921 7.12085 11.5923 7.12024C10.9741 7.11903 10.3559 7.11903 9.73775 7.12024C9.24637 7.12085 8.97702 7.39035 8.9752 7.88018C8.97459 8.06895 8.9752 8.25773 8.9752 8.46228H5.59375V6.46167H15.7417Z" fill="white"/>
			<path d="M9.66406 7.81421H11.6684V10.5031H9.66406V7.81421Z" fill="white"/>
			<path d="M10.9931 20.0003H10.3477V12.5618H10.9931V20.0003Z" fill="white"/>
			<path d="M3.35612 3.75424C2.93571 3.9072 2.84714 4.24165 2.8708 4.64589C2.88536 4.8984 2.86534 5.15333 2.87687 5.40644C2.88294 5.53816 2.83986 5.60978 2.71914 5.6553C2.20713 5.84954 1.7297 5.79005 1.31354 5.43375C0.897381 5.07746 0.75118 4.6113 0.882822 4.07958C1.01507 3.54665 1.36693 3.21099 1.89956 3.08778C2.49165 2.9506 3.10558 3.23527 3.35551 3.75424H3.35612Z" fill="white"/>
			<path d="M1.52148 2.46757V1.70581H4.89808V3.72705C4.65239 3.72705 4.41458 3.7313 4.17738 3.72281C4.13977 3.72159 4.09063 3.67303 4.07001 3.63419C3.49612 2.5659 2.74388 2.21264 1.5779 2.46757C1.56577 2.47 1.55242 2.46757 1.52148 2.46757Z" fill="white"/>
			<path d="M0.500071 17.6223C0.504925 17.246 0.816741 16.9486 1.1965 16.9565C1.56352 16.9643 1.86442 17.2763 1.85714 17.6411C1.84986 18.0071 1.54229 18.31 1.17709 18.3106C0.802788 18.3106 0.494612 17.998 0.500071 17.6217V17.6223Z" fill="white"/>
			<path d="M9.66211 11.1995H11.6737V11.8593H9.66211V11.1995Z" fill="white"/>
		</svg>',

		'<svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M7.3531 7.46499C6.2285 7.46499 5.14703 7.46499 4.06555 7.46499C3.73845 7.46499 3.41069 7.46968 3.0836 7.46165C2.84673 7.45629 2.67688 7.31775 2.66958 7.0835C2.653 6.59024 2.66428 6.0963 2.66428 5.58765H13.8891C13.8891 6.10233 13.905 6.60965 13.8824 7.11496C13.8718 7.35188 13.684 7.46365 13.4007 7.46365C12.1554 7.46499 10.9094 7.46365 9.66401 7.46365C9.52401 7.46365 9.38336 7.46365 9.2208 7.46365C9.24668 7.53192 9.25995 7.5781 9.28052 7.62094C9.54325 8.16975 9.80268 8.7199 10.0747 9.2647C10.1079 9.33096 10.2041 9.40659 10.2718 9.40726C12.1866 9.41395 14.1014 9.41195 16.0162 9.41061C16.0367 9.41061 16.0567 9.40124 16.0527 9.40191C15.8968 8.75538 15.7408 8.11018 15.5849 7.46566C15.5544 7.34051 15.5192 7.21535 15.4993 7.08885C15.4662 6.88205 15.5557 6.75957 15.7588 6.7589C16.8548 6.75421 17.9509 6.75354 19.047 6.7589C19.2692 6.76024 19.3475 6.90012 19.2878 7.15712C19.1425 7.77822 18.9952 8.39865 18.8453 9.01841C18.8154 9.14089 18.771 9.26069 18.7285 9.39789C18.7783 9.40258 18.8373 9.41195 18.8964 9.41195C19.2878 9.41328 19.6793 9.41195 20.0707 9.41262C20.437 9.41262 20.496 9.47151 20.496 9.83293C20.496 11.4051 20.496 12.9779 20.496 14.5501C20.496 16.2802 20.4934 18.0109 20.5 19.741C20.5007 19.9318 20.4602 20.0108 20.2538 19.9994C19.9201 19.982 19.5844 19.984 19.2507 19.9987C19.0563 20.0074 18.9952 19.9499 18.9965 19.7471C19.0045 18.1388 19.0012 16.5305 19.0012 14.9222V11.4747H15.017C14.6925 10.5022 14.1445 10.0464 13.3477 10.0672C12.5641 10.0879 12.0326 10.5531 11.7646 11.468H1.99681V11.7417C1.99681 14.3981 1.99681 17.0552 1.99681 19.7116C1.99681 19.9003 1.90371 19.9949 1.71749 19.9954C1.38309 19.9954 1.04804 19.9893 0.713641 19.998C0.563694 20.0047 0.5 19.9679 0.5 19.7993C0.503981 16.4461 0.502654 13.093 0.503981 9.7399C0.503981 9.49293 0.593551 9.41596 0.87354 9.41596C1.90592 9.41462 2.93763 9.41596 3.97001 9.41529C4.71709 9.41529 5.46483 9.40994 6.21191 9.41931C6.37248 9.42132 6.4501 9.36643 6.51645 9.22321C6.78251 8.64628 7.06117 8.07538 7.3531 7.46566V7.46499ZM17.4102 8.61951C17.6968 8.61416 17.9323 8.3692 17.9297 8.07873C17.9277 7.78893 17.6868 7.54732 17.4002 7.54732C17.1063 7.54732 16.8581 7.80499 16.8688 8.09948C16.8794 8.39128 17.1229 8.62487 17.4102 8.61951ZM8.28198 6.85594C8.46775 6.85394 8.62035 6.69732 8.61504 6.51394C8.60974 6.33122 8.44785 6.17996 8.26473 6.18666C8.08957 6.19335 7.94426 6.34394 7.94294 6.51996C7.94161 6.70602 8.09421 6.85795 8.28131 6.85527L8.28198 6.85594Z" fill="white"/>
			<path d="M13.9036 5.08858H2.6543C2.6543 5.00291 2.6543 4.92661 2.6543 4.84965C2.6543 3.42808 2.6543 2.00652 2.6543 0.584956C2.65496 0.127834 2.78434 0 3.24214 0C6.60201 0 9.96122 0 13.3211 0C13.7742 0 13.9036 0.130511 13.9036 0.591649C13.9036 2.01321 13.9036 3.43477 13.9036 4.85634V5.08858Z" fill="white"/>
			<path d="M14.7755 19.9895H13.6177C13.6177 19.6395 13.6177 19.2975 13.6177 18.9548C13.6177 18.8832 13.6164 18.8116 13.6177 18.7393C13.6197 18.578 13.5474 18.4662 13.3855 18.4649C13.2236 18.4636 13.1486 18.5733 13.1486 18.7353C13.1486 19.08 13.1486 19.424 13.1486 19.7687C13.1486 19.8383 13.1486 19.9079 13.1486 19.9875H11.9915C11.9915 19.9119 11.9915 19.8369 11.9915 19.7613C11.9915 19.2158 11.9935 18.6704 11.9902 18.1249C11.9888 17.9248 11.888 17.815 11.7314 17.8344C11.5596 17.8558 11.5211 17.9763 11.5217 18.1289C11.5237 18.667 11.5224 19.2051 11.5224 19.7439C11.5224 19.8209 11.5224 19.8978 11.5224 19.9976H10.6108C10.1696 19.9976 9.72835 20.0029 9.2878 19.9935C9.19359 19.9915 9.1007 19.9366 9.00781 19.9065C9.05227 19.8235 9.08279 19.7278 9.14383 19.6609C9.29775 19.4916 9.4623 19.333 9.6275 19.175C10.0919 18.7299 10.3175 18.1865 10.3069 17.5399C10.3016 17.1973 10.3062 16.8539 10.3062 16.4958H16.4521C16.4554 16.5742 16.4607 16.6565 16.4634 16.7388C16.476 17.1685 16.458 17.6022 16.5078 18.0278C16.5589 18.4676 16.7944 18.8317 17.1083 19.1409C17.3066 19.3363 17.507 19.5304 17.6974 19.7339C17.7379 19.7774 17.7552 19.861 17.7485 19.9226C17.7452 19.952 17.6609 19.9935 17.6132 19.9942C16.8323 19.9989 16.0513 19.9976 15.2459 19.9976V19.7486C15.2459 18.9019 15.2452 18.0546 15.2465 17.208C15.2465 17.0708 15.2353 16.9443 15.0827 16.8954C14.9088 16.8399 14.7781 16.9664 14.7774 17.1959C14.7755 18.0426 14.7768 18.8899 14.7768 19.7365C14.7768 19.8142 14.7768 19.8918 14.7768 19.9902L14.7755 19.9895Z" fill="white"/>
			<path d="M10.3111 15.9847C10.3111 15.5919 10.2939 15.2191 10.3158 14.8483C10.3377 14.4802 10.627 14.2312 11.0071 14.2245C11.4039 14.2172 11.8007 14.2232 12.2233 14.2232C12.2233 14.1181 12.2233 14.0344 12.2233 13.9508C12.2233 13.2112 12.2127 12.4723 12.2266 11.7328C12.2432 10.8272 13.1064 10.2684 13.8853 10.6545C14.3398 10.8794 14.5408 11.2716 14.5428 11.7736C14.5448 12.5058 14.5428 13.238 14.5435 13.9695C14.5435 14.0465 14.5435 14.1228 14.5435 14.2218C14.9276 14.2218 15.2886 14.2218 15.6495 14.2218C16.1936 14.2232 16.4576 14.4902 16.4596 15.0424C16.461 15.3503 16.4596 15.6575 16.4596 15.9841H10.3105L10.3111 15.9847ZM13.7878 11.7455C13.7911 11.5173 13.6186 11.3345 13.3937 11.3285C13.1588 11.3225 12.973 11.5052 12.975 11.7415C12.9763 11.9677 13.1562 12.1491 13.3797 12.1497C13.6033 12.1504 13.7845 11.971 13.7878 11.7448V11.7455Z" fill="white"/>
			<path d="M7.75651 16.491V18.4774H2.47852V16.491H7.75651ZM5.10856 17.7184C5.41509 17.7184 5.53451 17.6528 5.53319 17.4849C5.53186 17.3155 5.40911 17.2473 5.10657 17.2479C4.81729 17.2479 4.70118 17.3122 4.69322 17.4755C4.68526 17.6475 4.80667 17.7184 5.10789 17.7184H5.10856Z" fill="white"/>
			<path d="M2.47461 15.9864V14.196H7.75194V15.9864H2.47461ZM5.19953 14.8613C4.81405 14.8613 4.69595 14.9256 4.69528 15.0942C4.69528 15.2649 4.81869 15.3352 5.11925 15.3352C5.41383 15.3352 5.55383 15.2562 5.5326 15.0855C5.50075 14.8305 5.29971 14.86 5.19953 14.8613Z" fill="white"/>
			<path d="M2.47656 13.693V11.9548H7.74527V13.693H2.47656ZM5.10992 13.0618C5.41247 13.0618 5.53123 12.9969 5.53322 12.8303C5.53521 12.6609 5.41048 12.588 5.11589 12.588C4.81733 12.588 4.69525 12.6562 4.69259 12.8242C4.68994 12.9929 4.81069 13.0618 5.10926 13.0618H5.10992Z" fill="white"/>
			<path d="M18.4917 6.27144H16.3148C16.3148 5.71593 16.277 5.16979 16.3255 4.63102C16.3745 4.09024 16.9206 3.69 17.4527 3.72614C18.0213 3.76496 18.4705 4.20134 18.4884 4.76153C18.5043 5.26082 18.4917 5.76077 18.4917 6.27077V6.27144Z" fill="white"/>
		</svg>',

		'<svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M7.3531 7.46499C6.2285 7.46499 5.14703 7.46499 4.06555 7.46499C3.73845 7.46499 3.41069 7.46968 3.0836 7.46165C2.84673 7.45629 2.67688 7.31775 2.66958 7.0835C2.653 6.59024 2.66428 6.0963 2.66428 5.58765H13.8891C13.8891 6.10233 13.905 6.60965 13.8824 7.11496C13.8718 7.35188 13.684 7.46365 13.4007 7.46365C12.1554 7.46499 10.9094 7.46365 9.66401 7.46365C9.52401 7.46365 9.38336 7.46365 9.2208 7.46365C9.24668 7.53192 9.25995 7.5781 9.28052 7.62094C9.54325 8.16975 9.80268 8.7199 10.0747 9.2647C10.1079 9.33096 10.2041 9.40659 10.2718 9.40726C12.1866 9.41395 14.1014 9.41195 16.0162 9.41061C16.0367 9.41061 16.0567 9.40124 16.0527 9.40191C15.8968 8.75538 15.7408 8.11018 15.5849 7.46566C15.5544 7.34051 15.5192 7.21535 15.4993 7.08885C15.4662 6.88205 15.5557 6.75957 15.7588 6.7589C16.8548 6.75421 17.9509 6.75354 19.047 6.7589C19.2692 6.76024 19.3475 6.90012 19.2878 7.15712C19.1425 7.77822 18.9952 8.39865 18.8453 9.01841C18.8154 9.14089 18.771 9.26069 18.7285 9.39789C18.7783 9.40258 18.8373 9.41195 18.8964 9.41195C19.2878 9.41328 19.6793 9.41195 20.0707 9.41262C20.437 9.41262 20.496 9.47151 20.496 9.83293C20.496 11.4051 20.496 12.9779 20.496 14.5501C20.496 16.2802 20.4934 18.0109 20.5 19.741C20.5007 19.9318 20.4602 20.0108 20.2538 19.9994C19.9201 19.982 19.5844 19.984 19.2507 19.9987C19.0563 20.0074 18.9952 19.9499 18.9965 19.7471C19.0045 18.1388 19.0012 16.5305 19.0012 14.9222V11.4747H15.017C14.6925 10.5022 14.1445 10.0464 13.3477 10.0672C12.5641 10.0879 12.0326 10.5531 11.7646 11.468H1.99681V11.7417C1.99681 14.3981 1.99681 17.0552 1.99681 19.7116C1.99681 19.9003 1.90371 19.9949 1.71749 19.9954C1.38309 19.9954 1.04804 19.9893 0.713641 19.998C0.563694 20.0047 0.5 19.9679 0.5 19.7993C0.503981 16.4461 0.502654 13.093 0.503981 9.7399C0.503981 9.49293 0.593551 9.41596 0.87354 9.41596C1.90592 9.41462 2.93763 9.41596 3.97001 9.41529C4.71709 9.41529 5.46483 9.40994 6.21191 9.41931C6.37248 9.42132 6.4501 9.36643 6.51645 9.22321C6.78251 8.64628 7.06117 8.07538 7.3531 7.46566V7.46499ZM17.4102 8.61951C17.6968 8.61416 17.9323 8.3692 17.9297 8.07873C17.9277 7.78893 17.6868 7.54732 17.4002 7.54732C17.1063 7.54732 16.8581 7.80499 16.8688 8.09948C16.8794 8.39128 17.1229 8.62487 17.4102 8.61951ZM8.28198 6.85594C8.46775 6.85394 8.62035 6.69732 8.61504 6.51394C8.60974 6.33122 8.44785 6.17996 8.26473 6.18666C8.08957 6.19335 7.94426 6.34394 7.94294 6.51996C7.94161 6.70602 8.09421 6.85795 8.28131 6.85527L8.28198 6.85594Z" fill="white"/>
			<path d="M13.9036 5.08858H2.6543C2.6543 5.00291 2.6543 4.92661 2.6543 4.84965C2.6543 3.42808 2.6543 2.00652 2.6543 0.584956C2.65496 0.127834 2.78434 0 3.24214 0C6.60201 0 9.96122 0 13.3211 0C13.7742 0 13.9036 0.130511 13.9036 0.591649C13.9036 2.01321 13.9036 3.43477 13.9036 4.85634V5.08858Z" fill="white"/>
			<path d="M14.7755 19.9895H13.6177C13.6177 19.6395 13.6177 19.2975 13.6177 18.9548C13.6177 18.8832 13.6164 18.8116 13.6177 18.7393C13.6197 18.578 13.5474 18.4662 13.3855 18.4649C13.2236 18.4636 13.1486 18.5733 13.1486 18.7353C13.1486 19.08 13.1486 19.424 13.1486 19.7687C13.1486 19.8383 13.1486 19.9079 13.1486 19.9875H11.9915C11.9915 19.9119 11.9915 19.8369 11.9915 19.7613C11.9915 19.2158 11.9935 18.6704 11.9902 18.1249C11.9888 17.9248 11.888 17.815 11.7314 17.8344C11.5596 17.8558 11.5211 17.9763 11.5217 18.1289C11.5237 18.667 11.5224 19.2051 11.5224 19.7439C11.5224 19.8209 11.5224 19.8978 11.5224 19.9976H10.6108C10.1696 19.9976 9.72835 20.0029 9.2878 19.9935C9.19359 19.9915 9.1007 19.9366 9.00781 19.9065C9.05227 19.8235 9.08279 19.7278 9.14383 19.6609C9.29775 19.4916 9.4623 19.333 9.6275 19.175C10.0919 18.7299 10.3175 18.1865 10.3069 17.5399C10.3016 17.1973 10.3062 16.8539 10.3062 16.4958H16.4521C16.4554 16.5742 16.4607 16.6565 16.4634 16.7388C16.476 17.1685 16.458 17.6022 16.5078 18.0278C16.5589 18.4676 16.7944 18.8317 17.1083 19.1409C17.3066 19.3363 17.507 19.5304 17.6974 19.7339C17.7379 19.7774 17.7552 19.861 17.7485 19.9226C17.7452 19.952 17.6609 19.9935 17.6132 19.9942C16.8323 19.9989 16.0513 19.9976 15.2459 19.9976V19.7486C15.2459 18.9019 15.2452 18.0546 15.2465 17.208C15.2465 17.0708 15.2353 16.9443 15.0827 16.8954C14.9088 16.8399 14.7781 16.9664 14.7774 17.1959C14.7755 18.0426 14.7768 18.8899 14.7768 19.7365C14.7768 19.8142 14.7768 19.8918 14.7768 19.9902L14.7755 19.9895Z" fill="white"/>
			<path d="M10.3111 15.9847C10.3111 15.5919 10.2939 15.2191 10.3158 14.8483C10.3377 14.4802 10.627 14.2312 11.0071 14.2245C11.4039 14.2172 11.8007 14.2232 12.2233 14.2232C12.2233 14.1181 12.2233 14.0344 12.2233 13.9508C12.2233 13.2112 12.2127 12.4723 12.2266 11.7328C12.2432 10.8272 13.1064 10.2684 13.8853 10.6545C14.3398 10.8794 14.5408 11.2716 14.5428 11.7736C14.5448 12.5058 14.5428 13.238 14.5435 13.9695C14.5435 14.0465 14.5435 14.1228 14.5435 14.2218C14.9276 14.2218 15.2886 14.2218 15.6495 14.2218C16.1936 14.2232 16.4576 14.4902 16.4596 15.0424C16.461 15.3503 16.4596 15.6575 16.4596 15.9841H10.3105L10.3111 15.9847ZM13.7878 11.7455C13.7911 11.5173 13.6186 11.3345 13.3937 11.3285C13.1588 11.3225 12.973 11.5052 12.975 11.7415C12.9763 11.9677 13.1562 12.1491 13.3797 12.1497C13.6033 12.1504 13.7845 11.971 13.7878 11.7448V11.7455Z" fill="white"/>
			<path d="M7.75651 16.491V18.4774H2.47852V16.491H7.75651ZM5.10856 17.7184C5.41509 17.7184 5.53451 17.6528 5.53319 17.4849C5.53186 17.3155 5.40911 17.2473 5.10657 17.2479C4.81729 17.2479 4.70118 17.3122 4.69322 17.4755C4.68526 17.6475 4.80667 17.7184 5.10789 17.7184H5.10856Z" fill="white"/>
			<path d="M2.47461 15.9864V14.196H7.75194V15.9864H2.47461ZM5.19953 14.8613C4.81405 14.8613 4.69595 14.9256 4.69528 15.0942C4.69528 15.2649 4.81869 15.3352 5.11925 15.3352C5.41383 15.3352 5.55383 15.2562 5.5326 15.0855C5.50075 14.8305 5.29971 14.86 5.19953 14.8613Z" fill="white"/>
			<path d="M2.47656 13.693V11.9548H7.74527V13.693H2.47656ZM5.10992 13.0618C5.41247 13.0618 5.53123 12.9969 5.53322 12.8303C5.53521 12.6609 5.41048 12.588 5.11589 12.588C4.81733 12.588 4.69525 12.6562 4.69259 12.8242C4.68994 12.9929 4.81069 13.0618 5.10926 13.0618H5.10992Z" fill="white"/>
			<path d="M18.4917 6.27144H16.3148C16.3148 5.71593 16.277 5.16979 16.3255 4.63102C16.3745 4.09024 16.9206 3.69 17.4527 3.72614C18.0213 3.76496 18.4705 4.20134 18.4884 4.76153C18.5043 5.26082 18.4917 5.76077 18.4917 6.27077V6.27144Z" fill="white"/>
		</svg>'
	];
}

// service hero section metaboxes
function service_krisli_meta_box()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'our-services') {
		add_meta_box(
			'service_krisli_meta',
			'Service Hero Section',
			'service_krisli_meta_box_callback',
			['post', 'page'],
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'service_krisli_meta_box');

function service_krisli_meta_box_callback($post)
{
	wp_nonce_field('service_krisli_nonce_action', 'service_krisli_nonce');

	$headings   = get_post_meta($post->ID, '_service_krisli_headings', true) ?: '';
	$paragraphs = get_post_meta($post->ID, '_service_krisli_paragraphs', true) ?: '';
	$images     = get_post_meta($post->ID, '_service_krisli_images', true) ?: '';
	$buttons    = get_post_meta($post->ID, '_service_krisli_buttons', true) ?: '';
	$button_links = get_post_meta($post->ID, '_service_krisli_button_links', true) ?: '';
	$button_about    = get_post_meta($post->ID, '_service_krisli_button_about', true) ?: '';
	$button_link_about = get_post_meta($post->ID, '_service_krisli_button_link_about', true) ?: '';
	$image_orintation = get_post_meta($post->ID, '_service_krisli_img_orintation', true) ?: '';

?>
	<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
		<h4>Service Hero KrisLi Information </h4>

		<p><label>Heading:</label><br>
			<input type="text" name="service_krisli_headings" value="<?php echo esc_attr($headings ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Paragraph:</label><br>
			<textarea name="service_krisli_paragraphs" rows="4" style="width:100%;"><?php echo esc_textarea($paragraphs ?? ''); ?></textarea>
		</p>

		<p><label>Image URL:</label><br>
			<input type="text" name="service_krisli_images" id="service_krisli_image" value="<?php echo esc_attr($images ?? ''); ?>" style="width:80%;">
			<button class="button upload-image" data-target="service_krisli_image">Upload</button>
		</p>
		<p>
			<label>Button Label Service:</label><br>
			<input type="text" name="service_krisli_buttons" value="<?php echo esc_attr($buttons ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Button Link Service:</label><br>
			<input type="text" name="service_krisli_button_links" value="<?php echo esc_attr($button_links ?? ''); ?>" style="width:100%;">
		</p>
		<p>
			<label>Button Label About:</label><br>
			<input type="text" name="service_krisli_button_about" value="<?php echo esc_attr($button_about ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Button Link About:</label><br>
			<input type="text" name="service_krisli_button_link_about" value="<?php echo esc_attr($button_link_about ?? ''); ?>" style="width:100%;">
		</p>

		<p>
			<label>Image Orintation</label><br>
			<select name="service_krisli_img_orintation" id="service_krisli_img_orintation">
				<option value="normal" <?php selected($image_orintation, 'top'); ?>>Top</option>
				<option value="right" <?php selected($image_orintation, 'right'); ?>>Right</option>
				<option value="bottom" <?php selected($image_orintation, 'bottom'); ?>>Bottom</option>
				<option value="left" <?php selected($image_orintation, 'left'); ?>>Left</option>
			</select>

		</p>
	</div>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.upload-image').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const inputId = this.dataset.target;
					const input = document.getElementById(inputId);
					const uploader = wp.media({
						title: 'Select Image',
						button: {
							text: 'Use this image'
						},
						multiple: false
					}).on('select', function() {
						const attachment = uploader.state().get('selection').first().toJSON();
						input.value = attachment.url;
					}).open();
				});
			});
		});
	</script>
	<?php
}

function save_service_krisli_meta_box($post_id)
{
	if (
		!isset($_POST['service_krisli_nonce']) ||
		!wp_verify_nonce($_POST['service_krisli_nonce'], 'service_krisli_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_service_krisli_headings', sanitize_text_field($_POST['service_krisli_headings']));
	update_post_meta($post_id, '_service_krisli_paragraphs', sanitize_textarea_field($_POST['service_krisli_paragraphs']));
	update_post_meta($post_id, '_service_krisli_images', esc_url_raw($_POST['service_krisli_images']));
	update_post_meta($post_id, '_service_krisli_buttons', sanitize_text_field($_POST['service_krisli_buttons']));
	update_post_meta($post_id, '_service_krisli_button_links', esc_url_raw($_POST['service_krisli_button_links']));
	update_post_meta($post_id, '_service_krisli_button_about', sanitize_text_field($_POST['service_krisli_button_about']));
	update_post_meta($post_id, '_service_krisli_button_link_about', esc_url_raw($_POST['service_krisli_button_link_about']));
	update_post_meta($post_id, '_service_krisli_img_orintation', sanitize_text_field($_POST['service_krisli_img_orintation']));
}

add_action('save_post', 'save_service_krisli_meta_box');

// service why_choose_us section metabox
function why_choose_us_meta_box()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'our-services') {

		add_meta_box(
			'why_choose_us_meta',
			'Why_choose_us Section',
			'why_choose_us_meta_box_callback',
			['post', 'page'],
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'why_choose_us_meta_box');

function why_choose_us_meta_box_callback($post)
{
	wp_nonce_field('why_choose_us_nonce_action', 'why_choose_us_nonce');

	$why_choose_us_number     = get_post_meta($post->ID, '_why_choose_us_number', true) ?: [];
	$why_choose_us_title   = get_post_meta($post->ID, '_why_choose_us_title', true) ?: [];
	$why_choose_us_description = get_post_meta($post->ID, '_why_choose_us_description', true) ?: [];


	for ($i = 0; $i < 3; $i++) {
	?>
		<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
			<h4>Why_choose_us <?php echo $i + 1; ?></h4>


			<p>
				<label>Add Sequence Number:</label><br>
				<input type="text" name="why_choose_us_number[]" id="why_choose_us_number_<?php echo $i; ?>" value="<?php echo esc_attr($why_choose_us_number[$i] ?? ''); ?>" style="width:80%;">


			</p>
			<p><label>Title:</label><br>
				<input type="text" name="why_choose_us_title[]" value="<?php echo esc_attr($why_choose_us_title[$i] ?? ''); ?>" style="width:100%;">
			</p>

			<p><label>Description:</label><br>
				<textarea name="why_choose_us_description[]" rows="4" style="width:100%;"><?php echo esc_textarea($why_choose_us_description[$i] ?? ''); ?></textarea>
			</p>
		</div>
	<?php
	}

	?>

<?php
}

function save_why_choose_us_meta_box($post_id)
{
	if (
		!isset($_POST['why_choose_us_nonce']) ||
		!wp_verify_nonce($_POST['why_choose_us_nonce'], 'why_choose_us_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_why_choose_us_number', array_map('sanitize_text_field', $_POST['why_choose_us_number'] ?? []));
	update_post_meta($post_id, '_why_choose_us_title', array_map('sanitize_text_field', $_POST['why_choose_us_title'] ?? []));
	update_post_meta($post_id, '_why_choose_us_description', array_map('sanitize_textarea_field', $_POST['why_choose_us_description'] ?? []));
}
add_action('save_post', 'save_why_choose_us_meta_box');

// service pricing Info section metabox
function aboutus_vision_meta_box()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'our-services') {

		add_meta_box(
			'aboutus_vision_meta',
			'About Us Vision Section',
			'aboutus_vision_meta_box_callback',
			'page',
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'aboutus_vision_meta_box');
function aboutus_vision_meta_box_callback($post)
{
	wp_nonce_field('aboutus_vision_nonce_action', 'aboutus_vision_nonce');

	$headings         = get_post_meta($post->ID, '_aboutus_vision_headings', true) ?: [];
	$paragraphs       = get_post_meta($post->ID, '_aboutus_vision_paragraphs', true) ?: [];
	$images           = get_post_meta($post->ID, '_aboutus_vision_images', true) ?: [];
	$buttons          = get_post_meta($post->ID, '_aboutus_vision_buttons', true) ?: [];
	$button_links     = get_post_meta($post->ID, '_aboutus_vision_button_links', true) ?: [];
	$image_orientation = get_post_meta($post->ID, '_aboutus_vision_img_orientation', true) ?: [];
	$lists            = get_post_meta($post->ID, '_aboutus_vision_lists', true) ?: [];
?>
	<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
		<h4>Vision Section</h4>

		<p><label>Heading:</label><br>
			<input type="text" name="aboutus_vision_headings[]" value="<?php echo esc_attr($headings[0] ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Paragraph:</label><br>
			<textarea name="aboutus_vision_paragraphs[]" rows="4" style="width:100%;"><?php echo esc_textarea($paragraphs[0] ?? ''); ?></textarea>
		</p>

		<p><label>Image URL:</label><br>
			<input type="text" name="aboutus_vision_images[]" id="aboutus_vision_image_0" value="<?php echo esc_attr($images[0] ?? ''); ?>" style="width:80%;">
			<button class="button upload-vision-image" data-target="aboutus_vision_image_0">Upload</button>
		</p>

		<p><label>Button Label:</label><br>
			<input type="text" name="aboutus_vision_buttons[]" value="<?php echo esc_attr($buttons[0] ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Button Link:</label><br>
			<input type="text" name="aboutus_vision_button_links[]" value="<?php echo esc_attr($button_links[0] ?? ''); ?>" style="width:100%;">
		</p>

		<p>
			<label>Image Orientation:</label><br>
			<select name="aboutus_vision_img_orientation[]" id="aboutus_vision_img_orientation_0">
				<option value="top" <?php selected($image_orientation[0] ?? '', 'top'); ?>>Top</option>
				<option value="right" <?php selected($image_orientation[0] ?? '', 'right'); ?>>Right</option>
				<option value="bottom" <?php selected($image_orientation[0] ?? '', 'bottom'); ?>>Bottom</option>
				<option value="left" <?php selected($image_orientation[0] ?? '', 'left'); ?>>Left</option>
			</select>
		</p>

		<div id="aboutus-vision-list-container">
			<label>List Items:</label>
			<?php
			if (!empty($lists) && is_array($lists)) {
				foreach ($lists as $list) {
					$title = $list['title'] ?? '';
					$desc  = $list['description'] ?? '';
			?>
					<div class="list-item-group">
						<p><input type="text" name="aboutus_vision_lists_title[]" value="<?php echo esc_attr($title); ?>" placeholder="Title" style="width:100%;"></p>
						<p><textarea name="aboutus_vision_lists_description[]" rows="2" style="width:100%;" placeholder="Description"><?php echo esc_textarea($desc); ?></textarea></p>
					</div>
			<?php }
			}
			?>
			<div class="list-item-group">
				<p><input type="text" name="aboutus_vision_lists_title[]" placeholder="Title" style="width:100%;"></p>
				<p><textarea name="aboutus_vision_lists_description[]" rows="2" style="width:100%;" placeholder="Description"></textarea></p>
			</div>
		</div>
		<p><button type="button" class="button add-vision-list-item">+ Add List Item</button></p>

	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.upload-vision-image').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const inputId = this.dataset.target;
					const input = document.getElementById(inputId);
					const uploader = wp.media({
						title: 'Select Image',
						button: {
							text: 'Use this image'
						},
						multiple: false
					}).on('select', function() {
						const attachment = uploader.state().get('selection').first().toJSON();
						input.value = attachment.url;
					}).open();
				});
			});

			const listContainer = document.getElementById('aboutus-vision-list-container');
			document.querySelector('.add-vision-list-item').addEventListener('click', function() {
				const group = document.createElement('div');
				group.className = 'list-item-group';

				const title = document.createElement('input');
				title.type = 'text';
				title.name = 'aboutus_vision_lists_title[]';
				title.placeholder = 'Title';
				title.style.width = '100%';

				const desc = document.createElement('textarea');
				desc.name = 'aboutus_vision_lists_description[]';
				desc.rows = 2;
				desc.placeholder = 'Description';
				desc.style.width = '100%';

				group.appendChild(document.createElement('p')).appendChild(title);
				group.appendChild(document.createElement('p')).appendChild(desc);
				listContainer.appendChild(group);
			});
		});
	</script>
<?php
}

function save_aboutus_vision_meta_box($post_id)
{
	if (
		!isset($_POST['aboutus_vision_nonce']) ||
		!wp_verify_nonce($_POST['aboutus_vision_nonce'], 'aboutus_vision_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	$titles = $_POST['aboutus_vision_lists_title'] ?? [];
	$descs  = $_POST['aboutus_vision_lists_description'] ?? [];

	$structured_lists = [];

	for ($i = 0; $i < count($titles); $i++) {
		$title = sanitize_text_field($titles[$i]);
		$desc  = sanitize_textarea_field($descs[$i]);

		if ($title || $desc) {
			$structured_lists[] = [
				'title' => $title,
				'description' => $desc
			];
		}
	}

	update_post_meta($post_id, '_aboutus_vision_lists', $structured_lists);

	update_post_meta($post_id, '_aboutus_vision_headings', array_map('sanitize_text_field', $_POST['aboutus_vision_headings'] ?? []));
	update_post_meta($post_id, '_aboutus_vision_paragraphs', array_map('sanitize_textarea_field', $_POST['aboutus_vision_paragraphs'] ?? []));
	update_post_meta($post_id, '_aboutus_vision_images', array_map('esc_url_raw', $_POST['aboutus_vision_images'] ?? []));
	update_post_meta($post_id, '_aboutus_vision_buttons', array_map('sanitize_text_field', $_POST['aboutus_vision_buttons'] ?? []));
	update_post_meta($post_id, '_aboutus_vision_button_links', array_map('esc_url_raw', $_POST['aboutus_vision_button_links'] ?? []));
	update_post_meta($post_id, '_aboutus_vision_img_orientation', array_map('sanitize_text_field', $_POST['aboutus_vision_img_orientation'] ?? []));
}
add_action('save_post', 'save_aboutus_vision_meta_box');


// Provided service CUSTOM POST TYPE(CPT) section
function register_tabs_post_type()
{
	$labels = array(
		'name' => 'Tabs',
		'singular_name' => 'Tab',
		'add_new' => 'Add New Tab',
		'add_new_item' => 'Add New Tab Item',
		'edit_item' => 'Edit Tab Item',
		'new_item' => 'New Tab Item',
		'view_item' => 'View Tab Item',
		'search_items' => 'Search Tab Items',
		'not_found' => 'No Tab Items found',
		'not_found_in_trash' => 'No Tab Items found in Trash',
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'has_archive' => true,
		'rewrite' => array('slug' => 'tabs'),
		'supports' => array('title', 'editor'),
		'show_in_rest' => true,

		'show_in_nav_menus' => true,
	);

	register_post_type('tab_content', $args);
}
add_action('init', 'register_tabs_post_type');



function add_tab_meta_boxes()
{
	add_meta_box(
		'tab_sections',
		'Tab Sections',
		'render_tab_sections_meta_box',
		'tab_content',
		'normal',
		'default'
	);
}
add_action('add_meta_boxes', 'add_tab_meta_boxes');

function render_tab_sections_meta_box($post)
{
	wp_nonce_field('service_tab_posts_section_nonce_action', 'service_tab_posts_section_nonce');

	$headings   = get_post_meta($post->ID, '_service_tab_posts_headings', true) ?: '';
	$paragraphs = get_post_meta($post->ID, '_service_tab_posts_paragraphs', true) ?: '';
	$images     = get_post_meta($post->ID, '_service_tab_posts_images', true) ?: '';
	$buttons    = get_post_meta($post->ID, '_service_tab_posts_buttons', true) ?: '';
	$button_links = get_post_meta($post->ID, '_service_tab_posts_button_links', true) ?: '';
?>
	<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
		<h4>Service_tab_posts Section Information </h4>


		<p><label>Heading:</label><br>
			<input type="text" name="service_tab_posts_headings" value="<?php echo esc_attr($headings ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Paragraph:</label><br>
			<textarea name="service_tab_posts_paragraphs" rows="4" style="width:100%;"><?php echo esc_textarea($paragraphs ?? ''); ?></textarea>
		</p>

		<p><label>Image URL:</label><br>
			<input type="text" name="service_tab_posts_images" id="service_tab_posts_image" value="<?php echo esc_attr($images ?? ''); ?>" style="width:80%;">
			<button class="button upload-image" data-target="service_tab_posts_image">Upload</button>
		</p>
		<p><label>Button Label:</label><br>
			<input type="text" name="service_tab_posts_buttons" value="<?php echo esc_attr($buttons ?? ''); ?>" style="width:100%;">
		</p>

		<p><label>Button Link:</label><br>
			<input type="text" name="service_tab_posts_button_links" value="<?php echo esc_attr($button_links ?? ''); ?>" style="width:100%;">
		</p>
	</div>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.upload-image').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const inputId = this.dataset.target;
					const input = document.getElementById(inputId);
					const uploader = wp.media({
						title: 'Select Image',
						button: {
							text: 'Use this image'
						},
						multiple: false
					}).on('select', function() {
						const attachment = uploader.state().get('selection').first().toJSON();
						input.value = attachment.url;
					}).open();
				});
			});
		});
	</script>
<?php
}

function save_service_tab_posts_section_meta_box($post_id)
{
	if (
		!isset($_POST['service_tab_posts_section_nonce']) ||
		!wp_verify_nonce($_POST['service_tab_posts_section_nonce'], 'service_tab_posts_section_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	update_post_meta($post_id, '_service_tab_posts_headings', sanitize_text_field($_POST['service_tab_posts_headings']));
	update_post_meta($post_id, '_service_tab_posts_paragraphs', sanitize_textarea_field($_POST['service_tab_posts_paragraphs']));
	update_post_meta($post_id, '_service_tab_posts_images', esc_url_raw($_POST['service_tab_posts_images']));
	update_post_meta($post_id, '_service_tab_posts_buttons', sanitize_text_field($_POST['service_tab_posts_buttons']));
	update_post_meta($post_id, '_service_tab_posts_button_links', esc_url_raw($_POST['service_tab_posts_button_links']));
}


add_action('save_post', 'save_service_tab_posts_section_meta_box');

// %%%%%%%%%%%%%%%%%%%%%%%%%%%% Contactus Page %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%



function contactus_meta_box()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'contact-us') {

		add_meta_box(
			'contactus_meta',
			'Contact Page Background Image',
			'contactus_meta_box_callback',
			['post', 'page'],
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'contactus_meta_box');

function contactus_meta_box_callback($post)
{
	wp_nonce_field('contactus_nonce_action', 'contactus_nonce');

	$images     = get_post_meta($post->ID, '_contactus_bg_image', true) ?: '';


?>
	<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
		<h4>ContactUs Background Image </h4>
		<p><label>Image URL:</label><br>
			<input type="text" name="contactus_bg_image" id="contact_image" value="<?php echo esc_attr($images ?? ''); ?>" style="width:80%;">
			<button class="button upload-image" data-target="contact_image">Upload</button>
		</p>
	</div>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.upload-image').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const inputId = this.dataset.target;
					const input = document.getElementById(inputId);
					const uploader = wp.media({
						title: 'Select Image',
						button: {
							text: 'Use this image'
						},
						multiple: false
					}).on('select', function() {
						const attachment = uploader.state().get('selection').first().toJSON();
						input.value = attachment.url;
					}).open();
				});
			});
		});
	</script>
<?php
}




function save_contactus_meta_box($post_id)
{
	if (
		!isset($_POST['contactus_nonce']) ||
		!wp_verify_nonce($_POST['contactus_nonce'], 'contactus_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;
	update_post_meta($post_id, '_contactus_bg_image', esc_url_raw($_POST['contactus_bg_image']));
}
add_action('save_post', 'save_contactus_meta_box');

// Contactus Form section  metaboxes
function contact_page_form_meta_box()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'contact-us') {

		add_meta_box(
			'contact_page_form_meta',
			'Contact Form form',
			'contact_page_form_meta_box_callback',
			['post', 'page'],
			'advanced',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'contact_page_form_meta_box');

function contact_page_form_meta_box_callback($post)
{
	wp_nonce_field('contact_page_form_nonce_action', 'contact_page_form_nonce');
	$images = get_post_meta($post->ID, '_contact_form_images', true) ?: '';
	$recaptcha_logo = get_post_meta($post->ID, '_contact_form_recaptcha_images', true) ?: '';
?>
	<div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">
		<h4>Contact Information</h4>
		<p>
			<label>Image URL:</label><br>
			<input type="text" name="contact_form_images" id="contact_form_images" value="<?php echo esc_attr($images); ?>" style="width:80%;">
			<button class="button upload-image" data-target="contact_form_images">Upload</button>
		</p>
		<p>
			<label>Recaptcha Logo URL:</label><br>
			<input type="text" name="contact_form_recaptcha_images" id="contact_form_recaptcha_images" value="<?php echo esc_attr($recaptcha_logo); ?>" style="width:80%;">
			<button class="button upload-recaptcha" data-target="contact_form_recaptcha_images">Upload Recaptcha Logo</button>
		</p>
	</div>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.upload-image, .upload-recaptcha').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					const inputId = this.dataset.target;
					const input = document.getElementById(inputId);

					if (!input) {
						console.error('Input field with ID ' + inputId + ' not found.');
						return;
					}

					const uploader = wp.media({
						title: 'Select Image',
						button: {
							text: 'Use this image'
						},
						multiple: false
					}).on('select', function() {
						const attachment = uploader.state().get('selection').first().toJSON();
						input.value = attachment.url;
					}).open();
				});
			});
		});
	</script>
<?php
}

function save_contact_page_form_meta_box($post_id)
{
	if (
		!isset($_POST['contact_page_form_nonce']) ||
		!wp_verify_nonce($_POST['contact_page_form_nonce'], 'contact_page_form_nonce_action')
	) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	if (!current_user_can('edit_post', $post_id)) {
		return;
	}

	if (isset($_POST['contact_form_images'])) {
		update_post_meta($post_id, '_contact_form_images', esc_url_raw($_POST['contact_form_images']));
	}

	if (isset($_POST['contact_form_recaptcha_images'])) {
		update_post_meta($post_id, '_contact_form_recaptcha_images', esc_url_raw($_POST['contact_form_recaptcha_images']));
	}
}
add_action('save_post', 'save_contact_page_form_meta_box');


// %%%%%%%%%%%%%%%%%%%%%%%%%%%% FAQ PAGE %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
function custom_faq_metabox()
{
	global $post;
	$post_slug = $post->post_name;
	if ($post_slug === 'faq') {

		add_meta_box(
			'custom_faq_metabox',
			'FAQs',
			'render_custom_faq_metabox',
			'page',
			'advanced',
			'default'
		);
	}
}
add_action('add_meta_boxes', 'custom_faq_metabox');


function render_custom_faq_metabox($post)
{
	wp_nonce_field('save_custom_faqs', 'custom_faq_nonce');
	$faqs = get_post_meta($post->ID, '_custom_faqs', true);

?>
	<div id="faq-wrapper">
		<?php
		if (!empty($faqs) && is_array($faqs)) {
			foreach ($faqs as $index => $faq) {
		?>
				<div class="faq-item">
					<input type="text" name="custom_faqs[<?php echo $index; ?>][question]" placeholder="Question" value="<?php echo esc_attr($faq['question']); ?>" style="width: 100%; margin-bottom: 5px;" />
					<textarea name="custom_faqs[<?php echo $index; ?>][answer]" placeholder="Answer" style="width: 100%; margin-bottom: 10px;"><?php echo esc_textarea($faq['answer']); ?></textarea>
					<button type="button" class="remove-faq button">Remove</button>
					<hr>
				</div>
		<?php
			}
		}
		?>
	</div>
	<button type="button" id="add-faq" class="button">Add FAQ</button>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			let faqIndex = <?php echo !empty($faqs) ? count($faqs) : 0; ?>;
			document.getElementById('add-faq').addEventListener('click', function() {
				let wrapper = document.getElementById('faq-wrapper');
				let div = document.createElement('div');
				div.classList.add('faq-item');
				div.innerHTML = `
    <input type="text" name="custom_faqs[${faqIndex}][question]" placeholder="Question" style="width: 100%; margin-bottom: 5px;" />
    <textarea name="custom_faqs[${faqIndex}][answer]" placeholder="Answer" style="width: 100%; margin-bottom: 10px;"></textarea>
    <button type="button" class="remove-faq button">Remove</button>
    <hr>
`;
				wrapper.appendChild(div);
				faqIndex++;
			});

			document.getElementById('faq-wrapper').addEventListener('click', function(e) {
				if (e.target.classList.contains('remove-faq')) {
					e.target.closest('.faq-item').remove();
				}
			});
		});
	</script>
<?php
}
function save_custom_faq_metabox($post_id)
{
	if (!isset($_POST['custom_faq_nonce']) || !wp_verify_nonce($_POST['custom_faq_nonce'], 'save_custom_faqs')) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

	if (isset($_POST['custom_faqs']) && is_array($_POST['custom_faqs'])) {
		$faqs = array_map(function ($faq) {
			return [
				'question' => sanitize_text_field($faq['question']),
				'answer' => sanitize_textarea_field($faq['answer']),
			];
		}, $_POST['custom_faqs']);
		update_post_meta($post_id, '_custom_faqs', $faqs);
	} else {
		delete_post_meta($post_id, '_custom_faqs');
	}
}
add_action('save_post', 'save_custom_faq_metabox');
