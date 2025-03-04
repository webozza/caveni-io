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
class Caveni_Io_Admin
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
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Caveni_Io_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Caveni_Io_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if (isset($_GET['page']) && 'caveni-io' === $_GET['page']) {
			wp_enqueue_style('bootstrap', CAVENI_IO_URL . 'libs/bootstrap/css/bootstrap.min.css', array(), $this->version, 'all');
			wp_enqueue_style($this->caveni_io . "-icons", CAVENI_IO_URL  . 'public/css/icons.css', array(), $this->version, 'all');
			wp_enqueue_style($this->caveni_io . "-dashboard", plugin_dir_url(__FILE__) . 'css/caveni-io-dashboard.css', array(), $this->version, 'all');
		}
		wp_enqueue_style('select2', plugin_dir_url(__FILE__) . 'css/select2.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->caveni_io, plugin_dir_url(__FILE__) . 'css/caveni-io-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Caveni_Io_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Caveni_Io_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		// /print_r($_GET);exit;
		if (isset($_GET['page']) && 'caveni-io' === $_GET['page']) {
			wp_enqueue_script('bootstrap', CAVENI_IO_URL . 'libs/bootstrap/js/bootstrap.bundle.min.js', array(), $this->version, true);
			wp_enqueue_script('jquery-validator', CAVENI_IO_URL . 'public/js/jquery.validate.min.js', array('jquery'), $this->version, true);
		} else if ((isset($_GET['post_type']) && 'caveni_ticket' === $_GET['post_type']) || (isset($_GET['post']) && 'caveni_ticket' === get_post($_GET['post'])->post_type)) {
			wp_enqueue_script('jquery-validator', CAVENI_IO_URL . 'public/js/jquery.validate.min.js', array('jquery'), $this->version, true);
		}
		wp_enqueue_script('select2', plugin_dir_url(__FILE__) . 'js/select2.full.min.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->caveni_io, plugin_dir_url(__FILE__) . 'js/caveni-io-admin.js', array('jquery'), $this->version, false);
		$ticket_settings = get_option('caveni_settings');
		$allowed_file_types_str = isset($ticket_settings['file_type']) ? esc_html($ticket_settings['file_type']) : '.jpg,.png,.gif';
		$allowed_file_size = isset($ticket_settings['file_size']) ? esc_html($ticket_settings['file_size']) : '2000';
		$max_size        = intval($allowed_file_size) * 1024;
		$no_files        = isset($ticket_settings['files']) ? esc_html($ticket_settings['files']) : '';
		wp_localize_script(
			$this->caveni_io,
			'caveni',
			array(
				'file_number' => $no_files,
				'allowed_file_types' => $allowed_file_types_str,
				'allowed_file_types_msg' => wp_sprintf(__('Upload File Must of of type  %2$s and Max size is %1$s KB', 'caveni-io'), $allowed_file_size, $allowed_file_types_str),
				'category_name_required_msg' => wp_sprintf(__('Please Add a category name before Sumitting', 'caveni-io')),
				'allowed_file_size' => $max_size,
				'optionsUrl' => admin_url('options.php'),
				'ajaxurl' => admin_url('admin-ajax.php'),
				'security' => wp_create_nonce('update_settings_nonce_action'),
				'security_category' => wp_create_nonce('delete_category_nonce_action'),
			)
		);
	}
	/**
	 * Add admin menu in caveni io.
	 *
	 * @since    1.0.0
	 */
	public function caveni_io_admin_menu()
	{
		// $settings = get_option('caveni_settings');
		// pre($settings, 1);
		add_menu_page(
			__('Caveni.IO', 'caveni-io'),
			__('Caveni.IO', 'caveni-io'),
			'manage_options',
			'caveni-io',
			false,
			CAVENI_IO_URL . 'public/images/caveni-logo-io-size.png',
			10
		);
		add_submenu_page(
			'caveni-io',
			__('Plugin Hub', 'caveni-io'),
			__('Plugin Hub', 'caveni-io'),
			'manage_options',
			'caveni-io',
			array($this, 'caveni_io_callback'),
		);
		if (check_ticket_enable()) {
			add_submenu_page(
				'caveni-io',
				__('Tickets', 'caveni-io'),
				__('HelpDesk', 'caveni-io'),
				'manage_options',
				'edit.php?post_type=caveni_ticket',
			);
			// add_submenu_page(
			// 	'caveni-io',
			// 	__( 'Categories', 'caveni-io' ),
			// 	__( 'Categories', 'caveni-io' ),
			// 	'manage_options',
			// 	'edit-tags.php?taxonomy=caveni_ticket_category&post_type=caveni_ticket',
			// );
		}
	}
	/**
	 * Display settings page.
	 *
	 * @since    1.0.0
	 */
	public function caveni_io_callback()
	{
		$all_categories = get_all_ticket_categories();
		include_once plugin_dir_path(__FILE__) . 'partials/caveni-io-admin-display.php';
	}
	/**
	 * Initialize all functions required for runing admin.
	 *
	 * @since    1.0.0
	 */
	public function initialize_settings()
	{
		register_setting(
			'caveni_settings',
			'caveni_settings',
			array(
				'sanitize_callback' => array($this, 'caveni_settins_sanitize_before_save')
			)
		);
	}

	public function caveni_settins_sanitize_before_save($setting)
	{

		$file_types = trim($setting['file_types']);
		$email_content = trim($setting['email_content']);
		$setting['file_types'] = $file_types;
		$setting['email_content'] = $email_content;
		return $setting;
	}

	/**
	 * Adds "Categories" button on ticket list page
	 */
	public function add_categories_and_back_button()
	{
		global $current_screen;

		// Not our post type, exit earlier
		// You can remove this if condition if you don't have any specific post type to restrict to. 
		if ('caveni_ticket' != $current_screen->post_type) {
			return;
		}

		if ($current_screen->taxonomy == "caveni_ticket_category") {
?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					jQuery(jQuery("#col-left")).prepend("<a href='<?php echo admin_url('edit.php?post_type=caveni_ticket', 'https'); ?>'  id='back_to_tickets_button' style='margin-left: 0px;' class='add-new-h2'><?php echo __('Go back to Tickets', 'caveni-io'); ?></a>");
				});
			</script>
		<?php
		} else {
		?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					jQuery(jQuery(".wrap a.page-title-action")[0]).after("<a href='<?php echo admin_url('edit-tags.php?taxonomy=caveni_ticket_category&post_type=caveni_ticket', 'https'); ?>'  id='add_categories_button' class='add-new-h2'><?php echo __('Add New Categories', 'caveni-io'); ?></a>");
				});
			</script>
<?php
		}
	}

	public function add_category_backend()
	{
		check_ajax_referer('add_category_nonce_action', 'add_category_nonce');
		if (! current_user_can('manage_options')) {
			return;
		}
		$response = array(
			'status' => 'failed',
			'message' => __("There is an issue with the ajax call", 'caveni-io')
		);

		if ($_POST) {

			$category_name = isset($_POST['category_name_field']) ?  esc_html($_POST['category_name_field']) : '';

			if ($category_name == '') {
				$response['status'] = 'error';
				$response['message'] = __("Category name should not empty", 'caveni-io');
				echo json_encode($response);
				exit;
			}

			$parent = wp_insert_term($category_name, CAVENI_IO_POST_TAXONOMY); // I'll leave out `$args` here
			if (is_wp_error($parent)) {
				$response['status'] = 'error';
				$response['message'] = __("Category creation issue", 'caveni-io');
				echo json_encode($response);
				exit;
			}
			$response['status'] = 'success';
			$response['message'] = __("Category Created successfully. You will be redirected in 5 seconds", 'caveni-io');
			$response['redirectUrl'] = '';
			echo json_encode($response);
			exit;
		}

		echo json_encode($response);
		exit;
	}


	public function edit_category_backend()
	{
		check_ajax_referer('edit_category_nonce_action', 'edit_category_nonce');
		if (! current_user_can('manage_options')) {
			return;
		}
		$response = array(
			'status' => 'failed',
			'message' => __("There is an issue with the ajax call", 'caveni-io')
		);

		if ($_POST) {

			$category_id = isset($_POST['category_id']) ?  (int) $_POST['category_id'] : 0;
			$category_name = isset($_POST['edit_category_name_field']) ?  esc_html($_POST['edit_category_name_field']) : '';

			if ($category_name == '' || $category_id <= 0) {
				$response['status'] = 'error';
				$response['message'] = __("Category name and Id should not empty", 'caveni-io');
				echo json_encode($response);
				exit;
			}



			$update = wp_update_term($category_id, CAVENI_IO_POST_TAXONOMY, array(
				'name' => $category_name
			));

			if (! is_wp_error($update)) {
				$response['status'] = 'success';
				$response['message'] = __("Category Updated successfully. You will be redirected in 5 seconds", 'caveni-io');
				$response['redirectUrl'] = '';
				echo json_encode($response);
				exit;
			}

			$response['status'] = 'error';
			$response['message'] = __("Category updation issue", 'caveni-io');
			echo json_encode($response);
			exit;
		}

		echo json_encode($response);
		exit;
	}

	function delete_category_action()
	{

		check_ajax_referer('delete_category_nonce_action', 'delete_category_nonce');
		if (! current_user_can('manage_options')) {
			return;
		}

		if (!is_user_admin_or_in_array(array())) return;

		$response = array(
			'status' => 'failed',
			'message' => __("There is an issue with the ajax call", 'caveni-io')
		);

		if ($_POST) {
			$delete_category_id = isset($_POST['delete_category_id']) ?  (int) $_POST['delete_category_id'] : 0;
			if ($delete_category_id <= 0) {
				$response['status'] = 'error';
				$response['message'] = __("Issue with the Category Id", 'caveni-io');
				echo json_encode($response);
				exit;
			}

			if (wp_delete_term($delete_category_id, CAVENI_IO_POST_TAXONOMY)) {

				$response['status'] = 'success';
				$response['message'] = __("Category is deleted successfully. You will be redirected in 5 seconds", 'caveni-io');
				$response['redirectUrl'] = '';
				echo json_encode($response);
				exit;
			}
		}

		echo json_encode($response);
		exit;
	}
}
