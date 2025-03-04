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
class Caveni_Io_HelpDesk_Front {

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
	public function __construct( $caveni_io, $version ) {
		$this->caveni_io = $caveni_io;
		$this->version   = $version;
	}

	/**
	 * Perform  the of shortcode helpdesk.
	 *
	 * @since    1.0.0
	 * @param    array $attributes Paramter of shortcode array.
	 */
	public function perform_shortcode_action( $attributes, $req_data ) {

		$user_permission = check_current_user_ticket_permissions($_GET);
		if( $user_permission['status'] == 'failed'){
			wp_redirect(home_url());
			exit;
			//return __( $user_permission['message'], 'caveni-io');
		}

		if(!check_ticket_enable()){
			return __( "", 'caveni-io');
		}

		$action = 'list';
		$ticket_id = 0;
		if(isset($req_data['_ticket_action'])){
			$action = $req_data['_ticket_action'];
		}

		if(isset($req_data['_ticket_id'])){
			$ticket_id = (int) $req_data['_ticket_id'];
		}
		
		if($action == 'add'){
		
			return $this->display_ticket_add();
		
		}else if($action == 'edit' && $ticket_id > 0){
			
			$ticket = get_ticket_by_id($ticket_id);
			return $this->display_ticket_add(true, $ticket);

		}else if( $action == 'view' &&  $ticket_id > 0){
			
			$ticket = get_ticket_by_id($ticket_id);
			if(!$ticket) {
				wp_redirect(home_url());
				exit;
				//return __( "Ticket Does not Exist", 'caveni-io');
			}else{
				return $this->display_ticket_view($ticket);
			}

		}else{

			return $this->display_ticket_list();
		
		}
		wp_redirect(home_url());
		exit;
		//return __( $user_permission['message'], 'caveni-io');
	}

	/**
	 * show ticket list.
	 *
	 * @since    1.0.0
	 * 
	 */
	public function display_ticket_list() {
		
		ob_start();
		global $wp;
		$tickets = get_all_user_tickets();
		
		$all_tickets_count = count($tickets);
		$open_tickets_count = count(get_user_tickets_by_status('open'));
		$close_tickets_count = count(get_user_tickets_by_status('closed'));
		// include template with the arguments.
		include_once CAVENI_IO_PATH . 'public/partials/ticket/list.php';
		return ob_get_clean();

	}

	/**
	 * show ticket view.
	 *
	 * @since    1.0.0
	 * @param    object $ticket Paramter of current ticket.
	 */
	public function display_ticket_view($ticket) {
		
		ob_start();
		global $wp;
		$postedAtString = get_minutes_ago_time( get_the_modified_time('U',$ticket), get_the_time('U',$ticket));
		$ticket_status = get_post_meta($ticket->ID,'_caveni_ticket_status',true);
		$ticket_comments = get_comments_by_ticket_id($ticket->ID);
		$modified_by_text = __("Last Updated","caveni-io");
		if(count($ticket_comments) > 0 ){
			$last_comment = $ticket_comments[array_key_last($ticket_comments)];
			$modified_by_text = get_last_commented_by($last_comment->user_id,$ticket);
		}else{
			$modified_by_text = get_last_commented_by($ticket->post_author,$ticket);
		}
		
		$author_id=$ticket->post_author;
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
	public function display_ticket_add($edited=false, $ticket=false){

		ob_start();
		global $wp;
		$ticket_terms = get_all_ticket_terms();
		include_once CAVENI_IO_PATH . 'public/partials/ticket/edit.php';
		return ob_get_clean();

	}

	public function disable_comment_notification_emails($comment_id, $comment_approved) {
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

	public function add_helpdesk_menu_item($items, $args) {
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
				$new_item = '<li id="menu-item-59" class="nmr-administrator nmr-subscriber menu-item menu-item-type-custom menu-item-object-custom '. $active_class.'menu-item-59"><a href="'.$url.'"><i class="cera-icon text-primary cera-message-square"></i> <span>Helpdesk</span></a></li>';
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

	public function create_ticket_action(){
		// check if this is correct nonce otherwise exit 
		check_ajax_referer( 'create_ticket_nonce_action', 'create_ticket_nonce' );
		$response = array(
			'status' => 'failed',
			'message' => __( "There is an issue with the ajax call", 'caveni-io')
		);

		if($_POST){
			
			$ticket_subject = isset($_POST['ticket_subject']) ? $_POST['ticket_subject'] : ""; 
			$ticket_category = isset($_POST['ticket_category']) ? $_POST['ticket_category'] : ""; 
			$ticket_description = isset($_POST['ticket_description']) ? trim( sanitize_text_field( wp_unslash( $_POST['ticket_description'] ) ) ) : ""; 
			$image_id = 0 ;
			if($ticket_subject == "" || $ticket_category == "" || $ticket_description == "" ){
				$response['status'] = 'error';
				$response['message'] = __( "Please fill required fields before submit", 'caveni-io');
				echo json_encode($response);
				exit;
			}

			if($_FILES){
				$ticket_file = isset($_FILES['ticket_file']) ? $_FILES['ticket_file'] : "";
				if( ! empty( $ticket_file ) ){
					$ticket_settings = get_option( 'caveni_settings' );
					$size            = isset( $ticket_settings['file_size'] ) ? $ticket_settings['file_size'] : '';
					$max_size        = intval( $size ) * 1024;
					if ( UPLOAD_ERR_OK === $ticket_file['error'] ) {
						if ( $ticket_file['size'] <= $max_size ) {
							$upload_dir = wp_upload_dir();
							$name = basename($ticket_file['name']);
							$file_path  = $upload_dir['path'] . '/' . $name;
							$wp_filetype = wp_check_filetype($name, null );

							// Move the uploaded file.
							if ( move_uploaded_file( $ticket_file['tmp_name'], $file_path ) ) {
								$file_url = $upload_dir['url'] . '/' . $name; // Store the file URL.
								$attachment = array(
									'guid'=> $file_url, 
									'post_mime_type' => $wp_filetype['type'],
									'post_title' => preg_replace('/\.[^.]+$/', '',$name),
									'post_status' => 'inherit'
								  );
								
								  /**
									* STEP 1
									* add images as attachments to WordPress
									*/
								  $image_id = wp_insert_attachment($attachment, $name);
								  // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
								  require_once( ABSPATH . 'wp-admin/includes/image.php' );
								  // Generate the metadata for the attachment, and update the database record.
								  $attach_data = wp_generate_attachment_metadata( $image_id, $file_path );
								  wp_update_attachment_metadata( $image_id, $attach_data );
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
			wp_set_post_terms( $ticket_id, array( intval($ticket_category) ), CAVENI_IO_POST_TAXONOMY );
			send_email_action(true,get_post($ticket_id));
			$response['status'] = 'success';
			$response['message'] = __( "Ticket has been created. Page will be reloaded in 5 seconds", 'caveni-io');
			$response['redirectUrl'] = home_url('/helpdesk');
			echo json_encode($response);
			exit;
			
			
		}
		echo json_encode($response);
		exit;
	}

	public function reply_ticket_action() {
		
		check_ajax_referer( 'reply_ticket_nonce_action', 'reply_ticket_nonce' );
	
		$response = array(
			'status' => 'failed',
			'message' => __( "There is an issue with the ajax call", 'caveni-io')
		);

		if($_POST){
			
			$ticket_reply_description = isset($_POST['ticket_reply_description']) ? trim( sanitize_text_field( wp_unslash( $_POST['ticket_reply_description'] ) ) ) : ""; 
			$ticket_id = isset($_POST['ticket_id']) ? (int) $_POST['ticket_id'] : 0; 

			$image_id = 0 ;
			if($ticket_reply_description == "" && $ticket_id <= 0){
				$response['status'] = 'error';
				$response['message'] = __( "Please fill required fields before submitting Reply", 'caveni-io');
				echo json_encode($response);
				exit;
			}

			$post = get_post( $ticket_id );
			if ( ! $post ) {
				wp_die( -1 );
			}
	
			if ( ! current_user_can( 'edit_post', $ticket_id ) ) {
				wp_die( -1 );
			}
	
			if ( empty( $post->post_status ) ) {
				wp_die( 1 );
			} elseif ( in_array( $post->post_status, array( 'draft', 'pending', 'trash' ), true ) ) {
				wp_die( esc_html__( 'You cannot reply to a comment on a draft post.' ) );
			}

			if($_FILES){
				$ticket_file = isset($_FILES['ticket_comment_file']) ? $_FILES['ticket_comment_file'] : "";
				if( ! empty( $ticket_file ) ){
					$ticket_settings = get_option( 'caveni_settings' );
					$size            = isset( $ticket_settings['file_size'] ) ? $ticket_settings['file_size'] : '';
					$max_size        = intval( $size ) * 1024;
					if ( UPLOAD_ERR_OK === $ticket_file['error'] ) {
						if ( $ticket_file['size'] <= $max_size ) {
							$upload_dir = wp_upload_dir();
							//echo "<pre>";print_r($upload_dir);exit;
							$name = basename($ticket_file['name']);
							$file_path  = $upload_dir['path'] . '/' . $name;
							$wp_filetype = wp_check_filetype($name, null );

							// Move the uploaded file.
							if ( move_uploaded_file( $ticket_file['tmp_name'], $file_path ) ) {
								$file_url = $upload_dir['url'] . '/' . $name; // Store the file URL.
								
								$attachment = array(
									'guid'=> $file_url, 
									'post_mime_type' => $wp_filetype['type'],
									'post_title' => preg_replace('/\.[^.]+$/', '',$name),
									'post_status' => 'inherit'
								  );
								
								  /**
									* STEP 1
									* add images as attachments to WordPress
									*/
								  $image_id = wp_insert_attachment($attachment, $name);
								  
								  // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
								  require_once( ABSPATH . 'wp-admin/includes/image.php' );
								  // Generate the metadata for the attachment, and update the database record.
								  $attach_data = wp_generate_attachment_metadata( $image_id, $file_path );
								  wp_update_attachment_metadata( $image_id, $attach_data );
							}
						}
					}
				}
			}

			$user = wp_get_current_user();
			if ( $user->exists() ) {
				$comment_author       = wp_slash( $user->display_name );
				$comment_author_email = wp_slash( $user->user_email );
				$comment_author_url   = wp_slash( $user->user_url );
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
			$comment_id            = wp_new_comment( $commentdata );
			if ( is_wp_error( $comment_id ) ) {
				wp_die( esc_html( $comment_id->get_error_message() ) );
			}
			$comment = get_comment( $comment_id );

			if ( ! $comment ) {
				wp_die( 1 );
			}
			//echo "<pre>";print_r($image_id);exit;
			add_comment_meta( $comment_id, '_ticket_comment_reply_attachment', $image_id );
			$send_to = ($user->ID == $post->post_author) ? 'client' : 'admin';
			send_email_action(false,$comment,$send_to);
			$response['status'] = 'success';
			$response['message'] = __( "Thank you for your reply! Page will be reloaded in 5 seconds.", 'caveni-io');
			$response['redirectUrl'] = '';
			echo json_encode($response);
			exit;
			
			
		}
		echo json_encode($response);
		exit;
	}

	public function update_reply_ticket_action() {
		
		check_ajax_referer( 'reply_ticket_nonce_action', 'reply_ticket_nonce' );
	
		$response = array(
			'status' => 'failed',
			'message' => __( "There is an issue with the ajax call", 'caveni-io')
		);

		if($_POST){
			
			$ticket_reply_description = isset($_POST['ticket_reply_description']) ? trim( sanitize_text_field( wp_unslash( $_POST['ticket_reply_description'] ) ) ) : ""; 
			$ticket_id = isset($_POST['ticket_id']) ? (int) $_POST['ticket_id'] : 0; 
			$ticket_comment_id = isset($_POST['ticket_comment_id']) ? (int) $_POST['ticket_comment_id'] : 0; 
			
			$image_id = 0 ;
			if($ticket_reply_description == "" || $ticket_id <= 0  || $ticket_comment_id <= 0){
				$response['status'] = 'error';
				$response['message'] = __( "Please fill required fields before submitting Reply", 'caveni-io');
				echo json_encode($response);
				exit;
			}

			$post = get_post( $ticket_id );
			if ( ! $post ) {
				wp_die( -1 );
			}
	
			if ( ! current_user_can( 'edit_post', $ticket_id ) ) {
				wp_die( -1 );
			}
	
			if ( empty( $post->post_status ) ) {
				wp_die( 1 );
			} elseif ( in_array( $post->post_status, array( 'draft', 'pending', 'trash' ), true ) ) {
				wp_die( esc_html__( 'You cannot reply to a comment on a draft post.' ) );
			}

			if($_FILES){
				$ticket_file = isset($_FILES['ticket_comment_file']) ? $_FILES['ticket_comment_file'] : "";
				if( ! empty( $ticket_file ) ){
					$ticket_settings = get_option( 'caveni_settings' );
					$size            = isset( $ticket_settings['file_size'] ) ? $ticket_settings['file_size'] : '';
					$max_size        = intval( $size ) * 1024;
					if ( UPLOAD_ERR_OK === $ticket_file['error'] ) {
						if ( $ticket_file['size'] <= $max_size ) {
							$upload_dir = wp_upload_dir();
							$name = basename($ticket_file['name']);
							$file_path  = $upload_dir['path'] . '/' . $name;
							$wp_filetype = wp_check_filetype($name, null );

							// Move the uploaded file.
							if ( move_uploaded_file( $ticket_file['tmp_name'], $file_path ) ) {
								$file_url = $upload_dir['url'] . '/' . $name; // Store the file URL.
								$attachment = array(
									'guid'=> $file_url, 
									'post_mime_type' => $wp_filetype['type'],
									'post_title' => preg_replace('/\.[^.]+$/', '',$name),
									'post_status' => 'inherit'
								  );
								
								  /**
									* STEP 1
									* add images as attachments to WordPress
									*/
								  $image_id = wp_insert_attachment($attachment, $name);
								  // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
								  require_once( ABSPATH . 'wp-admin/includes/image.php' );
								  // Generate the metadata for the attachment, and update the database record.
								  $attach_data = wp_generate_attachment_metadata( $image_id, $file_path );
								  wp_update_attachment_metadata( $image_id, $attach_data );
							}
						}
					}
				}
			}

			$user = wp_get_current_user();
			if ( $user->exists() ) {
				$comment_author       = wp_slash( $user->display_name );
				$comment_author_email = wp_slash( $user->user_email );
				$comment_author_url   = wp_slash( $user->user_url );
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
			
			if ( !wp_update_comment( $commentdata ) ) {
				wp_die( "Issue While Updating the comment" );
			}
			$comment = get_comment( $comment_ID );

			if ( ! $comment ) {
				wp_die( 1 );
			}
			//echo "<pre>";print_r($image_id);exit;
			if($image_id > 0 ){
				add_comment_meta( $comment_id, '_ticket_comment_reply_attachment', $image_id );
			}
			$send_to = ($user->ID == $post->post_author) ? 'client' : 'admin';
			send_email_action(false,$comment,$send_to);
			$response['status'] = 'success';
			$response['message'] = __( "Thank you for your reply! Page will be reloaded in 5 seconds.", 'caveni-io');
			$response['redirectUrl'] = '';
			echo json_encode($response);
			exit;
			
			
		}
		echo json_encode($response);
		exit;
	}

	function close_ticket_action() {

		check_ajax_referer( 'close_ticket_nonce_action', 'close_ticket_nonce' );		
	
		$response = array(
			'status' => 'failed',
			'message' => __( "There is an issue with the ajax call", 'caveni-io')
		);

		if($_POST){
			$close_ticket_id = isset($_POST['close_ticket_id']) ?  (int) $_POST['close_ticket_id'] : 0; 

			if ( ! current_user_can( 'edit_post', $close_ticket_id ) ) {
				wp_die( -1 );
			}

			if( $close_ticket_id <= 0 ){
				$response['status'] = 'error';
				$response['message'] = __( "Issue with the ticket close Id", 'caveni-io');
				echo json_encode($response);
				exit;
			}
			
			if(update_post_meta($close_ticket_id,'_caveni_ticket_status','closed')){

				$response['status'] = 'success';
				$response['message'] = __( "Ticket is closed successfully. You will be redirected in 5 seconds", 'caveni-io');
				$response['redirectUrl'] = '';
				echo json_encode($response);
				exit;
			}			
		}

		echo json_encode($response);
		exit;
	}

	function delete_ticket_action() {

		check_ajax_referer( 'delete_ticket_nonce_action', 'delete_ticket_nonce' );		
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		if(!is_user_admin_or_in_array(array())) return;

		$response = array(
			'status' => 'failed',
			'message' => __( "There is an issue with the ajax call", 'caveni-io')
		);

		if($_POST){
			$delete_ticket_id = isset($_POST['delete_ticket_id']) ?  (int) $_POST['delete_ticket_id'] : 0; 
			if( $delete_ticket_id <= 0 ){
				$response['status'] = 'error';
				$response['message'] = __( "Issue with the ticket delete Id", 'caveni-io');
				echo json_encode($response);
				exit;
			}
			
			if(wp_delete_post($delete_ticket_id)){

				$response['status'] = 'success';
				$response['message'] = __( "Ticket is deleted successfully. You will be redirected in 5 seconds", 'caveni-io');
				$response['redirectUrl'] = home_url('/helpdesk');;
				echo json_encode($response);
				exit;
			}			
		}

		echo json_encode($response);
		exit;
	}

	function delete_ticket_comment_action() {

		check_ajax_referer( 'delete_ticket_comment_nonce_action', 'delete_ticket_comment_nonce' );		
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		if(!is_user_admin_or_in_array(array())) return;

		$response = array(
			'status' => 'failed',
			'message' => __( "There is an issue with the ajax call", 'caveni-io')
		);

		if($_POST){
			$delete_comment_id = isset($_POST['delete_comment_id']) ?  (int) $_POST['delete_comment_id'] : 0; 
			if( $delete_comment_id <= 0 ){
				$response['status'] = 'error';
				$response['message'] = __( "Issue with the comment Id", 'caveni-io');
				echo json_encode($response);
				exit;
			}
			
			if(wp_delete_comment($delete_comment_id)){

				$response['status'] = 'success';
				$response['message'] = __( "Comment deleted successfully. You will be redirected in 5 seconds", 'caveni-io');
				$response['redirectUrl'] = '';
				echo json_encode($response);
				exit;
			}			
		}

		echo json_encode($response);
		exit;
	}
	
}
