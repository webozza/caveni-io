<?php
/**
 * Fired during plugin activation
 *
 * @link       https://xfinitysoft.com
 * @since      1.0.0
 *
 * @package    Caveni_Io
 * @subpackage Caveni_Io/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Caveni_Io
 * @subpackage Caveni_Io/includes
 * @author     Xfinity Soft <email@example.com>
 */
class Caveni_Io_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$settings = get_option( 'caveni_settings' );
		if ( empty( $settings ) ) {
			$settings = array(
				'enable'    => 'on',
				'role'      => array( 'contributor' ),
				'file_size' => '2000',
				'file_type' => '.jpg, .png, .pdf',
				'email_enable'        => 'on',
				'email_subject' => '{site} ticket',
				'email_content' => '{ticket_message}',
			);
			update_option( 'caveni_settings', $settings );
		}
	}
}
