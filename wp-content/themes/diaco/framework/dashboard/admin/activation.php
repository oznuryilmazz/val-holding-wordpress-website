<?php
$license = get_option( 'envato_theme_license_key' );

$envato_clientemail = get_option( 'envato_clientemail' );
$status             = get_option( 'envato_theme_license_key_status' );
$required           = 'required';
if ( $status !== false && $status == 'valid' ) {
	$required = '';
}

$apikeys = get_option( 'envato_theme_license_token' );
$apikeys = json_decode( stripslashes( $apikeys ), true );
if ( $apikeys == '' ) {
	$license = false;
}
?>
<div id="activetion" class="gt-tab-pane gt-is-active">
	<div class="feature-section two-col">
		<h2><?php esc_attr_e( 'Theme License Options', 'diaco' ); ?></h2>

		<div class="activation_massage">
			<p><?php echo esc_html__( 'First enter your license key in the below field  and click the button "Save Changes". After saving "Activate License" Button will be visible.', 'diaco' ); ?></p>
			<p><?php echo esc_html__( 'Then click "Activate License" button for active theme.', 'diaco' ); ?>
		</div>
		<div class="ajax_massage">
			<p></p>
		</div>
		<form method="post" action="options.php" id="license_product_registration">
			<?php wp_nonce_field( 'envato_theme_nonce', 'envato_theme_nonce' ); ?>
			<?php settings_fields( 'envato_theme_license' ); ?>
			<table class="form-table">
				<tbody>
					<?php
					$checkboxaction = '';

					if ( ! $license ) {
						?>
						<tr valign="top" class="envato-liccence-button-tr">
							<th scope="row" valign="top">
								<?php esc_html_e( 'Activate License', 'diaco' ); ?>
							</th>
							<td>
								<div class="envato_theme_theme_license_activate_envato_div">
									<input type="button" data-url="<?php echo esc_attr( $urlapihit ); ?>" class="envato_theme_theme_license_activate_envato" name="envato_theme_theme_license_activate" value="<?php esc_attr_e( 'Activate License Envato', 'diaco' ); ?>" />
								</div>
								<p><?php esc_html_e('If your theme license is activated another domain then you can remove license via below Manage Your License','diaco');?></p>
								<a class="button button-large button-primary avada-large-button envato_theme_theme_license_manage" href="https://my.smartdatasoft.com/" target="_blank"><?php esc_html_e( 'Manage Your License', 'diaco' ); ?></a>
							</td>
						</tr>
					<?php } else { ?>
						<tr valign="top" class="envato-liccence-button-tr">
							<th scope="row" valign="top">
								<?php esc_attr_e( 'Deactivate License', 'diaco' ); ?>
							</th>
							<td>
								<div class="envato_theme_theme_license_deactivate_envato_div">
									<input type="button" data-url="<?php echo esc_attr( $urlapideactive ); ?>" class="envato_theme_theme_license_deactivate_envato" name="envato_theme_theme_license_deactivate" value="<?php esc_attr_e( 'Deactivate License Envato', 'diaco' ); ?>" />
								</div>
								<p><?php esc_html_e('If your theme license is activated another domain then you can remove license via below Manage Your License','diaco');?></p>
								<a class="button button-large button-primary avada-large-button envato_theme_theme_license_manage" href="https://my.smartdatasoft.com/" target="_blank"><?php esc_html_e( 'Manage Your License', 'diaco' ); ?></a>
							</td>
						</tr>

					<?php } ?>
				</tbody>
			</table>

		</form>
	</div>
</div>
