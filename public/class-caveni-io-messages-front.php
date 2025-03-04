<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://xfinitysoft.com
 * @since      1.0.0
 *
 * @package    Caveni_Io
 * @subpackage Caveni_Io/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Caveni_Io
 * @subpackage Caveni_Io/public
 * @author     Xfinity Soft <email@example.com>
 */
class Caveni_Io_Messages_Front
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $caveni_io    The ID of this plugin.
	 */
	private $caveni_io;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $caveni_io       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct($caveni_io, $version)
	{
		$this->caveni_io = $caveni_io;
		$this->version   = $version;
	}

	/**
	 * Perform  the of shortcode messages.
	 *
	 * @since    1.0.0
	 * @param    array $attributes Paramter of shortcode array.
	 */
	public function perform_shortcode_action($attributes, $req_data)
	{

		// $user_permission = check_current_user_ticket_permissions($_GET);
		// if( $user_permission['status'] == 'failed'){
		// 	wp_redirect(home_url());
		// 	exit;
		// 	//return __( $user_permission['message'], 'caveni-io');
		// }

		// if(!check_ticket_enable()){
		// 	return __( "", 'caveni-io');
		// }

		// $action = 'list';
		// $ticket_id = 0;
		// if(isset($req_data['_ticket_action'])){
		// 	$action = $req_data['_ticket_action'];
		// }

		// if(isset($req_data['_ticket_id'])){
		// 	$ticket_id = (int) $req_data['_ticket_id'];
		// }

		// if($action == 'add'){

		// 	return $this->display_ticket_add();

		// }else if($action == 'edit' && $ticket_id > 0){

		// 	$ticket = get_ticket_by_id($ticket_id);
		// 	return $this->display_ticket_add(true, $ticket);

		// }else if( $action == 'view' &&  $ticket_id > 0){

		// 	$ticket = get_ticket_by_id($ticket_id);
		// 	if(!$ticket) {
		// 		wp_redirect(home_url());
		// 		exit;
		// 		//return __( "Ticket Does not Exist", 'caveni-io');
		// 	}else{
		// 		return $this->display_ticket_view($ticket);
		// 	}

		// }else{

		return $this->display_messages_list();

		// }
		// wp_redirect(home_url());
		// exit;
		//return __( $user_permission['message'], 'caveni-io');
	}

	/**
	 * show ticket list.
	 *
	 * @since    1.0.0
	 * 
	 */


	function get_clients_with_company($search = '')
	{

		$ticket_settings = get_option('caveni_settings');
		$messages_role = isset($ticket_settings['messages_role']) ? (array) $ticket_settings['messages_role'] : array();
		$args = array(
			'role__in' => $messages_role,
			'orderby' => 'display_name',
			'order'   => 'ASC'
		);
		if ($search) {
			$args['meta_query'] = array(
				array(
					'key' => 'company',
					'value' => $search,
					'compare' => 'LIKE'
				)
			);
		}

		// Get users
		$clients = get_users($args);


		$clientsList = array();


		if (!empty($clients)) {
			foreach ($clients as $client) {

				$company_username = get_user_meta($client->data->ID, 'company', true);

				if (empty($company_username)) {

					$company_username = $client->data->display_name;
				}
				$profileImg = $this->get_buddypress_profile_picture($client->data->ID);
				$group_id = (int) $this->get_group_id($client->data->ID);

				$clientsList[$client->data->ID] = array(
					"profileImg" =>  $profileImg,
					"user" =>  $client->data->ID,
					"group_id" =>  $group_id,
					"name" =>  $company_username,

				);
			}
		}



		return $clientsList;
	}

	// Function to get BuddyPress profile picture by user ID
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


	public function display_messages_list()
	{
		ini_set('display_errors', 1);

		$this->get_attachment_data(6158);
		ob_start();

		global $wpdb;
		$current_user = wp_get_current_user();
		$current_user_id = get_current_user_id();

		//pre($current_user->roles);
		$settings = get_option('caveni_settings');
		$enable = (isset($settings['messages_enable']) && $settings['messages_enable']) ? 1 : 0;
		$enabledRoles = isset($settings['messages_role']) ? $settings['messages_role'] : [];
		// pre($settings);
		$adminStaff = isset($settings['messages_staff']) ? (int) $settings['messages_staff'] : 0;
		if (array_intersect($enabledRoles, (array) $current_user->roles) && $enable &&  $adminStaff) {
			$admin_name = 'Caveni Team';
			$userProfile = CAVENI_IO_URL . 'public/images/admin-logo.png';
			$group_id = $this->get_group_id($current_user_id);
			$messages = [];
			if ($group_id) {
				$messages = $this->get_group_messages($group_id);
			}
			$recentChats = $this->get_recent_chat_for_client($current_user_id);
			$client_company = get_user_meta($current_user_id, 'company', true);
			if (empty($client_company)) {
				$client_company = $current_user->display_name;
			}
			$clientProfile = $this->get_buddypress_profile_picture($current_user_id);
			include_once CAVENI_IO_PATH . 'public/partials/messages/client-messages.php';
		} elseif (in_array('administrator', (array) $current_user->roles)) {

			//$messages =    $this->get_latest_messages_for_admin($current_user_id);
			$messages = [];
			$recentUser = $this->get_recent_chat_user();
			$company_username = '';
			if ($recentUser) {
				$userProfile = $this->get_buddypress_profile_picture($recentUser->user_id);
				$company_username = get_user_meta($recentUser->user_id, 'company', true);
				if (empty($company_username)) {
					$userData = get_userdata($recentUser->user_id);
					$company_username = $userData->display_name;
				}
				$messages = $this->get_group_messages($recentUser->group_id);
			}


			$clients = $this->get_clients_with_company();

			$recentChats = $this->get_recent_chat_list();

			include_once CAVENI_IO_PATH . 'public/partials/messages/admin-messages.php';
		}

		return ob_get_clean();
	}

	/**
	 * show ticket view.
	 *
	 * @since    1.0.0
	 * @param    object $ticket Paramter of current ticket.
	 */
	public function display_ticket_view($ticket)
	{

		ob_start();
		global $wp;
		$postedAtString = get_minutes_ago_time(get_the_modified_time('U', $ticket), get_the_time('U', $ticket));
		$ticket_status = get_post_meta($ticket->ID, '_caveni_ticket_status', true);
		$ticket_comments = get_comments_by_ticket_id($ticket->ID);
		$modified_by_text = __("Last Updated", "caveni-io");
		if (count($ticket_comments) > 0) {
			$last_comment = $ticket_comments[array_key_last($ticket_comments)];
			$modified_by_text = get_last_commented_by($last_comment->user_id, $ticket);
		} else {
			$modified_by_text = get_last_commented_by($ticket->post_author, $ticket);
		}

		$author_id = $ticket->post_author;
		//echo "<pre>";print_r($ticket_comments);exit;
		// include view ticket template with the arguments.
		include_once CAVENI_IO_PATH . 'public/partials/ticket/view.php';
		return ob_get_clean();
	}

	/**
	 * show ticket add option.
	 *
	 * @since    1.0.0
	 * @param    boolean $edited Paramter for edited a ticket.
	 * @param    object $ticket Paramter of current ticket.
	 */
	public function display_ticket_add($edited = false, $ticket = false)
	{

		ob_start();
		global $wp;
		$ticket_terms = get_all_ticket_terms();
		include_once CAVENI_IO_PATH . 'public/partials/ticket/edit.php';
		return ob_get_clean();
	}

	public function disable_comment_notification_emails($comment_id, $comment_approved)
	{
		// Get the comment object
		$comment = get_comment($comment_id);

		// Get the post object
		$post = get_post($comment->comment_post_ID);

		// Check if the post type is the one you want to target
		if ($post->post_type == CAVENI_IO_POST_TYPE) {
			// Disable email notifications for comments on this post type
			remove_action('comment_post', 'wp_new_comment_notify_moderator');
			remove_action('comment_post', 'wp_new_comment_notify_postauthor');
		}
	}

	public function add_helpdesk_menu_item($items, $args)
	{
		global $wp; // Declare $wp as global
		// Check if the menu is the one you want to modify
		if ($args->theme_location == 'primary') {
			// Initialize the new menu item
			$new_item = '';
			$current_url = home_url(add_query_arg(array(), $wp->request));
			$allowed_roles = get_user_roles_setting();

			if (check_ticket_enable() && is_user_admin_or_in_array($allowed_roles)) {
				$url = '/helpdesk';
				$active_class = ($current_url == home_url($url)) ? ' current-menu-item ' : '';
				$new_item = '<li id="menu-item-59" class="nmr-administrator nmr-subscriber menu-item menu-item-type-custom menu-item-object-custom ' . $active_class . 'menu-item-59"><a href="' . $url . '"><i class="cera-icon text-primary cera-message-square"></i> <span>Helpdesk</span></a></li>';
			}

			// Define the position where you want to insert the new item
			$position = 16; // For example, insert after the second item

			// Split the menu items into an array
			$items_array = explode('</li>', $items);

			// Insert the new item at the specified position
			array_splice($items_array, $position, 0, $new_item);

			// Rebuild the menu items string
			$items = implode('</li>', $items_array);
		}
		return $items;
	}

	public function create_ticket_action()
	{
		// check if this is correct nonce otherwise exit 
		check_ajax_referer('create_ticket_nonce_action', 'create_ticket_nonce');
		$response = array(
			'status' => 'failed',
			'message' => __("There is an issue with the ajax call", 'caveni-io')
		);

		if ($_POST) {

			$ticket_subject = isset($_POST['ticket_subject']) ? $_POST['ticket_subject'] : "";
			$ticket_category = isset($_POST['ticket_category']) ? $_POST['ticket_category'] : "";
			$ticket_description = isset($_POST['ticket_description']) ? trim(sanitize_text_field(wp_unslash($_POST['ticket_description']))) : "";
			$image_id = 0;
			if ($ticket_subject == "" || $ticket_category == "" || $ticket_description == "") {
				$response['status'] = 'error';
				$response['message'] = __("Please fill required fields before submit", 'caveni-io');
				echo json_encode($response);
				exit;
			}

			if ($_FILES) {
				$ticket_file = isset($_FILES['ticket_file']) ? $_FILES['ticket_file'] : "";
				if (! empty($ticket_file)) {
					$ticket_settings = get_option('caveni_settings');
					$size            = isset($ticket_settings['file_size']) ? $ticket_settings['file_size'] : '';
					$max_size        = intval($size) * 1024;
					if (UPLOAD_ERR_OK === $ticket_file['error']) {
						if ($ticket_file['size'] <= $max_size) {
							$upload_dir = wp_upload_dir();
							$name = basename($ticket_file['name']);
							$file_path  = $upload_dir['path'] . '/' . $name;
							$wp_filetype = wp_check_filetype($name, null);

							// Move the uploaded file.
							if (move_uploaded_file($ticket_file['tmp_name'], $file_path)) {
								$file_url = $upload_dir['url'] . '/' . $name; // Store the file URL.
								$attachment = array(
									'guid' => $file_url,
									'post_mime_type' => $wp_filetype['type'],
									'post_title' => preg_replace('/\.[^.]+$/', '', $name),
									'post_status' => 'inherit'
								);

								/**
								 * STEP 1
								 * add images as attachments to WordPress
								 */
								$image_id = wp_insert_attachment($attachment, $name);
								// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
								require_once(ABSPATH . 'wp-admin/includes/image.php');
								// Generate the metadata for the attachment, and update the database record.
								$attach_data = wp_generate_attachment_metadata($image_id, $file_path);
								wp_update_attachment_metadata($image_id, $attach_data);
							}
						}
					}
				}
			}
			$user = wp_get_current_user();
			//print_r($user);exit;
			$post_arr = array(
				'post_title'   => esc_html($ticket_subject),
				'post_content' => $ticket_description,
				'post_author'  => $user->ID,
				'post_type'	   => CAVENI_IO_POST_TYPE,
				'post_status'       =>  'publish',
				'meta_input'   => array(
					'_caveni_tickets_file' => $image_id,
					'_caveni_ticket_status'   => 'open'
				),
			);

			$ticket_id = wp_insert_post($post_arr);
			wp_set_post_terms($ticket_id, array(intval($ticket_category)), CAVENI_IO_POST_TAXONOMY);
			send_email_action(true, get_post($ticket_id));
			$response['status'] = 'success';
			$response['message'] = __("Ticket has been created. Page will be reloaded in 5 seconds", 'caveni-io');
			$response['redirectUrl'] = home_url('/helpdesk');
			echo json_encode($response);
			exit;
		}
		echo json_encode($response);
		exit;
	}

	public function reply_ticket_action()
	{

		check_ajax_referer('reply_ticket_nonce_action', 'reply_ticket_nonce');

		$response = array(
			'status' => 'failed',
			'message' => __("There is an issue with the ajax call", 'caveni-io')
		);

		if ($_POST) {

			$ticket_reply_description = isset($_POST['ticket_reply_description']) ? trim(sanitize_text_field(wp_unslash($_POST['ticket_reply_description']))) : "";
			$ticket_id = isset($_POST['ticket_id']) ? (int) $_POST['ticket_id'] : 0;

			$image_id = 0;
			if ($ticket_reply_description == "" && $ticket_id <= 0) {
				$response['status'] = 'error';
				$response['message'] = __("Please fill required fields before submitting Reply", 'caveni-io');
				echo json_encode($response);
				exit;
			}

			$post = get_post($ticket_id);
			if (! $post) {
				wp_die(-1);
			}

			if (! current_user_can('edit_post', $ticket_id)) {
				wp_die(-1);
			}

			if (empty($post->post_status)) {
				wp_die(1);
			} elseif (in_array($post->post_status, array('draft', 'pending', 'trash'), true)) {
				wp_die(esc_html__('You cannot reply to a comment on a draft post.'));
			}

			if ($_FILES) {
				$ticket_file = isset($_FILES['ticket_comment_file']) ? $_FILES['ticket_comment_file'] : "";
				if (! empty($ticket_file)) {
					$ticket_settings = get_option('caveni_settings');
					$size            = isset($ticket_settings['file_size']) ? $ticket_settings['file_size'] : '';
					$max_size        = intval($size) * 1024;
					if (UPLOAD_ERR_OK === $ticket_file['error']) {
						if ($ticket_file['size'] <= $max_size) {
							$upload_dir = wp_upload_dir();
							//echo "<pre>";print_r($upload_dir);exit;
							$name = basename($ticket_file['name']);
							$file_path  = $upload_dir['path'] . '/' . $name;
							$wp_filetype = wp_check_filetype($name, null);

							// Move the uploaded file.
							if (move_uploaded_file($ticket_file['tmp_name'], $file_path)) {
								$file_url = $upload_dir['url'] . '/' . $name; // Store the file URL.

								$attachment = array(
									'guid' => $file_url,
									'post_mime_type' => $wp_filetype['type'],
									'post_title' => preg_replace('/\.[^.]+$/', '', $name),
									'post_status' => 'inherit'
								);

								/**
								 * STEP 1
								 * add images as attachments to WordPress
								 */
								$image_id = wp_insert_attachment($attachment, $name);

								// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
								require_once(ABSPATH . 'wp-admin/includes/image.php');
								// Generate the metadata for the attachment, and update the database record.
								$attach_data = wp_generate_attachment_metadata($image_id, $file_path);
								wp_update_attachment_metadata($image_id, $attach_data);
							}
						}
					}
				}
			}

			$user = wp_get_current_user();
			if ($user->exists()) {
				$comment_author       = wp_slash($user->display_name);
				$comment_author_email = wp_slash($user->user_email);
				$comment_author_url   = wp_slash($user->user_url);
				$user_id              = $user->ID;
			}
			$comment_type          = 'comment';
			$comment_parent        = 0;
			$comment_content       = $ticket_reply_description;
			$comment_auto_approved = false;
			$commentdata           = array(
				'comment_post_ID' => $ticket_id,
			);
			$commentdata          += compact(
				'comment_author',
				'comment_author_email',
				'comment_author_url',
				'comment_content',
				'comment_type',
				'comment_parent',
				'user_id'
			);
			$comment_id            = wp_new_comment($commentdata);
			if (is_wp_error($comment_id)) {
				wp_die(esc_html($comment_id->get_error_message()));
			}
			$comment = get_comment($comment_id);

			if (! $comment) {
				wp_die(1);
			}
			//echo "<pre>";print_r($image_id);exit;
			add_comment_meta($comment_id, '_ticket_comment_reply_attachment', $image_id);
			$send_to = ($user->ID == $post->post_author) ? 'client' : 'admin';
			send_email_action(false, $comment, $send_to);
			$response['status'] = 'success';
			$response['message'] = __("Thank you for your reply! Page will be reloaded in 5 seconds.", 'caveni-io');
			$response['redirectUrl'] = '';
			echo json_encode($response);
			exit;
		}
		echo json_encode($response);
		exit;
	}

	public function update_reply_ticket_action()
	{

		check_ajax_referer('reply_ticket_nonce_action', 'reply_ticket_nonce');

		$response = array(
			'status' => 'failed',
			'message' => __("There is an issue with the ajax call", 'caveni-io')
		);

		if ($_POST) {

			$ticket_reply_description = isset($_POST['ticket_reply_description']) ? trim(sanitize_text_field(wp_unslash($_POST['ticket_reply_description']))) : "";
			$ticket_id = isset($_POST['ticket_id']) ? (int) $_POST['ticket_id'] : 0;
			$ticket_comment_id = isset($_POST['ticket_comment_id']) ? (int) $_POST['ticket_comment_id'] : 0;

			$image_id = 0;
			if ($ticket_reply_description == "" || $ticket_id <= 0  || $ticket_comment_id <= 0) {
				$response['status'] = 'error';
				$response['message'] = __("Please fill required fields before submitting Reply", 'caveni-io');
				echo json_encode($response);
				exit;
			}

			$post = get_post($ticket_id);
			if (! $post) {
				wp_die(-1);
			}

			if (! current_user_can('edit_post', $ticket_id)) {
				wp_die(-1);
			}

			if (empty($post->post_status)) {
				wp_die(1);
			} elseif (in_array($post->post_status, array('draft', 'pending', 'trash'), true)) {
				wp_die(esc_html__('You cannot reply to a comment on a draft post.'));
			}

			if ($_FILES) {
				$ticket_file = isset($_FILES['ticket_comment_file']) ? $_FILES['ticket_comment_file'] : "";
				if (! empty($ticket_file)) {
					$ticket_settings = get_option('caveni_settings');
					$size            = isset($ticket_settings['file_size']) ? $ticket_settings['file_size'] : '';
					$max_size        = intval($size) * 1024;
					if (UPLOAD_ERR_OK === $ticket_file['error']) {
						if ($ticket_file['size'] <= $max_size) {
							$upload_dir = wp_upload_dir();
							$name = basename($ticket_file['name']);
							$file_path  = $upload_dir['path'] . '/' . $name;
							$wp_filetype = wp_check_filetype($name, null);

							// Move the uploaded file.
							if (move_uploaded_file($ticket_file['tmp_name'], $file_path)) {
								$file_url = $upload_dir['url'] . '/' . $name; // Store the file URL.
								$attachment = array(
									'guid' => $file_url,
									'post_mime_type' => $wp_filetype['type'],
									'post_title' => preg_replace('/\.[^.]+$/', '', $name),
									'post_status' => 'inherit'
								);

								/**
								 * STEP 1
								 * add images as attachments to WordPress
								 */
								$image_id = wp_insert_attachment($attachment, $name);
								// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
								require_once(ABSPATH . 'wp-admin/includes/image.php');
								// Generate the metadata for the attachment, and update the database record.
								$attach_data = wp_generate_attachment_metadata($image_id, $file_path);
								wp_update_attachment_metadata($image_id, $attach_data);
							}
						}
					}
				}
			}

			$user = wp_get_current_user();
			if ($user->exists()) {
				$comment_author       = wp_slash($user->display_name);
				$comment_author_email = wp_slash($user->user_email);
				$comment_author_url   = wp_slash($user->user_url);
				$user_id              = $user->ID;
			}
			$comment_type          = 'comment';
			$comment_parent        = 0;
			$comment_content       = $ticket_reply_description;
			$comment_auto_approved = false;
			$comment_ID = $ticket_comment_id;
			$commentdata           = array(
				'comment_post_ID' => $ticket_id,
			);
			$commentdata          += compact(
				'comment_author',
				'comment_author_email',
				'comment_author_url',
				'comment_content',
				'comment_type',
				'comment_parent',
				'user_id',
				'comment_ID'
			);

			if (!wp_update_comment($commentdata)) {
				wp_die("Issue While Updating the comment");
			}
			$comment = get_comment($comment_ID);

			if (! $comment) {
				wp_die(1);
			}
			//echo "<pre>";print_r($image_id);exit;
			if ($image_id > 0) {
				add_comment_meta($comment_id, '_ticket_comment_reply_attachment', $image_id);
			}
			$send_to = ($user->ID == $post->post_author) ? 'client' : 'admin';
			send_email_action(false, $comment, $send_to);
			$response['status'] = 'success';
			$response['message'] = __("Thank you for your reply! Page will be reloaded in 5 seconds.", 'caveni-io');
			$response['redirectUrl'] = '';
			echo json_encode($response);
			exit;
		}
		echo json_encode($response);
		exit;
	}

	function close_ticket_action()
	{

		check_ajax_referer('close_ticket_nonce_action', 'close_ticket_nonce');

		$response = array(
			'status' => 'failed',
			'message' => __("There is an issue with the ajax call", 'caveni-io')
		);

		if ($_POST) {
			$close_ticket_id = isset($_POST['close_ticket_id']) ?  (int) $_POST['close_ticket_id'] : 0;

			if (! current_user_can('edit_post', $close_ticket_id)) {
				wp_die(-1);
			}

			if ($close_ticket_id <= 0) {
				$response['status'] = 'error';
				$response['message'] = __("Issue with the ticket close Id", 'caveni-io');
				echo json_encode($response);
				exit;
			}

			if (update_post_meta($close_ticket_id, '_caveni_ticket_status', 'closed')) {

				$response['status'] = 'success';
				$response['message'] = __("Ticket is closed successfully. You will be redirected in 5 seconds", 'caveni-io');
				$response['redirectUrl'] = '';
				echo json_encode($response);
				exit;
			}
		}

		echo json_encode($response);
		exit;
	}

	function delete_ticket_action()
	{

		check_ajax_referer('delete_ticket_nonce_action', 'delete_ticket_nonce');
		if (! current_user_can('manage_options')) {
			return;
		}

		if (!is_user_admin_or_in_array(array())) return;

		$response = array(
			'status' => 'failed',
			'message' => __("There is an issue with the ajax call", 'caveni-io')
		);

		if ($_POST) {
			$delete_ticket_id = isset($_POST['delete_ticket_id']) ?  (int) $_POST['delete_ticket_id'] : 0;
			if ($delete_ticket_id <= 0) {
				$response['status'] = 'error';
				$response['message'] = __("Issue with the ticket delete Id", 'caveni-io');
				echo json_encode($response);
				exit;
			}

			if (wp_delete_post($delete_ticket_id)) {

				$response['status'] = 'success';
				$response['message'] = __("Ticket is deleted successfully. You will be redirected in 5 seconds", 'caveni-io');
				$response['redirectUrl'] = home_url('/helpdesk');;
				echo json_encode($response);
				exit;
			}
		}

		echo json_encode($response);
		exit;
	}

	function delete_ticket_comment_action()
	{

		check_ajax_referer('delete_ticket_comment_nonce_action', 'delete_ticket_comment_nonce');
		if (! current_user_can('manage_options')) {
			return;
		}

		if (!is_user_admin_or_in_array(array())) return;

		$response = array(
			'status' => 'failed',
			'message' => __("There is an issue with the ajax call", 'caveni-io')
		);

		if ($_POST) {
			$delete_comment_id = isset($_POST['delete_comment_id']) ?  (int) $_POST['delete_comment_id'] : 0;
			if ($delete_comment_id <= 0) {
				$response['status'] = 'error';
				$response['message'] = __("Issue with the comment Id", 'caveni-io');
				echo json_encode($response);
				exit;
			}

			if (wp_delete_comment($delete_comment_id)) {

				$response['status'] = 'success';
				$response['message'] = __("Comment deleted successfully. You will be redirected in 5 seconds", 'caveni-io');
				$response['redirectUrl'] = '';
				echo json_encode($response);
				exit;
			}
		}

		echo json_encode($response);
		exit;
	}

	/**
	 * Get human readable time
	 */
	public function timeAgo($timestamp)
	{
		$timeAgo = strtotime(current_time('mysql')) - strtotime($timestamp);
		$timeText = '';
		if ($timeAgo < 60) {
			$timeText = $timeAgo . ' seconds ago';
		} elseif ($timeAgo < 3600) {
			$minutes = floor($timeAgo / 60);
			$timeText = $minutes . ($minutes == 1 ? ' min ago' : ' mins ago');
		} elseif ($timeAgo < 86400) {
			$hours = floor($timeAgo / 3600);
			$timeText = $hours . ($hours == 1 ? ' hour ago' : ' hours ago');
		} elseif ($timeAgo < 604800) {
			$days = floor($timeAgo / 86400);
			$timeText = $days . ($days == 1 ? ' day ago' : ' days ago');
		} elseif ($timeAgo < 2592000) {
			$weeks = floor($timeAgo / 604800);
			$timeText = $weeks . ($weeks == 1 ? ' week ago' : ' weeks ago');
		} elseif ($timeAgo < 31536000) {
			$months = floor($timeAgo / 2592000);
			$timeText = $months . ($months == 1 ? ' month ago' : ' months ago');
		} else {
			$years = floor($timeAgo / 31536000);
			$timeText = $years . ($years == 1 ? ' year ago' : ' years ago');
		}
		return $timeText;
	}

	/*
	* Get/Load user messages
	*/

	public function load_user_messages()
	{

		check_ajax_referer('message_nonce_action', 'caveni_nonce');
		if (! current_user_can('manage_options')) {
			return;
		}
		$user_id = isset($_POST['user']) ? $_POST['user'] : 0;
		if (empty($user_id)) {
			wp_send_json_error(['message' => __("Something went wrong, invalid request", 'caveni-io')]);
		}
		$group_id = isset($_POST['group_id']) ? $_POST['group_id'] : 0;
		if (empty($group_id)) {
			$group_id = $this->create_new_group($user_id);
		}
		$messages = $this->get_group_messages($group_id);
		if ($messages) {
			$messages = array_reverse($messages);
			$response['message'] = __("Messages loaded successfully.", 'caveni-io');
		} else {
			$response['message'] = __("Send a new message to start the chat.", 'caveni-io');
		}

		$response['status'] = 'success';
		$response['messages'] = $messages;
		wp_send_json_success($response);
	}

	/*
	* Get attachment data by attachment id
	*/
	public function get_attachment_data($id)
	{
		$attachment = [];
		$attachment_url = wp_get_attachment_url($id);
		if (empty($attachment_url)) {
			return $attachment;
		}
		$attachmentMeta = pathinfo($attachment_url);
		// pre($attachmentMeta);
		$attachment['id'] = $id;
		$attachment['url'] = $attachment_url;
		$attachment['name'] = $attachmentMeta['basename'];
		$image_extensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'webp');
		if (in_array($attachmentMeta['extension'], $image_extensions)) {
			$attachment['type'] = 'image';
		} else {
			$attachment['type'] = $attachmentMeta['extension'];
		}
		return $attachment;
	}

	/*
	* Get chats of group
	* @param $group_id is current logged in user
	*/
	public function get_group_messages($group_id)
	{
		global $wpdb;
		$table = $wpdb->prefix . 'ci_messages';
		$query = "SELECT * FROM {$table} WHERE group_id={$group_id} order by id desc";
		$prepared_query = $wpdb->prepare($query);
		$results = $wpdb->get_results($prepared_query);
		$current_user = get_current_user_id();
		$messages = [];
		$admins = get_admin_user_ids();
		if ($results) {
			foreach ($results as $x => $result) {
				$senderName = '';
				$read_notification = false;
				if ($result->sender_id != $current_user) {
					if (in_array($result->sender_id, $admins)) {
						$senderName = get_user_meta($result->sender_id, 'first_name', true);
					} else {
						$senderName = get_user_meta($result->sender_id, 'company', true);
						$read_notification = true;
					}
					if (empty($senderName)) {
						$senderUser = get_userdata($result->sender_id);
						$senderName = $senderUser->display_name;
					}
				}
				$profileImg = $this->get_buddypress_profile_picture($result->sender_id);
				$attachment = !empty($result->attachment) ? $this->get_attachment_data($result->attachment) : [];

				$separator = get_date_separator($result->created_at);

				$messages[] = [
					'id' => $result->id,
					'message' => $result->message,
					'sender_id' => $result->sender_id,
					'sender_img' => $profileImg,
					'sender_name' => $senderName,
					'attachment' => $attachment,
					'separator' => $separator,
					'date_group' => date('Y-m-d', strtotime($result->created_at)),
					'date' => $this->timeAgo($result->created_at)
				];
				// var_dump($read_notification);
				// pre($result);
				if ($read_notification) {
					$wpdb->update($wpdb->prefix . 'ci_messages_read', ['is_read' => 1], ['message_id' => $result->id]);
				}
			}
		}
		// pre($messages);
		return $messages;
	}

	/*
	* Get recent chats list for admin side
	*/
	public function get_recent_chat_list()
	{
		global $wpdb;
		$table_messages = $wpdb->prefix . 'ci_messages';
		$table_group = $wpdb->prefix . 'caveni_groups ';
		$table_messages_read = $wpdb->prefix . 'ci_messages_read';
		$admins = implode(',', get_admin_user_ids());

		$query = "SELECT a.id, a.group_id, a.sender_id, a.message, a.created_at, b.user_id FROM {$table_messages} AS a LEFT JOIN {$table_group} AS b ON a.group_id = b.id WHERE a.id IN ( SELECT MAX(a_inner.id) FROM {$table_messages} AS a_inner LEFT JOIN {$table_group} AS b_inner ON a_inner.group_id = b_inner.id GROUP BY b_inner.user_id ) ORDER BY a.id DESC;";
		$prepared_query = $wpdb->prepare($query);
		$results = $wpdb->get_results($prepared_query);
		$recentChats = [];
		if ($results) {
			foreach ($results as $key => $chats) {
				$sender_id = $chats->user_id;

				$profileImg = $this->get_buddypress_profile_picture($sender_id);
				$company_username = get_user_meta($sender_id, 'company', true);
				if (empty($company_username)) {
					$userData = get_userdata($sender_id);
					$company_username = $userData->display_name;
				}
				// $msg = wp_trim_words($chats->message, 4, '...');
				$msg = substr($chats->message, 0, 30);
				if (strlen($chats->message) > 30) {
					$msg = $msg . '...';
				}
				$time = $this->timeAgo($chats->created_at);
				$unread_query = "SELECT COUNT(is_read) as total FROM $table_messages_read WHERE group_id={$chats->group_id} AND user_id IN ($admins) AND is_read=0";
				$prepared_unread = $wpdb->prepare($unread_query);
				$unreads = $wpdb->get_row($prepared_unread);

				$recentChats[] = [
					'profileImg' => $profileImg,
					'name' => $company_username,
					'message' => $msg,
					'sender_id' => $sender_id,
					'group_id' => $chats->group_id,
					'unreads' => isset($unreads->total) ? $unreads->total : 0,
					// 'unreads' => 0,
					'time' => $time,
				];
			}
		}
		return $recentChats;
		echo "<pre>";
		print_r($recentChats);
		die;
	}

	/*
	* Get recent chats list for client side
	*/
	public function get_recent_chat_for_client($client_id)
	{
		global $wpdb;
		$table = $wpdb->prefix . 'ci_messages';
		$table_group = $wpdb->prefix . 'caveni_groups';
		$admins = get_admin_user_ids();
		$admins[] = $client_id;
		$admins = implode(',', $admins);
		$query = "SELECT a.id, a.group_id, a.sender_id, a.message, a.created_at, b.user_id FROM {$table} as a LEFT JOIN {$table_group} as b on a.group_id=b.id  WHERE a.sender_id IN($admins) AND b.user_id={$client_id} ORDER BY a.id DESC;";
		// $prepared_query = $wpdb->prepare($query);
		$result = $wpdb->get_row($query);
		if ($result && isset($result->message)) {
			//$result->message = wp_trim_words($result->message, 4, '...');
			$result->message = substr($result->message, 0, 30);
			if (strlen($result->message) > 30) {
				$result->message = $result->message . '...';
			}
			$result->created_at = $this->timeAgo($result->created_at);
		}
		return $result;
	}

	/*
	* Get recent chat user
	*/
	public function get_recent_chat_user()
	{
		global $wpdb;
		$table_messages = $wpdb->prefix . 'ci_messages';
		$table_group = $wpdb->prefix . 'caveni_groups';
		$query = "SELECT a.id, a.group_id, a.sender_id, a.message, a.created_at, b.user_id FROM {$table_messages} AS a LEFT JOIN {$table_group} AS b ON a.group_id = b.id WHERE a.id IN ( SELECT MAX(a_inner.id) FROM {$table_messages} AS a_inner LEFT JOIN {$table_group} AS b_inner ON a_inner.group_id = b_inner.id GROUP BY b_inner.user_id ) ORDER BY a.id DESC;";
		$prepared_query = $wpdb->prepare($query);
		$result = $wpdb->get_row($prepared_query);
		return $result;
	}
	/*
	* Check if first message of user
	*/
	public function is_user_first_message($user_id)
	{
		global $wpdb;
		$table_messages = $wpdb->prefix . 'ci_messages';
		$query = "SELECT * FROM {$table_messages} WHERE sender_id={$user_id}";
		$prepared_query = $wpdb->prepare($query);
		$result = $wpdb->get_row($prepared_query);
		return $result;
	}

	/*
	* Send messages to user
	*/
	public function ci_send_message()
	{
		check_ajax_referer('message_send_nonce_action', 'caveni_nonce');
		ini_set('display_errors', 1);
		$receiver = isset($_POST['receiver']) ? $_POST['receiver'] : 0;
		$group_id = isset($_POST['group_id']) ? $_POST['group_id'] : 0;
		$sender = isset($_POST['sender']) ? $_POST['sender'] : 0;
		if (empty($receiver) || empty($sender)) {
			wp_send_json_error(['message' => __("Something went wrong, invalid request", 'caveni-io')]);
		}
		if (empty($group_id)) {
			$group_id = $this->get_group_id($sender);
			if (empty($group_id)) {
				$group_id = $this->create_new_group($sender);
			}
		}
		if (empty($group_id)) {
			wp_send_json_error(['message' => __("Something went wrong, invalid request", 'caveni-io')]);
		}
		$message = isset($_POST['message']) ? esc_html(preg_replace('/\\\\/', '', $_POST['message'])) : '';
		$attachment_id = 0;
		if (isset($_FILES['ci_attachment']) && !empty($_FILES['ci_attachment']['name'])) {
			$attachment = $this->get_message_attachment();
			if (isset($attachment['error']) && $attachment['error']) {
				wp_send_json_error(['message' => $attachment['error']]);
			}
			$attachment_id = $attachment['id'];
		}
		// pre($attachment_id, 1);
		global $wpdb;
		$table = $wpdb->prefix . 'ci_messages';

		$admins = get_admin_user_ids();
		if (in_array($sender, $admins)) {
			$send_notification = false;
			$senderName = get_user_meta($sender, 'first_name', true);
		} else {
			$send_notification = true;
			$senderName = get_user_meta($sender, 'company', true);
		}
		if (empty($senderName)) {
			$senderData = get_userdata($sender);
			$senderName = $senderData->display_name;
		}
		$senderAvtar = $this->get_buddypress_profile_picture($sender);

		$fisrtMessage = $this->get_first_messager($sender);
		//pre($fisrtMessage, 1);
		$sent = $wpdb->insert($table, ['sender_id' => $sender, 'group_id' => $group_id, 'attachment' => $attachment_id, 'message' => $message, 'created_at' => current_time('mysql')]);
		if ($sent) {
			$message_id = $wpdb->insert_id;
			$attachment = '';
			//if($receiver == $adminStaff)
			$message = [
				'id' => $message_id,
				'sender_id' => $sender,
				'sender_name' => $senderName,
				'sender_avtar' => $senderAvtar,
				'group_id' => $group_id,
				'sender_id' => $sender,
				'receiver_id' => $receiver,
				'message' => $message,
				'attachment' => $attachment_id ? $this->get_attachment_data($attachment_id) : [],
				'date' => esc_html(date('Y-m-d', strtotime(current_time('mysql')))),
				'timeago' => $this->timeAgo(current_time('mysql'))
			];
			if ($send_notification) {
				$wpdb->insert($wpdb->prefix . 'ci_messages_read', ['user_id' => $receiver, 'group_id' => $group_id, 'message_id' => $message_id, 'created_at' => current_time('mysql')]);
			}
			wp_send_json_success(['message' => __("Message sent successfully!", 'caveni-io'), 'sent' => $message, 'first' => $fisrtMessage]);
		} else {
			wp_send_json_error(['message' => __("Something went wrong, issue with the sending message", 'caveni-io')]);
		}
	}

	/*
	* Delete message of users
	*/
	public function ci_delete_message()
	{
		check_ajax_referer('delete_message_nonce_action', 'caveni_nonce');
		ini_set('display_errors', 1);
		$message = isset($_POST['message']) ? $_POST['message'] : 0;
		$attachment = isset($_POST['attachment']) ? $_POST['attachment'] : 0;
		if (empty($message)) {
			wp_send_json_error(['message' => __("Something went wrong, invalid request", 'caveni-io')]);
		}
		global $wpdb;
		$table_messages = $wpdb->prefix . 'ci_messages';
		$table_messages_read = $wpdb->prefix . 'ci_messages_read';
		$wpdb->delete($table_messages, array('id' => $message));
		$wpdb->delete($table_messages_read, array('message_id' => $message));
		if ($attachment) {
			wp_delete_attachment($attachment);
		}
		wp_send_json_success(['message' => __("Message deleted successfully!", 'caveni-io')]);
	}

	/*
	* Search messages or clients
	*/
	public function ci_search_messages()
	{
		check_ajax_referer('search_message_nonce_action', 'caveni_nonce');
		ini_set('display_errors', 1);
		$search = isset($_POST['search']) ? $_POST['search'] : '';
		$type = isset($_POST['type']) ? $_POST['type'] : '';
		$client_id = isset($_POST['user']) ? $_POST['user'] : 0;
		if (empty($search)) {
			wp_send_json_error(['message' => __("Something went wrong, invalid request", 'caveni-io')]);
		}
		if ($type == 'clients') {
			$clients = $this->get_clients_with_company($search);
			wp_send_json_success(['message' => __("Clients found successfully!", 'caveni-io'), 'results' => $clients]);
		} else {
			global $wpdb;
			$table = $wpdb->prefix . 'ci_messages';
			$table_group = $wpdb->prefix . 'caveni_groups';
			if ($client_id) {
				$admins = get_admin_user_ids();
				$admins[] = $client_id;
				$admins = implode(',', $admins);
				$query = "SELECT a.id, a.group_id, a.sender_id, a.message, b.user_id FROM {$table} as a LEFT JOIN {$table_group} as b on a.group_id=b.id  WHERE a.message LIKE '%$search%' AND a.sender_id IN($admins) AND b.user_id={$client_id} ORDER BY a.id DESC;";
			} else {
				$query = "SELECT a.id, a.group_id, a.sender_id, a.message, b.user_id FROM {$table} as a LEFT JOIN {$table_group} as b on a.group_id=b.id  WHERE a.message LIKE '%$search%' ORDER BY a.id DESC;";
			}
			// $prepared_query = $wpdb->prepare($query);
			$results = $wpdb->get_results($query);
			// pre($results);
			$messages = [];
			if ($results) {
				$admins = get_admin_user_ids();
				foreach ($results as $message) {
					if (in_array($message->sender_id, $admins)) {
						$senderName = get_user_meta($message->sender_id, 'first_name', true);
					} else {
						$senderName = get_user_meta($message->sender_id, 'company', true);
					}
					if (empty($senderName)) {
						$senderUser = get_userdata($message->sender_id);
						$senderName = $senderUser->display_name;
					}
					$profileImg = $this->get_buddypress_profile_picture($message->sender_id);
					$messages[] = [
						'id' => $message->id,
						'name' => $senderName,
						'avtar' => $profileImg,
						'group_id' => $message->group_id,
						'sender_id' => $message->sender_id,
						'user' => $message->user_id,
						'message' => $this->searchWithContext($search, $message->message),
					];
				}
			}
			wp_send_json_success(['message' => __("Message found successfully!", 'caveni-io'), 'results' => $messages]);
		}
	}

	/*
	* Get only matched searched words from whole message
	*/
	public function searchWithContext($searchTerm, $message)
	{
		$result = $searchTerm;
		$pattern = '/(\w+\s+)?(' . preg_quote($searchTerm, '/') . ')(\s+\w+)?/i';

		if (preg_match($pattern, $message, $matches)) {
			$context = trim(($matches[1] ?? '') . ' ' . $matches[2] . ' ' . ($matches[3] ?? ''));
			$result = $context;
		}
		// echo "result: $result";
		// echo "<br>";
		return $result;
	}

	/*
	* Check if user first message and return that user
	*/
	public function get_first_messager($user_id)
	{
		$receiverData = get_userdata($user_id);
		$fisrtMessage = [];
		if (!in_array('administrator', $receiverData->roles)) {
			if (empty($this->is_user_first_message($user_id))) {
				$company_username = get_user_meta($user_id, 'company', true);
				if (empty($company_username)) {
					$company_username = $receiverData->display_name;
				}
				$fisrtMessage = [
					'avtar' => $this->get_buddypress_profile_picture($user_id),
					'name' => $company_username,
					'date' => $this->timeAgo(current_time('mysql'))
				];
			}
		}
		return $fisrtMessage;
	}

	/*
	* get attachment id of a message
	*/
	public function get_message_attachment()
	{
		$result = ['id' => 0, 'error' => false];
		if (isset($_FILES['ci_attachment']) && !empty($_FILES['ci_attachment']['name'])) {
			$uploaded_file = $_FILES['ci_attachment'];
			$settings = get_option('caveni_settings');
			$file_types = isset($settings['messages_file_types'])  ? $settings['messages_file_types'] : '';
			$file_size = isset($settings['messages_file_size'])  ? (int)$settings['messages_file_size'] : 0;

			if ($file_types) {
				$file_types = array_map('trim', explode(',', $file_types));
				$file_info = pathinfo($_FILES['ci_attachment']['name']);
				$extension = strtolower($file_info['extension']);
				if ($file_types && !in_array('.' . $extension, $file_types)) {
					$suppoted_files = implode(', ', $file_types);
					$result['error'] =  __("File type is not supported, please upload $suppoted_files file types", 'caveni-io');
				}
			}
			if ($file_size) {
				$max_file_size = $file_size * 1024 * 1024;
				if ($uploaded_file['size'] > $max_file_size) {
					$result['error'] =  __("File uploaded is too large, please upload max {$file_size}MB file", 'caveni-io');
				}
			}
			if ($result['error']) {
				return $result;
			}
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			require_once(ABSPATH . 'wp-admin/includes/media.php');
			$upload_overrides = array('test_form' => false);
			$move_file = wp_handle_upload($uploaded_file, $upload_overrides);
			if (isset($move_file['file'])) {
				$file_path = $move_file['file'];
				$file_url = $move_file['url'];
				$attachment = wp_insert_attachment(array(
					'post_title' => basename($file_path),
					'post_content' => '',
					'post_status' => 'inherit',
					'guid' => $file_url
				), $file_path);

				$attach_data =  wp_generate_attachment_metadata($attachment, $file_path);
				wp_update_attachment_metadata($attachment, $attach_data);
				$result['id'] = $attachment;
				$result['error'] = false;
			} else {
				$result['id'] = 0;
				$result['error'] =  __("File uploading error.", 'caveni-io');
			}
		}
		return $result;
	}

	/*
	* get user message group id
	*/
	public function get_group_id($user_id)
	{
		global $wpdb;
		$table = $wpdb->prefix . 'caveni_groups';
		$query = "SELECT id FROM {$table} WHERE user_id={$user_id}";
		$prepared_query = $wpdb->prepare($query);
		$exist = $wpdb->get_row($prepared_query);
		if (isset($exist->id)) {
			return $exist->id;
		}
		return;
	}

	/*
	* created a new group for user
	*/
	public function create_new_group($user_id)
	{
		global $wpdb;
		$table = $wpdb->prefix . 'caveni_groups';
		$wpdb->insert($table, ['user_id' => $user_id, 'created_at' => current_time('mysql')]);
		return $wpdb->insert_id;
	}

	public function ci_deleted_user($user_id, $reassign, $user)
	{
		if ($user && isset($user->roles)) {
			$settings = get_option('caveni_settings');
			$enabledRoles = isset($settings['messages_role']) ? $settings['messages_role'] : [];
			if (array_intersect($enabledRoles, (array) $user->roles)) {
				global $wpdb;
				$table_groups = $wpdb->prefix . 'caveni_groups';
				$query = "SELECT id FROM {$table_groups} WHERE user_id={$user_id}";
				$exist = $wpdb->get_row($query);
				if ($exist && isset($exist->id)) {
					$group_id = $exist->id;
					$table_messages = $wpdb->prefix . 'ci_messages';
					$table_messages_read = $wpdb->prefix . 'ci_messages_read';
					$wpdb->delete($table_messages, array('group_id' => $group_id));
					$wpdb->delete($table_messages_read, array('group_id' => $group_id));
					$wpdb->delete($table_groups, array('id' => $group_id));
				}
			}
		}
	}
}
