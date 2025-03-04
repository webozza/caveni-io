<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://xfinitysoft.com
 * @since      1.0.0
 *
 * @package    Caveni_Io
 * @subpackage Caveni_Io/admin/partials
 */

$selected_tab = 'ticket-settings';
if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'caveni' ) ) {
	if ( isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ) {
		$selected_tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
	}
}
?>
<div class="wrap">
	<h2><?php esc_html_e( 'Caveni', 'caveni-io' ); ?></h2>
	<?php settings_errors(); ?>
	<nav class="nav-tab-wrapper wp-clearfix" aria-label="Secondary menu">
		<a  class="nav-tab <?php echo ( 'ticket-settings' === $selected_tab ) ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( wp_nonce_url( '?page=caveni-io&tab=ticket-settings', 'caveni' ) ); ?>">
			<?php esc_html_e( 'Ticket Settings', 'caveni-io' ); ?>    
		</a>
		<a  class="nav-tab <?php echo ( 'email-settings' === $selected_tab ) ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( wp_nonce_url( '?page=caveni-io&tab=email-settings', 'caveni' ) ); ?>">
			<?php esc_html_e( 'Email Settings', 'caveni-io' ); ?>    
		</a>
	</nav>
	<div class="tab-content">
		<?php
		switch ( $selected_tab ) {
			case 'email-settings':
				include plugin_dir_path( __FILE__ ) . 'caveni-io-email-settings.php';
				break;
			default:
				include plugin_dir_path( __FILE__ ) . 'caveni-io-ticket-settings.php';
				break;
		}
		?>
	</div>
</div>