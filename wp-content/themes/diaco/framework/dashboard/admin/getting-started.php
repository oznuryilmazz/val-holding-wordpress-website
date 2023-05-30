<?php
/**
 * Getting started section.
 *
 * @package Car Repair Services
 */

?>
<div id="getting-started" class="gt-tab-pane gt-is-active">
	<div class="feature-section two-col">
		<div class="col">
			<h3><?php esc_html_e( 'Implement Recommended Actions', 'diaco' ); ?></h3>
			<p>
				<?php
				echo sprintf( __( "Thank you so much for your purchase of SmartDataSoft's %1\$s theme! %2\$s theme is designed for you with different layouts, one-click demo installation, wonderful and unique designs, amazing content blocks, get the price section, SEO friendly, Sticky menu, WooCommerce and lots more. Enjoy!", 'diaco' ), $this->dashboard_Name, $this->dashboard_Name );
				?>
			</p>
			<p>
				<?php echo esc_html_e( 'If you’d like to get support from our team please open up a support ticket at first. Our support engineers are always ready to get back to you ASAP.', 'diaco' ); ?>
			</p>
		</div>
		<div class="col">
			<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/screenshot.png" alt="<?php esc_attr_e( 'screenshot', 'diaco' ); ?>">
		</div>
	</div>
</div>
