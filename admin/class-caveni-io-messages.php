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
class Caveni_Io_Messages {

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
	public function __construct( $caveni_io, $version ) {

		$this->caveni_io = $caveni_io;
		$this->version   = $version;
	}

	/**
	 * Initialize all functions required for runing helpdesk.
	 *
	 * @since    1.0.0
	 */
	public function initialize_helpdesk() {
		$this->create_messages_table();
		
	}
	/**
	 * Register the Ticket Custom post type in caveni io.
	 *
	 * @since    1.0.0
	 */
	public function create_messages_table() {
		
	}
}
