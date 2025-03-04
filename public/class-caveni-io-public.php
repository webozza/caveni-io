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
class Caveni_Io_Public
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
	 * The  variable of initial helpdesk class.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      object    $helpdesk   Variable of helpdesk class.
	 */
	protected $helpdesk;
	protected $messages;
	protected $clients;
	protected $seo;
	protected $ppc;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $caveni_io       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 * @param      object $helpdesk   Variable of helpdesk class.
	 */
	public function __construct($caveni_io, $version, $helpdesk, $messages, $clients, $seo, $ppc)
	{

		$this->caveni_io = $caveni_io;
		$this->version   = $version;
		$this->helpdesk  = $helpdesk;
		$this->messages  = $messages;
		$this->clients  = $clients;
		$this->seo  = $seo;
		$this->ppc  = $ppc;
	}
	/**
	 * The  Initial of shortcode in frontend.
	 *
	 * @since 1.0.0
	 */
	public function initialize_frontend_area()
	{
		add_shortcode('caveni-io', array($this, 'caveni_io_shortcode'));
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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
		// wp_register_style('prefix_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css'); .
		// wp_enqueue_style('prefix_bootstrap'); .
		wp_enqueue_style($this->caveni_io . "-icons", plugin_dir_url(__FILE__) . 'css/icons.css', array(), $this->version, 'all');
		wp_enqueue_style($this->caveni_io . "-choices-css", plugin_dir_url(__FILE__) . 'libs/choices.js/public/assets/styles/choices.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->caveni_io . "-quill-snow-css", plugin_dir_url(__FILE__) . 'libs/quill/quill.snow.css', array(), $this->version, 'all');
		wp_enqueue_style($this->caveni_io . "-quill-bubble-css", plugin_dir_url(__FILE__) . 'libs/quill/quill.bubble.css', array(), $this->version, 'all');
		wp_enqueue_style($this->caveni_io . "-bootstap-data-tables", 'https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->caveni_io . "-bootstap-data-tables-responsive", 'https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->caveni_io . "-bootstap-data-tables-button", 'https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->caveni_io, plugin_dir_url(__FILE__) . 'css/caveni-io-public.css', array(), $this->version, 'all');
		wp_enqueue_style($this->caveni_io . "-main-custom", plugin_dir_url(__FILE__) . 'css/caveni-io-custom.css', array(), $this->version, 'all');
		wp_enqueue_style($this->caveni_io . "-clients-min", plugin_dir_url(__FILE__) . 'css/select2.min.css', array(), $this->version, 'all');
		// wp_enqueue_style($this->caveni_io . "-daterange-picker", 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css', array(), $this->version, 'all');
		wp_enqueue_style($this->caveni_io . "-datepicker", plugin_dir_url(__FILE__) . 'css/jquery.periodpicker.min.css', array(), $this->version, 'all');

		wp_enqueue_style($this->caveni_io . "-clients-custom", plugin_dir_url(__FILE__) . 'css/caveni-io-clients.css', array(), $this->version, 'all');
		wp_enqueue_style($this->caveni_io . "-clients-seo", plugin_dir_url(__FILE__) . 'css/caveni-io-seo.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		// wp_register_script('prefix_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js');.
		// wp_enqueue_script('prefix_bootstrap');.
		wp_enqueue_script($this->caveni_io . "-choices-js", plugin_dir_url(__FILE__) . 'libs/choices.js/public/assets/scripts/choices.min.js', array('jquery'), $this->version, true);

		wp_enqueue_script($this->caveni_io . "-quill-lib-js", plugin_dir_url(__FILE__) . 'libs/quill/quill.min.js', array('jquery'), $this->version, true);
		wp_enqueue_script($this->caveni_io . "-jquery-validate", plugin_dir_url(__FILE__) . 'js/jquery.validate.min.js', array('jquery'), $this->version, true);
		wp_enqueue_script($this->caveni_io . "-jquery-scroll-js", plugin_dir_url(__FILE__) . 'js/jquery.scrollTo.min.js', array('jquery'), $this->version, true);
		wp_enqueue_script($this->caveni_io, plugin_dir_url(__FILE__) . 'js/caveni-io-public.js', array('jquery'), time(), true);
		wp_enqueue_script($this->caveni_io . "jquery-data-tables", 'https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js', array('jquery'), $this->version, true);
		wp_enqueue_script($this->caveni_io . "jquery-data-tables-boot", 'https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js', array('jquery'), $this->version, true);
		wp_enqueue_script($this->caveni_io . "jquery-data-tables-res", 'https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js', array('jquery'), $this->version, true);
		// cilents js
		wp_enqueue_script($this->caveni_io . "-clients-select", plugin_dir_url(__FILE__) . 'js/select2.min.js', array('jquery'), $this->version, true);
		wp_enqueue_script($this->caveni_io . "-clients", plugin_dir_url(__FILE__) . 'js/caveni-clients.js', array('jquery'), $this->version, true);



		// /* Enque js for date period picker */
		// wp_enqueue_script($this->caveni_io . "jquery-moment", 'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', array('jquery'), $this->version, false);
		// wp_enqueue_script($this->caveni_io . "jquery-daternage-picker", 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', array('jquery'), $this->version, false);


		$settings = get_option('caveni_settings');
		$allowed_file_types_str = isset($settings['file_type']) ? esc_html($settings['file_type']) : '.jpg,.png,.gif';
		$allowed_file_size = isset($settings['file_size']) ? esc_html($settings['file_size']) : '2000';
		$max_size        = intval($allowed_file_size) * 1024;
		$no_files        = isset($settings['files']) ? esc_html($settings['files']) : '';

		wp_localize_script(
			$this->caveni_io,
			'ajaxObj',
			array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'homeUrl' => home_url('/'),
				'caveniUser' => get_current_user_id(),
				'caveniReceviers' => get_admin_user_ids(),
				'security' => wp_create_nonce('delete_ticket_nonce_action'),
				'security_message' => wp_create_nonce('message_nonce_action'),
				'search_message' => wp_create_nonce('search_message_nonce_action'),
				'security_message_delete' => wp_create_nonce('delete_message_nonce_action'),
				'security_comment' => wp_create_nonce('delete_ticket_comment_nonce_action'),
				'file_number' => $no_files,
				'allowed_file_types' => $allowed_file_types_str,
				'allowed_file_types_msg' => wp_sprintf(__('Upload File Must of of type  %2$s and Max size is %1$s KB', 'caveni-io'), $allowed_file_size, $allowed_file_types_str),
				'allowed_file_size' => $max_size
			)
		);
	}
	/**
	 * Callback function shortcode helpdesk.
	 *
	 * @since    1.0.0
	 * @param    array $atts array of attribute pass in shortcode.
	 */
	public function caveni_io_shortcode($atts)
	{

		$attributes = shortcode_atts(array('module' => 'messages'), $atts);

		if (is_object($this->{$attributes['module']})) {
			return $this->{$attributes['module']}->perform_shortcode_action($attributes, $_GET);
		}
		return __('Shortcode Module is not correct please check your shortcode.', 'caveni-io');
	}
}
