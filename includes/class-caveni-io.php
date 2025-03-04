<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://xfinitysoft.com
 * @since      1.0.0
 *
 * @package    Caveni_Io
 * @subpackage Caveni_Io/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Caveni_Io
 * @subpackage Caveni_Io/includes
 * @author     Xfinity Soft <email@example.com>
 */
class Caveni_Io
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Caveni_Io_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $caveni_io    The string used to uniquely identify this plugin.
	 */
	protected $caveni_io;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('CAVENI_IO_VERSION')) {
			$this->version = CAVENI_IO_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->caveni_io = 'caveni-io';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Caveni_Io_Loader. Orchestrates the hooks of the plugin.
	 * - Caveni_Io_i18n. Defines internationalization functionality.
	 * - Caveni_Io_Admin. Defines all hooks for the admin area.
	 * - Caveni_Io_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(__DIR__) . 'includes/class-caveni-io-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(__DIR__) . 'includes/class-caveni-io-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(__DIR__) . 'admin/class-caveni-io-admin.php';

		/**
		 * The class responsible for defining all helpdesk actions that occur in the admin area.
		 */
		require_once plugin_dir_path(__DIR__) . 'admin/class-caveni-io-helpdesk.php';

		/**
		 * The class responsible for defining all helpdesk actions that occur in the admin area.
		 */
		require_once plugin_dir_path(__DIR__) . 'admin/class-caveni-io-messages.php';





		/**
		 * The class responsible for defining all helpdesk actions that occur in the admin area.
		 */
		require_once plugin_dir_path(__DIR__) . 'common/functions.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(__DIR__) . 'public/class-caveni-io-public.php';
		require_once plugin_dir_path(__DIR__) . 'public/class-caveni-io-helpdesk-front.php';
		require_once plugin_dir_path(__DIR__) . 'public/class-caveni-io-messages-front.php';
		require_once plugin_dir_path(__DIR__) . 'public/class-caveni-io-clients-front.php';
		require_once plugin_dir_path(__DIR__) . 'public/class-caveni-io-seo-front.php';
		require_once plugin_dir_path(__DIR__) . 'public/class-caveni-io-ppc-front.php';

		$this->loader = new Caveni_Io_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Caveni_Io_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{
		$plugin_i18n = new Caveni_Io_I18n();
		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_helpdesk = new Caveni_Io_HelpDesk($this->get_caveni_io(), $this->get_version());
		$plugin_admin    = new Caveni_Io_Admin($this->get_caveni_io(), $this->get_version());
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_menu', $plugin_admin, 'caveni_io_admin_menu');
		$this->loader->add_action('init', $plugin_admin, 'initialize_settings');
		$this->loader->add_action('admin_init', $plugin_helpdesk, 'add_caveni_ticket_capabilities');
		$this->loader->add_action('admin_head', $plugin_admin, 'add_categories_and_back_button');
		$this->loader->add_action('init', $plugin_helpdesk, 'initialize_helpdesk');
		$this->loader->add_action('add_meta_boxes', $plugin_helpdesk, 'cavenio_add_files_meta_box', 10, 2);
		$this->loader->add_action('post_edit_form_tag', $plugin_helpdesk, 'update_edit_form');
		$this->loader->add_action('save_post', $plugin_helpdesk, 'caveni_io_save_ticket_meta_box');
		$this->loader->add_filter('manage_caveni_ticket_posts_columns', $plugin_helpdesk, 'caveni_io_ticket_columns');
		$this->loader->add_action('manage_caveni_ticket_posts_custom_column', $plugin_helpdesk, 'caveni_io_ticket_column', 200, 2);
		$this->loader->add_action('restrict_manage_posts', $plugin_helpdesk, 'caveni_ticket_admin_filters', 10, 1);
		$this->loader->add_action('pre_get_posts', $plugin_helpdesk, 'caveni_ticket_filter');
		$this->loader->add_action('comment_form_logged_in_after', $plugin_helpdesk, 'caveni_io_comment_attachment');
		$this->loader->add_action('comment_form_after_fields', $plugin_helpdesk, 'caveni_io_comment_attachment');
		$this->loader->add_action('wp_ajax_reply_ticket_comment', $plugin_helpdesk, 'reply_ticket_comment');
		$this->loader->add_action('wp_ajax_add_category_backend_action', $plugin_admin, 'add_category_backend');
		$this->loader->add_action('wp_ajax_edit_category_backend_action', $plugin_admin, 'edit_category_backend');

		$this->loader->add_action('wp_ajax_caveni_io_delete_category_action', $plugin_admin, 'delete_category_action');
		// cilents shortcode

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_helpdesk_front = new Caveni_Io_HelpDesk_Front($this->get_caveni_io(), $this->get_version());
		$plugin_messages_front = new Caveni_Io_Messages_Front($this->get_caveni_io(), $this->get_version());
		$plugin_cilents_front  = new Caveni_Io_Clients_Front($this->get_caveni_io(), $this->get_version());
		$Caveni_Io_Seo_Front  = new Caveni_Io_Seo_Front($this->get_caveni_io(), $this->get_version());
		$Caveni_Io_Ppc_Front  = new Caveni_Io_Ppc_Front($this->get_caveni_io(), $this->get_version());

		$plugin_public         = new Caveni_Io_Public($this->get_caveni_io(), $this->get_version(), $plugin_helpdesk_front, $plugin_messages_front, $plugin_cilents_front, $Caveni_Io_Seo_Front, $Caveni_Io_Ppc_Front);

		$this->loader->add_action('init', $plugin_public, 'initialize_frontend_area');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
		//$this->loader->add_filter( 'wp_nav_menu_items', $plugin_helpdesk_front, 'add_helpdesk_menu_item', 10, 2);
		$this->loader->add_action('comment_post', $plugin_helpdesk_front, 'disable_comment_notification_emails', 10, 2);
		$this->loader->add_action('wp_ajax_caveni_io_create_ticket_action', $plugin_helpdesk_front, 'create_ticket_action');
		$this->loader->add_action('wp_ajax_caveni_io_reply_ticket_action', $plugin_helpdesk_front, 'reply_ticket_action');
		$this->loader->add_action('wp_ajax_caveni_io_update_reply_ticket_action', $plugin_helpdesk_front, 'update_reply_ticket_action');
		$this->loader->add_action('wp_ajax_caveni_io_close_ticket_action', $plugin_helpdesk_front, 'close_ticket_action');
		$this->loader->add_action('wp_ajax_caveni_io_delete_ticket_action', $plugin_helpdesk_front, 'delete_ticket_action');
		$this->loader->add_action('wp_ajax_caveni_io_delete_ticket_comment_action', $plugin_helpdesk_front, 'delete_ticket_comment_action');

		$this->loader->add_action('wp_ajax_caveni_io_load_user_messages', $plugin_messages_front, 'load_user_messages');
		$this->loader->add_action('wp_ajax_caveni_io_send_message', $plugin_messages_front, 'ci_send_message');
		$this->loader->add_action('wp_ajax_caveni_io_delete_messages', $plugin_messages_front, 'ci_delete_message');
		$this->loader->add_action('wp_ajax_caveni_io_search_messages', $plugin_messages_front, 'ci_search_messages');
		$this->loader->add_action('deleted_user', $plugin_messages_front, 'ci_deleted_user', 10, 3);

		$this->loader->add_action('wp_ajax_caveni_io_add_new_client', $plugin_cilents_front, 'caveni_io_add_new_client');
		// $this->loader->add_action('wp_ajax_nopriv_caveni_io_add_new_client', $plugin_cilents_front, 'caveni_io_add_new_client');
		$this->loader->add_action('wp_ajax_caveni_fetch_client_data', $plugin_cilents_front, 'caveni_fetch_client_data');
		$this->loader->add_action('wp_ajax_caveni_client_delete', $plugin_cilents_front, 'caveni_client_delete_callback');

		//caveni seo scripts
		$this->loader->add_action('wp_enqueue_scripts', $Caveni_Io_Seo_Front, 'enqueue_scripts');
		$this->loader->add_action('wp_ajax_caveni_get_seo_data', $Caveni_Io_Seo_Front, 'caveni_get_seo_data_callback');
		$this->loader->add_action('wp_enqueue_scripts', $Caveni_Io_Ppc_Front, 'enqueue_scripts');
		$this->loader->add_action('wp_ajax_caveni_get_ppc_data', $Caveni_Io_Ppc_Front, 'caveni_get_ppc_data_callback');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_caveni_io()
	{
		return $this->caveni_io;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Caveni_Io_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
