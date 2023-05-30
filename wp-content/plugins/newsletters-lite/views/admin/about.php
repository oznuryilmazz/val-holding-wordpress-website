<?php // phpcs:ignoreFile ?>
<?php
/**
 * Newsletters About Dashboard v4.5
 */

/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( ABSPATH . 'wp-admin/admin.php' );

$major_features = array(
	array(
		'src'         => $this -> url() . '/images/about/feature-1.jpg',
		'heading'     => 'Subscribe Forms Builder',
		'description' => 'A new, drag & drop subscribe forms builder makes it easy to construct subscribe forms with the fields and settings you need.',
	),
	array(
		'src'         => $this -> url() . '/images/about/feature-2.jpg',
		'heading'     => 'Videos in Newsletters',
		'description' => 'Put the URL of a video of any popular video service eg. YouTube, Tumblr, Vimeo, etc. and it will be replaced with a static player, linking back to the original video.',
	)
);
shuffle( $major_features );

$minor_features = array(
	array(
		'src'         => $this -> url() . '/images/about/feature-3.jpg',
		'heading'     => 'JSON API',
		'description' => 'The JSON API is a direct bridge and communication channel between any code or application and the Newsletter plugin itself, including it\'s database.',
	),
	array(
		'src'         => $this -> url() . '/images/about/feature-1.jpg',
		'heading'     => 'Bounces Section',
		'description' => 'The bounces section will show you bounces as they happen with a status code, reason, newsletter bounced on and much more.',
	),
	array(
		'src'			=>	$this -> url() . '/images/about/feature-5.jpg',
		'heading'		=>	'Media Files per Newsletter',
		'description'	=>	'Media files are now linked to and separated by each newsletter individually so that you can easily find your files and images when access a newsletter again.'
	)
);

$tech_features = array(
	array(
		'heading'     => __( 'Taxonomy Roadmap' ),
		'description' => __( 'Terms shared across multiple taxonomies are now split into separate terms.' ),
	),
	array(
		'heading'     => __( 'Template Hierarchy' ),
		/* Translators: 1: singular.php; 2: single.php; 3:page.php */
		'description' => sprintf( __( 'Added %1$s as a fallback for %2$s and %3$s' ), '<code>singular.php</code>', '<code>single.php</code>', '<code>page.php</code>.' ),
	),
	array(
		'heading'     => '<code>WP_List_Table</code>',
		'description' => __( 'List tables can and should designate a primary column.' ),
	),
);

?>

<div class="wrap newsletters about-wrap">
	<h1><?php echo sprintf( 'Welcome to Tribulant Newsletters %s', $this -> version); ?></h1>
	<div class="about-text">
		<?php echo sprintf('Thank you for installing! Tribulant Newsletters %s is more powerful, reliable and versatile than before. It includes many features and improvements to make email marketing easier and more efficient for you.', $this -> version); ?>
	</div>
	<div class="newsletters-badge">
		<div>
			<i class="fa fa-envelope fa-fw" style="font-size: 72px !important; color: white;"></i>
		</div>
		<?php echo sprintf('Version %s', $this -> version); ?>
	</div>
	
	<div class="feature-section one-col">
		<div class="col">
			<h2>An Update that will Blow Your Mind!</h2>
			<p class="lead-description">The best newsletter plugin just got better, all for you!</p>
			<p>With the purpose of increasing speed and performance we overhauled many of the classes in the plugin as well as procedures used to create, queue and send emails. Feel it fly!</p>
		</div>
	</div>

	<hr />

	<h2>New Major Features</h2>

	<div class="feature-section two-col has-2-columns">
		<?php foreach ($major_features as $feature) : ?>
			<div class="col column">
				<div class="media-container">
					<?php
					// Video.
					if ( is_array( $feature['src'] ) ) :
						echo wp_video_shortcode( array(
							'mp4'      => $feature['src']['mp4'],
							'ogv'      => $feature['src']['ogv'],
							'webm'     => $feature['src']['webm'],
							'loop'     => true,
							'autoplay' => true,
							'width'    => 500,
							'height'   => 284
						) );
	
					// Image.
					else:
					?>
					<img src="<?php echo esc_url( $feature['src'] ); ?>" />
					<?php endif; ?>
				</div>
				<h3><?php echo esc_html( $feature['heading']); ?></h3>
				<p><?php echo esc_html( $feature['description']); ?></p>
			</div>
		<?php endforeach; ?>
	</div>
	
	<hr/>
	
	<h2>New Minor Features</h2>
	<div class="feature-section three-col has-3-columns">
		<?php foreach ($minor_features as $feature) : ?>
			<div class="col column">
				<div class="minor-img-container">
					<img src="<?php echo esc_attr( $feature['src'] ); ?>" />
				</div>
				<h3><?php echo esc_html( $feature['heading']); ?></h3>
				<p><?php echo esc_html( $feature['description']); ?></p>
			</div>
		<?php endforeach; ?>
	</div>
	
	<hr/>
	
	<div class="changelog">
		<h2>New Extension Plugins</h2>
		<div class="feature-section under-the-hood three-col has-3-columns">
			<div class="col column">
				<h4>Bloom Subscribers</h4>
				<a href="http://tribulant.com/extensions/view/69/bloom-subscribers"><img style="float:left; margin:0 15px 10px 0;" src="<?php echo esc_url_raw( $this -> render_url('images/about/bloom-subscribers.png')); ?>" alt="bloom-subscribers" /></a>
				<p>Capture email/newsletter subscribers through Bloom plugin optin forms directly into your Newsletter plugin with ease.</p>
				<p><a href="http://tribulant.com/extensions/view/69/bloom-subscribers" class="button button button-primary">Bloom Subscribers</a></p>
			</div>
			<div class="col column">
				<h4>Events Manager Subscribers</h4>
				<a href="http://tribulant.com/extensions/view/68/events-manager-subscribers"><img style="float:left; margin:0 15px 10px 0;" src="<?php echo esc_url_raw( $this -> render_url('images/about/events-manager-subscribers.png')); ?>" alt="events-manager-subscribers" /></a>
				<p>As your users register/book for events in your Events Manager plugin, you can subscribe them to your newsletters accordingly.</p>
				<p><a href="http://tribulant.com/extensions/view/68/events-manager-subscribers" class="button button button-primary">Events Manager Subscribers</a></p>
			</div>
			<div class="col column">
				<h4>Profile Builder Subscribers</h4>
				<a href="http://tribulant.com/extensions/view/66/profile-builder-subscribers"><img style="float:left; margin:0 15px 10px 0;" src="<?php echo esc_url_raw( $this -> render_url('images/about/profile-builder-subscribers.png')); ?>" alt="profile-builder-subscribers" /></a>
				<p>Create a subscribe checkbox custom field on your Profile Builder plugin registration form for users to subscribe to your newsletters as they register their profile.</p>
				<p><a href="http://tribulant.com/extensions/view/66/profile-builder-subscribers" class="button button button-primary">Profile Builder Subscribers</a></p>
			</div>
		</div>
	</div>
	
	<hr/>
	
	<div class="changelog">
		<h2>New Newsletter Templates</h2>
		<div class="feature-section under-the-hood three-col has-3-columns">
			<div class="col column">
				<h4>Magazine</h4>
				<a href="http://tribulant.com/emailthemes/view/3/magazine-newsletter-template"><img style="float:left; margin:0 15px 10px 0;" src="<?php echo esc_url_raw( $this -> render_url('images/about/news-theme-magazine.jpg')); ?>" alt="magazine" /></a>
				<p>The ideal newsletter template for content rich websites. Display content in multiple content areas with a sidebar as well. Fully responsive with media queries and fluid design.</p>
				<p><a href="http://tribulant.com/emailthemes/view/3/magazine-newsletter-template" class="button button button-primary">Magazine</a></p>
			</div>
			<div class="col column">
				<h4>Simple Business</h4>
				<a href="http://tribulant.com/emailthemes/view/1/simple-business-newsletter-template"><img style="float:left; margin:0 15px 10px 0;" src="<?php echo esc_url_raw( $this -> render_url('images/about/news-theme-simple-business.jpg')); ?>" alt="simple-business" /></a>
				<p>The perfect newsletter theme for your business, whether you want to promote your products and pages or simply update your clients. Fully responsive, fluid and media query versions available.</p>
				<p><a href="http://tribulant.com/emailthemes/view/1/simple-business-newsletter-template" class="button button button-primary">Simple Business</a></p>
			</div>
			<div class="col column">
				<h4>Easy Shop</h4>
				<a href="http://tribulant.com/emailthemes/view/2/easy-shop-newsletter-template"><img style="float:left; margin:0 15px 10px 0;" src="<?php echo esc_url_raw( $this -> render_url('images/about/news-theme-easy-shop.jpg')); ?>" alt="easy-shop" /></a>
				<p>Market and showcase your products in a beautifully elegant eCommerce newsletter template. This professional email theme is fully responsive and created for shop owners.</p>
				<p><a href="http://tribulant.com/emailthemes/view/2/easy-shop-newsletter-template" class="button button button-primary">Easy Shop</a></p>
			</div>
		</div>
		
		<div class="return-to-dashboard">
			<a class="button button-primary button-hero" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> welcome)) ?>">Go to Newsletters Overview <i class="fa fa-arrow-right"></i></a>
		</div>
	</div>
</div>