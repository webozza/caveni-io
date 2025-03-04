<?php
/**
 * Setting of Emails
 *
 * @link       https://xfinitysoft.com
 * @since      1.0.0
 *
 * @package    Caveni_Io
 * @subpackage Caveni_Io/admin/views
 */

?>
<form method="post" action="options.php">
	<?php
	settings_fields( 'caveni_email_settings' );
	do_settings_sections( 'caveni_email_settings' );
	$email_settings = get_option( 'caveni_email_settings' );
	?>
	<h2><?php esc_html_e( 'Admin Mail Settings', 'caveni-io' ); ?></h2>
	<table class="form-table">
		<tbody>
			<tr>
				<th><?php esc_html_e( 'Enable', 'caveni-io' ); ?></th>
				<td>
					<input type="checkbox" name="caveni_email_settings[admin_enable]" <?php echo isset( $email_settings['admin_enable'] ) ? 'checked' : ''; ?>>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Email subject', 'caveni-io' ); ?></th>
				<td>
					<input class="caveni-input" type="text" name="caveni_email_settings[admin_email_subject]" value="<?php echo ( isset( $email_settings['admin_email_subject'] ) ) ? esc_html( $email_settings['admin_email_subject'] ) : ''; ?>" required="required">
					<p class="description">
						<?php esc_html_e( 'Allowed placeholders: {site_title} {user_name}', 'caveni-io' ); ?>
					</p>
				</td>
				</tr>
				<tr>
				<th><?php esc_html_e( 'Email content', 'caveni-io' ); ?></th>
				<td>
					<textarea name="caveni_email_settings[admin_email_content]" rows="5" cols="40" required="required"><?php echo ( isset( $email_settings['admin_email_content'] ) ) ? esc_html( $email_settings['admin_email_content'] ) : ''; ?></textarea>
					<p class="description">
						<?php esc_html_e( 'Allowed placeholders: {site_title} {user_name} {ticket_message}', 'caveni-io' ); ?>
					</p>
				</td>
			</tr>
		</tbody>
	</table>
	<h2><?php esc_html_e( 'User Mail Settings', 'caveni-io' ); ?></h2>
	<table class="form-table">
		<tbody>
			<tr>
				<th><?php esc_html_e( 'Enable', 'caveni-io' ); ?></th>
				<td>
					<input type="checkbox" name="caveni_email_settings[user_enable]" <?php echo isset( $email_settings['user_enable'] ) ? 'checked' : ''; ?>>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Email subject', 'caveni-io' ); ?></th>
				<td>
					<input class="caveni-input" type="text" name="caveni_email_settings[user_email_subject]" value="<?php echo ( isset( $email_settings['user_email_subject'] ) ) ? esc_html( $email_settings['user_email_subject'] ) : ''; ?>" required="required">
					<p class="description">
						<?php esc_html_e( 'Allowed placeholders: {site_title} {user_name}', 'caveni-io' ); ?>
					</p>
				</td>
				</tr>
				<tr>
				<th><?php esc_html_e( 'Email content', 'caveni-io' ); ?></th>
				<td>
					<textarea name="caveni_email_settings[user_email_content]" rows="5" cols="40" required="required"><?php echo ( isset( $email_settings['user_email_content'] ) ) ? esc_html( $email_settings['user_email_content'] ) : ''; ?></textarea>
					<p class="description">
						<?php esc_html_e( 'Allowed placeholders: {site_title} {user_name} {ticket_message}', 'caveni-io' ); ?>
					</p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php submit_button( esc_html__( 'Save Changes', 'caveni-io' ) ); ?>
</form>