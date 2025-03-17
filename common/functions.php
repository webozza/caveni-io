<?php

/**
 * Callback function for checking ticket permissions.
 *
 * @since    1.0.0
 * @param    array $req_data array of GET parameters pass in shortcode.
 */


// For the local development server
//  error_reporting(E_ALL);
//  ini_set('display_errors', 1);

function check_current_user_ticket_permissions($req_data)
{


	if (is_user_admin_or_in_array(array())) {
		$user_permission['status'] = 'success';
		$user_permission['message'] = __('Correct Permission', 'caveni-io');
		return $user_permission;
	}

	$user_permission = array(
		'status' => 'failed',
		'message' => '',
	);

	if (!is_user_logged_in()) {
		$user_permission['message'] = __('User is not logged in', 'caveni-io');
		return $user_permission;
	}

	$user = wp_get_current_user();
	if (isset($req_data['_ticket_id'])) {
		$ticket_id = (int) $req_data['_ticket_id'];
		$c_ticket = get_post($ticket_id);
		if ($c_ticket && $c_ticket->post_author != $user->ID) {
			$user_permission['message'] = __('You cannot edit other users tickets', 'caveni-io');
			return $user_permission;
		}
	}

	$user_permission['status'] = 'success';
	$user_permission['message'] = __('Correct Permission', 'caveni-io');
	return $user_permission;
}

/**
 * Callback function for getting all tickets.
 *
 * @since    1.0.0
 * @param    array $req_data array of GET parameters pass in shortcode.
 */
function get_all_user_tickets()
{
	global $current_user;

	$args = array(
		'author' => $current_user->ID,
		'post_type'   => CAVENI_IO_POST_TYPE,
		'numberposts' => -1
	);

	if (current_user_can('manage_options')) {
		$args['author'] = '';
	}
	//if($by = 'status' && )
	return get_posts($args);
}

function get_all_client_users($role = 'contributor')
{
	$args = array(
		'role'    => $role, // Specify the role to filter users (e.g., 'subscriber', 'client', etc.)
		'orderby' => 'display_name',
		'order'   => 'ASC',
		'fields'  => array('ID', 'display_name', 'user_email') // Adjust fields as needed
	);

	// Use get_users() to fetch all users matching the criteria
	$users = get_users($args);

	$clients = array();

	foreach ($users as $user) {
		$clients[] = array(
			'ID'           => $user->ID,
			'display_name' => $user->display_name,
			'email'        => $user->user_email
		);
	}

	return $clients; // Return the list of clients
}


function get_ticket_by_id($ticket_id = 0)
{
	if (! $ticket_id) return null;
	return get_post($ticket_id);
}

/**
 * Callback function for getting all tickets by status.
 *
 * @since    1.0.0
 * @param    array $status type open or close.
 */
function get_user_tickets_by_status($status = 'open')
{
	global $current_user;

	$args = array(
		'author' => $current_user->ID,
		'post_type'   => CAVENI_IO_POST_TYPE,
		'meta_key' => '_caveni_ticket_status',
		'numberposts' => -1,
		'meta_value' => $status,
		'order' => 'ASC'
	);

	if (current_user_can('manage_options')) {
		$args['author'] = '';
	}
	//if($by = 'status' && )
	return get_posts($args);
}

/**
 * Callback function for getting all tickets by status.
 *
 * @since 1.0.0
 */
function get_all_ticket_terms()
{
	return get_terms(CAVENI_IO_POST_TAXONOMY, array('hide_empty' => false));
}
/**
 * Check user roles is exist
 *
 * @since 1.0.0
 * @param  array $roles_array name user roles.
 */
function is_user_admin_or_in_array($roles_array)
{
	// Get the current user.
	$current_user = wp_get_current_user();
	// Check if the user has the 'administrator' role.
	if (in_array('administrator', $current_user->roles, true)) {
		return true;
	}
	// Check if the user has any role from the provided array.
	foreach ($current_user->roles as $role) {
		if (in_array($role, $roles_array, true)) {
			return true;
		}
	}
	return false; // Not an admin and not in the specified roles.
}
/**
 * Check ticket post type enable or not.
 *
 * @since 1.0.0
 */
function check_ticket_enable()
{
	$settings = get_option('caveni_settings');
	if ((isset($settings['enable'])  && (is_array($settings['role']) &&  is_user_admin_or_in_array($settings['role'])))) {
		return true;
	}
	return false;
}


/**
 * Check messages enable or not.
 *
 * @since 1.0.0
 */
function check_messags_enable()
{
	$settings = get_option('caveni_settings');
	if ((isset($settings['messages_enable']))) {
		return true;
	}
	return false;
}

function get_comments_by_ticket_id($ticket_id = 0)
{

	if (!$ticket_id) return false;

	$args = array(
		'number'  => 0,
		'post_id' => $ticket_id, // use post_id, not post_ID
	);

	return get_comments($args);
}


function get_minutes_ago_time($posted = 0, $lastmodified = 0)
{

	if ($lastmodified > $posted) {
		return human_time_diff($lastmodified, current_time('U')) . " ago";
	}
	return human_time_diff($posted, current_time('U')) . " ago";
}

function get_last_commented_by($user_id = 0, $ticket)
{

	if ($user_id <= 0) {
		return __("Last Updated", "caveni-io");
	}

	if (check_if_user_is_admin($user_id)) {
		return __("last commented by admin", "caveni-io");
	} else if ($ticket->post_author == $user_id) {
		return __("last commented by client", "caveni-io");
	} else {
		return __("last commented by user", "caveni-io");
	}
}

function get_all_ticket_categories()
{

	if (! current_user_can('manage_options')) return false;

	return get_terms(array(
		'taxonomy'   => CAVENI_IO_POST_TAXONOMY,
		'hide_empty' => false,
	));
}

function check_if_user_is_admin($user_id = 0)
{

	if ($user_id <= 0) return false;

	$user = get_user_by('id', $user_id);
	if (!$user) return false;

	$allowed_roles = array('administrator');
	if (array_intersect($allowed_roles, $user->roles)) {
		return true;
	}

	return false;
}


function get_user_attachment_by_comment_id($comment_id = 0, $size = 'thumbnail')
{

	if ($comment_id <= 0) return '';

	$comment_attachment_id = get_comment_meta($comment_id, '_ticket_comment_reply_attachment', true);
	//var_dump($comment_attachment_id);exit;
	if ($comment_attachment_id) {
		return wp_get_attachment_image_src($comment_attachment_id, $size);
	}

	return '';
}

function get_user_attachment_by_ticket_id($ticket_id = 0, $size = 'thumbnail')
{

	if ($ticket_id <= 0) return '';

	$attachment_id = get_post_meta($ticket_id, '_caveni_tickets_file', true);

	if (empty($attachment_id) || $attachment_id <= 0) return '';

	$file = get_attached_file($attachment_id, true);

	if (empty($size) || $size === 'full') {
		// for the original size get_attached_file is fine
		return realpath($file);
	}

	if (! wp_attachment_is_image($attachment_id)) {
		return false; // the id is not referring to a media
	}
	$info = image_get_intermediate_size($attachment_id, $size);
	var_dump($info);
	exit;
	if (!is_array($info) || ! isset($info['file'])) {
		return false; // probably a bad size argument
	}

	return realpath(str_replace(wp_basename($file), $info['file'], $file));
}

function get_wp_scaled_image_url($post_id, $size = 'thumbnail', $is_comment = false)
{

	if ($post_id <= 0) return '';
	$attachment_id = 0;
	if ($is_comment) {
		$attachment_id = get_comment_meta($post_id, '_ticket_comment_reply_attachment', true);
	} else {
		$attachment_id = get_post_meta($post_id, '_caveni_tickets_file', true);
	}

	if (empty($attachment_id) || $attachment_id <= 0) return '';
	$file = get_attached_file_url_with_correct_path($attachment_id, true);
	if (empty($size) || $size === 'full') {
		// for the original size get_attached_file is fine
		return $file;
	}
	if (! wp_attachment_is_image($attachment_id)) {
		return $file; // the id is not referring to a media
	}
	$info = image_get_intermediate_size($attachment_id, $size);
	// /echo "<pre>";var_dump($info);exit;
	if (!is_array($info) || ! isset($info['file'])) {
		return false; // probably a bad size argument
	}

	$upload_dir = wp_upload_dir();
	$file_url = $upload_dir['baseurl'] . '/' . $info['path'];

	return $file_url;
}

function get_attached_file_url_with_correct_path($attachment_id, $unfiltered = false)
{
	$file = get_post_meta($attachment_id, '_wp_attached_file', true);

	// If the file is relative, prepend upload dir.
	if ($file && ! str_starts_with($file, '/') && ! preg_match('|^.:\\\|', $file)) {
		$uploads = wp_get_upload_dir();
		if (false === $uploads['error']) {
			$file = $uploads['url'] . "/$file";
		}
	}

	if ($unfiltered) {
		return $file;
	}

	/**
	 * Filters the attached file based on the given ID.
	 *
	 * @since 2.1.0
	 *
	 * @param string|false $file          The file path to where the attached file should be, false otherwise.
	 * @param int          $attachment_id Attachment ID.
	 */
	return apply_filters('get_attached_file_url', $file, $attachment_id);
}

function get_support_amdin()
{
	$settings = get_option('caveni_settings');
	if (! isset($settings['staff']) || isset($settings['staff']) && $settings['staff'] == "") {
		return 1;
	}

	return (int) $settings['staff'];
}

function get_user_roles_setting()
{
	$settings = get_option('caveni_settings');

	if (isset($settings['role']) && !empty($settings['role'])) {
		return $settings['role'];
	}

	return array();
}

function send_email_action($ticket_created = true, $data, $send_to = 'admin')
{

	$settings = get_option('caveni_settings');
	if (! isset($settings['email_enable'])) {
		return false;
	}
	$from_data = (isset($settings['from_email']) && $settings['from_email']  != "" && is_valid_email($settings['from_email'])) ? trim($settings['from_email']) : get_option('admin_email');
	$subject_data = __("Caveni.IO", "caveni-io");
	$body = __("Caveni.IO", "caveni-io");
	$to = '';
	$ticket_link = '';
	$ticket_link_text = __("View Ticket", "caveni-io");
	$to = get_option('admin_email');
	if (isset($settings['staff']) && $settings['staff'] != "") {
		$user_info = get_userdata($settings['staff']);
		if ($user_info) {
			$to = $user_info->user_email;
		}
	}
	if ($ticket_created) {
		$subject_data = (isset($settings['ticket_email_subject']) && $settings['ticket_email_subject']  != "") ? trim($settings['ticket_email_subject']) : __("Caveni.IO", "caveni-io");
		$body = (isset($settings['ticket_email_content']) && $settings['ticket_email_content']  != "") ? trim($settings['ticket_email_content']) : __("Caveni.IO", "caveni-io");
		$id = $data->ID;
		$description = $data->post_content;
		$author = $data->post_author;
		$author_info = get_userdata($author);
		$author_user_name = $author_info->user_login;
		$subject = $subject_data;
		$title = $data->post_title;
		$category_detail = get_the_terms($data, CAVENI_IO_POST_TAXONOMY);
		if ($category_detail) {
			$category = $category_detail[0]->name;
		}

		$ticket_link = home_url("/helpdesk") . "/?_ticket_action=view&_ticket_id=" . $id;
	} else {
		$comment_ticket_id = $data->comment_post_ID;
		if ($send_to == 'admin') {
			$subject_data = (isset($settings['admin_reply_email_subject']) && $settings['admin_reply_email_subject']  != "") ? trim($settings['admin_reply_email_subject']) : __("Caveni.IO", "caveni-io");
			$body = (isset($settings['admin_reply_email_content']) && $settings['admin_reply_email_content']  != "") ? trim($settings['admin_reply_email_content']) : __("Caveni.IO", "caveni-io");
			$client_id = get_post_field('post_author', $comment_ticket_id);
			$to = get_the_author_meta('user_email', $client_id);
		} else {
			$subject_data = (isset($settings['client_reply_email_subject']) && $settings['client_reply_email_subject']  != "") ? trim($settings['client_reply_email_subject']) : __("Caveni.IO", "caveni-io");
			$body = (isset($settings['client_reply_email_content']) && $settings['client_reply_email_content']  != "") ? trim($settings['client_reply_email_content']) : __("Caveni.IO", "caveni-io");
		}
		$id = $data->comment_ID;
		$description = $data->comment_content;
		$author = $data->user_id;
		$author_info = get_userdata($author);
		$author_user_name = $author_info->user_login;
		$subject = $subject_data;
		$title = '';
		$category = '';
		$ticket_link = home_url("/helpdesk") . "/?_ticket_action=view&_ticket_id=" . $comment_ticket_id;
	}


	//$to = 'xfinitysoftdemo@gmail.com';

	$subject = str_replace(
		array(
			'{site_title}',
			'{user_name}',
		),
		array(
			get_bloginfo('name'),
			$author_user_name
		),
		$subject
	);

	$body = str_replace(
		array(
			'{site_title}',
			'{title}',
			'{category}',
			'{user_name}',
			'{ticket_message}',
			'{reply_message}',
			'{ticket_link}'
		),
		array(
			get_bloginfo('name'),
			$title,
			$category,
			$author_user_name,
			$description,
			$description,
			sprintf("<a href='%s' target='_blank'>%s</a>", $ticket_link, $ticket_link_text)
		),
		$body
	);

	$headers = array('Content-Type: text/html; charset=UTF-8', 'From: Caveni.IO <' . $from_data . '>');

	wp_mail($to, $subject, $body, $headers);
	return true;
}

function send_ticket_created_email() {}

function send_ticket_reply_email() {}

function is_valid_email($email)
{
	return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}


function posts_for_current_author($query)
{
	global $pagenow;

	if ($query->query['post_type'] != "caveni_ticket" || 'edit.php' != $pagenow || !$query->is_admin)
		return $query;


	if (!current_user_can('manage_options')) {
		global $user_ID;
		$query->set('author', $user_ID);
	}
	return $query;
}
add_filter('pre_get_posts', 'posts_for_current_author');

function my_nav_menu_item_custom_fields($item_id, $item, $depth, $args, $id)
{
	// Check for user roles
	if (current_user_can('administrator')) {
		// Add nonce field for security
		wp_nonce_field('save_menu_item_custom_fields', 'menu_item_custom_nonce');
?>
		<p class="field-custom description description-wide">
			<label for="edit-menu-item-role-<?php echo $item_id; ?>">
				<?php _e('Select Caveni.IO Module', 'caveni-io'); ?><br />
				<select id="edit-menu-item-role-<?php echo $item_id; ?>" name="menu-item-role[<?php echo $item_id; ?>]">
					<option value=""><?php _e('None', 'caveni-io'); ?></option>
					<option value="1" <?php selected('1', get_post_meta($item_id, '_menu_item_role', true)); ?>><?php _e('Helpdesk', 'caveni-io'); ?></option>
				</select>
			</label>
		</p>
<?php
	}
}
add_action('wp_nav_menu_item_custom_fields', 'my_nav_menu_item_custom_fields', 10, 5);

function my_update_nav_menu_item($menu_id, $menu_item_db_id)
{
	// Check if nonce is set and valid
	if (!isset($_POST['menu_item_custom_nonce']) || !wp_verify_nonce($_POST['menu_item_custom_nonce'], 'save_menu_item_custom_fields')) {
		return;
	}

	// Check if the current user has permission to edit
	if (!current_user_can('edit_theme_options')) {
		return;
	}

	// Save the custom field value
	if (isset($_POST['menu-item-role'][$menu_item_db_id])) {
		$role_value = sanitize_text_field($_POST['menu-item-role'][$menu_item_db_id]);
		update_post_meta($menu_item_db_id, '_menu_item_role', $role_value);
	}
}
add_action('wp_update_nav_menu_item', 'my_update_nav_menu_item', 10, 2);

function my_filter_nav_menu_items($items, $args)
{
	$user = wp_get_current_user();
	$filtered_items = array();

	// Loop through menu items and check user role
	foreach ($items as $item) {
		$menu_item_role = get_post_meta($item->ID, '_menu_item_role', true);
		// Show item if no role is set or if the user has the required role
		if (empty($menu_item_role)) {
			$filtered_items[] = $item;
		} else {
			if ($menu_item_role == 1) {
				$allowed_roles = get_user_roles_setting();

				if (check_ticket_enable() && is_user_admin_or_in_array($allowed_roles)) {
					$filtered_items[] = $item;
				}
			}
		}
	}

	return $filtered_items;
}
add_filter('wp_get_nav_menu_items', 'my_filter_nav_menu_items', 10, 2);

function get_admin_user_ids()
{
	$args = array(
		'role'    => 'administrator',
		'fields'  => 'ID'
	);

	$user_query = new WP_User_Query($args);
	return $user_query->results;
}
function get_date_separator($old_date)
{
	$new_date = current_time('mysql');
	$old_date_obj = new DateTime($old_date);
	$new_date_obj = new DateTime($new_date);

	$old_date_formatted = $old_date_obj->format('Y-m-d');
	$new_date_formatted = $new_date_obj->format('Y-m-d');


	// echo $old_date_formatted . '===' . $new_date_formatted . "<br><br><br>";
	if ($old_date_formatted === $new_date_formatted) {
		return 'Today';
	}
	$yesterday = $new_date_obj->modify('-1 day')->format('Y-m-d');
	if ($old_date_formatted === $yesterday) {
		return 'Yesterday';
	}
	return $old_date_obj->format('F j, Y');
}

function validate_form_client_data($data)
{
	$errors = array();


	// if (empty($data['caveni_client_id'])) {
	// 	$errors['caveni_client_id'] = __("The cilent id field is required.", "caveni-io");
	// }


	if (empty($data['caveni_client_username'])) {
		$errors['caveni_client_username'] = __("The username field is requried.", "caveni-io");
	}
	// if (empty($data['caveni_client_firstname'])) {
	// 	$errors['caveni_client_firstname'] = __("The First name field is requried.", "caveni-io");
	// }
	// if (empty($data['caveni_client_lastname'])) {
	// 	$errors['caveni_client_lastname'] = __("The Last name field is requried.", "caveni-io");
	// }
	if (empty($data['caveni_client_email'])) {
		$errors['caveni_client_email'] = __("The Email field is requried.", "caveni-io");
	}

	// if (empty($data['caveni_client_phone'])) {
	// 	$errors['caveni_client_phone'] = __("The phone no field is required.", "caveni-io");
	// }

	if (empty($data['caveni_client_web'])) {
		$errors['caveni_client_web'] = __("The client url field is required.", "caveni-io");
	}
	if (empty($data['caveni_company_name'])) {
		$errors['caveni_company_name'] = __("The company field is requried.", "caveni-io");
	}

	// if (empty($data['caveni_client_city'])) {
	// 	$errors['caveni_client_city'] = __("The city field is required.", "caveni-io");
	// }

	// if (empty($data['caveni_client_state'])) {
	// 	$errors['caveni_client_state'] = __("The state field is required.", "caveni-io");
	// }
	// if (empty($data['caveni_client_zip'])) {
	// 	$errors['caveni_client_zip'] = __("The zipcode field is required.", "caveni-io");
	// }

	// if (empty($data['caveni_client_country'])) {
	// 	$errors['caveni_client_country'] = __("The country field is required.", "caveni-io");
	// }

	// if (empty($data['caveni_client_address'])) {
	// 	$errors['caveni_client_address'] = __("The address field is required.", "caveni-io");
	// }

	return $errors;
}
function get_buddypress_profile_picture($user_id, $size = '150')
{
	// Fetch the avatar URL
	$avatar_url = bp_core_fetch_avatar(array(
		'item_id' => $user_id,
		'type'    => 'thumb', // You can change this to 'thumb' for a smaller image
		'width'   => $size,
		'height'  => $size,
		'html'    => false // Set to true if you want the HTML <img> tag
	));

	return $avatar_url;
}
function get_all_role_titles($roleID)
{
	global $wp_roles;
	if (!isset($wp_roles)) {
		$wp_roles = new WP_Roles();
	}
	$roles = $wp_roles->roles;
	foreach ($roles as $role_key => $role) {
		if ($role_key == $roleID) {
			return $role['name'];
		}
	}
	return;
}

function redirect_non_admin_users()
{
	// Check if the current page is the specific page
	if (is_page('clients') && !current_user_can('administrator')) {
		// Redirect to another page (e.g., homepage)
		wp_redirect(home_url());
		exit; // Always call exit() after wp_redirect
	}
}
add_action('template_redirect', 'redirect_non_admin_users');

// By @webozza
include CAVENI_IO_PATH . 'includes/apis/ga4.php';
include CAVENI_IO_PATH . 'includes/modules/report-handler.php';

function convertToDate($data_value, $is_previous = false) {
    if (preg_match('/^(\d+)daysAgo$/', $data_value, $matches)) {
        $daysAgo = intval($matches[1]);

        if ($is_previous) {
            // Shift the period backwards for the previous period
            $newDaysAgo = $daysAgo * 2; // Previous period starts twice the range
            return date('Y-m-d', strtotime('-' . $newDaysAgo . ' days'));
        }

        return date('Y-m-d', strtotime('-' . $daysAgo . ' days'));
    } elseif ($data_value === 'yesterday') {
        return $is_previous ? date('Y-m-d', strtotime('-2 days')) : date('Y-m-d', strtotime('-1 day'));
    } elseif ($data_value === 'today') {
        return $is_previous ? date('Y-m-d', strtotime('-1 day')) : date('Y-m-d');
    } else {
        return $data_value; // Assume already in 'Y-m-d'
    }
}

function splitDateRange($start_date, $end_date = null, $timezone = 'UTC') {
	// Set correct timezone
	date_default_timezone_set($timezone);
	
	$yesterday = new DateTime('yesterday', new DateTimeZone($timezone));
	$yesterday_str = $yesterday->format('Y-m-d');

	// **If "NdaysAgo" format is used**
	if (preg_match('/^(\d+)daysAgo$/', $start_date, $matches)) {
		$total_days = intval($matches[1]);
		$midpoint = intval($total_days / 2);

		// Use DateTime for timezone consistency
		$current_start = clone $yesterday;
		$current_start->modify("-{$midpoint} days")->modify('+1 day'); 
		$current_end = clone $yesterday;

		$previous_start = clone $yesterday;
		$previous_start->modify("-{$total_days} days")->modify('+1 day'); 
		$previous_end = clone $current_start;
		$previous_end->modify('-1 day');

		return [
			'current' => ['start' => $current_start->format('Y-m-d'), 'end' => $current_end->format('Y-m-d')],
			'previous' => ['start' => $previous_start->format('Y-m-d'), 'end' => $previous_end->format('Y-m-d')],
		];
	}

	// **If "YYYY-MM-DD" format is used**
	if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
		$start_dt = new DateTime($start_date, new DateTimeZone($timezone));
		$end_dt = new DateTime($end_date, new DateTimeZone($timezone));

		// Calculate the range duration (number of days)
		$days_difference = $start_dt->diff($end_dt)->days + 1;

		// Mirror the exact same duration backwards
		$previous_start_date = clone $start_dt;
		$previous_start_date->modify("-{$days_difference} days");

		$previous_end_date = clone $start_dt;
		$previous_end_date->modify('-1 day');

		return [
			'current' => ['start' => $start_dt->format('Y-m-d'), 'end' => $end_dt->format('Y-m-d')],
			'previous' => ['start' => $previous_start_date->format('Y-m-d'), 'end' => $previous_end_date->format('Y-m-d')],
		];
	}

	// **Handle 'yesterday' and 'today' cases**
	if ($start_date === 'yesterday') {
		$two_days_ago = clone $yesterday;
		$two_days_ago->modify('-1 day');

		return [
			'current' => ['start' => $yesterday_str, 'end' => $yesterday_str],
			'previous' => ['start' => $two_days_ago->format('Y-m-d'), 'end' => $two_days_ago->format('Y-m-d')],
		];
	} elseif ($start_date === 'today') {
		$today = new DateTime('today', new DateTimeZone($timezone));

		return [
			'current' => ['start' => $today->format('Y-m-d'), 'end' => $today->format('Y-m-d')],
			'previous' => ['start' => $yesterday_str, 'end' => $yesterday_str],
		];
	}

	// **Default case: Return unchanged values**
	return [
		'current' => ['start' => $start_date, 'end' => $end_date ?: $start_date],
		'previous' => ['start' => $start_date, 'end' => $end_date ?: $start_date],
	];
}

function caveni_filter_settings_shortcode() {
    ob_start(); // Start output buffering

    $file_path = CAVENI_IO_PATH . 'public/partials/marketing/filter-settings.php';

    if (file_exists($file_path)) {
        include_once $file_path;
    } else {
        return '<p>Filter settings not found.</p>'; // Optional fallback message
    }

    return ob_get_clean(); // Return the buffered content
}
add_shortcode('caveni_filter_settings', 'caveni_filter_settings_shortcode');






// testing pdf styling
function seo_report_shortcode() {
    // Dummy data for testing
    $api_data_avg_position = [
        ["keyword" => "quick blinds", "avg_position" => "1.00", "trend" => "pre_high", "percentage_change" => "40.0%"],
        ["keyword" => "kiwiblinds", "avg_position" => "1.71", "trend" => "pre_low", "percentage_change" => "14.3%"],
        ["keyword" => "we blinds", "avg_position" => "1.75", "trend" => "pre_low", "percentage_change" => "0.0%"],
        ["keyword" => "blinds home visit", "avg_position" => "2.00", "trend" => "pre_high", "percentage_change" => "New"],
        ["keyword" => "vertical blinds", "avg_position" => "3.00", "trend" => "pre_low", "percentage_change" => "6.7%"],
    ];

    // Image paths (using pre-defined CAVENI_IO_URL)
    $green_data_bg = CAVENI_IO_URL . "public/images/green-data-bg.png";
    $red_data_bg = CAVENI_IO_URL . "public/images/red-data-bg.png";
    $indicator_up_img = CAVENI_IO_URL . "public/images/increase-indicator.png";
    $indicator_down_img = CAVENI_IO_URL . "public/images/decrease-indicator.png";

    // Start output buffering
    ob_start();
    ?>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            font-family: "Poppins", sans-serif;
            text-align: left;
            font-size: 14px;
            padding: 10px 12px;
            border: 1px solid #ddd;
        }
        th {
            font-weight: 600;
            background: #f4f4f4;
        }
        td.has-bg {
            background-size: cover;
            background-repeat: no-repeat;
            width: 100px;
            padding: 5px 12px;
        }
        td.has-bg .indicator-container {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 7px;
            font-size: 10px;
            font-weight: 500;
            gap: 6px;
        }
        td.has-bg.up .indicator-container {
            background: #e6faf4;
        }
        td.has-bg.down .indicator-container {
            background: #ffe9ec;
        }
        td.has-bg.up span {
            color: #0dcd94;
        }
        td.has-bg.down span {
            color: #f7284a;
        }
    </style>

    <h2>SEO Report</h2>
    <table>
        <thead>
            <tr>
                <th>Keyword</th>
                <th>Value</th>
                <th>Vs Prev</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($api_data_avg_position as $keyword): 
                $trend = $keyword['trend'];
                $percentage_change = $keyword['percentage_change'];
                $indicator_img = ($trend === 'pre_low') ? $indicator_down_img : $indicator_up_img;
                $bg_image = ($trend === 'pre_low') ? $red_data_bg : $green_data_bg;
                $trend_class = ($trend === 'pre_low') ? 'down' : 'up';
            ?>
            <tr>
                <td><?= htmlspecialchars($keyword['keyword']); ?></td>
                <td><?= htmlspecialchars($keyword['avg_position']); ?></td>
                <td class="has-bg <?= $trend_class; ?>" style="background-image: url('<?= $bg_image; ?>');">
                    <div class="indicator-container">
                        <img src="<?= $indicator_img; ?>" style="height: 16px;"> 
                        <span><?= htmlspecialchars($percentage_change); ?></span>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php
    return ob_get_clean();
}

// Register shortcode
add_shortcode('seo_report', 'seo_report_shortcode');

