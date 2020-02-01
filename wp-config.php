<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'fingmkvv_wp500' );

/** MySQL database username */
define( 'DB_USER', 'fingmkvv_wp500' );

/** MySQL database password */
define( 'DB_PASSWORD', 'S7SsR5@2!p' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'mzbjyljfslmy5ihabwg3cll5dg78v7m5jiusccauwmuky0eczs8poxsurocvktfy' );
define( 'SECURE_AUTH_KEY',  'bhu58y7chhnv7cf2yheaqxyopkbl72wpefpjv4trlp54b9gtrlgaj0cws7oav0vt' );
define( 'LOGGED_IN_KEY',    'cidwomme38rjzjvto3yugjwnebpdyh3n3anuzlkwht9ece9mgmvrmnbexcu3ajpg' );
define( 'NONCE_KEY',        'k6ydhitp3h6lh7k75nmeol68zswn0nwpsfzcqub2xg12wysckatal7ijng8r7gdg' );
define( 'AUTH_SALT',        '2anh9zv4zrxsiqn5dufvdbeskrrhnlbtdrqxovafsew3qi46k77ec7r4qvftov2x' );
define( 'SECURE_AUTH_SALT', 'lyndpiepr2iuwtxg6tjpsghd1lqlu2ljx6u3eoj39a6pww8tpuuq0kl5rqp8t5iz' );
define( 'LOGGED_IN_SALT',   'tahsptvbeuuu2lrrfzdv8qfs7tav4iurxiek0xuezstmf2q0dwedetqlyutjtjyd' );
define( 'NONCE_SALT',       'lmhgkmjibmlpqqhppmpe9r8uclajbm1qwqdzbdgepyr6rwurqzvmafdhbwqducol' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpqe_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
