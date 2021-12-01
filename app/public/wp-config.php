<?php
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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'w5nwHIoK7VVVDLWAgCwG6vBrTmR8D368c8Nz4eHAg6U2JAGZgHQ0nfOXdUQN3eE2B8Zz2/9A7856evLJ7jor3g==');
define('SECURE_AUTH_KEY',  'Iuy5d5v4JQQ0Zm3Ak+GvmoB+qgFpJ/MecvzXG0Zd/eNpKv7tfKf6nVGu/yIaoaMYGM3bsOz684f5CmHRAFUzqQ==');
define('LOGGED_IN_KEY',    'Pv9VX7YJ6VNSGK83e+i7ZPnibmG0ax6gUGcGQibFlY2ss2Hwqb5jjeTyvoyLMG0WZhBk5s4FPkdwz3yj1y1znA==');
define('NONCE_KEY',        'TWz1npxq84xt7Y4sJxwYYYNB0YWXCqjp+E6T1CIy7R1EMxSTGWhM9PWZGSfTDHED//opC1sMA2HMh5BU+4bjMw==');
define('AUTH_SALT',        'tPl7Do1Bx9h9Z4F+TYHj+6nvl83s1njMln47EUDh6Z4+BLRfBoNWGsmOZpIDXYdqFASyedW0hOjNeBb89LYaCg==');
define('SECURE_AUTH_SALT', 'MySFCZCIUoQ83sCqaQKy7f/Bs5nLOmVErbNlCH03sPpufvOjnZkyz85M3VojtC7ELGFT9ntFJZgElgKStB7JnA==');
define('LOGGED_IN_SALT',   'uD5eMtQKzJ1KyxgY35sQ+1lO0OHPaCwPFdSHLI1/6WZQZahnYAUgUHGtH/N5Tk4oKXUAdvYXX6rYxykpRWR0ng==');
define('NONCE_SALT',       'rIlK/Ex4u5fRG0IX6nITKoemkSibj5wqB7sMo20lIpcBpBOqiYK/dy2KrpaVRTnav6lw8JSXpTuW7SN3f6I+OQ==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
