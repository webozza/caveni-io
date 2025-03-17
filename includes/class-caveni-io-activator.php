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

 class Caveni_Io_Activator {

	/**
	 * Runs on plugin activation.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;

		// Get table name with the proper prefix
		$table_name = $wpdb->prefix . 'caveni_client_reports';

		// Define the table structure
		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			client_id BIGINT(20) UNSIGNED NOT NULL,
			pdf_reference TEXT NOT NULL,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
	
		// Include WordPress upgrade functions
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		// Default settings
		$settings = get_option('caveni_settings');
		if (empty($settings)) {
			$settings = array(
				'enable'         => 'on',
				'role'           => array('contributor'),
				'file_size'      => '2000',
				'file_type'      => '.jpg, .png, .pdf',
				'email_enable'   => 'on',
				'email_subject'  => '{site} ticket',
				'email_content'  => '{ticket_message}',
			);
			update_option('caveni_settings', $settings);
		}
	}
}
