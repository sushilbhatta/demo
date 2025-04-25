

function  register_team_member_cpt()
{
	$labels = [
		'name' => 'Team Members',
		'singual_name' =>  'Team Member',
		'menu_name' => 'Team ',
		'add New' => 'Add New',
		'add_new_item'       => 'Add New Team Member',
		'new_item'           => 'New Team Member',
		'edit_item'          => 'Edit Team Member',
		'view_item'          => 'View Team Member',
		'all_items'          => 'All Team Members',
		'search_items'       => 'Search Team Members',
		'not_found'          => 'No team members found.',

	];
	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'has_archive'        => false, // No archive page needed
		'menu_icon'          => 'dashicons-groups', // Icon for admin menu
		'supports'           => array('title', 'thumbnail'), // Title for name, thumbnail for photo
		'show_in_rest'       => true, // Enable block editor support
		'rewrite'            => array('slug' => 'team-member'), // URL slug
	);

	register_post_type('team_member', $args);
}
add_action('init', 'register_team_member_cpt');



// Add metaboxes
add_action('add_meta_boxes', 'team_member_add_meta_boxes');
function team_member_add_meta_boxes()
{
	add_meta_box(
		'team_member_details',
		'Team Member Details',
		'team_member_details_callback',
		'team_member',
		'normal',
		'high'
	);
}

// Metabox callback to render fields
function team_member_details_callback($post)
{
	wp_nonce_field('team_member_save_meta', 'team_member_nonce');

	$job_title = get_post_meta($post->ID, '_team_member_job_title', true);
	$bio = get_post_meta($post->ID, '_team_member_bio', true);
	$social_links = get_post_meta($post->ID, '_team_member_social_links', true);
	if (!is_array($social_links)) {
		$social_links = array();
	}
?>
	<p>
		<label for="team_member_job_title">Job Title:</label><br>
		<input type="text" id="team_member_job_title" name="team_member_job_title" value="<?php echo esc_attr($job_title); ?>" style="width: 100%;">
	</p>
	<p>
		<label for="team_member_bio">Bio:</label><br>
		<textarea id="team_member_bio" name="team_member_bio" rows="5" style="width: 100%;"><?php echo esc_textarea($bio); ?></textarea>
	</p>
	<div id="social-links-container">
		<h4>Social Links</h4>
		<?php foreach ($social_links as $index => $link) : ?>
			<div class="social-link-row" style="margin-bottom: 10px;">
				<input type="text" name="team_member_social_links[<?php echo $index; ?>][platform]" value="<?php echo esc_attr($link['platform']); ?>" placeholder="Platform (e.g., LinkedIn)" style="width: 40%;">
				<input type="url" name="team_member_social_links[<?php echo $index; ?>][url]" value="<?php echo esc_url($link['url']); ?>" placeholder="URL" style="width: 40%;">
				<button type="button" class="remove-social-link button">Remove</button>
			</div>
		<?php endforeach; ?>
		<div class="social-link-row" style="margin-bottom: 10px;">
			<input type="text" name="team_member_social_links[new][platform]" placeholder="Platform (e.g., LinkedIn)" style="width: 40%;">
			<input type="url" name="team_member_social_links[new][url]" placeholder="URL" style="width: 40%;">
			<button type="button" class="remove-social-link button" style="display: none;">Remove</button>
		</div>
	</div>
	<button type="button" id="add-social-link" class="button">Add Social Link</button>

	<script>
		jQuery(document).ready(function($) {
			$('#add-social-link').on('click', function() {
				var newRow = $('.social-link-row:last').clone();
				newRow.find('input').val('');
				newRow.find('input[name*="new"]').each(function() {
					var name = $(this).attr('name').replace('new', Date.now());
					$(this).attr('name', name);
				});
				newRow.find('.remove-social-link').show();
				$('#social-links-container').append(newRow);
			});

			$(document).on('click', '.remove-social-link', function() {
				if ($('.social-link-row').length > 1) {
					$(this).closest('.social-link-row').remove();
				}
			});
		});
	</script>
<?php
}

// Save metabox data
add_action('save_post', 'team_member_save_meta');
function team_member_save_meta($post_id)
{
	if (!isset($_POST['team_member_nonce']) || !wp_verify_nonce($_POST['team_member_nonce'], 'team_member_save_meta')) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	if (!current_user_can('edit_post', $post_id)) {
		return;
	}

	// Save Job Title
	if (isset($_POST['team_member_job_title'])) {
		update_post_meta($post_id, '_team_member_job_title', sanitize_text_field($_POST['team_member_job_title']));
	}

	// Save Bio
	if (isset($_POST['team_member_bio'])) {
		update_post_meta($post_id, '_team_member_bio', wp_kses_post($_POST['team_member_bio']));
	}

	// Save Social Links
	$social_links = array();
	if (isset($_POST['team_member_social_links']) && is_array($_POST['team_member_social_links'])) {
		foreach ($_POST['team_member_social_links'] as $index => $link) {
			if ($index === 'new' || empty($link['platform']) || empty($link['url'])) {
				continue;
			}
			$social_links[] = array(
				'platform' => sanitize_text_field($link['platform']),
				'url'      => esc_url_raw($link['url']),
			);
		}
	}
	update_post_meta($post_id, '_team_member_social_links', $social_links);
}