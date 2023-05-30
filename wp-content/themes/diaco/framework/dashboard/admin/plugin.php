<?php
/**
 * Getting started section.
 *
 * @package Car Repair Services
 */
if ( ! class_exists( 'TGM_Plugin_Activation' ) ) {
	return false;
}

$plugin_table      = new TGMPA_List_Table();
$plugin_activation = new TGM_Plugin_Activation();
			// Return early if processing a plugin installation action.
if ( ( ( 'tgmpa-bulk-install' === $plugin_table->current_action() || 'tgmpa-bulk-update' === $plugin_table->current_action() ) && $plugin_table->process_bulk_actions() ) ) {
	return;
}

			// Force refresh of available plugin information so we'll know about manual updates/deletes.
			wp_clean_plugins_cache( false );

?>
			<div class="tgmpa">
				<?php
				if ( ! empty( $plugin_activation->message ) && is_string( $plugin_activation->message ) ) {
					echo wp_kses_post( $plugin_activation->message );
				}
				?>
		<h1><?php esc_html_e( 'Recommended Plugins', 'diaco' ); ?></h1>
		<?php $plugin_table->prepare_items(); ?>
				<?php $plugin_table->views(); ?>

				<form id="tgmpa-plugins" action="" method="post">
					<input type="hidden" name="tgmpa-page" value="<?php echo esc_attr( $plugin_activation->menu ); ?>" />
					<input type="hidden" name="plugin_status" value="<?php echo esc_attr( $plugin_table->view_context ); ?>" />
					<?php $plugin_table->display(); ?>
				</form>
			</div>
