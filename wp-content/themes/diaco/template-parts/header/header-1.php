<?php
global $diaco_options;
$header_social_link = isset( $diaco_options['diaco_header_social_link'] ) ? $diaco_options['diaco_header_social_link'] : '';
?>
	<!-- Main Header -->
	<header class="main-header header-style-one">
		<div class="outer-container">
			<div class="outer-box clearfix">
				<div class="pull-left logo-box">
					<figure class="logo">
					<?php
					if ( function_exists( 'get_custom_logo' ) && has_custom_logo() ) {
						$output = get_custom_logo();
						if ( empty( $output ) ) {
							?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
								<img src="<?php echo esc_url( DIACO_IMG_URL . 'logo.svg' ); ?>" alt="<?php esc_attr_e( 'Logo', 'diaco' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
							</a>
							<?php
						} else {
							$custom_logo_id = get_theme_mod( 'custom_logo' );
							$image          = wp_get_attachment_image_src( $custom_logo_id, 'full' );
							?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
								<img src="<?php echo esc_url( $image[0] ); ?>" alt="<?php esc_attr_e( 'Logo', 'diaco' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
							</a>
							<?php
						}
					} else {
						?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
							<img src="<?php echo esc_url( DIACO_IMG_URL . 'logo.svg' ); ?>" alt="<?php esc_attr_e( 'Logo', 'diaco' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
						</a>
						<?php
					}
					?>
					</figure>
				</div>
				<div class="pull-right nav-toggler">
					<button class="nav-btn">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>
			</div>
		</div>

		<?php get_template_part( 'template-parts/header/sticky-header' ); ?>
	</header>
	<!-- End Main Header -->


	<!--Form Back Drop-->
	<div class="form-back-drop"></div>


	<!-- Hidden Bar -->
	<section class="hidden-bar">
		<div class="inner-box">
			<div class="cross-icon"><span class="fa fa-times"></span></div>
			<!-- logo -->
			<div class="logo-box centred">
			<?php
			if ( function_exists( 'get_custom_logo' ) && has_custom_logo() ) {
				$output = get_custom_logo();
				if ( empty( $output ) ) {
					?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
								<img src="<?php echo esc_url( DIACO_IMG_URL . 'logo.svg' ); ?>" alt="<?php esc_attr_e( 'Logo', 'diaco' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
							</a>
							<?php
				} else {
					$custom_logo_id = get_theme_mod( 'custom_logo' );
					$image          = wp_get_attachment_image_src( $custom_logo_id, 'full' );
					?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
								<img src="<?php echo esc_url( $image[0] ); ?>" alt="<?php esc_attr_e( 'Logo', 'diaco' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
							</a>
							<?php
				}
			} else {
				?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
							<img src="<?php echo esc_url( DIACO_IMG_URL . 'logo.svg' ); ?>" alt="<?php esc_attr_e( 'Logo', 'diaco' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
						</a>
						<?php
			}
			?>
			</div>

			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location'  => 'primary',
						'depth'           => 3, // 1 = no dropdowns, 2 = with dropdowns.
						'container'       => 'div',
						'container_class' => 'side-menu',
						'container_id'    => '',
						'menu_class'      => 'clearfix gfvxfg',
						'fallback_cb'     => 'Diaco_Bootstrap_Navwalker::fallback',
						'walker'          => new Diaco_Bootstrap_Navwalker(),
					)
				);
			} else {
				wp_nav_menu(
					array(
						'depth'           => 3, // 1 = no dropdowns, 2 = with dropdowns.
						'container'       => 'div',
						'container_class' => 'side-menu',
						'container_id'    => '',
						'menu_class'      => 'clearfix',
						'fallback_cb'     => 'Diaco_Bootstrap_Navwalker::fallback',
						'walker'          => new Diaco_Bootstrap_Navwalker(),
					)
				);
			}
			?>
 
		<?php echo wp_kses_post( $header_social_link ); ?>
		</div>
	</section>
	<!--End Hidden Bar -->

   <?php
	get_template_part( 'template-parts/header/slider' );
