<?php
global $diaco_options;
$header_sticky_logo = isset( $diaco_options['diaco_header_sticky_logo']['url'] ) ? $diaco_options['diaco_header_sticky_logo']['url'] : '';
?>
	   <!--Sticky Header-->
		<div class="sticky-header">
			<div class="container clearfix">
				<figure class="logo-box">
					<?php if ( isset( $header_sticky_logo ) && ! empty( $header_sticky_logo ) ) { ?>
						<a href="<?php echo esc_url( home_url() ); ?>"><img src="<?php echo esc_url( $header_sticky_logo ); ?>" alt="<?php esc_attr_e( 'logo', 'diaco' ); ?>"></a>
				  <?php } else { ?>
					<a href="<?php echo esc_url( home_url() ); ?>"><img src="<?php echo esc_url( DIACO_IMG_URL . 'logo.svg' ); ?>" alt="<?php esc_attr_e( 'logo', 'diaco' ); ?>"></a>
				<?php } ?>
				</figure>
				<div class="menu-area">
					<nav class="main-menu navbar-expand-lg">
						<div class="navbar-header">
							<!-- Toggle Button -->      
							<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							</button>
						</div>
						<?php
						if ( has_nav_menu( 'primary' ) ) {
							wp_nav_menu(
								array(
									'theme_location'  => 'primary',
									'depth'           => 3, // 1 = no dropdowns, 2 = with dropdowns.
									'container'       => 'div',
									'container_class' => 'navbar-collapse collapse clearfix"',
									'container_id'    => '',
									'menu_class'      => 'navigation clearfix',
									'fallback_cb'     => 'Diaco_Bootstrap_Navwalker::fallback',
									'walker'          => new Diaco_Bootstrap_Navwalker(),
								)
							);
						} else {
							wp_nav_menu(
								array(
									'depth'           => 3, // 1 = no dropdowns, 2 = with dropdowns.
									'container'       => 'div',
									'container_class' => 'navbar-collapse collapse clearfix"',
									'container_id'    => '',
									'menu_class'      => 'navigation clearfix',
									'fallback_cb'     => 'Diaco_Bootstrap_Navwalker::fallback',
									'walker'          => new Diaco_Bootstrap_Navwalker(),
								)
							);
						}
						?>
					</nav>
				</div>
			</div>
		</div><!-- sticky-header end -->
