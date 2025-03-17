<?php

/**
 * The Main plugin file.
 *
 * @link              https://xfinitysoft.com
 * @since             1.0.0
 * @package           Caveni_Io
 *
 * @wordpress-plugin
 * Plugin Name:       Caveni.IO
 * Description:       Comprehensive management software designed to streamline client interactions, project workflows, and other business operations.
 * Version:           4.7.2
 * Author:            Caveni
 * Author URI:        https://caveni.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       caveni-io
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('CAVENI_IO_VERSION', '4.7.2');
define('CAVENI_IO_PATH', plugin_dir_path(__FILE__));
define('CAVENI_IO_URL', plugin_dir_url(__FILE__));
define('CAVENI_IO_POST_TYPE', 'caveni_ticket');
define('CAVENI_IO_POST_TAXONOMY', 'caveni_ticket_category');
define('CAVENI_ENABLE_DUMMY_DATA', false);

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-caveni-io-activator.php
 */
function activate_caveni_io()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-caveni-io-activator.php';
	Caveni_Io_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-caveni-io-deactivator.php
 */
function deactivate_caveni_io()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-caveni-io-deactivator.php';
	Caveni_Io_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_caveni_io');
register_deactivation_hook(__FILE__, 'deactivate_caveni_io');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-caveni-io.php';
require plugin_dir_path(__FILE__) . 'public/class-caveni-io-reporting-front.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_caveni_io()
{
	$plugin = new Caveni_Io();
	$plugin->run();
}
run_caveni_io();


function pre($val, $die = false)
{
	echo "<pre>" . print_r($val, 1) . "</pre>";
	if ($die) {
		die("HERE");
	}
}
add_action('template_redirect', function () {
	if (isset($_GET['0202'])) {
		$avatar_url = bp_core_fetch_avatar(array(
			'item_id' => 10,
			'type'    => 'thumb', // You can change this to 'thumb' for a smaller image
			'width'   => 150,
			'height'  => 150,
			'html'    => false // Set to true if you want the HTML <img> tag
		));
		echo $avatar_url;
		die;
	}
});
