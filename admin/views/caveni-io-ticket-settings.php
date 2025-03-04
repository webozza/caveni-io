<?php
/**
 * Setting of ticket module
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
	settings_fields( 'caveni_ticket_settings' );
	do_settings_sections( 'caveni_ticket_settings' );
	$ticket_settings = get_option( 'caveni_ticket_settings' );
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th><?php esc_html_e( 'Enable', 'caveni-io' ); ?></th>
				<td>
					<input type="checkbox" name="caveni_ticket_settings[enable]" <?php echo isset( $ticket_settings['enable'] ) ? 'checked' : ''; ?>>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'User Role', 'caveni-io' ); ?></th>
				<td>
					<?php global $wp_roles; ?>
					<select class='cavenio-roles' name="caveni_ticket_settings[role][]" multiple>
						<?php
						if ( is_array( $wp_roles->roles ) && ! empty( $wp_roles->roles ) ) {
							foreach ( $wp_roles->roles  as $key => $name ) {
								if ( 'administrator' === $key ) {
									continue;
								}
								?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php echo ( isset( $ticket_settings['role'] ) && in_array( $key, $ticket_settings['role'], true ) ) ? 'selected' : ''; ?> ><?php echo esc_html( $name['name'] ); ?></option>
								<?php
							}
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'File size', 'caveni-io' ); ?></th>
				<td>
					<input class="caveni-input" type="number" name="caveni_ticket_settings[file_size]" value="<?php echo isset( $ticket_settings['file_size'] ) ? esc_html( $ticket_settings['file_size'] ) : ''; ?>">
					<span><?php esc_html_e( 'KB', 'caveni-io' ); ?></span>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'File type', 'caveni-io' ); ?></th>
				<td>
					<textarea cols="40" rows="5" name="caveni_ticket_settings[file_types]"> <?php echo isset( $ticket_settings['file_types'] ) ? esc_html( $ticket_settings['file_types'] ) : ''; ?></textarea>
					<p class="description"><?php esc_html_e( 'Sets the file extensions allowed to upload. Seperate each extension by a comma. For example: .jpg, .png, .pdf', 'caveni-io' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php submit_button( esc_html__( 'Save Changes', 'caveni-io' ) ); ?>
</form>