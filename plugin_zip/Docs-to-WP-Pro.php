<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Docs to WP Pro
 * Plugin URI:        https://www.docstowp.pro
 * Description:       Plugin to interact with the "Docs to WP Pro" Google Docs Editor add-on that allows you to SEO-optimize your posts with smart internal links and helps you push the content from Google Docs to WordPress.
 * Version:           1.0.0
 * Author:            Vikram Aruchamy
 * Author URI:        https://twitter.com/vikramaruchamy/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       docs-to-wp-pro
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('DOCS_TO_WP_PRO_VERSION', '1.0.0');

/* Register activation hook. */
register_activation_hook(__FILE__, 'docs_to_wp_pro_activation_hook');

/**
 * Runs only when the plugin is activated.
 * @since 1.0.2
 */
function docs_to_wp_pro_activation_hook()
{

	/* Create transient data */
	set_transient('docs-to-wp-pro-activation-notice', true, 5);
}

/* Add admin notice */
add_action('admin_notices', 'docs_to_wp_pro_notice');


/**
 * Admin Notice on Activation.
 * @since 1.0.0
 */
function docs_to_wp_pro_notice()
{
	/* Check transient, if available display notice */
	if (get_transient('docs-to-wp-pro-activation-notice')) {
?>
		<style>
			div#message.updated {
				display: none;
			}
		</style>
		<div class="updated notice is-dismissible">
			<p><?php esc_html_e('Thank you for installing the Docs to WP Pro plugin.', 'docs-to-wp-pro'); ?></p>
			<p><?php esc_html_e('Complete the installation by installing the Google Docs Editor add-on and start enjoying one-click Google Docs to WordPress publishing.', 'docs-to-wp-pro'); ?></p>
			<div>
				<a href="<?php echo esc_url('https://workspace.google.com/marketplace/app/docs_to_wp_pro/346830534164?pann=b&utm_source=wordpress&utm_medium=plugin-activation-notice'); ?>" target="_blank" aria-label="<?php esc_attr_e('Get it from the Google Workspace Marketplace', 'docs-to-wp-pro'); ?>">
					<img alt="<?php esc_attr_e('Google Workspace Marketplace badge', 'docs-to-wp-pro'); ?>" alt-text="<?php esc_attr_e('Get it from the Google Workspace Marketplace', 'docs-to-wp-pro'); ?>" src="https://workspace.google.com/static/img/marketplace/en/gwmBadge.svg?" style="height: 68px">
				</a>
			</div>
		</div>
<?php
		/* Delete transient, only display this notice once. */
		delete_transient('docs-to-wp-pro-activation-notice');
	}
}



// Add a custom page for instructions
function docs_to_wp_pro_instructions_page_content()
{
	// The content of the instructions page goes here:
	echo '<h1>Docs to WP Pro Instructions</h1>';
	echo '<p>Here are the instructions for using the Docs to WP Pro plugin:</p>';
	echo '<ul>';
	echo '<li>Step 1: Install the Docs to WP Pro add-on from the Google Workspace Marketplace.</li>';
	echo '<li>Step 2: Publish your content from Google Docs to WordPress with a single click.</li>';
	echo '</ul>';
}

function docs_to_wp_pro_create_home_page()
{
	//remove_submenu_page('Docs to WP Pro','docs-to-wp-pro'); 
	$icon = plugin_dir_url(__FILE__) . 'assets/images/icon-20x20.png';
	add_menu_page('Docs to WP Pro', 'Docs to WP Pro', 'manage_options', 'docs-to-wp-pro', 'docs_to_wp_pro_home_page_content', $icon, 76);
	//add_submenu_page('docs-to-wp-pro', 'Docs to WP Instructions', 'Instructions', 'manage_options', 'docs-to-wp-pro-instructions', 'docs_to_wp_pro_instructions_page_content');
}

add_action('admin_menu', 'docs_to_wp_pro_create_home_page');

function docs_to_wp_pro_home_page_content()
{
	// The content of the page goes here:
	echo '<h1>Docs to WP Pro Plugin</h1>';
	echo '<p>Docs to WP Pro is a plugin that allows you to publish content from Google Docs to WordPress.</p>';
	echo '<p>This plugin allows the Docs to WP Pro Google Docs editor add on to interact with your WordPress site and update the RankMath and the Yoast SEO meta data.</p>';
	echo '<p>To use this plugin, please install the Docs to WP Pro add on from the Google Workspace Marketplace. Then you can publish to WordPress to Google Docs in a single click.</p>';
	echo '<div><a href="https://workspace.google.com/marketplace/app/docs_to_wp_pro/346830534164?pann=b&utm_source=wordpress&utm_medium=plugin-home-page" target="_blank" aria-label="Get it from the Google Workspace Marketplace">
<img alt="Google Workspace Marketplace badge" alt-text="Get it from the Google Workspace Marketplace" src="https://workspace.google.com/static/img/marketplace/en/gwmBadge.svg?" style="height: 68px">
</a></div>';
}

add_filter('plugin_row_meta', 'docs_to_wp_pro_userful_links', 10, 4);

function docs_to_wp_pro_userful_links($links_array, $plugin_file_name, $plugin_data, $status)
{
	// Check if the current plugin file matches your plugin
	if (strpos($plugin_file_name, basename(__FILE__)) !== false) {
		// Add FAQ link
		$install_editor_add_on_link = '<a href="https://workspace.google.com/marketplace/app/docs_to_wp_pro/346830534164?utm_source=wordpress&utm_medium=plugin-description" target="_blank">Install Docs to WP Pro Google Docs Editor Addon</a>';
		$links_array[] = $install_editor_add_on_link;

		// Add Support link
		$support_link = '<a href="https://www.docstowp.pro/contact-us?utm_source=wordpress&utm_medium=plugin-description" target="_blank">Support</a>';
		$links_array[] = $support_link;
	}

	return $links_array;
}

function register_rest_meta_endpoints()
{
	register_rest_route('/docs-to-wp-pro/v1', '/check', array(
		'methods' => 'GET',
		'callback' => 'return_info',
		'permission_callback' => 'docstowppro_check_permissions_callback'
	));

	register_rest_route('/docs-to-wp-pro/v1', '/check-plugin-installation', array(
		'methods' => 'POST',
		'callback' => 'docstowppro_check_plugin_installation',
		'permission_callback' => 'docstowppro_check_permissions_callback'
	));

	register_rest_route('/docs-to-wp-pro/v1', '/update-seo-meta', array(
		'methods' => 'POST',
		'callback' => 'docstowppro_plugin_update_seo_meta',
		'permission_callback' => 'docstowppro_check_permissions_callback'
	));
}

add_action('rest_api_init', 'register_rest_meta_endpoints');

function docstowppro_check_permissions_callback($request)
{
	// Retrieve the Authorization header from the request
	$auth_header = $request->get_header('Authorization');

	// Check if the Authorization header is present and starts with 'Basic'
	if ($auth_header && strpos($auth_header, 'Basic') === 0) {
		// Decode the username and password from the Authorization header
		$auth_data = explode(' ', $auth_header);
		$auth_data = base64_decode($auth_data[1]);
		list($username, $password) = explode(':', $auth_data);

		// Authenticate using the application password
		$user = wp_authenticate_application_password(null, $username, $password);

		if (is_wp_error($user)) {
			// Authentication failed
			return new WP_Error('authentication_failed', $user->get_error_message(), array('status' => 401));
		} else {
			// Check if the user has 'edit_posts' capability
			if (user_can($user, 'edit_posts')) {
				// User has permission to edit posts
				return true;
			} else {
				// User does not have permission to edit posts
				return new WP_Error('insufficient_permissions', __('You do not have permission to edit posts.', 'docs-to-wp-pro'), array('status' => 403));
			}
		}
	} else {
		// If Authorization header is missing or not in the correct format, return an error response
		return new WP_Error('authorization_header_missing', 'Authorization header is missing or in incorrect format.', array('status' => 401));
	}
}

function return_info()
{
	return array(
		'message' => 'This message shows that the Docs to WP Pro plugin was successfully installed. You can now update the Meta descriptions from the Docs to WP Pro Add on.',
	);
}
function docstowppro_check_plugin_installation($request)
{
	$seo_plugin = $request->get_param('SEOPlugin');

	switch ($seo_plugin) {
		case 'RankMath':
			if (!function_exists('rank_math')) {
				$error = new WP_Error(
					'plugin_not_installed',
					'The Rank Math plugin is not installed. Please install it in your WordPress site and try again.',
					array('status' => 501)
				);
				return $error;
			}
			break;

		case 'Yoast':
			if (!class_exists('WPSEO_Options')) {
				$error = new WP_Error(
					'plugin_not_installed',
					'The Yoast plugin is not installed. Please install it in your WordPress site and try again.',
					array('status' => 501)
				);
				return $error;
			}
			break;

		default:
			return new WP_Error('Invalid_seo_plugin', 'Invalid SEO plugin specified.', array('status' => 400));
	}

	return new WP_REST_Response(array('message' => $seo_plugin . ' is installed on your system'), 200);
}


// Update SEO meta fields
function docstowppro_plugin_update_seo_meta($request)
{
	$seo_plugin = $request->get_param('SEOPlugin');

	// Check which SEO plugin is specified
	if ($seo_plugin === 'RankMath') {
		return docstowppro_update_rankmath($request);
	} elseif ($seo_plugin === 'Yoast') {
		return docstowppro_update_yoast($request);
	} else {
		return new WP_Error('Invalid_seo_plugin', 'Invalid SEO plugin specified.', array('status' => 400));
	}
}

// Update Rank Math SEO meta fields
function docstowppro_update_rankmath($request)
{
	if (!function_exists('rank_math')) {
		$error = new WP_Error(
			'plugin_not_installed',
			'The Rank Math plugin is not installed. Please install it in your WordPress site and try again.',
			array('status' => 501)
		);
		return $error;
	}

	$post_id = $request->get_param('postId');
	$focus_keyword = $request->get_param('focusKeyword');
	$meta_description = $request->get_param('metaDescription');
	$title = $request->get_param('title');

	// Check for required parameters
	if (empty($post_id)) {
		return new WP_Error('invalid_parameters', 'Invalid parameters for Rank Math update.', array('status' => 400));
	}

	// Update the focus keyword and description for Rank Math
	if (!empty($focus_keyword)) {
		update_post_meta($post_id, 'rank_math_focus_keyword', $focus_keyword);
	}

	if (!empty($meta_description)) {
		update_post_meta($post_id, 'rank_math_description', $meta_description);
	}
	if (!empty($title)) {
		update_post_meta($post_id, 'rank_math_title', $title);
	}

	return new WP_REST_Response(array('message' => 'RankMath meta fields are updated.'), 200);
}

// Update Yoast SEO meta fields
function docstowppro_update_yoast($request)
{
	if (!class_exists('WPSEO_Options')) {
		$error = new WP_Error(
			'plugin_not_installed',
			'The Yoast plugin is not installed. Please install it in your WordPress site and try again.',
			array('status' => 501)
		);
		return $error;
	}

	$post_id = $request->get_param('postId');
	$focus_keyword = $request->get_param('focusKeyword');
	$title = $request->get_param('title');
	$meta_description = $request->get_param('metaDescription');


	// Check for required parameters
	if (empty($post_id)) {
		return new WP_Error('invalid_parameters', 'Invalid parameters for Yoast SEO update.', array('status' => 400));
	}

	// Update Yoast SEO meta fields
	$args = array(
		'ID' => $post_id,
		'meta_input' => array(
			'_yoast_wpseo_focuskw' => $focus_keyword,
			'_yoast_wpseo_title' => $title,
			'_yoast_wpseo_metadesc' => $meta_description
		),
	);

	$result = wp_update_post($args);

	if (is_wp_error($result)) {
		return new WP_Error('update_failed', __('Yoast SEO update failed. Please try again', 'docstowppro'), array('status' => 500));
	}

	return new WP_REST_Response(array('message' => 'Yoast Meta fields have been updated.'), 200);
}
