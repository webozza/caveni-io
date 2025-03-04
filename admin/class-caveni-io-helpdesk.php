<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://xfinitysoft.com
 * @since      1.0.0
 *
 * @package    Caveni_Io
 * @subpackage Caveni_Io/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Caveni_Io
 * @subpackage Caveni_Io/admin
 * @author     Xfinity Soft <email@example.com>
 */
class Caveni_Io_HelpDesk
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
	 * @since 1.0.0
	 * @param string $caveni_io       The name of this plugin.
	 * @param string $version    The version of this plugin.
	 */
	public function __construct($caveni_io, $version)
	{

		$this->caveni_io = $caveni_io;
		$this->version   = $version;
	}

	/**
	 * Initialize all functions required for runing helpdesk.
	 *
	 * @since    1.0.0
	 */
	public function initialize_helpdesk()
	{
		$this->create_ticket_post_type();
		$this->create_ticket_taxonomy();
	}
	/**
	 * Register the Ticket Custom post type in caveni io.
	 *
	 * @since    1.0.0
	 */
	public function create_ticket_post_type()
	{
		// Register tickets post type and initial settings.
		$labels = array(
			'name'               => __('Tickets', 'caveni-io'),
			'singular_name'      => __('Ticket', 'caveni-io'),
			'menu_name'          => __('Help Desk', 'caveni-io'),
			'name_admin_bar'     => __('Ticket', 'caveni-io'),
			'add_new'            => __('Add New Ticket', 'caveni-io'),
			'add_new_item'       => __('Add New Ticket', 'caveni-io'),
			'new_item'           => __('New Ticket', 'caveni-io'),
			'edit_item'          => __('Edit Ticket', 'caveni-io'),
			'view_item'          => __('View Ticket', 'caveni-io'),
			'all_items'          => __('All Tickets', 'caveni-io'),
			'search_items'       => __('Search Tickets', 'caveni-io'),
			'not_found'          => __('No tickets found.', 'caveni-io'),
			'not_found_in_trash' => __('No tickets found in Trash.', 'caveni-io'),
		);
		$args   = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'query_var'          => true,
			//'capability_type'    => 'post',
			'capability_type' => array('caveni_ticket', 'caveni_tickets'),
			'capabilities' => array(
				'edit_post' => 'edit_caveni_ticket',
				'read_post' => 'read_caveni_ticket',
				'delete_post' => 'delete_caveni_ticket',
				'edit_posts' => 'edit_caveni_tickets',
				'edit_others_posts' => 'edit_others_caveni_tickets',
				'publish_posts' => 'publish_caveni_tickets',
				'read_private_posts' => 'read_private_caveni_tickets',
				'delete_posts' => 'delete_caveni_tickets',
				'delete_private_posts' => 'delete_private_caveni_tickets',
				'delete_published_posts' => 'delete_published_caveni_tickets',
				'delete_others_posts' => 'delete_others_caveni_tickets',
				'edit_private_posts' => 'edit_private_caveni_tickets',
				'edit_published_posts' => 'edit_published_caveni_tickets',
			),
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 6,
			'menu_icon'          => 'dashicons-tickets-alt',
			'supports'           => array('title', 'editor', 'author', 'comments'),
		);
		if (check_ticket_enable()) {
			register_post_type(CAVENI_IO_POST_TYPE, $args);
		}
	}
	/**
	 * Register the Custom taxonomy in caveni io.
	 *
	 * @since    1.0.0
	 */
	public function create_ticket_taxonomy()
	{
		$labels = array(
			'name'              => __('Categories', 'caveni-io'),
			'singular_name'     => __('Category', 'caveni-io'),
			'search_items'      => __('Search Category', 'caveni-io'),
			'all_items'         => __('All Categories', 'caveni-io'),
			'parent_item'       => __('Parent Category', 'caveni-io'),
			'parent_item_colon' => __('Parent Category:', 'caveni-io'),
			'edit_item'         => __('Edit Category', 'caveni-io'),
			'update_item'       => __('Update Category', 'caveni-io'),
			'add_new_item'      => __('Add New Category', 'caveni-io'),
			'new_item_name'     => __('New Category Name', 'caveni-io'),
			'menu_name'         => __('Categories', 'caveni-io'),
		);
		$args   = array(
			'labels'            => $labels,
			'hierarchical'      => false,
			'public'            => true,
			'show_admin_column' => true,
		);
		register_taxonomy(CAVENI_IO_POST_TAXONOMY, array(CAVENI_IO_POST_TYPE), $args);
	}

	public function add_caveni_ticket_capabilities()
	{
		// Get the administrator role
		$role = get_role('administrator');

		// Add custom capabilities
		$capabilities = array(
			'edit_caveni_ticket',
			'read_caveni_ticket',
			'delete_caveni_ticket',
			'edit_caveni_tickets',
			'edit_others_caveni_tickets',
			'publish_caveni_tickets',
			'read_private_caveni_tickets',
			'delete_caveni_tickets',
			'delete_private_caveni_tickets',
			'delete_published_caveni_tickets',
			'delete_others_caveni_tickets',
			'edit_private_caveni_tickets',
			'edit_published_caveni_tickets',
		);

		foreach ($capabilities as $cap) {
			$role->add_cap($cap);
		}
		$all_allowed_roles = get_user_roles_setting();
		/////////////////////////////////////////////////////////
		//Commented by Mahesh on 02-11-2024 12:45 AM 
		///////////////////////////////////////////////////////////
		if (!empty($all_allowed_roles)) {
			foreach ($all_allowed_roles as $user_role) {
				// Get the author role
				$user_role = trim($user_role);
				$role = get_role($user_role);

				// Add custom capabilities
				$role->add_cap('edit_caveni_ticket');
				$role->add_cap('read_caveni_ticket');
				$role->add_cap('edit_caveni_tickets');
				$role->add_cap('publish_caveni_tickets');
				$role->add_cap('edit_published_caveni_tickets');
			}
		}
	}

	/**
	 * Add meta box for attachment in ticket.
	 *
	 * @since    1.0.0
	 * @param    string $post_type  Name of post type.
	 * @param    object $post Object of post detail.
	 */
	public function cavenio_add_files_meta_box($post_type, $post)
	{
		if ('caveni_ticket' === $post_type) {
			$ticket_settings = get_option('caveni_settings');
			$files_size      = isset($ticket_settings['file_size']) ? esc_html($ticket_settings['file_size']) : '';
			$file_types      = isset($ticket_settings['file_types']) ? esc_html($ticket_settings['file_types']) : '';
			// translators: %2$s: size of files.
			$file_label = wp_sprintf(__('Upload Files ( max %1$s KB, types: %2$s )', 'caveni-io'), $files_size, $file_types);
			add_meta_box(
				'caveni_ticket_file',
				$file_label,
				array($this, 'caveni_ticket_file_callback'),
				$post_type,
				'normal',
				'high'
			);
			if ('publish' === $post->post_status) {
				add_meta_box(
					'caveni_ticket_comments',
					__('Comments', 'caveni-io'),
					array($this, 'caveni_ticket_comment_callback'),
					$post_type,
					'normal',
					'high'
				);
				add_meta_box(
					'caveni_ticket_reply',
					__('Reply', 'caveni-io'),
					array($this, 'caveni_ticket_reply_callback'),
					$post_type,
					'normal',
					'high'
				);
			}
			add_meta_box(
				'caveni_ticket_status',
				__('Status', 'caveni-io'),
				array($this, 'caveni_ticket_status_callback'),
				$post_type,
				'side',
				'core'
			);
			add_meta_box(
				'caveni_ticket_category',
				__('Categories', 'caveni-io'),
				array($this, 'caveni_ticket_category_callback'),
				$post_type,
				'side',
				'core'
			);
		}
	}
	/**
	 * Callback function of ticket file attchments.
	 *
	 * @since    1.0.0
	 * @param    object $ticket object of ticket detail.
	 */
	public function caveni_ticket_file_callback($ticket)
	{
		$ticket_settings = get_option('caveni_settings');
		$file            = get_wp_scaled_image_url($ticket->ID, 'full');
		$file            = !empty($file) ? $file : '';
		$file_types      = isset($ticket_settings['file_types']) ? esc_html($ticket_settings['file_types']) : '';
?>
		<div id="caveni_file_upload_container">
			<?php wp_nonce_field('caveni_io_tickets', '_caveni_io_tickets_nonce', true); ?>
			<input type="file" name="caveni_ticket_file" accept="<?php echo esc_html($file_types); ?>" />
			<a href="<?php echo $file; ?>" target="_blank" download><?php echo esc_html(basename(esc_html($file))); ?></a>
		</div>
	<?php
	}
	/**
	 * Callback function of ticket status.
	 *
	 * @since    1.0.0
	 * @param    object $ticket object of ticket detail.
	 */
	public function caveni_ticket_status_callback($ticket)
	{
		$status = get_post_meta($ticket->ID, '_caveni_ticket_status', true);
	?>
		<div id='caveni_status_ticket'>
			<ul>
				<li>
					<label>
						<input type="radio" name="status" value="open" <?php echo ('open' === $status || empty($status)) ? 'checked="checked"' : ''; ?> />
						<?php esc_html_e('Open', 'caveni-io'); ?>
					</label>
				</li>
				<li>
					<label>
						<input type="radio" name="status" value="closed" <?php echo ('closed' === $status) ? 'checked="checked"' : ''; ?> />
						<?php esc_html_e('Closed', 'caveni-io'); ?>
					</label>
				</li>
			</ul>
		</div>
		<?php
	}
	/**
	 * Callback function of ticket Comments.
	 *
	 * @since    1.0.0
	 * @param    object $ticket object of ticket detail.
	 */
	public function caveni_ticket_comment_callback($ticket)
	{
		$ticket_comments = get_comments_by_ticket_id($ticket->ID);
		if ($ticket_comments) {
			echo '<div class="card support-converbody custom-card">';
			foreach ($ticket_comments as $comment) {
				//$author = get_comment_author( $comment->comment_ID); 
				$author_avatar = get_avatar($comment->user_id, 48);
				$is_admin = check_if_user_is_admin($comment->user_id);
				//echo "<pre>";print_r($comment);exit;
				$to_time = strtotime("now");
				$from_time = strtotime($comment->comment_date_gmt);
				$diff_in_minutes = round(abs($to_time - $from_time) / 60, 2);
				$comment_creator_class  = ($is_admin) ? "bg-primary" : "bg-success";
				$comment_creator_text = ($is_admin) ? __('ADMIN', 'caveni-io') : __('CLIENT', 'caveni-io');
				$comment_attachment = get_wp_scaled_image_url($comment->comment_ID, 'full', true);
				$ticket_status = get_post_meta($ticket->ID, '_caveni_ticket_status', true);
		?>
				<div class="card-body border-bottom">
					<div class="d-sm-flex">
						<div class="d-flex mr-3">
							<div class="avatar avatar-lg avatar-rounded">
								<!-- <img src="../assets/images/users/16.jpg" alt="img">-->
								<?php echo $author_avatar; ?>

							</div>
						</div>
						<div class="media-body comments-body">
							<h5 class="mt-1 mb-1 fw-semibold"><?php echo $comment->comment_author; ?>
								<span class="badge <?php echo $comment_creator_class; ?> bg-md ml-2"><?php echo $comment_creator_text; ?></span>
							</h5>
							<small class="text-muted"><span class="dashicons dashicons-clock"></span> <?php echo get_minutes_ago_time(get_comment_date('U', $comment->comment_ID)) ?></small>
							<p class="fs-13 mb-2 mt-1 supportnote-body">
								<?php echo trim($comment->comment_content); ?>
							</p>
							<?php if ($comment_attachment != "") { ?>
								<div class="row">
									<div class="col-lg-4 col-md-3 mt-2 file-area">
										<a href="<?php echo $comment_attachment; ?>" download class="attach-supportfiles">
											<span class="dashicons dashicons-download"></span>
											<div class="attach-title">

												<?php echo esc_html(basename(esc_html($comment_attachment))); ?>

											</div>
										</a>
									</div>
								</div>
							<?php } ?>
						</div>
						<?php if ($diff_in_minutes <= 15 && $ticket_status == 'open') { ?>
							<div class="ms-auto">
								<span class="action-btns edit-comment-button supportnote-icon" data-comment-id="<?php echo $comment->comment_ID; ?>" data-bs-toggle="tooltip"
									data-bs-placement="top" title="Edit"><i
										class="fe fe-edit text-primary fs-16"></i></span>
							</div>
						<?php } ?>
					</div>
				</div>
		<?php
			}
			echo "</div>";
		} else {
			esc_html_e('No Comments', 'caveni-io');
		}
	}
	/**
	 * Callback function of ticket reply.
	 *
	 * @since    1.0.0
	 * @param    object $ticket object of ticket detail.
	 */
	public function caveni_ticket_reply_callback($ticket)
	{
		$ticket_settings = get_option('caveni_settings');
		$file_types      = isset($ticket_settings['file_types']) ? esc_html($ticket_settings['file_types']) : '';
		?>
		<div class='caveni_comments'></div>
		<div class="add_comment">
			<form id='ticket_reply' enctype="multipart/form-data">
				<?php
				wp_editor(
					'',
					'reply_comment',
					array(
						'media_buttons' => false,
						'tinymce'       => false,
						'textarea_rows' => '5',
						'data-id'       => $ticket->ID,
					)
				);
				?>
				<input type="file" id="comment-file" name="caveni_ticket_comment_file" accept="<?php echo esc_html($file_types); ?>" />
				<br>
				<button class="button button-primary submit_ticket_comment"><?php esc_html_e('Reply', 'caveni-io'); ?></button>
				<span class="spinner caveni-spinner"></span>
			</form>
		</div>
	<?php
	}
	/**
	 * Callback function of ticket category.
	 *
	 * @since    1.0.0
	 * @param    object $ticket object of ticket detail.
	 */
	public function caveni_ticket_category_callback($ticket)
	{
		$taxonomy     = CAVENI_IO_POST_TAXONOMY;
		$terms        = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			)
		);
		$current_term = wp_get_post_terms($ticket->ID, $taxonomy, array('fields' => 'ids'));
		echo '<div>';
		echo '<ul>';
		foreach ($terms as $term) {
			$checked = (in_array($term->term_id, $current_term, true)) ? 'checked="checked"' : '';
			echo '<li>';
			echo '<label>';
			echo '<input type="radio" name="' . esc_attr($taxonomy) . '" value="' . esc_attr($term->term_id) . '" ' . esc_html($checked) . ' /> ';
			echo esc_html($term->name);
			echo '</label>';
			echo '</li>';
		}
		echo '</ul>';
		echo '</div>';
	}
	/**
	 * Add post edit form tag for file
	 *
	 * @since    1.0.0
	 */
	public function update_edit_form()
	{
		echo ' enctype="multipart/form-data"';
	}
	/**
	 * Save function of ticket file attchments.
	 *
	 * @since    1.0.0
	 * @param    integer $ticket_id ID of ticket.
	 */
	public function caveni_io_save_ticket_meta_box($ticket_id)
	{
		// Check if this is the right post type.
		if ('caveni_ticket' !== get_post_type($ticket_id)) {
			return; // Exit if not the specified post type.
		}
		if (! current_user_can('manage_options')) {
			return;
		}
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		if (! isset($_POST['_caveni_io_tickets_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_caveni_io_tickets_nonce'])), 'caveni_io_tickets')) {
			return;
		}
		if (array_key_exists('caveni_ticket_file', $_FILES)) {
			$ticket_settings = get_option('caveni_settings');
			$size            = isset($ticket_settings['file_size']) ? $ticket_settings['file_size'] : '';
			$file            = $_FILES['caveni_ticket_file'];
			$max_size        = intval($size) * 1024;
			$image_id = 0;
			$file_url        = '';
			if (! empty($file)) {
				if (UPLOAD_ERR_OK === $file['error']) {
					// Check file type.
					if ($file['size'] <= $max_size) {
						$upload_dir = wp_upload_dir();
						$name = basename($file['name']);
						$file_path  = $upload_dir['path'] . '/' . $name;
						$wp_filetype = wp_check_filetype($name, null);

						// Move the uploaded file.
						if (move_uploaded_file($file['tmp_name'], $file_path)) {
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

					update_post_meta($ticket_id, '_caveni_tickets_file', $image_id);
				}
			}
		}
		if (isset($_POST['status']) && ! empty($_POST['status'])) {
			update_post_meta($ticket_id, '_caveni_ticket_status', sanitize_text_field(wp_unslash($_POST['status'])));
		}
		if (isset($_POST['caveni_ticket_category'])) {
			wp_set_post_terms($ticket_id, array(intval($_POST['caveni_ticket_category'])), 'caveni_ticket_category');
		} else {
			wp_set_post_terms($ticket_id, array(), $taxonomy); // Clear the taxonomy if no radio button is selected.
		}
	}
	/**
	 * Add comment in ticket.
	 *
	 * @since    1.0.0
	 */
	public function reply_ticket_comment()
	{
		if (! current_user_can('manage_options')) {
			return;
		}
		if (! isset($_POST['_caveni_io_tickets_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_caveni_io_tickets_nonce'])), 'caveni_io_tickets')) {
			return;
		}
		$comment_post_id = isset($_POST['ticket_id']) ? (int) $_POST['ticket_id'] : '';
		$post            = get_post($comment_post_id);

		if (! $post) {
			wp_die(-1);
		}

		if (! current_user_can('edit_post', $comment_post_id)) {
			wp_die(-1);
		}

		if (empty($post->post_status)) {
			wp_die(1);
		} elseif (in_array($post->post_status, array('draft', 'pending', 'trash'), true)) {
			wp_die(esc_html__('You cannot reply to a comment on a draft post.'));
		}
		$user = wp_get_current_user();
		if ($user->exists()) {
			$comment_author       = wp_slash($user->display_name);
			$comment_author_email = wp_slash($user->user_email);
			$comment_author_url   = wp_slash($user->user_url);
			$user_id              = $user->ID;
		}
		$comment_content = isset($_POST['comment']) ? trim(sanitize_text_field(wp_unslash($_POST['comment']))) : '';
		if ('' === $comment_content) {
			wp_die(esc_html__('Please type your comment text.'));
		}
		$comment_type          = 'comment';
		$comment_parent        = 0;
		$comment_auto_approved = false;
		$commentdata           = array(
			'comment_post_ID' => $comment_post_id,
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
		$position = '-1';
		if (array_key_exists('caveni_ticket_comment_file', $_FILES)) {
			$ticket_settings = get_option('caveni_settings');
			$size            = isset($ticket_settings['file_size']) ? $ticket_settings['file_size'] : '';
			$file            = $_FILES['caveni_ticket_comment_file'];
			$max_size        = intval($size) * 1024;
			$image_id = 0;
			$file_url        = '';
			if (! empty($file)) {
				if (UPLOAD_ERR_OK === $file['error']) {
					// Check file type.
					if ($file['size'] <= $max_size) {
						$upload_dir = wp_upload_dir();
						//echo "<pre>";print_r($upload_dir);exit;
						$name = basename($file['name']);
						$file_path  = $upload_dir['path'] . '/' . $name;
						$wp_filetype = wp_check_filetype($name, null);

						// Move the uploaded file.
						if (move_uploaded_file($file['tmp_name'], $file_path)) {
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
					update_comment_meta($comment_id, '_ticket_comment_reply_attachment', $image_id);
				}
			}
		}
		$response = array();
		wp_send_json_success($response);
	}
	/**
	 * Add custom columns ticket in caveni io.
	 *
	 * @since    1.0.0
	 * @param array $columns Array of columns name.
	 */
	public function caveni_io_ticket_columns($columns)
	{
		unset($columns['author']);
		unset($columns['title']);
		unset($columns['taxonomy-caveni_ticket_category']);
		unset($columns['comments']);
		unset($columns['date']);
		$columns['ticket_status']                   = __('Status', 'caveni-io');
		$columns['title']                           = __('Title', 'caveni-io');
		$columns['ticket_id']                       = __('Ticket ID', 'caveni-io');
		$columns['taxonomy-caveni_ticket_category'] = __('Categories', 'caveni-io');
		$columns['author']                          = __('Created by', 'caveni-io');
		$columns['date']                            = __('Date', 'caveni-io');
		$columns['comments']                        = __('Activity', 'caveni-io');
		return $columns;
	}
	/**
	 * Add value custom columns ticket in caveni io.
	 *
	 * @since    1.0.0
	 * @param string $column column name.
	 * @param init   $ticket_id Id of ticket.
	 */
	public function caveni_io_ticket_column($column, $ticket_id)
	{
		switch ($column) {
			case 'ticket_id':
				echo '#' . esc_html($ticket_id);
				break;
			case 'ticket_status':
				$status = get_post_meta($ticket_id, '_caveni_ticket_status', true);
				$color  = '#0ABB87';
				$text   = __('Open', 'caveni-io');
				if ('closed' === $status) {
					$color = '#ED2121';
					$text  = __('Closed', 'caveni-io');
				}
				echo '<span class="caveni-io-label" style="background-color:' . esc_attr($color) . '">' . esc_html($text) . '</span>';
				break;
		}
	}
	/**
	 * Add custom field for attachment in caveni io.
	 *
	 * @since    1.0.0
	 */
	public function caveni_io_comment_attachment()
	{
		echo '<p class="comment-form-file">
			<label for="file">Upload File</label>
			<input type="file" name="comment_file" id="file" />
		</p>';
	}
	/**
	 * Add custom Filter ticket in caveni io.
	 *
	 * @since    1.0.0
	 * @param string $post_type Slug of post type.
	 */
	public function caveni_ticket_admin_filters($post_type)
	{
		if ('caveni_ticket' !== $post_type) {
			return;
		}
		$taxonomies_slugs = array(
			'caveni_ticket_category',
		);
		// loop through the taxonomy filters array.
		foreach ($taxonomies_slugs as $slug) {
			$taxonomy = get_taxonomy($slug);
			$selected = '';
			// if the current page is already filtered, get the selected term slug.
			$selected = isset($_REQUEST[$slug]) ? $_REQUEST[$slug] : '';
			// render a dropdown for this taxonomy's terms.
			wp_dropdown_categories(
				array(
					'show_option_all' => $taxonomy->labels->all_items,
					'taxonomy'        => $slug,
					'name'            => $slug,
					'orderby'         => 'name',
					'value_field'     => 'slug',
					'selected'        => $selected,
					'hierarchical'    => false,
				)
			);
		}
		$selected = isset($_REQUEST['ticket_status']) ? $_REQUEST['ticket_status'] : '';
	?>
		<select name='ticket_status'>
			<option value="none"><?php esc_html_e('All Status', 'caveni-io'); ?></option>
			<option value="open" <?php echo ('open' === $selected) ? 'selected' : ''; ?>><?php esc_html_e('Open', 'caveni-io'); ?></option>
			<option value="closed" <?php echo ('closed' === $selected) ? 'selected' : ''; ?>><?php esc_html_e('Closed', 'caveni-io'); ?></option>
		</select>
<?php
	}
	/**
	 * Add custom Filter ticket in caveni io.
	 *
	 * @since    1.0.0
	 * @param array $query Query of post.
	 */
	public function caveni_ticket_filter($query)
	{
		if (! is_admin() || ! $query->is_main_query() || $query->get('post_type') !== 'caveni_ticket') {
			return;
		}
		if (isset($_GET['ticket_status']) && ! empty($_GET['ticket_status'])) {
			$meta_value = sanitize_text_field($_GET['ticket_status']);
			if ('none' !== $meta_value) {
				$meta_query = array(
					array(
						'key'   => '_caveni_ticket_status', // Replace with your meta key.
						'value' => $meta_value,
					),
				);
				$query->set('meta_query', $meta_query);
			}
		}
	}
}
