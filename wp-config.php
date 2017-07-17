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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'ninjas');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'l+lL)PE^/mRV.T-Yy#6F;F<>j>s%-[u71j]_Jln!I!_I<07<lEy^vk>ah]H:wd@v');
define('SECURE_AUTH_KEY',  'hU^ipLs,v[ep0#o1T90I @3+gMrdW2dfgU>s8H0*2=^G=2{JnrC97AO,i9$*$(*g');
define('LOGGED_IN_KEY',    'fr_qfrWR+{]meZbk{pp7QAMRw!:`B<lVa>*766aC0ut+IId~/W]ZMB#;grXy}5*Z');
define('NONCE_KEY',        'Y^U+ZoZ/z1SKB2,V`%Lj8c$7c-re!J6_hJPBX._4lD-c$>;DUY S*$/$ZZ>uTujF');
define('AUTH_SALT',        'Dl:fN&Utn>c_XSu)}G DswItJBmT>Lk<Y)ZI199!,vdWU,vFtN!(~#M5565iiTJ`');
define('SECURE_AUTH_SALT', 'xjT&[ek]oNMd?lnH1L}8YM5eP O1!}L-lGB^ADRX|[y!/LmYq:}F,7mcv2}g38b.');
define('LOGGED_IN_SALT',   'tkf5p,(sp?)]`@37,z?>GnM7X[P1g;w!1Zjfn<5fqN:)3#(5g bmu6#tU0@*CZXU');
define('NONCE_SALT',       '(<)b5my}|W8b+>&,%UXv}G<]n&F/@-Ja?agNbgcB-*{4NCr,hU(sQGQ`b=X&u|Qt');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'pr_';

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
