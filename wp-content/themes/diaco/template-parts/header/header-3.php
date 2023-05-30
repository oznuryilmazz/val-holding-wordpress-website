<?php get_template_part( 'searchform-top' ); ?> 
<!-- Main Header -->
<header class="main-header header-style-three">
	<!-- header-upper -->
	<div class="header-upper">
		<div class="container">
			<div class="inner-container clearfix">
				<div class="logo-box pull-left">
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
				<div class="nav-outer clearfix">
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
										'container_class' => 'navbar-collapse collapse clearfix',
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
										'container_class' => 'navbar-collapse collapse clearfix',
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
					<div class="outer-box">
						<div class="search-outer">
							<div class="header-flyout-searchbar">
								<i class="fa fa-search"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div><!-- header-upper end -->

	<?php get_template_part( 'template-parts/header/sticky-header' ); ?>
</header>
<!-- End Main Header -->
<?php get_template_part( 'template-parts/header/slider' ); ?>
