<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'oznuryilmaz_val' );

/** Database username */
define( 'DB_USER', 'oznuryilmaz_val' );

/** Database password */
define( 'DB_PASSWORD', 'KukES&-6r()Q' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'm,fmJg]M 10C$JN7/$r%R ~epNwfq#U^q84<eMaEk|6&+mo+p[{RpWFOFfdJXxJR' );
define( 'SECURE_AUTH_KEY',  'P)vzNS.s|Gv,:6<w##h)i.gG7~_a_Y1J}MjrTEIV=6*7IAhQ!n:XZ><umu rXAZa' );
define( 'LOGGED_IN_KEY',    ')0acL6 Wk<osGvDe}p7%T+,)_U+kN1plm+3CjPQ]F*Wj0)RI[R]i-yo4wuaS{&]%' );
define( 'NONCE_KEY',        'g=!kJzsaJN>c#qbJ,Sr0?_x~A/_Tz i-x|HMC[^*N5}CVR6Y+{XgjHmf<<pwf#~P' );
define( 'AUTH_SALT',        'LT!C*!R!?exMq_!-*~~|UC8Vdol{ayQ##Q|68kHg!jLRz8 &]pOw9ztwnuD=Ln$`' );
define( 'SECURE_AUTH_SALT', 'L`@ </DhBN,U?kH]UBEQw0@fVWkQP4(Nnzw{sPV%wj[azST0m__PDG|-@15!CSKZ' );
define( 'LOGGED_IN_SALT',   'YLKUQ|,ud,]unkSsyk!H>7FRu}!W9Q;;ok5YRFIAYdrBNEEr]D]oPYi?o[2onGmq' );
define( 'NONCE_SALT',       '>ClzC0}$<Gmej3g/IA[]?(OdQ0aZ1TB7{PrtzgcA9yH6s{Vc%VqwL(-bO0A^?y$)' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
