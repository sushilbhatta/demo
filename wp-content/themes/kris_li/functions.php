<?php
// theme options
add_theme_support('title-tag');
add_theme_support('post-thumbnails');
add_theme_support('menus');
add_theme_support('html5');
add_theme_support('custom-header');
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
	wp_enqueue_style('main-css');
	wp_enqueue_style('shared-css');
	wp_enqueue_style('home-css');
	wp_enqueue_style('about-css');
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

// Menus
register_nav_menus([
	'top-menu' => esc_html__('Top Menu Location', 'kris_li'),
	'mobile-menu' => esc_html__('Mobile Menu Location', 'kris_li'),
	'footer-menu-quick-link' => esc_html__('Footer Menu Quick Link Location', 'kris_li'),
	'footer-menu-services' => esc_html__('Footer Menu Services Location', 'kris_li'),
	'footer-menu-custom-link' => esc_html__('Footer Menu Custom Link Location', 'kris_li'),
	'footer-menu-end-link' => esc_html__('Footer Menu End Location', 'kris_li'),

]);


// dropdown menu 
function add_dropdown_toggles_to_menu($item_output, $item, $depth, $args)
{
	if (in_array('menu-item-has-children', $item->classes)) {

		$item_output .= '<button class="dropdown-toggle" aria-expanded="false"><span class="screen-reader-text">Toggle submenu</span></button>';
	};
	return $item_output;
}
add_filter('walker_nav_menu_start_el', 'add_dropdown_toggles_to_menu', 10, 4);


//  get block from backend
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

		// Check inner blocks recursively
		if (!empty($block['innerBlocks'])) {
			$inner_matches = get_blocks_by_name_with_class($block_name, $class_name, $block['innerBlocks']);
			$matched_blocks = array_merge($matched_blocks, $inner_matches);
		}
	}

	return $matched_blocks;
}


// hero section metaboxes

function hero_section_meta_box()
{
	add_meta_box(
		'hero_section_meta',              // ID
		'Hero Section',                   // Title
		'hero_section_meta_box_callback', // Callback
		['post', 'page'],                    // Screen(s)
		'side',                            // Context
		'high'                               // Priority
	);
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
	// $image_orintation = get_post_meta($post->ID, '_hero_img_orintation', true) ?: [];
	// $image_container_color = get_post_meta($post->ID, '_hero_container_color', true) ?: [];


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
	// update_post_meta($post_id, '_hero_img_orintation', array_map('sanitize_text_field', $_POST['hero_img_orintation'] ?? []));
	// update_post_meta($post_id, '_hero_container_color', array_map('sanitize_text_field', $_POST['hero_img_container_color'] ?? []));
}

add_action('save_post', 'save_hero_section_meta_box');


// #######################33######33
// ###############  Testimonial heading
function brand_carousel_section_meta_box()
{
	add_meta_box(
		'brand_carousel_section_meta',
		'Brand_carousel Section',
		'brand_carousel_section_meta_box_callback',
		['post', 'page'],
		'side',
		'high'
	);
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


// Offer Content  meta_box

function carousel_meta_box()
{
	add_meta_box(
		'carousel_meta',              // ID
		'Carousel Section',                   // Title
		'carousel_meta_box_callback', // Callback
		['post', 'page'],                    // Screen(s)
		'side',                            // Context
		'high'                               // Priority
	);
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


// ##########
//##########################################



// feature section metaboxes

function feature_section_meta_box()
{
	add_meta_box(
		'feature_section_meta',              // ID
		'Feature Section',                   // Title
		'feature_section_meta_box_callback', // Callback
		['post', 'page'],                    // Screen(s)
		'side',                            // Context
		'high'                               // Priority
	);
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
	add_meta_box(
		'offer_section_meta',
		'Offer Section',
		'offer_section_meta_box_callback',
		['post', 'page'],
		'side',
		'high'
	);
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
	add_meta_box(
		'offer_meta',              // ID
		'Offer Section',                   // Title
		'offer_meta_box_callback', // Callback
		['post', 'page'],                    // Screen(s)
		'side',                            // Context
		'high'                               // Priority
	);
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




// !Contact Us metabox


function contact_section_meta_box()
{
	add_meta_box(
		'contact_section_meta',              // ID
		'Contact Section',                   // Title
		'contact_section_meta_box_callback', // Callback
		['post', 'page'],                    // Screen(s)
		'side',                            // Context
		'high'                               // Priority
	);
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


// Testimonial Header


function testimonial_section_meta_box()
{
	add_meta_box(
		'Testimonial_section_meta',
		'Testimonial Section',
		'testimonial_section_meta_box_callback',
		['post', 'page'],
		'advanced',
		'high'
	);
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



// Register the meta_box

function testimonial_card_meta_box()
{
	add_meta_box(
		'testimonial_card_meta',              // ID
		'Testimonial Card Section',                   // Title
		'testimonial_card_meta_box_callback', // Callback
		['post', 'page'],                    // Screen(s)
		'side',                            // Context
		'high'                               // Priority
	);
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


// Get In Touch Section

function get_in_touch_section_meta_box()
{
	add_meta_box(
		'get_in_touch_section_meta',              // ID
		'Get_in_touch Section',                   // Title
		'get_in_touch_section_meta_box_callback', // Callback
		['post', 'page'],                    // Screen(s)
		'side',                            // Context
		'high'                               // Priority
	);
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




//################################################About us Section##############################################
//################################################About us Section##############################################
//################################################About us Section##############################################


function about_krisli_meta_box()
{
	add_meta_box(
		'about_krisli_meta',              // ID
		'Contact Section',                   // Title
		'about_krisli_meta_box_callback', // Callback
		['post', 'page'],                    // Screen(s)
		'side',                            // Context
		'high'                               // Priority
	);
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
		<h4>About KrisLi Information </h4>

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


// aboutus  feature section

function about_feature_meta_box()
{
	add_meta_box(
		'about_feature_meta',              // ID
		'Featur About Section',                   // Title
		'about_feature_meta_box_callback', // Callback
		['post', 'page'],                    // Screen(s)
		'side',                            // Context
		'high'                               // Priority
	);
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
		<h4>About KrisLi Information </h4>

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




// About Feature Content  meta_box

function about_feature_item_meta_box()
{
	add_meta_box(
		'about_feature_item_meta',              // ID
		'About_feature_item Section',                   // Title
		'about_feature_item_meta_box_callback', // Callback
		['post', 'page'],                    // Screen(s)
		'side',                            // Context
		'high'                               // Priority
	);
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



// About us Services Section

function aboutus_service_meta_box()
{
	add_meta_box(
		'aboutus_service_meta',              // ID
		'Feature Section',                   // Title
		'aboutus_service_meta_box_callback', // Callback
		['post', 'page'],                    // Screen(s)
		'advanced',                            // Context
		'high'                               // Priority
	);
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
			<h4>SErvice <?php echo $i + 1; ?></h4>


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




// Aboutus Need

// 1. Add Meta Box (optional: show only on 'about-us' page)
function aboutus_mission_meta_box()
{
	add_meta_box(
		'aboutus_mission_meta',
		'Feature Section',
		'aboutus_mission_meta_box_callback',
		'page',
		'advanced',
		'high'
	);
}
add_action('add_meta_boxes', 'aboutus_mission_meta_box');

// 2. Meta Box Callback
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
		<h4>Mission Section</h4>

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
			<!-- Empty group for new entries -->
			<div class="list-item-group">
				<p><input type="text" name="aboutus_mission_lists_title[]" placeholder="Title" style="width:100%;"></p>
				<p><textarea name="aboutus_mission_lists_description[]" rows="2" style="width:100%;" placeholder="Description"></textarea></p>
			</div>
		</div>
		<p><button type="button" class="button add-list-item">+ Add List Item</button></p>


	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			// Upload button handler
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

			// Add new list item
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

// 3. Save Meta Box
function save_aboutus_mission_meta_box($post_id)
{
	if (
		!isset($_POST['aboutus_mission_nonce']) ||
		!wp_verify_nonce($_POST['aboutus_mission_nonce'], 'aboutus_mission_nonce_action')
	) return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;
	// Save structured list items (title + description)
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


// OUR TEAM


function team_header_meta_box()
{
	add_meta_box(
		'team_header_meta',
		'Team Section',
		'team_header_meta_box_callback',
		['post', 'page'],
		'side',
		'high'
	);
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





















// /
// 
// 

// Team  Content  meta_box

function team_meta_box()
{
	add_meta_box(
		'team_meta',              // ID
		'Team Section',                   // Title
		'team_meta_box_callback', // Callback
		['post', 'page'],                    // Screen(s)
		'side',                            // Context
		'high'                               // Priority
	);
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


?>