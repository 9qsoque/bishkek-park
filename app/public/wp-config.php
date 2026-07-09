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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          '7is[xY3`/z{qY#H;5|^%Sr7r#Hdv%HZcW^yf&Py*r8*]W1b{OO*G@S 99vi$GB`C' );
define( 'SECURE_AUTH_KEY',   ']y%!J3=H8tS=#K8>,=P,%V8X.3OepX@h+.MFk-7isgAVE:@SJCu.oiC&R<pF-yo8' );
define( 'LOGGED_IN_KEY',     'B4!BB6FE7e {(nug-]4rm;5~yJTIRVL6.uN33xr3O@lWc4/^NY4d=?E=vektx&[:' );
define( 'NONCE_KEY',         'n}heEGY7pac7J)G+Mh3]@CNq[|E|p%?J0-7ia|~Sy;wy32& +yvRT{]LXZ{Q$Kty' );
define( 'AUTH_SALT',         '@+8KzYhS2Y4Mj2JG@Ed$JV3Hdq%T^ooYHWzA/J$,7/v:jC3uP():]Nbf(MzjL+u3' );
define( 'SECURE_AUTH_SALT',  'e)9WT5t_^^bXa|>|N,Y&P9f_A?/#~uxEG@79UI$7$PkijAg4|a&hXdIVPL@DmP6-' );
define( 'LOGGED_IN_SALT',    'hR(Viw74IlNtTE_dHdtt10w@gthi 8$3gR9p*]9l_h)n)!,6}J00%|Gt*.t`{u+>' );
define( 'NONCE_SALT',        '3E{-@#3N|gOORBh `>fODyd$&E}%oz=fp[ep9;o4Rv/TDyJXPC+pPWVPev:G:17z' );
define( 'WP_CACHE_KEY_SALT', '9mz5,32C{DCxYm^9vEs`:n;_Kpy`#6-a+.!8Lw(tJ3nFRX?j:P}Lo|]A]~Q>YTW`' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
