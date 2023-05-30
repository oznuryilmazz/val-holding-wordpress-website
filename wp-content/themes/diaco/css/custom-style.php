<?php
/*
 * print css with cheking value is empty or not
 *
 */
function diaco_print_css( $props = '', $values = array(), $vkey = '', $pre_fix = '', $post_fix = '' ) {
	if ( isset( $values[ $vkey ] ) && ! empty( $values[ $vkey ] ) ) {
		print wp_kses_post( $props . ':' . $pre_fix . $values[ $vkey ] . $post_fix . ";\n" );
	}
}

function diaco_color_brightness( $colourstr, $steps, $darken = false ) {
	$colourstr = str_replace( '#', '', $colourstr );
	$rhex      = substr( $colourstr, 0, 2 );
	$ghex      = substr( $colourstr, 2, 2 );
	$bhex      = substr( $colourstr, 4, 2 );

	$r = hexdec( $rhex );
	$g = hexdec( $ghex );
	$b = hexdec( $bhex );

	if ( $darken ) {
		$steps = $steps * -1;
	}

	$r = max( 0, min( 255, $r + $steps ) );
	$g = max( 0, min( 255, $g + $steps ) );
	$b = max( 0, min( 255, $b + $steps ) );

	$hex  = '#';
	$hex .= str_pad( dechex( $r ), 2, '0', STR_PAD_LEFT );
	$hex .= str_pad( dechex( $g ), 2, '0', STR_PAD_LEFT );
	$hex .= str_pad( dechex( $b ), 2, '0', STR_PAD_LEFT );

	return $hex;
}

function diaco_get_custom_styles() {
	global $diaco_options;
	$redix_opt_prefix = 'diaco';

	$diaco_main_color = ' <?php echo esc_html($diaco_main_color);?>';

	if ( ( isset( $diaco_options[ $redix_opt_prefix . '_main_color' ] ) ) && ( ! empty( $diaco_options[ $redix_opt_prefix . '_main_color' ] ) ) ) {

		$diaco_main_color = $diaco_options[ $redix_opt_prefix . '_main_color' ];
	}

		ob_start();
	if ( ( isset( $diaco_options[ $redix_opt_prefix . '_main_color' ] ) ) && ( ! empty( $diaco_options[ $redix_opt_prefix . '_main_color' ] ) ) ) {

		?>


	/* Colore-File */

.main-header .link-nav li a {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
   
}
a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.scroll-top {
	background: <?php echo esc_html( $diaco_main_color ); ?>;
}
.header-style-one .nav-toggler .nav-btn {
	background: <?php echo esc_html( $diaco_main_color ); ?>;
}

.sec-title .top-title {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.main-menu .navigation>li.current>a,
.main-menu .navigation>li:hover>a {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.main-menu .navigation>li>ul {
	border-top: 3px solid <?php echo esc_html( $diaco_main_color ); ?>;
}

.main-menu .navigation>li>ul>li:hover {
	background: <?php echo esc_html( $diaco_main_color ); ?>;
	border-bottom: 1px solid <?php echo esc_html( $diaco_main_color ); ?>;
}

.main-menu .navigation>li>ul>li>ul {
	border-top: 3px solid <?php echo esc_html( $diaco_main_color ); ?>;
}

.main-menu .navigation>li>ul>li>ul>li:hover>a {
	background: <?php echo esc_html( $diaco_main_color ); ?>;
}

.sticky-header .main-menu .navigation>li.current>a,
.sticky-header .main-menu .navigation>li:hover>a {
	background: <?php echo esc_html( $diaco_main_color ); ?>;
}

.hidden-bar .social-links li a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.main-slider h1 span {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.tp-leftarrow.tparrows.metis:hover:before,
.nav-style-one .owl-nav .owl-prev:hover:before {
	border-color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.tp-leftarrow.tparrows.metis:after,
.nav-style-one .owl-nav .owl-prev:after {
	background: <?php echo esc_html( $diaco_main_color ); ?>;
}

.tp-rightarrow.tparrows.metis:hover:before,
.nav-style-one .owl-nav .owl-next:hover:before {
	border-color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.tp-rightarrow.tparrows.metis:after,
.nav-style-one .owl-nav .owl-next:after {
	background: <?php echo esc_html( $diaco_main_color ); ?>;
}

.scroll-btn-flip:after {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.mouse-btn-down:hover .scroll-arrow:before {
	border-right: 2px solid <?php echo esc_html( $diaco_main_color ); ?>;
	border-bottom: 2px solid <?php echo esc_html( $diaco_main_color ); ?>;
}

.scroll-arrow:after {
	background: <?php echo esc_html( $diaco_main_color ); ?>;
}

.about-section .count-text:before {
	background: <?php echo esc_html( $diaco_main_color ); ?>;
}

.about-section .content-box a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.service-block-one .inner-box .caption-box a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.service-block-one .inner-box .overlay-box h4 a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.video-section .video-gallery .text {
	background: <?php echo esc_html( $diaco_main_color ); ?>;
}

.work-section .product-tab-btns li.active-btn,
.work-section .product-tab-btns li:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.work-section .p-tabs-content .single-item .tab-content .lower-content h2 a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.project-block-one .inner-box .icon-box a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.project-section .load-btns a {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.team-section .line-overlay .line:after,
.team-section .line-overlay .line:before {
	background: <?php echo esc_html( $diaco_main_color ); ?>;
}

.team-block-one:hover .image-box {
	border: 1px solid <?php echo esc_html( $diaco_main_color ); ?>;
}

.team-block-one .lower-content h3 a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.news-block-one .lower-content h4.post-title a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.main-footer .logo-widget .social-links li a {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.main-footer .logo-widget .social-links li a:hover {
	background: <?php echo esc_html( $diaco_main_color ); ?>;
}

.main-footer .post-widget .post a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.main-footer .gallery-widget .widget-content .list li {
	background: <?php echo esc_html( $diaco_main_color ); ?>;
}

.main-footer .contact-widget .list li a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.main-footer .contact-widget .list li span {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.main-footer .footer-bottom .copyright a {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}


/*** 

====================================================================
						Home-Page-Two
====================================================================

***/


/** slider-style-two **/


.about-style-two .content-box a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.service-style-two .inner-content .link a:hover {
	border: 1px solid <?php echo esc_html( $diaco_main_color ); ?>;
	background: <?php echo esc_html( $diaco_main_color ); ?>;
}

.work-block-one h4 a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.project-block-two .content-box h2 a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.project-style-two .view-btn a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.video-style-two .inner-content .top-text {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.video-style-two .inner-content .video-link:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.recent-project .load-more a {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.call-to-action .inner-content .appointment-form .form-group button:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.call-to-action .inner-content .appointment-form .form-group input:focus,
.call-to-action .inner-content .appointment-form .form-group textarea:focus {
	border: 1px solid <?php echo esc_html( $diaco_main_color ); ?>;
}

.service-block-two:hover .icon-box {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.service-block-two h5 a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

/*** 

====================================================================
						Home-Page-Five
====================================================================

***/

.project-style-two.black-bg .view-btn a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}


/*** 

====================================================================
						Home-Page-Five
====================================================================

***/


/** slider-style-five **/

.project-block-one .lower-content h4 a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.pricing-table:hover {
	border-top: 5px solid <?php echo esc_html( $diaco_main_color ); ?>;
}

.pricing-table .table-header .price {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.pricing-table .table-content li:before {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.pricing-table .table-content li:before {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.pricing-column:last-child .pricing-table .table-content li:nth-child(5):before,
.pricing-column:last-child .pricing-table .table-content li:last-child:before {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.pricing-table .table-footer a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.project-page-07 .lower-content .load-btn a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.project-page-10 .load-more a {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.single-project .info-content li a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.single-progress-box .progress-bar {
	background-color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.about-style-two.single-team-page .content-box .info li {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}


/** education-section **/


.education-section .inner-content .single-item .content-box span {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}



/*** 

====================================================================
						Faq-Page
====================================================================

***/


.coming-soon .inner-content .text a {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.error-section .content-box p a {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.error-section input:focus+button,
.error-section button:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.error-section input:focus {
	border: 1px solid <?php echo esc_html( $diaco_main_color ); ?>;
}


/*** 

====================================================================
						Blog-Page
====================================================================

***/


.sidebar-page-container .news-block-one .lower-content .link a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.sidebar-search .widget-content .form-group input:focus+button,
.sidebar-search .widget-content button:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.sidebar-search .widget-content .form-group input:focus {
	border: 1px solid <?php echo esc_html( $diaco_main_color ); ?>;
}

.sidebar-page-container .sidebar .sidebar-categories .list li a:hover,
.sidebar-page-container .sidebar .widget ul li a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.sidebar-page-container .sidebar .sidebar-post .post .thumb {
	background: <?php echo esc_html( $diaco_main_color ); ?>;
}


/*** 

====================================================================
						Cooming-Soon-Page
====================================================================

***/


.sidebar-page-container .sidebar .sidebar-post .post h5 a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.sidebar-page-container .widget .social-links li a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.blog-single .blog-single-content .post-share-option .social-links li a:hover {
	background: <?php echo esc_html( $diaco_main_color ); ?> !important;
}

.blog-single .blog-single-content .post-share-option .social-share .share a:hover {
	background: <?php echo esc_html( $diaco_main_color ); ?>;
}

.blog-single .blog-single-content .post-controls .inner .nav-previous a:hover,
.blog-single .blog-single-content .post-controls .inner .nav-next a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.blog-single .blog-single-content .post-controls .scroll-btn:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.blog-single .blog-single-content .post-controls .scroll-btn i {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.blog-single .blog-single-content .author-box .author-inner .author-content .info-box a {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.comments-form-area .form-group input:focus,
.comments-area .form-group input:focus,
.comments-form-area .form-group textarea:focus,
.comments-area .form-group textarea:focus {
	border: 1px solid <?php echo esc_html( $diaco_main_color ); ?>;
}

.logged-in-as a {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.comments-form-area .comment-form .message-btn button:hover {
	background: <?php echo esc_html( $diaco_main_color ); ?>;
}

.contact-info-section .info-box .single-info:hover .icon-box {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.contact-info-section .info-box .single-info a:hover {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.contact-form-area .form-group input:focus,
.contact-form-area .form-group textarea:focus {
	border: 1px solid <?php echo esc_html( $diaco_main_color ); ?>;
}

.contact-form-area .form-group button {
	background: <?php echo esc_html( $diaco_main_color ); ?>;
}

.comments-area .comment-box .comment-reply-link {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.main-footer .gallery-columns-3 .gallery-item .gallery-icon {
	background: <?php echo esc_html( $diaco_main_color ); ?>;
}

.page-links a:hover,
.page-links .current,
.page-links a.active,
.pagination .nav-links a:hover,
.pagination .nav-links .current,
.pagination .nav-links a.active {
	border-color: <?php echo esc_html( $diaco_main_color ); ?>;
	background-color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.blog-single .blog-single-content .post-share-option .tags li a:hover,
.tagcloud a:hover {
	background-color: <?php echo esc_html( $diaco_main_color ); ?>;
}

select {
	border: 1px solid <?php echo esc_html( $diaco_main_color ); ?>;
}

a {
	color: <?php echo esc_html( $diaco_main_color ); ?>;
}

.post-password-form input {
	border: 1px solid <?php echo esc_html( $diaco_main_color ); ?>;
}



		<?php
	}

		$diaco_custom_css = ob_get_clean();

		return $diaco_custom_css;

}
